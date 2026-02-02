<script setup lang="ts">
import { CreditCard, Zap, Crown, Calendar } from 'lucide-vue-next';
import { computed } from 'vue';

interface TrialData {
    active: boolean;
    expired: boolean;
    days_left: number;
    ends_at: string | null;
}

interface SubscriptionData {
    name: string | null;
    key: string | null;
    billing_type: string | null;
    active: boolean;
    ends_at: string | null;
    current_period_end?: string | null;
    created_at: string | null;
    stripe_price: string | null;
    on_grace_period: boolean;
    cancelled: boolean;
}

interface BillingData {
    currentPlanName: string | null;
    currentPlanKey: string | null;
    isSubscribed: boolean;
    onFreePlan: boolean;
}

const props = defineProps<{
    trial: TrialData;
    subscription?: SubscriptionData | null;
    billing?: BillingData;
}>();

const emit = defineEmits<{
    'show-plan-modal': [];
}>();

// Determina se mostrare il banner (sempre, tranne se non c'è né trial né subscription)
const shouldShow = computed(() => {
    return props.trial?.active || props.subscription?.active || props.billing?.onFreePlan;
});

// Tipo di banner: trial, subscription, free
const bannerType = computed(() => {
    if (props.trial?.active) return 'trial';
    if (props.subscription?.active) return 'subscription';
    if (props.billing?.onFreePlan) return 'free';
    return 'none';
});

// Calcola i giorni rimanenti per subscription
const daysRemaining = computed(() => {
    if (props.trial?.active) {
        return props.trial.days_left;
    }
    
    // Per subscription attive, usa current_period_end invece di ends_at
    const endDateStr = props.subscription?.current_period_end || props.subscription?.ends_at;
    if (endDateStr) {
        const endDate = new Date(endDateStr);
        const now = new Date();
        const diff = endDate.getTime() - now.getTime();
        return Math.ceil(diff / (1000 * 60 * 60 * 24));
    }
    
    return 0;
});

// Formatta la data di scadenza
const formattedEndDate = computed(() => {
    let dateStr;
    if (props.trial?.active) {
        dateStr = props.trial.ends_at;
    } else if (props.subscription) {
        dateStr = props.subscription.current_period_end || props.subscription.ends_at;
    }
    
    if (!dateStr) return null;
    
    const date = new Date(dateStr);
    return new Intl.DateTimeFormat('it-IT', { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric' 
    }).format(date);
});

const getBannerClass = () => {
    if (bannerType.value === 'trial') {
        if (props.trial.days_left > 7) {
            return 'from-blue-500/10 to-blue-600/10 border-blue-500/20';
        } else if (props.trial.days_left > 3) {
            return 'from-yellow-500/10 to-yellow-600/10 border-yellow-500/20';
        } else {
            return 'from-red-500/10 to-red-600/10 border-red-500/20';
        }
    } else if (bannerType.value === 'free') {
        return 'from-gray-500/10 to-gray-600/10 border-gray-500/20';
    } else {
        return 'from-green-500/10 to-green-600/10 border-green-500/20';
    }
};

const getTextClass = () => {
    if (bannerType.value === 'trial') {
        if (props.trial.days_left > 7) {
            return 'text-blue-700 dark:text-blue-300';
        } else if (props.trial.days_left > 3) {
            return 'text-yellow-700 dark:text-yellow-300';
        } else {
            return 'text-red-700 dark:text-red-300';
        }
    } else if (bannerType.value === 'free') {
        return 'text-gray-700 dark:text-gray-300';
    } else {
        return 'text-green-700 dark:text-green-300';
    }
};

const getIconClass = () => {
    if (bannerType.value === 'trial') {
        if (props.trial.days_left > 7) {
            return 'text-blue-500';
        } else if (props.trial.days_left > 3) {
            return 'text-yellow-500';
        } else {
            return 'text-red-500';
        }
    } else if (bannerType.value === 'free') {
        return 'text-gray-500';
    } else {
        return 'text-green-500';
    }
};

const planName = computed(() => {
    if (bannerType.value === 'trial') return 'Prova Gratuita';
    if (bannerType.value === 'free') return 'Piano Free';
    return props.subscription?.name || props.billing?.currentPlanName || 'Piano Attivo';
});

const billingTypeLabel = computed(() => {
    if (!props.subscription?.billing_type) return '';
    return props.subscription.billing_type === 'yearly' ? ' (Annuale)' : ' (Mensile)';
});
</script>

<template>
    <div v-if="shouldShow" 
        :class="getBannerClass()" 
        class="bg-gradient-to-br border rounded-lg p-3 mx-2 mb-4 relative overflow-hidden">
        
        <!-- Background pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, currentColor 1px, transparent 0); background-size: 12px 12px;"></div>
        </div>
        
        <div class="relative">
            <!-- Header -->
            <div class="flex items-center mb-3">
                <div class="flex items-center">
                    <Zap v-if="bannerType === 'trial'" :class="getIconClass()" class="w-4 h-4 mr-2" />
                    <Crown v-else :class="getIconClass()" class="w-4 h-4 mr-2" />
                    <span :class="getTextClass()" class="text-xs font-bold uppercase tracking-wide">{{ planName }}{{ billingTypeLabel }}</span>
                </div>
            </div>
            
            <!-- Plan limits -->
            <div class="mb-3 space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span :class="getTextClass()" class="opacity-75">Link</span>
                    <span :class="getTextClass()" class="font-semibold">
                        {{ billing?.limits?.links === -1 ? 'Illimitati' : billing?.limits?.links || 0 }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span :class="getTextClass()" class="opacity-75">QR Code</span>
                    <span :class="getTextClass()" class="font-semibold">
                        {{ billing?.limits?.qrcodes === -1 ? 'Illimitati' : billing?.limits?.qrcodes || 0 }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span :class="getTextClass()" class="opacity-75">Pagine</span>
                    <span :class="getTextClass()" class="font-semibold">
                        {{ billing?.limits?.pages === -1 ? 'Illimitate' : billing?.limits?.pages || 0 }}
                    </span>
                </div>
            </div>
            
            <!-- Renewal date (only for active subscriptions) -->
            <div v-if="bannerType === 'subscription' && formattedEndDate" class="mb-3 text-center">
                <p :class="getTextClass()" class="text-xs opacity-75">
                    Rinnovo il {{ formattedEndDate }}
                </p>
                <p :class="getTextClass()" class="text-xs font-semibold mt-0.5">
                    {{ daysRemaining }} {{ daysRemaining === 1 ? 'giorno' : 'giorni' }} rimanenti
                </p>
            </div>
            
            <!-- CTA Button -->
            <button 
                @click="emit('show-plan-modal')"
                :class="bannerType === 'trial' || bannerType === 'free' 
                    ? 'from-green-500 to-green-600 hover:from-green-600 hover:to-green-700' 
                    : 'from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700'"
                class="w-full inline-flex items-center justify-center px-3 py-1.5 bg-gradient-to-r text-white text-xs font-semibold rounded-md transition-all duration-200 shadow-sm hover:shadow-md cursor-pointer">
                <CreditCard class="w-3 h-3 mr-1.5" />
                {{ bannerType === 'trial' || bannerType === 'free' ? 'Scegli Piano' : 'Gestisci Piano' }}
            </button>
        </div>
    </div>
</template>

