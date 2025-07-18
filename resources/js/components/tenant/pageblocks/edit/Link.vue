<template>
  <div class="flex flex-col gap-2 p-2">
    <label class="font-semibold mb-1">Link</label>
    <input
      type="text"
      v-model="link"
      class="border rounded px-2 py-1"
      placeholder="Inserisci il link..."
    />
    <button
      class="bg-primary text-primary-contrast px-3 py-1 rounded mt-2"
      @click="saveLink"
      :disabled="loading"
    >
      Salva
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps<{
  id: number;
  content: string;
}>();

const link = ref(props.content);
const loading = ref(false);

const saveLink = async () => {
  loading.value = true;
  try {
    await axios.put(`/blocks/${props.id}`, {
      content: link.value
    });
    // Puoi aggiungere una notifica di successo qui
  } catch (e) {
    // Puoi gestire l'errore qui
  } finally {
    loading.value = false;
  }
};
</script>
