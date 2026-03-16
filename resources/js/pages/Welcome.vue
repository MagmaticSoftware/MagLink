<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useI18n } from 'vue-i18n';
import type { SharedData } from '@/types';
import { 
    LucideLink2, 
    LucideQrCode, 
    LucideLayout,
    LucideBarChart3,
    LucideShieldCheck,
    LucideShield,
    LucideCheck,
    LucideShare2,
    LucideArrowUp
} from 'lucide-vue-next';
import LanguageSelector from '@/components/LanguageSelector.vue';
import CookieBanner from '@/components/CookieBanner.vue';
import MadeWithLove from '@/components/MadeWithLove.vue';
import heroImage from '../../images/mockup-maglink-index.png';

const showMobileMenu = ref(false);
const page = usePage<SharedData>();
const { t } = useI18n();

const isAuthenticated = computed(() => page.props.auth?.user);
const isAdmin = computed(() => page.props.auth?.isAdmin);
const isTenant = computed(() => page.props.auth?.tenant);
const dashboardUrl = computed(() => {
    if (!isAuthenticated.value) {
        return null;
    }

    if (isAdmin.value) {
        return route('admin.index');
    }

    if (isTenant.value) {
        return route('tenant.index', { tenant: page.props.auth.tenant });
    }

    return route('verification.notice');
});

// Scroll to top
const showScrollTop = ref(false);
const handleScroll = () => { showScrollTop.value = window.scrollY > 400; };
onMounted(() => window.addEventListener('scroll', handleScroll, { passive: true }));
onUnmounted(() => window.removeEventListener('scroll', handleScroll));
const scrollToTop = () => window.scrollTo({ top: 0, behavior: 'smooth' });

// Hero animated word cycling
const currentWord = ref(0);
const wordVisible = ref(true);

const heroWords = computed(() => [
    t('welcome.hero.word1'),
    t('welcome.hero.word2'),
    t('welcome.hero.word3'),
]);

setInterval(() => {
    wordVisible.value = false;
    setTimeout(() => {
        currentWord.value = (currentWord.value + 1) % 3;
        wordVisible.value = true;
    }, 350);
}, 3200);
</script>

