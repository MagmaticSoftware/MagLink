<script setup lang="ts">
import { LucideAlertCircle, LucideTrash2 } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps<{
  id: number;
  type: string;
  title?: string;
  content: any;
}>();

const deleteBlock = async () => {
  if (!confirm('Sei sicuro di voler eliminare questo blocco?')) return;
  try {
    await axios.delete(route('page-blocks.destroy', props.id));
    window.location.reload();
  } catch (error) {
    console.error('Failed to delete block:', error);
  }
};
</script>

<template>
  <div class="h-full w-full flex flex-col p-0">
    <!-- Header with actions -->
    <div class="flex items-center justify-between mb-3 pb-2 border-b border-amber-200 dark:border-amber-700">
      <div class="flex items-center gap-2">
        <div class="p-1.5 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
          <LucideAlertCircle :size="16" class="text-amber-600 dark:text-amber-400" />
        </div>
        <span class="text-xs font-medium text-amber-600 dark:text-amber-400">Unknown Block</span>
      </div>
      <button
        @click="deleteBlock"
        class="p-1.5 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
        title="Delete block"
      >
        <LucideTrash2 :size="14" class="text-red-600 dark:text-red-400" />
      </button>
    </div>

    <!-- Content -->
    <div class="flex-1 flex flex-col items-center justify-center text-center overflow-y-auto">
      <h3 class="text-base font-semibold text-slate-900 dark:text-slate-50 mb-2">
        {{ title || 'Unknown Block Type' }}
      </h3>
      <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">
        Type: <span class="font-mono text-amber-600 dark:text-amber-400">{{ type }}</span>
      </p>
      <div v-if="content" class="max-w-full overflow-hidden mt-3">
        <details class="text-xs text-left">
          <summary class="cursor-pointer text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200">
            View raw content
          </summary>
          <pre class="mt-2 p-2 bg-slate-100 dark:bg-slate-800 rounded text-slate-700 dark:text-slate-300 overflow-x-auto text-xs">{{ JSON.stringify(content, null, 2) }}</pre>
        </details>
      </div>
    </div>
  </div>
</template>
