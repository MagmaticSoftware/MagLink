<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import { useConfirm } from 'primevue/useconfirm';
import BlockCardHeader from './BlockCardHeader.vue';
import { LucideType } from 'lucide-vue-next';
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
      label="Testo"
      :icon="LucideType"
      tone="violet"
      :has-changes="hasChanges"
      :is-saving="isSaving"
      @save="saveBlock"
      @delete="deleteBlock"
    />

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
      
      <div class="flex-1">
        <label class="ml-label mb-1 text-xs">
          Contenuto
        </label>
        <Textarea 
          v-model="localContent" 
          placeholder="Scrivi il testo..."
          @blur="saveBlock"
          rows="6"
          class="w-full text-sm resize-none"
        />
      </div>
    </div>
  </div>
</template>
