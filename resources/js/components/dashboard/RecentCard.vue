<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight } from 'lucide-vue-next';
import type { Component } from 'vue';

export type Tone = 'blue' | 'emerald' | 'violet' | 'orange';

const props = defineProps<{
    title: string;
    subtitle: string;
    icon: Component;
    tone: Tone;
    viewAllHref?: string;
    // Invito all'azione in fondo (facoltativo)
    footerLabel?: string;
    footerHref?: string;
    footerIcon?: Component;
    empty?: boolean;
    emptyText?: string;
}>();

// Classi per tonalità: riquadro icona e barra in fondo
const tones: Record<Tone, { tile: string; footer: string; footerIcon: string }> = {
    blue: {
        tile: 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
        footer: 'from-blue-50 to-blue-100/40 text-blue-700 hover:from-blue-100/80 dark:from-blue-500/10 dark:to-blue-500/5 dark:text-blue-300',
        footerIcon: 'bg-white text-blue-600 dark:bg-blue-500/15 dark:text-blue-300',
    },
    emerald: {
        tile: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
        footer: 'from-emerald-50 to-emerald-100/50 text-emerald-700 hover:from-emerald-100/80 dark:from-emerald-500/10 dark:to-emerald-500/5 dark:text-emerald-300',
        footerIcon: 'bg-white text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300',
    },
    violet: {
        tile: 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400',
        footer: 'from-violet-50 to-violet-100/50 text-violet-700 hover:from-violet-100/80 dark:from-violet-500/10 dark:to-violet-500/5 dark:text-violet-300',
        footerIcon: 'bg-white text-violet-600 dark:bg-violet-500/15 dark:text-violet-300',
    },
    orange: {
        tile: 'bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400',
        footer: 'from-orange-50 to-orange-100/60 text-orange-700 hover:from-orange-100/80 dark:from-orange-500/10 dark:to-orange-500/5 dark:text-orange-300',
        footerIcon: 'bg-white text-orange-600 dark:bg-orange-500/15 dark:text-orange-300',
    },
};
</script>

<template>
    <section class="ml-card flex flex-col p-5">
        <!-- Intestazione -->
        <div class="mb-3 flex items-start gap-3 border-b border-surface-100 pb-4 dark:border-surface-700/60">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl" :class="tones[props.tone].tile">
                <component :is="icon" :size="21" />
            </span>
            <div class="min-w-0 flex-1">
                <h3 class="text-[17px] leading-tight font-bold tracking-tight text-surface-900 dark:text-white">{{ title }}</h3>
                <p class="mt-0.5 truncate text-xs text-surface-500 dark:text-surface-400">{{ subtitle }}</p>
            </div>
            <Link
                v-if="viewAllHref"
                :href="viewAllHref"
                class="inline-flex shrink-0 items-center gap-1 pt-0.5 text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
            >
                Vedi tutti <ArrowRight :size="13" />
            </Link>
        </div>

        <!-- Elenco -->
        <div class="flex-1">
            <slot v-if="!empty" />
            <p v-else class="flex h-full min-h-[8rem] items-center justify-center text-sm text-surface-500 dark:text-surface-400">{{ emptyText }}</p>
        </div>

        <!-- Invito all'azione -->
        <Link
            v-if="footerHref"
            :href="footerHref"
            class="group mt-4 flex items-center gap-3 rounded-xl bg-gradient-to-r px-3 py-2.5 text-[13px] font-semibold transition-colors"
            :class="tones[props.tone].footer"
        >
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg shadow-sm" :class="tones[props.tone].footerIcon">
                <component :is="footerIcon" :size="16" />
            </span>
            <span class="flex-1">{{ footerLabel }}</span>
            <ArrowRight :size="15" class="opacity-60 transition-transform group-hover:translate-x-0.5" />
        </Link>
    </section>
</template>
