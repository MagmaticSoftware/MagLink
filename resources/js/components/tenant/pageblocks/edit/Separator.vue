<script setup lang="ts">
import { useConfirm } from 'primevue/useconfirm';
import BlockCardHeader from './BlockCardHeader.vue';
import { LucideMinus } from 'lucide-vue-next';
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
  <div class="flex h-full w-full flex-col">
    <BlockCardHeader
      label="Separatore"
      :icon="LucideMinus"
      tone="slate"
      :can-save="false"
      @delete="deleteBlock"
    />

    <!-- Content -->
    <div class="flex-1 flex items-center justify-center bg-surface-50 dark:bg-surface-900 rounded-lg border border-surface-200 dark:border-surface-700 p-3">
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
