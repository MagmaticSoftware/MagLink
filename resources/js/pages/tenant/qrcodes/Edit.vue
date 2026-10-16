<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    AlertTriangle,
    BarChart3,
    Clock,
    Contact,
    Download,
    Eye,
    FileText,
    Info,
    Link2,
    Lock,
    Mail,
    MapPin,
    MessageSquare,
    Phone,
    QrCode,
    Save,
    ScanLine,
    Settings,
    Shield,
    ShieldCheck,
} from 'lucide-vue-next';
import InputText from '@/components/volt/InputText.vue';
import Textarea from '@/components/volt/Textarea.vue';
import QrCustomization from '@/components/QrCustomization.vue';
import PageHeader from '@/components/PageHeader.vue';
import ToggleSetting from '@/components/ToggleSetting.vue';
import { useI18n } from 'vue-i18n';
import { ref, computed, watch } from 'vue';
import { useRoute } from '@/composables/useRoute';

const { t } = useI18n();
const route = useRoute();

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
        require_consent: boolean;
        last_scanned_at: string | null;
        created_at: string;
        updated_at: string;
    };
    shortUrl: string;
}>();

const form = useForm({
    user_id: props.qrcode.user_id,
    company_id: props.qrcode.company_id,
    tenant_id: props.qrcode.tenant_id,
    name: props.qrcode.name ?? '',
    slug: props.qrcode.slug ?? '',
    description: props.qrcode.description ?? '',
    type: props.qrcode.type,
    format: props.qrcode.format,
    payload: props.qrcode.payload ?? {},
    options: props.qrcode.options ?? {},
    is_active: props.qrcode.is_active,
    require_consent: props.qrcode.require_consent,
});

const isStatic = computed(() => props.qrcode.type === 'static');

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('qrcodes.title'), href: route('qrcodes.index') },
    { title: props.qrcode.name || t('qrcodes.noName'), href: route('qrcodes.show', props.qrcode.slug) },
    { title: t('common.edit'), href: route('qrcodes.edit', props.qrcode.slug) },
];

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

// Contenuto del payload: recupera da content, url o altri campi
const payloadContent = ref(
    props.qrcode.payload?.content || props.qrcode.payload?.url || props.qrcode.payload?.email || props.qrcode.payload?.phone || props.qrcode.payload?.text || '',
);

// Anteprima immagine del QR
const qrPreviewUrl = ref('');

const baseQrImageUrl = computed(() => {
    const params = new URLSearchParams({
        slug: props.qrcode.slug,
        format: props.qrcode.format,
        content: payloadContent.value || '',
        type: props.qrcode.type,
    });
    return `/api/qrcode/preview?${params.toString()}`;
});

qrPreviewUrl.value = baseQrImageUrl.value;

watch([payloadContent, () => form.format], () => {
    qrPreviewUrl.value = baseQrImageUrl.value;
});

const downloadQr = (format: 'png' | 'jpg' | 'svg') => {
    window.open(`/api/qrcode/${props.qrcode.slug}/download/${format}`, '_blank');
};

const formatDate = (date: string) => new Date(date).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });

const submitForm = () => {
    form.payload = { content: payloadContent.value };
    form.put(route('qrcodes.update', props.qrcode.slug));
};
</script>

