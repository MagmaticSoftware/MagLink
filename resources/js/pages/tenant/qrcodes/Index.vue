<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    BarChart3,
    Calendar,
    Check,
    Contact,
    Copy,
    Eye,
    EyeOff,
    ExternalLink,
    Link2,
    Mail,
    MessageSquare,
    Pencil,
    Phone,
    Plus,
    QrCode,
    ScanLine,
    SearchX,
    ShieldCheck,
    Sparkles,
    Trash2,
    TrendingUp,
    Type,
} from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { ref, computed, type Component } from 'vue';
import { useRoute } from '@/composables/useRoute';
import PageHeader from '@/components/PageHeader.vue';
import ListToolbar from '@/components/ListToolbar.vue';
import StatCard, { type Trend } from '@/components/dashboard/StatCard.vue';
import Sparkline from '@/components/dashboard/Sparkline.vue';
import { METRIC_COLORS } from '@/components/dashboard/colors';
import LimitReachedDialog from '@/components/LimitReachedDialog.vue';
import PlanSelectionModal from '@/components/PlanSelectionModal.vue';
import type { PageProps } from '@/types/inertia';

const { t } = useI18n();
const page = usePage<PageProps>();
const route = useRoute();

const showLimitDialog = ref(false);
const showPlanModal = ref(false);

const handleShowPlans = () => {
    showPlanModal.value = true;
};

const props = defineProps<{
    qrcodes: any[];
    shortUrl: string;
    stats?: {
        total: number;
        active: number;
        totalScans: number;
        avgScansPerCode: number;
    };
    trends?: { created: Trend; views: Trend };
    sparklines?: Record<number, number[]>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('qrcodes.title'),
        href: route('qrcodes.index'),
    },
];

// Search and filter state
const searchQuery = ref('');
const sortBy = ref('created_desc');
const filterStatus = ref('all');

const computedStats = computed(() => {
    const totalScans = props.qrcodes.reduce((sum, qr) => sum + (qr.views_count || 0), 0);
    return {
        total: props.stats?.total ?? props.qrcodes.length,
        active: props.stats?.active ?? props.qrcodes.filter((q) => q.is_active).length,
        dynamic: props.qrcodes.filter((q) => q.type === 'dynamic').length,
        totalScans: props.stats?.totalScans ?? totalScans,
    };
});

// Limiti del piano
const limits = computed(() => page.props.billing?.limits || ({} as Record<string, number>));
const qrLimit = computed(() => limits.value.qrcodes ?? -1);
const dynamicLimit = computed(() => limits.value.qrcodes_dynamic ?? -1);
const atQrLimit = computed(() => qrLimit.value !== -1 && computedStats.value.total >= qrLimit.value);
const atDynamicLimit = computed(() => dynamicLimit.value !== -1 && computedStats.value.dynamic >= dynamicLimit.value);

const formatNumber = (num: number) => new Intl.NumberFormat(undefined, { maximumFractionDigits: 1 }).format(num);
const withLimit = (value: number, limit: number) => (limit > 0 ? `${value} / ${limit}` : formatNumber(value));
const limitClass = (reached: boolean) => (reached ? 'text-rose-600 dark:text-rose-400' : undefined);

const statCards = computed(() => [
    {
        label: t('qrcodes.stats.totalQrCodes'),
        value: withLimit(computedStats.value.total, qrLimit.value),
        valueClass: limitClass(atQrLimit.value),
        icon: QrCode,
        iconClass: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
        color: METRIC_COLORS.qrcodes,
        trend: props.trends?.created,
    },
    {
        label: t('qrcodes.stats.activeQrCodes'),
        value: formatNumber(computedStats.value.active),
        icon: TrendingUp,
        iconClass: 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
        color: METRIC_COLORS.links,
    },
    {
        label: t('qrcodes.stats.dynamicQrCodes'),
        value: withLimit(computedStats.value.dynamic, dynamicLimit.value),
        valueClass: limitClass(atDynamicLimit.value),
        icon: Sparkles,
        iconClass: 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400',
        color: METRIC_COLORS.pages,
    },
    {
        label: t('qrcodes.stats.totalScans'),
        value: formatNumber(computedStats.value.totalScans),
        icon: ScanLine,
        iconClass: 'bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400',
        color: METRIC_COLORS.blocks,
        trend: props.trends?.views,
    },
]);

