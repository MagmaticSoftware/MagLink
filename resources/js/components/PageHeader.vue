<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import type { Component } from 'vue';

defineProps<{
    title: string;
    subtitle?: string;
    icon?: Component;
    // Classi del riquadro icona (sfondo + colore)
    iconClass?: string;
    // Se presente mostra il tasto "indietro"
    backHref?: string;
    backLabel?: string;
}>();
</script>

<template>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex min-w-0 items-center gap-3">
            <Link v-if="backHref" :href="backHref" class="ml-icon-btn h-9 w-9 shrink-0 border border-surface-200 bg-white shadow-sm dark:border-surface-700 dark:bg-surface-800" :title="backLabel" :aria-label="backLabel">
                <ArrowLeft :size="18" />
            </Link>
            <span v-if="icon" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl" :class="iconClass ?? 'bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400'">
                <component :is="icon" :size="22" />
            </span>
            <div class="min-w-0">
                <h1 class="truncate text-xl font-semibold tracking-tight text-surface-900 sm:text-2xl dark:text-white">{{ title }}</h1>
                <p v-if="subtitle" class="truncate text-sm text-surface-500 dark:text-surface-400">{{ subtitle }}</p>
            </div>
        </div>
        <div v-if="$slots.actions" class="flex shrink-0 flex-wrap items-center gap-2">
            <slot name="actions" />
        </div>
    </div>
</template>
