<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import { LucideSave, LucidePlay, LucideTrash2, LucideYoutube } from 'lucide-vue-next';
import InputText from '@/components/volt/InputText.vue';

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

const localTitle = ref(props.title || '');
const localContent = ref(props.content || '');
const isSaving = ref(false);

watch(() => props.title, (newVal) => { localTitle.value = newVal || ''; });
watch(() => props.content, (newVal) => { localContent.value = newVal || ''; });

const videoUrl = computed(() => {
  try {
    const parsed = JSON.parse(localContent.value);
    return parsed.url || localContent.value;
  } catch {
    return localContent.value;
  }
});

const embedUrl = computed(() => {
  const url = videoUrl.value;
  
  if (url.includes('youtube.com') || url.includes('youtu.be')) {
    let videoId = '';
    if (url.includes('youtu.be/')) {
      videoId = url.split('youtu.be/')[1].split('?')[0];
    } else if (url.includes('watch?v=')) {
      videoId = url.split('watch?v=')[1].split('&')[0];
    }
    return `https://www.youtube.com/embed/${videoId}`;
  }
  
  if (url.includes('vimeo.com')) {
    const videoId = url.split('vimeo.com/')[1].split('?')[0];
    return `https://player.vimeo.com/video/${videoId}`;
  }
  
  return url;
});

const isYouTube = computed(() => videoUrl.value.includes('youtube') || videoUrl.value.includes('youtu.be'));

const saveBlock = async () => {
  isSaving.value = true;
  try {
    await axios.put(route('page-blocks.update', props.id), {
      title: localTitle.value,
      content: localContent.value,
    });
  } catch (error) {
    console.error('Failed to save block:', error);
  } finally {
    isSaving.value = false;
  }
};

const deleteBlock = async () => {
  if (!confirm('Sei sicuro di voler eliminare questo blocco?')) return;
  try {
    await axios.delete(route('page-blocks.destroy', props.id));
    window.location.reload();
  } catch (error) {
    console.error('Failed to delete block:', error);
  }
};
</script>

<template>
  <div class="h-full w-full flex flex-col p-0">
    <!-- Header with actions -->
    <div class="flex items-center justify-between mb-3 pb-2 border-b border-surface-200 dark:border-surface-700">
      <div class="flex items-center gap-2">
        <div class="p-1.5 bg-red-100 dark:bg-red-900/30 rounded-lg">
          <LucideYoutube v-if="isYouTube" :size="16" class="text-red-600 dark:text-red-400" />
          <LucidePlay v-else :size="16" class="text-red-600 dark:text-red-400" />
        </div>
        <span class="text-xs font-medium text-surface-600 dark:text-surface-400">Video Block</span>
      </div>
      <div class="flex items-center gap-1">
        <button
          @click="saveBlock"
          :disabled="isSaving"
          class="p-1.5 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors disabled:opacity-50"
          title="Save changes"
        >
          <LucideSave :size="14" class="text-green-600 dark:text-green-400" />
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
          Video Title (optional)
        </label>
        <InputText 
          v-model="localTitle" 
          placeholder="Video description..."
          @blur="saveBlock"
          class="w-full text-sm"
        />
      </div>
      
      <div>
        <label class="text-xs font-medium text-surface-700 dark:text-surface-300 block mb-1">
          Video URL
        </label>
        <InputText 
          v-model="localContent" 
          placeholder="https://youtube.com/watch?v=..."
          @blur="saveBlock"
          type="url"
          class="w-full text-sm font-mono"
        />
        <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">
          Supports YouTube and Vimeo URLs
        </p>
      </div>

      <!-- Video Preview -->
      <div v-if="embedUrl && videoUrl" class="flex-1 min-h-[150px] relative rounded-lg overflow-hidden bg-black">
        <iframe 
          :src="embedUrl"
          class="w-full h-full"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen
        />
      </div>
    </div>
  </div>
</template>
