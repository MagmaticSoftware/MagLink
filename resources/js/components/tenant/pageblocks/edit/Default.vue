<script setup lang="ts">
import { LucideAlertCircle } from 'lucide-vue-next';
import axios from 'axios';
import { useConfirm } from 'primevue/useconfirm';
import BlockCardHeader from './BlockCardHeader.vue';

const confirm = useConfirm();

const props = defineProps<{
  id: number;
  type: string;
  title?: string;
  content: any;
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
      label="Blocco sconosciuto"
      :icon="LucideAlertCircle"
      tone="amber"
      :can-save="false"
      @delete="deleteBlock"
    />

    <!-- Content -->
    <div class="flex-1 flex flex-col items-center justify-center text-center overflow-y-auto p-3">
      <h3 class="text-base font-semibold text-slate-900 dark:text-slate-50 mb-2">
        {{ title || 'Unknown Block Type' }}
      </h3>
      <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">
        Tipo: <span class="font-mono text-amber-600 dark:text-amber-400">{{ type }}</span>
      </p>
      <div v-if="content" class="max-w-full overflow-hidden mt-3">
        <details class="text-xs text-left">
          <summary class="cursor-pointer text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200">
            Mostra contenuto grezzo
          </summary>
          <pre class="mt-2 p-2 bg-slate-100 dark:bg-slate-800 rounded text-slate-700 dark:text-slate-300 overflow-x-auto text-xs">{{ JSON.stringify(content, null, 2) }}</pre>
        </details>
      </div>
    </div>
  </div>
</template>
