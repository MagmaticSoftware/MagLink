<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { LucideMapPin } from 'lucide-vue-next';

const props = defineProps<{
  id: number;
  title?: string;
  content: string; // Format: "latitude,longitude" or address
  position?: {
    x: number;
    y: number;
  };
}>();

const mapContainer = ref<HTMLDivElement | null>(null);
const error = ref<string | null>(null);

// Parse coordinates from content
const coordinates = computed(() => {
  if (!props.content) return null;
  
  // Try to parse as "lat,lng"
  const parts = props.content.split(',').map(p => p.trim());
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

const locationText = computed(() => {
  if (props.title) return props.title;
  if (coordinates.value) {
    return `${coordinates.value.lat.toFixed(4)}, ${coordinates.value.lng.toFixed(4)}`;
  }
  return 'Posizione';
});
</script>

<template>
  <div class="h-full w-full flex flex-col overflow-hidden">
    <div v-if="mapUrl" class="relative flex-1">
      <iframe
        :src="mapUrl"
        class="absolute inset-0 w-full h-full border-0"
        allowfullscreen
        loading="lazy"
      ></iframe>
      
      <!-- Location label overlay -->
      <div class="absolute bottom-4 left-4 bg-white dark:bg-slate-800 px-3 py-2 rounded-lg shadow-lg border border-slate-200 dark:border-slate-700 flex items-center gap-2">
        <LucideMapPin class="w-4 h-4 text-blue-600 dark:text-blue-400" />
        <span class="text-sm font-medium text-slate-900 dark:text-slate-50">{{ locationText }}</span>
      </div>
    </div>
    
    <div v-else class="flex-1 flex items-center justify-center bg-slate-100 dark:bg-slate-800">
      <div class="text-center">
        <LucideMapPin class="w-12 h-12 mx-auto mb-3 text-slate-400" />
        <p class="text-sm text-slate-500 dark:text-slate-400">
          Inserisci coordinate (lat,lng) per visualizzare la mappa
        </p>
      </div>
    </div>
  </div>
</template>
