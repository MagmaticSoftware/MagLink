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
    onProLifetime?: boolean;
    limits?: {
        links: number;
        qrcodes: number;
        pages: number;
    };
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
    return props.billing?.onProLifetime || props.trial?.active || props.subscription?.active || props.billing?.onFreePlan;
});

// Tipo di banner: trial, subscription, free
const bannerType = computed(() => {
    if (props.billing?.onProLifetime) return 'lifetime';
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
    if (bannerType.value === 'lifetime') return 'Pro Lifetime';
    if (bannerType.value === 'trial') return 'Prova Gratuita';
    if (bannerType.value === 'free') return 'Piano Free';
    return props.subscription?.name || props.billing?.currentPlanName || 'Piano Attivo';
});

const billingTypeLabel = computed(() => {
    if (bannerType.value === 'lifetime' || !props.subscription?.billing_type) return '';
    return props.subscription.billing_type === 'yearly' ? 'Annuale' : 'Mensile';
});

const formatLimit = (limit: number | undefined, unlimited: string) => {
    if (limit === -1) return unlimited;
    return limit ?? 0;
};
</script>

<template>
    <div
        v-if="shouldShow"
        class="rounded-xl border border-white/70 bg-white/85 p-3 shadow-sm shadow-surface-900/5 backdrop-blur-md dark:border-surface-700/70 dark:bg-surface-900/80"
    >
        <!-- Header -->
        <div class="mb-2.5 flex items-center justify-between gap-2">
            <div class="flex min-w-0 items-center gap-2">
                <Zap v-if="bannerType === 'trial'" :class="getIconClass()" class="h-4 w-4 shrink-0" />
                <Crown v-else :class="getIconClass()" class="h-4 w-4 shrink-0" />
                <span class="truncate text-[13px] font-semibold text-surface-900 dark:text-white">{{ planName }}</span>
            </div>
            <span
                v-if="billingTypeLabel"
                class="shrink-0 rounded-md bg-surface-100 px-1.5 py-0.5 text-[10px] font-semibold text-surface-600 dark:bg-surface-800 dark:text-surface-300"
            >
                {{ billingTypeLabel }}
            </span>
            <span
                v-else-if="bannerType === 'trial'"
                class="shrink-0 rounded-md bg-surface-100 px-1.5 py-0.5 text-[10px] font-semibold text-surface-600 tabular-nums dark:bg-surface-800 dark:text-surface-300"
            >
                {{ trial.days_left }} gg
            </span>
        </div>

        <!-- Plan limits -->
        <dl class="space-y-1 text-xs">
            <div class="flex items-center justify-between">
                <dt class="text-surface-500 dark:text-surface-400">Link</dt>
                <dd class="font-medium text-surface-800 dark:text-surface-100">{{ formatLimit(billing?.limits?.links, 'Illimitati') }}</dd>
            </div>
            <div class="flex items-center justify-between">
                <dt class="text-surface-500 dark:text-surface-400">QR Code</dt>
                <dd class="font-medium text-surface-800 dark:text-surface-100">{{ formatLimit(billing?.limits?.qrcodes, 'Illimitati') }}</dd>
            </div>
            <div class="flex items-center justify-between">
                <dt class="text-surface-500 dark:text-surface-400">Pagine</dt>
                <dd class="font-medium text-surface-800 dark:text-surface-100">{{ formatLimit(billing?.limits?.pages, 'Illimitate') }}</dd>
            </div>
        </dl>

        <!-- Renewal date (only for active subscriptions) -->
        <p v-if="bannerType === 'subscription' && formattedEndDate" class="mt-2.5 text-[11px] text-surface-500 dark:text-surface-400">
            Rinnovo il {{ formattedEndDate }} · {{ daysRemaining }} {{ daysRemaining === 1 ? 'giorno' : 'giorni' }}
        </p>

        <!-- CTA Button -->
        <button
            v-if="bannerType !== 'lifetime'"
            @click="emit('show-plan-modal')"
            :class="bannerType === 'trial' || bannerType === 'free'
                ? 'bg-primary-500 text-white shadow-sm shadow-primary-500/30 hover:bg-primary-600'
                : 'border border-surface-200 bg-white text-surface-700 hover:bg-surface-50 dark:border-surface-600 dark:bg-surface-800 dark:text-surface-100 dark:hover:bg-surface-700'"
            class="mt-3 inline-flex w-full cursor-pointer items-center justify-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors"
        >
            <CreditCard class="h-3.5 w-3.5" />
            {{ bannerType === 'trial' || bannerType === 'free' ? 'Scegli piano' : 'Gestisci piano' }}
        </button>
    </div>
</template>
