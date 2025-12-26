<script setup lang="ts">
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { LucideAlertTriangle, LucideSparkles } from 'lucide-vue-next';
import type { PageProps } from '@/types/inertia';

const props = defineProps<{
    type: 'links' | 'qrcodes' | 'pages' | 'blocks';
    currentCount: number;
    qrcodeType?: 'static' | 'dynamic';
}>();

const page = usePage<PageProps>();

const limitInfo = computed(() => {
    const limits = page.props.billing?.limits;
    if (!limits) return null;

    let limit = 0;
    let label = '';

    switch (props.type) {
        case 'links':
            limit = limits.links;
            label = 'link';
            break;
        case 'qrcodes':
            if (props.qrcodeType === 'dynamic') {
                limit = limits.qrcodes_dynamic || 0;
                label = 'QR Code dinamici';
            } else {
                limit = limits.qrcodes;
                label = 'QR Code';
            }
            break;
        case 'pages':
            limit = limits.pages;
            label = 'pagine';
            break;
        case 'blocks':
            limit = limits.blocks_per_page || 0;
            label = 'blocchi per pagina';
            break;
    }

    return { limit, label };
});

const isNearLimit = computed(() => {
    if (!limitInfo.value || limitInfo.value.limit === -1) return false;
    return props.currentCount >= limitInfo.value.limit * 0.8;
});

const isAtLimit = computed(() => {
    if (!limitInfo.value || limitInfo.value.limit === -1) return false;
    return props.currentCount >= limitInfo.value.limit;
});

const remaining = computed(() => {
    if (!limitInfo.value || limitInfo.value.limit === -1) return Infinity;
    return limitInfo.value.limit - props.currentCount;
});
</script>

<template>
    <div 
        v-if="isNearLimit && !isAtLimit"
        class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4 mb-4"
    >
        <div class="flex items-start gap-3">
            <LucideAlertTriangle class="w-5 h-5 text-orange-600 dark:text-orange-400 flex-shrink-0 mt-0.5" />
            <div class="flex-1">
                <h4 class="text-sm font-semibold text-orange-900 dark:text-orange-100 mb-1">
                    Attenzione: limite quasi raggiunto
                </h4>
                <p class="text-sm text-orange-800 dark:text-orange-200">
                    Hai utilizzato {{ currentCount }} su {{ limitInfo?.limit }} {{ limitInfo?.label }} disponibili nel piano gratuito.
                    {{ remaining > 0 ? `Ti rimangono solo ${remaining} ${limitInfo?.label}.` : '' }}
                </p>
                <Link
                    :href="route('plans.index', { tenant: page.props.auth.tenant })"
                    class="inline-flex items-center gap-2 mt-2 text-sm font-medium text-orange-700 dark:text-orange-300 hover:underline"
                >
                    <LucideSparkles :size="14" />
                    Effettua l'upgrade per continuare senza limiti
                </Link>
            </div>
        </div>
    </div>

    <div 
        v-else-if="isAtLimit"
        class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-4"
    >
        <div class="flex items-start gap-3">
            <LucideAlertTriangle class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" />
            <div class="flex-1">
                <h4 class="text-sm font-semibold text-red-900 dark:text-red-100 mb-1">
                    Limite raggiunto
                </h4>
                <p class="text-sm text-red-800 dark:text-red-200">
                    Hai raggiunto il limite di {{ limitInfo?.limit }} {{ limitInfo?.label }} del piano gratuito.
                    Effettua l'upgrade per continuare a creare {{ limitInfo?.label }}.
                </p>
                <Link
                    :href="route('plans.index', { tenant: page.props.auth.tenant })"
                    class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors"
                >
                    <LucideSparkles :size="14" />
                    Passa a un piano superiore
                </Link>
            </div>
        </div>
    </div>
</template>
