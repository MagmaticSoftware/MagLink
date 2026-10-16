<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { LucideCrown, LucideDownload, LucideFileSpreadsheet, LucideFileText, LucideSearch, LucideLock } from 'lucide-vue-next';
import Dialog from '@/components/volt/Dialog.vue';
import { useRoute } from '@/composables/useRoute';

interface ExportLink {
    id: number;
    title: string | null;
    slug: string;
}

const props = withDefaults(defineProps<{
    visible: boolean;
    links: ExportLink[];
    canExport: boolean;
    /** Link fisso (pagina analytics del singolo link): sempre incluso nell'export */
    lockedLink?: ExportLink | null;
    /** Link preselezionati all'apertura (pagina lista) */
    preselected?: number[];
}>(), {
    lockedLink: null,
    preselected: () => [],
});

const emit = defineEmits<{
    'update:visible': [value: boolean];
    'show-plans': [];
}>();

const { t } = useI18n();
const route = useRoute();

const MAX_LINKS = 10;

const format = ref<'pdf' | 'csv'>('pdf');
const dataset = ref<'daily' | 'views'>('daily');
const preset = ref('30d');
const search = ref('');
const selectedIds = ref<number[]>([]);

const toInputDate = (d: Date) => {
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
};
const daysAgo = (n: number) => {
    const d = new Date();
    d.setDate(d.getDate() - n);
    return d;
};

const today = toInputDate(new Date());
const from = ref(toInputDate(daysAgo(29)));
const to = ref(today);

const presets = computed(() => [
    { key: '7d', label: t('analytics.export.presets.7d') },
    { key: '30d', label: t('analytics.export.presets.30d') },
    { key: '90d', label: t('analytics.export.presets.90d') },
    { key: 'thisMonth', label: t('analytics.export.presets.thisMonth') },
    { key: 'lastMonth', label: t('analytics.export.presets.lastMonth') },
    { key: 'custom', label: t('analytics.export.presets.custom') },
]);

const applyPreset = (key: string) => {
    preset.value = key;
    const now = new Date();
    switch (key) {
        case '7d':
            from.value = toInputDate(daysAgo(6));
            to.value = today;
            break;
        case '30d':
            from.value = toInputDate(daysAgo(29));
            to.value = today;
            break;
        case '90d':
            from.value = toInputDate(daysAgo(89));
            to.value = today;
            break;
        case 'thisMonth':
            from.value = toInputDate(new Date(now.getFullYear(), now.getMonth(), 1));
            to.value = today;
            break;
        case 'lastMonth':
            from.value = toInputDate(new Date(now.getFullYear(), now.getMonth() - 1, 1));
            to.value = toInputDate(new Date(now.getFullYear(), now.getMonth(), 0));
            break;
    }
};

// Reset della selezione a ogni apertura
watch(() => props.visible, (open) => {
    if (open) {
        selectedIds.value = props.preselected.slice(0, props.lockedLink ? MAX_LINKS - 1 : MAX_LINKS);
        search.value = '';
    }
});

const selectableLinks = computed(() => props.links.filter((l) => l.id !== props.lockedLink?.id));

const filteredLinks = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (!q) return selectableLinks.value;
    return selectableLinks.value.filter((l) => (l.title || '').toLowerCase().includes(q) || l.slug.toLowerCase().includes(q));
});

const maxSelectable = computed(() => (props.lockedLink ? MAX_LINKS - 1 : MAX_LINKS));
const totalSelected = computed(() => selectedIds.value.length + (props.lockedLink ? 1 : 0));

const toggleLink = (id: number) => {
    if (selectedIds.value.includes(id)) {
        selectedIds.value = selectedIds.value.filter((x) => x !== id);
    } else if (selectedIds.value.length < maxSelectable.value) {
        selectedIds.value = [...selectedIds.value, id];
    }
};

const periodError = computed(() => {
    if (!from.value || !to.value) return t('analytics.export.errors.period');
    if (from.value > to.value) return t('analytics.export.errors.order');
    const diff = (new Date(to.value).getTime() - new Date(from.value).getTime()) / 86400000;
    if (diff > 731) return t('analytics.export.errors.tooLong');
    return '';
});

const canSubmit = computed(() => props.canExport && totalSelected.value > 0 && !periodError.value);

