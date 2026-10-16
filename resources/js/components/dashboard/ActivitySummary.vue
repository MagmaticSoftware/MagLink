<script setup lang="ts">
import { computed } from 'vue';
import { Activity, CalendarDays, Eye, FileText, Link2, QrCode } from 'lucide-vue-next';
import TrendBadge from './TrendBadge.vue';
import type { Trend } from './StatCard.vue';

const props = defineProps<{
    period: number;
    trends: Record<'links' | 'qrcodes' | 'pages' | 'views', Trend>;
}>();

const formatNumber = (num: number) => new Intl.NumberFormat('it-IT').format(num);

const rows = computed(() => [
    { label: 'Link creati', icon: Link2, iconClass: 'text-blue-600 dark:text-blue-400', trend: props.trends.links },
    { label: 'QR Code creati', icon: QrCode, iconClass: 'text-emerald-600 dark:text-emerald-400', trend: props.trends.qrcodes },
    { label: 'Pagine create', icon: FileText, iconClass: 'text-violet-600 dark:text-violet-400', trend: props.trends.pages },
    { label: 'Visite', icon: Eye, iconClass: 'text-indigo-600 dark:text-indigo-400', trend: props.trends.views },
]);
</script>

<template>
    <section class="ml-card flex flex-col p-5">
        <div class="mb-4 flex items-center justify-between gap-2">
            <h3 class="ml-section-title">
                <Activity :size="18" class="text-primary-500" />
                Riepilogo attività
            </h3>
            <span class="inline-flex items-center gap-1 text-xs text-surface-500 dark:text-surface-400">
                <CalendarDays :size="13" /> Ultimi {{ period }} giorni
            </span>
        </div>
        <ul class="flex flex-1 flex-col justify-center gap-2">
            <li
                v-for="row in rows"
                :key="row.label"
                class="flex items-center gap-3 rounded-lg bg-surface-50 px-3 py-2.5 dark:bg-surface-700/30"
            >
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-surface-200/70 dark:bg-surface-800 dark:ring-surface-700">
                    <component :is="row.icon" :size="14" :class="row.iconClass" />
                </span>
                <span class="flex-1 truncate text-[13px] text-surface-600 dark:text-surface-300">{{ row.label }}</span>
                <span class="text-sm font-semibold text-surface-900 tabular-nums dark:text-white">{{ formatNumber(row.trend.current) }}</span>
                <TrendBadge :change="row.trend.change" :current="row.trend.current" class="w-[4.5rem] justify-center" />
            </li>
        </ul>
    </section>
</template>
