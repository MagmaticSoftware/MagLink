<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { 
    LucideEdit, 
    LucideTrash2, 
    LucidePlus, 
    LucideFileText, 
    LucideEye,
    LucideSearch,
    LucideFilter,
    LucideArrowUpDown,
    LucideLayout,
    LucideTrendingUp,
    LucideExternalLink,
    LucideCalendar,
    LucideCheckCircle
} from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';
import InputText from '@/components/volt/InputText.vue';
import Select from '@/components/volt/Select.vue';
import LimitReachedDialog from '@/components/LimitReachedDialog.vue';
import PlanSelectionModal from '@/components/PlanSelectionModal.vue';

const { t } = useI18n();
const page = usePage();

const props = defineProps<{
    pages: any[];
    stats?: {
        total: number;
        published: number;
        totalBlocks: number;
        avgBlocksPerPage: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('pages.title'),
        href: route('pages.index'),
    },
];

// Dialog visibility state
const showLimitDialog = ref(false);
const showPlanModal = ref(false);

const handleShowPlans = () => {
    showPlanModal.value = true;
};

// Handle create click - check limits
const handleCreateClick = () => {
    const limits = page.props.billing?.limits;
    const pageLimit = limits?.pages || 0;
    const currentCount = computedStats.value.total;
    
    // Se il limite è -1 (illimitato) o non raggiunto, naviga alla pagina di creazione
    if (pageLimit === -1 || currentCount < pageLimit) {
        router.visit(route('pages.create'));
    } else {
        // Altrimenti mostra la dialog
        showLimitDialog.value = true;
    }
};

// Search and filter state
const searchQuery = ref('');
const sortBy = ref('created_desc');
const filterStatus = ref('all');

// Compute stats
const computedStats = computed(() => {
    if (props.stats) return props.stats;
    
    const totalBlocks = props.pages.reduce((sum, page) => sum + (page.blocks_count || 0), 0);
    return {
        total: props.pages.length,
        published: props.pages.filter(p => p.is_published).length,
        totalBlocks,
        avgBlocksPerPage: props.pages.length > 0 ? Math.round(totalBlocks / props.pages.length) : 0
    };
});

// Get limits from billing
const limits = computed(() => page.props.billing?.limits || {});

// Helper to get limit status (normal, warning, exceeded)
const getLimitStatus = (current: number, limit: number) => {
    if (limit === -1) return 'normal'; // unlimited
    if (current >= limit) return 'exceeded';
    if (current >= limit - 1) return 'warning';
    return 'normal';
};

// Helper to get limit classes
const getLimitClasses = (status: string) => {
    if (status === 'exceeded') return 'text-red-700 dark:text-red-400';
    if (status === 'warning') return 'text-orange-600 dark:text-orange-400';
    return 'text-surface-900 dark:text-surface-50';
};

// Sort options
const sortOptions = [
    { label: t('pages.sort.newest'), value: 'created_desc' },
    { label: t('pages.sort.oldest'), value: 'created_asc' },
    { label: t('pages.sort.alphabetical'), value: 'title_asc' },
    { label: t('pages.sort.mostBlocks'), value: 'blocks_desc' }
];

// Filter options
const filterOptions = [
    { label: t('pages.filter.all'), value: 'all' },
    { label: t('pages.filter.published'), value: 'published' },
    { label: t('pages.filter.draft'), value: 'draft' }
];

// Filtered and sorted pages
const filteredPages = computed(() => {
    let result = [...props.pages];
    
    // Apply search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(page => 
            page.title?.toLowerCase().includes(query) ||
            page.description?.toLowerCase().includes(query) ||
            page.slug?.toLowerCase().includes(query)
        );
    }
    
    // Apply status filter
    if (filterStatus.value !== 'all') {
        if (filterStatus.value === 'published') {
            result = result.filter(page => page.is_published);
        } else if (filterStatus.value === 'draft') {
            result = result.filter(page => !page.is_published);
        }
    }
    
    // Apply sorting
    result.sort((a, b) => {
        switch (sortBy.value) {
            case 'created_desc':
                return new Date(b.created_at).getTime() - new Date(a.created_at).getTime();
            case 'created_asc':
                return new Date(a.created_at).getTime() - new Date(b.created_at).getTime();
            case 'blocks_desc':
                return (b.blocks_count || 0) - (a.blocks_count || 0);
            case 'title_asc':
                return (a.title || '').localeCompare(b.title || '');
            default:
                return 0;
        }
    });
    
    return result;
});

