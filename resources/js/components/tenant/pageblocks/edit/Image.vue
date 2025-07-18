<template>
  <div class="p-2">
    <label class="font-semibold mb-1">Image Block</label>
    <img :src="content" alt="Immagine" class="max-w-full rounded border" />
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{ content: string }>();
</script>
