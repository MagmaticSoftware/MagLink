<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import { useConfirm } from 'primevue/useconfirm';
import BlockCardHeader from './BlockCardHeader.vue';
import { LucideMousePointer } from 'lucide-vue-next';
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
const localContent = ref(props.content || '');
const localBackgroundColor = ref(parsedSettings.value.backgroundColor || '#3b82f6');
const localTextColor = ref(parsedSettings.value.textColor || '#ffffff');
const isSaving = ref(false);
const savedTitle = ref(props.title || '');
const savedContent = ref(props.content || '');
const savedBackgroundColor = ref(parsedSettings.value.backgroundColor || '#3b82f6');
const savedTextColor = ref(parsedSettings.value.textColor || '#ffffff');

const hasChanges = computed(() => {
  return localTitle.value !== savedTitle.value || 
         localContent.value !== savedContent.value ||
         localBackgroundColor.value !== savedBackgroundColor.value ||
         localTextColor.value !== savedTextColor.value;
});

const isValidUrl = computed(() => {
  if (!localContent.value) return false;
  try {
    new URL(localContent.value);
    return true;
  } catch {
    return false;
  }
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
watch(() => parsedSettings.value.backgroundColor, (newVal) => {
  localBackgroundColor.value = newVal || '#3b82f6';
  savedBackgroundColor.value = newVal || '#3b82f6';
});
watch(() => parsedSettings.value.textColor, (newVal) => {
  localTextColor.value = newVal || '#ffffff';
  savedTextColor.value = newVal || '#ffffff';
});

const saveBlock = async () => {
  isSaving.value = true;
  try {
    await axios.put(route('page-blocks.update', props.id), {
      title: localTitle.value,
      content: localContent.value,
      settings: {
        backgroundColor: localBackgroundColor.value,
        textColor: localTextColor.value,
      },
    });
    savedTitle.value = localTitle.value;
    savedContent.value = localContent.value;
    savedBackgroundColor.value = localBackgroundColor.value;
    savedTextColor.value = localTextColor.value;
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
      label="Pulsante"
      :icon="LucideMousePointer"
      tone="blue"
      :has-changes="hasChanges"
      :is-saving="isSaving"
      @save="saveBlock"
      @delete="deleteBlock"
    />

    <!-- Editable content -->
    <div class="flex-1 flex flex-col gap-3 overflow-y-auto p-3">
      <div>
        <label class="ml-label mb-1 text-xs">
          Testo del pulsante
        </label>
        <InputText 
          v-model="localTitle" 
          placeholder="es. Visita il nostro sito"
          @blur="saveBlock"
          class="w-full text-sm"
        />
      </div>
      
      <div>
        <label class="ml-label mb-1 text-xs">
          URL
        </label>
        <InputText 
          v-model="localContent" 
          placeholder="https://example.com"
          @blur="saveBlock"
          type="url"
          class="w-full text-sm font-mono"
          :class="{ 'border-red-500': localContent && !isValidUrl }"
        />
      </div>

      <div class="@container"><div class="grid grid-cols-1 gap-3 @[16rem]:grid-cols-2">
        <div>
          <label class="ml-label mb-1 text-xs">
            Colore sfondo
          </label>
          <div class="flex gap-2">
            <input 
              v-model="localBackgroundColor" 
              type="color"
              @blur="saveBlock"
              class="h-10 w-10 shrink-0 cursor-pointer rounded-lg border border-surface-200 bg-white p-1 dark:border-surface-600 dark:bg-surface-900"
            />
            <InputText 
              v-model="localBackgroundColor" 
              @blur="saveBlock"
              class="min-w-0 flex-1 font-mono text-sm"
              placeholder="#3b82f6"
            />
          </div>
        </div>

        <div>
          <label class="ml-label mb-1 text-xs">
            Colore testo
          </label>
          <div class="flex gap-2">
            <input 
              v-model="localTextColor" 
              type="color"
              @blur="saveBlock"
              class="h-10 w-10 shrink-0 cursor-pointer rounded-lg border border-surface-200 bg-white p-1 dark:border-surface-600 dark:bg-surface-900"
            />
            <InputText 
              v-model="localTextColor" 
              @blur="saveBlock"
              class="min-w-0 flex-1 font-mono text-sm"
              placeholder="#ffffff"
            />
          </div>
        </div>
      </div></div>

      <!-- Preview -->
      <div class="mt-2">
        <label class="ml-label mb-1 text-xs">
          Anteprima
        </label>
        <div 
          class="w-full px-6 py-3 rounded-lg text-center font-semibold cursor-default transition-all"
          :style="{ 
            backgroundColor: localBackgroundColor,
            color: localTextColor 
          }"
        >
          {{ localTitle || 'Button Preview' }}
        </div>
      </div>
    </div>
  </div>
</template>
