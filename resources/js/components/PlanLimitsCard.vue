<script setup lang="ts">
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { LucideLink, LucideQrCode, LucideFileText, LucideSparkles } from 'lucide-vue-next';
import type { PageProps } from '@/types/inertia';

const props = defineProps<{
    stats: {
        links_count: number;
        qrcodes_count: number;
        pages_count: number;
    };
}>();

const page = usePage<PageProps>();

const planLimits = computed(() => {
    const plans = page.props.plans;
    const billing = page.props.billing;
    
    // Determina il piano corrente
    let currentPlanKey = 'free';
    if (billing?.currentPlanName) {
        currentPlanKey = Object.keys(plans || {}).find(
            key => plans[key].name === billing.currentPlanName
        ) || 'free';
    }
    
    return plans?.[currentPlanKey]?.limits || {
        links: 5,
        qrcodes: 5,
        pages: 1,
    };
});

const linkProgress = computed(() => {
    const limit = planLimits.value.links;
    if (limit === -1) return 0;
    return Math.min((props.stats.links_count / limit) * 100, 100);
});

const qrcodeProgress = computed(() => {
    const limit = planLimits.value.qrcodes;
    if (limit === -1) return 0;
    return Math.min((props.stats.qrcodes_count / limit) * 100, 100);
});

const pageProgress = computed(() => {
    const limit = planLimits.value.pages;
    if (limit === -1) return 0;
    return Math.min((props.stats.pages_count / limit) * 100, 100);
});

const isNearLimit = (current: number, limit: number) => {
    if (limit === -1) return false;
    return (current / limit) >= 0.8;
};

const isAtLimit = (current: number, limit: number) => {
    if (limit === -1) return false;
    return current >= limit;
};

const formatLimit = (limit: number) => {
    return limit === -1 ? '∞' : limit;
};
</script>

<template>
    <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-surface-900 dark:text-surface-50">
                Utilizzo Piano
            </h3>
            <Link
                v-if="page.props.billing?.onFreePlan"
                :href="route('plans.index', { tenant: page.props.auth.tenant })"
                class="text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1"
            >
                <LucideSparkles :size="14" />
                Upgrade
            </Link>
        </div>

        <div class="space-y-4">
            <!-- Links -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2 text-sm">
                        <LucideLink :size="16" class="text-blue-600 dark:text-blue-400" />
                        <span class="font-medium text-surface-700 dark:text-surface-300">Link</span>
                    </div>
                    <span 
                        class="text-sm font-semibold"
                        :class="{
                            'text-red-600 dark:text-red-400': isAtLimit(stats.links_count, planLimits.links),
                            'text-orange-600 dark:text-orange-400': isNearLimit(stats.links_count, planLimits.links) && !isAtLimit(stats.links_count, planLimits.links),
                            'text-surface-700 dark:text-surface-300': !isNearLimit(stats.links_count, planLimits.links)
                        }"
                    >
                        {{ stats.links_count }} / {{ formatLimit(planLimits.links) }}
                    </span>
                </div>
                <div class="w-full bg-surface-200 dark:bg-surface-700 rounded-full h-2">
                    <div 
                        class="h-2 rounded-full transition-all"
                        :class="{
                            'bg-red-500': isAtLimit(stats.links_count, planLimits.links),
                            'bg-orange-500': isNearLimit(stats.links_count, planLimits.links) && !isAtLimit(stats.links_count, planLimits.links),
                            'bg-blue-500': !isNearLimit(stats.links_count, planLimits.links)
                        }"
                        :style="{ width: linkProgress + '%' }"
                    ></div>
                </div>
            </div>

            <!-- QR Codes -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2 text-sm">
                        <LucideQrCode :size="16" class="text-green-600 dark:text-green-400" />
                        <span class="font-medium text-surface-700 dark:text-surface-300">QR Code</span>
                    </div>
                    <span 
                        class="text-sm font-semibold"
                        :class="{
                            'text-red-600 dark:text-red-400': isAtLimit(stats.qrcodes_count, planLimits.qrcodes),
                            'text-orange-600 dark:text-orange-400': isNearLimit(stats.qrcodes_count, planLimits.qrcodes) && !isAtLimit(stats.qrcodes_count, planLimits.qrcodes),
                            'text-surface-700 dark:text-surface-300': !isNearLimit(stats.qrcodes_count, planLimits.qrcodes)
                        }"
                    >
                        {{ stats.qrcodes_count }} / {{ formatLimit(planLimits.qrcodes) }}
                    </span>
                </div>
                <div class="w-full bg-surface-200 dark:bg-surface-700 rounded-full h-2">
                    <div 
                        class="h-2 rounded-full transition-all"
                        :class="{
                            'bg-red-500': isAtLimit(stats.qrcodes_count, planLimits.qrcodes),
                            'bg-orange-500': isNearLimit(stats.qrcodes_count, planLimits.qrcodes) && !isAtLimit(stats.qrcodes_count, planLimits.qrcodes),
                            'bg-green-500': !isNearLimit(stats.qrcodes_count, planLimits.qrcodes)
                        }"
                        :style="{ width: qrcodeProgress + '%' }"
                    ></div>
                </div>
            </div>

            <!-- Pages -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2 text-sm">
                        <LucideFileText :size="16" class="text-purple-600 dark:text-purple-400" />
                        <span class="font-medium text-surface-700 dark:text-surface-300">Pagine</span>
                    </div>
                    <span 
                        class="text-sm font-semibold"
                        :class="{
                            'text-red-600 dark:text-red-400': isAtLimit(stats.pages_count, planLimits.pages),
                            'text-orange-600 dark:text-orange-400': isNearLimit(stats.pages_count, planLimits.pages) && !isAtLimit(stats.pages_count, planLimits.pages),
                            'text-surface-700 dark:text-surface-300': !isNearLimit(stats.pages_count, planLimits.pages)
                        }"
                    >
                        {{ stats.pages_count }} / {{ formatLimit(planLimits.pages) }}
                    </span>
                </div>
                <div class="w-full bg-surface-200 dark:bg-surface-700 rounded-full h-2">
                    <div 
                        class="h-2 rounded-full transition-all"
                        :class="{
                            'bg-red-500': isAtLimit(stats.pages_count, planLimits.pages),
                            'bg-orange-500': isNearLimit(stats.pages_count, planLimits.pages) && !isAtLimit(stats.pages_count, planLimits.pages),
                            'bg-purple-500': !isNearLimit(stats.pages_count, planLimits.pages)
                        }"
                        :style="{ width: pageProgress + '%' }"
                    ></div>
                </div>
            </div>
        </div>

        <!-- Warning message se vicini ai limiti -->
        <div 
            v-if="isNearLimit(stats.links_count, planLimits.links) || 
                  isNearLimit(stats.qrcodes_count, planLimits.qrcodes) || 
                  isNearLimit(stats.pages_count, planLimits.pages)"
            class="mt-4 p-3 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg"
        >
            <p class="text-sm text-orange-800 dark:text-orange-200">
                Stai per raggiungere i limiti del tuo piano. 
                <Link 
                    :href="route('plans.index', { tenant: page.props.auth.tenant })"
                    class="font-medium underline hover:no-underline"
                >
                    Effettua l'upgrade
                </Link>
                per continuare senza interruzioni.
            </p>
        </div>
    </div>
</template>
