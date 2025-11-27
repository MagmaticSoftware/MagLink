<script setup lang="ts">
import { reactive } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useDark, useToggle } from '@vueuse/core';
import { LucideSun, LucideMoon } from 'lucide-vue-next';
import LinkBlock from '@/components/tenant/pageblocks/show/Link.vue';
import HtmlBlock from '@/components/tenant/pageblocks/show/Html.vue';
import TextBlock from '@/components/tenant/pageblocks/show/Text.vue';
import ImageBlock from '@/components/tenant/pageblocks/show/Image.vue';
import VideoBlock from '@/components/tenant/pageblocks/show/Video.vue';
import DefaultBlock from '@/components/tenant/pageblocks/show/Default.vue';
import { GridLayout, GridItem } from 'grid-layout-plus';

const isDark = useDark();
const toggleDark = useToggle(isDark);

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

// Get theme color from page style
const themeColor = props.page.style?.primaryColor || 'blue';

// Generate gradient classes based on theme color
const getGradientClasses = (color: string) => {
    const gradients: Record<string, { light: string; dark: string }> = {
        blue: { light: 'from-blue-600 to-indigo-600', dark: 'from-blue-400 to-indigo-400' },
        indigo: { light: 'from-indigo-600 to-purple-600', dark: 'from-indigo-400 to-purple-400' },
        purple: { light: 'from-purple-600 to-pink-600', dark: 'from-purple-400 to-pink-400' },
        pink: { light: 'from-pink-600 to-rose-600', dark: 'from-pink-400 to-rose-400' },
        red: { light: 'from-red-600 to-orange-600', dark: 'from-red-400 to-orange-400' },
        orange: { light: 'from-orange-600 to-amber-600', dark: 'from-orange-400 to-amber-400' },
        amber: { light: 'from-amber-600 to-yellow-600', dark: 'from-amber-400 to-yellow-400' },
        yellow: { light: 'from-yellow-600 to-lime-600', dark: 'from-yellow-400 to-lime-400' },
        lime: { light: 'from-lime-600 to-green-600', dark: 'from-lime-400 to-green-400' },
        green: { light: 'from-green-600 to-emerald-600', dark: 'from-green-400 to-emerald-400' },
        emerald: { light: 'from-emerald-600 to-teal-600', dark: 'from-emerald-400 to-teal-400' },
        teal: { light: 'from-teal-600 to-cyan-600', dark: 'from-teal-400 to-cyan-400' },
        cyan: { light: 'from-cyan-600 to-sky-600', dark: 'from-cyan-400 to-sky-400' },
        sky: { light: 'from-sky-600 to-blue-600', dark: 'from-sky-400 to-blue-400' },
    };
    return gradients[color] || gradients.blue;
};

const gradientClasses = getGradientClasses(themeColor);

