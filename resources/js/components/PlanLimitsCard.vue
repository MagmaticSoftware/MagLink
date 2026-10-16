<script setup lang="ts">
import { computed, type Component } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { Gauge, Link2, QrCode, FileText, Sparkles } from 'lucide-vue-next';
import type { PageProps } from '@/types/inertia';

const props = defineProps<{
    stats: {
        links_count: number;
        qrcodes_count: number;
        pages_count: number;
    };
}>();

const page = usePage<PageProps>();

// I limiti arrivano già calcolati dal backend (User::getPlanLimits), che tiene conto
// anche dei piani gestiti internamente come Pro Lifetime
const planLimits = computed(() => {
    return page.props.billing?.limits || {
        links: 5,
        qrcodes: 5,
        pages: 1,
    };
});

interface UsageRow {
    label: string;
    icon: Component;
    iconClass: string;
    barClass: string;
    current: number;
    limit: number;
}

const rows = computed<UsageRow[]>(() => [
    {
        label: 'Link',
        icon: Link2,
        iconClass: 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
        barClass: 'bg-blue-500',
        current: props.stats.links_count,
        limit: planLimits.value.links,
    },
    {
        label: 'QR Code',
        icon: QrCode,
        iconClass: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
        barClass: 'bg-emerald-500',
        current: props.stats.qrcodes_count,
        limit: planLimits.value.qrcodes,
    },
    {
        label: 'Pagine',
        icon: FileText,
        iconClass: 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400',
        barClass: 'bg-violet-500',
        current: props.stats.pages_count,
        limit: planLimits.value.pages,
    },
]);

const progress = (row: UsageRow) => {
    if (row.limit === -1) return 0;
    return Math.min((row.current / row.limit) * 100, 100);
};

const isNearLimit = (current: number, limit: number) => {
    if (limit === -1) return false;
    return (current / limit) >= 0.8;
};

const isAtLimit = (current: number, limit: number) => {
    if (limit === -1) return false;
    return current >= limit;
};

const barClass = (row: UsageRow) => {
    if (isAtLimit(row.current, row.limit)) return 'bg-rose-500';
    if (isNearLimit(row.current, row.limit)) return 'bg-amber-500';
    return row.barClass;
};

const formatLimit = (limit: number) => {
    return limit === -1 ? '∞' : limit;
};

// Risorse al limite (o oltre, es. dopo un downgrade) e risorse vicine al limite
const atLimitRows = computed(() => rows.value.filter((row) => isAtLimit(row.current, row.limit)));
const overLimit = computed(() => atLimitRows.value.some((row) => row.current > row.limit));
const nearLimitRows = computed(() =>
    rows.value.filter((row) => isNearLimit(row.current, row.limit) && !isAtLimit(row.current, row.limit)),
);
const listLabels = (items: UsageRow[]) => items.map((row) => row.label).join(', ');
</script>

<template>
    <div class="ml-card flex flex-col p-5">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="flex items-center gap-2 text-[15px] font-semibold text-surface-900 dark:text-surface-50">
                <Gauge :size="18" class="text-primary-500" />
                Utilizzo piano
            </h3>
            <Link
                v-if="page.props.billing?.onFreePlan"
                :href="route('plans.index', { tenant: page.props.auth.tenant })"
                class="flex items-center gap-1 text-xs font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300"
            >
                <Sparkles :size="13" />
                Upgrade
            </Link>
        </div>

        <ul class="flex flex-1 flex-col justify-center gap-4">
            <li v-for="row in rows" :key="row.label" class="flex items-center gap-3">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg" :class="row.iconClass">
                    <component :is="row.icon" :size="16" />
                </span>
                <div class="min-w-0 flex-1">
                    <div class="mb-1.5 flex items-center justify-between text-[13px]">
                        <span class="font-medium text-surface-700 dark:text-surface-300">{{ row.label }}</span>
                        <span
                            class="font-semibold tabular-nums"
                            :class="isAtLimit(row.current, row.limit)
                                ? 'text-rose-600 dark:text-rose-400'
                                : 'text-surface-900 dark:text-surface-100'"
                        >
                            {{ row.current }}<span class="font-normal text-surface-400"> / {{ formatLimit(row.limit) }}</span>
                        </span>
                    </div>
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-surface-100 dark:bg-surface-700/70">
                        <div
                            class="h-full rounded-full transition-all duration-500"
                            :class="barClass(row)"
                            :style="{ width: `${progress(row)}%` }"
                        ></div>
                    </div>
                </div>
            </li>
        </ul>

        <!-- Limite raggiunto o superato: non si possono creare nuove risorse -->
        <div
            v-if="atLimitRows.length"
            class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2.5 text-xs text-rose-800 dark:border-rose-500/25 dark:bg-rose-500/10 dark:text-rose-200"
        >
            <p>
                <strong class="font-semibold">{{ overLimit ? 'Hai superato' : 'Hai raggiunto' }} il limite del piano</strong>
                per: {{ listLabels(atLimitRows) }}. Non puoi crearne di nuovi.
            </p>
            <Link
                :href="route('plans.index', { tenant: page.props.auth.tenant })"
                class="mt-1 inline-block font-semibold underline hover:no-underline"
            >
                Effettua l'upgrade
            </Link>
        </div>

        <!-- Vicino al limite -->
        <div
            v-else-if="nearLimitRows.length"
            class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2.5 text-xs text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-200"
        >
            <p>Stai per raggiungere il limite del piano per: {{ listLabels(nearLimitRows) }}.</p>
            <Link
                :href="route('plans.index', { tenant: page.props.auth.tenant })"
                class="mt-1 inline-block font-semibold underline hover:no-underline"
            >
                Effettua l'upgrade
            </Link>
        </div>
    </div>
</template>
