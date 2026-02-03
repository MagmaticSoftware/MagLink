<script setup lang="ts">
import { reactive, computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import LinkBlock from '@/components/tenant/pageblocks/show/Link.vue';
import HtmlBlock from '@/components/tenant/pageblocks/show/Html.vue';
import TextBlock from '@/components/tenant/pageblocks/show/Text.vue';
import ImageBlock from '@/components/tenant/pageblocks/show/Image.vue';
import DefaultBlock from '@/components/tenant/pageblocks/show/Default.vue';
import VideoBlock from '@/components/tenant/pageblocks/show/Video.vue';
import TitleBlock from '@/components/tenant/pageblocks/show/Title.vue';
import SeparatorBlock from '@/components/tenant/pageblocks/show/Separator.vue';
import MapBlock from '@/components/tenant/pageblocks/show/Map.vue';
import ButtonBlock from '@/components/tenant/pageblocks/show/Button.vue';
import SocialBlock from '@/components/tenant/pageblocks/show/Social.vue';
import { GridLayout, GridItem } from 'grid-layout-plus';
import { 
    LucideEye, 
    LucideEdit, 
    LucideCalendar,
    LucideLayout,
    LucideExternalLink,
    LucideArrowLeft
} from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const blockComponents: Record<string, any> = {
    link: LinkBlock,
    html: HtmlBlock,
    text: TextBlock,
    image: ImageBlock,
    video: VideoBlock,
    title: TitleBlock,
    separator: SeparatorBlock,
    map: MapBlock,
    button: ButtonBlock,
    social: SocialBlock,
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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('pages.title'),
        href: route('pages.index'),
    },
    {
        title: props.page.title,
        href: route('pages.show', props.page.slug),
    },
];

// Prepare layout for grid
const layout = reactive(
    Array.isArray(props.blocks)
        ? props.blocks
            .filter(block => block.is_active)
            .map((block, index) => {
                const x = block.position?.x ?? (index % 2);
                const y = block.position?.y ?? Math.floor(index / 2);
                return {
                    x,
                    y,
                    w: block.size?.width ?? 1,
                    h: block.size?.height ?? 2,
                    i: String(block.id),
                    id: block.id,
                    page_id: block.page_id,
                    type: block.type,
                    title: block.title,
                    content: block.content,
                    position: { x, y },
                    size: block.size ?? { width: 1, height: 2 },
                    style: block.style,
                    settings: block.settings,
                    is_active: block.is_active,
                    static: true
                };
            })
        : []
);

