<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useDark, useToggle } from '@vueuse/core';
import { LucideSun, LucideMoon } from 'lucide-vue-next';
import LinkBlock from '@/components/tenant/pageblocks/show/Link.vue';
import HtmlBlock from '@/components/tenant/pageblocks/show/Html.vue';
import TextBlock from '@/components/tenant/pageblocks/show/Text.vue';
import ImageBlock from '@/components/tenant/pageblocks/show/Image.vue';
import VideoBlock from '@/components/tenant/pageblocks/show/Video.vue';
import DefaultBlock from '@/components/tenant/pageblocks/show/Default.vue';
import TitleBlock from '@/components/tenant/pageblocks/show/Title.vue';
import SeparatorBlock from '@/components/tenant/pageblocks/show/Separator.vue';
import MapBlock from '@/components/tenant/pageblocks/show/Map.vue';

const isDark = useDark();
const toggleDark = useToggle(isDark);

const blockComponents: Record<string, any> = {
    link: LinkBlock,
    html: HtmlBlock,
    text: TextBlock,
    image: ImageBlock,
    video: VideoBlock,
    title: TitleBlock,
    separator: SeparatorBlock,
    map: MapBlock,
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

// Generate color classes based on theme color
const getColorClasses = (color: string) => {
    const colors: Record<string, { light: string; dark: string }> = {
        blue: { light: 'text-blue-600', dark: 'text-blue-400' },
        indigo: { light: 'text-indigo-600', dark: 'text-indigo-400' },
        purple: { light: 'text-purple-600', dark: 'text-purple-400' },
        pink: { light: 'text-pink-600', dark: 'text-pink-400' },
        red: { light: 'text-red-600', dark: 'text-red-400' },
        orange: { light: 'text-orange-600', dark: 'text-orange-400' },
        amber: { light: 'text-amber-600', dark: 'text-amber-400' },
        yellow: { light: 'text-yellow-600', dark: 'text-yellow-400' },
        lime: { light: 'text-lime-600', dark: 'text-lime-400' },
        green: { light: 'text-green-600', dark: 'text-green-400' },
        emerald: { light: 'text-emerald-600', dark: 'text-emerald-400' },
        teal: { light: 'text-teal-600', dark: 'text-teal-400' },
        cyan: { light: 'text-cyan-600', dark: 'text-cyan-400' },
        sky: { light: 'text-sky-600', dark: 'text-sky-400' },
    };
    return colors[color] || colors.blue;
};

const colorClasses = getColorClasses(themeColor);

// Get active blocks with their positions
const blocks = computed(() => {
    if (!Array.isArray(props.page.blocks)) return [];
    
    return props.page.blocks
        .filter(block => block.is_active)
        .map(block => {
            const x = block.position?.x ?? 0;
            const y = block.position?.y ?? 0;
            const width = block.size?.width ?? 1;
            const height = block.size?.height ?? 2;
            
            return {
                ...block,
                position: { x, y },
                size: { width, height }
            };
        });
});

// Calculate max rows needed for the grid
const maxRows = computed(() => {
    if (blocks.value.length === 0) return 0;
    
    return Math.max(...blocks.value.map(block => {
        const height = (block.type === 'title' || block.type === 'separator') ? 1 : block.size.height;
        return block.position.y + height;
    }));
});

// Helper to get grid positioning styles
const getGridPosition = (block: any) => {
    const x = block.position.x;
    const y = block.position.y;
    const width = block.size.width;
    const height = (block.type === 'title' || block.type === 'separator') ? 1 : block.size.height;
    
    return {
        gridColumnStart: x + 1, // CSS Grid is 1-based
        gridColumnEnd: x + 1 + width,
        gridRowStart: y + 1,
        gridRowEnd: y + 1 + height
    };
};

// Helper to get block height class
const getBlockHeight = (block: any) => {
    // All blocks use size height (80px base unit)
    const heightMap: Record<number, string> = {
        1: 'h-[80px]',
        2: 'h-[192px]', // 80*2 + 32 (gap)
        3: 'h-[304px]', // 80*3 + 32*2
        4: 'h-[416px]'  // 80*4 + 32*3
    };
    
    return heightMap[block.size.height] || heightMap[2];
};
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
            <!-- Header with Image: Left Aligned -->
            <div class="max-w-6xl mx-auto">
                <div class="flex items-center gap-6">
                    <img 
                        v-if="props.page.settings?.profile_image"
                        :src="props.page.settings.profile_image"
                        alt="Profile"
                        class="w-46 h-46 lg:w-46 lg:h-46 rounded-full object-cover shadow-xl shrink-0"
                    />
                    <div class="flex-1 text-left px-6">
                        <h1 :class="[
                            'text-4xl lg:text-6xl font-bold mb-2',
                            colorClasses.light,
                            'dark:' + colorClasses.dark
                        ]">
                            {{ props.page.title }}
                        </h1>
                        <p v-if="props.page.description" class="text-xl text-slate-700 dark:text-slate-400 whitespace-pre-line">
                            {{ props.page.description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Section (scrollable) -->
        <div class="flex-1 overflow-y-auto px-4 pb-6">
            <div class="max-w-6xl mx-auto">
                <div 
                    v-if="blocks.length > 0" 
                    class="grid grid-cols-4 gap-8 w-full"
                    :style="{ gridTemplateRows: `repeat(${maxRows}, minmax(0, 1fr))` }"
                >
                    <div 
                        v-for="block in blocks" 
                        :key="block.id"
                        :class="getBlockHeight(block)"
                        :style="getGridPosition(block)"
                    >
                        <div 
                            :class="[
                                'h-full w-full',
                                block.type === 'title' || block.type === 'separator' 
                                    ? '' 
                                    : 'bento-card overflow-auto',
                                block.type === 'map' || block.type === 'video' || block.type === 'image' || block.type === 'text' || block.type === 'title' || block.type === 'separator'
                                    ? 'p-0' 
                                    : 'p-6'
                            ]"
                        >
                            <component 
                                :is="blockComponents[block.type] ?? DefaultBlock" 
                                :id="block.id"
                                :title="block.title" 
                                :content="block.content"
                                :position="block.position"
                            />
                        </div>
                    </div>
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
                        'font-semibold',
                        colorClasses.light,
                        'dark:' + colorClasses.dark
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
