<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { MoreVertical } from 'lucide-vue-next';
import { onBeforeUnmount, ref, watch, type Component } from 'vue';

export interface RowMenuItem {
    label: string;
    href: string;
    icon: Component;
    // Link esterno (nuova scheda) invece di navigazione Inertia
    external?: boolean;
}

defineProps<{
    items: RowMenuItem[];
    label?: string;
}>();

const open = ref(false);
const root = ref<HTMLElement | null>(null);

const onClickOutside = (event: MouseEvent) => {
    if (root.value && !root.value.contains(event.target as Node)) open.value = false;
};
const onEscape = (event: KeyboardEvent) => {
    if (event.key === 'Escape') open.value = false;
};

watch(open, (value) => {
    if (value) {
        document.addEventListener('mousedown', onClickOutside);
        document.addEventListener('keydown', onEscape);
    } else {
        document.removeEventListener('mousedown', onClickOutside);
        document.removeEventListener('keydown', onEscape);
    }
});

onBeforeUnmount(() => {
    document.removeEventListener('mousedown', onClickOutside);
    document.removeEventListener('keydown', onEscape);
});
</script>

<template>
    <div ref="root" class="relative">
        <button
            type="button"
            class="ml-icon-btn h-7 w-7"
            :aria-label="label ?? 'Azioni'"
            :aria-expanded="open"
            aria-haspopup="menu"
            @click.prevent.stop="open = !open"
        >
            <MoreVertical :size="16" />
        </button>
        <Transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="opacity-0 scale-95"
            leave-active-class="transition duration-75 ease-in"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="open"
                role="menu"
                class="absolute top-full right-0 z-20 mt-1 w-44 origin-top-right overflow-hidden rounded-lg border border-surface-200 bg-white py-1 shadow-lg dark:border-surface-700 dark:bg-surface-800"
            >
                <template v-for="item in items" :key="item.label">
                    <a
                        v-if="item.external"
                        :href="item.href"
                        target="_blank"
                        rel="noopener"
                        role="menuitem"
                        class="flex items-center gap-2 px-3 py-2 text-[13px] text-surface-700 hover:bg-surface-50 dark:text-surface-200 dark:hover:bg-surface-700/60"
                        @click="open = false"
                    >
                        <component :is="item.icon" :size="14" class="text-surface-400" /> {{ item.label }}
                    </a>
                    <Link
                        v-else
                        :href="item.href"
                        role="menuitem"
                        class="flex items-center gap-2 px-3 py-2 text-[13px] text-surface-700 hover:bg-surface-50 dark:text-surface-200 dark:hover:bg-surface-700/60"
                        @click="open = false"
                    >
                        <component :is="item.icon" :size="14" class="text-surface-400" /> {{ item.label }}
                    </Link>
                </template>
            </div>
        </Transition>
    </div>
</template>
