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
  <div class="h-full w-full flex items-end justify-start pb-2">
    <h2 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-slate-50 text-left">
      {{ content || title || 'Titolo' }}
    </h2>
  </div>
</template>
