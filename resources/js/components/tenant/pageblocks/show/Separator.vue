<script setup lang="ts">
defineProps<{
  id: number;
  title?: string;
  content?: string;
  position?: {
    x: number;
    y: number;
  };
}>();
</script>

<template>
  <div class="h-full w-full flex items-center justify-center">
    <div class="w-full border-t-2 border-slate-300 dark:border-slate-700"></div>
  </div>
</template>
