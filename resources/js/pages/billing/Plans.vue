<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { LucideCheck, LucideCrown, LucideRocket, LucideBuilding, LucideSparkles } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { PageProps } from '@/types/inertia';

interface PlanFeatures {
    name: string;
    description: string;
    features: string[];
    limits?: {
        links: number;
        qrcodes: number;
        pages: number;
    };
    popular?: boolean;
    monthly: {
        price: number;
        currency: string;
        price_id: string;
    };
    yearly?: {
        price: number;
        currency: string;
        price_id: string;
        discount: number;
    };
}

interface Props {
    plans: Record<string, PlanFeatures>;
    isNewUser: boolean;
    hasActiveTrial: boolean;
    canStartTrial: boolean;
    currentPlan: string | null;
    currentPlanKey: string | null;
    isSubscribed: boolean;
    onFreePlan: boolean;
    subscriptionEndsAt: string | null;
    trialEndsAt: string | null;
}

const props = defineProps<Props>();
const page = usePage<PageProps>();

const billingCycle = ref<'monthly' | 'yearly'>('monthly');
const loading = ref<string | null>(null);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Piani e Prezzi', href: '#' },
];

const formatPrice = (priceInCents: number, currency: string = 'EUR') => {
    return new Intl.NumberFormat('it-IT', {
        style: 'currency',
        currency: currency,
    }).format(priceInCents / 100);
};

const getMonthlyEquivalent = (yearlyPrice: number) => {
    return Math.round(yearlyPrice / 12);
};

const planIcons: Record<string, any> = {
    free: LucideSparkles,
    professional: LucideRocket,
    enterprise: LucideBuilding,
};

const startTrial = () => {
    loading.value = 'trial';
    router.post(route('plans.start-trial', { tenant: page.props.auth.tenant }), {}, {
        onFinish: () => {
            loading.value = null;
        },
    });
};

const selectPlan = (planKey: string) => {
    if (planKey === 'free') {
        // Per il piano gratuito, usa POST alla route dedicata
        loading.value = planKey;
        router.post(route('checkout.free', { 
            tenant: page.props.auth.tenant
        }), {}, {
            onFinish: () => {
                loading.value = null;
            },
        });
        return;
    }
    
    loading.value = planKey;
    // Redirect al checkout Stripe
    window.location.href = route('checkout', { 
        tenant: page.props.auth.tenant,
        plan: planKey,
        billing: billingCycle.value
    });
};

const currentPlanKey = computed(() => {
    return props.currentPlanKey;
});

const canSelectPlan = (planKey: string) => {
    // Piano attuale - sempre disabilitato
    if (currentPlanKey.value === planKey) return false;
    
    // Piano free - sempre selezionabile se non è il piano attuale
    if (planKey === 'free') return true;
    
    // Se l'utente ha una subscription Stripe attiva (non trial, non free)
    // può fare upgrade al piano enterprise, ma non può cambiare tra piani a pagamento
    if (props.isSubscribed && !props.hasActiveTrial) {
        // Può solo fare upgrade da professional a enterprise
        if (currentPlanKey.value === 'professional' && planKey === 'enterprise') {
            return false; // Bloccato - deve contattare assistenza
        }
        // Tutti gli altri cambi tra piani a pagamento sono bloccati
        if (planKey !== 'free' && currentPlanKey.value !== 'free') {
            return false;
        }
    }
    
    return true;
};

const getPlanButtonText = (planKey: string) => {
    if (currentPlanKey.value === planKey) return 'Piano Attuale';
    if (planKey === 'free') return 'Passa a Gratis';
    
    // Se ha una subscription attiva e vuole cambiare tra piani a pagamento
    if (props.isSubscribed && !props.hasActiveTrial && planKey !== 'free' && currentPlanKey.value !== 'free') {
        return 'Contatta Assistenza';
    }
    
    // Determina se è un upgrade o downgrade
    const planOrder = ['free', 'professional', 'enterprise'];
    const currentIndex = planOrder.indexOf(currentPlanKey.value || '');
    const targetIndex = planOrder.indexOf(planKey);
    
    if (targetIndex > currentIndex) return `Upgrade a ${props.plans[planKey].name}`;
    if (targetIndex < currentIndex) return `Downgrade a ${props.plans[planKey].name}`;
    
    return `Scegli ${props.plans[planKey].name}`;
};

