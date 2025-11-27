<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { 
    LucideSave, 
    LucideQrCode,
    LucideArrowLeft,
    LucideSettings,
    LucideInfo,
    LucideLink2,
    LucideFileText,
    LucideMail,
    LucidePhone,
    LucideMessageSquare,
    LucideUser,
    LucideEye,
    LucideEyeOff,
    LucideClock,
    LucideScan
} from 'lucide-vue-next';
import Button from '@/components/volt/Button.vue';
import InputText from '@/components/volt/InputText.vue';
import Select from '@/components/volt/Select.vue';
import Textarea from '@/components/volt/Textarea.vue';
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';

const { t } = useI18n();

const props = defineProps<{
    qrcode: {
        id: number;
        user_id: number;
        company_id: number;
        tenant_id: string;
        slug: string;
        name: string | null;
        description: string | null;
        type: string;
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

const form = useForm({
    name: props.qrcode.name ?? '',
    slug: props.qrcode.slug ?? '',
    description: props.qrcode.description ?? '',
    type: props.qrcode.type,
    format: props.qrcode.format,
    payload: props.qrcode.payload ?? {},
    options: props.qrcode.options ?? {},
    is_active: props.qrcode.is_active,
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('qrcodes.title'),
        href: route('qrcodes.index'),
    },
    {
        title: props.qrcode.name || t('qrcodes.noName'),
        href: route('qrcodes.show', props.qrcode.slug),
    },
    {
        title: t('common.edit'),
        href: route('qrcodes.edit', props.qrcode.slug),
    },
];

// Type options
const typeOptions = [
    { label: t('qrcodes.static'), value: 'static' },
    { label: t('qrcodes.dynamic'), value: 'dynamic' }
];

// Format options with icons
const formatOptions = [
    { label: 'URL', value: 'url', icon: LucideLink2, description: 'Link to a website' },
    { label: 'Text', value: 'text', icon: LucideFileText, description: 'Plain text content' },
    { label: 'Email', value: 'email', icon: LucideMail, description: 'Email address' },
    { label: 'Phone', value: 'phone', icon: LucidePhone, description: 'Phone number' },
    { label: 'SMS', value: 'sms', icon: LucideMessageSquare, description: 'SMS message' },
    { label: 'vCard', value: 'vcard', icon: LucideUser, description: 'Contact card' }
];

// Current format details
const currentFormat = computed(() => 
    formatOptions.find(f => f.value === form.format) || formatOptions[0]
);

// Payload content helper
const payloadContent = ref(props.qrcode.payload?.content || '');

// Update payload when content changes
const updatePayload = () => {
    form.payload = { content: payloadContent.value };
};

// Format date
const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString(undefined, { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
};

const submitForm = () => {
    updatePayload();
    form.put(route('qrcodes.update', props.qrcode.slug), {
        onError: (errors: Record<string, any>) => {
            console.error('Form submission errors:', errors);
        },
    });
};
</script>

<template>
    <Head :title="`${t('common.edit')} - ${qrcode.name || t('qrcodes.noName')}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-primary/20 to-primary/5 rounded-xl flex items-center justify-center">
                        <LucideQrCode :size="32" class="text-primary" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-surface-900 dark:text-surface-50">{{ t('common.edit') }} QR Code</h1>
                        <p class="text-surface-600 dark:text-surface-400 mt-1">{{ qrcode.name || t('qrcodes.noName') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Button 
                        variant="outline" 
                        :href="route('qrcodes.show', qrcode.slug)"
                        as="a"
                    >
                        <LucideArrowLeft :size="16" class="mr-2" />
                        {{ t('common.back') }}
                    </Button>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitForm" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Main Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-50 mb-4 flex items-center gap-2">
                            <LucideInfo :size="20" />
                            Basic Information
                        </h2>
                        
                        <div class="space-y-4">
                            <!-- Name -->
                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    Name <span class="text-red-500">*</span>
                                </label>
                                <InputText
                                    v-model="form.name"
                                    placeholder="My QR Code"
                                    required
                                    class="w-full"
                                />
                                <p v-if="form.errors.name" class="text-sm text-red-600 dark:text-red-400 mt-1">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    Description
                                </label>
                                <Textarea
                                    v-model="form.description"
                                    placeholder="Add a description for this QR Code..."
                                    rows="3"
                                    class="w-full"
                                />
                                <p v-if="form.errors.description" class="text-sm text-red-600 dark:text-red-400 mt-1">
                                    {{ form.errors.description }}
                                </p>
                            </div>

                            <!-- Format Selection (disabled for static) -->
                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-3">
                                    Format Type <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    <button
                                        v-for="format in formatOptions"
                                        :key="format.value"
                                        type="button"
                                        @click="qrcode.type === 'dynamic' ? form.format = format.value : null"
                                        :disabled="qrcode.type === 'static'"
                                        class="p-4 rounded-lg border-2 transition-all text-left"
                                        :class="[
                                            form.format === format.value
                                                ? 'border-primary bg-primary/5 dark:bg-primary/10'
                                                : 'border-surface-200 dark:border-surface-700 hover:border-surface-300 dark:hover:border-surface-600',
                                            qrcode.type === 'static' ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                                        ]"
                                    >
                                        <component :is="format.icon" :size="24" 
                                            class="mb-2"
                                            :class="form.format === format.value ? 'text-primary' : 'text-surface-500'" 
                                        />
                                        <div class="font-medium text-surface-900 dark:text-surface-50 text-sm">
                                            {{ format.label }}
                                        </div>
                                        <div class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                                            {{ format.description }}
                                        </div>
                                    </button>
                                </div>
                                <p v-if="qrcode.type === 'static'" class="text-xs text-orange-600 dark:text-orange-400 mt-2">
                                    ⚠️ Format cannot be changed for static QR codes
                                </p>
                            </div>

                            <!-- Content Input -->
                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    Content <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <component :is="currentFormat.icon" 
                                        :size="20" 
                                        class="absolute left-3 top-3 text-surface-400" 
                                    />
                                    <Textarea
                                        v-model="payloadContent"
                                        :placeholder="`Enter ${currentFormat.label.toLowerCase()} content...`"
                                        rows="4"
                                        class="w-full pl-10"
                                        required
                                        :disabled="qrcode.type === 'static'"
                                    />
                                </div>
                                <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                                    {{ currentFormat.description }}
                                </p>
                                <p v-if="qrcode.type === 'static'" class="text-xs text-orange-600 dark:text-orange-400 mt-1">
                                    ⚠️ Content cannot be changed for static QR codes
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                    <p class="text-sm font-medium text-surface-600 dark:text-surface-400">Created</p>
                                    <p class="text-sm font-semibold text-surface-900 dark:text-surface-50 mt-2">
                                        {{ formatDate(qrcode.created_at) }}
                                    </p>
                                </div>
                                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <LucideClock :size="24" class="text-blue-600 dark:text-blue-400" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Settings -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Settings -->
                    <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-50 mb-4 flex items-center gap-2">
                            <LucideSettings :size="20" />
                            Settings
                        </h2>
                        
                        <div class="space-y-4">
                            <!-- Type (Read-only) -->
                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    Type
                                </label>
                                <div class="px-4 py-3 bg-surface-50 dark:bg-surface-800 rounded-lg text-surface-900 dark:text-surface-50 font-medium capitalize">
                                    {{ qrcode.type }}
                                </div>
                                <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                                    Type cannot be changed after creation
                                </p>
                            </div>

                            <!-- Slug (Read-only) -->
                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    Slug
                                </label>
                                <div class="px-4 py-3 bg-surface-50 dark:bg-surface-800 rounded-lg">
                                    <code class="text-sm text-primary font-mono">{{ qrcode.slug }}</code>
                                </div>
                            </div>

                            <!-- Active Status -->
                            <div class="flex items-center justify-between p-4 bg-surface-50 dark:bg-surface-800 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        <LucideEye v-if="form.is_active" :size="20" class="text-green-600 dark:text-green-400" />
                                        <LucideEyeOff v-else :size="20" class="text-surface-400" />
                                    </div>
                                    <div>
                                        <div class="font-medium text-surface-900 dark:text-surface-50 text-sm">
                                            {{ form.is_active ? t('qrcodes.active') : t('qrcodes.inactive') }}
                                        </div>
                                        <div class="text-xs text-surface-500 dark:text-surface-400">
                                            QR Code status
                                        </div>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        v-model="form.is_active" 
                                        class="sr-only peer"
                                    />
                                    <div class="w-11 h-6 bg-surface-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 dark:peer-focus:ring-primary/40 rounded-full peer dark:bg-surface-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-surface-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-surface-600 peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Preview (Placeholder) -->
                    <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-50 mb-4">
                            Current QR Code
                        </h2>
                        <div class="aspect-square bg-surface-50 dark:bg-surface-800 rounded-lg border-2 border-dashed border-surface-300 dark:border-surface-700 flex items-center justify-center">
                            <div class="text-center p-4">
                                <LucideQrCode :size="80" class="mx-auto text-surface-300 dark:text-surface-600 mb-2" />
                                <p class="text-xs text-surface-500 dark:text-surface-400">
                                    QR Code preview
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-2">
                        <Button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full justify-center"
                        >
                            <LucideSave :size="16" class="mr-2" />
                            {{ form.processing ? 'Updating...' : t('common.save') }}
                        </Button>
                        <Button 
                            variant="outline"
                            type="button"
                            :href="route('qrcodes.show', qrcode.slug)"
                            as="a"
                            class="w-full justify-center"
                        >
                            {{ t('common.cancel') }}
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
