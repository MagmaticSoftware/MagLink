<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import { useConfirm } from 'primevue/useconfirm';
import BlockCardHeader from './BlockCardHeader.vue';
import { LucideHeading1 } from 'lucide-vue-next';
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
  <div class="flex h-full w-full flex-col">
    <BlockCardHeader
      label="Titolo"
      :icon="LucideHeading1"
      tone="indigo"
      :has-changes="hasChanges"
      :is-saving="isSaving"
      @save="saveBlock"
      @delete="deleteBlock"
    />

    <!-- Content -->
    <div class="flex-1 flex flex-col gap-3 overflow-y-auto p-3">
      <div>
        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
          Testo del titolo
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
