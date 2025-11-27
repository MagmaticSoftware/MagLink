<script setup lang="ts">
import { LucideExternalLink, LucideLink2 } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
  id: number;
  title?: string;
  content: string;
  position?: {
    x: number;
    y: number;
  };
}>();

// Extract URL from content (assuming content is the URL or JSON with url field)
const linkUrl = computed(() => {
  try {
    const parsed = JSON.parse(props.content);
    return parsed.url || props.content;
  } catch {
    return props.content;
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
</script>

<template>
  <a 
    :href="linkUrl" 
    target="_blank" 
    rel="noopener noreferrer"
    class="h-full w-full flex flex-col justify-between group cursor-pointer p-0 hover:scale-[1.02] transition-transform duration-300"
  >
    <div class="flex-1 flex flex-col justify-center">
      <div class="flex items-center gap-3 mb-2">
        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
          <LucideLink2 :size="20" class="text-blue-600 dark:text-blue-400" />
        </div>
        <div class="flex-1">
          <h3 v-if="title" class="text-base font-semibold text-slate-900 dark:text-slate-50 mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
            {{ title }}
          </h3>
          <p class="text-sm text-slate-500 dark:text-slate-400 font-mono truncate">
            {{ displayUrl }}
          </p>
        </div>
        <LucideExternalLink 
          :size="16" 
          class="text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors flex-shrink-0" 
        />
      </div>
    </div>
  </a>
</template>
