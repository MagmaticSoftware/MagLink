<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { LucideSearch, LucideMapPin, LucideX } from 'lucide-vue-next';
import Dialog from '@/components/volt/Dialog.vue';
import InputText from '@/components/volt/InputText.vue';
import Button from '@/components/volt/Button.vue';

const props = defineProps<{
  visible: boolean;
  initialCoordinates?: string;
}>();

const emit = defineEmits<{
  'update:visible': [value: boolean];
  'select': [coordinates: string, locationName: string];
}>();

const searchQuery = ref('');
const manualLat = ref('');
const manualLng = ref('');
const locationName = ref('');
const searchResults = ref<any[]>([]);
const selectedCoordinates = ref<{ lat: number; lng: number } | null>(null);
const isSearching = ref(false);
const activeTab = ref<'search' | 'manual'>('search');

// Parse initial coordinates
watch(() => props.initialCoordinates, (coords) => {
  if (coords) {
    const parts = coords.split(',').map(p => p.trim());
    if (parts.length === 2) {
      manualLat.value = parts[0];
      manualLng.value = parts[1];
      selectedCoordinates.value = {
        lat: parseFloat(parts[0]),
        lng: parseFloat(parts[1])
      };
    }
  }
}, { immediate: true });

const mapUrl = computed(() => {
  if (!selectedCoordinates.value) return null;
  const { lat, lng } = selectedCoordinates.value;
  return `https://www.openstreetmap.org/export/embed.html?bbox=${lng-0.01},${lat-0.01},${lng+0.01},${lat+0.01}&layer=mapnik&marker=${lat},${lng}`;
});

const isValidCoordinates = computed(() => {
  if (activeTab.value === 'manual') {
    const lat = parseFloat(manualLat.value);
    const lng = parseFloat(manualLng.value);
    return !isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180;
  }
  return !!selectedCoordinates.value;
});

