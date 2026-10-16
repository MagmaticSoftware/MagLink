<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Calendar, FileText, Link2, Palette, Save, Settings, ToggleRight } from 'lucide-vue-next';
import InputText from '@/components/volt/InputText.vue';
import Textarea from '@/components/volt/Textarea.vue';
import LimitWarning from '@/components/LimitWarning.vue';
import PageHeader from '@/components/PageHeader.vue';
import ToggleSetting from '@/components/ToggleSetting.vue';
import { useRoute } from '@/composables/useRoute';
import { type PageProps } from '@/types/inertia';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const page = usePage<PageProps>();
const route = useRoute();

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
    <Head :title="t('pages.create')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex w-full flex-col gap-4 sm:gap-5 lg:p-2">
            <LimitWarning type="pages" :currentCount="pageCount" />

            <PageHeader
                :title="t('pages.create')"
                :subtitle="t('pages.createDescription')"
                :icon="FileText"
                icon-class="bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400"
                :back-href="route('pages.index')"
                :back-label="t('common.back')"
            >
                <template #actions>
                    <Link :href="route('pages.index')" class="ml-btn-secondary">{{ t('common.cancel') }}</Link>
                    <button type="submit" form="page-form" class="ml-btn-primary" :disabled="form.processing">
                        <Save :size="16" />
                        {{ form.processing ? t('common.saving') : t('pages.createPage') }}
                    </button>
                </template>
            </PageHeader>

            <form id="page-form" class="grid grid-cols-1 gap-5 lg:grid-cols-3" @submit.prevent="submitForm">
                <section class="ml-card p-5 sm:p-6 lg:col-span-2">
                    <div class="mb-5">
                        <h2 class="ml-section-title"><FileText :size="18" class="text-primary-500" /> {{ t('pages.pageInfo') }}</h2>
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">{{ t('pages.pageInfoDescription') }}</p>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label for="page-title" class="ml-label">{{ t('pages.form.title') }} <span class="text-rose-500">*</span></label>
                            <InputText id="page-title" v-model="form.title" required class="w-full" :placeholder="t('pages.form.titlePlaceholder')" @blur="generateSlug" />
                            <p v-if="form.errors.title" class="ml-error">{{ form.errors.title }}</p>
                        </div>

                        <div>
                            <label for="page-slug" class="ml-label inline-flex items-center gap-1.5"><Link2 :size="14" /> {{ t('pages.form.slug') }}</label>
                            <InputText id="page-slug" v-model="form.slug" class="w-full font-mono text-sm" :placeholder="t('pages.placeholders.slug')" />
                            <p class="ml-hint">{{ t('pages.form.slugHint') }}: /{{ form.slug || t('pages.form.slugExample') }}</p>
                            <p v-if="form.errors.slug" class="ml-error">{{ form.errors.slug }}</p>
                        </div>

                        <div>
                            <label for="page-description" class="ml-label">{{ t('pages.form.description') }}</label>
                            <Textarea id="page-description" v-model="form.description" class="w-full" rows="4" :placeholder="t('pages.form.descriptionPlaceholder')" />
                        </div>
                    </div>
                </section>

                <section class="ml-card space-y-5 p-5 sm:p-6">
                    <h2 class="ml-section-title"><Settings :size="18" class="text-primary-500" /> {{ t('pages.settings') }}</h2>

                    <div>
                        <label for="page-published" class="ml-label inline-flex items-center gap-1.5"><Calendar :size="14" /> {{ t('pages.form.published') }}</label>
                        <input
                            id="page-published"
                            v-model="form.published_at"
                            type="date"
                            class="w-full rounded-lg border border-surface-200 bg-white px-3 py-2 text-sm text-surface-700 focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 focus:outline-none dark:border-surface-700 dark:bg-surface-900 dark:text-surface-100"
                        />
                        <p class="ml-hint">{{ t('pages.form.publishedHint') }}</p>
                    </div>

                    <ToggleSetting
                        v-model="form.is_active"
                        :icon="ToggleRight"
                        :title="t('pages.form.active')"
                        :description="form.is_active ? t('pages.form.activeHint') : t('pages.form.inactiveHint')"
                    />

                    <div class="border-t border-surface-100 pt-5 dark:border-surface-700">
                        <p class="ml-label inline-flex items-center gap-1.5"><Palette :size="14" /> {{ t('pages.form.themeColor') }}</p>
                        <div class="grid grid-cols-7 gap-2">
                            <button
                                v-for="color in themeColors"
                                :key="color.value"
                                type="button"
                                :title="color.name"
                                :aria-label="color.name"
                                class="h-8 w-8 rounded-lg border-2 transition-shadow"
                                :class="[
                                    color.class,
                                    selectedColor === color.value
                                        ? 'border-white ring-2 ring-surface-900 dark:border-surface-900 dark:ring-surface-100'
                                        : 'border-transparent hover:ring-2 hover:ring-surface-300 dark:hover:ring-surface-600',
                                ]"
                                @click="selectColor(color.value)"
                            />
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </AppLayout>
</template>
