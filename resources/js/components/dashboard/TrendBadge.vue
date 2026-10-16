<script setup lang="ts">
import { computed } from 'vue';
import { ArrowDownRight, ArrowUpRight, Minus } from 'lucide-vue-next';

const props = defineProps<{
    // Variazione percentuale rispetto al periodo precedente (null = periodo precedente vuoto)
    change: number | null;
    current: number;
}>();

const state = computed(() => {
    if (props.change === null) {
        return props.current > 0 ? 'new' : 'flat';
    }
    if (props.change > 0) return 'up';
    if (props.change < 0) return 'down';
    return 'flat';
});

const label = computed(() => {
    if (state.value === 'new') return 'Nuovo';
    if (props.change === null || props.change === 0) return '0%';
    const value = new Intl.NumberFormat('it-IT', { maximumFractionDigits: 1 }).format(Math.abs(props.change));
    return `${props.change > 0 ? '+' : '−'}${value}%`;
});

const classes = computed(
    () =>
        ({
            up: 'bg-emerald-50 text-emerald-700 ring-emerald-600/15 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-400/20',
            new: 'bg-emerald-50 text-emerald-700 ring-emerald-600/15 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-400/20',
            down: 'bg-rose-50 text-rose-700 ring-rose-600/15 dark:bg-rose-500/10 dark:text-rose-300 dark:ring-rose-400/20',
            flat: 'bg-surface-100 text-surface-600 ring-surface-500/15 dark:bg-surface-700/50 dark:text-surface-300 dark:ring-surface-500/20',
        })[state.value],
);
</script>

<template>
    <span
        class="inline-flex items-center gap-0.5 rounded-md px-1.5 py-0.5 text-[11px] font-semibold tabular-nums ring-1 ring-inset"
        :class="classes"
    >
        <ArrowUpRight v-if="state === 'up' || state === 'new'" :size="12" :stroke-width="2.5" />
        <ArrowDownRight v-else-if="state === 'down'" :size="12" :stroke-width="2.5" />
        <Minus v-else :size="12" :stroke-width="2.5" />
        {{ label }}
    </span>
</template>
