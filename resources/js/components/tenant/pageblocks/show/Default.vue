<script setup lang="ts">
defineProps<{
  type: string;
  content: any;
}>();
</script>

<template>
  <div class="flex flex-col gap-2 p-4 rounded-xl border border-yellow-400 dark:border-yellow-600 bg-yellow-50 dark:bg-yellow-900 shadow">
    <div class="font-semibold text-yellow-700 dark:text-yellow-300 mb-1">Blocco non gestito: {{ type }}</div>
    <div v-if="content" class="mt-2">
      <pre class="whitespace-pre-wrap text-xs">{{ content }}</pre>
    </div>
  </div>
</template>
