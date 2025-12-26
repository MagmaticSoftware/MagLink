<script setup lang="ts">
import { computed, ref } from 'vue';
import { LucideImage } from 'lucide-vue-next';

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

const imageError = ref(false);

// Extract image URL from content
const imageUrl = computed(() => {
  try {
    const parsed = JSON.parse(props.content);
    return parsed.url || parsed.src || props.content;
  } catch {
    return props.content;
  }
});

const linkUrl = computed(() => {
  try {
    if (typeof props.settings === 'string') {
      const parsed = JSON.parse(props.settings);
      return parsed.link || '';
    }
    return props.settings?.link || '';
  } catch {
    return '';
  }
});

const handleImageError = () => {
  imageError.value = true;
};
</script>

<template>
  <div class="h-full w-full flex flex-col overflow-hidden">
    <div v-if="title" class="mb-2 px-4 pt-4">
      <h3 class="text-base font-semibold text-slate-900 dark:text-slate-50">
        {{ title }}
      </h3>
    </div>
    
    <div class="flex-1 relative overflow-hidden bg-slate-100 dark:bg-slate-800">
      <component 
        :is="linkUrl ? 'a' : 'div'"
        :href="linkUrl || undefined"
        :target="linkUrl ? '_blank' : undefined"
        :rel="linkUrl ? 'noopener noreferrer' : undefined"
        class="block w-full h-full"
        :class="linkUrl ? 'cursor-pointer hover:opacity-90 transition-opacity' : ''"
      >
        <img 
          v-if="!imageError && imageUrl"
          :src="imageUrl" 
          :alt="title || 'Image'"
          @error="handleImageError"
          class="w-full h-full object-cover"
        />
      
      <!-- Fallback -->
      <div 
        v-else
        class="w-full h-full flex items-center justify-center"
      >
        <div class="text-center">
          <LucideImage :size="48" class="mx-auto text-slate-300 dark:text-slate-600 mb-2" />
          <p class="text-sm text-slate-500 dark:text-slate-400">Image preview</p>
        </div>
      </div>      </component>    </div>
  </div>
</template>