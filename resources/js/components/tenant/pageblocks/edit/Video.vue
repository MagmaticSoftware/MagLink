<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import { useConfirm } from 'primevue/useconfirm';
import BlockCardHeader from './BlockCardHeader.vue';
import { LucidePlay, LucideYoutube } from 'lucide-vue-next';
import InputText from '@/components/volt/InputText.vue';

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
const isSaving = ref(false);
const savedTitle = ref(props.title || '');
const savedContent = ref(extractUrl(props.content));

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
  localContent.value = extractUrl(newVal); 
  savedContent.value = extractUrl(newVal);
});

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
      label="Video"
      :icon="isYouTube ? LucideYoutube : LucidePlay"
      tone="red"
      :has-changes="hasChanges"
      :is-saving="isSaving"
      @save="saveBlock"
      @delete="deleteBlock"
    />

    <!-- Editable content -->
    <div class="flex-1 flex flex-col gap-3 overflow-y-auto p-3">
      <div>
        <label class="ml-label mb-1 text-xs">
          Titolo video (opzionale)
        </label>
        <InputText 
          v-model="localTitle" 
          placeholder="Titolo del video..."
          @blur="saveBlock"
          class="w-full text-sm"
        />
      </div>
      
      <div>
        <label class="ml-label mb-1 text-xs">
          URL del video
        </label>
        <InputText 
          v-model="localContent" 
          placeholder="https://youtube.com/watch?v=..."
          @blur="saveBlock"
          type="url"
          class="w-full text-sm font-mono"
        />
        <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">
          Supporta link YouTube e Vimeo
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
