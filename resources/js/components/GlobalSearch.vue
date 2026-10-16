<script setup lang="ts">
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch, type Component } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ArrowRight, CornerDownLeft, FileText, Link2, Loader2, QrCode, Search, SearchX } from 'lucide-vue-next';
import { useRoute } from '@/composables/useRoute';

interface SearchItem {
    id: number;
    title: string;
    subtitle: string;
    href: string;
    active: boolean;
}

interface SearchGroup {
    key: 'links' | 'qrcodes' | 'pages';
    items: SearchItem[];
}

const { t } = useI18n();
const route = useRoute();

const open = ref(false);
const query = ref('');
const groups = ref<SearchGroup[]>([]);
const loading = ref(false);
const selected = ref(0);
const input = ref<HTMLInputElement | null>(null);
const list = ref<HTMLElement | null>(null);

const isMac = typeof navigator !== 'undefined' && /Mac|iPhone|iPad/.test(navigator.platform);

const groupMeta: Record<SearchGroup['key'], { label: string; icon: Component; tile: string; heading: string }> = {
    links: {
        label: 'search.groups.links',
        icon: Link2,
        tile: 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
        heading: 'text-blue-600 dark:text-blue-400',
    },
    qrcodes: {
        label: 'search.groups.qrcodes',
        icon: QrCode,
        tile: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
        heading: 'text-emerald-600 dark:text-emerald-400',
    },
    pages: {
        label: 'search.groups.pages',
        icon: FileText,
        tile: 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400',
        heading: 'text-violet-600 dark:text-violet-400',
    },
};

// Elenco piatto dei risultati, per la navigazione da tastiera
const flatItems = computed(() => groups.value.flatMap((group) => group.items));
const indexOf = (item: SearchItem) => flatItems.value.indexOf(item);

const openSearch = async () => {
    open.value = true;
    await nextTick();
    input.value?.focus();
    input.value?.select();
};

const close = () => {
    open.value = false;
};

let timer: ReturnType<typeof setTimeout> | undefined;
let controller: AbortController | undefined;

watch(query, (value) => {
    clearTimeout(timer);
    controller?.abort();
    selected.value = 0;

    if (value.trim().length < 2) {
        groups.value = [];
        loading.value = false;
        return;
    }

    loading.value = true;
    timer = setTimeout(async () => {
        controller = new AbortController();
        try {
            const response = await fetch(route('search', { q: value.trim() }), {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                signal: controller.signal,
            });
            const data = await response.json();
            groups.value = data.groups ?? [];
        } catch (error) {
            if ((error as Error).name !== 'AbortError') groups.value = [];
        } finally {
            loading.value = false;
        }
    }, 180);
});

const go = (item?: SearchItem) => {
    if (!item) return;
    close();
    router.visit(item.href);
};

const move = async (step: number) => {
    const total = flatItems.value.length;
    if (!total) return;
    selected.value = (selected.value + step + total) % total;
    await nextTick();
    list.value?.querySelector('[data-selected="true"]')?.scrollIntoView({ block: 'nearest' });
};

const onInputKeydown = (event: KeyboardEvent) => {
    if (event.key === 'ArrowDown') {
        event.preventDefault();
        move(1);
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        move(-1);
    } else if (event.key === 'Enter') {
        event.preventDefault();
        go(flatItems.value[selected.value]);
    } else if (event.key === 'Escape') {
        close();
    }
};

// Scorciatoia globale ⌘K / Ctrl+K
const onGlobalKeydown = (event: KeyboardEvent) => {
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault();
        open.value ? close() : openSearch();
    }
};

onMounted(() => window.addEventListener('keydown', onGlobalKeydown));
onBeforeUnmount(() => {
    window.removeEventListener('keydown', onGlobalKeydown);
    clearTimeout(timer);
    controller?.abort();
});

const escapeHtml = (value: string) =>
    value.replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char] as string);

// Evidenzia i termini cercati (testo già sanificato prima di inserire i <mark>)
const highlight = (text: string) => {
    const safe = escapeHtml(text ?? '');
    const terms = query.value
        .trim()
        .split(/\s+/)
        .filter((term) => term.length > 1)
        .map((term) => escapeHtml(term).replace(/[.*+?^${}()|[\]\\]/g, '\\$&'));
    if (!terms.length) return safe;
    return safe.replace(
        new RegExp(`(${terms.join('|')})`, 'gi'),
        '<mark class="rounded-sm bg-amber-100 px-0.5 text-inherit dark:bg-amber-400/25">$1</mark>',
    );
};
</script>

