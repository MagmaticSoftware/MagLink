<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { LucideEdit, LucideTrash2, LucidePlus, LucideQrCode, LucideEye } from 'lucide-vue-next';

const props = defineProps<{
    qrcodes: any[]; // Adjust type as necessary
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'QR Codes',
        href: route('qrcodes.index'),
    },
];

const getFormatIcon = (format: string) => {
    switch (format) {
        case 'url':
            return '🔗';
        case 'text':
            return '📝';
        case 'email':
            return '📧';
        case 'phone':
            return '📞';
        case 'sms':
            return '💬';
        case 'vcard':
            return '👤';
        default:
            return '📱';
    }
};

const getTypeColor = (type: string) => {
    return type === 'static' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800';
};
</script>

<template>
    <Head title="QR Codes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            
            <div class="mt-6 flex justify-end">
                <Link
                    :href="route('qrcodes.create')"
                    class="btn btn-primary flex items-center gap-2"
                >
                    <LucidePlus class="w-4 h-4" /> Aggiungi nuovo QR Code
                </Link>
            </div>
            <div class="flex flex-col gap-4">
                <div
                    v-for="qrcode in props.qrcodes"
                    :key="qrcode.id"
                    class="flex items-center justify-between rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 shadow transition hover:shadow-md"
                >
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <LucideQrCode class="w-10 h-10 text-gray-600 dark:text-gray-400" />
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ qrcode.name || 'QR Code senza nome' }}
                                </h2>
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                    :class="getTypeColor(qrcode.type)"
                                >
                                    {{ qrcode.type }}
                                </span>
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                    :class="qrcode.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                >
                                    {{ qrcode.is_active ? 'Attivo' : 'Inattivo' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                {{ qrcode.description || 'Nessuna descrizione' }}
                            </p>
                            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                <span class="flex items-center gap-1">
                                    <span>{{ getFormatIcon(qrcode.format) }}</span>
                                    <span class="font-medium">{{ qrcode.format.toUpperCase() }}</span>
                                </span>
                                <span class="flex items-center gap-1">
                                    <span>👁️</span>
                                    <span class="font-medium">{{ qrcode.scans }} scansioni</span>
                                </span>
                                <span class="flex items-center gap-1">
                                    <span>🗂️</span>
                                    <span class="font-medium">{{ qrcode.slug }}</span>
                                </span>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium">URL:</span>
                                    <a
                                        :href="`/qrcodes/${qrcode.slug}`"
                                        target="_blank"
                                        class="text-blue-600 dark:text-blue-400 underline hover:text-blue-800"
                                    >
                                        {{ `/qrcodes/${qrcode.slug}` }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <Link
                            :href="route('qrcodes.show', qrcode.slug)"
                            class="btn btn-sm btn-outline flex items-center gap-1"
                        >
                            <LucideEye class="w-4 h-4" /> Visualizza
                        </Link>
                        <Link
                            :href="route('qrcodes.edit', qrcode.slug)"
                            class="btn btn-sm btn-secondary flex items-center gap-1"
                        >
                            <LucideEdit class="w-4 h-4" /> Modifica
                        </Link>
                        <Link
                            :href="route('qrcodes.destroy', qrcode.slug)"
                            method="delete"
                            as="button"
                            class="btn btn-sm btn-danger flex items-center gap-1"
                        >
                            <LucideTrash2 class="w-4 h-4" /> Elimina
                        </Link>
                    </div>
                </div>
                <div
                    v-if="props.qrcodes.length === 0"
                    class="text-center py-12 text-gray-500 dark:text-gray-400"
                >
                    <LucideQrCode class="w-16 h-16 mx-auto mb-4 text-gray-300" />
                    <p class="text-lg font-medium">Nessun QR Code trovato</p>
                    <p class="text-sm">Crea il tuo primo QR Code per iniziare!</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