// Format date
const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString(undefined, { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
};

// Get public URL for page
const getPublicUrl = (slug: string) => {
    const protocol = window.location.protocol;
    const hostname = window.location.hostname.replace('app.', '');
    const port = window.location.port ? `:${window.location.port}` : '';
    return `${protocol}//${hostname}${port}/${slug}`;
};
</script>

<template>
    <Head title="Pages" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-surface-900 dark:text-surface-50">{{ t('pages.title') }}</h1>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">{{ t('pages.subtitle') }}</p>
                </div>
                <div>
                    <button
                        @click="handleCreateClick"
                        class="button-primary flex items-center gap-2"
                    >
                        <LucidePlus class="w-4 h-4" /> {{ t('pages.addNew') }}
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Pages -->
                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-surface-600 dark:text-surface-400">{{ t('pages.stats.totalPages') }}</p>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <LucideFileText :size="24" class="text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <p 
                        :class="['text-3xl font-bold mt-2', getLimitClasses(getLimitStatus(computedStats.total, limits.pages || -1))]"
                    >
                        {{ computedStats.total }}{{ limits.pages > 0 ? ` / ${limits.pages}` : '' }}
                    </p>
                    <p 
                        v-if="getLimitStatus(computedStats.total, limits.pages || -1) === 'exceeded'"
                        class="text-xs text-red-700 dark:text-red-400 mt-1"
                    >
                        Limite raggiunto per il piano free
                    </p>
                </div>

                <!-- Published Pages -->
                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-surface-600 dark:text-surface-400">{{ t('pages.stats.published') }}</p>
                        <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <LucideCheckCircle :size="24" class="text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-surface-900 dark:text-surface-50 mt-2">{{ computedStats.published }}</p>
                </div>

                <!-- Total Blocks -->
                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-surface-600 dark:text-surface-400">{{ t('pages.stats.totalBlocks') }}</p>
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <LucideLayout :size="24" class="text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                    <p 
                        :class="['text-3xl font-bold mt-2', getLimitClasses(getLimitStatus(computedStats.totalBlocks, limits.blocks_per_page || -1))]"
                    >
                        {{ computedStats.totalBlocks }}{{ limits.blocks_per_page > 0 ? ` / ${limits.blocks_per_page * computedStats.total}` : '' }}
                    </p>
                    <p 
                        v-if="getLimitStatus(computedStats.totalBlocks, limits.blocks_per_page * computedStats.total || -1) === 'exceeded'"
                        class="text-xs text-red-700 dark:text-red-400 mt-1"
                    >
                        Limite raggiunto per il piano free
                    </p>
                </div>

                <!-- Average Blocks Per Page -->
                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-surface-600 dark:text-surface-400">{{ t('pages.stats.avgBlocks') }}</p>
                        <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                            <LucideTrendingUp :size="24" class="text-orange-600 dark:text-orange-400" />
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-surface-900 dark:text-surface-50 mt-2">{{ computedStats.avgBlocksPerPage }}</p>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white dark:bg-surface-900 rounded-xl p-4 shadow-sm border border-surface-200 dark:border-surface-800">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <div class="relative">
                            <LucideSearch :size="20" class="absolute left-3 top-1/2 -translate-y-1/2 text-surface-400" />
                            <InputText
                                v-model="searchQuery"
                                :placeholder="t('pages.search')"
                                class="pl-10 w-full"
                            />
                        </div>
                    </div>

                    <!-- Sort -->
                    <div class="w-full md:w-48">
                        <div class="flex items-center gap-2">
                            <LucideArrowUpDown :size="20" class="text-surface-400" />
                            <Select
                                v-model="sortBy"
                                :options="sortOptions"
                                optionLabel="label"
                                optionValue="value"
                                :placeholder="t('pages.sortBy')"
                                class="w-full"
                            />
                        </div>
                    </div>

                    <!-- Filter -->
                    <div class="w-full md:w-48">
                        <div class="flex items-center gap-2">
                            <LucideFilter :size="20" class="text-surface-400" />
                            <Select
                                v-model="filterStatus"
                                :options="filterOptions"
                                optionLabel="label"
                                optionValue="value"
                                :placeholder="t('pages.filterBy')"
                                class="w-full"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pages Grid -->
            <div class="grid grid-cols-1 gap-4">
                <div
                    v-for="page in filteredPages"
                    :key="page.id"
                    class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800 hover:shadow-md transition-all hover:scale-[1.01]"
                >
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <!-- Page Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-primary/20 to-primary/5 rounded-lg flex items-center justify-center">
                                    <LucideFileText :size="24" class="text-primary" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-lg font-semibold text-surface-900 dark:text-surface-50">
                                            {{ page.title }}
                                        </h3>
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-medium"
                                            :class="page.is_published 
                                                ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' 
                                                : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400'"
                                        >
                                            {{ page.is_published ? t('pages.published') : t('pages.draft') }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-surface-600 dark:text-surface-400 mb-3">
                                        {{ page.description || 'No description' }}
                                    </p>
                                    
                                    <!-- Page URL -->
                                    <div class="flex items-center gap-2 mb-3 text-sm">
                                        <span class="font-medium text-surface-700 dark:text-surface-300">{{ t('pages.url') }}:</span>
                                        <code class="px-2 py-1 bg-surface-100 dark:bg-surface-800 rounded text-primary font-mono text-xs">
                                            /{{ page.slug }}
                                        </code>
                                        <a
                                            :href="`/pages/${page.slug}`"
                                            target="_blank"
                                            class="p-1 hover:bg-surface-100 dark:hover:bg-surface-800 rounded transition-colors"
                                            :title="t('pages.viewPage')"
                                        >
                                            <LucideExternalLink :size="14" class="text-surface-500" />
                                        </a>
                                    </div>

                                    <!-- Meta Info -->
                                    <div class="flex items-center gap-4 text-xs text-surface-500 dark:text-surface-400">
                                        <span class="flex items-center gap-1">
                                            <LucideCalendar :size="14" />
                                            {{ t('pages.createdAt') }}: {{ formatDate(page.created_at) }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <LucideLayout :size="14" />
                                            {{ page.blocks_count || 0 }} {{ t('pages.blocks') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <Link
                                :href="route('pages.show', page.slug)"
                                class="px-4 py-2 bg-purple-100 dark:bg-purple-900/30 hover:bg-purple-200 dark:hover:bg-purple-900/50 text-purple-700 dark:text-purple-400 rounded-lg flex items-center gap-2 transition-colors"
                            >
                                <LucideEye :size="16" />
                                <span class="hidden sm:inline">Anteprima</span>
                            </Link>
                            <a
                                :href="getPublicUrl(page.slug)"
                                target="_blank"
                                class="px-4 py-2 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-400 rounded-lg flex items-center gap-2 transition-colors"
                            >
                                <LucideExternalLink :size="16" />
                                <span class="hidden sm:inline">Visualizza</span>
                            </a>
                            <Link
                                :href="route('pages.edit', page.slug)"
                                class="px-4 py-2 bg-surface-100 dark:bg-surface-800 hover:bg-surface-200 dark:hover:bg-surface-700 text-surface-700 dark:text-surface-300 rounded-lg flex items-center gap-2 transition-colors"
                            >
                                <LucideEdit :size="16" />
                                <span class="hidden sm:inline">{{ t('common.edit') }}</span>
                            </Link>
                            <Link
                                :href="route('pages.destroy', page.slug)"
                                method="delete"
                                as="button"
                                class="px-4 py-2 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 rounded-lg flex items-center gap-2 transition-colors"
                            >
                                <LucideTrash2 :size="16" />
                                <span class="hidden sm:inline">{{ t('common.delete') }}</span>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Empty States -->
                <div
                    v-if="filteredPages.length === 0 && props.pages.length > 0"
                    class="text-center py-12"
                >
                    <LucideSearch class="w-16 h-16 mx-auto mb-4 text-surface-300 dark:text-surface-700" />
                    <p class="text-lg font-medium text-surface-700 dark:text-surface-300">{{ t('common.noResults') }}</p>
                    <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Try adjusting your search or filters</p>
                </div>

                <div
                    v-if="props.pages.length === 0"
                    class="text-center py-16"
                >
                    <div class="inline-flex p-4 bg-surface-100 dark:bg-surface-800 rounded-full mb-4">
                        <LucideFileText class="w-16 h-16 text-surface-400 dark:text-surface-600" />
                    </div>
                    <p class="text-lg font-medium text-surface-700 dark:text-surface-300">{{ t('pages.noPages') }}</p>
                    <p class="text-sm text-surface-500 dark:text-surface-400 mt-1 mb-4">{{ t('pages.createFirst') }}</p>
                    <Link
                        :href="route('pages.create')"
                        class="button-primary inline-flex items-center gap-2"
                    >
                        <LucidePlus class="w-4 h-4" /> {{ t('pages.addNew') }}
                    </Link>
                </div>
            </div>
        </div>

        <!-- Limit Reached Dialog -->
        <LimitReachedDialog
            v-model:visible="showLimitDialog"
            type="pages"
            @show-plans="handleShowPlans"
        />

        <PlanSelectionModal
            v-model:visible="showPlanModal"
            :plans="page.props.plans || {}"
            :is-new-user="page.props.billing?.isNewUser || false"
            :has-active-trial="page.props.billing?.hasActiveTrial || false"
            :can-start-trial="page.props.billing?.canStartTrial || false"
            :is-subscribed="page.props.billing?.isSubscribed || false"
            :current-plan-key="page.props.billing?.currentPlanKey || null"
            :on-free-plan="page.props.billing?.onFreePlan || false"
        />
    </AppLayout>
</template>
