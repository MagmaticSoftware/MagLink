<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import { useConfirm } from 'primevue/useconfirm';
import { LucideSave, LucideLink2, LucideTrash2, LucideExternalLink, LucideLoader2 } from 'lucide-vue-next';
import InputText from '@/components/volt/InputText.vue';
import Button from '@/components/volt/Button.vue';

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
const metadata = ref<any>(null);
const isSaving = ref(false);
const isFetchingMetadata = ref(false);
const savedTitle = ref(props.title || '');
const savedContent = ref(props.content || '');

// Load metadata from settings if available
if (props.settings) {
  try {
    const settings = typeof props.settings === 'string' ? JSON.parse(props.settings) : props.settings;
    if (settings.metadata) {
      metadata.value = settings.metadata;
    }
  } catch (e) {
    // Ignore parse errors
  }
}

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

// Auto-fetch metadata when URL changes and is valid
watch(() => localContent.value, async (newUrl, oldUrl) => {
  if (newUrl !== oldUrl && isValidUrl.value && !metadata.value) {
    // Debounce to avoid too many requests
    await new Promise(resolve => setTimeout(resolve, 1000));
    if (localContent.value === newUrl) {
      await fetchMetadata();
    }
  }
});

const isValidUrl = computed(() => {
  try {
    new URL(localContent.value);
    return true;
  } catch {
    return false;
  }
});

const fetchMetadata = async () => {
  if (!isValidUrl.value) return;
  
  isFetchingMetadata.value = true;
  try {
    const response = await axios.post(route('blocks.fetch-url-metadata'), {
      url: localContent.value
    });
    metadata.value = response.data;
    
    // Auto-fill title if empty
    if (!localTitle.value && metadata.value.title) {
      localTitle.value = metadata.value.title;
    }
  } catch (error) {
    console.error('Failed to fetch metadata:', error);
  } finally {
    isFetchingMetadata.value = false;
  }
};

const saveBlock = async () => {
  isSaving.value = true;
  try {
    await axios.put(route('page-blocks.update', props.id), {
      title: localTitle.value,
      content: localContent.value,
      settings: JSON.stringify({ metadata: metadata.value }),
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
        <div class="p-1.5 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
          <LucideLink2 :size="16" class="text-blue-600 dark:text-blue-400" />
        </div>
        <span class="text-xs font-medium text-surface-600 dark:text-surface-400">Link Block</span>
      </div>
      <div class="flex items-center gap-1">
        <a
          v-if="isValidUrl"
          :href="localContent"
          target="_blank"
          rel="noopener noreferrer"
          class="p-1.5 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
          title="Test link"
        >
          <LucideExternalLink :size="14" class="text-blue-600 dark:text-blue-400" />
        </a>
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
          Link Title
        </label>
        <InputText 
          v-model="localTitle" 
          placeholder="e.g., Visit my website"
          @blur="saveBlock"
          class="w-full text-sm"
        />
      </div>
      
      <div>
        <label class="text-xs font-medium text-surface-700 dark:text-surface-300 block mb-1">
          URL
        </label>
        <div class="flex gap-2">
          <InputText 
            v-model="localContent" 
            placeholder="https://example.com"
            @blur="saveBlock"
            type="url"
            class="flex-1 text-sm font-mono"
            :class="{ 'border-red-500': localContent && !isValidUrl }"
          />
          <Button 
            @click="fetchMetadata"
            :loading="isFetchingMetadata"
            :disabled="!isValidUrl || isFetchingMetadata"
            severity="secondary"
            size="small"
          >
            <LucideLoader2 v-if="isFetchingMetadata" :size="14" class="animate-spin" />
            <template v-else>Carica Anteprima</template>
          </Button>
        </div>
        <p v-if="localContent && !isValidUrl" class="text-xs text-red-600 dark:text-red-400 mt-1">
          Invalid URL format
        </p>
      </div>
      
      <!-- Metadata Preview -->
      <div v-if="metadata" class="p-3 bg-surface-50 dark:bg-surface-900 rounded-lg border border-surface-200 dark:border-surface-700">
        <div class="flex items-start gap-3">
          <img 
            v-if="metadata.favicon"
            :src="metadata.favicon"
            alt="Favicon"
            class="w-5 h-5 object-contain shrink-0"
            @error="(e) => (e.target as HTMLImageElement).style.display = 'none'"
          />
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-surface-900 dark:text-surface-50 truncate">
              {{ metadata.title || localTitle || 'Untitled' }}
            </p>
            <p v-if="metadata.description" class="text-xs text-surface-600 dark:text-surface-400 line-clamp-2 mt-1">
              {{ metadata.description }}
            </p>
          </div>
        </div>
        <img 
          v-if="metadata.image"
          :src="metadata.image"
          alt="Preview"
          class="w-full h-24 object-cover rounded mt-2"
          @error="(e) => (e.target as HTMLImageElement).style.display = 'none'"
        />
      </div>
    </div>
  </div>
</template>
