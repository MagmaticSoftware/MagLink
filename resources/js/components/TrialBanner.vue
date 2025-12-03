<script setup lang="ts">
import { usePage, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { LucideClock, LucideCrown, LucideSparkles, LucideX } from 'lucide-vue-next';
import type { PageProps } from '@/types/inertia';

const page = usePage<PageProps>();
const dismissed = ref(false);
const loading = ref(false);

const trial = computed(() => page.props.trial as {
    active: boolean;
    expired: boolean;
    days_left: number;
    ends_at: string | null;
});

const billing = computed(() => page.props.billing as {
    isNewUser: boolean;
    hasActiveTrial: boolean;
    canStartTrial: boolean;
    isSubscribed: boolean;
    onFreePlan: boolean;
    currentPlanName: string | null;
    hasAccess: boolean;
});

const showTrialBanner = computed(() => {
    if (dismissed.value) return false;
    // Mostra se è in trial attivo
    if (trial.value?.active) return true;
    // Mostra se può iniziare trial
    if (billing.value?.canStartTrial) return true;
    return false;
});

const bannerType = computed(() => {
    if (billing.value?.canStartTrial) return 'start-trial';
    if (trial.value?.active) {
        if (trial.value.days_left <= 7) return 'trial-ending';
        return 'trial-active';
    }
    return null;
});

const startTrial = () => {
    loading.value = true;
    router.post(route('plans.start-trial', { tenant: page.props.auth.tenant }), {}, {
        onFinish: () => {
            loading.value = false;
        },
    });
};

const dismiss = () => {
    dismissed.value = true;
};
</script>

<template>
    <div v-if="showTrialBanner">
        <!-- Banner per iniziare trial -->
        <div 
            v-if="bannerType === 'start-trial'"
            class="relative bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-6 text-white mb-6"
        >
            <button 
                @click="dismiss" 
                class="absolute top-4 right-4 text-white/70 hover:text-white"
            >
                <LucideX class="w-5 h-5" />
            </button>
            <div class="flex items-center gap-6">
                <LucideCrown class="w-16 h-16 flex-shrink-0" />
                <div class="flex-1">
                    <h3 class="text-xl font-bold mb-1">
                        Benvenuto su MagLink! 🎉
                    </h3>
                    <p class="text-blue-100 mb-4">
                        Inizia subito con 30 giorni di prova gratuita e accedi a tutte le funzionalità premium.
                    </p>
                    <div class="flex gap-4">
                        <button 
                            @click="startTrial"
                            :disabled="loading"
                            class="bg-white text-blue-600 px-6 py-2 rounded-lg font-semibold hover:bg-blue-50 transition-colors disabled:opacity-50"
                        >
                            <span v-if="loading">Attivazione...</span>
                            <span v-else>Attiva Trial Gratuito</span>
                        </button>
                        <Link 
                            :href="route('plans.index', { tenant: page.props.auth.tenant })"
                            class="border border-white/50 text-white px-6 py-2 rounded-lg font-semibold hover:bg-white/10 transition-colors"
                        >
                            Vedi i Piani
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Banner trial attivo -->
        <div 
            v-else-if="bannerType === 'trial-active'"
            class="relative bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-4 text-white mb-6"
        >
            <button 
                @click="dismiss" 
                class="absolute top-3 right-3 text-white/70 hover:text-white"
            >
                <LucideX class="w-4 h-4" />
            </button>
            <div class="flex items-center gap-4">
                <LucideSparkles class="w-10 h-10 flex-shrink-0" />
                <div class="flex-1">
                    <p class="font-medium">
                        <span class="font-bold">Trial attivo!</span> 
                        Ti restano {{ trial.days_left }} giorni per esplorare tutte le funzionalità.
                    </p>
                </div>
                <Link 
                    :href="route('plans.index', { tenant: page.props.auth.tenant })"
                    class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                >
                    Vedi i Piani
                </Link>
            </div>
        </div>

        <!-- Banner trial in scadenza -->
        <div 
            v-else-if="bannerType === 'trial-ending'"
            class="relative bg-gradient-to-r from-orange-500 to-red-500 rounded-xl p-4 text-white mb-6"
        >
            <button 
                @click="dismiss" 
                class="absolute top-3 right-3 text-white/70 hover:text-white"
            >
                <LucideX class="w-4 h-4" />
            </button>
            <div class="flex items-center gap-4">
                <LucideClock class="w-10 h-10 flex-shrink-0 animate-pulse" />
                <div class="flex-1">
                    <p class="font-medium">
                        <span class="font-bold">Il tuo trial scade tra {{ trial.days_left }} giorni!</span> 
                        Scegli un piano per continuare a usare MagLink.
                    </p>
                </div>
                <Link 
                    :href="route('plans.index', { tenant: page.props.auth.tenant })"
                    class="bg-white text-orange-600 px-4 py-2 rounded-lg font-semibold hover:bg-orange-50 transition-colors"
                >
                    Scegli un Piano
                </Link>
            </div>
        </div>
    </div>
</template>
