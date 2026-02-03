<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { 
    LucideSave, 
    LucideFileText,
    LucideLayout,
    LucideCalendar,
    LucideToggleLeft,
    LucideToggleRight,
    LucideLink
} from 'lucide-vue-next';
import InputText from '@/components/volt/InputText.vue';
import Textarea from '@/components/volt/Textarea.vue';
import Button from '@/components/volt/Button.vue';
import ToggleSwitch from '@/components/volt/ToggleSwitch.vue';
import LimitWarning from '@/components/LimitWarning.vue';
import { type PageProps } from '@/types/inertia';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const page = usePage<PageProps>();

const props = defineProps<{
    slug?: string;
    pageCount: number;
}>();

const form = useForm({
    user_id: page.props.auth.user.id,
    company_id: page.props.auth.user.company?.id ?? 1,
    tenant_id: page.props.auth.user.tenant_id,
    slug: props.slug || '',
    title: '',
    description: '',
    style: {},
    settings: {},
    is_active: true,
    published_at: '',
});

// Theme colors palette
const themeColors = [
    { name: 'Blue', value: 'blue', class: 'bg-blue-500' },
    { name: 'Indigo', value: 'indigo', class: 'bg-indigo-500' },
    { name: 'Purple', value: 'purple', class: 'bg-purple-500' },
    { name: 'Pink', value: 'pink', class: 'bg-pink-500' },
    { name: 'Red', value: 'red', class: 'bg-red-500' },
    { name: 'Orange', value: 'orange', class: 'bg-orange-500' },
    { name: 'Amber', value: 'amber', class: 'bg-amber-500' },
    { name: 'Yellow', value: 'yellow', class: 'bg-yellow-500' },
    { name: 'Lime', value: 'lime', class: 'bg-lime-500' },
    { name: 'Green', value: 'green', class: 'bg-green-500' },
    { name: 'Emerald', value: 'emerald', class: 'bg-emerald-500' },
    { name: 'Teal', value: 'teal', class: 'bg-teal-500' },
    { name: 'Cyan', value: 'cyan', class: 'bg-cyan-500' },
    { name: 'Sky', value: 'sky', class: 'bg-sky-500' },
];

const selectedColor = ref('blue');

const selectColor = (color: string) => {
    selectedColor.value = color;
    form.style = { ...form.style, primaryColor: color };
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('pages.title'),
        href: route('pages.index'),
    },
    {
        title: t('pages.create'),
        href: route('pages.create'),
    },
];

const submitForm = () => {
    form.post(route('pages.store'), {
        onSuccess: () => {
            form.reset();
        },
        onError: (errors: Record<string, any>) => {
            console.error('Form submission errors:', errors);
        },
    });
};

// Generate slug from title
const generateSlug = () => {
    if (form.title && !form.slug) {
        form.slug = form.title
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
    }
};

// Helper per formattare la data in formato YYYY-MM-DD
const formatDate = (date: Date): string => {
    return date.toISOString().split('T')[0];
};

// Watch per gestire la data di pubblicazione quando si attiva/disattiva la pagina
watch(() => form.is_active, (isActive, oldValue) => {
    if (isActive && !form.published_at) {
        // Se viene attivata e non c'è data, imposta la data odierna
        form.published_at = formatDate(new Date());
    } else if (!isActive && oldValue === true) {
        // Se viene disattivata, cancella la data di pubblicazione
        form.published_at = '';
    }
});
</script>