<template>
    <Head :title="`${t('common.edit')} - ${qrcode.name || t('qrcodes.noName')}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex w-full flex-col gap-4 sm:gap-5 lg:p-2">
            <PageHeader
                :title="t('qrForm.editTitle')"
                :subtitle="qrcode.name || t('qrcodes.noName')"
                :icon="QrCode"
                icon-class="bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400"
                :back-href="route('qrcodes.index')"
                :back-label="t('common.back')"
            >
                <template #actions>
                    <Link :href="route('qrcodes.show', qrcode.slug)" class="ml-btn-secondary">{{ t('common.cancel') }}</Link>
                    <button type="submit" form="qrcode-form" class="ml-btn-primary" :disabled="form.processing">
                        <Save :size="16" />
                        {{ form.processing ? t('common.saving') : t('common.save') }}
                    </button>
                </template>
            </PageHeader>

            <form id="qrcode-form" class="grid grid-cols-1 gap-5 lg:grid-cols-12" @submit.prevent="submitForm">
                <!-- Impostazioni e personalizzazione -->
                <div class="space-y-5 lg:col-span-4">
                    <section class="ml-card p-5 sm:p-6">
                        <h2 class="ml-section-title mb-5"><Settings :size="18" class="text-primary-500" /> {{ t('qrForm.settings') }}</h2>

                        <div class="space-y-3">
                            <div>
                                <p class="ml-label">{{ t('qrForm.type') }}</p>
                                <p class="rounded-lg bg-surface-50 px-3 py-2.5 text-sm font-medium text-surface-900 dark:bg-surface-900/60 dark:text-surface-50">
                                    {{ isStatic ? t('qrcodes.static') : t('qrcodes.dynamic') }}
                                </p>
                                <p class="ml-hint">{{ t('qrForm.typeCannotChange') }}</p>
                            </div>

                            <div v-if="!isStatic">
                                <p class="ml-label">{{ t('qrForm.slug') }}</p>
                                <code class="block rounded-lg bg-surface-50 px-3 py-2.5 font-mono text-sm text-primary-600 dark:bg-surface-900/60 dark:text-primary-400">{{ qrcode.slug }}</code>
                            </div>

                            <ToggleSetting
                                v-model="form.is_active"
                                :icon="Eye"
                                :title="form.is_active ? t('qrcodes.active') : t('qrcodes.inactive')"
                                :description="t('qrForm.statusHint')"
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
                        <QrCustomization v-model="form.options" :previewUrl="baseQrImageUrl" @update:previewUrl="qrPreviewUrl = $event" />
                    </section>
                </div>

                <!-- Informazioni principali e statistiche -->
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
                                        :disabled="isStatic"
                                        class="rounded-lg border p-3.5 text-left transition-colors"
                                        :class="[
                                            form.format === format.value
                                                ? 'border-primary-400 bg-primary-50/60 ring-1 ring-primary-400 dark:bg-primary-500/10'
                                                : 'border-surface-200 hover:border-surface-300 hover:bg-surface-50 dark:border-surface-700 dark:hover:border-surface-600 dark:hover:bg-surface-800',
                                            isStatic ? 'cursor-not-allowed opacity-50' : 'cursor-pointer',
                                        ]"
                                        @click="!isStatic && (form.format = format.value)"
                                    >
                                        <component :is="format.icon" :size="20" class="mb-2" :class="form.format === format.value ? 'text-primary-600 dark:text-primary-400' : 'text-surface-500'" />
                                        <span class="block text-sm font-medium text-surface-900 dark:text-surface-50">{{ format.label }}</span>
                                        <span class="mt-0.5 block text-xs text-surface-500 dark:text-surface-400">{{ format.description }}</span>
                                    </button>
                                </div>
                                <p v-if="isStatic" class="mt-2 flex items-center gap-1.5 text-xs text-amber-700 dark:text-amber-400">
                                    <AlertTriangle :size="13" class="shrink-0" /> {{ t('qrcodes.formatCannotChange') }}
                                </p>
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
                                        :disabled="isStatic"
                                    />
                                </div>
                                <p class="ml-hint">{{ currentFormat.description }}</p>
                                <p v-if="isStatic" class="mt-1.5 flex items-center gap-1.5 text-xs text-amber-700 dark:text-amber-400">
                                    <AlertTriangle :size="13" class="shrink-0" /> {{ t('qrcodes.contentCannotChange') }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="ml-card flex items-center gap-4 p-5">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                                <ScanLine :size="20" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-2xl font-semibold text-surface-900 tabular-nums dark:text-white">{{ qrcode.scans }}</p>
                                <p class="text-[13px] text-surface-500 dark:text-surface-400">{{ t('qrcodes.totalScans') }}</p>
                            </div>
                            <Link :href="route('qrcodes.analytics', qrcode.slug)" class="ml-icon-btn" title="Analytics">
                                <BarChart3 :size="17" class="text-violet-500" />
                            </Link>
                        </div>
                        <div class="ml-card flex items-center gap-4 p-5">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                                <Clock :size="20" />
                            </span>
                            <div>
                                <p class="text-base font-semibold text-surface-900 dark:text-white">{{ formatDate(qrcode.created_at) }}</p>
                                <p class="text-[13px] text-surface-500 dark:text-surface-400">{{ t('qrForm.created') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Anteprima e download -->
                <div class="lg:col-span-3">
                    <section class="ml-card sticky top-20 space-y-4 p-5 sm:p-6">
                        <h2 class="ml-section-title"><QrCode :size="18" class="text-primary-500" /> {{ t('qrcodes.currentQrCode') }}</h2>
                        <div class="mx-auto flex aspect-square max-w-[280px] items-center justify-center rounded-xl border border-surface-200 bg-white dark:border-surface-700">
                            <img :src="qrPreviewUrl" alt="QR Code" class="h-full w-full object-contain p-3" />
                        </div>

                        <div>
                            <p class="ml-label">{{ t('qrcodes.downloadQr') }}</p>
                            <div class="grid grid-cols-3 gap-2">
                                <button v-for="ext in (['png', 'jpg', 'svg'] as const)" :key="ext" type="button" class="ml-btn-secondary px-2 py-1.5 text-xs uppercase" @click="downloadQr(ext)">
                                    <Download :size="13" /> {{ ext }}
                                </button>
                            </div>
                        </div>

                        <div v-if="!isStatic" class="rounded-lg border border-surface-200 bg-surface-50/60 p-3 text-xs dark:border-surface-700 dark:bg-surface-900/40">
                            <p class="truncate text-surface-500 dark:text-surface-400">
                                {{ t('qrcodes.shortUrl') }}: <span class="font-mono text-primary-600 dark:text-primary-400">{{ shortUrl }}/q/{{ qrcode.slug }}</span>
                            </p>
                            <p class="mt-1 truncate text-surface-600 dark:text-surface-300">{{ t('qrcodes.content') }}: {{ payloadContent || t('qrcodes.preview') }}</p>
                        </div>

                        <div v-if="!isStatic" class="flex gap-2 rounded-lg border border-blue-200 bg-blue-50 p-3 text-xs text-blue-800 dark:border-blue-500/25 dark:bg-blue-500/10 dark:text-blue-200">
                            <MapPin :size="15" class="mt-0.5 shrink-0" />
                            <div>
                                <p class="font-semibold">{{ t('qrcodes.dynamicQr') }}</p>
                                <p class="mt-0.5 opacity-90">{{ t('qrcodes.canBeUpdated') }}</p>
                            </div>
                        </div>
                        <div v-else class="flex gap-2 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-800 dark:border-emerald-500/25 dark:bg-emerald-500/10 dark:text-emerald-200">
                            <Lock :size="15" class="mt-0.5 shrink-0" />
                            <div>
                                <p class="font-semibold">{{ t('qrcodes.staticQr') }}</p>
                                <p class="mt-0.5 opacity-90">{{ t('qrcodes.embeddedDirectly') }}. {{ t('qrcodes.cannotBeModified') }}.</p>
                            </div>
                        </div>
                    </section>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