// Format date
const formatDate = (date: string | null) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString(undefined, { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
};

// Page stats
const pageStats = computed(() => ({
    totalBlocks: layout.length,
    views: props.page.views || 0,
    publishedDate: formatDate(props.page.published_at),
}));
</script>

<template>
    <Head :title="props.page.title">
        <link rel="icon" href="/favicon.ico" />
    </Head>

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Page Header -->
            <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <Link 
                                :href="route('pages.index')"
                                class="text-surface-400 hover:text-surface-600 dark:hover:text-surface-300 transition-colors"
                            >
                                <LucideArrowLeft :size="20" />
                            </Link>
                            <h1 class="text-3xl font-bold text-surface-900 dark:text-surface-50">
                                {{ props.page.title }}
                            </h1>
                            <span 
                                v-if="props.page.is_active" 
                                class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400"
                            >
                                {{ t('pages.published') }}
                            </span>
                            <span 
                                v-else 
                                class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400"
                            >
                                {{ t('pages.draft') }}
                            </span>
                        </div>
                        <p v-if="props.page.description" class="text-surface-600 dark:text-surface-400 mt-2 whitespace-pre-line">
                            {{ props.page.description }}
                        </p>
                        
                        <!-- Page Info -->
                        <div class="flex flex-wrap items-center gap-4 mt-4 text-sm text-surface-500 dark:text-surface-400">
                            <div class="flex items-center gap-2">
                                <LucideLayout :size="16" />
                                <span>{{ pageStats.totalBlocks }} {{ t('pages.blocks') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <LucideEye :size="16" />
                                <span>{{ pageStats.views }} {{ t('pages.stats.views') }}</span>
                            </div>
                            <div v-if="props.page.published_at" class="flex items-center gap-2">
                                <LucideCalendar :size="16" />
                                <span>{{ pageStats.publishedDate }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        <Link
                            :href="route('pages.edit', props.page.slug)"
                            class="button-secondary flex items-center gap-2"
                        >
                            <LucideEdit :size="16" />
                            {{ t('common.edit') }}
                        </Link>
                        <a
                            :href="route('pages.show.public', props.page.slug)"
                            target="_blank"
                            class="button-primary flex items-center gap-2"
                        >
                            <LucideExternalLink :size="16" />
                            {{ t('pages.viewPage') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Page Preview -->
            <div class="bg-white dark:bg-surface-900 rounded-xl shadow-sm border border-surface-200 dark:border-surface-800 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-950/30 dark:to-purple-950/30 px-6 py-4 border-b border-surface-200 dark:border-surface-800">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-50">
                            {{ t('pages.preview') }}
                        </h2>
                        <span class="text-sm text-surface-600 dark:text-surface-400">
                            {{ t('pages.publicView') }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <div v-if="layout.length > 0" class="min-h-[600px]">
                        <GridLayout 
                            v-model:layout="layout" 
                            :col-num="4" 
                            :row-height="120" 
                            :is-draggable="false"
                            :is-resizable="false"
                            :vertical-compact="false"
                            :prevent-collision="true"
                            :use-css-transforms="true"
                            :margin="[16, 16]"
                            class="w-full"
                        >
                            <GridItem 
                                v-for="item in layout" 
                                :key="item.i" 
                                :x="item.x" 
                                :y="item.y" 
                                :w="item.w" 
                                :h="item.h"
                                :i="item.i"
                                :static="true"
                                :class="[
                                    'overflow-auto transition-all duration-200',
                                    item.type === 'title' || item.type === 'separator'
                                        ? ''
                                        : 'bg-gradient-to-br from-surface-50 to-surface-100 dark:from-surface-800 dark:to-surface-900 p-6 shadow-md rounded-xl border border-surface-200 dark:border-surface-700'
                                ]"
                            >
                                <component 
                                    :is="blockComponents[item.type] ?? DefaultBlock" 
                                    :id="item.id"
                                    :title="item.title" 
                                    :content="item.content"
                                    :settings="item.settings"
                                    :position="item.position"
                                />
                            </GridItem>
                        </GridLayout>
                    </div>

                    <!-- Empty State -->
                    <div 
                        v-else 
                        class="flex flex-col items-center justify-center py-20 text-center"
                    >
                        <LucideLayout :size="64" class="text-surface-300 dark:text-surface-600 mb-4" />
                        <h3 class="text-xl font-semibold text-surface-900 dark:text-surface-50 mb-2">
                            {{ t('pages.noBlocks') }}
                        </h3>
                        <p class="text-surface-600 dark:text-surface-400 mb-6">
                            {{ t('pages.addBlocksToPage') }}
                        </p>
                        <Link
                            :href="route('pages.edit', props.page.slug)"
                            class="button-primary flex items-center gap-2"
                        >
                            <LucideEdit :size="16" />
                            {{ t('pages.editPage') }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.vgl-layout {
  background-color: transparent;
  position: relative;
  min-height: 600px;
}

/* Grid background pattern */
.vgl-layout::before {
  position: absolute;
  inset: 0;
  content: '';
  background-image: 
    linear-gradient(to right, rgba(148, 163, 184, 0.05) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(148, 163, 184, 0.05) 1px, transparent 1px);
  background-repeat: repeat;
  background-size: calc(50% - 8px) 136px;
  pointer-events: none;
  z-index: 0;
}

:root.dark .vgl-layout::before {
  background-image: 
    linear-gradient(to right, rgba(71, 85, 105, 0.1) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(71, 85, 105, 0.1) 1px, transparent 1px);
}

/* Static items (no interaction) */
:deep(.vgl-item) {
  position: absolute;
  z-index: 1;
  cursor: default;
}

/* No resize handle in view mode */
:deep(.vgl-item__resizer) {
  display: none;
}
</style>
