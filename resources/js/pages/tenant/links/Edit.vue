<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { 
    LucideSave,
    LucideLink,
    LucideArrowLeft,
    LucideSettings,
    LucideInfo,
    LucideExternalLink,
    LucideEye,
    LucideEyeOff,
    LucideGlobe,
    LucideMousePointerClick,
    LucideClock
} from 'lucide-vue-next';
import Button from '@/components/volt/Button.vue';
import InputText from '@/components/volt/InputText.vue';
import Textarea from '@/components/volt/Textarea.vue';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const { t } = useI18n();

const baseUrl = computed(() => window.location.origin);

const props = defineProps<{
    link: { 
        id: number;
        user_id: number; 
        company_id: number; 
        tenant_id: number; 
        slug: string; 
        title: string;
        description: string; 
        url: string; 
        is_active: boolean;
        type: string;
        clicks?: number;
        created_at: string;
        updated_at: string;
    };
}>();

const form = useForm({
    url: props.link.url,
    title: props.link.title,
    description: props.link.description,
    is_active: props.link.is_active,
    type: props.link.type,
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('links.title'),
        href: route('links.index'),
    },
    {
        title: props.link.title,
        href: route('links.show', props.link.id),
    },
    {
        title: t('common.edit'),
        href: route('links.edit', props.link.id),
    },
];

// Format date
const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString(undefined, { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
};

const submitForm = () => {
    form.put(route('links.update', props.link.id), {
        onError: (errors: Record<string, any>) => {
            console.error('Form submission errors:', errors);
        },
    });
};
</script>

<template>
    <Head :title="`${t('common.edit')} - ${link.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-primary/20 to-primary/5 rounded-xl flex items-center justify-center">
                        <LucideLink :size="32" class="text-primary" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-surface-900 dark:text-surface-50">{{ t('common.edit') }} Link</h1>
                        <p class="text-surface-600 dark:text-surface-400 mt-1">{{ link.title }}</p>
                    </div>
                </div>
                <Button 
                    variant="outline" 
                    :href="route('links.index')"
                    as="a"
                >
                    <LucideArrowLeft :size="16" class="mr-2" />
                    {{ t('common.back') }}
                </Button>
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
                            <!-- Title -->
                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    Title <span class="text-red-500">*</span>
                                </label>
                                <InputText
                                    v-model="form.title"
                                    placeholder="My Awesome Link"
                                    required
                                    class="w-full"
                                />
                                <p v-if="form.errors.title" class="text-sm text-red-600 dark:text-red-400 mt-1">
                                    {{ form.errors.title }}
                                </p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    Description
                                </label>
                                <Textarea
                                    v-model="form.description"
                                    placeholder="Add a description for this link..."
                                    rows="3"
                                    class="w-full"
                                />
                                <p v-if="form.errors.description" class="text-sm text-red-600 dark:text-red-400 mt-1">
                                    {{ form.errors.description }}
                                </p>
                            </div>

                            <!-- Destination URL -->
                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    Destination URL <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <LucideGlobe :size="20" class="absolute left-3 top-3 text-surface-400" />
                                    <InputText
                                        v-model="form.url"
                                        type="url"
                                        placeholder="https://example.com"
                                        required
                                        class="w-full pl-10"
                                    />
                                </div>
                                <div class="flex items-center gap-2 mt-2">
                                    <a 
                                        v-if="form.url" 
                                        :href="form.url" 
                                        target="_blank" 
                                        class="text-sm text-primary hover:underline flex items-center gap-1"
                                    >
                                        Preview URL
                                        <LucideExternalLink :size="14" />
                                    </a>
                                </div>
                                <p v-if="form.errors.url" class="text-sm text-red-600 dark:text-red-400 mt-1">
                                    {{ form.errors.url }}
                                </p>
                            </div>

                            <!-- Short Link Preview -->
                            <div class="bg-surface-50 dark:bg-surface-800 rounded-lg p-4 border border-surface-200 dark:border-surface-700">
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    Short Link
                                </label>
                                <div class="flex items-center gap-2">
                                    <code class="flex-1 px-3 py-2 bg-white dark:bg-surface-900 rounded text-sm text-primary font-mono border border-surface-200 dark:border-surface-700">
                                        {{ baseUrl }}/{{ link.slug }}
                                    </code>
                                </div>
                                <p class="text-xs text-surface-500 dark:text-surface-400 mt-2">
                                    Your shortened link URL
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-surface-600 dark:text-surface-400">Total Clicks</p>
                                    <p class="text-3xl font-bold text-surface-900 dark:text-surface-50 mt-2">{{ link.clicks || 0 }}</p>
                                </div>
                                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                    <LucideMousePointerClick :size="24" class="text-purple-600 dark:text-purple-400" />
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-surface-600 dark:text-surface-400">Created</p>
                                    <p class="text-sm font-semibold text-surface-900 dark:text-surface-50 mt-2">
                                        {{ formatDate(link.created_at) }}
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
                            <!-- Slug (Read-only) -->
                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    Slug
                                </label>
                                <div class="px-4 py-3 bg-surface-50 dark:bg-surface-800 rounded-lg">
                                    <code class="text-sm text-primary font-mono">{{ link.slug }}</code>
                                </div>
                                <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                                    Unique identifier
                                </p>
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
                                            {{ form.is_active ? 'Active' : 'Inactive' }}
                                        </div>
                                        <div class="text-xs text-surface-500 dark:text-surface-400">
                                            Link status
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

                    <!-- Link Preview Card -->
                    <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-50 mb-4">
                            Preview
                        </h2>
                        <div class="space-y-3">
                            <div class="p-4 bg-surface-50 dark:bg-surface-800 rounded-lg border border-surface-200 dark:border-surface-700">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-primary/20 to-primary/5 rounded-lg flex items-center justify-center">
                                        <LucideLink :size="20" class="text-primary" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium text-surface-900 dark:text-surface-50 truncate">
                                            {{ form.title }}
                                        </div>
                                        <div class="text-sm text-surface-600 dark:text-surface-400 truncate mt-1">
                                            {{ form.description || 'No description' }}
                                        </div>
                                        <div class="text-xs text-primary mt-2 truncate">
                                            {{ form.url }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-surface-500 dark:text-surface-400 text-center">
                                How your link will appear
                            </p>
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
                            :href="route('links.index')"
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
