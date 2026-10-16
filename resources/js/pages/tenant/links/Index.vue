<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    BarChart3,
    Calendar,
    Check,
    Copy,
    Crown,
    Download,
    ExternalLink,
    EyeOff,
    Link2,
    MousePointerClick,
    Pencil,
    Plus,
    SearchX,
    ShieldCheck,
    Trash2,
    TrendingUp,
    Eye,
} from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';
import { useRoute } from '@/composables/useRoute';
import PageHeader from '@/components/PageHeader.vue';
import ListToolbar from '@/components/ListToolbar.vue';
import StatCard, { type Trend } from '@/components/dashboard/StatCard.vue';
import Sparkline from '@/components/dashboard/Sparkline.vue';
import { METRIC_COLORS } from '@/components/dashboard/colors';
import LimitReachedDialog from '@/components/LimitReachedDialog.vue';
import PlanSelectionModal from '@/components/PlanSelectionModal.vue';
import AnalyticsExportDialog from '@/components/AnalyticsExportDialog.vue';
import type { PageProps } from '@/types/inertia';

const { t } = useI18n();
const page = usePage<PageProps>();
const route = useRoute();

const showLimitDialog = ref(false);
const showPlanModal = ref(false);
const showExportDialog = ref(false);

const handleShowPlans = () => {
    showPlanModal.value = true;
};

const props = defineProps<{
    links: any[];
    shortUrl: string;
    stats?: {
        total: number;
        active: number;
        totalClicks: number;
        avgClicksPerLink: number;
    };
    trends?: { created: Trend; views: Trend };
    sparklines?: Record<number, number[]>;
    canExportAnalytics?: boolean;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('links.title'),
        href: route('links.index'),
    },
];

// Search and filter state
const searchQuery = ref('');
const sortBy = ref('created_desc');
const filterStatus = ref('all');

// Compute stats from links if not provided
const computedStats = computed(() => {
    if (props.stats) return props.stats;

    const totalClicks = props.links.reduce((sum, link) => sum + (link.views_count || 0), 0);
    return {
        total: props.links.length,
        active: props.links.filter((l) => l.is_active).length,
        totalClicks,
        avgClicksPerLink: props.links.length > 0 ? Number((totalClicks / props.links.length).toFixed(1)) : 0,
    };
});

// Get limits from billing
const limits = computed(() => page.props.billing?.limits || ({} as Record<string, number>));
const linkLimit = computed(() => limits.value.links ?? -1);
const atLinkLimit = computed(() => linkLimit.value !== -1 && computedStats.value.total >= linkLimit.value);

const formatNumber = (num: number) => new Intl.NumberFormat(undefined, { maximumFractionDigits: 1 }).format(num);

const statCards = computed(() => [
    {
        label: t('links.stats.totalLinks'),
        value: linkLimit.value > 0 ? `${computedStats.value.total} / ${linkLimit.value}` : formatNumber(computedStats.value.total),
        valueClass: atLinkLimit.value ? 'text-rose-600 dark:text-rose-400' : undefined,
        icon: Link2,
        iconClass: 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
        color: METRIC_COLORS.links,
        trend: props.trends?.created,
    },
    {
        label: t('links.stats.activeLinks'),
        value: formatNumber(computedStats.value.active),
        icon: TrendingUp,
        iconClass: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
        color: METRIC_COLORS.qrcodes,
    },
    {
        label: t('links.stats.totalClicks'),
        value: formatNumber(computedStats.value.totalClicks),
        icon: MousePointerClick,
        iconClass: 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400',
        color: METRIC_COLORS.pages,
        trend: props.trends?.views,
    },
    {
        label: t('links.stats.avgClicks'),
        value: formatNumber(Number(computedStats.value.avgClicksPerLink)),
        icon: Eye,
        iconClass: 'bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400',
        color: METRIC_COLORS.blocks,
    },
]);

// Sort options
const sortOptions = [
    { label: t('links.sort.newest'), value: 'created_desc' },
    { label: t('links.sort.oldest'), value: 'created_asc' },
    { label: t('links.sort.mostClicks'), value: 'clicks_desc' },
    { label: t('links.sort.leastClicks'), value: 'clicks_asc' },
    { label: t('links.sort.alphabetical'), value: 'title_asc' },
];

// Filter options
const filterOptions = [
    { label: t('links.filter.all'), value: 'all' },
    { label: t('links.filter.active'), value: 'active' },
    { label: t('links.filter.inactive'), value: 'inactive' },
];

