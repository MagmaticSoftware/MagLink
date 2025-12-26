<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import { useConfirm } from 'primevue/useconfirm';
import { LucideSave, LucideCode, LucideTrash2, LucideEye, LucideEyeOff } from 'lucide-vue-next';
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
  <div class="h-full w-full flex flex-col p-2">
    <!-- Header with actions -->
    <div class="flex items-center justify-between mb-3 pb-2 border-b border-surface-200 dark:border-surface-700">
      <div class="flex items-center gap-2">
        <div class="p-1.5 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
          <LucideCode :size="16" class="text-orange-600 dark:text-orange-400" />
        </div>
        <span class="text-xs font-medium text-surface-600 dark:text-surface-400">HTML Block</span>
      </div>
      <div class="flex items-center gap-1">
        <button
          @click="showPreview = !showPreview"
          class="p-1.5 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
          :title="showPreview ? 'Show code' : 'Show preview'"
        >
          <LucideEye v-if="!showPreview" :size="14" class="text-blue-600 dark:text-blue-400" />
          <LucideEyeOff v-else :size="14" class="text-blue-600 dark:text-blue-400" />
        </button>
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

    <!-- Editable content -->
    <div class="flex-1 flex flex-col gap-3 overflow-y-auto">
      <div>
        <label class="text-xs font-medium text-surface-700 dark:text-surface-300 block mb-1">
          Title (optional)
        </label>
        <InputText 
          v-model="localTitle" 
          placeholder="HTML block title..."
          @blur="saveBlock"
          class="w-full text-sm"
        />
      </div>
      
      <div v-if="!showPreview" class="flex-1">
        <label class="text-xs font-medium text-surface-700 dark:text-surface-300 block mb-1">
          HTML Content
        </label>
        <Textarea 
          v-model="localContent" 
          placeholder="<div>Your HTML here...</div>"
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
