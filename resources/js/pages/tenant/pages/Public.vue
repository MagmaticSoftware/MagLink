<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import LinkBlock from '@/components/tenant/pageblocks/show/Link.vue';
import HtmlBlock from '@/components/tenant/pageblocks/show/Html.vue';
import TextBlock from '@/components/tenant/pageblocks/show/Text.vue';
import ImageBlock from '@/components/tenant/pageblocks/show/Image.vue';
import VideoBlock from '@/components/tenant/pageblocks/show/Video.vue';
import DefaultBlock from '@/components/tenant/pageblocks/show/Default.vue';

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
        blocks: Array<{
            id: number;
            page_id: number;
            type: string;
            content: any;
            position: { x: number; y: number };
            style?: any;
            settings?: any;
            is_active: boolean;
            title?: string;
            size?: any;
        }>;
    };
}>();

const maxRows = Math.max(
    ...props.page.blocks.map(b => (b.position?.y ?? 0) + (b.size?.height ?? 1))
);
</script>

<template>
    <Head title="MagLink - Professional URL Shortener">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800">
        <div class="container h-screen overflow-hidden mx-auto py-16 flex flex-row items-stretch justify-center">
            <div class="w-2/5 p-8">
                <h1 class="text-4xl font-bold mb-4">{{ props.page.title }}</h1>
                <p>{{ props.page.description }}</p>                
            </div>
            <div class="mt-8 overflow-y-auto max-h-full w-3/5">
                <div
                    :class="`grid grid-cols-2 gap-4 relative grid-rows-${maxRows}`"
                >
                    <div v-for="block in props.page.blocks" :key="block.id"
                        :class="[
                            'relative',
                            `col-start-${(block.position?.x ?? 0) + 1}`,
                            `row-start-${(block.position?.y ?? 0) + 1}`,
                            `col-span-${block.size?.width ?? 1}`,
                            `row-span-${block.size?.height ?? 1}`
                        ]">
                        <div class="h-[150px] w-full bg-white/80 dark:bg-gray-900/80 border border-gray-200 dark:border-gray-700 rounded-xl flex items-center justify-center shadow">
                            <component
                                :is="blockComponents[block.type] ?? DefaultBlock"
                                v-bind="block"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
