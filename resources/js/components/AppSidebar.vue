<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, LayoutTemplate, Link as LinkIcon, QrCode, Settings, HelpCircle, X, UtensilsCrossed } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useDark } from '@vueuse/core';
import { useI18n } from 'vue-i18n';
import { useRoute } from '@/composables/useRoute';
import ApplicationLogo from './ApplicationLogo.vue';
import SidebarTrialBanner from './SidebarTrialBanner.vue';
import PlanSelectionModal from './PlanSelectionModal.vue';
import type { PageProps } from '@/types/inertia';
import sidebarMountains from '../../images/dashboard/sidebar-mountains.webp';

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
    {
        label: t('nav.menu'),
        href: 'menu.index',
        icon: UtensilsCrossed,
        badge: t('nav.comingSoon'),
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
        class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col overflow-hidden border-r border-surface-200/80 bg-white transition-transform duration-200 md:z-10 md:translate-x-0 dark:border-surface-800 dark:bg-surface-900"
        :class="props.mobileOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <!-- Immagine decorativa in fondo alla sidebar, sfumata verso l'alto per non disturbare la lettura -->
        <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 bottom-0 h-80">
            <img :src="sidebarMountains" alt="" class="h-full w-full object-cover object-[70%_center] dark:opacity-50" />
            <div class="absolute inset-0 bg-gradient-to-b from-white from-5% via-white/45 via-45% to-white/0 dark:from-surface-900 dark:via-surface-900/60 dark:to-surface-900/10"></div>
        </div>

        <!-- Header con Logo -->
        <div class="relative flex h-16 items-center justify-between px-5">
            <Link :href="route('tenant.index')">
                <ApplicationLogo class="h-11" :is-dark="isDark" />
            </Link>
            <button
                type="button"
                class="md:hidden flex h-9 w-9 items-center justify-center rounded-lg text-surface-600 dark:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-800"
                :aria-label="t('common.close')"
                @click="emit('close')"
            >
                <X :size="20" />
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="relative flex-1 overflow-y-auto px-3 py-4">
            <ul class="space-y-0.5">
                <li v-for="item in mainNavItems" :key="item.href">
                    <Link
                        :href="route().has(item.href) ? route(item.href) : item.href"
                        class="relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors duration-150"
                        :class="isActive(item.href)
                            ? 'bg-surface-100 font-semibold text-surface-900 shadow-[inset_0_0_0_1px_rgb(0_0_0/0.03)] dark:bg-white/[0.07] dark:text-white'
                            : 'font-medium text-surface-600 hover:bg-surface-50 hover:text-surface-900 dark:text-surface-300 dark:hover:bg-white/[0.04] dark:hover:text-white'"
                    >
                        <!-- Indicatore della voce attiva -->
                        <span
                            v-if="isActive(item.href)"
                            aria-hidden="true"
                            class="absolute top-1/2 -left-3 h-5 w-[3px] -translate-y-1/2 rounded-r-full bg-primary-500"
                        ></span>
                        <component
                            :is="item.icon"
                            :size="18"
                            :stroke-width="isActive(item.href) ? 2.25 : 1.75"
                            :class="isActive(item.href) ? 'text-primary-500' : ''"
                        />
                        <span class="flex-1">{{ item.label }}</span>
                        <span
                            v-if="item.badge"
                            class="rounded-md bg-gradient-to-r from-primary-500 to-rose-500 px-1.5 py-0.5 text-[9px] font-bold tracking-wide text-white uppercase"
                        >
                            {{ item.badge }}
                        </span>
                    </Link>
                </li>
            </ul>
        </nav>

        <!-- Footer con Links Secondari: rialzato per lasciare visibile lo sfondo (solo su schermi alti) -->
        <div class="relative p-3 [@media(min-height:760px)]:pb-40">
            <div class="mb-3 space-y-0.5">
                <Link
                    :href="route('profile.edit')"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-surface-600 transition-colors hover:bg-white/70 hover:text-surface-900 dark:text-surface-300 dark:hover:bg-surface-800/70 dark:hover:text-white"
                >
                    <Settings :size="18" :stroke-width="1.75" />
                    <span>{{ t('common.settings') }}</span>
                </Link>
                <Link
                    :href="route('help.index')"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-surface-600 transition-colors hover:bg-white/70 hover:text-surface-900 dark:text-surface-300 dark:hover:bg-surface-800/70 dark:hover:text-white"
                >
                    <HelpCircle :size="18" :stroke-width="1.75" />
                    <span>{{ t('common.help') }}</span>
                </Link>
            </div>

            <!-- Subscription/Trial Banner -->
            <SidebarTrialBanner
                :trial="trial"
                :subscription="subscription"
                :billing="billing"
                @show-plan-modal="showPlanModal = true"
            />
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
