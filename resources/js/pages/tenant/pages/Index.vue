<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Calendar, CheckCircle2, Eye, ExternalLink, FileText, LayoutGrid, Pencil, Plus, SearchX, Trash2, TrendingUp } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';
import PageHeader from '@/components/PageHeader.vue';
import ListToolbar from '@/components/ListToolbar.vue';
import StatCard from '@/components/dashboard/StatCard.vue';
import { METRIC_COLORS } from '@/components/dashboard/colors';
import { useRoute } from '@/composables/useRoute';
import LimitReachedDialog from '@/components/LimitReachedDialog.vue';
import PlanSelectionModal from '@/components/PlanSelectionModal.vue';

const { t } = useI18n();
const page = usePage<any>();
const route = useRoute();

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
    if (!atPageLimit.value) {
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

// Limiti del piano
const limits = computed(() => page.props.billing?.limits || {});
const pageLimit = computed<number>(() => limits.value.pages ?? -1);
const atPageLimit = computed(() => pageLimit.value !== -1 && computedStats.value.total >= pageLimit.value);

const statCards = computed(() => [
    {
        label: t('pages.stats.totalPages'),
        value: pageLimit.value > 0 ? `${computedStats.value.total} / ${pageLimit.value}` : String(computedStats.value.total),
        valueClass: atPageLimit.value ? 'text-rose-600 dark:text-rose-400' : undefined,
        icon: FileText,
        iconClass: 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400',
        color: METRIC_COLORS.pages,
    },
    {
        label: t('pages.stats.published'),
        value: String(computedStats.value.published),
        icon: CheckCircle2,
        iconClass: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
        color: METRIC_COLORS.qrcodes,
    },
    {
        label: t('pages.stats.totalBlocks'),
        value: String(computedStats.value.totalBlocks),
        icon: LayoutGrid,
        iconClass: 'bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400',
        color: METRIC_COLORS.blocks,
    },
    {
        label: t('pages.stats.avgBlocks'),
        value: String(computedStats.value.avgBlocksPerPage),
        icon: TrendingUp,
        iconClass: 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
        color: METRIC_COLORS.links,
    },
]);

const destroy = (item: { slug: string; title: string }) => {
    if (!window.confirm(t('pages.confirmDelete', { title: item.title }))) return;
    router.delete(route('pages.destroy', item.slug), { preserveScroll: true });
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
    <Head :title="t('pages.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex w-full flex-col gap-4 sm:gap-5 lg:p-2">
            <PageHeader :title="t('pages.title')" :subtitle="t('pages.subtitle')">
                <template #actions>
                    <button type="button" class="ml-btn-primary" @click="handleCreateClick">
                        <Plus :size="16" :stroke-width="2.5" /> {{ t('pages.addNew') }}
                    </button>
                </template>
            </PageHeader>

            <div class="grid grid-cols-2 gap-3 sm:gap-5 xl:grid-cols-4">
                <StatCard v-for="card in statCards" :key="card.label" v-bind="card" />
            </div>

            <ListToolbar
                v-model:search="searchQuery"
                v-model:sort="sortBy"
                v-model:filter="filterStatus"
                :search-placeholder="t('pages.search')"
                :sort-options="sortOptions"
                :filter-options="filterOptions"
                :sort-label="t('pages.sortBy')"
                :filter-label="t('pages.filterBy')"
            />

            <ul v-if="filteredPages.length" class="flex flex-col gap-2.5">
                <li
                    v-for="item in filteredPages"
                    :key="item.id"
                    class="ml-card grid gap-4 p-4 transition-colors hover:bg-surface-50/80 sm:px-5 lg:grid-cols-[minmax(0,1fr)_12rem_5.5rem_auto] lg:items-center dark:hover:bg-surface-800"
                >
                    <div class="flex min-w-0 items-start gap-3.5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                            <FileText :size="18" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <Link :href="route('pages.edit', item.slug)" class="block truncate font-semibold text-surface-900 hover:text-primary-600 dark:text-white dark:hover:text-primary-400">
                                {{ item.title }}
                            </Link>
                            <p class="truncate text-xs text-surface-500 dark:text-surface-400">{{ item.description || t('pages.noDescription') }}</p>
                            <a :href="getPublicUrl(item.slug)" target="_blank" rel="noopener" class="mt-2 inline-flex max-w-full items-center gap-1.5 rounded-md border border-surface-200 bg-surface-50 px-2 py-0.5 font-mono text-xs text-primary-600 hover:underline dark:border-surface-700 dark:bg-surface-900/60 dark:text-primary-400">
                                <span class="truncate">/{{ item.slug }}</span> <ExternalLink :size="12" class="shrink-0" />
                            </a>
                        </div>
                    </div>

                    <dl class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-surface-500 lg:flex-col lg:border-l lg:border-surface-100 lg:pl-5 dark:text-surface-400 dark:lg:border-surface-700">
                        <div class="flex items-center gap-1.5">
                            <dt class="sr-only">{{ t('pages.createdAt') }}</dt>
                            <Calendar :size="13" class="shrink-0" />
                            <dd>{{ formatDate(item.created_at) }}</dd>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <LayoutGrid :size="13" class="shrink-0" />
                            <dd><strong class="font-semibold text-surface-800 dark:text-surface-100">{{ item.blocks_count || 0 }}</strong> {{ t('pages.blocks') }}</dd>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <Eye :size="13" class="shrink-0" />
                            <dd><strong class="font-semibold text-surface-800 dark:text-surface-100">{{ item.views || 0 }}</strong> {{ t('pages.views') }}</dd>
                        </div>
                    </dl>

                    <div class="flex lg:justify-center">
                        <span
                            class="ml-badge"
                            :class="item.is_published
                                ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300'
                                : 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300'"
                        >
                            <span class="h-1.5 w-1.5 rounded-full" :class="item.is_published ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                            {{ item.is_published ? t('pages.published') : t('pages.draft') }}
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5 lg:justify-end">
                        <Link :href="route('pages.show', item.slug)" class="ml-btn-secondary px-3 py-1.5 text-[13px]">
                            <Eye :size="15" class="text-violet-500" /> <span>{{ t('pages.preview') }}</span>
                        </Link>
                        <Link :href="route('pages.edit', item.slug)" class="ml-btn-secondary px-3 py-1.5 text-[13px]">
                            <Pencil :size="14" /> <span>{{ t('common.edit') }}</span>
                        </Link>
                        <button type="button" class="ml-icon-btn h-[34px] w-[34px] text-rose-500 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10" :title="t('common.delete')" @click="destroy(item)">
                            <Trash2 :size="16" />
                        </button>
                    </div>
                </li>
            </ul>

            <div v-else-if="props.pages.length > 0" class="ml-card flex flex-col items-center gap-2 py-14 text-center">
                <SearchX :size="28" class="text-surface-300 dark:text-surface-600" />
                <p class="font-medium text-surface-700 dark:text-surface-200">{{ t('common.noResults') }}</p>
                <p class="text-sm text-surface-500 dark:text-surface-400">{{ t('common.noResultsHint') }}</p>
            </div>

            <div v-else class="ml-card flex flex-col items-center gap-2 py-16 text-center">
                <span class="mb-2 flex h-14 w-14 items-center justify-center rounded-full bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                    <FileText :size="26" />
                </span>
                <p class="font-medium text-surface-800 dark:text-surface-100">{{ t('pages.noPages') }}</p>
                <p class="mb-3 text-sm text-surface-500 dark:text-surface-400">{{ t('pages.createFirst') }}</p>
                <Link :href="route('pages.create')" class="ml-btn-primary">
                    <Plus :size="16" :stroke-width="2.5" /> {{ t('pages.addNew') }}
                </Link>
            </div>
        </div>

        <LimitReachedDialog v-model:visible="showLimitDialog" type="pages" @show-plans="handleShowPlans" />

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
