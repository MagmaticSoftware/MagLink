<script setup lang="ts">
import { LucideAlertCircle } from 'lucide-vue-next';

const props = defineProps<{
  type: string;
  title?: string;
  content: any;
}>();
</script>

<template>
  <div class="h-full w-full flex flex-col items-center justify-center p-4 text-center">
    <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg mb-3">
      <LucideAlertCircle :size="32" class="text-amber-600 dark:text-amber-400" />
    </div>
    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-50 mb-2">
      {{ title || 'Unknown Block' }}
    </h3>
    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">
      Type: <span class="font-mono text-amber-600 dark:text-amber-400">{{ type }}</span>
    </p>
    <div v-if="content" class="max-w-full overflow-hidden">
      <details class="text-xs text-left">
        <summary class="cursor-pointer text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200">
          View content
        </summary>
        <pre class="mt-2 p-2 bg-slate-100 dark:bg-slate-800 rounded text-slate-700 dark:text-slate-300 overflow-x-auto">{{ JSON.stringify(content, null, 2) }}</pre>
      </details>
    </div>
  </div>
</template>
