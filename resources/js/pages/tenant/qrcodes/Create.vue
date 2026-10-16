<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Contact, Eye, FileText, Info, Link2, Lock, Mail, MapPin, MessageSquare, Phone, QrCode, Save, Settings, Shield, ShieldCheck } from 'lucide-vue-next';
import InputText from '@/components/volt/InputText.vue';
import Textarea from '@/components/volt/Textarea.vue';
import LimitWarning from '@/components/LimitWarning.vue';
import QrCustomization from '@/components/QrCustomization.vue';
import PageHeader from '@/components/PageHeader.vue';
import ToggleSetting from '@/components/ToggleSetting.vue';
import { useI18n } from 'vue-i18n';
import { type PageProps } from '@/types/inertia';
import { ref, computed, watch } from 'vue';
import { useRoute } from '@/composables/useRoute';

const { t } = useI18n();
const page = usePage<PageProps>();
const route = useRoute();

const props = defineProps<{
    slug: string;
    shortUrl: string;
    dynamicQrCodeCount: number;
    totalQrCodeCount: number;
}>();

const form = useForm({
    user_id: page.props.auth.user.id,
    company_id: page.props.auth.user.company?.id ?? 1,
    tenant_id: page.props.auth.user.tenant_id,
    slug: props.slug,
    name: '',
    description: '',
    type: 'static',
    format: 'url',
    payload: { content: '' },
    options: {},
    is_active: true,
    require_consent: false,
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('qrcodes.title'), href: route('qrcodes.index') },
    { title: t('common.create'), href: route('qrcodes.create') },
];

const typeOptions = computed(() => [
    { value: 'static', label: t('qrcodes.static'), icon: Lock },
    { value: 'dynamic', label: t('qrcodes.dynamic'), icon: MapPin },
]);

// Formati disponibili
const formatOptions = computed(() => [
    { value: 'url', icon: Link2 },
    { value: 'text', icon: FileText },
    { value: 'email', icon: Mail },
    { value: 'phone', icon: Phone },
    { value: 'sms', icon: MessageSquare },
    { value: 'vcard', icon: Contact },
].map((format) => ({
    ...format,
    label: t(`qrForm.formats.${format.value}.label`),
    description: t(`qrForm.formats.${format.value}.description`),
})));

const currentFormat = computed(() => formatOptions.value.find((f) => f.value === form.format) || formatOptions.value[0]);

const payloadContent = ref('');

// Anteprima immagine del QR
const qrPreviewUrl = ref('');

const baseQrPreviewUrl = computed(() => {
    if (!payloadContent.value && form.format !== 'url') return null;

    const params = new URLSearchParams({
        slug: props.slug,
        format: form.format,
        content: payloadContent.value || '',
        type: form.type,
    });

    return `/api/qrcode/preview?${params.toString()}`;
});

qrPreviewUrl.value = baseQrPreviewUrl.value || '';

watch([payloadContent, () => form.format, () => form.type], () => {
    if (baseQrPreviewUrl.value) {
        qrPreviewUrl.value = baseQrPreviewUrl.value;
    }
});

