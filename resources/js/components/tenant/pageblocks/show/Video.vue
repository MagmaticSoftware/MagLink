<script setup lang="ts">
import { computed } from 'vue';
import { LucidePlay, LucideYoutube } from 'lucide-vue-next';

const props = defineProps<{
  id: number;
  title?: string;
  content: string;
  position?: {
    x: number;
    y: number;
  };
}>();

// Extract video URL and convert to embed
const videoData = computed(() => {
  try {
    const parsed = JSON.parse(props.content);
    return {
      url: parsed.url || props.content,
      type: parsed.type || 'url'
    };
  } catch {
    return { url: props.content, type: 'url' };
  }
});

const embedUrl = computed(() => {
  const url = videoData.value.url;
  
  // YouTube
  if (url.includes('youtube.com') || url.includes('youtu.be')) {
    let videoId = '';
    if (url.includes('youtu.be/')) {
      videoId = url.split('youtu.be/')[1].split('?')[0];
    } else if (url.includes('watch?v=')) {
      videoId = url.split('watch?v=')[1].split('&')[0];
    }
    return `https://www.youtube.com/embed/${videoId}`;
  }
  
  // Vimeo
  if (url.includes('vimeo.com')) {
    const videoId = url.split('vimeo.com/')[1].split('?')[0];
    return `https://player.vimeo.com/video/${videoId}`;
  }
  
  return url;
});

const isYouTube = computed(() => videoData.value.url.includes('youtube') || videoData.value.url.includes('youtu.be'));
</script>

<template>
  <div class="h-full w-full flex flex-col p-0 overflow-hidden">
    <div v-if="title" class="mb-2">
      <h3 class="text-base font-semibold text-slate-900 dark:text-slate-50 flex items-center gap-2">
        <LucideYoutube v-if="isYouTube" :size="20" class="text-red-500" />
        <LucidePlay v-else :size="20" class="text-blue-500" />
        {{ title }}
      </h3>
    </div>
    
    <div class="flex-1 relative rounded-lg overflow-hidden bg-black">
      <iframe 
        :src="embedUrl"
        class="w-full h-full"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen
      />
    </div>
  </div>
</template>