const formatDate = (date: string | null) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('it-IT', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Piani e Prezzi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Scegli il piano perfetto per te
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    Inizia con una prova gratuita di 30 giorni, poi scegli il piano più adatto alle tue esigenze.
                </p>
            </div>

            <!-- Current Plan Info Banner -->
            <div 
                v-if="currentPlanKey" 
                class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 rounded-xl p-6 mb-8"
            >
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            Piano Attivo: {{ currentPlan }}
                        </h3>
                        <div class="space-y-1 text-sm text-gray-600 dark:text-gray-300">
                            <p v-if="hasActiveTrial && trialEndsAt">
                                ⏰ Il tuo periodo di prova termina il <strong>{{ formatDate(trialEndsAt) }}</strong>
                            </p>
                            <p v-else-if="isSubscribed && subscriptionEndsAt">
                                📅 Prossimo rinnovo: <strong>{{ formatDate(subscriptionEndsAt) }}</strong>
                            </p>
                            <p v-else-if="onFreePlan">
                                ✨ Stai usando il piano gratuito
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trial Banner (se può iniziare trial) -->
            <div 
                v-if="canStartTrial" 
                class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 mb-12 text-white text-center"
            >
                <LucideCrown class="w-12 h-12 mx-auto mb-4" />
                <h2 class="text-2xl font-bold mb-2">Prova tutte le funzionalità gratuitamente!</h2>
                <p class="text-blue-100 mb-6 max-w-xl mx-auto">
                    Inizia subito con 30 giorni di prova gratuita. Accesso completo a tutte le funzionalità premium, senza carta di credito.
                </p>
                <button 
                    @click="startTrial"
                    :disabled="loading === 'trial'"
                    class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors disabled:opacity-50"
                >
                    <span v-if="loading === 'trial'">Attivazione...</span>
                    <span v-else>Inizia il Trial Gratuito</span>
                </button>
            </div>

            <!-- Billing Cycle Toggle -->
            <div class="flex justify-center mb-8">
                <div class="bg-gray-100 dark:bg-gray-800 p-1 rounded-lg inline-flex">
                    <button
                        @click="billingCycle = 'monthly'"
                        :class="[
                            'px-6 py-2 rounded-md font-medium transition-all',
                            billingCycle === 'monthly' 
                                ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow' 
                                : 'text-gray-600 dark:text-gray-400'
                        ]"
                    >
                        Mensile
                    </button>
                    <button
                        @click="billingCycle = 'yearly'"
                        :class="[
                            'px-6 py-2 rounded-md font-medium transition-all',
                            billingCycle === 'yearly' 
                                ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow' 
                                : 'text-gray-600 dark:text-gray-400'
                        ]"
                    >
                        Annuale
                        <span class="ml-2 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                            -17%
                        </span>
                    </button>
                </div>
            </div>

            <!-- Plans Grid -->
            <div class="grid md:grid-cols-3 gap-8">
                <div 
                    v-for="(plan, key) in plans" 
                    :key="key"
                    :class="[
                        'relative bg-white dark:bg-gray-800 rounded-2xl p-8 border-2 transition-all',
                        plan.popular 
                            ? 'border-blue-500 shadow-xl scale-105' 
                            : 'border-gray-200 dark:border-gray-700 hover:border-blue-300',
                        currentPlanKey === key ? 'ring-2 ring-green-500' : ''
                    ]"
                >
                    <!-- Popular Badge -->
                    <div 
                        v-if="plan.popular" 
                        class="absolute -top-4 left-1/2 -translate-x-1/2 bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium"
                    >
                        Più Popolare
                    </div>

                    <!-- Current Plan Badge -->
                    <div 
                        v-if="currentPlanKey === key" 
                        class="absolute -top-4 right-4 bg-green-500 text-white px-4 py-1 rounded-full text-sm font-medium"
                    >
                        Piano Attuale
                    </div>

                    <!-- Plan Header -->
                    <div class="text-center mb-6">
                        <component 
                            :is="planIcons[key] || LucideSparkles" 
                            class="w-12 h-12 mx-auto mb-4 text-blue-500"
                        />
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ plan.name }}
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">
                            {{ plan.description }}
                        </p>
                    </div>

                    <!-- Price -->
                    <div class="text-center mb-6">
                        <template v-if="key === 'free'">
                            <div class="text-5xl font-bold text-gray-900 dark:text-white">
                                Gratis
                            </div>
                            <div class="text-gray-500 dark:text-gray-400 mt-1">
                                Per sempre
                            </div>
                        </template>
                        <template v-else>
                            <div class="text-5xl font-bold text-gray-900 dark:text-white">
                                {{ billingCycle === 'yearly' && plan.yearly 
                                    ? formatPrice(getMonthlyEquivalent(plan.yearly.price))
                                    : formatPrice(plan.monthly.price) 
                                }}
                            </div>
                            <div class="text-gray-500 dark:text-gray-400 mt-1">
                                /mese
                                <span v-if="billingCycle === 'yearly'" class="text-sm">
                                    (fatturato annualmente)
                                </span>
                            </div>
                            <div 
                                v-if="billingCycle === 'yearly' && plan.yearly" 
                                class="text-sm text-green-600 dark:text-green-400 mt-2"
                            >
                                Risparmi {{ formatPrice(plan.monthly.price * 12 - plan.yearly.price) }}/anno
                            </div>
                        </template>
                    </div>

                    <!-- Features -->
                    <ul class="space-y-3 mb-8">
                        <li 
                            v-for="feature in plan.features" 
                            :key="feature"
                            class="flex items-start"
                        >
                            <LucideCheck class="w-5 h-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" />
                            <span class="text-gray-700 dark:text-gray-300">{{ feature }}</span>
                        </li>
                    </ul>

                    <!-- CTA Button -->
                    <div>
                        <button
                            @click="selectPlan(key)"
                            :disabled="loading !== null || !canSelectPlan(key)"
                            :class="[
                                'w-full py-3 px-6 rounded-lg font-semibold transition-all',
                                plan.popular 
                                    ? 'bg-blue-600 hover:bg-blue-700 text-white' 
                                    : key === 'free'
                                        ? 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white'
                                        : 'bg-gray-900 hover:bg-gray-800 dark:bg-white dark:hover:bg-gray-100 text-white dark:text-gray-900',
                                (loading !== null || !canSelectPlan(key)) ? 'opacity-50 cursor-not-allowed' : ''
                            ]"
                        >
                            <span v-if="loading === key">Caricamento...</span>
                            <span v-else>{{ getPlanButtonText(key) }}</span>
                        </button>
                        
                        <!-- Warning message per upgrade/downgrade tra piani a pagamento -->
                        <p 
                            v-if="isSubscribed && !hasActiveTrial && key !== 'free' && currentPlanKey !== 'free' && currentPlanKey !== key"
                            class="text-xs text-center text-orange-600 dark:text-orange-400 mt-2"
                        >
                            Per modificare il piano contatta l'assistenza
                        </p>
                    </div>
                </div>
            </div>

            <!-- FAQ or Additional Info -->
            <div class="mt-16 text-center">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                    Domande frequenti
                </h3>
                <div class="max-w-2xl mx-auto text-left space-y-6">
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">
                            Posso cambiare piano in qualsiasi momento?
                        </h4>
                        <p class="text-gray-600 dark:text-gray-400">
                            Sì! Puoi passare a un piano superiore o inferiore quando vuoi. Le modifiche saranno applicate immediatamente.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">
                            Cosa succede dopo il trial?
                        </h4>
                        <p class="text-gray-600 dark:text-gray-400">
                            Al termine dei 30 giorni di prova, dovrai scegliere un piano per continuare. Puoi sempre iniziare con il piano gratuito!
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">
                            I pagamenti sono sicuri?
                        </h4>
                        <p class="text-gray-600 dark:text-gray-400">
                            Assolutamente! Utilizziamo Stripe per elaborare tutti i pagamenti, garantendo massima sicurezza e conformità PCI.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
