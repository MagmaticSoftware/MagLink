<script setup lang="ts">
import type { Component } from 'vue';
import { GripVertical, Loader2, Save, Trash2 } from 'lucide-vue-next';

export type BlockTone = 'violet' | 'orange' | 'pink' | 'red' | 'blue' | 'cyan' | 'slate' | 'indigo' | 'amber';

const props = withDefaults(
    defineProps<{
        label: string;
        icon: Component;
        tone?: BlockTone;
        hasChanges?: boolean;
        isSaving?: boolean;
        // Alcuni blocchi (es. separatore) non hanno nulla da salvare
        canSave?: boolean;
    }>(),
    { tone: 'slate', hasChanges: false, isSaving: false, canSave: true },
);

const emit = defineEmits<{
    save: [];
    delete: [];
}>();

const tones: Record<BlockTone, string> = {
    violet: 'bg-violet-50 text-violet-600 dark:bg-violet-500/15 dark:text-violet-300',
    orange: 'bg-orange-50 text-orange-600 dark:bg-orange-500/15 dark:text-orange-300',
    pink: 'bg-pink-50 text-pink-600 dark:bg-pink-500/15 dark:text-pink-300',
    red: 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-300',
    blue: 'bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-300',
    cyan: 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/15 dark:text-cyan-300',
    slate: 'bg-surface-100 text-surface-600 dark:bg-surface-700/60 dark:text-surface-300',
    indigo: 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300',
    amber: 'bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300',
};
</script>

<template>
    <!-- Intestazione comune a tutti i blocchi: trascinamento, tipo, stato e azioni -->
    <div class="@container flex shrink-0 items-center gap-2 border-b border-surface-100 bg-surface-50/70 px-2.5 py-2 dark:border-surface-700/70 dark:bg-surface-900/40">
        <GripVertical :size="15" class="shrink-0 cursor-grab text-surface-300 dark:text-surface-600" aria-hidden="true" />
        <span class="hidden h-7 w-7 shrink-0 items-center justify-center rounded-lg @[13rem]:flex" :class="tones[props.tone]">
            <component :is="icon" :size="15" />
        </span>
        <span class="min-w-0 flex-1 truncate text-[13px] font-semibold text-surface-800 dark:text-surface-100">{{ label }}</span>

        <!-- Stato del salvataggio -->
        <span v-if="isSaving" class="hidden items-center gap-1 text-[11px] text-surface-500 @[22rem]:inline-flex">
            <Loader2 :size="12" class="animate-spin" /> Salvataggio…
        </span>
        <span v-else-if="hasChanges" class="hidden items-center gap-1.5 text-[11px] font-medium text-amber-600 @[22rem]:inline-flex dark:text-amber-400">
            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Non salvato
        </span>

        <div class="flex shrink-0 items-center gap-0.5" @mousedown.stop>
            <slot name="actions" />
            <!-- Il pulsante compare solo quando c'è qualcosa da salvare -->
            <button
                v-if="canSave && (hasChanges || isSaving)"
                type="button"
                :disabled="isSaving || !hasChanges"
                class="inline-flex h-7 items-center gap-1 rounded-md px-2 text-xs font-semibold transition-colors disabled:cursor-default"
                :class="hasChanges
                    ? 'bg-primary-500 text-white shadow-sm hover:bg-primary-600'
                    : 'text-surface-400 dark:text-surface-500'"
                :title="hasChanges ? 'Salva le modifiche' : 'Nessuna modifica da salvare'"
                @click="emit('save')"
            >
                <Save :size="13" />
                <span class="hidden @[14rem]:inline">Salva</span>
            </button>
            <button
                type="button"
                class="ml-icon-btn h-7 w-7 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10 dark:hover:text-rose-400"
                title="Elimina blocco"
                aria-label="Elimina blocco"
                @click="emit('delete')"
            >
                <Trash2 :size="14" />
            </button>
        </div>
    </div>
</template>
