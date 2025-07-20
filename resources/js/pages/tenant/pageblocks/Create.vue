<script lang="ts" setup>
import { ref } from 'vue';
import axios from 'axios';

const emit = defineEmits(['created']);

const props = defineProps<{
    page: {
        id: number;
        user_id: number;
        company_id: number;
        tenant_id: string;
        slug: string;
        title: string;
        description: string | null;
        style: any;
        settings: any;
        is_active: boolean;
        views: number;
        last_viewed_at: string | null;
        published_at: string | null;
    };
}>();

const type = ref('text');
const title = ref('');
const content = ref('');
const loading = ref(false);

const createBlock = async () => {
    loading.value = true;
    try {
        const response = await axios.post(route('page-blocks.store'), {
            page_id: props.page.id,
            type: type.value,
            title: title.value,
            content: content.value,
        });
        emit('created', response.data);
        // Reset campi
        type.value = 'text';
        title.value = '';
        content.value = '';
    } catch {
        // Gestisci errore
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <form class="flex flex-col gap-2 p-2" @submit.prevent="createBlock">
        <label class="font-semibold">Tipo blocco</label>
        <select v-model="type" class="border rounded px-2 py-1">
            <option value="text">Testo</option>
            <option value="html">HTML</option>
            <option value="image">Immagine</option>
            <option value="link">Link</option>
        </select>
        <label class="font-semibold">Titolo</label>
        <input v-model="title" type="text" class="border rounded px-2 py-1" placeholder="Titolo" />
        <label class="font-semibold">Contenuto</label>
        <input v-model="content" type="text" class="border rounded px-2 py-1" placeholder="Contenuto" />
        <button type="submit" class="bg-primary text-primary-contrast px-3 py-1 rounded mt-2" :disabled="loading">
            Crea blocco
        </button>
    </form>
</template>