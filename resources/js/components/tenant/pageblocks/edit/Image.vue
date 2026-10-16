<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import { useConfirm } from 'primevue/useconfirm';
import BlockCardHeader from './BlockCardHeader.vue';
import { LucideImage, LucideLink2 } from 'lucide-vue-next';
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

// Parse settings if it's a JSON string
const parsedSettings = computed(() => {
  if (!props.settings) return {};
  if (typeof props.settings === 'string') {
    try {
      return JSON.parse(props.settings);
    } catch {
      return {};
    }
  }
  return props.settings;
});

const localTitle = ref(props.title || '');
// Alcuni blocchi salvano il contenuto come JSON ({"url": ...}): nel campo mostriamo solo l'URL
const extractUrl = (content?: string) => {
  if (!content) return '';
  try {
    const parsed = JSON.parse(content);
    return parsed?.url || parsed?.src || content;
  } catch {
    return content;
  }
};

const localContent = ref(extractUrl(props.content));
const localImageLinkUrl = ref(parsedSettings.value.link || '');
const isSaving = ref(false);
const imageError = ref(false);
const savedTitle = ref(props.title || '');
const savedContent = ref(extractUrl(props.content));
const savedImageLinkUrl = ref(parsedSettings.value.link || '');

const hasChanges = computed(() => {
  return localTitle.value !== savedTitle.value || 
         localContent.value !== savedContent.value ||
         localImageLinkUrl.value !== savedImageLinkUrl.value;
});

watch(hasChanges, (isDirty) => {
  emit('dirtyChange', props.id, isDirty);
});

watch(() => props.title, (newVal) => { 
  localTitle.value = newVal || ''; 
  savedTitle.value = newVal || '';
});
watch(() => props.content, (newVal) => { 
  localContent.value = extractUrl(newVal); 
  savedContent.value = extractUrl(newVal);
  imageError.value = false;
});
watch(() => parsedSettings.value.link, (newVal) => {
  localImageLinkUrl.value = newVal || '';
  savedImageLinkUrl.value = newVal || '';
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
      settings: { link: localImageLinkUrl.value },
    });
    savedTitle.value = localTitle.value;
    savedContent.value = localContent.value;
    savedImageLinkUrl.value = localImageLinkUrl.value;
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
      label="Immagine"
      :icon="LucideImage"
      tone="pink"
      :has-changes="hasChanges"
      :is-saving="isSaving"
      @save="saveBlock"
      @delete="deleteBlock"
    />

    <!-- Editable content -->
    <div class="flex-1 flex flex-col gap-3 overflow-y-auto p-3">
      <div>
        <label class="ml-label mb-1 text-xs">
          Titolo immagine (opzionale)
        </label>
        <InputText 
          v-model="localTitle" 
          placeholder="Didascalia..."
          @blur="saveBlock"
          class="w-full text-sm"
        />
      </div>
      
      <div>
        <label class="ml-label mb-1 text-xs">
          URL immagine
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
        <label class="ml-label mb-1 text-xs">
          <LucideLink2 :size="12" class="inline mr-1" />
          Link URL (opzionale)
        </label>
        <InputText 
          v-model="localImageLinkUrl" 
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
            <p class="text-xs text-slate-500 dark:text-slate-400">Impossibile caricare l’immagine</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>