<template>

    <Head :title="t('pages.create')">
        <link rel="icon" href="/favicon.ico" />
    </Head>

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Limit Warning -->
            <LimitWarning type="pages" :currentCount="pageCount" />
            
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-surface-900 dark:text-surface-50">{{ t('pages.create') }}</h1>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">{{ t('pages.createDescription') }}</p>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form -->
                <div class="lg:col-span-2">
                    <div
                        class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-3 bg-primary/10 rounded-lg">
                                <LucideFileText :size="24" class="text-primary" />
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-surface-900 dark:text-surface-50">
                                    {{ t('pages.pageInfo') }}
                                </h3>
                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                    {{ t('pages.pageInfoDescription') }}
                                </p>
                            </div>
                        </div>

                        <form @submit.prevent="submitForm" class="space-y-6">
                            <!-- Title -->
                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    {{ t('pages.form.title') }} *
                                </label>
                                <InputText v-model="form.title" required class="w-full"
                                    :placeholder="t('pages.form.titlePlaceholder')" @blur="generateSlug" />
                                <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}
                                </p>
                            </div>

                            <!-- Slug -->
                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    <div class="flex items-center gap-2">
                                        <LucideLink :size="16" />
                                        Slug (URL)
                                    </div>
                                </label>
                                <InputText v-model="form.slug" class="w-full font-mono text-sm"
                                    :placeholder="t('pages.placeholders.slug')" />
                                <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                                    {{ t('pages.form.slugHint') }}: /{{ form.slug || 'your-page-url' }}
                                </p>
                                <p v-if="form.errors.slug" class="text-red-500 text-sm mt-1">{{ form.errors.slug }}</p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    {{ t('pages.form.description') }}
                                </label>
                                <Textarea v-model="form.description" class="w-full" rows="4"
                                    :placeholder="t('pages.form.descriptionPlaceholder')" />
                            </div>

                            <!-- Submit Button (Desktop) -->
                            <div class="hidden lg:block pt-4 border-t border-surface-200 dark:border-surface-800">
                                <Button type="submit" :disabled="form.processing"
                                    class="w-full flex items-center justify-center gap-2">
                                    <LucideSave :size="16" />
                                    {{ form.processing ? t('common.saving') : t('pages.createPage') }}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Settings Sidebar -->
                <div class="lg:col-span-1">
                    <div
                        class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800 sticky top-6">
                        <h3 class="text-lg font-semibold text-surface-900 dark:text-surface-50 mb-4">
                            {{ t('pages.settings') }}
                        </h3>

                        <div class="space-y-6">
                            <!-- Published At -->
                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    <div class="flex items-center gap-2">
                                        <LucideCalendar :size="16" />
                                        {{ t('pages.form.published') }}
                                    </div>
                                </label>
                                <input v-model="form.published_at" type="date"
                                    class="w-full rounded-md border border-surface-300 dark:border-surface-700 bg-surface-0 dark:bg-surface-950 text-surface-700 dark:text-surface-0 px-3 py-2 text-sm focus:border-primary focus:outline-none" />
                                <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                                    {{ t('pages.form.publishedHint') }}
                                </p>
                            </div>

                            <!-- Active Toggle -->
                            <div
                                class="flex items-center justify-between p-4 bg-surface-50 dark:bg-surface-800 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <component :is="form.is_active ? LucideToggleRight : LucideToggleLeft" :size="20"
                                        :class="form.is_active ? 'text-green-500' : 'text-surface-400'" />
                                    <div>
                                        <p class="text-sm font-medium text-surface-700 dark:text-surface-300">
                                            {{ t('pages.form.active') }}
                                        </p>
                                        <p class="text-xs text-surface-500 dark:text-surface-400">
                                            {{ form.is_active ? t('pages.form.activeHint') :
                                            t('pages.form.inactiveHint') }}
                                        </p>
                                    </div>
                                </div>
                                <ToggleSwitch v-model="form.is_active" />
                            </div>
                        </div>

                        <!-- Theme Color -->
                        <div class="pt-4 border-t border-surface-200 dark:border-surface-800">
                            <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-3">
                                <div class="flex items-center gap-2">
                                    <LucideLayout :size="16" />
                                    {{ t('pages.form.themeColor') }}
                                </div>
                            </label>
                            <div class="grid grid-cols-7 gap-2">
                                <button v-for="color in themeColors" :key="color.value" type="button"
                                    @click="selectColor(color.value)" :title="color.name" :class="[
                                            'w-8 h-8 rounded-lg transition-all duration-200',
                                            color.class,
                                            'hover:scale-110 active:scale-95',
                                            'border-2',
                                            selectedColor === color.value
                                                ? 'border-surface-900 dark:border-surface-100 ring-2 ring-offset-2 ring-surface-400 dark:ring-surface-600 scale-110'
                                                : 'border-transparent hover:border-surface-300 dark:hover:border-surface-600'
                                        ]" />
                            </div>
                        </div>

                        <!-- Submit Button (Mobile) -->
                        <div class="lg:hidden pt-4 border-t border-surface-200 dark:border-surface-800">
                            <Button type="button" @click="submitForm" :disabled="form.processing"
                                class="w-full flex items-center justify-center gap-2">
                                <LucideSave :size="16" />
                                {{ form.processing ? t('common.saving') : t('pages.createPage') }}
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