// Filtered and sorted links
const filteredLinks = computed(() => {
    let result = [...props.links];

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            (link) =>
                link.title?.toLowerCase().includes(query) ||
                link.description?.toLowerCase().includes(query) ||
                link.slug?.toLowerCase().includes(query) ||
                link.url?.toLowerCase().includes(query),
        );
    }

    if (filterStatus.value !== 'all') {
        result = result.filter((link) => (filterStatus.value === 'active' ? link.is_active : !link.is_active));
    }

    result.sort((a, b) => {
        switch (sortBy.value) {
            case 'created_desc':
                return new Date(b.created_at).getTime() - new Date(a.created_at).getTime();
            case 'created_asc':
                return new Date(a.created_at).getTime() - new Date(b.created_at).getTime();
            case 'clicks_desc':
                return (b.views_count || 0) - (a.views_count || 0);
            case 'clicks_asc':
                return (a.views_count || 0) - (b.views_count || 0);
            case 'title_asc':
                return (a.title || '').localeCompare(b.title || '');
            default:
                return 0;
        }
    });

    return result;
});

// Export: preseleziona i 5 link più visitati tra quelli filtrati
const exportPreselected = computed(() =>
    [...filteredLinks.value]
        .sort((a, b) => (b.views_count || 0) - (a.views_count || 0))
        .slice(0, 5)
        .map((l) => l.id),
);

// Copia del link breve, con conferma visiva sul pulsante
const copiedId = ref<number | null>(null);
const copyToClipboard = async (link: { id: number; slug: string }) => {
    await navigator.clipboard.writeText(`${props.shortUrl}/${link.slug}`);
    copiedId.value = link.id;
    setTimeout(() => {
        if (copiedId.value === link.id) copiedId.value = null;
    }, 1500);
};

const destroy = (link: { id: number; title: string }) => {
    if (!window.confirm(t('links.confirmDelete', { title: link.title }))) return;
    router.delete(route('links.destroy', link.id), { preserveScroll: true });
};

// Handle create button click
const handleCreateClick = () => {
    if (atLinkLimit.value) {
        showLimitDialog.value = true;
    } else {
        router.visit(route('links.create'));
    }
};

const formatDate = (date: string) =>
    new Date(date).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });

