<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Eye, ExternalLink, Globe, Info, Link2, Save, Settings, Shield, ShieldCheck } from 'lucide-vue-next';
import InputText from '@/components/volt/InputText.vue';
import Textarea from '@/components/volt/Textarea.vue';
import LimitWarning from '@/components/LimitWarning.vue';
import PageHeader from '@/components/PageHeader.vue';
import ToggleSetting from '@/components/ToggleSetting.vue';
import { useI18n } from 'vue-i18n';
import { type PageProps } from '@/types/inertia';
import { useRoute } from '@/composables/useRoute';

const { t } = useI18n();
const page = usePage<PageProps>();
const route = useRoute();

const props = defineProps<{
    slug: string;
    shortUrl: string;
    linkCount: number;
}>();

const form = useForm({
    user_id: page.props.auth.user.id,
    company_id: page.props.auth.user.company?.id ?? 1,
    tenant_id: page.props.auth.user.tenant_id,
    slug: props.slug,
    url: '',
    title: '',
    description: '',
    is_active: true,
    require_consent: false,
    type: 'link',
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('links.title'), href: route('links.index') },
    { title: t('common.create'), href: route('links.create') },
];

const submitForm = () => {
    form.post(route('links.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head :title="t('links.addNew')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex w-full flex-col gap-4 sm:gap-5 lg:p-2">
            <LimitWarning type="links" :currentCount="linkCount" />

            <PageHeader
                :title="t('links.addNew')"
                :subtitle="t('links.createDescription')"
                :icon="Link2"
                icon-class="bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400"
                :back-href="route('links.index')"
                :back-label="t('common.back')"
            >
                <template #actions>
                    <Link :href="route('links.index')" class="ml-btn-secondary">{{ t('common.cancel') }}</Link>
                    <button type="submit" form="link-form" class="ml-btn-primary" :disabled="form.processing">
                        <Save :size="16" />
                        {{ form.processing ? t('linkForm.creating') : t('common.save') }}
                    </button>
                </template>
            </PageHeader>

            <form id="link-form" class="grid grid-cols-1 gap-5 lg:grid-cols-3" @submit.prevent="submitForm">
                <!-- Dettagli principali -->
                <div class="space-y-5 lg:col-span-2">
                    <section class="ml-card p-5 sm:p-6">
                        <h2 class="ml-section-title mb-5"><Info :size="18" class="text-primary-500" /> {{ t('linkForm.basicInfo') }}</h2>

                        <div class="space-y-5">
                            <div>
                                <label for="link-title" class="ml-label">{{ t('linkForm.title') }} <span class="text-rose-500">*</span></label>
                                <InputText id="link-title" v-model="form.title" :placeholder="t('links.placeholders.title')" required class="w-full" />
                                <p v-if="form.errors.title" class="ml-error">{{ form.errors.title }}</p>
                            </div>

                            <div>
                                <label for="link-description" class="ml-label">{{ t('linkForm.description') }}</label>
                                <Textarea id="link-description" v-model="form.description" :placeholder="t('links.placeholders.description')" rows="3" class="w-full" />
                                <p v-if="form.errors.description" class="ml-error">{{ form.errors.description }}</p>
                            </div>

                            <div>
                                <label for="link-url" class="ml-label">{{ t('linkForm.destination') }} <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <Globe :size="17" class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-surface-400" />
                                    <InputText id="link-url" v-model="form.url" type="url" :placeholder="t('links.placeholders.url')" required class="w-full pl-10" />
                                </div>
                                <a v-if="form.url" :href="form.url" target="_blank" rel="noopener" class="mt-1.5 inline-flex items-center gap-1 text-xs font-medium text-primary-600 hover:underline dark:text-primary-400">
                                    {{ t('linkForm.previewUrl') }} <ExternalLink :size="12" />
                                </a>
                                <p v-if="form.errors.url" class="ml-error">{{ form.errors.url }}</p>
                            </div>

                            <div class="rounded-lg border border-surface-200 bg-surface-50/60 p-4 dark:border-surface-700 dark:bg-surface-900/40">
                                <p class="ml-label">{{ t('linkForm.shortLink') }}</p>
                                <code class="block truncate rounded-md border border-surface-200 bg-white px-3 py-2 font-mono text-sm text-primary-600 dark:border-surface-700 dark:bg-surface-900 dark:text-primary-400">
                                    {{ props.shortUrl }}/{{ props.slug }}
                                </code>
                                <p class="ml-hint">{{ t('linkForm.shortLinkHintCreate') }}</p>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Impostazioni e anteprima -->
                <div class="space-y-5">
                    <section class="ml-card p-5 sm:p-6">
                        <h2 class="ml-section-title mb-5"><Settings :size="18" class="text-primary-500" /> {{ t('linkForm.settings') }}</h2>

                        <div class="space-y-3">
                            <div>
                                <p class="ml-label">{{ t('linkForm.slug') }}</p>
                                <code class="block rounded-lg bg-surface-50 px-3 py-2.5 font-mono text-sm text-surface-900 dark:bg-surface-900/60 dark:text-surface-50">{{ props.slug }}</code>
                                <p class="ml-hint">{{ t('linkForm.slugHintAuto') }}</p>
                            </div>

                            <ToggleSetting
                                v-model="form.is_active"
                                :icon="Eye"
                                :title="form.is_active ? t('linkForm.active') : t('linkForm.inactive')"
                                :description="t('linkForm.statusHint')"
                            />

                            <ToggleSetting
                                v-model="form.require_consent"
                                :icon="form.require_consent ? ShieldCheck : Shield"
                                tone="blue"
                                :title="form.require_consent ? t('linkForm.consentOn') : t('linkForm.consentOff')"
                                :description="t('linkForm.consentHint')"
                            />

                            <p v-if="form.require_consent" class="flex gap-2 rounded-lg border border-blue-200 bg-blue-50 p-3 text-xs text-blue-900 dark:border-blue-500/25 dark:bg-blue-500/10 dark:text-blue-200">
                                <ShieldCheck :size="15" class="mt-0.5 shrink-0" />
                                <span>{{ t('linkForm.consentInfo') }}</span>
                            </p>
                        </div>
                    </section>

                    <section class="ml-card p-5 sm:p-6">
                        <h2 class="ml-section-title mb-4"><Eye :size="18" class="text-primary-500" /> {{ t('linkForm.preview') }}</h2>
                        <div class="flex items-start gap-3 rounded-lg border border-surface-200 bg-surface-50/60 p-4 dark:border-surface-700 dark:bg-surface-900/40">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                                <Link2 :size="18" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold text-surface-900 dark:text-surface-50">{{ form.title || t('linkForm.previewTitle') }}</p>
                                <p class="truncate text-sm text-surface-500 dark:text-surface-400">{{ form.description || t('linkForm.previewDescription') }}</p>
                                <p class="mt-1.5 truncate font-mono text-xs text-primary-600 dark:text-primary-400">{{ props.shortUrl }}/{{ props.slug }}</p>
                                <p class="truncate text-xs text-surface-500 dark:text-surface-400">{{ t('linkForm.destinationLabel') }}: {{ form.url || 'https://example.com' }}</p>
                            </div>
                        </div>
                        <p class="ml-hint text-center">{{ t('linkForm.previewHint') }}</p>
                    </section>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
