<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import { useConfirm } from 'primevue/useconfirm';
import { LucideSave, LucideHeading1, LucideTrash2 } from 'lucide-vue-next';
import InputText from '@/components/volt/InputText.vue';

const confirm = useConfirm();

const props = defineProps<{
  id: number;
  pageId: number;
  title?: string;
  content: string;
  position?: {
    x: number;
    y: number;
  };
  isActive: boolean;
}>();

const emit = defineEmits<{
  deleted: [id: number];
  dirtyChange: [id: number, isDirty: boolean];
}>();

const localContent = ref(props.content || '');
const isSaving = ref(false);
const savedContent = ref(props.content || '');

const hasChanges = computed(() => {
  return localContent.value !== savedContent.value;
});

watch(hasChanges, (isDirty) => {
  emit('dirtyChange', props.id, isDirty);
});

watch(() => props.content, (newVal) => { 
  localContent.value = newVal || ''; 
  savedContent.value = newVal || '';
});

const saveBlock = async () => {
  isSaving.value = true;
  try {
    await axios.put(route('page-blocks.update', props.id), {
      content: localContent.value,
    });
    savedContent.value = localContent.value;
  } catch (error) {
    console.error('Failed to save block:', error);
  } finally {
    isSaving.value = false;
  }
};

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
        <div class="p-1.5 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
          <LucideHeading1 :size="16" class="text-indigo-600 dark:text-indigo-400" />
        </div>
        <span class="text-xs font-medium text-surface-600 dark:text-surface-400">Titolo</span>
      </div>
      <div class="flex items-center gap-1">
        <button
          @click="saveBlock"
          :disabled="isSaving"
          class="p-1.5 rounded-lg transition-colors disabled:opacity-50"
          :class="hasChanges ? 'bg-green-500 hover:bg-green-600 animate-pulse' : 'hover:bg-green-100 dark:hover:bg-green-900/30'"
          :title="hasChanges ? 'Save pending changes' : 'No changes to save'"
        >
          <LucideSave :size="14" :class="hasChanges ? 'text-white' : 'text-green-600 dark:text-green-400'" />
        </button>
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
    <div class="flex-1 flex flex-col gap-3 overflow-y-auto">
      <div>
        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
          Testo del Titolo
        </label>
        <InputText
          v-model="localContent"
          placeholder="Inserisci il titolo..."
          class="w-full"
        />
      </div>
      
      <!-- Preview -->
      <div class="flex-1 bg-surface-50 dark:bg-surface-900 rounded-lg p-4 border border-surface-200 dark:border-surface-700">
        <p class="text-xs text-surface-500 dark:text-surface-400 mb-2">Anteprima:</p>
        <h2 class="text-2xl font-bold text-surface-900 dark:text-surface-50 text-center">
          {{ localContent || 'Titolo' }}
        </h2>
      </div>
    </div>
  </div>
</template>
