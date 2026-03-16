<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { LucideCookie, LucideX, LucideSettings2, LucideShieldCheck, LucideBarChart3, LucideMegaphone } from 'lucide-vue-next';
import { useCookieConsent } from '@/composables/useCookieConsent';

const { t } = useI18n();
const { hasDecided, analyticsAllowed, marketingAllowed, acceptAll, rejectAll, saveCustom } = useCookieConsent();

const showPreferences = ref(false);
const customAnalytics = ref(false);
const customMarketing = ref(false);

function openPreferences() {
    customAnalytics.value = analyticsAllowed.value;
    customMarketing.value = marketingAllowed.value;
    showPreferences.value = true;
}

function confirmCustom() {
    saveCustom(customAnalytics.value, customMarketing.value);
    showPreferences.value = false;
}

function handleAcceptAll() {
    acceptAll();
    showPreferences.value = false;
}

function handleRejectAll() {
    rejectAll();
    showPreferences.value = false;
}

onMounted(() => {
    window.addEventListener('open-cookie-preferences', openPreferences);
});

onUnmounted(() => {
    window.removeEventListener('open-cookie-preferences', openPreferences);
});
</script>

<template>
    <!-- Cookie Banner -->
    <Transition
        enter-active-class="transition-all duration-500 ease-out"
        enter-from-class="translate-y-8 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition-all duration-300 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-8 opacity-0"
    >
        <div
            v-if="!hasDecided"
            class="fixed bottom-0 left-0 right-0 z-[100] p-4 sm:p-6"
            role="region"
            aria-label="Cookie consent banner"
        >
            <div class="max-w-5xl mx-auto bg-gray-900 border border-white/10 rounded-2xl shadow-2xl shadow-black/60 px-6 py-5">
                <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                    <!-- Icon + text -->
                    <div class="flex items-start gap-4 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-orange-600/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <LucideCookie :size="20" class="text-orange-500" />
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-white text-sm">{{ t('cookieBanner.title') }}</p>
                            <p class="text-gray-400 text-xs mt-0.5 leading-relaxed">
                                {{ t('cookieBanner.description') }}
                                <a href="#" @click.prevent="openPreferences" class="text-orange-500 hover:text-orange-400 underline underline-offset-2">{{ t('cookieBanner.learnMore') }}</a>
                                &nbsp;·&nbsp;<a :href="route('cookie-policy')" class="text-orange-500 hover:text-orange-400 underline underline-offset-2">{{ t('cookieBanner.cookiePolicyLink') }}</a>.
                            </p>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center gap-2 flex-shrink-0 flex-wrap">
                        <button
                            @click="openPreferences"
                            class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-white border border-white/10 hover:border-white/20 rounded-lg px-3 py-2 transition-colors"
                        >
                            <LucideSettings2 :size="13" />
                            {{ t('cookieBanner.customize') }}
                        </button>
                        <button
                            @click="handleRejectAll"
                            class="text-xs text-gray-300 hover:text-white border border-white/10 hover:border-white/20 rounded-lg px-3 py-2 transition-colors"
                        >
                            {{ t('cookieBanner.rejectAll') }}
                        </button>
                        <button
                            @click="handleAcceptAll"
                            class="text-xs bg-orange-600 hover:bg-orange-500 text-white font-semibold rounded-lg px-4 py-2 transition-colors"
                        >
                            {{ t('cookieBanner.acceptAll') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>

    <!-- Preferences Modal -->
    <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="showPreferences"
            class="fixed inset-0 z-[110] flex items-end sm:items-center justify-center p-4"
            @click.self="showPreferences = false"
        >
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="showPreferences = false"></div>

            <!-- Modal -->
            <Transition
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="translate-y-4 opacity-0 scale-95"
                enter-to-class="translate-y-0 opacity-100 scale-100"
            >
                <div
                    v-if="showPreferences"
                    class="relative w-full max-w-lg bg-gray-900 border border-white/10 rounded-2xl shadow-2xl overflow-hidden"
                    role="dialog"
                    aria-modal="true"
                    :aria-label="t('cookieBanner.preferences.title')"
                >
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-5 border-b border-white/[0.07]">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-orange-600/20 flex items-center justify-center">
                                <LucideSettings2 :size="18" class="text-orange-500" />
                            </div>
                            <h2 class="font-bold text-white">{{ t('cookieBanner.preferences.title') }}</h2>
                        </div>
                        <button
                            @click="showPreferences = false"
                            class="text-gray-500 hover:text-white transition-colors p-1"
                            :aria-label="t('cookieBanner.preferences.close')"
                        >
                            <LucideX :size="18" />
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-5 space-y-4 max-h-[60vh] overflow-y-auto">
                        <p class="text-gray-400 text-sm leading-relaxed">
                            {{ t('cookieBanner.preferences.description') }}
                            <a :href="route('cookie-policy')" class="text-orange-400 hover:text-orange-300 underline underline-offset-2 text-xs">{{ t('cookieBanner.preferences.cookiePolicyLink') }}</a>
                        </p>

                        <!-- Necessary -->
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-white/[0.04] border border-white/[0.07]">
                            <div class="w-8 h-8 rounded-lg bg-green-600/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <LucideShieldCheck :size="16" class="text-green-500" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-semibold text-sm text-white">{{ t('cookieBanner.preferences.necessary.title') }}</p>
                                    <span class="text-xs text-green-500 font-medium bg-green-500/10 px-2 py-0.5 rounded-full flex-shrink-0">{{ t('cookieBanner.preferences.alwaysOn') }}</span>
                                </div>
                                <p class="text-gray-400 text-xs mt-1 leading-relaxed">{{ t('cookieBanner.preferences.necessary.description') }}</p>
                            </div>
                        </div>

                        <!-- Analytics -->
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-white/[0.04] border border-white/[0.07]">
                            <div class="w-8 h-8 rounded-lg bg-blue-600/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <LucideBarChart3 :size="16" class="text-blue-400" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-semibold text-sm text-white">{{ t('cookieBanner.preferences.analytics.title') }}</p>
                                    <!-- Toggle -->
                                    <button
                                        type="button"
                                        role="switch"
                                        :aria-checked="customAnalytics"
                                        @click="customAnalytics = !customAnalytics"
                                        :class="[
                                            'relative inline-flex h-5 w-9 items-center rounded-full transition-colors flex-shrink-0',
                                            customAnalytics ? 'bg-orange-600' : 'bg-gray-700'
                                        ]"
                                    >
                                        <span
                                            :class="[
                                                'inline-block h-3.5 w-3.5 rounded-full bg-white shadow transition-transform',
                                                customAnalytics ? 'translate-x-4.5' : 'translate-x-0.5'
                                            ]"
                                        ></span>
                                    </button>
                                </div>
                                <p class="text-gray-400 text-xs mt-1 leading-relaxed">{{ t('cookieBanner.preferences.analytics.description') }}</p>
                            </div>
                        </div>

                        <!-- Marketing -->
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-white/[0.04] border border-white/[0.07]">
                            <div class="w-8 h-8 rounded-lg bg-purple-600/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <LucideMegaphone :size="16" class="text-purple-400" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-semibold text-sm text-white">{{ t('cookieBanner.preferences.marketing.title') }}</p>
                                    <!-- Toggle -->
                                    <button
                                        type="button"
                                        role="switch"
                                        :aria-checked="customMarketing"
                                        @click="customMarketing = !customMarketing"
                                        :class="[
                                            'relative inline-flex h-5 w-9 items-center rounded-full transition-colors flex-shrink-0',
                                            customMarketing ? 'bg-orange-600' : 'bg-gray-700'
                                        ]"
                                    >
                                        <span
                                            :class="[
                                                'inline-block h-3.5 w-3.5 rounded-full bg-white shadow transition-transform',
                                                customMarketing ? 'translate-x-4.5' : 'translate-x-0.5'
                                            ]"
                                        ></span>
                                    </button>
                                </div>
                                <p class="text-gray-400 text-xs mt-1 leading-relaxed">{{ t('cookieBanner.preferences.marketing.description') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer actions -->
                    <div class="px-6 py-4 border-t border-white/[0.07] flex flex-col sm:flex-row gap-2 sm:justify-between sm:items-center">
                        <button
                            @click="handleRejectAll"
                            class="text-xs text-gray-400 hover:text-white transition-colors text-left"
                        >
                            {{ t('cookieBanner.rejectAll') }}
                        </button>
                        <div class="flex gap-2">
                            <button
                                @click="confirmCustom"
                                class="flex-1 sm:flex-none text-xs border border-white/10 hover:border-white/20 text-gray-300 hover:text-white rounded-lg px-4 py-2 transition-colors"
                            >
                                {{ t('cookieBanner.preferences.saveCustom') }}
                            </button>
                            <button
                                @click="handleAcceptAll"
                                class="flex-1 sm:flex-none text-xs bg-orange-600 hover:bg-orange-500 text-white font-semibold rounded-lg px-4 py-2 transition-colors"
                            >
                                {{ t('cookieBanner.acceptAll') }}
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>

    <!-- Floating cookie preferences button — always visible, bottom-left -->
    <button
        @click="openPreferences"
        class="fixed bottom-6 left-6 z-50 w-11 h-11 rounded-full bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white border border-white/10 hover:border-white/20 flex items-center justify-center shadow-lg transition-colors duration-200"
        :title="t('cookieBanner.customize')"
        :aria-label="t('cookieBanner.customize')"
    >
        <LucideCookie :size="18" />
    </button>
</template>