// Sort options
const sortOptions = [
    { label: t('qrcodes.sort.newest'), value: 'created_desc' },
    { label: t('qrcodes.sort.oldest'), value: 'created_asc' },
    { label: t('qrcodes.sort.mostScans'), value: 'scans_desc' },
    { label: t('qrcodes.sort.leastScans'), value: 'scans_asc' },
    { label: t('qrcodes.sort.alphabetical'), value: 'name_asc' },
];

// Filter options
const filterOptions = [
    { label: t('qrcodes.filter.all'), value: 'all' },
    { label: t('qrcodes.filter.active'), value: 'active' },
    { label: t('qrcodes.filter.inactive'), value: 'inactive' },
    { label: t('qrcodes.filter.static'), value: 'static' },
    { label: t('qrcodes.filter.dynamic'), value: 'dynamic' },
];

const filteredQrCodes = computed(() => {
    let result = [...props.qrcodes];

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            (qr) => qr.name?.toLowerCase().includes(query) || qr.description?.toLowerCase().includes(query) || qr.slug?.toLowerCase().includes(query),
        );
    }

    if (filterStatus.value === 'active') result = result.filter((qr) => qr.is_active);
    else if (filterStatus.value === 'inactive') result = result.filter((qr) => !qr.is_active);
    else if (filterStatus.value === 'static') result = result.filter((qr) => qr.type === 'static');
    else if (filterStatus.value === 'dynamic') result = result.filter((qr) => qr.type === 'dynamic');

    result.sort((a, b) => {
        switch (sortBy.value) {
            case 'created_desc':
                return new Date(b.created_at).getTime() - new Date(a.created_at).getTime();
            case 'created_asc':
                return new Date(a.created_at).getTime() - new Date(b.created_at).getTime();
            case 'scans_desc':
                return (b.views_count || 0) - (a.views_count || 0);
            case 'scans_asc':
                return (a.views_count || 0) - (b.views_count || 0);
            case 'name_asc':
                return (a.name || '').localeCompare(b.name || '');
            default:
                return 0;
        }
    });

    return result;
});

// Icona per il formato del contenuto
const formatIcons: Record<string, Component> = {
    url: Link2,
    text: Type,
    email: Mail,
    phone: Phone,
    sms: MessageSquare,
    vcard: Contact,
};
const formatIcon = (format: string) => formatIcons[format] ?? QrCode;

const copiedId = ref<number | null>(null);
const copyToClipboard = async (qr: { id: number; slug: string }) => {
    await navigator.clipboard.writeText(`${props.shortUrl}/q/${qr.slug}`);
    copiedId.value = qr.id;
    setTimeout(() => {
        if (copiedId.value === qr.id) copiedId.value = null;
    }, 1500);
};

const destroy = (qr: { slug: string; name: string | null }) => {
    if (!window.confirm(t('qrcodes.confirmDelete', { name: qr.name || qr.slug }))) return;
    router.delete(route('qrcodes.destroy', qr.slug), { preserveScroll: true });
};

const handleCreateClick = () => {
    if (atQrLimit.value) {
        showLimitDialog.value = true;
    } else {
        router.visit(route('qrcodes.create'));
    }
};

const formatDate = (date: string) =>
    new Date(date).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });

