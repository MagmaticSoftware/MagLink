<template>
  <div class="p-2 text-gray-500 text-sm">
    <span>Blocco non gestito: {{ type }}</span>
    <div v-if="content" class="mt-2">
      <pre>{{ content }}</pre>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  type: string;
  content: any;
}>();
</script>
