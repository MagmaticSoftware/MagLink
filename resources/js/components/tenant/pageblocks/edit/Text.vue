<template>
  <div class="p-2">
    <label class="font-semibold mb-1">Text Block</label>
    <div class="border rounded p-2 bg-white">{{ content }}</div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{ content: string }>();
</script>