const submitForm = () => {
    form.payload = { content: payloadContent.value };
    form.post(route('qrcodes.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head :title="t('qrcodes.addNew')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex w-full flex-col gap-4 sm:gap-5 lg:p-2">
            <LimitWarning type="qrcodes" :currentCount="totalQrCodeCount" />

            <PageHeader
                :title="t('qrcodes.addNew')"
                :subtitle="t('qrcodes.createDescription')"
                :icon="QrCode"
                icon-class="bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400"
                :back-href="route('qrcodes.index')"
                :back-label="t('common.back')"
            >
                <template #actions>
                    <Link :href="route('qrcodes.index')" class="ml-btn-secondary">{{ t('common.cancel') }}</Link>
                    <button type="submit" form="qrcode-form" class="ml-btn-primary" :disabled="form.processing">
                        <Save :size="16" />
                        {{ form.processing ? t('qrcodes.creating') : t('common.save') }}
                    </button>
                </template>
            </PageHeader>

            <form id="qrcode-form" class="grid grid-cols-1 gap-5 lg:grid-cols-12" @submit.prevent="submitForm">
                <!-- Impostazioni e personalizzazione -->
                <div class="space-y-5 lg:col-span-4">
                    <LimitWarning v-if="form.type === 'dynamic'" type="qrcodes" :currentCount="dynamicQrCodeCount" qrcodeType="dynamic" />

                    <section class="ml-card p-5 sm:p-6">
                        <h2 class="ml-section-title mb-5"><Settings :size="18" class="text-primary-500" /> {{ t('qrcodes.settingsTitle') }}</h2>

                        <div class="space-y-3">
                            <div>
                                <p class="ml-label">{{ t('qrcodes.typeLabel') }}</p>
                                <div class="grid grid-cols-2 gap-1 rounded-lg bg-surface-100 p-1 dark:bg-surface-900/60">
                                    <button
                                        v-for="option in typeOptions"
                                        :key="option.value"
                                        type="button"
                                        class="inline-flex items-center justify-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium transition-colors"
                                        :class="form.type === option.value
                                            ? 'bg-white text-surface-900 shadow-sm dark:bg-surface-700 dark:text-white'
                                            : 'text-surface-500 hover:text-surface-800 dark:text-surface-400 dark:hover:text-surface-100'"
                                        @click="form.type = option.value"
                                    >
                                        <component :is="option.icon" :size="14" /> {{ option.label }}
                                    </button>
                                </div>
                                <p class="ml-hint">{{ t('qrcodes.staticCannotEdit') }}</p>
                                <p v-if="form.type === 'static'" class="mt-2 flex gap-2 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900 dark:border-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200">
                                    <Info :size="15" class="mt-0.5 shrink-0" />
                                    <span>{{ t('qrcodes.staticNotTracked') }}</span>
                                </p>
                            </div>

                            <ToggleSetting
                                v-model="form.is_active"
                                :icon="Eye"
                                :title="form.is_active ? t('qrcodes.active') : t('qrcodes.inactive')"
                                :description="t('qrcodes.statusHint')"
                            />

                            <ToggleSetting
                                v-model="form.require_consent"
                                :icon="form.require_consent ? ShieldCheck : Shield"
                                tone="blue"
                                :title="form.require_consent ? t('qrcodes.consent.enabled') : t('qrcodes.consent.disabled')"
                                :description="t('qrcodes.consent.gdpr')"
                            />

                            <p v-if="form.require_consent" class="flex gap-2 rounded-lg border border-blue-200 bg-blue-50 p-3 text-xs text-blue-900 dark:border-blue-500/25 dark:bg-blue-500/10 dark:text-blue-200">
                                <ShieldCheck :size="15" class="mt-0.5 shrink-0" />
                                <span><strong>{{ t('qrcodes.consent.infoTitle') }}</strong> {{ t('qrcodes.consent.infoBody') }}</span>
                            </p>
                        </div>
                    </section>

                    <section class="ml-card p-5 sm:p-6">
                        <QrCustomization v-model="form.options" :previewUrl="baseQrPreviewUrl || ''" @update:previewUrl="qrPreviewUrl = $event" />
                    </section>
                </div>

                <!-- Informazioni principali -->
                <div class="space-y-5 lg:col-span-5">
                    <section class="ml-card p-5 sm:p-6">
                        <h2 class="ml-section-title mb-5"><Info :size="18" class="text-primary-500" /> {{ t('qrForm.basicInfo') }}</h2>

                        <div class="space-y-5">
                            <div>
                                <label for="qr-name" class="ml-label">{{ t('qrForm.name') }} <span class="text-rose-500">*</span></label>
                                <InputText id="qr-name" v-model="form.name" :placeholder="t('qrcodes.placeholders.title')" required class="w-full" />
                                <p v-if="form.errors.name" class="ml-error">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label for="qr-description" class="ml-label">{{ t('qrForm.description') }}</label>
                                <Textarea id="qr-description" v-model="form.description" :placeholder="t('qrcodes.placeholders.description')" rows="3" class="w-full" />
                                <p v-if="form.errors.description" class="ml-error">{{ form.errors.description }}</p>
                            </div>

                            <div>
                                <p class="ml-label">{{ t('qrForm.formatType') }} <span class="text-rose-500">*</span></p>
                                <div class="grid grid-cols-2 gap-2.5 md:grid-cols-3">
                                    <button
                                        v-for="format in formatOptions"
                                        :key="format.value"
                                        type="button"
                                        class="cursor-pointer rounded-lg border p-3.5 text-left transition-colors"
                                        :class="form.format === format.value
                                            ? 'border-primary-400 bg-primary-50/60 ring-1 ring-primary-400 dark:bg-primary-500/10'
                                            : 'border-surface-200 hover:border-surface-300 hover:bg-surface-50 dark:border-surface-700 dark:hover:border-surface-600 dark:hover:bg-surface-800'"
                                        @click="form.format = format.value"
                                    >
                                        <component :is="format.icon" :size="20" class="mb-2" :class="form.format === format.value ? 'text-primary-600 dark:text-primary-400' : 'text-surface-500'" />
                                        <span class="block text-sm font-medium text-surface-900 dark:text-surface-50">{{ format.label }}</span>
                                        <span class="mt-0.5 block text-xs text-surface-500 dark:text-surface-400">{{ format.description }}</span>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label for="qr-content" class="ml-label">{{ t('qrForm.content') }} <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <component :is="currentFormat.icon" :size="17" class="pointer-events-none absolute top-3 left-3 text-surface-400" />
                                    <Textarea
                                        id="qr-content"
                                        v-model="payloadContent"
                                        :placeholder="t('qrForm.contentPlaceholder', { format: currentFormat.label.toLowerCase() })"
                                        rows="4"
                                        class="w-full pl-10"
                                        required
                                    />
                                </div>
                                <p class="ml-hint">{{ currentFormat.description }}</p>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Anteprima -->
                <div class="lg:col-span-3">
                    <section class="ml-card sticky top-20 space-y-4 p-5 sm:p-6">
                        <h2 class="ml-section-title"><QrCode :size="18" class="text-primary-500" /> {{ t('qrcodes.previewTitle') }}</h2>
                        <div
                            class="mx-auto flex aspect-square max-w-[280px] items-center justify-center rounded-xl border bg-white"
                            :class="qrPreviewUrl ? 'border-surface-200 dark:border-surface-700' : 'border-dashed border-surface-300 dark:border-surface-600 dark:bg-surface-900/40'"
                        >
                            <div v-if="!qrPreviewUrl" class="p-4 text-center">
                                <QrCode :size="56" class="mx-auto mb-2 text-surface-300 dark:text-surface-600" />
                                <p class="text-xs text-surface-500 dark:text-surface-400">{{ t('qrcodes.preview') }}</p>
                            </div>
                            <img v-else :src="qrPreviewUrl" alt="QR Code" class="h-full w-full object-contain p-3" />
                        </div>

                        <div v-if="form.type === 'dynamic'" class="rounded-lg border border-surface-200 bg-surface-50/60 p-3 text-xs dark:border-surface-700 dark:bg-surface-900/40">
                            <p class="truncate text-surface-500 dark:text-surface-400">
                                {{ t('qrcodes.shortUrl') }}: <span class="font-mono text-primary-600 dark:text-primary-400">{{ shortUrl }}/q/{{ props.slug }}</span>
                            </p>
                            <p class="mt-1 truncate text-surface-600 dark:text-surface-300">{{ payloadContent ? `${t('qrcodes.content')}: ${payloadContent}` : t('qrcodes.preview') }}</p>
                        </div>

                        <div v-if="form.type === 'dynamic'" class="flex gap-2 rounded-lg border border-blue-200 bg-blue-50 p-3 text-xs text-blue-800 dark:border-blue-500/25 dark:bg-blue-500/10 dark:text-blue-200">
                            <MapPin :size="15" class="mt-0.5 shrink-0" />
                            <div>
                                <p class="font-semibold">{{ t('qrcodes.dynamicQr') }}</p>
                                <p class="mt-0.5 opacity-90">{{ t('qrcodes.canBeChanged') }}</p>
                            </div>
                        </div>
                        <div v-else class="flex gap-2 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-800 dark:border-emerald-500/25 dark:bg-emerald-500/10 dark:text-emerald-200">
                            <Lock :size="15" class="mt-0.5 shrink-0" />
                            <div>
                                <p class="font-semibold">{{ t('qrcodes.staticQr') }}</p>
                                <p class="mt-0.5 opacity-90">{{ t('qrcodes.embeddedDirectly') }}. {{ t('qrcodes.worksOffline') }}.</p>
                            </div>
                        </div>

                        <p class="text-center text-xs text-surface-500 dark:text-surface-400">{{ t('qrcodes.howItAppears') }}</p>
                    </section>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
