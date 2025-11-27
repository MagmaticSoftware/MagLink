<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { 
    LucideEdit, 
    LucideTrash2, 
    LucideQrCode, 
    LucideScan,
    LucideCopy,
    LucideExternalLink,
    LucideCalendar,
    LucideDownload,
    LucideShare2,
    LucideClock,
    LucideActivity,
    LucideSettings,
    LucideEye,
    LucideEyeOff,
    LucideLink2,
    LucideFileText,
    LucideMail,
    LucidePhone,
    LucideMessageSquare,
    LucideUser
} from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const { t } = useI18n();

const props = defineProps<{
    qrcode: {
        id: number;
        slug: string;
        name: string;
        description: string;
        type: 'static' | 'dynamic';
        format: string;
        payload: any;
        options: any;
        scans: number;
        is_active: boolean;
        last_scanned_at: string | null;
        created_at: string;
        updated_at: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('qrcodes.title'),
        href: route('qrcodes.index'),
    },
    {
        title: props.qrcode.name || t('qrcodes.noName'),
        href: route('qrcodes.show', props.qrcode.slug),
    },
];

// Generate QR code URL
const qrCodeUrl = computed(() => {
    return `${window.location.origin}/qrcodes/${props.qrcode.slug}`;
});

// Copy to clipboard
const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
};