const download = () => {
    if (!canSubmit.value) return;

    const query: Record<string, unknown> = {
        format: format.value,
        from: from.value,
        to: to.value,
        link_ids: selectedIds.value,
    };
    if (format.value === 'csv') query.dataset = dataset.value;

    const url = props.lockedLink
        ? route('links.analytics.export', { link: props.lockedLink.id, ...query })
        : route('links.export', query);

    // Download diretto: il browser gestisce il file senza lasciare la pagina
    window.location.href = url as string;
    emit('update:visible', false);
};

const showPlans = () => {
    emit('update:visible', false);
    emit('show-plans');
};
</script>

<template>
    <Dialog
        :visible="visible"
        @update:visible="emit('update:visible', $event)"
        modal
        :draggable="false"
        :header="t('analytics.export.title')"
        class="w-[calc(100vw-1.5rem)] max-w-xl"
    >
        <!-- Piano non premium -->
        <div v-if="!canExport" class="flex flex-col items-center text-center gap-4 py-4">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30">
                <LucideCrown :size="28" class="text-amber-600 dark:text-amber-400" />
            </div>
            <div>
                <h3 class="text-lg font-semibold text-surface-900 dark:text-surface-50">{{ t('analytics.export.premiumTitle') }}</h3>
                <p class="mt-1 text-sm text-surface-600 dark:text-surface-400">{{ t('analytics.export.premiumDescription') }}</p>
            </div>
            <button type="button" class="button-primary inline-flex items-center gap-2" @click="showPlans">
                <LucideCrown :size="16" /> {{ t('analytics.export.upgrade') }}
            </button>
        </div>

        <div v-else class="flex flex-col gap-5">
            <!-- Formato -->
            <div>
                <p class="mb-2 text-sm font-medium text-surface-700 dark:text-surface-300">{{ t('analytics.export.format') }}</p>
                <div class="grid grid-cols-2 gap-2">
                    <button
                        v-for="opt in [{ key: 'pdf', icon: LucideFileText, label: t('analytics.export.pdf'), hint: t('analytics.export.pdfHint') }, { key: 'csv', icon: LucideFileSpreadsheet, label: t('analytics.export.csv'), hint: t('analytics.export.csvHint') }]"
                        :key="opt.key"
                        type="button"
                        class="flex items-start gap-2 rounded-lg border p-3 text-left transition-colors"
                        :class="format === opt.key
                            ? 'border-primary bg-primary-50 dark:bg-primary-900/20'
                            : 'border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-800'"
                        @click="format = opt.key as 'pdf' | 'csv'"
                    >
                        <component :is="opt.icon" :size="20" class="mt-0.5 shrink-0 text-primary" />
                        <span>
                            <span class="block text-sm font-semibold text-surface-900 dark:text-surface-50">{{ opt.label }}</span>
                            <span class="block text-xs text-surface-500 dark:text-surface-400">{{ opt.hint }}</span>
                        </span>
                    </button>
                </div>
                <div v-if="format === 'csv'" class="mt-3 flex flex-col gap-2 sm:flex-row sm:gap-4 text-sm text-surface-700 dark:text-surface-300">
                    <label class="flex items-center gap-2">
                        <input v-model="dataset" type="radio" value="daily" class="accent-orange-600" />
                        {{ t('analytics.export.datasetDaily') }}
                    </label>
                    <label class="flex items-center gap-2">
                        <input v-model="dataset" type="radio" value="views" class="accent-orange-600" />
                        {{ t('analytics.export.datasetViews') }}
                    </label>
                </div>
            </div>

            <!-- Periodo -->
            <div>
                <p class="mb-2 text-sm font-medium text-surface-700 dark:text-surface-300">{{ t('analytics.export.period') }}</p>
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="p in presets"
                        :key="p.key"
                        type="button"
                        class="rounded-full border px-3 py-1 text-xs font-medium transition-colors"
                        :class="preset === p.key
                            ? 'border-primary bg-primary text-white'
                            : 'border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-800'"
                        @click="applyPreset(p.key)"
                    >
                        {{ p.label }}
                    </button>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-3">
                    <label class="flex flex-col gap-1 text-xs text-surface-600 dark:text-surface-400">
                        {{ t('analytics.export.from') }}
                        <input v-model="from" type="date" :max="today" class="rounded-md border border-surface-300 dark:border-surface-700 bg-surface-0 dark:bg-surface-950 px-2 py-1.5 text-sm text-surface-900 dark:text-surface-50" @input="preset = 'custom'" />
                    </label>
                    <label class="flex flex-col gap-1 text-xs text-surface-600 dark:text-surface-400">
                        {{ t('analytics.export.to') }}
                        <input v-model="to" type="date" :max="today" class="rounded-md border border-surface-300 dark:border-surface-700 bg-surface-0 dark:bg-surface-950 px-2 py-1.5 text-sm text-surface-900 dark:text-surface-50" @input="preset = 'custom'" />
                    </label>
                </div>
                <p v-if="periodError" class="mt-2 text-xs text-red-600 dark:text-red-400">{{ periodError }}</p>
                <p v-else class="mt-2 text-xs text-surface-500 dark:text-surface-400">{{ t('analytics.export.previousPeriodHint') }}</p>
            </div>

            <!-- Link -->
            <div>
                <div class="mb-2 flex items-center justify-between gap-2">
                    <p class="text-sm font-medium text-surface-700 dark:text-surface-300">
                        {{ lockedLink ? t('analytics.export.compareWith') : t('analytics.export.links') }}
                    </p>
                    <span class="text-xs text-surface-500 dark:text-surface-400">{{ totalSelected }}/{{ MAX_LINKS }}</span>
                </div>

                <div v-if="lockedLink" class="mb-2 flex items-center gap-2 rounded-md bg-surface-100 dark:bg-surface-800 px-3 py-2 text-sm">
                    <LucideLock :size="14" class="text-surface-500" />
                    <span class="truncate font-medium text-surface-900 dark:text-surface-50">{{ lockedLink.title || lockedLink.slug }}</span>
                </div>

                <div v-if="selectableLinks.length > 0">
                    <div class="relative mb-2">
                        <LucideSearch :size="16" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-surface-400" />
                        <input
                            v-model="search"
                            type="text"
                            :placeholder="t('analytics.export.searchLinks')"
                            class="w-full rounded-md border border-surface-300 dark:border-surface-700 bg-surface-0 dark:bg-surface-950 py-1.5 pl-8 pr-2 text-sm text-surface-900 dark:text-surface-50"
                        />
                    </div>
                    <ul class="max-h-48 overflow-y-auto rounded-md border border-surface-200 dark:border-surface-700 divide-y divide-surface-100 dark:divide-surface-800">
                        <li v-for="link in filteredLinks" :key="link.id">
                            <label
                                class="flex cursor-pointer items-center gap-3 px-3 py-2 text-sm hover:bg-surface-50 dark:hover:bg-surface-800"
                                :class="{ 'opacity-50 cursor-not-allowed': !selectedIds.includes(link.id) && selectedIds.length >= maxSelectable }"
                            >
                                <input
                                    type="checkbox"
                                    class="accent-orange-600"
                                    :checked="selectedIds.includes(link.id)"
                                    :disabled="!selectedIds.includes(link.id) && selectedIds.length >= maxSelectable"
                                    @change="toggleLink(link.id)"
                                />
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-surface-900 dark:text-surface-50">{{ link.title || link.slug }}</span>
                                    <span class="block truncate text-xs text-surface-500">/{{ link.slug }}</span>
                                </span>
                            </label>
                        </li>
                        <li v-if="filteredLinks.length === 0" class="px-3 py-4 text-center text-sm text-surface-500">
                            {{ t('common.noResults') }}
                        </li>
                    </ul>
                </div>
                <p v-if="!lockedLink && totalSelected === 0" class="mt-2 text-xs text-red-600 dark:text-red-400">{{ t('analytics.export.errors.noLinks') }}</p>
                <p v-else-if="totalSelected > 1" class="mt-2 text-xs text-surface-500 dark:text-surface-400">{{ t('analytics.export.comparisonHint') }}</p>
            </div>

            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-800"
                    @click="emit('update:visible', false)"
                >
                    {{ t('common.cancel') }}
                </button>
                <button
                    type="button"
                    class="button-primary inline-flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="!canSubmit"
                    @click="download"
                >
                    <LucideDownload :size="16" /> {{ t('analytics.export.download') }}
                </button>
            </div>
        </div>
    </Dialog>
</template>
