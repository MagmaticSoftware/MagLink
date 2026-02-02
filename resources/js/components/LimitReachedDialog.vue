<script setup lang="ts">
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { LucideAlertTriangle, LucideSparkles, LucideLink, LucideQrCode, LucideFileText } from 'lucide-vue-next';
import Dialog from '@/components/volt/Dialog.vue';
import Button from '@/components/volt/Button.vue';
import type { PageProps } from '@/types/inertia';

const props = defineProps<{
    visible: boolean;
    type: 'links' | 'qrcodes' | 'pages' | 'blocks';
}>();

const emit = defineEmits<{
    'update:visible': [value: boolean];
    'show-plans': [];
}>();

const page = usePage<PageProps>();

const dialogConfig = computed(() => {
    const limits = page.props.billing?.limits;
    
    const configs = {
        links: {
            title: 'Limite Link Raggiunto',
            icon: LucideLink,
            limit: limits?.links || 0,
            singular: 'link',
            plural: 'link',
            description: 'Hai raggiunto il limite massimo di link brevi del tuo piano.'
        },
        qrcodes: {
            title: 'Limite QR Code Raggiunto',
            icon: LucideQrCode,
            limit: limits?.qrcodes || 0,
            singular: 'QR Code',
            plural: 'QR Code',
            description: 'Hai raggiunto il limite massimo di QR Code del tuo piano.'
        },
        pages: {
            title: 'Limite Pagine Raggiunto',
            icon: LucideFileText,
            limit: limits?.pages || 0,
            singular: 'pagina',
            plural: 'pagine',
            description: 'Hai raggiunto il limite massimo di pagine del tuo piano.'
        },
        blocks: {
            title: 'Limite Blocchi Raggiunto',
            icon: LucideFileText,
            limit: limits?.blocks_per_page || 0,
            singular: 'blocco',
            plural: 'blocchi',
            description: 'Hai raggiunto il limite massimo di blocchi per pagina del tuo piano.'
        }
    };
    
    return configs[props.type];
});

const closeDialog = () => {
    emit('update:visible', false);
};

const goToPlans = () => {
    closeDialog();
    emit('show-plans');
};
</script>

<template>
    <Dialog
        :visible="visible"
        @update:visible="closeDialog"
        modal
        :draggable="false"
        :closable="true"
        class="max-w-md"
        style="width: 90%; max-width: 28rem;"
    >
        <template #header>
            <div class="flex items-start gap-4 pb-2">
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/40 dark:to-orange-800/30 flex items-center justify-center shadow-lg ring-4 ring-orange-50 dark:ring-orange-900/20">
                    <LucideAlertTriangle class="w-7 h-7 text-orange-600 dark:text-orange-400" />
                </div>
                <div class="flex-1 pt-1">
                    <h3 class="text-2xl font-bold text-surface-900 dark:text-surface-50 mb-1">
                        {{ dialogConfig.title }}
                    </h3>
                    <p class="text-sm text-surface-500 dark:text-surface-400">
                        Passa a un piano superiore per continuare
                    </p>
                </div>
            </div>
        </template>

        <div class="space-y-5 pt-2">
            <!-- Messaggio principale con design migliorato -->
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-orange-50 to-orange-100/50 dark:from-orange-900/20 dark:to-orange-800/10 border-2 border-orange-200 dark:border-orange-800/50 p-5">
                <div class="absolute top-0 right-0 w-32 h-32 bg-orange-200/30 dark:bg-orange-700/10 rounded-full -mr-16 -mt-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-orange-300/20 dark:bg-orange-600/10 rounded-full -ml-12 -mb-12"></div>
                
                <div class="relative flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-white dark:bg-surface-800 shadow-md flex items-center justify-center">
                        <component :is="dialogConfig.icon" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-orange-900 dark:text-orange-100 font-semibold mb-2 leading-relaxed">
                            {{ dialogConfig.description }}
                        </p>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/80 dark:bg-surface-800/80 backdrop-blur-sm rounded-lg border border-orange-200/50 dark:border-orange-700/50">
                            <span class="text-xs font-medium text-orange-700 dark:text-orange-300">Piano attuale:</span>
                            <span class="text-xs font-bold text-orange-900 dark:text-orange-100">Free</span>
                            <span class="text-xs text-orange-600 dark:text-orange-400">({{ dialogConfig.limit }} {{ dialogConfig.plural }})</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Benefici dell'upgrade con design moderno -->
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="h-px flex-1 bg-gradient-to-r from-transparent via-surface-200 dark:via-surface-700 to-transparent"></div>
                    <p class="text-xs font-bold text-surface-900 dark:text-surface-50 uppercase tracking-wider">
                        Con un piano superiore
                    </p>
                    <div class="h-px flex-1 bg-gradient-to-r from-transparent via-surface-200 dark:via-surface-700 to-transparent"></div>
                </div>
                
                <ul class="space-y-3">
                    <li class="flex items-start gap-3 group">
                        <div class="flex-shrink-0 w-6 h-6 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-sm text-surface-700 dark:text-surface-300 pt-0.5 font-medium">
                            {{ type === 'links' ? 'Link illimitati' : type === 'qrcodes' ? 'QR Code illimitati' : type === 'pages' ? 'Più pagine' : 'Più blocchi per pagina' }}
                        </span>
                    </li>
                    <li class="flex items-start gap-3 group">
                        <div class="flex-shrink-0 w-6 h-6 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-sm text-surface-700 dark:text-surface-300 pt-0.5 font-medium">
                            Statistiche avanzate e personalizzazioni
                        </span>
                    </li>
                    <li class="flex items-start gap-3 group">
                        <div class="flex-shrink-0 w-6 h-6 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-sm text-surface-700 dark:text-surface-300 pt-0.5 font-medium">
                            Supporto prioritario e funzionalità premium
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <template #footer>
            <div class="flex flex-col sm:flex-row gap-3 justify-end pt-2">
                <Button
                    variant="outline"
                    @click="closeDialog"
                    class="order-2 sm:order-1"
                >
                    Chiudi
                </Button>
                <Button
                    @click="goToPlans"
                    class="order-1 sm:order-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white border-0 transition-all"
                >
                    <LucideSparkles class="w-4 h-4 mr-2" />
                    Vedi i Piani
                </Button>
            </div>
        </template>
    </Dialog>
</template>
