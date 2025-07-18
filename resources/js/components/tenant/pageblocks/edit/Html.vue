<template>
  <div class="flex flex-col gap-2 p-2">
    <label class="font-semibold mb-1">HTML Block</label>
    <textarea
      v-model="html"
      class="border rounded px-2 py-1 min-h-[100px] font-mono"
      placeholder="Inserisci HTML..."
    />
    <button
      class="bg-primary text-primary-contrast px-3 py-1 rounded mt-2"
      @click="saveHtml"
      :disabled="loading"
    >
      Salva
    </button>
    <div class="mt-4 border rounded p-2 bg-white">
      <span class="text-xs text-gray-400">Anteprima:</span>
      <div v-html="html" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps<{
  id: number;
  content: string;
}>();

const html = ref(props.content);
const loading = ref(false);

const saveHtml = async () => {
  loading.value = true;
  try {
    await axios.put(route('page-blocks.update', props.id), {
      content: html.value
    });
    // Puoi aggiungere una notifica di successo qui
  } catch {
    // Puoi gestire l'errore qui
  } finally {
    loading.value = false;
  }
};
</script>
