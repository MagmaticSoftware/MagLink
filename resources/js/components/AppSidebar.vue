<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, LayoutTemplate, Link as LinkIcon, QrCode, Settings, HelpCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRoute } from '@/composables/useRoute';
import ApplicationLogo from './ApplicationLogo.vue';
import SidebarTrialBanner from './SidebarTrialBanner.vue';
import PlanSelectionModal from './PlanSelectionModal.vue';
import type { PageProps } from '@/types/inertia';

const { t } = useI18n();
const page = usePage<PageProps>();
const route = useRoute();

// Trial data from shared props
const trial = computed(() => page.props.trial);

// Subscription data from shared props
const subscription = computed(() => page.props.subscription);

// Billing data from shared props
const billing = computed(() => page.props.billing);

// Plan modal visibility
const showPlanModal = ref(false);

// Dati per la modal dei piani
const planModalData = computed(() => ({
    plans: page.props.plans || {},
    isNewUser: page.props.billing?.isNewUser || false,
    hasActiveTrial: page.props.billing?.hasActiveTrial || false,
    canStartTrial: page.props.billing?.canStartTrial || false,
    isSubscribed: page.props.billing?.isSubscribed || false,
}));

const mainNavItems = computed(() => [
    {
        label: t('nav.dashboard'),
        href: 'tenant.index',
        icon: LayoutGrid,
    },
    {
        label: t('nav.links'),
        href: 'links.index',
        icon: LinkIcon,
    },
    {
        label: t('nav.qrcodes'),
        href: 'qrcodes.index',
        icon: QrCode,
    },
    {
        label: t('nav.pages'),
        href: 'pages.index',
        icon: LayoutTemplate,
    },
]);

const isActive = (href: string) => {
    return route().current(href.split('.')[0] + '.*');
};
</script>

<template>
    <aside class="fixed inset-y-0 left-0 z-10 hidden w-64 flex-col bg-gradient-to-b from-surface-50 to-surface-100 dark:from-surface-900 dark:to-surface-950 md:flex shadow-lg">
        <!-- Header con Logo -->
        <div class="flex h-16 items-center px-6">
            <Link :href="route('tenant.index')">
                <ApplicationLogo class="h-12"/>
            </Link>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto px-3 py-6">
            <ul class="space-y-1">
                <li v-for="item in mainNavItems" :key="item.href">
                    <Link 
                        :href="route().has(item.href) ? route(item.href) : item.href"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 text-surface-700 dark:text-surface-200 transition-all duration-200 hover:bg-primary-100 dark:hover:bg-primary-900"
                        :class="{
                            'bg-primary dark:bg-primary-800 dark:hover:bg-primary-600 text-white hover:bg-primary-600': isActive(item.href)
                        }"
                    >
                        <component :is="item.icon" :size="20" :stroke-width="isActive(item.href) ? 2.5 : 2" />
                        <span class="text-sm font-medium">{{ item.label }}</span>
                    </Link>
                </li>
            </ul>
        </nav>

        <!-- Footer con Links Secondari -->
        <div class="border-t border-surface-200/50 dark:border-surface-700/50 p-3">
            <!-- Subscription/Trial Banner -->
            <SidebarTrialBanner 
                :trial="trial" 
                :subscription="subscription"
                :billing="billing"
                @show-plan-modal="showPlanModal = true" 
            />
            
            <div class="space-y-1">
                <Link 
                    :href="route('profile.edit')"
                    class="flex items-center gap-3 rounded-lg px-4 py-2 text-sm text-surface-600 dark:text-surface-400 hover:bg-white/60 dark:hover:bg-surface-800/60 hover:text-surface-900 dark:hover:text-surface-100 transition-all"
                >
                    <Settings :size="18" />
                    <span>{{ t('common.settings') }}</span>
                </Link>
                <Link 
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-4 py-2 text-sm text-surface-600 dark:text-surface-400 hover:bg-white/60 dark:hover:bg-surface-800/60 hover:text-surface-900 dark:hover:text-surface-100 transition-all"
                >
                    <HelpCircle :size="18" />
                    <span>{{ t('common.help') }}</span>
                </Link>
            </div>
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
    </aside>
</template>
