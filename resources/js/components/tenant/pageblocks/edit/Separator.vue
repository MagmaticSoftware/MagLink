<script setup lang="ts">
import { useConfirm } from 'primevue/useconfirm';
import { LucideMinus, LucideTrash2 } from 'lucide-vue-next';
import axios from 'axios';

const confirm = useConfirm();

const props = defineProps<{
  id: number;
  pageId: number;
  title?: string;
  content?: string;
  position?: {
    x: number;
    y: number;
  };
  isActive: boolean;
}>();

const emit = defineEmits<{
  deleted: [id: number];
}>();

const deleteBlock = () => {
  confirm.require({
    message: 'Questa azione eliminerà definitivamente il blocco. Continuare?',
    header: 'Conferma eliminazione',
    acceptProps: {
      label: 'Elimina'
    },
    rejectProps: {
      label: 'Annulla'
    },
    accept: async () => {
      try {
        await axios.delete(route('page-blocks.destroy', props.id));
        emit('deleted', props.id);
      } catch (error) {
        console.error('Failed to delete block:', error);
      }
    }
  });
};
</script>

<template>
  <div class="h-full w-full flex flex-col p-2">
    <!-- Header with actions -->
    <div class="flex items-center justify-between mb-3 pb-2 border-b border-surface-200 dark:border-surface-700">
      <div class="flex items-center gap-2">
        <div class="p-1.5 bg-slate-100 dark:bg-slate-900/30 rounded-lg">
          <LucideMinus :size="16" class="text-slate-600 dark:text-slate-400" />
        </div>
        <span class="text-xs font-medium text-surface-600 dark:text-surface-400">Separatore</span>
      </div>
      <div class="flex items-center gap-1">
        <button
          @click="deleteBlock"
          class="p-1.5 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
          title="Delete block"
        >
          <LucideTrash2 :size="14" class="text-red-600 dark:text-red-400" />
        </button>
      </div>
    </div>

    <!-- Content -->
    <div class="flex-1 flex items-center justify-center bg-surface-50 dark:bg-surface-900 rounded-lg border border-surface-200 dark:border-surface-700">
      <div class="text-center">
        <LucideMinus class="w-12 h-12 mx-auto mb-2 text-slate-400" />
        <p class="text-sm text-surface-500 dark:text-surface-400">
          Linea divisoria
        </p>
        <p class="text-xs text-surface-400 dark:text-surface-500 mt-1">
          Non richiede configurazione
        </p>
      </div>
    </div>
  </div>
</template>