<template>
    <!-- Campo nella topbar -->
    <button
        type="button"
        class="group flex h-9 w-full items-center gap-2 rounded-lg border border-surface-200 bg-surface-50 px-3 text-sm text-surface-400 transition-colors hover:border-surface-300 hover:bg-white dark:border-surface-700 dark:bg-surface-800/60 dark:text-surface-500 dark:hover:border-surface-600"
        :aria-label="t('search.placeholder')"
        @click="openSearch"
    >
        <Search :size="16" class="shrink-0" />
        <span class="hidden flex-1 truncate text-left sm:block">{{ t('search.placeholder') }}</span>
        <kbd class="hidden rounded border border-surface-200 bg-white px-1.5 py-0.5 font-sans text-[10px] font-medium text-surface-400 sm:inline dark:border-surface-600 dark:bg-surface-800">
            {{ isMac ? '⌘' : 'Ctrl' }} K
        </kbd>
    </button>

    <!-- Modal risultati -->
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0"
            leave-active-class="transition duration-100 ease-in"
            leave-to-class="opacity-0"
        >
            <div v-if="open" class="fixed inset-0 z-50 flex items-start justify-center bg-surface-950/40 px-4 pt-[10vh] backdrop-blur-sm" @mousedown.self="close">
                <div role="dialog" aria-modal="true" :aria-label="t('search.title')" class="w-full max-w-xl overflow-hidden rounded-2xl border border-surface-200 bg-white shadow-2xl dark:border-surface-700 dark:bg-surface-900">
                    <!-- Input -->
                    <div class="flex items-center gap-3 border-b border-surface-100 px-5 py-4 dark:border-surface-800">
                        <Loader2 v-if="loading" :size="20" class="shrink-0 animate-spin text-surface-400" />
                        <Search v-else :size="20" class="shrink-0 text-surface-400" />
                        <input
                            ref="input"
                            v-model="query"
                            type="text"
                            :placeholder="t('search.inputPlaceholder')"
                            class="flex-1 border-0 bg-transparent p-0 text-base text-surface-900 placeholder:text-surface-400 focus:ring-0 focus:outline-none dark:text-white"
                            autocomplete="off"
                            spellcheck="false"
                            @keydown="onInputKeydown"
                        />
                        <kbd class="rounded-md border border-surface-200 px-1.5 py-0.5 text-[10px] font-semibold text-surface-400 dark:border-surface-700">ESC</kbd>
                    </div>

                    <!-- Risultati -->
                    <div ref="list" class="max-h-[60vh] overflow-y-auto py-2">
                        <template v-if="flatItems.length">
                            <section v-for="group in groups" :key="group.key" class="mb-1">
                                <h3 class="flex items-center gap-1.5 px-5 pt-2 pb-1 text-[11px] font-bold tracking-wider uppercase" :class="groupMeta[group.key].heading">
                                    {{ t(groupMeta[group.key].label) }}
                                    <span class="font-semibold text-surface-300 dark:text-surface-600">{{ group.items.length }}</span>
                                </h3>
                                <ul>
                                    <li v-for="item in group.items" :key="`${group.key}-${item.id}`">
                                        <button
                                            type="button"
                                            class="flex w-full items-center gap-3 px-5 py-2.5 text-left transition-colors"
                                            :class="indexOf(item) === selected ? 'bg-primary-50/70 dark:bg-primary-500/10' : 'hover:bg-surface-50 dark:hover:bg-surface-800/60'"
                                            :data-selected="indexOf(item) === selected"
                                            @mouseenter="selected = indexOf(item)"
                                            @click="go(item)"
                                        >
                                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg" :class="groupMeta[group.key].tile">
                                                <component :is="groupMeta[group.key].icon" :size="17" />
                                            </span>
                                            <span class="min-w-0 flex-1">
                                                <span class="block truncate text-sm font-medium text-surface-900 dark:text-white" v-html="highlight(item.title)"></span>
                                                <span class="block truncate text-xs text-surface-500 dark:text-surface-400" v-html="highlight(item.subtitle)"></span>
                                            </span>
                                            <span v-if="!item.active" class="ml-badge shrink-0 bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-400">{{ t('search.inactive') }}</span>
                                            <ArrowRight v-if="indexOf(item) === selected" :size="16" class="shrink-0 text-surface-400" />
                                        </button>
                                    </li>
                                </ul>
                            </section>
                        </template>

                        <div v-else-if="query.trim().length >= 2 && !loading" class="flex flex-col items-center gap-2 px-5 py-10 text-center">
                            <SearchX :size="24" class="text-surface-300 dark:text-surface-600" />
                            <p class="text-sm text-surface-500 dark:text-surface-400">{{ t('search.noResults', { query: query.trim() }) }}</p>
                        </div>

                        <p v-else-if="query.trim().length < 2" class="px-5 py-8 text-center text-sm text-surface-400 dark:text-surface-500">
                            {{ t('search.hint') }}
                        </p>
                    </div>

                    <!-- Legenda tasti -->
                    <div class="flex items-center gap-4 border-t border-surface-100 px-5 py-2.5 text-[11px] text-surface-400 dark:border-surface-800 dark:text-surface-500">
                        <span class="inline-flex items-center gap-1.5"><kbd class="rounded border border-surface-200 px-1 dark:border-surface-700">↑↓</kbd> {{ t('search.keys.navigate') }}</span>
                        <span class="inline-flex items-center gap-1.5"><kbd class="rounded border border-surface-200 px-1 dark:border-surface-700"><CornerDownLeft :size="10" class="inline" /></kbd> {{ t('search.keys.open') }}</span>
                        <span class="inline-flex items-center gap-1.5"><kbd class="rounded border border-surface-200 px-1 dark:border-surface-700">ESC</kbd> {{ t('search.keys.close') }}</span>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
