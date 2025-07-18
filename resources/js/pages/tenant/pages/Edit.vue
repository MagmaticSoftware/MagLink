<script setup lang="ts">
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import LinkBlock from '@/components/tenant/pageblocks/edit/Link.vue';
import HtmlBlock from '@/components/tenant/pageblocks/edit/Html.vue';
import TextBlock from '@/components/tenant/pageblocks/edit/Text.vue';
import ImageBlock from '@/components/tenant/pageblocks/edit/Image.vue';
import DefaultBlock from '@/components/tenant/pageblocks/edit/Default.vue';

const blockComponents: Record<string, any> = {
  link: LinkBlock,
  html: HtmlBlock,
  text: TextBlock,
  image: ImageBlock,
};
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@volt/Button.vue';
import InputText from '@volt/InputText.vue';
import Label from '@volt/Label.vue';
import { LucideSave } from 'lucide-vue-next';
import { GridLayout, GridItem } from 'grid-layout-plus';
import { reactive } from 'vue';

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
    form.put(route('pages.update', props.page.id), {
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
        ? props.blocks.map(block => ({
            x: block.position?.x ?? 0,
            y: block.position?.y ?? 0,
            w: block.size?.width ?? 2,
            h: block.size?.height ?? 2,
            i: String(block.id),
            id: block.id,
            page_id: block.page_id,
            type: block.type,
            title: block.title,
            content: block.content,
            position: block.position,
            size: block.size,
            style: block.style,
            settings: block.settings,
            is_active: block.is_active,
            static: false
        }))
        : []
);

const updatePosition = (item: any) => {
    const block = props.blocks.find((b: any) => b.id == item.i);
    if (block) {
        block.position.x = item.x;
        block.position.y = item.y;
        // Chiamata API per aggiornare posizione
        axios.post(`/blocks/${block.id}/position`, {
            x: item.x,
            y: item.y
        });
    }
};

const updateSize = (item: any) => {
    const block = props.blocks.find((b: any) => b.id == item.i);
    if (block) {
        block.size.width = item.w;
        block.size.height = item.h;
        // Chiamata API per aggiornare grandezza
        axios.post(`/blocks/${block.id}/size`, {
            width: item.w,
            height: item.h
        });
    }
};
</script>

<template>
    <Head title="Modifica Pagina" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <form class="flex flex-row flex-wrap gap-y-4 items-center justify-start w-full" @submit.prevent="submitForm">
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
                </div>
            </form>
            <GridLayout
                v-model:layout="layout"
                :col-num="1"
                :row-height="100"
                :max-rows="20"
                is-draggable
                is-resizable
                vertical-compact
                use-css-transforms
            >
                <GridItem
                v-for="item in layout"
                :key="item.i"
                :x="item.x"
                :y="item.y"
                :w="item.w"
                :h="item.h"
                :i="item.i"
                @moved="updatePosition(item)"
                @resized="updateSize(item)"
                class="bg-gray-200 dark:bg-gray-800 rounded-lg flex items-center justify-center text-gray-700 dark:text-gray-300"
                >
                <component
                  :is="blockComponents[item.type] ?? DefaultBlock"
                  :id="item.id"
                  :page-id="item.page_id"
                  :type="item.type"
                  :title="item.title"
                  :content="item.content"
                  :position="item.position"
                  :size="item.size"
                  :style="item.style"
                  :settings="item.settings"
                  :is-active="item.is_active"
                />
                </GridItem>
            </GridLayout>
        </div>
    </AppLayout>
</template>

<style scoped>
.vgl-layout {
  background-color: #333;
}

.vgl-layout::before {
  position: absolute;
  width: calc(100% - 5px);
  height: calc(100% - 5px);
  margin: 5px;
  content: '';
  background-image: linear-gradient(to right, rgb(41, 16, 16) 1px, transparent 1px),
    linear-gradient(to bottom, rgb(41, 16, 16) 1px, transparent 1px);
  background-repeat: repeat;
  background-size: calc(calc(100% - 5px) / 12) 40px;
}
</style>
