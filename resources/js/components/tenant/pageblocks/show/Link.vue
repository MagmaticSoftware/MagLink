<script setup lang="ts">
import { Link } from 'lucide-vue-next';

defineProps<{
  id: number;
  title: string;
  content: string;
    position: {
        x: number;
        y: number;
    };
}>();
</script>

<template>
  <div class="flex flex-row flex-wrap items-center justify-start gap-2 w-full">
    <div class="p-4">
      <Link class="text-gray-400 dark:text-gray-400" :size="30"></Link>
    </div>
    <div class="flex-1 flex flex-col gap-2">
        <div class="font-semibold text-gray-700 dark:text-gray-400 w-full">{{ title }} | position: {{ position.x }} - {{ position.y }}</div>
        <div class="w-full">{{ content }}</div>
    </div>
  </div>
</template>
