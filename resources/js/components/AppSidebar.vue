<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, LayoutTemplate, Link as LinkIcon, QrCode, Settings, HelpCircle, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useDark } from '@vueuse/core';
import { useI18n } from 'vue-i18n';
import { useRoute } from '@/composables/useRoute';
import ApplicationLogo from './ApplicationLogo.vue';
import SidebarTrialBanner from './SidebarTrialBanner.vue';
import PlanSelectionModal from './PlanSelectionModal.vue';
import type { PageProps } from '@/types/inertia';

const { t } = useI18n();

// Drawer mobile: su schermi < md la sidebar è nascosta e si apre dal pulsante menu della top bar
const props = withDefaults(defineProps<{ mobileOpen?: boolean }>(), { mobileOpen: false });
const emit = defineEmits<{ (e: 'close'): void }>();

// Stesse opzioni di AppTopBar, così il logo segue il toggle del tema
const isDark = useDark({
    selector: 'html',
    attribute: 'class',
    valueDark: 'dark',
    valueLight: 'light',
});
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
    currentPlanKey: page.props.billing?.currentPlanKey || null,
    onFreePlan: page.props.billing?.onFreePlan || false,
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

// Chiude il drawer quando si naviga verso un'altra pagina
watch(() => page.url, () => emit('close'));

const isActive = (href: string) => {
    return route().current(href.split('.')[0] + '.*');
};
</script>

<template>
    <!-- Overlay mobile -->
    <div
        v-if="props.mobileOpen"
        class="fixed inset-0 z-30 bg-black/40 backdrop-blur-sm md:hidden"
        @click="emit('close')"
    ></div>
    <aside
        class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-gradient-to-b from-surface-50 to-surface-100 dark:from-surface-900 dark:to-surface-950 shadow-lg transition-transform duration-200 md:z-10 md:translate-x-0"
        :class="props.mobileOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <!-- Header con Logo -->
        <div class="flex h-16 items-center justify-between px-6">
            <Link :href="route('tenant.index')">
                <ApplicationLogo class="h-12" :is-dark="isDark" />
            </Link>
            <button
                type="button"
                class="md:hidden flex h-9 w-9 items-center justify-center rounded-lg text-surface-600 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-800"
                :aria-label="t('common.close')"
                @click="emit('close')"
            >
                <X :size="20" />
            </button>
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
            :current-plan-key="planModalData.currentPlanKey"
            :on-free-plan="planModalData.onFreePlan" 
        />
    </aside>
</template>