const displayUrl = (url: string) => url?.replace(/^https?:\/\//, '');
</script>

<template>
    <Head :title="t('qrcodes.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex w-full flex-col gap-4 sm:gap-5 lg:p-2">
            <PageHeader :title="t('qrcodes.title')" :subtitle="t('qrcodes.subtitle')">
                <template #actions>
                    <button type="button" class="ml-btn-primary" @click="handleCreateClick">
                        <Plus :size="16" :stroke-width="2.5" /> {{ t('qrcodes.addNew') }}
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
                :search-placeholder="t('qrcodes.search')"
                :sort-options="sortOptions"
                :filter-options="filterOptions"
                :sort-label="t('qrcodes.sortBy')"
                :filter-label="t('qrcodes.filterBy')"
            />

            <!-- Elenco QR code -->
            <ul v-if="filteredQrCodes.length" class="flex flex-col gap-2.5">
                <li
                    v-for="qrcode in filteredQrCodes"
                    :key="qrcode.id"
                    class="ml-card grid gap-4 p-4 transition-colors hover:bg-surface-50/80 sm:px-5 lg:grid-cols-[minmax(0,1fr)_11rem_5.5rem_auto] xl:grid-cols-[minmax(0,1fr)_12rem_6.5rem_5.5rem_auto] lg:items-center dark:hover:bg-surface-800"
                >
                    <!-- Nome, tipo e link -->
                    <div class="flex min-w-0 items-start gap-3.5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                            <QrCode :size="18" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex min-w-0 items-center gap-2">
                                <Link :href="route('qrcodes.edit', qrcode.slug)" class="truncate font-semibold text-surface-900 hover:text-primary-600 dark:text-white dark:hover:text-primary-400">
                                    {{ qrcode.name || t('qrcodes.noName') }}
                                </Link>
                                <span
                                    class="ml-badge shrink-0"
                                    :class="qrcode.type === 'static'
                                        ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300'
                                        : 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-300'"
                                >
                                    {{ qrcode.type === 'static' ? t('qrcodes.static') : t('qrcodes.dynamic') }}
                                </span>
                            </div>
                            <p class="truncate text-xs text-surface-500 dark:text-surface-400">{{ qrcode.description || t('qrcodes.noDescription') }}</p>
                            <div class="mt-2 inline-flex max-w-full items-center gap-0.5 rounded-md border border-surface-200 bg-surface-50 py-0.5 pr-0.5 pl-2 dark:border-surface-700 dark:bg-surface-900/60">
                                <span class="truncate font-mono text-xs text-primary-600 dark:text-primary-400">{{ displayUrl(shortUrl) }}/q/{{ qrcode.slug }}</span>
                                <button type="button" class="ml-icon-btn h-6 w-6" :title="t('links.copyLink')" @click="copyToClipboard(qrcode)">
                                    <Check v-if="copiedId === qrcode.id" :size="13" class="text-emerald-600" />
                                    <Copy v-else :size="13" />
                                </button>
                                <a :href="`${shortUrl}/q/${qrcode.slug}`" target="_blank" rel="noopener" class="ml-icon-btn h-6 w-6" :title="t('links.openLink')">
                                    <ExternalLink :size="13" />
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Dati -->
                    <dl class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-surface-500 lg:flex-col lg:border-l lg:border-surface-100 lg:pl-5 dark:text-surface-400 dark:lg:border-surface-700">
                        <div class="flex items-center gap-1.5">
                            <dt class="sr-only">{{ t('qrcodes.format') }}</dt>
                            <component :is="formatIcon(qrcode.format)" :size="13" class="shrink-0" />
                            <dd class="font-medium uppercase">{{ qrcode.format }}</dd>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <dt class="sr-only">{{ t('qrcodes.createdAt') }}</dt>
                            <Calendar :size="13" class="shrink-0" />
                            <dd>{{ formatDate(qrcode.created_at) }}</dd>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <dt class="sr-only">{{ t('qrcodes.scans') }}</dt>
                            <ScanLine :size="13" class="shrink-0" />
                            <dd><strong class="font-semibold text-surface-800 dark:text-surface-100">{{ qrcode.views_count || 0 }}</strong> {{ t('qrcodes.scans').toLowerCase() }}</dd>
                        </div>
                        <div v-if="qrcode.views_with_consent_count > 0 || qrcode.views_anonymous_count > 0" class="flex items-center gap-3">
                            <span v-if="qrcode.views_with_consent_count > 0" class="inline-flex items-center gap-1" :title="t('links.consentViews', { count: qrcode.views_with_consent_count })">
                                <ShieldCheck :size="13" class="text-blue-500" /> {{ qrcode.views_with_consent_count }}
                            </span>
                            <span v-if="qrcode.views_anonymous_count > 0" class="inline-flex items-center gap-1" :title="t('links.anonymousViews', { count: qrcode.views_anonymous_count })">
                                <EyeOff :size="13" /> {{ qrcode.views_anonymous_count }}
                            </span>
                        </div>
                    </dl>

                    <!-- Andamento 7 giorni -->
                    <div class="hidden xl:block" :title="t('links.last7days')">
                        <Sparkline :values="sparklines?.[qrcode.id] ?? []" :color="METRIC_COLORS.qrcodes" :width="96" :height="32" />
                    </div>

                    <!-- Stato -->
                    <div class="flex lg:justify-center">
                        <span
                            class="ml-badge"
                            :class="qrcode.is_active
                                ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300'
                                : 'bg-surface-100 text-surface-600 dark:bg-surface-700/60 dark:text-surface-300'"
                        >
                            <span class="h-1.5 w-1.5 rounded-full" :class="qrcode.is_active ? 'bg-emerald-500' : 'bg-surface-400'"></span>
                            {{ qrcode.is_active ? t('qrcodes.active') : t('qrcodes.inactive') }}
                        </span>
                    </div>

                    <!-- Azioni -->
                    <div class="flex items-center gap-1.5 lg:justify-end">
                        <Link :href="route('qrcodes.show', qrcode.slug)" class="ml-icon-btn h-[34px] w-[34px] border border-surface-200 bg-white shadow-sm dark:border-surface-600 dark:bg-surface-800" :title="t('common.view')">
                            <Eye :size="15" />
                        </Link>
                        <Link :href="route('qrcodes.analytics', qrcode.slug)" class="ml-btn-secondary px-3 py-1.5 text-[13px]">
                            <BarChart3 :size="15" class="text-violet-500" /> <span class="lg:hidden xl:inline">Analytics</span>
                        </Link>
                        <Link :href="route('qrcodes.edit', qrcode.slug)" class="ml-btn-secondary px-3 py-1.5 text-[13px]">
                            <Pencil :size="14" /> <span class="lg:hidden xl:inline">{{ t('common.edit') }}</span>
                        </Link>
                        <button type="button" class="ml-icon-btn h-[34px] w-[34px] text-rose-500 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10" :title="t('common.delete')" @click="destroy(qrcode)">
                            <Trash2 :size="16" />
                        </button>
                    </div>
                </li>
            </ul>

            <div v-else-if="props.qrcodes.length > 0" class="ml-card flex flex-col items-center gap-2 py-14 text-center">
                <SearchX :size="28" class="text-surface-300 dark:text-surface-600" />
                <p class="font-medium text-surface-700 dark:text-surface-200">{{ t('common.noResults') }}</p>
                <p class="text-sm text-surface-500 dark:text-surface-400">{{ t('common.noResultsHint') }}</p>
            </div>

            <div v-else class="ml-card flex flex-col items-center gap-2 py-16 text-center">
                <span class="mb-2 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <QrCode :size="26" />
                </span>
                <p class="font-medium text-surface-800 dark:text-surface-100">{{ t('qrcodes.noQrCodes') }}</p>
                <p class="mb-3 text-sm text-surface-500 dark:text-surface-400">{{ t('qrcodes.createFirst') }}</p>
                <Link :href="route('qrcodes.create')" class="ml-btn-primary">
                    <Plus :size="16" :stroke-width="2.5" /> {{ t('qrcodes.addNew') }}
                </Link>
            </div>
        </div>

        <LimitReachedDialog v-model:visible="showLimitDialog" type="qrcodes" @show-plans="handleShowPlans" />

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