const displayUrl = (url: string) => url?.replace(/^https?:\/\//, '');
</script>

<template>
    <Head :title="t('links.title')" />

    <!-- Limit Reached Dialog -->
    <LimitReachedDialog v-model:visible="showLimitDialog" type="links" @show-plans="handleShowPlans" />

    <AnalyticsExportDialog
        v-model:visible="showExportDialog"
        :links="props.links"
        :can-export="!!props.canExportAnalytics"
        :preselected="exportPreselected"
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

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex w-full flex-col gap-4 sm:gap-5 lg:p-2">
            <PageHeader :title="t('links.title')" :subtitle="t('links.subtitle')">
                <template #actions>
                    <button v-if="props.links.length > 0" type="button" class="ml-btn-secondary" @click="showExportDialog = true">
                        <Download :size="16" /> {{ t('analytics.export.button') }}
                        <Crown v-if="!props.canExportAnalytics" :size="15" class="text-amber-500" />
                    </button>
                    <button type="button" class="ml-btn-primary" @click="handleCreateClick">
                        <Plus :size="16" :stroke-width="2.5" /> {{ t('links.addNew') }}
                    </button>
                </template>
            </PageHeader>

            <!-- Statistiche -->
            <div class="grid grid-cols-2 gap-3 sm:gap-5 xl:grid-cols-4">
                <StatCard v-for="card in statCards" :key="card.label" v-bind="card" />
            </div>

            <ListToolbar
                v-model:search="searchQuery"
                v-model:sort="sortBy"
                v-model:filter="filterStatus"
                :search-placeholder="t('links.search')"
                :sort-options="sortOptions"
                :filter-options="filterOptions"
                :sort-label="t('links.sortBy')"
                :filter-label="t('links.filterBy')"
            />

            <!-- Elenco link -->
            <ul v-if="filteredLinks.length" class="flex flex-col gap-2.5">
                <li
                    v-for="link in filteredLinks"
                    :key="link.id"
                    class="ml-card grid gap-4 p-4 transition-colors hover:bg-surface-50/80 sm:px-5 lg:grid-cols-[minmax(0,1fr)_11rem_5.5rem_auto] xl:grid-cols-[minmax(0,1fr)_12rem_6.5rem_5.5rem_auto] lg:items-center dark:hover:bg-surface-800"
                >
                    <!-- Titolo, destinazione e link breve -->
                    <div class="flex min-w-0 items-start gap-3.5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                            <Link2 :size="18" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <Link :href="route('links.edit', link.id)" class="block truncate font-semibold text-surface-900 hover:text-primary-600 dark:text-white dark:hover:text-primary-400">
                                {{ link.title }}
                            </Link>
                            <a :href="link.url" target="_blank" rel="noopener" class="block truncate text-xs text-surface-500 hover:underline dark:text-surface-400">{{ displayUrl(link.url) }}</a>
                            <div class="mt-2 inline-flex max-w-full items-center gap-0.5 rounded-md border border-surface-200 bg-surface-50 py-0.5 pr-0.5 pl-2 dark:border-surface-700 dark:bg-surface-900/60">
                                <span class="truncate font-mono text-xs text-primary-600 dark:text-primary-400">{{ displayUrl(shortUrl) }}/{{ link.slug }}</span>
                                <button type="button" class="ml-icon-btn h-6 w-6" :title="t('links.copyLink')" @click="copyToClipboard(link)">
                                    <Check v-if="copiedId === link.id" :size="13" class="text-emerald-600" />
                                    <Copy v-else :size="13" />
                                </button>
                                <a :href="`${shortUrl}/${link.slug}`" target="_blank" rel="noopener" class="ml-icon-btn h-6 w-6" :title="t('links.openLink')">
                                    <ExternalLink :size="13" />
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Dati -->
                    <dl class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-surface-500 lg:flex-col lg:border-l lg:border-surface-100 lg:pl-5 dark:text-surface-400 dark:lg:border-surface-700">
                        <div class="flex items-center gap-1.5">
                            <dt class="sr-only">{{ t('links.createdAt') }}</dt>
                            <Calendar :size="13" class="shrink-0" />
                            <dd>{{ formatDate(link.created_at) }}</dd>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <dt class="sr-only">{{ t('links.clicks') }}</dt>
                            <MousePointerClick :size="13" class="shrink-0" />
                            <dd><strong class="font-semibold text-surface-800 dark:text-surface-100">{{ link.views_count || 0 }}</strong> {{ t('links.clicks').toLowerCase() }}</dd>
                        </div>
                        <div v-if="link.views_with_consent_count > 0 || link.views_anonymous_count > 0" class="flex items-center gap-3">
                            <span v-if="link.views_with_consent_count > 0" class="inline-flex items-center gap-1" :title="t('links.consentViews', { count: link.views_with_consent_count })">
                                <ShieldCheck :size="13" class="text-blue-500" /> {{ link.views_with_consent_count }}
                            </span>
                            <span v-if="link.views_anonymous_count > 0" class="inline-flex items-center gap-1" :title="t('links.anonymousViews', { count: link.views_anonymous_count })">
                                <EyeOff :size="13" /> {{ link.views_anonymous_count }}
                            </span>
                        </div>
                    </dl>

                    <!-- Andamento 7 giorni -->
                    <div class="hidden xl:block" :title="t('links.last7days')">
                        <Sparkline :values="sparklines?.[link.id] ?? []" :color="METRIC_COLORS.links" :width="96" :height="32" />
                    </div>

                    <!-- Stato -->
                    <div class="flex lg:justify-center">
                        <span
                            class="ml-badge"
                            :class="link.is_active
                                ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300'
                                : 'bg-surface-100 text-surface-600 dark:bg-surface-700/60 dark:text-surface-300'"
                        >
                            <span class="h-1.5 w-1.5 rounded-full" :class="link.is_active ? 'bg-emerald-500' : 'bg-surface-400'"></span>
                            {{ link.is_active ? t('qrcodes.active') : t('qrcodes.inactive') }}
                        </span>
                    </div>

                    <!-- Azioni -->
                    <div class="flex items-center gap-1.5 lg:justify-end">
                        <Link :href="route('links.analytics', link.id)" class="ml-btn-secondary px-3 py-1.5 text-[13px]">
                            <BarChart3 :size="15" class="text-violet-500" /> <span class="lg:hidden xl:inline">Analytics</span>
                        </Link>
                        <Link :href="route('links.edit', link.id)" class="ml-btn-secondary px-3 py-1.5 text-[13px]">
                            <Pencil :size="14" /> <span class="lg:hidden xl:inline">{{ t('common.edit') }}</span>
                        </Link>
                        <button type="button" class="ml-icon-btn h-[34px] w-[34px] text-rose-500 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10" :title="t('common.delete')" @click="destroy(link)">
                            <Trash2 :size="16" />
                        </button>
                    </div>
                </li>
            </ul>

            <!-- Nessun risultato con i filtri attuali -->
            <div v-else-if="props.links.length > 0" class="ml-card flex flex-col items-center gap-2 py-14 text-center">
                <SearchX :size="28" class="text-surface-300 dark:text-surface-600" />
                <p class="font-medium text-surface-700 dark:text-surface-200">{{ t('common.noResults') }}</p>
                <p class="text-sm text-surface-500 dark:text-surface-400">{{ t('common.noResultsHint') }}</p>
            </div>

            <!-- Nessun link -->
            <div v-else class="ml-card flex flex-col items-center gap-2 py-16 text-center">
                <span class="mb-2 flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <Link2 :size="26" />
                </span>
                <p class="font-medium text-surface-800 dark:text-surface-100">{{ t('links.noLinks') }}</p>
                <p class="mb-3 text-sm text-surface-500 dark:text-surface-400">{{ t('links.createFirst') }}</p>
                <Link :href="route('links.create')" class="ml-btn-primary">
                    <Plus :size="16" :stroke-width="2.5" /> {{ t('links.addNew') }}
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
