<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import { useConfirm } from 'primevue/useconfirm';
import { LucideSave, LucideImage, LucideTrash2, LucideLink2 } from 'lucide-vue-next';
import InputText from '@/components/volt/InputText.vue';

const confirm = useConfirm();

const props = defineProps<{
  id: number;
  pageId: number;
  title?: string;
  content: string;
  settings?: any;
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
const localLinkUrl = ref(props.settings?.link || '');
const isSaving = ref(false);
const imageError = ref(false);
const savedTitle = ref(props.title || '');
const savedContent = ref(props.content || '');
const savedLinkUrl = ref(props.settings?.link || '');

const hasChanges = computed(() => {
  return localTitle.value !== savedTitle.value || 
         localContent.value !== savedContent.value ||
         localLinkUrl.value !== savedLinkUrl.value;
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
  imageError.value = false;
});
watch(() => props.settings?.link, (newVal) => {
  localLinkUrl.value = newVal || '';
  savedLinkUrl.value = newVal || '';
});

const imageUrl = computed(() => {
  try {
    const parsed = JSON.parse(localContent.value);
    return parsed.url || parsed.src || localContent.value;
  } catch {
    return localContent.value;
  }
});

const saveBlock = async () => {
  isSaving.value = true;
  try {
    await axios.put(route('page-blocks.update', props.id), {
      title: localTitle.value,
      content: localContent.value,
      settings: { link: localLinkUrl.value },
    });
    savedTitle.value = localTitle.value;
    savedContent.value = localContent.value;
    savedLinkUrl.value = localLinkUrl.value;
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
        <div class="p-1.5 bg-pink-100 dark:bg-pink-900/30 rounded-lg">
          <LucideImage :size="16" class="text-pink-600 dark:text-pink-400" />
        </div>
        <span class="text-xs font-medium text-surface-600 dark:text-surface-400">Image Block</span>
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

    <!-- Editable content -->
    <div class="flex-1 flex flex-col gap-3 overflow-y-auto">
      <div>
        <label class="text-xs font-medium text-surface-700 dark:text-surface-300 block mb-1">
          Image Title (optional)
        </label>
        <InputText 
          v-model="localTitle" 
          placeholder="Image caption..."
          @blur="saveBlock"
          class="w-full text-sm"
        />
      </div>
      
      <div>
        <label class="text-xs font-medium text-surface-700 dark:text-surface-300 block mb-1">
          Image URL
        </label>
        <InputText 
          v-model="localContent" 
          placeholder="https://example.com/image.jpg"
          @blur="saveBlock"
          type="url"
          class="w-full text-sm font-mono"
        />
      </div>
      
      <div>
        <label class="text-xs font-medium text-surface-700 dark:text-surface-300 block mb-1">
          <LucideLink2 :size="12" class="inline mr-1" />
          Link URL (opzionale)
        </label>
        <InputText 
          v-model="localLinkUrl" 
          placeholder="https://example.com"
          @blur="saveBlock"
          type="url"
          class="w-full text-sm font-mono"
        />
        <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">
          L'immagine diventerà cliccabile se inserisci un link
        </p>
      </div>

      <!-- Image Preview -->
      <div v-if="imageUrl" class="flex-1 min-h-[100px] relative rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800">
        <img 
          v-if="!imageError"
          :src="imageUrl" 
          :alt="localTitle || 'Preview'"
          @error="imageError = true"
          class="w-full h-full object-cover"
        />
        <div v-else class="w-full h-full flex items-center justify-center">
          <div class="text-center">
            <LucideImage :size="32" class="mx-auto text-slate-300 dark:text-slate-600 mb-1" />
            <p class="text-xs text-slate-500 dark:text-slate-400">Failed to load image</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>