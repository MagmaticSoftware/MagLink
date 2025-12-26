<script setup lang="ts">
defineProps<{
  id: number;
  title?: string;
  content: string;
  position?: {
    x: number;
    y: number;
  };
}>();
</script>

<template>
  <div class="h-full w-full flex flex-col p-0 overflow-hidden">
    <div v-if="title" class="mb-3">
      <h3 class="text-base font-semibold text-slate-900 dark:text-slate-50">
        {{ title }}
      </h3>
    </div>
    
    <div class="flex-1 overflow-y-auto prose prose-sm dark:prose-invert max-w-none">
      <div v-html="content" class="html-content" />
    </div>
  </div>
</template>

<style scoped>
.html-content :deep(*) {
  max-width: 100%;
}

.html-content :deep(img) {
  border-radius: 0.5rem;
  max-height: 300px;
  object-fit: cover;
}

.html-content :deep(a) {
  color: rgb(59, 130, 246);
  text-decoration: underline;
}

.html-content :deep(a:hover) {
  color: rgb(37, 99, 235);
}
</style>
