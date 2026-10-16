<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import { useConfirm } from 'primevue/useconfirm';
import BlockCardHeader from './BlockCardHeader.vue';
import { LucideCode, LucideEye, LucideEyeOff } from 'lucide-vue-next';
import InputText from '@/components/volt/InputText.vue';
import Textarea from '@/components/volt/Textarea.vue';

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

const localTitle = ref(props.title || '');
const localContent = ref(props.content || '');
const isSaving = ref(false);
const showPreview = ref(false);
const savedTitle = ref(props.title || '');
const savedContent = ref(props.content || '');

const hasChanges = computed(() => {
  return localTitle.value !== savedTitle.value || localContent.value !== savedContent.value;
});

watch(hasChanges, (isDirty) => {
  emit('dirtyChange', props.id, isDirty);
});

watch(() => props.title, (newVal) => { 
  localTitle.value = newVal || ''; 
  savedTitle.value = newVal || '';
});
watch(() => props.content, (newVal) => { 
  localContent.value = newVal || ''; 
  savedContent.value = newVal || '';
});

const saveBlock = async () => {
  isSaving.value = true;
  try {
    await axios.put(route('page-blocks.update', props.id), {
      title: localTitle.value,
      content: localContent.value,
    });
    savedTitle.value = localTitle.value;
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
      label="HTML"
      :icon="LucideCode"
      tone="orange"
      :has-changes="hasChanges"
      :is-saving="isSaving"
      @save="saveBlock"
      @delete="deleteBlock"
    >
      <template #actions>
        <button
                  @click="showPreview = !showPreview"
                  class="ml-icon-btn h-7 w-7"
                  :title="showPreview ? 'Mostra codice' : 'Mostra anteprima'"
                >
                  <LucideEye v-if="!showPreview" :size="14" class="text-blue-600 dark:text-blue-400" />
                  <LucideEyeOff v-else :size="14" class="text-blue-600 dark:text-blue-400" />
                </button>
      </template>
    </BlockCardHeader>

    <!-- Editable content -->
    <div class="flex-1 flex flex-col gap-3 overflow-y-auto p-3">
      <div>
        <label class="ml-label mb-1 text-xs">
          Titolo (opzionale)
        </label>
        <InputText 
          v-model="localTitle" 
          placeholder="Titolo del blocco..."
          @blur="saveBlock"
          class="w-full text-sm"
        />
      </div>
      
      <div v-if="!showPreview" class="flex-1">
        <label class="ml-label mb-1 text-xs">
          Codice HTML
        </label>
        <Textarea 
          v-model="localContent" 
          placeholder="<div>Il tuo HTML qui...</div>"
          @blur="saveBlock"
          rows="8"
          class="w-full text-sm font-mono resize-none"
        />
      </div>

      <!-- HTML Preview -->
      <div v-else class="flex-1 p-3 bg-surface-50 dark:bg-surface-800 rounded-lg overflow-y-auto">
        <div v-html="localContent" class="prose prose-sm dark:prose-invert max-w-none" />
      </div>
    </div>
  </div>
</template>
