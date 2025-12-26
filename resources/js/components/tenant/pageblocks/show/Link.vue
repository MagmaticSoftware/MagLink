<script setup lang="ts">
import { LucideExternalLink, LucideLink2 } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
  id: number;
  title?: string;
  content: string;
  settings?: any;
  position?: {
    x: number;
    y: number;
  };
}>();

// Extract URL from content
const linkUrl = computed(() => {
  try {
    const parsed = JSON.parse(props.content);
    return parsed.url || props.content;
  } catch {
    return props.content;
  }
});

// Extract metadata from settings
const metadata = computed(() => {
  try {
    if (typeof props.settings === 'string') {
      const parsed = JSON.parse(props.settings);
      return parsed.metadata || null;
    }
    return props.settings?.metadata || null;
  } catch {
    return null;
  }
});

const displayUrl = computed(() => {
  try {
    const url = new URL(linkUrl.value);
    return url.hostname.replace('www.', '');
  } catch {
    return linkUrl.value;
  }
});

const displayTitle = computed(() => {
  return props.title || metadata.value?.title || displayUrl.value;
});
</script>

<template>
  <a 
    :href="linkUrl" 
    target="_blank" 
    rel="noopener noreferrer"
    class="h-full w-full flex flex-col p-4 group cursor-pointer hover:scale-[1.02] transition-transform duration-300"
  >
    <div class="flex items-start gap-3">
      <!-- Favicon -->
      <div class="shrink-0">
        <img 
          v-if="metadata?.favicon"
          :src="metadata.favicon"
          alt="Site icon"
          class="w-6 h-6 object-contain rounded"
          @error="(e) => (e.target as HTMLImageElement).style.display = 'none'"
        />
        <div 
          v-else
          class="w-6 h-6 flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 rounded"
        >
          <LucideLink2 :size="14" class="text-blue-600 dark:text-blue-400" />
        </div>
      </div>
      
      <!-- Content -->
      <div class="flex-1 min-w-0">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 mb-1 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
          {{ displayTitle }}
        </h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
          {{ displayUrl }}
        </p>
        <p v-if="metadata?.description" class="text-xs text-slate-600 dark:text-slate-400 mt-2 line-clamp-2">
          {{ metadata.description }}
        </p>
      </div>
      
      <!-- External link icon -->
      <LucideExternalLink 
        :size="16" 
        class="text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors shrink-0 mt-0.5" 
      />
    </div>
    
    <!-- Preview image if available -->
    <img 
      v-if="metadata?.image"
      :src="metadata.image"
      alt="Preview"
      class="w-full h-32 object-cover rounded mt-3"
      @error="(e) => (e.target as HTMLImageElement).style.display = 'none'"
    />
  </a>
</template>
