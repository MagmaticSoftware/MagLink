<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import { useConfirm } from 'primevue/useconfirm';
import BlockCardHeader from './BlockCardHeader.vue';
import { LucideMapPin, LucideSearch } from 'lucide-vue-next';
import InputText from '@/components/volt/InputText.vue';
import Button from '@/components/volt/Button.vue';
import MapCoordinatePicker from '@/components/MapCoordinatePicker.vue';

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
const showCoordinatePicker = ref(false);

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

// Parse coordinates for preview
const coordinates = computed(() => {
  if (!localContent.value) return null;
  
  const parts = localContent.value.split(',').map(p => p.trim());
  if (parts.length === 2) {
    const lat = parseFloat(parts[0]);
    const lng = parseFloat(parts[1]);
    if (!isNaN(lat) && !isNaN(lng)) {
      return { lat, lng };
    }
  }
  return null;
});

const mapUrl = computed(() => {
  if (!coordinates.value) return null;
  const { lat, lng } = coordinates.value;
  return `https://www.openstreetmap.org/export/embed.html?bbox=${lng-0.01},${lat-0.01},${lng+0.01},${lat+0.01}&layer=mapnik&marker=${lat},${lng}`;
});

const handleCoordinateSelect = (coords: string, name: string) => {
  localContent.value = coords;
  if (name && !localTitle.value) {
    localTitle.value = name;
  }
};

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
      label="Mappa"
      :icon="LucideMapPin"
      tone="cyan"
      :has-changes="hasChanges"
      :is-saving="isSaving"
      @save="saveBlock"
      @delete="deleteBlock"
    />

    <!-- Content -->
    <div class="flex-1 flex flex-col gap-3 overflow-y-auto p-3">
      <div>
        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
          Nome della posizione (opzionale)
        </label>
        <InputText
          v-model="localTitle"
          placeholder="es. Milano, Italia"
          class="w-full"
        />
      </div>
      
      <div>
        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
          Coordinate
        </label>
        <div class="flex gap-2">
          <InputText
            v-model="localContent"
            placeholder="es. 45.4642, 9.1900"
            class="flex-1"
            readonly
          />
          <Button @click="showCoordinatePicker = true" severity="secondary">
            <LucideSearch :size="16" />
            Cerca
          </Button>
        </div>
        <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">
          Clicca "Cerca" per selezionare una posizione dalla mappa
        </p>
      </div>
      
      <!-- Preview -->
      <div class="flex-1 bg-surface-50 dark:bg-surface-900 rounded-lg overflow-hidden border border-surface-200 dark:border-surface-700">
        <div v-if="mapUrl" class="relative h-full min-h-[300px]">
          <iframe
            :src="mapUrl"
            class="w-full h-full border-0"
            allowfullscreen
            loading="lazy"
          ></iframe>
          <div v-if="localTitle" class="absolute bottom-3 left-3 bg-white dark:bg-slate-800 px-3 py-1.5 rounded-lg shadow-lg text-sm flex items-center gap-2">
            <LucideMapPin class="w-4 h-4 text-cyan-600" />
            <span class="font-medium">{{ localTitle }}</span>
          </div>
        </div>
        <div v-else class="h-full min-h-[300px] flex items-center justify-center">
          <div class="text-center">
            <LucideMapPin class="w-16 h-16 mx-auto mb-3 text-surface-300 dark:text-surface-600" />
            <p class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
              Nessuna posizione selezionata
            </p>
            <p class="text-xs text-surface-500 dark:text-surface-400">
              Clicca "Cerca" per selezionare una posizione
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Coordinate Picker Modal -->
  <MapCoordinatePicker
    v-model:visible="showCoordinatePicker"
    :initial-coordinates="localContent"
    @select="handleCoordinateSelect"
  />
</template>