// Format date
const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString(undefined, { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Format relative time
const formatRelativeTime = (date: string) => {
    const now = new Date();
    const past = new Date(date);
    const diffInSeconds = Math.floor((now.getTime() - past.getTime()) / 1000);
    
    if (diffInSeconds < 60) return `${diffInSeconds} seconds ago`;
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
    if (diffInSeconds < 2592000) return `${Math.floor(diffInSeconds / 86400)} days ago`;
    return formatDate(date);
};

// Get format icon
const getFormatIcon = (format: string) => {
    switch (format) {
        case 'url': return LucideLink2;
        case 'text': return LucideFileText;
        case 'email': return LucideMail;
        case 'phone': return LucidePhone;
        case 'sms': return LucideMessageSquare;
        case 'vcard': return LucideUser;
        default: return LucideQrCode;
    }
};

// Get format color
const getFormatColor = (format: string) => {
    switch (format) {
        case 'url': return 'text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30';
        case 'text': return 'text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/30';
        case 'email': return 'text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/30';
        case 'phone': return 'text-orange-600 dark:text-orange-400 bg-orange-100 dark:bg-orange-900/30';
        case 'sms': return 'text-pink-600 dark:text-pink-400 bg-pink-100 dark:bg-pink-900/30';
        case 'vcard': return 'text-indigo-600 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-900/30';
        default: return 'text-surface-600 dark:text-surface-400 bg-surface-100 dark:bg-surface-800';
    }
};

// Download QR code (placeholder - would need backend implementation)
const downloadQrCode = () => {
    // This would trigger a download endpoint
    window.open(`/api/qrcodes/${props.qrcode.slug}/download`, '_blank');
};

// Share QR code
const shareQrCode = async () => {
    if (navigator.share) {
        try {
            await navigator.share({
                title: props.qrcode.name,
                text: props.qrcode.description,
                url: qrCodeUrl.value
            });
        } catch {
            copyToClipboard(qrCodeUrl.value);
        }
    } else {
        copyToClipboard(qrCodeUrl.value);
    }
};
</script>

<template>
    <Head :title="qrcode.name || t('qrcodes.noName')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-primary/20 to-primary/5 rounded-xl flex items-center justify-center">
                        <LucideQrCode :size="32" class="text-primary" />
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-3xl font-bold text-surface-900 dark:text-surface-50">
                                {{ qrcode.name || t('qrcodes.noName') }}
                            </h1>
                            <span
                                class="px-3 py-1 rounded-full text-sm font-medium"
                                :class="qrcode.is_active 
                                    ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' 
                                    : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'"
                            >
                                <LucideEye v-if="qrcode.is_active" :size="14" class="inline mr-1" />
                                <LucideEyeOff v-else :size="14" class="inline mr-1" />
                                {{ qrcode.is_active ? t('qrcodes.active') : t('qrcodes.inactive') }}
                            </span>
                            <span
                                class="px-3 py-1 rounded-full text-sm font-medium"
                                :class="qrcode.type === 'static' 
                                    ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' 
                                    : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400'"
                            >
                                {{ qrcode.type === 'static' ? t('qrcodes.static') : t('qrcodes.dynamic') }}
                            </span>
                        </div>
                        <p class="text-surface-600 dark:text-surface-400">
                            {{ qrcode.description || t('qrcodes.noDescription') }}
                        </p>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button
                        @click="shareQrCode"
                        class="px-4 py-2 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-400 rounded-lg flex items-center gap-2 transition-colors"
                    >
                        <LucideShare2 :size="16" />
                        <span class="hidden sm:inline">Share</span>
                    </button>
                    <button
                        @click="downloadQrCode"
                        class="px-4 py-2 bg-green-100 dark:bg-green-900/30 hover:bg-green-200 dark:hover:bg-green-900/50 text-green-700 dark:text-green-400 rounded-lg flex items-center gap-2 transition-colors"
                    >
                        <LucideDownload :size="16" />
                        <span class="hidden sm:inline">Download</span>
                    </button>
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

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - QR Code Preview -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800 sticky top-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-50 mb-4 flex items-center gap-2">
                            <LucideQrCode :size="20" />
                            QR Code Preview
                        </h2>
                        
                        <!-- QR Code Image Placeholder -->
                        <div class="aspect-square bg-surface-50 dark:bg-surface-800 rounded-lg border-2 border-dashed border-surface-300 dark:border-surface-700 flex items-center justify-center mb-4">
                            <div class="text-center p-8">
                                <LucideQrCode :size="120" class="mx-auto text-surface-400 dark:text-surface-600 mb-4" />
                                <p class="text-sm text-surface-600 dark:text-surface-400">
                                    QR Code will be generated here
                                </p>
                            </div>
                        </div>

                        <!-- QR Code URL -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-surface-700 dark:text-surface-300">
                                QR Code URL
                            </label>
                            <div class="flex items-center gap-2">
                                <code class="flex-1 px-3 py-2 bg-surface-100 dark:bg-surface-800 rounded text-sm text-primary font-mono truncate">
                                    {{ qrCodeUrl }}
                                </code>
                                <button
                                    @click="copyToClipboard(qrCodeUrl)"
                                    class="p-2 hover:bg-surface-100 dark:hover:bg-surface-800 rounded transition-colors"
                                    title="Copy URL"
                                >
                                    <LucideCopy :size="16" class="text-surface-500" />
                                </button>
                                <a
                                    :href="qrCodeUrl"
                                    target="_blank"
                                    class="p-2 hover:bg-surface-100 dark:hover:bg-surface-800 rounded transition-colors"
                                    title="Open URL"
                                >
                                    <LucideExternalLink :size="16" class="text-surface-500" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-surface-600 dark:text-surface-400">Total Scans</p>
                                    <p class="text-3xl font-bold text-surface-900 dark:text-surface-50 mt-2">{{ qrcode.scans }}</p>
                                </div>
                                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                    <LucideScan :size="24" class="text-purple-600 dark:text-purple-400" />
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-surface-600 dark:text-surface-400">Type</p>
                                    <p class="text-2xl font-bold text-surface-900 dark:text-surface-50 mt-2 capitalize">
                                        {{ qrcode.type }}
                                    </p>
                                </div>
                                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <LucideSettings :size="24" class="text-blue-600 dark:text-blue-400" />
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-surface-600 dark:text-surface-400">Last Scan</p>
                                    <p class="text-sm font-semibold text-surface-900 dark:text-surface-50 mt-2">
                                        {{ qrcode.last_scanned_at ? formatRelativeTime(qrcode.last_scanned_at) : 'Never' }}
                                    </p>
                                </div>
                                <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                                    <LucideActivity :size="24" class="text-orange-600 dark:text-orange-400" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Format & Payload -->
                    <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-50 mb-4 flex items-center gap-2">
                            <component :is="getFormatIcon(qrcode.format)" :size="20" />
                            Content Details
                        </h2>

                        <div class="space-y-4">
                            <!-- Format -->
                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    Format Type
                                </label>
                                <div class="flex items-center gap-2">
                                    <span 
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium"
                                        :class="getFormatColor(qrcode.format)"
                                    >
                                        <component :is="getFormatIcon(qrcode.format)" :size="16" />
                                        {{ qrcode.format.toUpperCase() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Payload -->
                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    Payload Content
                                </label>
                                <div class="bg-surface-50 dark:bg-surface-800 rounded-lg p-4 border border-surface-200 dark:border-surface-700">
                                    <pre class="text-sm text-surface-900 dark:text-surface-50 whitespace-pre-wrap break-words">{{ JSON.stringify(qrcode.payload, null, 2) }}</pre>
                                </div>
                            </div>

                            <!-- Options (if any) -->
                            <div v-if="qrcode.options && Object.keys(qrcode.options).length > 0">
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    Configuration Options
                                </label>
                                <div class="bg-surface-50 dark:bg-surface-800 rounded-lg p-4 border border-surface-200 dark:border-surface-700">
                                    <pre class="text-sm text-surface-900 dark:text-surface-50 whitespace-pre-wrap break-words">{{ JSON.stringify(qrcode.options, null, 2) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metadata -->
                    <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-50 mb-4 flex items-center gap-2">
                            <LucideClock :size="20" />
                            Metadata
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-surface-600 dark:text-surface-400 block mb-1">
                                    Created At
                                </label>
                                <div class="flex items-center gap-2 text-surface-900 dark:text-surface-50">
                                    <LucideCalendar :size="16" class="text-surface-500" />
                                    <span>{{ formatDate(qrcode.created_at) }}</span>
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-surface-600 dark:text-surface-400 block mb-1">
                                    Last Updated
                                </label>
                                <div class="flex items-center gap-2 text-surface-900 dark:text-surface-50">
                                    <LucideCalendar :size="16" class="text-surface-500" />
                                    <span>{{ formatDate(qrcode.updated_at) }}</span>
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-surface-600 dark:text-surface-400 block mb-1">
                                    Slug
                                </label>
                                <div class="flex items-center gap-2">
                                    <code class="px-2 py-1 bg-surface-100 dark:bg-surface-800 rounded text-sm text-primary font-mono">
                                        {{ qrcode.slug }}
                                    </code>
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-surface-600 dark:text-surface-400 block mb-1">
                                    ID
                                </label>
                                <div class="flex items-center gap-2">
                                    <code class="px-2 py-1 bg-surface-100 dark:bg-surface-800 rounded text-sm text-surface-900 dark:text-surface-50 font-mono">
                                        #{{ qrcode.id }}
                                    </code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Timeline (Placeholder for future enhancement) -->
                    <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-50 mb-4 flex items-center gap-2">
                            <LucideActivity :size="20" />
                            Recent Activity
                        </h2>
                        
                        <div class="text-center py-8 text-surface-500 dark:text-surface-400">
                            <LucideActivity :size="48" class="mx-auto mb-3 opacity-30" />
                            <p class="text-sm">No activity data available yet</p>
                            <p class="text-xs mt-1">Scan analytics will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
