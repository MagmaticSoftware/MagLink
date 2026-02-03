<script setup lang="ts">
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
const buttonUrl = computed(() => {
  try {
    const parsed = JSON.parse(props.content);
    return parsed.url || props.content;
  } catch {
    return props.content;
  }
});

// Extract colors from settings
const backgroundColor = computed(() => {
  try {
    if (typeof props.settings === 'string') {
      const parsed = JSON.parse(props.settings);
      return parsed.backgroundColor || '#3b82f6'; // Default blue
    }
    return props.settings?.backgroundColor || '#3b82f6';
  } catch {
    return '#3b82f6';
  }
});

const textColor = computed(() => {
  try {
    if (typeof props.settings === 'string') {
      const parsed = JSON.parse(props.settings);
      return parsed.textColor || '#ffffff'; // Default white
    }
    return props.settings?.textColor || '#ffffff';
  } catch {
    return '#ffffff';
  }
});

const displayTitle = computed(() => {
  return props.title || 'Button';
});
</script>

<template>
  <a 
    :href="buttonUrl"
    target="_blank"
    rel="noopener noreferrer"
    class="h-full w-full flex items-center justify-center rounded-lg overflow-hidden transition-all hover:scale-[1.02] active:scale-[0.98] shadow-sm hover:shadow-md"
    :style="{ 
      backgroundColor: backgroundColor,
      color: textColor 
    }"
  >
    <span class="text-base font-semibold px-4 py-3 text-center">
      {{ displayTitle }}
    </span>
  </a>
</template>
