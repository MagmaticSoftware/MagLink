<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { 
    LucideEdit, 
    LucideTrash2, 
    LucidePlus, 
    LucideQrCode, 
    LucideEye,
    LucideSearch,
    LucideFilter,
    LucideArrowUpDown,
    LucideScan,
    LucideTrendingUp,
    LucideCopy,
    LucideExternalLink,
    LucideCalendar,
    LucideBarChart3
} from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import InputText from '@/components/volt/InputText.vue';
import Select from '@/components/volt/Select.vue';
import LimitReachedDialog from '@/components/LimitReachedDialog.vue';
import PlanSelectionModal from '@/components/PlanSelectionModal.vue';
import type { PageProps } from '@/types/inertia';

const { t } = useI18n();
const page = usePage<PageProps>();

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
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('qrcodes.title'),
        href: route('qrcodes.index'),
    },
];

// Handle create click - check limits
const handleCreateClick = () => {
    const page = usePage();
    const limits = page.props.billing?.limits;
    const currentCount = computedStats.value.total;
    
    if (limits && currentCount >= limits.qrcodes) {
        showLimitDialog.value = true;
    } else {
        router.visit(route('qrcodes.create'));
    }
};

// Search and filter state
const searchQuery = ref('');
const sortBy = ref('created_desc');
const filterStatus = ref('all');

// Compute stats
const computedStats = computed(() => {
    if (props.stats) return props.stats;
    
    const totalScans = props.qrcodes.reduce((sum, qr) => sum + (qr.views_count || 0), 0);
    const dynamicCount = props.qrcodes.filter(q => q.type === 'dynamic').length;
    
    return {
        total: props.qrcodes.length,
        active: props.qrcodes.filter(q => q.is_active).length,
        dynamic: dynamicCount,
        totalScans,
        avgScansPerCode: props.qrcodes.length > 0 ? (totalScans / props.qrcodes.length).toFixed(1) : '0.0'
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
    { label: t('qrcodes.sort.newest'), value: 'created_desc' },
    { label: t('qrcodes.sort.oldest'), value: 'created_asc' },
    { label: t('qrcodes.sort.mostScans'), value: 'scans_desc' },
    { label: t('qrcodes.sort.leastScans'), value: 'scans_asc' },
    { label: t('qrcodes.sort.alphabetical'), value: 'name_asc' }
];

// Filter options
const filterOptions = [
    { label: t('qrcodes.filter.all'), value: 'all' },
    { label: t('qrcodes.filter.active'), value: 'active' },
    { label: t('qrcodes.filter.inactive'), value: 'inactive' },
    { label: t('qrcodes.filter.static'), value: 'static' },
    { label: t('qrcodes.filter.dynamic'), value: 'dynamic' }
];

// Filtered and sorted QR codes
const filteredQrCodes = computed(() => {
    let result = [...props.qrcodes];
    
    // Apply search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(qr => 
            qr.name?.toLowerCase().includes(query) ||
            qr.description?.toLowerCase().includes(query) ||
            qr.slug?.toLowerCase().includes(query)
        );
    }
    
    // Apply status filter
    if (filterStatus.value !== 'all') {
        if (filterStatus.value === 'active') {
            result = result.filter(qr => qr.is_active);
        } else if (filterStatus.value === 'inactive') {
            result = result.filter(qr => !qr.is_active);
        } else if (filterStatus.value === 'static') {
            result = result.filter(qr => qr.type === 'static');
        } else if (filterStatus.value === 'dynamic') {
            result = result.filter(qr => qr.type === 'dynamic');
        }
    }
    
    // Apply sorting
    result.sort((a, b) => {
        switch (sortBy.value) {
            case 'created_desc':
                return new Date(b.created_at).getTime() - new Date(a.created_at).getTime();
            case 'created_asc':
                return new Date(a.created_at).getTime() - new Date(b.created_at).getTime();
            case 'scans_desc':
                return (b.scans || 0) - (a.scans || 0);
            case 'scans_asc':
                return (a.scans || 0) - (b.scans || 0);
            case 'name_asc':
                return (a.name || '').localeCompare(b.name || '');
            default:
                return 0;
        }
    });
    
    return result;
});

// Copy QR code link to clipboard
const copyToClipboard = (slug: string) => {
    const url = `${props.shortUrl}/${slug}`;
    navigator.clipboard.writeText(url);
};

