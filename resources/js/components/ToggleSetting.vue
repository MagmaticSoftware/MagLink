<script setup lang="ts">
import type { Component } from 'vue';

const props = withDefaults(
    defineProps<{
        title: string;
        description?: string;
        icon?: Component;
        // Colore dello stato attivo: 'primary' | 'blue'
        tone?: 'primary' | 'blue';
    }>(),
    { tone: 'primary' },
);

const model = defineModel<boolean>({ required: true });

const tones = {
    primary: { icon: 'text-primary-600 dark:text-primary-400', track: 'peer-checked:bg-primary-500 dark:peer-checked:bg-primary-500' },
    blue: { icon: 'text-blue-600 dark:text-blue-400', track: 'peer-checked:bg-blue-600 dark:peer-checked:bg-blue-500' },
};
</script>

<template>
    <label class="flex cursor-pointer items-center justify-between gap-3 rounded-lg border border-surface-200 bg-surface-50/60 p-3.5 transition-colors hover:bg-surface-50 dark:border-surface-700 dark:bg-surface-900/40">
        <span class="flex min-w-0 items-center gap-3">
            <component :is="icon" v-if="icon" :size="18" class="shrink-0" :class="model ? tones[props.tone].icon : 'text-surface-400'" />
            <span class="min-w-0">
                <span class="block text-sm font-medium text-surface-900 dark:text-surface-50">{{ title }}</span>
                <span v-if="description" class="block text-xs text-surface-500 dark:text-surface-400">{{ description }}</span>
            </span>
        </span>
        <span class="relative inline-flex shrink-0 items-center">
            <input v-model="model" type="checkbox" class="peer sr-only" />
            <span
                class="h-6 w-11 rounded-full bg-surface-200 transition-colors peer-focus-visible:ring-4 peer-focus-visible:ring-primary-500/20 after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow-sm after:transition-all after:content-[''] peer-checked:after:translate-x-full dark:bg-surface-700"
                :class="tones[props.tone].track"
            ></span>
        </span>
    </label>
</template>
