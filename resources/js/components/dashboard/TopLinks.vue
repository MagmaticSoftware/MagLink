<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowDownRight, ArrowUpRight, Link2, Trophy } from 'lucide-vue-next';
import { useRoute } from '@/composables/useRoute';

export interface TopLink {
    id: number;
    title: string;
    slug: string;
    url: string;
    views: number;
    change: number | null;
}

defineProps<{
    links: TopLink[];
    period: number;
}>();

const route = useRoute();

// Colori a rotazione per distinguere le righe a colpo d'occhio
const tiles = [
    'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
    'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
    'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400',
    'bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400',
    'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400',
];

const formatCompact = (num: number) => new Intl.NumberFormat('it-IT', { notation: 'compact', maximumFractionDigits: 1 }).format(num);
const formatChange = (change: number) =>
    `${change > 0 ? '+' : change < 0 ? '−' : ''}${new Intl.NumberFormat('it-IT', { maximumFractionDigits: 1 }).format(Math.abs(change))}%`;
</script>

<template>
    <section class="ml-card flex flex-col p-5">
        <div class="mb-3 flex items-center justify-between gap-2">
            <h3 class="ml-section-title">
                <Trophy :size="18" class="text-primary-500" />
                Link più performanti
            </h3>
            <span class="text-xs text-surface-500 dark:text-surface-400">Ultimi {{ period }} giorni</span>
        </div>

        <ul v-if="links.length" class="-mx-2 flex flex-col">
            <li v-for="(link, index) in links" :key="link.id">
                <Link
                    :href="route('links.analytics', { link: link.id })"
                    class="flex items-center gap-3 rounded-lg px-2 py-2 transition-colors hover:bg-surface-50 dark:hover:bg-surface-700/40"
                >
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg" :class="tiles[index % tiles.length]">
                        <Link2 :size="16" />
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-[13px] font-semibold text-surface-900 dark:text-white">{{ link.title }}</span>
                        <span class="block truncate text-xs text-surface-500 dark:text-surface-400">/{{ link.slug }}</span>
                    </span>
                    <span class="flex shrink-0 flex-col items-end">
                        <span class="text-sm font-semibold text-surface-900 tabular-nums dark:text-white">{{ formatCompact(link.views) }}</span>
                        <span
                            v-if="link.change !== null"
                            class="inline-flex items-center text-[11px] font-semibold tabular-nums"
                            :class="link.change >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                        >
                            <ArrowUpRight v-if="link.change >= 0" :size="11" :stroke-width="2.5" />
                            <ArrowDownRight v-else :size="11" :stroke-width="2.5" />
                            {{ formatChange(link.change) }}
                        </span>
                        <span v-else class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">Nuovo</span>
                    </span>
                </Link>
            </li>
        </ul>
        <div v-else class="flex flex-1 flex-col items-center justify-center gap-2 py-8 text-center">
            <Trophy :size="22" class="text-surface-300 dark:text-surface-600" />
            <p class="text-sm text-surface-500 dark:text-surface-400">Nessun click negli ultimi {{ period }} giorni</p>
        </div>
    </section>
</template>