// Format date
const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString(undefined, { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
};

// Get format icon
const getFormatIcon = (format: string) => {
    const icons: Record<string, string> = {
        url: '🔗',
        text: '📝',
        email: '📧',
        phone: '📞',
        sms: '💬',
        vcard: '👤'
    };
    return icons[format] || '📱';
};
</script>

<template>
    <Head title="QR Codes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-surface-900 dark:text-surface-50">{{ t('qrcodes.title') }}</h1>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">{{ t('qrcodes.subtitle') }}</p>
                </div>
                <div>
                    <button
                        @click="handleCreateClick"
                        class="button-primary flex items-center gap-2"
                    >
                        <LucidePlus class="w-4 h-4" /> {{ t('qrcodes.addNew') }}
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total QR Codes -->
                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-surface-600 dark:text-surface-400">{{ t('qrcodes.stats.totalQrCodes') }}</p>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <LucideQrCode :size="24" class="text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <p 
                        :class="['text-3xl font-bold mt-2', getLimitClasses(getLimitStatus(computedStats.total, limits.qrcodes || -1))]"
                    >
                        {{ computedStats.total }}{{ limits.qrcodes > 0 ? ` / ${limits.qrcodes}` : '' }}
                    </p>
                    <p 
                        v-if="getLimitStatus(computedStats.total, limits.qrcodes || -1) === 'exceeded'"
                        class="text-xs text-red-700 dark:text-red-400 mt-1"
                    >
                        Limite raggiunto per il piano free
                    </p>
                </div>

                <!-- Active QR Codes -->
                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-surface-600 dark:text-surface-400">{{ t('qrcodes.stats.activeQrCodes') }}</p>
                        <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <LucideTrendingUp :size="24" class="text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-surface-900 dark:text-surface-50 mt-2">{{ computedStats.active }}</p>
                </div>

                <!-- Dynamic QR Codes -->
                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-surface-600 dark:text-surface-400">QR Code dinamici</p>
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <LucideQrCode :size="24" class="text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                    <p 
                        :class="['text-3xl font-bold mt-2', getLimitClasses(getLimitStatus(computedStats.dynamic, limits.qrcodes_dynamic || -1))]"
                    >
                        {{ computedStats.dynamic }}{{ limits.qrcodes_dynamic > 0 ? ` / ${limits.qrcodes_dynamic}` : '' }}
                    </p>
                    <p 
                        v-if="getLimitStatus(computedStats.dynamic, limits.qrcodes_dynamic || -1) === 'exceeded'"
                        class="text-xs text-red-700 dark:text-red-400 mt-1"
                    >
                        Limite raggiunto per il piano free
                    </p>
                </div>

                <!-- Total Scans -->
                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-surface-600 dark:text-surface-400">{{ t('qrcodes.stats.totalScans') }}</p>
                        <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                            <LucideScan :size="24" class="text-orange-600 dark:text-orange-400" />
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-surface-900 dark:text-surface-50 mt-2">{{ computedStats.totalScans }}</p>
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
                                :placeholder="t('qrcodes.search')"
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
                                :placeholder="t('qrcodes.sortBy')"
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
                                :placeholder="t('qrcodes.filterBy')"
                                class="w-full"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- QR Codes Grid -->
            <div class="grid grid-cols-1 gap-4">
                <div
                    v-for="qrcode in filteredQrCodes"
                    :key="qrcode.id"
                    class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800 hover:shadow-md transition-all hover:scale-[1.01]"
                >
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <!-- QR Code Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-primary/20 to-primary/5 rounded-lg flex items-center justify-center">
                                    <LucideQrCode :size="24" class="text-primary" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-lg font-semibold text-surface-900 dark:text-surface-50">
                                            {{ qrcode.name || t('qrcodes.noName') }}
                                        </h3>
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-medium"
                                            :class="qrcode.type === 'static' 
                                                ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' 
                                                : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400'"
                                        >
                                            {{ qrcode.type === 'static' ? t('qrcodes.static') : t('qrcodes.dynamic') }}
                                        </span>
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-medium"
                                            :class="qrcode.is_active 
                                                ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' 
                                                : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'"
                                        >
                                            {{ qrcode.is_active ? t('qrcodes.active') : t('qrcodes.inactive') }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-surface-600 dark:text-surface-400 mb-3">
                                        {{ qrcode.description || t('qrcodes.noDescription') }}
                                    </p>
                                    
                                    <!-- Format and URL -->
                                    <div class="flex items-center gap-2 mb-3 text-sm">
                                        <span class="text-2xl">{{ getFormatIcon(qrcode.format) }}</span>
                                        <span class="font-medium text-surface-700 dark:text-surface-300">{{ qrcode.format.toUpperCase() }}</span>
                                        <span class="text-surface-400">·</span>
                                        <code class="px-2 py-1 bg-surface-100 dark:bg-surface-800 rounded text-primary font-mono text-xs">
                                            {{ shortUrl }}/q/{{ qrcode.slug }}
                                        </code>
                                        <button
                                            @click="copyToClipboard(`q/${qrcode.slug}`)"
                                            class="p-1 hover:bg-surface-100 dark:hover:bg-surface-800 rounded transition-colors"
                                            :title="t('qrcodes.copyLink')"
                                        >
                                            <LucideCopy :size="14" class="text-surface-500" />
                                        </button>
                                        <a
                                            :href="`${shortUrl}/q/${qrcode.slug}`"
                                            target="_blank"
                                            class="p-1 hover:bg-surface-100 dark:hover:bg-surface-800 rounded transition-colors"
                                            :title="t('qrcodes.openLink')"
                                        >
                                            <LucideExternalLink :size="14" class="text-surface-500" />
                                        </a>
                                    </div>

                                    <!-- Meta Info -->
                                    <div class="flex items-center flex-wrap gap-3 text-xs text-surface-500 dark:text-surface-400">
                                        <span class="flex items-center gap-1">
                                            <LucideCalendar :size="14" />
                                            {{ t('qrcodes.createdAt') }}: {{ formatDate(qrcode.created_at) }}
                                        </span>
                                        
                                        <!-- Views Statistics -->
                                        <span class="flex items-center gap-1 font-medium text-primary">
                                            <LucideEye :size="14" />
                                            {{ qrcode.views_count || 0 }} views
                                        </span>
                                        <span 
                                            v-if="qrcode.views_with_consent_count > 0"
                                            class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400"
                                        >
                                            🔒 {{ qrcode.views_with_consent_count }} con consenso
                                        </span>
                                        <span 
                                            v-if="qrcode.views_anonymous_count > 0"
                                            class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900/30 text-gray-700 dark:text-gray-400"
                                        >
                                            🕶️ {{ qrcode.views_anonymous_count }} anonimi
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <Link
                                :href="route('qrcodes.show', qrcode.slug)"
                                class="px-4 py-2 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-400 rounded-lg flex items-center gap-2 transition-colors"
                            >
                                <LucideEye :size="16" />
                                <span class="hidden sm:inline">{{ t('common.view') }}</span>
                            </Link>
                            <Link
                                :href="route('qrcodes.analytics', qrcode.slug)"
                                class="px-4 py-2 bg-purple-100 dark:bg-purple-900/30 hover:bg-purple-200 dark:hover:bg-purple-900/50 text-purple-700 dark:text-purple-400 rounded-lg flex items-center gap-2 transition-colors"
                            >
                                <LucideBarChart3 :size="16" />
                                <span class="hidden sm:inline">Analytics</span>
                            </Link>
                            <Link
                                :href="route('qrcodes.edit', qrcode.slug)"
                                class="px-4 py-2 bg-surface-100 dark:bg-surface-800 hover:bg-surface-200 dark:hover:bg-surface-700 text-surface-700 dark:text-surface-300 rounded-lg flex items-center gap-2 transition-colors"
                            >
                                <LucideEdit :size="16" />
                                <span class="hidden sm:inline">{{ t('common.edit') }}</span>
                            </Link>
                            <Link
                                :href="route('qrcodes.destroy', qrcode.slug)"
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
                    v-if="filteredQrCodes.length === 0 && props.qrcodes.length > 0"
                    class="text-center py-12"
                >
                    <LucideSearch class="w-16 h-16 mx-auto mb-4 text-surface-300 dark:text-surface-700" />
                    <p class="text-lg font-medium text-surface-700 dark:text-surface-300">{{ t('common.noResults') }}</p>
                    <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Try adjusting your search or filters</p>
                </div>

                <div
                    v-if="props.qrcodes.length === 0"
                    class="text-center py-16"
                >
                    <div class="inline-flex p-4 bg-surface-100 dark:bg-surface-800 rounded-full mb-4">
                        <LucideQrCode class="w-16 h-16 text-surface-400 dark:text-surface-600" />
                    </div>
                    <p class="text-lg font-medium text-surface-700 dark:text-surface-300">{{ t('qrcodes.noQrCodes') }}</p>
                    <p class="text-sm text-surface-500 dark:text-surface-400 mt-1 mb-4">{{ t('qrcodes.createFirst') }}</p>
                    <Link
                        :href="route('qrcodes.create')"
                        class="button-primary inline-flex items-center gap-2"
                    >
                        <LucidePlus class="w-4 h-4" /> {{ t('qrcodes.addNew') }}
                    </Link>
                </div>
            </div>
        </div>

        <!-- Limit Reached Dialog -->
        <LimitReachedDialog
            v-model:visible="showLimitDialog"
            type="qrcodes"
            @show-plans="handleShowPlans"
        />

        <PlanSelectionModal
            v-model:visible="showPlanModal"
            :plans="page.props.plans || {}"
            :is-new-user="page.props.billing?.isNewUser || false"
            :has-active-trial="page.props.billing?.hasActiveTrial || false"
            :can-start-trial="page.props.billing?.canStartTrial || false"
            :is-subscribed="page.props.billing?.isSubscribed || false"
        />
    </AppLayout>
</template>