// Prepare layout for grid - only active blocks
const layout = reactive(
    Array.isArray(props.page.blocks)
        ? props.page.blocks
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
</script>

<template>
    <Head :title="props.page.title">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    
    <div class="w-screen h-screen flex flex-col bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-gray-950 dark:via-slate-900 dark:to-gray-900 overflow-hidden">
        <!-- Theme Toggle Button -->
        <div class="absolute top-6 right-6 z-50">
            <button
                @click="toggleDark()"
                class="p-3 rounded-xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-lg border border-slate-200 dark:border-slate-700 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110"
                :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
            >
                <LucideSun 
                    v-if="isDark" 
                    :size="20" 
                    class="text-amber-500"
                />
                <LucideMoon 
                    v-else 
                    :size="20" 
                    class="text-indigo-600"
                />
            </button>
        </div>

        <!-- Header Section -->
        <div class="flex-shrink-0 px-4 pt-12 pb-6 lg:pt-16 lg:pb-8">
            <div class="max-w-6xl mx-auto text-center">
                <h1 :class="[
                    'text-4xl lg:text-6xl font-bold bg-gradient-to-r bg-clip-text text-transparent mb-4',
                    gradientClasses.light,
                    'dark:' + gradientClasses.dark
                ]">
                    {{ props.page.title }}
                </h1>
                <p v-if="props.page.description" class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                    {{ props.page.description }}
                </p>
            </div>
        </div>

        <!-- Content Section (scrollable) -->
        <div class="flex-1 overflow-y-auto px-4 pb-6">
            <div class="max-w-6xl mx-auto">
                <div v-if="layout.length > 0" class="bento-container">
                    <GridLayout 
                        v-model:layout="layout" 
                        :col-num="2" 
                        :row-height="150" 
                        :is-draggable="false"
                        :is-resizable="false"
                        :vertical-compact="false"
                        :prevent-collision="true"
                        :use-css-transforms="true"
                        :margin="[20, 20]"
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
                            class="bento-item"
                        >
                            <div class="bento-card h-full w-full p-6 overflow-auto">
                                <component 
                                    :is="blockComponents[item.type] ?? DefaultBlock" 
                                    :id="item.id"
                                    :title="item.title" 
                                    :content="item.content"
                                    :position="item.position"
                                />
                            </div>
                        </GridItem>
                    </GridLayout>
                </div>

                <!-- Empty State -->
                <div 
                    v-else 
                    class="flex flex-col items-center justify-center py-20 text-center"
                >
                    <div class="bg-white/60 dark:bg-slate-900/60 backdrop-blur-lg rounded-2xl p-12 border border-slate-200 dark:border-slate-800">
                        <h3 class="text-2xl font-semibold text-slate-900 dark:text-slate-50 mb-2">
                            No content yet
                        </h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            This page is still being built
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer (fixed at bottom) -->
        <div class="flex-shrink-0 px-4 py-6 border-t border-slate-200/50 dark:border-slate-800/50 backdrop-blur-sm">
            <div class="max-w-6xl mx-auto text-center">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Powered by <span :class="[
                        'font-semibold bg-gradient-to-r bg-clip-text text-transparent',
                        gradientClasses.light,
                        'dark:' + gradientClasses.dark
                    ]">MagLink</span>
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Bento Grid Container */
.bento-container {
  position: relative;
  min-height: 600px;
}

.vgl-layout {
  background-color: transparent;
  position: relative;
}

/* Bento Card Styling */
.bento-card {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 1.5rem;
  box-shadow: 
    0 4px 6px -1px rgba(0, 0, 0, 0.05),
    0 2px 4px -1px rgba(0, 0, 0, 0.03),
    0 0 0 1px rgba(255, 255, 255, 0.1) inset;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.bento-card:hover {
  transform: translateY(-2px);
  box-shadow: 
    0 20px 25px -5px rgba(0, 0, 0, 0.1),
    0 10px 10px -5px rgba(0, 0, 0, 0.04),
    0 0 0 1px rgba(255, 255, 255, 0.2) inset;
  border-color: rgba(99, 102, 241, 0.3);
}

/* Dark mode */
:root.dark .bento-card {
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(51, 65, 85, 0.8);
  box-shadow: 
    0 4px 6px -1px rgba(0, 0, 0, 0.3),
    0 2px 4px -1px rgba(0, 0, 0, 0.2),
    0 0 0 1px rgba(255, 255, 255, 0.05) inset;
}

:root.dark .bento-card:hover {
  box-shadow: 
    0 20px 25px -5px rgba(0, 0, 0, 0.5),
    0 10px 10px -5px rgba(0, 0, 0, 0.3),
    0 0 0 1px rgba(255, 255, 255, 0.1) inset;
  border-color: rgba(99, 102, 241, 0.5);
}

/* Grid items */
:deep(.vgl-item) {
  position: absolute;
  z-index: 1;
  cursor: default;
}

/* No resize handle */
:deep(.vgl-item__resizer) {
  display: none;
}

/* Smooth animations */
.bento-item {
  transition: all 0.3s ease;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .bento-card {
    padding: 1rem;
  }
}
</style>