// Search using Nominatim API
const searchLocation = async () => {
  if (!searchQuery.value.trim()) return;
  
  isSearching.value = true;
  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchQuery.value)}&limit=5`
    );
    const data = await response.json();
    searchResults.value = data;
  } catch (error) {
    console.error('Errore ricerca:', error);
  } finally {
    isSearching.value = false;
  }
};

const selectLocation = (result: any) => {
  selectedCoordinates.value = {
    lat: parseFloat(result.lat),
    lng: parseFloat(result.lon)
  };
  locationName.value = result.display_name;
  manualLat.value = result.lat;
  manualLng.value = result.lon;
};

const updateManualCoordinates = () => {
  const lat = parseFloat(manualLat.value);
  const lng = parseFloat(manualLng.value);
  
  if (!isNaN(lat) && !isNaN(lng)) {
    selectedCoordinates.value = { lat, lng };
  }
};

const handleConfirm = () => {
  if (isValidCoordinates.value) {
    let coords: string;
    let name: string;
    
    if (activeTab.value === 'manual') {
      coords = `${manualLat.value}, ${manualLng.value}`;
      name = locationName.value || `${manualLat.value}, ${manualLng.value}`;
    } else if (selectedCoordinates.value) {
      coords = `${selectedCoordinates.value.lat}, ${selectedCoordinates.value.lng}`;
      name = locationName.value;
    } else {
      return;
    }
    
    emit('select', coords, name);
    handleClose();
  }
};

const handleClose = () => {
  emit('update:visible', false);
  // Reset dopo un breve delay per evitare flickering
  setTimeout(() => {
    searchQuery.value = '';
    searchResults.value = [];
    activeTab.value = 'search';
  }, 300);
};
</script>

<template>
  <Dialog 
    :visible="visible" 
    @update:visible="handleClose"
    header="Seleziona Posizione"
    :style="{ width: '90vw', maxWidth: '800px' }"
    :modal="true"
  >
    <div class="space-y-4">
      <!-- Tabs -->
      <div class="flex gap-2 border-b border-surface-200 dark:border-surface-700">
        <button
          @click="activeTab = 'search'"
          :class="[
            'px-4 py-2 text-sm font-medium transition-colors border-b-2',
            activeTab === 'search'
              ? 'border-primary text-primary'
              : 'border-transparent text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-surface-100'
          ]"
        >
          <LucideSearch class="inline w-4 h-4 mr-1" />
          Cerca Località
        </button>
        <button
          @click="activeTab = 'manual'"
          :class="[
            'px-4 py-2 text-sm font-medium transition-colors border-b-2',
            activeTab === 'manual'
              ? 'border-primary text-primary'
              : 'border-transparent text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-surface-100'
          ]"
        >
          <LucideMapPin class="inline w-4 h-4 mr-1" />
          Coordinate Manuali
        </button>
      </div>

      <!-- Search Tab -->
      <div v-if="activeTab === 'search'" class="space-y-4">
        <div class="flex gap-2">
          <InputText
            v-model="searchQuery"
            placeholder="Cerca città, indirizzo, punto di interesse..."
            class="flex-1"
            @keyup.enter="searchLocation"
          />
          <Button @click="searchLocation" :loading="isSearching">
            <LucideSearch :size="16" />
            Cerca
          </Button>
        </div>

        <div v-if="searchResults.length > 0" class="space-y-2 max-h-60 overflow-y-auto">
          <button
            v-for="result in searchResults"
            :key="result.place_id"
            @click="selectLocation(result)"
            :class="[
              'w-full text-left p-3 rounded-lg border transition-colors',
              selectedCoordinates?.lat === parseFloat(result.lat) && selectedCoordinates?.lng === parseFloat(result.lon)
                ? 'border-primary bg-primary/10'
                : 'border-surface-200 dark:border-surface-700 hover:border-primary/50 bg-surface-50 dark:bg-surface-900'
            ]"
          >
            <div class="flex items-start gap-2">
              <LucideMapPin :size="16" class="mt-1 text-primary shrink-0" />
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-surface-900 dark:text-surface-50 truncate">
                  {{ result.display_name }}
                </p>
                <p class="text-xs text-surface-500 dark:text-surface-400">
                  {{ parseFloat(result.lat).toFixed(6) }}, {{ parseFloat(result.lon).toFixed(6) }}
                </p>
              </div>
            </div>
          </button>
        </div>

        <p v-else-if="searchQuery && !isSearching" class="text-sm text-surface-500 dark:text-surface-400 text-center py-4">
          Nessun risultato trovato. Prova con una ricerca diversa.
        </p>
      </div>

      <!-- Manual Tab -->
      <div v-if="activeTab === 'manual'" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
              Latitudine
            </label>
            <InputText
              v-model="manualLat"
              type="number"
              step="0.000001"
              min="-90"
              max="90"
              placeholder="es. 52.520008"
              class="w-full"
              @input="updateManualCoordinates"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
              Longitudine
            </label>
            <InputText
              v-model="manualLng"
              type="number"
              step="0.000001"
              min="-180"
              max="180"
              placeholder="es. 13.404954"
              class="w-full"
              @input="updateManualCoordinates"
            />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
            Nome Località (opzionale)
          </label>
          <InputText
            v-model="locationName"
            placeholder="es. Berlin, Germany"
            class="w-full"
          />
        </div>

        <p class="text-xs text-surface-500 dark:text-surface-400">
          Inserisci coordinate valide: Latitudine tra -90 e 90, Longitudine tra -180 e 180
        </p>
      </div>

      <!-- Map Preview -->
      <div v-if="mapUrl" class="space-y-2">
        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">
          Anteprima
        </label>
        <div class="relative h-64 bg-surface-100 dark:bg-surface-900 rounded-lg overflow-hidden border border-surface-200 dark:border-surface-700">
          <iframe
            :src="mapUrl"
            class="w-full h-full border-0"
            allowfullscreen
            loading="lazy"
          ></iframe>
          <div v-if="locationName" class="absolute bottom-2 left-2 bg-white dark:bg-slate-800 px-2 py-1 rounded shadow-lg text-xs">
            <LucideMapPin class="w-3 h-3 inline mr-1 text-cyan-600" />
            {{ locationName }}
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end gap-2">
        <Button severity="secondary" @click="handleClose">
          Annulla
        </Button>
        <Button @click="handleConfirm" :disabled="!isValidCoordinates">
          Conferma
        </Button>
      </div>
    </template>
  </Dialog>
</template>
