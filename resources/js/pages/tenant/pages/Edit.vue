<script setup lang="ts">
import axios from 'axios';
import { reactive, ref } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import LinkBlock from '@/components/tenant/pageblocks/edit/Link.vue';
import HtmlBlock from '@/components/tenant/pageblocks/edit/Html.vue';
import TextBlock from '@/components/tenant/pageblocks/edit/Text.vue';
import ImageBlock from '@/components/tenant/pageblocks/edit/Image.vue';
import DefaultBlock from '@/components/tenant/pageblocks/edit/Default.vue';
import VideoBlock from '@/components/tenant/pageblocks/edit/Video.vue';
import Dialog from '@volt/Dialog.vue';
import Button from '@volt/Button.vue';
import CreateBlock from '../pageblocks/Create.vue';
import InputText from '@volt/InputText.vue';
import Label from '@volt/Label.vue';
import { LucideSave } from 'lucide-vue-next';
import { GridLayout, GridItem } from 'grid-layout-plus';

const blockComponents: Record<string, any> = {
    link: LinkBlock,
    html: HtmlBlock,
    text: TextBlock,
    image: ImageBlock,
    video: VideoBlock,
};

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
    blocks: Array<{
        id: number;
        page_id: number;
        type: string;
        content: any;
        position: any;
        style?: any;
        settings?: any;
        is_active: boolean;
        title?: string;
        size?: any;
        created_at: string;
        updated_at: string;
    }>;
}>();

const form = useForm({
    title: props.page.title,
    description: props.page.description ?? '',
    style: props.page.style ?? {},
    settings: props.page.settings ?? {},
    is_active: props.page.is_active,
    published_at: props.page.published_at ?? '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pagine',
        href: route('pages.index'),
    },
    {
        title: 'Modifica',
        href: route('pages.edit', props.page.id),
    },
];

const submitForm = () => {
    form.put(route('pages.update', props.page.slug), {
        onSuccess: () => {
            form.reset();
        },
        onError: (errors: Record<string, any>) => {
            console.error('Form submission errors:', errors);
        },
    });
};

const layout = reactive(
    Array.isArray(props.blocks)
        ? props.blocks.map(block => {
            const x = block.position?.x ?? 0;
            const y = block.position?.y ?? 0;
            return {
                x,
                y,
                w: block.size?.width ?? 2,
                h: block.size?.height ?? 2,
                i: String(block.id),
                id: block.id,
                page_id: block.page_id,
                type: block.type,
                title: block.title,
                content: block.content,
                position: { x, y },
                size: block.size,
                style: block.style,
                settings: block.settings,
                is_active: block.is_active,
                static: false
            };
        })
        : []
);


const updateAllPositions = () => {
    layout.forEach(item => {
        item.position.x = item.x;
        item.position.y = item.y;
    });
    const positions = layout.map(item => ({
        id: item.id,
        x: item.x,
        y: item.y
    }));
    axios.post(route('blocks.positions'), { positions });
};

const updateSize = (item: any) => {
    const block = props.blocks.find((b: any) => b.id == item.i);
    if (block) {
        if (!block.size) block.size = {};
        block.size.width = item.w;
        block.size.height = item.h;
        // Chiamata API per aggiornare grandezza
        axios.post(route('blocks.size', block.id), {
            width: item.w,
            height: item.h
        });
    }
};

const visible = ref(false);

function handleBlockCreated(newBlock: any) {
    // Costruisci l'oggetto layout come per gli altri blocchi
    const position = newBlock.position ?? {};
    const size = newBlock.size ?? {};
    const item = {
        x: position.x ?? 0,
        y: position.y ?? 0,
        w: size.width ?? 2,
        h: size.height ?? 2,
        i: String(newBlock.id),
        id: newBlock.id,
        page_id: newBlock.page_id,
        type: newBlock.type,
        title: newBlock.title,
        content: newBlock.content,
        position: position,
        size: size,
        style: newBlock.style,
        settings: newBlock.settings,
        is_active: newBlock.is_active,
        static: false
    };
    layout.push(item);
    visible.value = false;
}
</script>

<template>

    <Head title="Modifica Pagina" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-row flex-wrap justify-center items-start rounded-xl p-4">
            <form class="flex w-1/4 flex-row flex-wrap gap-y-4 items-center justify-start" @submit.prevent="submitForm">
                <div class="w-full">
                    <Label label="Titolo" />
                    <InputText v-model="form.title" required />
                </div>
                <div class="w-full">
                    <Label label="Descrizione" />
                    <InputText v-model="form.description" />
                </div>
                <!-- Puoi aggiungere qui campi per style/settings se necessario -->
                <div class="w-full">
                    <Label label="Pubblicata il" />
                    <InputText v-model="form.published_at" type="date" />
                </div>
                <div class="w-full">
                    <Label label="Attiva" />
                    <input type="checkbox" v-model="form.is_active" />
                </div>
                <div class="w-full">
                    <Button type="submit">
                        <LucideSave class="mr-2" />
                        Aggiorna Pagina
                    </Button>
                    <Button type="button" class="ml-2" @click="visible = true">
                        Aggiungi Blocco
                    </Button>
                </div>
            </form>
            <GridLayout v-model:layout="layout" :col-num="2" :row-height="100" is-draggable is-resizable
                vertical-compact use-css-transforms class="w-3/4 h-full" :margin="[30,30]">
                <template v-if="layout.length > 0">
                    <GridItem v-for="item in layout" :key="item.i" :x="item.x" :y="item.y" :w="item.w" :h="item.h"
                        :i="item.i" @resized="updateSize(item)"  @moved="updateAllPositions"
                        class="bg-gray-50 p-3 overflow-hidden dark:bg-gray-800 shadow-md rounded-lg flex items-start justify-center text-gray-700 dark:text-gray-300">
                        <component :is="blockComponents[item.type] ?? DefaultBlock" :id="item.id"
                            :page-id="item.page_id" :type="item.type" :title="item.title" :content="item.content"
                            :position="item.position" :size="item.size.width" :style="item.style"
                            :settings="item.settings" :is-active="item.is_active" />
                    </GridItem>
                </template>
                <template v-else>
                    <GridItem :key="1" :x="0" :y="0" :w="1" :h="1" :i="1">
                        <Button type="button" class="ml-2" @click="visible = true">
                            Aggiungi Blocco
                        </Button>
                    </GridItem>
                </template>
            </GridLayout>
            <div>
                <div v-for="item in layout" :key="item.i">
                    title: {{ item.title }}, x: {{ item.x }}, y: {{ item.y }}, position: {{ item.position }}
                </div>
            </div>
        </div>
        <Dialog v-model:visible="visible" modal header="Aggiungi blocco" class="sm:w-100 w-9/10">
            <CreateBlock :page="props.page" @created="handleBlockCreated" />
        </Dialog>
    </AppLayout>
</template>

<style scoped>
.vgl-layout {
  background-color: #fff;
}

.vgl-layout::before {
  position: absolute;
  width: calc(100% - 5px);
  height: calc(100% - 5px);
  margin: 15px;
  content: '';
  background-image: linear-gradient(to right, lightgrey 1px, transparent 1px),
    linear-gradient(to bottom, lightgrey 1px, transparent 1px);
  background-repeat: repeat;
  background-size: calc(calc(100% - 30px) / 2) 130px;
}

:deep(.vgl-item__resizer) {
    right: 0.5em;
    bottom: 0.5em;
}
</style>
