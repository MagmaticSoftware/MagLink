<script setup lang="ts">
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

// Parse content as markdown-like or plain text
const formattedContent = computed(() => {
  if (!props.content) return '';
  // Simple formatting: support line breaks
  return props.content.replace(/\n/g, '<br>');
});
</script>

<template>
  <div class="h-full w-full flex flex-col justify-between px-6 py-4">
    <div class="flex-1 overflow-y-auto">
      <h3 v-if="title" class="text-base font-semibold text-slate-900 dark:text-slate-50 mb-2">
        {{ title }}
      </h3>
      <div 
        class="text-slate-700 dark:text-slate-300 leading-relaxed text-base prose prose-sm dark:prose-invert max-w-none"
        v-html="formattedContent"
      />
    </div>
  </div>
</template>

<style scoped>
.prose :deep(p) {
  margin-bottom: 0.75rem;
}

.prose :deep(p:last-child) {
  margin-bottom: 0;
}
</style>