<template>
    <Head title="MagLink - Professional URL Shortener">
        <meta name="description" content="MagLink is the all-in-one link management platform. Create branded short links, dynamic QR codes, and beautiful landing pages - with built-in analytics and GDPR compliance." />
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    
    <div class="min-h-screen bg-[#13131c] text-white">
        <!-- Header/Navigation -->
        <header class="bg-[#13131c]/95 backdrop-blur-md sticky top-0 z-50 border-b border-white/[0.07]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-3">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <Link :href="route('home')" class="flex items-center gap-3">
                            <svg width="165" height="44" viewBox="0 0 461 123" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><rect id="Convertito" x="0" y="0" width="460.879" height="122.234" style="fill:none;"/><g><g><path d="M149.577,88.04l0,-54.985l7.509,0l22.291,35.666l-4.302,0l22.214,-35.666l7.508,0l0,54.985l-10.559,0l0,-36.761l2.034,0.626l-15.487,24.872l-7.196,0l-15.486,-24.872l2.112,-0.626l-0,36.761l-10.638,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M230.765,88.822c-3.442,0 -6.544,-0.86 -9.308,-2.581c-2.764,-1.72 -4.928,-4.067 -6.492,-7.039c-1.564,-2.972 -2.346,-6.309 -2.346,-10.012c-0,-3.754 0.782,-7.117 2.346,-10.089c1.564,-2.973 3.728,-5.319 6.492,-7.04c2.764,-1.72 5.866,-2.581 9.308,-2.581c2.711,0 5.136,0.548 7.274,1.643c2.137,1.095 3.845,2.62 5.123,4.575c1.277,1.956 1.968,4.159 2.072,6.609l0,13.61c-0.104,2.503 -0.795,4.719 -2.072,6.648c-1.278,1.929 -2.986,3.455 -5.123,4.576c-2.138,1.121 -4.563,1.681 -7.274,1.681Zm1.877,-9.464c2.868,0 5.188,-0.951 6.961,-2.854c1.773,-1.904 2.659,-4.368 2.659,-7.392c0,-1.981 -0.404,-3.741 -1.212,-5.279c-0.808,-1.539 -1.929,-2.738 -3.363,-3.598c-1.434,-0.861 -3.116,-1.291 -5.045,-1.291c-1.877,0 -3.533,0.43 -4.967,1.291c-1.434,0.86 -2.555,2.059 -3.363,3.598c-0.808,1.538 -1.212,3.298 -1.212,5.279c-0,2.034 0.404,3.82 1.212,5.358c0.808,1.538 1.929,2.737 3.363,3.598c1.434,0.86 3.09,1.29 4.967,1.29Zm9.073,8.682l-0,-10.168l1.642,-9.229l-1.642,-9.073l-0,-9.308l10.168,0l-0,37.778l-10.168,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M276.755,104.778c-4.015,0 -7.561,-0.717 -10.637,-2.151c-3.077,-1.434 -5.527,-3.454 -7.352,-6.061l6.491,-6.492c1.46,1.721 3.09,3.037 4.889,3.95c1.799,0.912 3.976,1.368 6.531,1.368c3.181,0 5.697,-0.808 7.548,-2.424c1.851,-1.617 2.776,-3.859 2.776,-6.727l0,-9.464l1.721,-8.291l-1.643,-8.29l0,-9.934l10.168,0l0,35.823c0,3.754 -0.873,7.026 -2.62,9.816c-1.747,2.79 -4.158,4.967 -7.235,6.531c-3.076,1.564 -6.622,2.346 -10.637,2.346Zm-0.469,-17.755c-3.39,0 -6.44,-0.821 -9.151,-2.463c-2.712,-1.643 -4.837,-3.898 -6.375,-6.766c-1.538,-2.868 -2.307,-6.075 -2.307,-9.62c-0,-3.546 0.769,-6.727 2.307,-9.543c1.538,-2.815 3.663,-5.045 6.375,-6.687c2.711,-1.643 5.761,-2.464 9.151,-2.464c2.816,0 5.305,0.548 7.469,1.643c2.164,1.095 3.872,2.594 5.123,4.497c1.252,1.903 1.93,4.132 2.034,6.687l-0,11.889c-0.104,2.503 -0.795,4.732 -2.073,6.688c-1.277,1.955 -2.998,3.467 -5.162,4.536c-2.164,1.069 -4.628,1.603 -7.391,1.603Zm2.033,-9.307c1.878,-0 3.507,-0.404 4.889,-1.212c1.382,-0.809 2.464,-1.93 3.246,-3.364c0.782,-1.434 1.173,-3.063 1.173,-4.888c-0,-1.877 -0.391,-3.52 -1.173,-4.928c-0.782,-1.408 -1.864,-2.516 -3.246,-3.324c-1.382,-0.808 -3.011,-1.212 -4.889,-1.212c-1.877,-0 -3.519,0.404 -4.927,1.212c-1.408,0.808 -2.503,1.929 -3.285,3.363c-0.782,1.434 -1.173,3.064 -1.173,4.889c-0,1.773 0.391,3.376 1.173,4.81c0.782,1.434 1.877,2.568 3.285,3.402c1.408,0.835 3.05,1.252 4.927,1.252Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M307.337,88.04l0,-54.985l10.637,0l0,54.985l-10.637,0Zm7.822,0l-0,-9.464l28.001,0l-0,9.464l-28.001,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M349.73,88.04l-0,-37.778l10.324,0l0,37.778l-10.324,0Zm5.162,-43.878c-1.669,-0 -3.05,-0.561 -4.145,-1.682c-1.095,-1.121 -1.643,-2.516 -1.643,-4.185c0,-1.616 0.548,-2.998 1.643,-4.145c1.095,-1.147 2.476,-1.721 4.145,-1.721c1.721,0 3.115,0.574 4.184,1.721c1.069,1.147 1.604,2.529 1.604,4.145c-0,1.669 -0.535,3.064 -1.604,4.185c-1.069,1.121 -2.463,1.682 -4.184,1.682Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M394.547,88.04l-0,-21.665c-0,-2.243 -0.704,-4.068 -2.112,-5.475c-1.408,-1.408 -3.233,-2.112 -5.475,-2.112c-1.46,-0 -2.763,0.313 -3.911,0.938c-1.147,0.626 -2.046,1.513 -2.698,2.66c-0.652,1.147 -0.978,2.476 -0.978,3.989l-3.989,-2.034c0,-2.972 0.639,-5.566 1.917,-7.782c1.277,-2.216 3.05,-3.95 5.318,-5.202c2.268,-1.251 4.836,-1.877 7.704,-1.877c2.764,0 5.241,0.691 7.431,2.073c2.19,1.382 3.911,3.181 5.162,5.397c1.251,2.216 1.877,4.601 1.877,7.156l0,23.934l-10.246,0Zm-25.42,0l0,-37.778l10.246,0l0,37.778l-10.246,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M437.174,88.04l-14.626,-19.475l14.548,-18.303l11.81,0l-17.05,20.649l0.391,-5.006l17.442,22.135l-12.515,0Zm-24.09,0l-0,-56.549l10.246,-0l0,56.549l-10.246,0Z" style="fill:#fff;fill-rule:nonzero;"/></g><path d="M25.176,96.816c-8.679,-9.38 -13.986,-21.925 -13.986,-35.699c-0,-29.027 23.566,-52.592 52.592,-52.592c29.027,-0 52.592,23.565 52.592,52.592c0,11.105 -3.449,21.411 -9.334,29.904l-0.048,-2.446c-0.087,-4.064 -0.992,-8.013 -2.564,-11.613c-2.355,-5.4 -6.187,-10.048 -10.968,-13.413c-1.015,-0.717 -2.084,-1.371 -3.186,-1.97l0.378,19.084c1.223,2.212 1.954,4.708 2.01,7.379l0.327,16.807c-2.697,1.806 -5.571,3.37 -8.589,4.659l-0.521,-56.659c-0.085,-4.066 -0.987,-8.013 -2.559,-11.613c-2.353,-5.408 -6.18,-10.05 -10.965,-13.41c-4.771,-3.371 -10.546,-5.447 -16.648,-5.675c-4.059,-0.15 -7.951,0.536 -11.464,1.908c-5.273,2.058 -9.701,5.631 -12.803,10.228c-3.097,4.603 -4.848,10.264 -4.733,16.366l0.469,46.163Zm23.137,14.577c-3.028,-0.931 -5.94,-2.13 -8.708,-3.566l-0.577,-56.636c-0.031,-2.131 0.348,-4.106 1.088,-5.892c1.095,-2.689 2.997,-4.972 5.4,-6.528c2.406,-1.553 5.297,-2.4 8.473,-2.285c2.13,0.079 4.128,0.582 5.97,1.417c2.743,1.256 5.13,3.286 6.82,5.792c1.698,2.503 2.709,5.444 2.772,8.624l0.58,61.01c-2.082,0.251 -4.2,0.38 -6.349,0.38c-0.372,0 -0.742,-0.004 -1.112,-0.012l-0.529,-26.78c-0.034,-2.134 0.349,-4.101 1.078,-5.903c0.188,-0.454 0.419,-0.901 0.647,-1.341l-0.375,-19.087c-4.462,2.122 -8.217,5.377 -10.945,9.43c-3.091,4.597 -4.861,10.255 -4.722,16.366l0.489,25.011Z" style="fill:url(#_Linear1);"/></g><g id="SVGRepo_iconCarrier"></g><defs><linearGradient id="_Linear1" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(8.74093e-15,-105.184,142.75,6.44068e-15,63.7821,113.709)"><stop offset="0" style="stop-color:#ff1f2b;stop-opacity:1"/><stop offset="1" style="stop-color:#ff682f;stop-opacity:1"/></linearGradient></defs></svg>
                        </Link>
                    </div>
                    
                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex items-center space-x-8">
                        <a href="#features" class="text-gray-300 hover:text-orange-600 transition-colors">{{ t('welcome.nav.features') }}</a>
                        <a href="#how-it-works" class="text-gray-300 hover:text-orange-600 transition-colors">{{ t('welcome.nav.howItWorks') }}</a>
                        <a href="#use-cases" class="text-gray-300 hover:text-orange-600 transition-colors">{{ t('welcome.nav.useCases') }}</a>
                        <a href="#pricing" class="text-gray-300 hover:text-orange-600 transition-colors">{{ t('welcome.nav.pricing') }}</a>
                        
                        <div class="flex items-center space-x-4">
                            <!-- Language Selector -->
                            <LanguageSelector />
                            
                            <template v-if="isAuthenticated">
                                <a
                                    :href="dashboardUrl || route('verification.notice')"
                                    class="bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-500 hover:to-orange-800 text-white px-5 py-2.5 rounded-lg font-medium transition-all"
                                >
                                    {{ isAdmin ? t('welcome.nav.adminDashboard') : t('welcome.nav.dashboard') }}
                                </a>
                            </template>
                            <template v-else>
                                <a
                                    :href="route('login')"
                                    class="text-gray-300 hover:text-white transition-colors"
                                >
                                    {{ t('welcome.nav.signIn') }}
                                </a>
                                <a
                                    :href="route('register')"
                                    class="bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-500 hover:to-orange-800 text-white px-5 py-2.5 rounded-lg font-medium transition-all"
                                >
                                    {{ t('welcome.nav.getStarted') }}
                                </a>
                            </template>
                        </div>
                    </nav>
                    
                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button
                            @click="showMobileMenu = !showMobileMenu"
                            class="text-gray-300 hover:text-white"
                        >
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Mobile Navigation -->
                <div v-if="showMobileMenu" class="md:hidden py-4 border-t border-white/[0.08]">
                    <div class="flex flex-col space-y-4">
                        <a href="#features" class="text-gray-300 hover:text-orange-700 transition-colors">{{ t('welcome.nav.features') }}</a>
                        <a href="#how-it-works" class="text-gray-300 hover:text-orange-700 transition-colors">{{ t('welcome.nav.howItWorks') }}</a>
                        <a href="#use-cases" class="text-gray-300 hover:text-orange-700 transition-colors">{{ t('welcome.nav.useCases') }}</a>
                        <a href="#pricing" class="text-gray-300 hover:text-orange-700 transition-colors">{{ t('welcome.nav.pricing') }}</a>
                        
                        <!-- Language Selector Mobile -->
                        <div class="py-2">
                            <LanguageSelector />
                        </div>
                        
                        <div class="flex flex-col space-y-2 pt-4 border-t border-white/[0.08]">
                            <Link
                                v-if="isAuthenticated"
                                :href="dashboardUrl || route('verification.notice')"
                                class="bg-gradient-to-r from-orange-600 to-orange-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center"
                            >
                                {{ isAdmin ? t('welcome.nav.adminDashboard') : t('welcome.nav.dashboard') }}
                            </Link>
                            <template v-else-if="!isAuthenticated">
                                <Link
                                    :href="route('login')"
                                    class="text-gray-300 hover:text-white transition-colors text-center"
                                >
                                    {{ t('welcome.nav.signIn') }}
                                </Link>
                                <a
                                    :href="route('register')"
                                    class="bg-gradient-to-r from-orange-600 to-orange-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center"
                                >
                                    {{ t('welcome.nav.getStarted') }}
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative py-20 lg:py-36 overflow-hidden">
            <div class="absolute inset-0 bg-grid-white/[0.03] bg-[size:50px_50px]"></div>
            <div class="absolute top-0 right-0 w-[700px] h-[700px] bg-orange-500/[0.12] rounded-full blur-[160px] pointer-events-none"></div>
            <div class="absolute bottom-0 left-[-100px] w-[500px] h-[500px] bg-violet-600/[0.06] rounded-full blur-[130px] pointer-events-none"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Left: Text -->
                    <div>
                        <div class="inline-flex items-center gap-2 bg-orange-500/10 border border-orange-500/20 rounded-full px-4 py-1.5 mb-7">
                            <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                            <span class="text-sm text-orange-400 font-medium">{{ t('welcome.hero.badge') }}</span>
                        </div>
                        <h1 class="font-bold mb-6">
                            <span class="block text-2xl sm:text-3xl text-gray-400 font-medium mb-3">{{ t('welcome.hero.title') }}</span>
                            <span
                                class="block text-5xl sm:text-6xl lg:text-7xl text-orange-500 leading-[1.1] pb-1 transition-all duration-300"
                                :style="{ opacity: wordVisible ? '1' : '0', transform: wordVisible ? 'translateY(0)' : 'translateY(10px)' }"
                            >{{ heroWords[currentWord] }}</span>
                            <span class="block text-2xl sm:text-3xl text-gray-400 font-medium mt-3">{{ t('welcome.hero.titleEnd') }}</span>
                        </h1>
                        <p class="text-xl text-gray-300 mb-8 leading-relaxed">
                            {{ t('welcome.hero.subtitle') }}
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a
                                v-if="!isAuthenticated"
                                :href="route('register')"
                                class="inline-flex items-center justify-center bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-500 hover:to-orange-800 text-white px-8 py-4 rounded-lg font-semibold transition-all text-lg"
                            >
                                {{ t('welcome.hero.getStarted') }}
                            </a>
                            <Link
                                v-else
                                :href="dashboardUrl || route('verification.notice')"
                                class="inline-flex items-center justify-center bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-500 hover:to-orange-800 text-white px-8 py-4 rounded-lg font-semibold transition-all text-lg"
                            >
                                {{ t('welcome.hero.goToDashboard') }}
                            </Link>
                        </div>
                    </div>
                    
                    <!-- Right: Hero Image -->
                    <div class="relative flex items-center justify-center">
                        <img :src="heroImage" alt="MagLink Dashboard" class="w-full" />                        
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Banner - TODO: uncomment when real metrics are available
        <section class="py-14 border-y border-white/[0.06] bg-[#191925]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div class="flex flex-col items-center">
                        <span class="text-4xl sm:text-5xl font-bold text-white mb-2">10K+</span>
                        <span class="text-sm text-gray-400 uppercase tracking-wider">{{ t('welcome.stats.links') }}</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-4xl sm:text-5xl font-bold text-white mb-2">99.9<span class="text-orange-500">%</span></span>
                        <span class="text-sm text-gray-400 uppercase tracking-wider">{{ t('welcome.stats.uptime') }}</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-4xl sm:text-5xl font-bold text-white mb-2">50+</span>
                        <span class="text-sm text-gray-400 uppercase tracking-wider">{{ t('welcome.stats.countries') }}</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-4xl sm:text-5xl font-bold text-white mb-2">100<span class="text-orange-500">%</span></span>
                        <span class="text-sm text-gray-400 uppercase tracking-wider">{{ t('welcome.stats.gdpr') }}</span>
                    </div>
                </div>
            </div>
        </section>
        -->

        <!-- Features Section -->
        <section id="features" class="py-24 bg-gradient-to-b from-[#13131c] via-[#191925] to-[#13131c]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20">
                    <span class="inline-block text-xs font-semibold text-orange-500 uppercase tracking-widest mb-4 px-3 py-1 bg-orange-500/10 rounded-full border border-orange-500/20">{{ t('welcome.features.label') }}</span>
                    <h2 class="text-4xl sm:text-5xl font-bold mb-6">
                        {{ t('welcome.features.title') }}
                    </h2>
                    <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                        {{ t('welcome.features.subtitle') }}
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="group p-8 rounded-2xl bg-white/[0.04] backdrop-blur-sm border border-white/[0.09] hover:border-orange-500/40 hover:bg-white/[0.07] transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LucideLink2 :size="28" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.features.customLinks.title') }}</h3>
                        <p class="text-gray-300 leading-relaxed">
                            {{ t('welcome.features.customLinks.description') }}
                        </p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="group p-8 rounded-2xl bg-white/[0.04] backdrop-blur-sm border border-white/[0.09] hover:border-orange-500/40 hover:bg-white/[0.07] transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-600 to-orange-800 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LucideBarChart3 :size="28" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.features.analytics.title') }}</h3>
                        <p class="text-gray-300 leading-relaxed">
                            {{ t('welcome.features.analytics.description') }}
                        </p>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="group p-8 rounded-2xl bg-white/[0.04] backdrop-blur-sm border border-white/[0.09] hover:border-orange-500/40 hover:bg-white/[0.07] transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LucideShield :size="28" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.features.security.title') }}</h3>
                        <p class="text-gray-300 leading-relaxed">
                            {{ t('welcome.features.security.description') }}
                        </p>
                    </div>
                    
                    <!-- Feature 4 -->
                    <div class="group p-8 rounded-2xl bg-white/[0.04] backdrop-blur-sm border border-white/[0.09] hover:border-orange-500/40 hover:bg-white/[0.07] transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LucideQrCode :size="28" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.features.qrGenerator.title') }}</h3>
                        <p class="text-gray-300 leading-relaxed">
                            {{ t('welcome.features.qrGenerator.description') }}
                        </p>
                    </div>
                    
                    <!-- Feature 5 -->
                    <div class="group p-8 rounded-2xl bg-white/[0.04] backdrop-blur-sm border border-white/[0.09] hover:border-orange-500/40 hover:bg-white/[0.07] transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-600 to-orange-800 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LucideLayout :size="28" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.features.blockPages.title') }}</h3>
                        <p class="text-gray-300 leading-relaxed">
                            {{ t('welcome.features.blockPages.description') }}
                        </p>
                    </div>
                    
                    <!-- Feature 6 -->
                    <div class="group p-8 rounded-2xl bg-white/[0.04] backdrop-blur-sm border border-white/[0.09] hover:border-orange-500/40 hover:bg-white/[0.07] transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-600 to-orange-800 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LucideShieldCheck :size="28" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.features.gdpr.title') }}</h3>
                        <p class="text-gray-300 leading-relaxed">
                            {{ t('welcome.features.gdpr.description') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <!-- How It Works Section -->
        <section id="how-it-works" class="py-24 bg-[#191925]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="inline-block text-xs font-semibold text-orange-500 uppercase tracking-widest mb-4 px-3 py-1 bg-orange-500/10 rounded-full border border-orange-500/20">{{ t('welcome.howItWorks.label') }}</span>
                    <h2 class="text-4xl sm:text-5xl font-bold mb-4">
                        {{ t('welcome.howItWorks.title') }}
                    </h2>
                    <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                        {{ t('welcome.howItWorks.subtitle') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                    <!-- connector line -->
                    <div class="hidden md:block absolute top-[3.25rem] left-[calc(33.33%+2rem)] right-[calc(33.33%+2rem)] h-px bg-gradient-to-r from-transparent via-orange-500/40 to-transparent"></div>

                    <!-- Step 1 -->
                    <div class="relative p-8 rounded-2xl bg-white/[0.04] backdrop-blur-sm border border-white/[0.09] hover:border-orange-500/40 hover:bg-white/[0.07] transition-all duration-300 text-center group">
                        <div class="inline-flex w-14 h-14 rounded-xl bg-gradient-to-br from-orange-500/20 to-orange-600/10 border border-orange-500/30 items-center justify-center mb-5 group-hover:border-orange-500/60 transition-colors">
                            <LucideLink2 :size="24" class="text-orange-400" />
                        </div>
                        <div class="absolute top-5 right-6 text-6xl font-black text-white/[0.04] select-none leading-none">01</div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.howItWorks.step1.title') }}</h3>
                        <p class="text-gray-400 leading-relaxed text-sm">{{ t('welcome.howItWorks.step1.description') }}</p>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative p-8 rounded-2xl bg-white/[0.04] backdrop-blur-sm border border-white/[0.09] hover:border-orange-500/40 hover:bg-white/[0.07] transition-all duration-300 text-center group">
                        <div class="inline-flex w-14 h-14 rounded-xl bg-gradient-to-br from-orange-500/20 to-orange-600/10 border border-orange-500/30 items-center justify-center mb-5 group-hover:border-orange-500/60 transition-colors">
                            <LucideShare2 :size="24" class="text-orange-400" />
                        </div>
                        <div class="absolute top-5 right-6 text-6xl font-black text-white/[0.04] select-none leading-none">02</div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.howItWorks.step2.title') }}</h3>
                        <p class="text-gray-400 leading-relaxed text-sm">{{ t('welcome.howItWorks.step2.description') }}</p>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative p-8 rounded-2xl bg-white/[0.04] backdrop-blur-sm border border-white/[0.09] hover:border-orange-500/40 hover:bg-white/[0.07] transition-all duration-300 text-center group">
                        <div class="inline-flex w-14 h-14 rounded-xl bg-gradient-to-br from-orange-500/20 to-orange-600/10 border border-orange-500/30 items-center justify-center mb-5 group-hover:border-orange-500/60 transition-colors">
                            <LucideBarChart3 :size="24" class="text-orange-400" />
                        </div>
                        <div class="absolute top-5 right-6 text-6xl font-black text-white/[0.04] select-none leading-none">03</div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.howItWorks.step3.title') }}</h3>
                        <p class="text-gray-400 leading-relaxed text-sm">{{ t('welcome.howItWorks.step3.description') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Use Cases Section -->
        <section id="use-cases" class="py-24 bg-[#13131c]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl sm:text-5xl font-bold mb-4">
                        {{ t('welcome.useCases.title') }}
                    </h2>
                    <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                        {{ t('welcome.useCases.subtitle') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Marketing -->
                    <div class="p-8 rounded-2xl bg-white/[0.04] backdrop-blur-sm border border-white/[0.09] hover:border-orange-500/40 hover:bg-white/[0.07] transition-all duration-300">
                        <span class="inline-block text-xs font-semibold text-orange-500 uppercase tracking-wider mb-4 px-3 py-1 bg-orange-500/10 rounded-full">{{ t('welcome.useCases.marketing.tag') }}</span>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.useCases.marketing.title') }}</h3>
                        <p class="text-gray-300 leading-relaxed">{{ t('welcome.useCases.marketing.description') }}</p>
                    </div>

                    <!-- Creators -->
                    <div class="p-8 rounded-2xl bg-white/[0.04] backdrop-blur-sm border border-white/[0.09] hover:border-orange-500/40 hover:bg-white/[0.07] transition-all duration-300">
                        <span class="inline-block text-xs font-semibold text-orange-500 uppercase tracking-wider mb-4 px-3 py-1 bg-orange-500/10 rounded-full">{{ t('welcome.useCases.creators.tag') }}</span>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.useCases.creators.title') }}</h3>
                        <p class="text-gray-300 leading-relaxed">{{ t('welcome.useCases.creators.description') }}</p>
                    </div>

                    <!-- Enterprise -->
                    <div class="p-8 rounded-2xl bg-white/[0.04] backdrop-blur-sm border border-white/[0.09] hover:border-orange-500/40 hover:bg-white/[0.07] transition-all duration-300">
                        <span class="inline-block text-xs font-semibold text-orange-500 uppercase tracking-wider mb-4 px-3 py-1 bg-orange-500/10 rounded-full">{{ t('welcome.useCases.enterprise.tag') }}</span>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.useCases.enterprise.title') }}</h3>
                        <p class="text-gray-300 leading-relaxed">{{ t('welcome.useCases.enterprise.description') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section - TODO: uncomment when real testimonials are available
        <section class="py-24 bg-[#191925] relative overflow-hidden">
            <div class="absolute inset-0 bg-grid-white/[0.02] bg-[size:50px_50px]"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center mb-16">
                    <span class="inline-block text-xs font-semibold text-orange-500 uppercase tracking-widest mb-4 px-3 py-1 bg-orange-500/10 rounded-full border border-orange-500/20">{{ t('welcome.testimonials.label') }}</span>
                    <h2 class="text-4xl sm:text-5xl font-bold mb-4">{{ t('welcome.testimonials.title') }}</h2>
                    <p class="text-xl text-gray-300 max-w-2xl mx-auto">{{ t('welcome.testimonials.subtitle') }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="p-8 rounded-2xl bg-white/[0.04] backdrop-blur-sm border border-white/[0.09] hover:border-orange-500/30 hover:bg-white/[0.07] transition-all duration-300 flex flex-col">
                        <div class="flex items-center gap-1 mb-6"><span v-for="i in 5" :key="i" class="text-orange-400 text-lg">★</span></div>
                        <p class="text-gray-300 leading-relaxed flex-1 mb-6 italic">"{{ t('welcome.testimonials.t1.quote') }}"</p>
                        <div class="flex items-center gap-3 pt-4 border-t border-white/[0.08]">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-500 to-amber-400 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">{{ t('welcome.testimonials.t1.initials') }}</div>
                            <div><div class="text-white font-semibold text-sm">{{ t('welcome.testimonials.t1.name') }}</div><div class="text-gray-500 text-xs">{{ t('welcome.testimonials.t1.role') }}</div></div>
                        </div>
                    </div>
                    <div class="p-8 rounded-2xl bg-white/[0.04] backdrop-blur-sm border border-white/[0.09] hover:border-orange-500/30 hover:bg-white/[0.07] transition-all duration-300 flex flex-col">
                        <div class="flex items-center gap-1 mb-6"><span v-for="i in 5" :key="i" class="text-orange-400 text-lg">★</span></div>
                        <p class="text-gray-300 leading-relaxed flex-1 mb-6 italic">"{{ t('welcome.testimonials.t2.quote') }}"</p>
                        <div class="flex items-center gap-3 pt-4 border-t border-white/[0.08]">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-500 to-purple-400 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">{{ t('welcome.testimonials.t2.initials') }}</div>
                            <div><div class="text-white font-semibold text-sm">{{ t('welcome.testimonials.t2.name') }}</div><div class="text-gray-500 text-xs">{{ t('welcome.testimonials.t2.role') }}</div></div>
                        </div>
                    </div>
                    <div class="p-8 rounded-2xl bg-white/[0.04] backdrop-blur-sm border border-white/[0.09] hover:border-orange-500/30 hover:bg-white/[0.07] transition-all duration-300 flex flex-col">
                        <div class="flex items-center gap-1 mb-6"><span v-for="i in 5" :key="i" class="text-orange-400 text-lg">★</span></div>
                        <p class="text-gray-300 leading-relaxed flex-1 mb-6 italic">"{{ t('welcome.testimonials.t3.quote') }}"</p>
                        <div class="flex items-center gap-3 pt-4 border-t border-white/[0.08]">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-400 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">{{ t('welcome.testimonials.t3.initials') }}</div>
                            <div><div class="text-white font-semibold text-sm">{{ t('welcome.testimonials.t3.name') }}</div><div class="text-gray-500 text-xs">{{ t('welcome.testimonials.t3.role') }}</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        -->

        <!-- Pricing Section -->
        <section id="pricing" class="py-24 bg-[#191925]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20">
                    <h2 class="text-4xl sm:text-5xl font-bold mb-6">
                        {{ t('welcome.pricing.title') }}
                    </h2>
                    <p class="text-xl text-gray-400">
                        {{ t('welcome.pricing.subtitle') }}
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Free Plan -->
                    <div class="relative p-8 rounded-2xl bg-white/[0.04] backdrop-blur-sm border border-white/[0.09] hover:border-orange-500/40 hover:bg-white/[0.06] transition-all duration-300">
                        <h3 class="text-2xl font-bold mb-2">{{ t('welcome.pricing.free.name') }}</h3>
                        <div class="flex items-baseline gap-2 mb-6">
                            <span class="text-5xl font-bold">{{ t('welcome.pricing.free.price') }}</span>
                            <span class="text-gray-400">{{ t('welcome.pricing.free.period') }}</span>
                        </div>
                        <p class="text-gray-400 mb-8">{{ t('welcome.pricing.free.description') }}</p>
                        
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-600 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.free.feature1') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-600 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.free.feature2') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-600 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.free.feature3') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-600 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.free.feature4') }}</span>
                            </li>
                        </ul>
                        
                        <a
                            :href="route('register')"
                            class="block w-full text-center bg-white/[0.08] hover:bg-white/[0.14] text-white font-semibold py-3 px-6 rounded-lg transition-colors"
                        >
                            {{ t('welcome.pricing.startNow') }}
                        </a>
                    </div>
                    
                    <!-- Professional Plan - Most Popular -->
                    <div class="relative p-8 rounded-2xl bg-gradient-to-br from-orange-500/10 to-orange-600/5 border-2 border-orange-500 transform scale-105">
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <span class="bg-gradient-to-r from-orange-600 to-orange-800 text-white px-4 py-1 rounded-full text-sm font-semibold">
                                {{ t('welcome.pricing.professional.badge') }}
                            </span>
                        </div>
                        
                        <h3 class="text-2xl font-bold mb-2">{{ t('welcome.pricing.professional.name') }}</h3>
                        <div class="flex items-baseline gap-2 mb-6">
                            <span class="text-5xl font-bold">{{ t('welcome.pricing.professional.price') }}</span>
                            <span class="text-gray-400">{{ t('welcome.pricing.professional.period') }}</span>
                        </div>
                        <p class="text-gray-400 mb-8">{{ t('welcome.pricing.professional.description') }}</p>
                        
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-600 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.professional.feature1') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-600 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.professional.feature2') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-600 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.professional.feature3') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-600 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.professional.feature4') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-600 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.professional.feature5') }}</span>
                            </li>
                        </ul>
                        
                        <a
                            :href="route('register')"
                            class="block w-full text-center bg-gradient-to-r from-orange-600 to-orange-800 hover:from-orange-500 hover:to-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition-all"
                        >
                            {{ t('welcome.pricing.startNow') }}
                        </a>
                    </div>
                    
                    <!-- Enterprise Plan -->
                    <div class="relative p-8 rounded-2xl bg-white/[0.04] backdrop-blur-sm border border-white/[0.09] hover:border-orange-500/40 hover:bg-white/[0.06] transition-all duration-300">
                        <h3 class="text-2xl font-bold mb-2">{{ t('welcome.pricing.enterprise.name') }}</h3>
                        <div class="flex items-baseline gap-2 mb-6">
                            <span class="text-5xl font-bold">{{ t('welcome.pricing.enterprise.price') }}</span>
                            <span class="text-gray-400">{{ t('welcome.pricing.enterprise.period') }}</span>
                        </div>
                        <p class="text-gray-400 mb-8">{{ t('welcome.pricing.enterprise.description') }}</p>
                        
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-600 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.enterprise.feature1') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-600 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.enterprise.feature2') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-600 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.enterprise.feature3') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-600 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.enterprise.feature4') }}</span>
                            </li>
                        </ul>
                        
                        <a
                            :href="route('register')"
                            class="block w-full text-center bg-white/[0.08] hover:bg-white/[0.14] text-white font-semibold py-3 px-6 rounded-lg transition-colors"
                        >
                            {{ t('welcome.pricing.startNow') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="relative py-28 overflow-hidden bg-[#13131c]">
            <div class="absolute inset-0">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 via-transparent to-violet-500/10"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-orange-500/8 rounded-full blur-[120px]"></div>
                <div class="absolute inset-0 bg-grid-white/[0.02] bg-[size:50px_50px]"></div>
            </div>
            <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    {{ t('welcome.cta.title') }}
                    <span class="bg-gradient-to-r from-orange-500 to-amber-400 bg-clip-text text-transparent"> {{ t('welcome.cta.titleHighlight') }}</span>
                </h2>
                <p class="text-xl text-gray-300 mb-10 max-w-2xl mx-auto leading-relaxed">{{ t('welcome.cta.subtitle') }}</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        :href="route('register')"
                        class="inline-flex items-center justify-center px-8 py-4 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 text-lg"
                    >
                        {{ t('welcome.cta.button') }}
                    </Link>
                    <a
                        href="#pricing"
                        class="inline-flex items-center justify-center px-8 py-4 bg-white/[0.08] hover:bg-white/[0.14] border border-white/[0.12] text-white font-semibold rounded-xl transition-all duration-200 text-lg"
                    >
                        {{ t('welcome.cta.seePlans') }}
                    </a>
                </div>
                <p class="text-sm text-gray-500 mt-6">{{ t('welcome.cta.noCreditCard') }}</p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-[#0d0d14] border-t border-white/[0.07] py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                    <!-- Logo + Tagline -->
                    <div>
                        <Link :href="route('home')" class="flex items-center mb-4">
                            <svg width="132" height="35" viewBox="0 0 461 123" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><rect id="Convertito" x="0" y="0" width="460.879" height="122.234" style="fill:none;"/><g><g><path d="M149.577,88.04l0,-54.985l7.509,0l22.291,35.666l-4.302,0l22.214,-35.666l7.508,0l0,54.985l-10.559,0l0,-36.761l2.034,0.626l-15.487,24.872l-7.196,0l-15.486,-24.872l2.112,-0.626l-0,36.761l-10.638,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M230.765,88.822c-3.442,0 -6.544,-0.86 -9.308,-2.581c-2.764,-1.72 -4.928,-4.067 -6.492,-7.039c-1.564,-2.972 -2.346,-6.309 -2.346,-10.012c-0,-3.754 0.782,-7.117 2.346,-10.089c1.564,-2.973 3.728,-5.319 6.492,-7.04c2.764,-1.72 5.866,-2.581 9.308,-2.581c2.711,0 5.136,0.548 7.274,1.643c2.137,1.095 3.845,2.62 5.123,4.575c1.277,1.956 1.968,4.159 2.072,6.609l0,13.61c-0.104,2.503 -0.795,4.719 -2.072,6.648c-1.278,1.929 -2.986,3.455 -5.123,4.576c-2.138,1.121 -4.563,1.681 -7.274,1.681Zm1.877,-9.464c2.868,0 5.188,-0.951 6.961,-2.854c1.773,-1.904 2.659,-4.368 2.659,-7.392c0,-1.981 -0.404,-3.741 -1.212,-5.279c-0.808,-1.539 -1.929,-2.738 -3.363,-3.598c-1.434,-0.861 -3.116,-1.291 -5.045,-1.291c-1.877,0 -3.533,0.43 -4.967,1.291c-1.434,0.86 -2.555,2.059 -3.363,3.598c-0.808,1.538 -1.212,3.298 -1.212,5.279c-0,2.034 0.404,3.82 1.212,5.358c0.808,1.538 1.929,2.737 3.363,3.598c1.434,0.86 3.09,1.29 4.967,1.29Zm9.073,8.682l-0,-10.168l1.642,-9.229l-1.642,-9.073l-0,-9.308l10.168,0l-0,37.778l-10.168,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M276.755,104.778c-4.015,0 -7.561,-0.717 -10.637,-2.151c-3.077,-1.434 -5.527,-3.454 -7.352,-6.061l6.491,-6.492c1.46,1.721 3.09,3.037 4.889,3.95c1.799,0.912 3.976,1.368 6.531,1.368c3.181,0 5.697,-0.808 7.548,-2.424c1.851,-1.617 2.776,-3.859 2.776,-6.727l0,-9.464l1.721,-8.291l-1.643,-8.29l0,-9.934l10.168,0l0,35.823c0,3.754 -0.873,7.026 -2.62,9.816c-1.747,2.79 -4.158,4.967 -7.235,6.531c-3.076,1.564 -6.622,2.346 -10.637,2.346Zm-0.469,-17.755c-3.39,0 -6.44,-0.821 -9.151,-2.463c-2.712,-1.643 -4.837,-3.898 -6.375,-6.766c-1.538,-2.868 -2.307,-6.075 -2.307,-9.62c-0,-3.546 0.769,-6.727 2.307,-9.543c1.538,-2.815 3.663,-5.045 6.375,-6.687c2.711,-1.643 5.761,-2.464 9.151,-2.464c2.816,0 5.305,0.548 7.469,1.643c2.164,1.095 3.872,2.594 5.123,4.497c1.252,1.903 1.93,4.132 2.034,6.687l-0,11.889c-0.104,2.503 -0.795,4.732 -2.073,6.688c-1.277,1.955 -2.998,3.467 -5.162,4.536c-2.164,1.069 -4.628,1.603 -7.391,1.603Zm2.033,-9.307c1.878,-0 3.507,-0.404 4.889,-1.212c1.382,-0.809 2.464,-1.93 3.246,-3.364c0.782,-1.434 1.173,-3.063 1.173,-4.888c-0,-1.877 -0.391,-3.52 -1.173,-4.928c-0.782,-1.408 -1.864,-2.516 -3.246,-3.324c-1.382,-0.808 -3.011,-1.212 -4.889,-1.212c-1.877,-0 -3.519,0.404 -4.927,1.212c-1.408,0.808 -2.503,1.929 -3.285,3.363c-0.782,1.434 -1.173,3.064 -1.173,4.889c-0,1.773 0.391,3.376 1.173,4.81c0.782,1.434 1.877,2.568 3.285,3.402c1.408,0.835 3.05,1.252 4.927,1.252Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M307.337,88.04l0,-54.985l10.637,0l0,54.985l-10.637,0Zm7.822,0l-0,-9.464l28.001,0l-0,9.464l-28.001,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M349.73,88.04l-0,-37.778l10.324,0l0,37.778l-10.324,0Zm5.162,-43.878c-1.669,-0 -3.05,-0.561 -4.145,-1.682c-1.095,-1.121 -1.643,-2.516 -1.643,-4.185c0,-1.616 0.548,-2.998 1.643,-4.145c1.095,-1.147 2.476,-1.721 4.145,-1.721c1.721,0 3.115,0.574 4.184,1.721c1.069,1.147 1.604,2.529 1.604,4.145c-0,1.669 -0.535,3.064 -1.604,4.185c-1.069,1.121 -2.463,1.682 -4.184,1.682Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M394.547,88.04l-0,-21.665c-0,-2.243 -0.704,-4.068 -2.112,-5.475c-1.408,-1.408 -3.233,-2.112 -5.475,-2.112c-1.46,-0 -2.763,0.313 -3.911,0.938c-1.147,0.626 -2.046,1.513 -2.698,2.66c-0.652,1.147 -0.978,2.476 -0.978,3.989l-3.989,-2.034c0,-2.972 0.639,-5.566 1.917,-7.782c1.277,-2.216 3.05,-3.95 5.318,-5.202c2.268,-1.251 4.836,-1.877 7.704,-1.877c2.764,0 5.241,0.691 7.431,2.073c2.19,1.382 3.911,3.181 5.162,5.397c1.251,2.216 1.877,4.601 1.877,7.156l0,23.934l-10.246,0Zm-25.42,0l0,-37.778l10.246,0l0,37.778l-10.246,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M437.174,88.04l-14.626,-19.475l14.548,-18.303l11.81,0l-17.05,20.649l0.391,-5.006l17.442,22.135l-12.515,0Zm-24.09,0l-0,-56.549l10.246,-0l0,56.549l-10.246,0Z" style="fill:#fff;fill-rule:nonzero;"/></g><path d="M25.176,96.816c-8.679,-9.38 -13.986,-21.925 -13.986,-35.699c-0,-29.027 23.566,-52.592 52.592,-52.592c29.027,-0 52.592,23.565 52.592,52.592c0,11.105 -3.449,21.411 -9.334,29.904l-0.048,-2.446c-0.087,-4.064 -0.992,-8.013 -2.564,-11.613c-2.355,-5.4 -6.187,-10.048 -10.968,-13.413c-1.015,-0.717 -2.084,-1.371 -3.186,-1.97l0.378,19.084c1.223,2.212 1.954,4.708 2.01,7.379l0.327,16.807c-2.697,1.806 -5.571,3.37 -8.589,4.659l-0.521,-56.659c-0.085,-4.066 -0.987,-8.013 -2.559,-11.613c-2.353,-5.408 -6.18,-10.05 -10.965,-13.41c-4.771,-3.371 -10.546,-5.447 -16.648,-5.675c-4.059,-0.15 -7.951,0.536 -11.464,1.908c-5.273,2.058 -9.701,5.631 -12.803,10.228c-3.097,4.603 -4.848,10.264 -4.733,16.366l0.469,46.163Zm23.137,14.577c-3.028,-0.931 -5.94,-2.13 -8.708,-3.566l-0.577,-56.636c-0.031,-2.131 0.348,-4.106 1.088,-5.892c1.095,-2.689 2.997,-4.972 5.4,-6.528c2.406,-1.553 5.297,-2.4 8.473,-2.285c2.13,0.079 4.128,0.582 5.97,1.417c2.743,1.256 5.13,3.286 6.82,5.792c1.698,2.503 2.709,5.444 2.772,8.624l0.58,61.01c-2.082,0.251 -4.2,0.38 -6.349,0.38c-0.372,0 -0.742,-0.004 -1.112,-0.012l-0.529,-26.78c-0.034,-2.134 0.349,-4.101 1.078,-5.903c0.188,-0.454 0.419,-0.901 0.647,-1.341l-0.375,-19.087c-4.462,2.122 -8.217,5.377 -10.945,9.43c-3.091,4.597 -4.861,10.255 -4.722,16.366l0.489,25.011Z" style="fill:url(#_Linear1_footer);"/></g><g id="SVGRepo_iconCarrier"></g><defs><linearGradient id="_Linear1_footer" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(8.74093e-15,-105.184,142.75,6.44068e-15,63.7821,113.709)"><stop offset="0" style="stop-color:#ff1f2b;stop-opacity:1"/><stop offset="1" style="stop-color:#ff682f;stop-opacity:1"/></linearGradient></defs></svg>
                        </Link>
                        <p class="text-gray-400 text-sm leading-relaxed max-w-xs">{{ t('welcome.footer.tagline') }}</p>
                    </div>

                    <!-- Platform -->
                    <div>
                        <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">{{ t('welcome.footer.product') }}</h4>
                        <ul class="space-y-3">
                            <li><a href="#features" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.features') }}</a></li>
                            <li><a href="#how-it-works" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.howItWorks') }}</a></li>
                            <li><a href="#pricing" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.pricing') }}</a></li>
                        </ul>
                    </div>

                    <!-- Legal -->
                    <div>
                        <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">{{ t('welcome.footer.legal') }}</h4>
                        <ul class="space-y-3">
                            <li><a :href="route('privacy-policy')" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.privacyPolicy') }}</a></li>
                            <li><a :href="route('cookie-policy')" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.cookiePolicy') }}</a></li>
                            <li><a :href="route('terms-of-service')" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.termsOfService') }}</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div class="border-t border-white/[0.08] pt-8 text-center">
                    <MadeWithLove />
                </div>
            </div>
        </footer>

        <!-- Cookie Banner -->
        <CookieBanner />

        <!-- Scroll to top -->
        <Transition name="scroll-top-fade">
            <button
                v-if="showScrollTop"
                @click="scrollToTop"
                class="fixed bottom-6 right-6 z-50 w-11 h-11 rounded-full bg-orange-500 hover:bg-orange-600 text-white flex items-center justify-center shadow-lg shadow-orange-500/30 transition-colors duration-200"
                aria-label="Scroll to top"
            >
                <LucideArrowUp :size="20" />
            </button>
        </Transition>
    </div>
</template>

<style scoped>
.bg-grid-white\/\[0\.02\] {
    background-image: linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                      linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
}

.scroll-top-fade-enter-active,
.scroll-top-fade-leave-active {
    transition: opacity 0.25s ease, transform 0.25s ease;
}
.scroll-top-fade-enter-from,
.scroll-top-fade-leave-to {
    opacity: 0;
    transform: scale(0.75);
}
</style>
