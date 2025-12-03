<script setup lang="ts">
import AppSidebar from '@/components/AppSidebar.vue';
import AppTopBar from '@/components/AppTopBar.vue';
import MadeWithLove from '@/components/MadeWithLove.vue';
import PlanSelectionModal from '@/components/PlanSelectionModal.vue';
import { useI18n } from 'vue-i18n';
import { ref, computed, watch, watchEffect } from 'vue';
import { usePage } from '@inertiajs/vue3';
import type { BreadcrumbItemType } from '@/types';
import type { PageProps } from '@/types/inertia';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const { t } = useI18n();
const page = usePage<PageProps>();

// Gestione modal dei piani
const showPlanModal = ref(false);

// Dati per la modal dei piani
const planModalData = computed(() => ({
    plans: page.props.plans || {},
    isNewUser: page.props.billing?.isNewUser || false,
    hasActiveTrial: page.props.billing?.hasActiveTrial || false,
    canStartTrial: page.props.billing?.canStartTrial || false,
    isSubscribed: page.props.billing?.isSubscribed || false,
}));

// Traccia se abbiamo già mostrato la modal per questo utente in questa sessione
const hasShownModal = ref(false);

// Watch dei dati trial per chiudere automaticamente la modal quando viene attivato
watch(() => page.props.trial?.active, (newTrialActive) => {
    if (newTrialActive) {
        showPlanModal.value = false;
        hasShownModal.value = false;
    }
});

// Watch dello stato subscription per chiudere la modal
watch(() => page.props.billing?.isSubscribed, (newIsSubscribed) => {
    if (newIsSubscribed) {
        showPlanModal.value = false;
        hasShownModal.value = false;
    }
});

// Mostra automaticamente la modal se necessario
watchEffect(() => {
    const user = page.props.auth?.user;
    if (user) {        
        // Mostra la modal solo se:
        // 1. L'utente non ha trial attivo
        // 2. L'utente non è abbonato 
        // 3. L'utente può iniziare trial
        // 4. Non abbiamo già mostrato la modal in questa sessione
        const shouldShowModal = !page.props.trial?.active && 
                               !page.props.billing?.isSubscribed && 
                               planModalData.value.canStartTrial &&
                               !hasShownModal.value;
        
        if (shouldShowModal) {
            showPlanModal.value = true;
            hasShownModal.value = true;
        }
    }
});
</script>

<template>
    <div class="flex min-h-screen w-full bg-surface-50 dark:bg-surface-900">
        <!-- Sidebar -->
        <AppSidebar />

        <!-- Main Content Area -->
        <div class="flex flex-1 flex-col ml-64">
            <!-- Top Bar -->
            <AppTopBar :breadcrumbs="breadcrumbs" />

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <slot />
            </main>

            <!-- Footer -->
            <footer class="bg-transparent">
                <MadeWithLove />
            </footer>
        </div>

        <!-- Plan Selection Modal -->
        <PlanSelectionModal 
            v-model:visible="showPlanModal" 
            :plans="planModalData.plans"
            :is-new-user="planModalData.isNewUser" 
            :has-active-trial="planModalData.hasActiveTrial"
            :can-start-trial="planModalData.canStartTrial" 
            :is-subscribed="planModalData.isSubscribed" 
        />
    </div>
</template>
