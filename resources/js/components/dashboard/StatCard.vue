<script setup lang="ts">
import type { Component } from 'vue';
import Sparkline from './Sparkline.vue';
import TrendBadge from './TrendBadge.vue';

export interface Trend {
    series: number[];
    current: number;
    previous: number;
    change: number | null;
}

defineProps<{
    label: string;
    value: string;
    icon: Component;
    // Classi del riquadro icona (sfondo + colore)
    iconClass: string;
    // Colore della sparkline
    color: string;
    trend?: Trend;
    // Evidenzia il valore (es. limite del piano raggiunto)
    valueClass?: string;
}>();
</script>

<template>
    <!-- Il layout si adatta alla larghezza della card (container query), non a quella dello schermo -->
    <div class="ml-card @container p-4 sm:p-5">
        <!-- Card larga: icona, valore e trend affiancati -->
        <div class="hidden items-center gap-4 @[17rem]:flex">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full" :class="iconClass">
                <component :is="icon" :size="22" :stroke-width="2" />
            </span>
            <div class="min-w-0 flex-1">
                <div class="text-2xl leading-tight font-semibold tracking-tight tabular-nums" :class="valueClass ?? 'text-surface-900 dark:text-white'">{{ value }}</div>
                <div class="truncate text-[13px] text-surface-500 dark:text-surface-400" :title="label">{{ label }}</div>
            </div>
            <div v-if="trend" class="flex shrink-0 flex-col items-end gap-2">
                <TrendBadge :change="trend.change" :current="trend.current" />
                <Sparkline :values="trend.series" :color="color" :width="76" :height="26" />
            </div>
        </div>

        <!-- Card stretta: impilata -->
        <div class="flex flex-col gap-3 @[17rem]:hidden">
            <div class="flex items-start justify-between gap-2">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full" :class="iconClass">
                    <component :is="icon" :size="19" :stroke-width="2" />
                </span>
                <TrendBadge v-if="trend" :change="trend.change" :current="trend.current" />
            </div>
            <div class="min-w-0">
                <div class="text-2xl leading-tight font-semibold tracking-tight tabular-nums" :class="valueClass ?? 'text-surface-900 dark:text-white'">{{ value }}</div>
                <div class="truncate text-[13px] text-surface-500 dark:text-surface-400" :title="label">{{ label }}</div>
            </div>
        </div>
    </div>
</template>
