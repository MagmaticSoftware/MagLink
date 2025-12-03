<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Dialog from '@/components/volt/Dialog.vue';
import { LucideCheck, LucideX, LucideSparkles, LucideRocket, LucideBuilding, LucideBan, LucideFlag, LucidePiggyBank, LucideArrowUpCircle, LucideLoader2 } from 'lucide-vue-next';
import type { PageProps } from '@/types/inertia';

interface PlanData {
    name: string;
    description: string;
    features: string[];
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

const props = defineProps<{
    plans: Record<string, PlanData>;
    isNewUser: boolean;
    hasActiveTrial: boolean;
    canStartTrial: boolean;
    isSubscribed: boolean;
    visible: boolean;
}>();

const emit = defineEmits<{
    'update:visible': [value: boolean];
}>();

const page = usePage<PageProps>();
const billingType = ref<'monthly' | 'yearly'>('monthly');
const isStartingTrial = ref(false);

const startTrial = () => {
    isStartingTrial.value = true;
    router.post(
        route('plans.start-trial', { tenant: page.props.auth.tenant }),
        {},
        {
            onFinish: () => {
                isStartingTrial.value = false;
            }
        }
    );
};

// Determina se la modal può essere chiusa
const canCloseModal = computed(() => {    
    // Gli utenti abbonati possono sempre chiudere la modal
    if (props.isSubscribed) {
        return true;
    }
    
    // Gli utenti con trial attivo possono chiudere la modal
    if (props.hasActiveTrial) {
        return true;
    }
    
    // Gli utenti nuovi che devono scegliere NON possono chiudere
    if (props.isNewUser && props.canStartTrial) {
        return false;
    }
    
    // Gli utenti con trial scaduto NON possono chiudere
    if (!props.hasActiveTrial && !props.canStartTrial) {
        return false;
    }
    
    // Default: può chiudere
    return true;
});

const formatPrice = (price: number) => {
    return (price / 100).toFixed(2);
};

const getDiscountedPrice = (plan: PlanData, type: 'yearly') => {
    if (type === 'yearly' && plan.yearly?.discount) {
        const monthlyPrice = plan.monthly.price * 12;
        const yearlyPrice = plan.yearly.price;
        return monthlyPrice - yearlyPrice;
    }
    return 0;
};

const planIcons: Record<string, any> = {
    free: LucideSparkles,
    professional: LucideRocket,
    enterprise: LucideBuilding,
};

const closeModal = () => {
    if (canCloseModal.value) {
        emit('update:visible', false);
    }
};
</script>

<template>
    <Dialog 
        :visible="visible"
        @update:visible="$emit('update:visible', $event)"
        :closable="canCloseModal"
        modal
        :dismissableMask="canCloseModal"
        :style="{ width: '90vw', maxWidth: '1200px' }"
        :pt="{
            root: 'border-0 bg-white dark:bg-gray-900 rounded-xl',
            mask: 'bg-black/50 backdrop-blur-sm'
        }"
    >
        <template #container="{ closeCallback }">
            <div class="max-w-7xl mx-auto px-4 lg:px-6 py-6 relative">
                <!-- Close button - solo se la modal può essere chiusa -->
                <button
                    v-if="canCloseModal"
                    @click="closeCallback"
                    class="absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors"
                    aria-label="Chiudi"
                >
                    <LucideX class="w-5 h-5" />
                </button>

                <!-- Header -->
                <div class="text-center mb-1">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Benvenuto in MagLink</h2>
                </div>

                <template v-if="canStartTrial">
                    <!-- Descrizione soft -->
                    <div class="text-center mb-6">
                        <p class="text-base text-gray-700 dark:text-gray-300">
                            Scegli il piano che meglio si adatta alle tue esigenze, oppure inizia subito ad
                            esplorare gratuitamente.
                        </p>

                        <!-- Pulsante Esplora Gratuitamente -->
                        <button 
                            @click="startTrial"
                            :disabled="isStartingTrial"
                            class="cursor-pointer inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 my-4 disabled:opacity-70 disabled:cursor-not-allowed"
                        >
                            <LucideLoader2 v-if="isStartingTrial" class="w-5 h-5 mr-2 animate-spin" />
                            <LucideSparkles v-else class="w-5 h-5 mr-2" />
                            {{ isStartingTrial ? 'Attivazione in corso...' : 'Inizia ad esplorare subito - 30 giorni di prova gratuita' }}
                        </button>

                        <div class="text-xs flex flex-wrap justify-center gap-4 sm:gap-8 text-gray-500 dark:text-gray-400">
                            <span class="flex items-center">
                                <LucideFlag class="w-4 h-4 mr-2" />
                                Accesso immediato
                            </span>
                            <span class="flex items-center">
                                <LucidePiggyBank class="w-4 h-4 mr-2" />
                                Nessun pagamento richiesto
                            </span>
                            <span class="flex items-center">
                                <LucideBan class="w-4 h-4 mr-2" />
                                Cancellazione facile
                            </span>
                            <span class="flex items-center">
                                <LucideArrowUpCircle class="w-4 h-4 mr-2" />
                                Scegli un piano in qualsiasi momento
                            </span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="flex items-center my-5">
                        <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
                        <span class="px-3 text-base text-gray-500 dark:text-gray-400 font-medium uppercase">oppure scegli un piano</span>
                        <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
                    </div>
                </template>

                <!-- Toggle per fatturazione mensile/annuale -->
                <div class="text-center mt-5 mb-8">
                    <div class="inline-flex items-center rounded-lg p-1 bg-gray-100 dark:bg-gray-800">
                        <button 
                            @click="billingType = 'monthly'" 
                            :class="[
                                'px-6 py-2 rounded-md text-sm font-medium transition-colors cursor-pointer',
                                billingType === 'monthly'
                                    ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
                                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                            ]"
                        >
                            Mensile
                        </button>
                        <button 
                            @click="billingType = 'yearly'" 
                            :class="[
                                'px-6 py-2 text-sm font-medium rounded-md transition-colors cursor-pointer',
                                billingType === 'yearly'
                                    ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
                                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                            ]"
                        >
                            Annuale
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                Risparmia 17%
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Grid dei piani -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div 
                        v-for="(plan, planKey) in plans" 
                        :key="planKey" 
                        :class="[
                            'relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg border-2 p-6 flex flex-col',
                            plan.popular ? 'border-blue-500 dark:border-blue-400' : 'border-gray-200 dark:border-gray-700'
                        ]"
                    >
                        <!-- Badge per il piano consigliato -->
                        <div v-if="plan.popular" class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <span class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-1 rounded-full text-sm font-medium">
                                Più Popolare
                            </span>
                        </div>

                        <!-- Header del piano -->
                        <div class="text-center mb-6">
                            <component 
                                :is="planIcons[planKey] || LucideSparkles" 
                                class="w-10 h-10 mx-auto mb-3 text-blue-500"
                            />
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                {{ plan.name }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">
                                {{ plan.description }}
                            </p>

                            <!-- Prezzo -->
                            <div class="flex items-baseline justify-center mb-1">
                                <template v-if="planKey === 'free'">
                                    <span class="text-3xl font-bold text-gray-900 dark:text-white">
                                        Gratis
                                    </span>
                                </template>
                                <template v-else>
                                    <span class="text-3xl font-bold text-gray-900 dark:text-white">
                                        €{{ billingType === 'yearly' && plan.yearly 
                                            ? formatPrice(plan.yearly.price / 12) 
                                            : formatPrice(plan.monthly.price) }}
                                    </span>
                                    <span class="text-base text-gray-500 dark:text-gray-400 ml-1">
                                        /mese
                                    </span>
                                </template>
                            </div>

                            <!-- Sconto annuale -->
                            <div 
                                v-if="billingType === 'yearly' && plan.yearly && getDiscountedPrice(plan, 'yearly') > 0"
                                class="text-sm text-green-600 dark:text-green-400"
                            >
                                Risparmi €{{ formatPrice(getDiscountedPrice(plan, 'yearly')) }} all'anno
                            </div>

                            <!-- Prezzo equivalente mensile per piano annuale -->
                            <div 
                                v-if="billingType === 'yearly' && plan.yearly && planKey !== 'free'" 
                                class="text-xs text-gray-500 dark:text-gray-400"
                            >
                                €{{ formatPrice(plan.yearly.price) }} fatturati annualmente
                            </div>
                        </div>

                        <!-- Features -->
                        <div class="flex-grow mb-4">
                            <ul class="space-y-2">
                                <li v-for="feature in plan.features" :key="feature" class="flex items-start">
                                    <LucideCheck class="flex-shrink-0 w-5 h-5 text-green-500 mt-0.5 mr-3" />
                                    <span class="text-gray-700 text-sm dark:text-gray-300">{{ feature }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- CTA Button -->
                        <div class="mt-auto">
                            <a 
                                v-if="planKey !== 'free'"
                                :href="route('checkout', { tenant: page.props.auth.tenant, plan: planKey, billing: billingType })" 
                                :class="[
                                    'w-full py-3 px-4 rounded-lg font-medium text-center transition-all duration-200 block',
                                    plan.popular
                                        ? 'bg-blue-600 hover:bg-blue-700 text-white'
                                        : 'bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white'
                                ]"
                            >
                                Scegli {{ plan.name }}
                            </a>
                            <Link 
                                v-else
                                :href="route('checkout', { tenant: page.props.auth.tenant, plan: 'free', billing: 'monthly' })" 
                                class="w-full py-3 px-4 rounded-lg font-medium text-center transition-all duration-200 block bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white"
                            >
                                Inizia Gratis
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Sezione informazioni aggiuntive -->
                <div class="mt-6 text-center">
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                        <p class="text-blue-600 dark:text-blue-400 font-medium text-sm">
                            💰 Hai un coupon? Usalo al checkout per ottenere uno sconto extra!
                        </p>
                    </div>
                </div>
            </div>
        </template>
    </Dialog>
</template>
