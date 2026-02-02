<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import type { SharedData } from '@/types';
import { 
    LucideLink2, 
    LucideQrCode, 
    LucideLayout,
    LucideBarChart3,
    LucideZap,
    LucideShield,
    LucideCheck
} from 'lucide-vue-next';
import LanguageSelector from '@/components/LanguageSelector.vue';

const showMobileMenu = ref(false);
const page = usePage<SharedData>();
const { t } = useI18n();

const isAuthenticated = computed(() => page.props.auth?.user);
const isAdmin = computed(() => page.props.auth?.isAdmin);
const isTenant = computed(() => page.props.auth?.tenant);

const currentSlide = ref(0);
const previousSlide = ref(0);

// Slider features for hero section (inside MacBook)
const sliderFeatures = computed(() => [
    {
        icon: LucideLink2,
        title: t('welcome.slider.linkShortening.title'),
        description: t('welcome.slider.linkShortening.description'),
    },
    {
        icon: LucideQrCode,
        title: t('welcome.slider.qrCodes.title'),
        description: t('welcome.slider.qrCodes.description'),
    },
    {
        icon: LucideLayout,
        title: t('welcome.slider.blockPages.title'),
        description: t('welcome.slider.blockPages.description'),
    },
]);

// Auto-rotate slider with smooth transition
setInterval(() => {
    previousSlide.value = currentSlide.value;
    currentSlide.value = (currentSlide.value + 1) % sliderFeatures.value.length;
}, 3000);
</script>

<template>
    <Head title="MagLink - Professional URL Shortener">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    
    <div class="min-h-screen bg-black text-white">
        <!-- Header/Navigation -->
        <header class="bg-black/80 backdrop-blur-sm sticky top-0 z-50 border-b border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <Link :href="route('home')" class="flex items-center gap-3">
                            <svg width="100%" height="100%" viewBox="0 0 461 123" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><rect id="Convertito" x="0" y="0" width="460.879" height="122.234" style="fill:none;"/><g><g><path d="M149.577,88.04l0,-54.985l7.509,0l22.291,35.666l-4.302,0l22.214,-35.666l7.508,0l0,54.985l-10.559,0l0,-36.761l2.034,0.626l-15.487,24.872l-7.196,0l-15.486,-24.872l2.112,-0.626l-0,36.761l-10.638,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M230.765,88.822c-3.442,0 -6.544,-0.86 -9.308,-2.581c-2.764,-1.72 -4.928,-4.067 -6.492,-7.039c-1.564,-2.972 -2.346,-6.309 -2.346,-10.012c-0,-3.754 0.782,-7.117 2.346,-10.089c1.564,-2.973 3.728,-5.319 6.492,-7.04c2.764,-1.72 5.866,-2.581 9.308,-2.581c2.711,0 5.136,0.548 7.274,1.643c2.137,1.095 3.845,2.62 5.123,4.575c1.277,1.956 1.968,4.159 2.072,6.609l0,13.61c-0.104,2.503 -0.795,4.719 -2.072,6.648c-1.278,1.929 -2.986,3.455 -5.123,4.576c-2.138,1.121 -4.563,1.681 -7.274,1.681Zm1.877,-9.464c2.868,0 5.188,-0.951 6.961,-2.854c1.773,-1.904 2.659,-4.368 2.659,-7.392c0,-1.981 -0.404,-3.741 -1.212,-5.279c-0.808,-1.539 -1.929,-2.738 -3.363,-3.598c-1.434,-0.861 -3.116,-1.291 -5.045,-1.291c-1.877,0 -3.533,0.43 -4.967,1.291c-1.434,0.86 -2.555,2.059 -3.363,3.598c-0.808,1.538 -1.212,3.298 -1.212,5.279c-0,2.034 0.404,3.82 1.212,5.358c0.808,1.538 1.929,2.737 3.363,3.598c1.434,0.86 3.09,1.29 4.967,1.29Zm9.073,8.682l-0,-10.168l1.642,-9.229l-1.642,-9.073l-0,-9.308l10.168,0l-0,37.778l-10.168,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M276.755,104.778c-4.015,0 -7.561,-0.717 -10.637,-2.151c-3.077,-1.434 -5.527,-3.454 -7.352,-6.061l6.491,-6.492c1.46,1.721 3.09,3.037 4.889,3.95c1.799,0.912 3.976,1.368 6.531,1.368c3.181,0 5.697,-0.808 7.548,-2.424c1.851,-1.617 2.776,-3.859 2.776,-6.727l0,-9.464l1.721,-8.291l-1.643,-8.29l0,-9.934l10.168,0l0,35.823c0,3.754 -0.873,7.026 -2.62,9.816c-1.747,2.79 -4.158,4.967 -7.235,6.531c-3.076,1.564 -6.622,2.346 -10.637,2.346Zm-0.469,-17.755c-3.39,0 -6.44,-0.821 -9.151,-2.463c-2.712,-1.643 -4.837,-3.898 -6.375,-6.766c-1.538,-2.868 -2.307,-6.075 -2.307,-9.62c-0,-3.546 0.769,-6.727 2.307,-9.543c1.538,-2.815 3.663,-5.045 6.375,-6.687c2.711,-1.643 5.761,-2.464 9.151,-2.464c2.816,0 5.305,0.548 7.469,1.643c2.164,1.095 3.872,2.594 5.123,4.497c1.252,1.903 1.93,4.132 2.034,6.687l-0,11.889c-0.104,2.503 -0.795,4.732 -2.073,6.688c-1.277,1.955 -2.998,3.467 -5.162,4.536c-2.164,1.069 -4.628,1.603 -7.391,1.603Zm2.033,-9.307c1.878,-0 3.507,-0.404 4.889,-1.212c1.382,-0.809 2.464,-1.93 3.246,-3.364c0.782,-1.434 1.173,-3.063 1.173,-4.888c-0,-1.877 -0.391,-3.52 -1.173,-4.928c-0.782,-1.408 -1.864,-2.516 -3.246,-3.324c-1.382,-0.808 -3.011,-1.212 -4.889,-1.212c-1.877,-0 -3.519,0.404 -4.927,1.212c-1.408,0.808 -2.503,1.929 -3.285,3.363c-0.782,1.434 -1.173,3.064 -1.173,4.889c-0,1.773 0.391,3.376 1.173,4.81c0.782,1.434 1.877,2.568 3.285,3.402c1.408,0.835 3.05,1.252 4.927,1.252Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M307.337,88.04l0,-54.985l10.637,0l0,54.985l-10.637,0Zm7.822,0l-0,-9.464l28.001,0l-0,9.464l-28.001,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M349.73,88.04l-0,-37.778l10.324,0l0,37.778l-10.324,0Zm5.162,-43.878c-1.669,-0 -3.05,-0.561 -4.145,-1.682c-1.095,-1.121 -1.643,-2.516 -1.643,-4.185c0,-1.616 0.548,-2.998 1.643,-4.145c1.095,-1.147 2.476,-1.721 4.145,-1.721c1.721,0 3.115,0.574 4.184,1.721c1.069,1.147 1.604,2.529 1.604,4.145c-0,1.669 -0.535,3.064 -1.604,4.185c-1.069,1.121 -2.463,1.682 -4.184,1.682Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M394.547,88.04l-0,-21.665c-0,-2.243 -0.704,-4.068 -2.112,-5.475c-1.408,-1.408 -3.233,-2.112 -5.475,-2.112c-1.46,-0 -2.763,0.313 -3.911,0.938c-1.147,0.626 -2.046,1.513 -2.698,2.66c-0.652,1.147 -0.978,2.476 -0.978,3.989l-3.989,-2.034c0,-2.972 0.639,-5.566 1.917,-7.782c1.277,-2.216 3.05,-3.95 5.318,-5.202c2.268,-1.251 4.836,-1.877 7.704,-1.877c2.764,0 5.241,0.691 7.431,2.073c2.19,1.382 3.911,3.181 5.162,5.397c1.251,2.216 1.877,4.601 1.877,7.156l0,23.934l-10.246,0Zm-25.42,0l0,-37.778l10.246,0l0,37.778l-10.246,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M437.174,88.04l-14.626,-19.475l14.548,-18.303l11.81,0l-17.05,20.649l0.391,-5.006l17.442,22.135l-12.515,0Zm-24.09,0l-0,-56.549l10.246,-0l0,56.549l-10.246,0Z" style="fill:#fff;fill-rule:nonzero;"/></g><path d="M25.176,96.816c-8.679,-9.38 -13.986,-21.925 -13.986,-35.699c-0,-29.027 23.566,-52.592 52.592,-52.592c29.027,-0 52.592,23.565 52.592,52.592c0,11.105 -3.449,21.411 -9.334,29.904l-0.048,-2.446c-0.087,-4.064 -0.992,-8.013 -2.564,-11.613c-2.355,-5.4 -6.187,-10.048 -10.968,-13.413c-1.015,-0.717 -2.084,-1.371 -3.186,-1.97l0.378,19.084c1.223,2.212 1.954,4.708 2.01,7.379l0.327,16.807c-2.697,1.806 -5.571,3.37 -8.589,4.659l-0.521,-56.659c-0.085,-4.066 -0.987,-8.013 -2.559,-11.613c-2.353,-5.408 -6.18,-10.05 -10.965,-13.41c-4.771,-3.371 -10.546,-5.447 -16.648,-5.675c-4.059,-0.15 -7.951,0.536 -11.464,1.908c-5.273,2.058 -9.701,5.631 -12.803,10.228c-3.097,4.603 -4.848,10.264 -4.733,16.366l0.469,46.163Zm23.137,14.577c-3.028,-0.931 -5.94,-2.13 -8.708,-3.566l-0.577,-56.636c-0.031,-2.131 0.348,-4.106 1.088,-5.892c1.095,-2.689 2.997,-4.972 5.4,-6.528c2.406,-1.553 5.297,-2.4 8.473,-2.285c2.13,0.079 4.128,0.582 5.97,1.417c2.743,1.256 5.13,3.286 6.82,5.792c1.698,2.503 2.709,5.444 2.772,8.624l0.58,61.01c-2.082,0.251 -4.2,0.38 -6.349,0.38c-0.372,0 -0.742,-0.004 -1.112,-0.012l-0.529,-26.78c-0.034,-2.134 0.349,-4.101 1.078,-5.903c0.188,-0.454 0.419,-0.901 0.647,-1.341l-0.375,-19.087c-4.462,2.122 -8.217,5.377 -10.945,9.43c-3.091,4.597 -4.861,10.255 -4.722,16.366l0.489,25.011Z" style="fill:url(#_Linear1);"/></g><g id="SVGRepo_iconCarrier"></g><defs><linearGradient id="_Linear1" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(8.74093e-15,-105.184,142.75,6.44068e-15,63.7821,113.709)"><stop offset="0" style="stop-color:#ff1f2b;stop-opacity:1"/><stop offset="1" style="stop-color:#ff682f;stop-opacity:1"/></linearGradient></defs></svg>
                        </Link>
                    </div>
                    
                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex items-center space-x-8">
                        <a href="#features" class="text-gray-300 hover:text-orange-600 transition-colors">{{ t('welcome.nav.features') }}</a>
                        <a href="#pricing" class="text-gray-300 hover:text-orange-600 transition-colors">{{ t('welcome.nav.pricing') }}</a>
                        
                        <div class="flex items-center space-x-4">
                            <!-- Language Selector -->
                            <LanguageSelector />
                            
                            <template v-if="isAuthenticated">
                                <a
                                    v-if="isTenant"
                                    :href="route('tenant.index', { tenant: page.props.auth.tenant })"
                                    class="bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-500 hover:to-orange-800 text-white px-5 py-2.5 rounded-lg font-medium transition-all"
                                >
                                    {{ t('welcome.nav.dashboard') }}
                                </a>
                                <a v-else-if="isAdmin"
                                    :href="route('admin.index')"
                                    class="bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-500 hover:to-orange-800 text-white px-5 py-2.5 rounded-lg font-medium transition-all"
                                >
                                    {{ t('welcome.nav.adminDashboard') }}
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
                <div v-if="showMobileMenu" class="md:hidden py-4 border-t border-gray-800">
                    <div class="flex flex-col space-y-4">
                        <a href="#features" class="text-gray-300 hover:text-orange-700 transition-colors">{{ t('welcome.nav.features') }}</a>
                        <a href="#pricing" class="text-gray-300 hover:text-orange-700 transition-colors">{{ t('welcome.nav.pricing') }}</a>
                        
                        <!-- Language Selector Mobile -->
                        <div class="py-2">
                            <LanguageSelector />
                        </div>
                        
                        <div class="flex flex-col space-y-2 pt-4 border-t border-gray-800">
                            <Link
                                v-if="isAuthenticated && isTenant"
                                :href="route('tenant.index', { tenant: page.props.auth.tenant })"
                                class="bg-gradient-to-r from-orange-600 to-orange-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center"
                            >
                                {{ t('welcome.nav.dashboard') }}
                            </Link>
                            <Link
                                v-else-if="isAuthenticated && isAdmin"
                                :href="route('admin.index')"
                                class="bg-gradient-to-r from-orange-600 to-orange-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center"
                            >
                                {{ t('welcome.nav.adminDashboard') }}
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
        <section class="relative py-20 lg:py-32 overflow-hidden">
            <div class="absolute inset-0 bg-grid-white/[0.02] bg-[size:50px_50px]"></div>
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-orange-500/10 rounded-full blur-[120px]"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Left: Text -->
                    <div>
                        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold mb-6 leading-tight">
                            {{ t('welcome.hero.title') }}
                            <span class="text-orange-600">
                                {{ t('welcome.hero.titleHighlight') }}
                            </span>
                            {{ t('welcome.hero.titleEnd') }}
                        </h1>
                        <p class="text-xl text-gray-400 mb-8 leading-relaxed">
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
                                :href="route('tenant.index', { tenant: page.props.auth.tenant })"
                                class="inline-flex items-center justify-center bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-500 hover:to-orange-800 text-white px-8 py-4 rounded-lg font-semibold transition-all text-lg"
                            >
                                {{ t('welcome.hero.goToDashboard') }}
                            </Link>
                        </div>
                    </div>
                    
                    <!-- Right: MacBook Mockup with Slider -->
                    <div class="relative">
                        <!-- MacBook Container -->
                        <div class="relative">
                            <!-- MacBook Image -->
                            <img 
                                src="/macbook-pro.png" 
                                alt="MacBook Pro" 
                                class="w-full relative z-10"
                            />
                            
                            <!-- Screenshots Slider Inside MacBook Screen -->
                            <div class="absolute inset-0 flex items-center justify-center z-0">
                                <!-- Perfectly aligned with MacBook screen -->
                                <div class="w-[79.5%] h-[82%] overflow-hidden" style="margin-top: 6.2%; border-radius: 0.4rem;">
                                    <!-- Feature Slider -->
                                    <div class="relative w-full h-full bg-gray-950">
                                        <div 
                                            v-for="(slide, index) in sliderFeatures" 
                                            :key="index"
                                            :class="[
                                                'absolute inset-0 transition-all duration-1000 ease-in-out',
                                                currentSlide === index 
                                                    ? 'opacity-100 translate-x-0 z-20' 
                                                    : previousSlide === index
                                                        ? 'opacity-0 translate-x-full z-10'
                                                        : 'opacity-0 -translate-x-full z-0'
                                            ]"
                                        >
                                            <div class="w-full h-full flex items-center justify-center p-8 bg-gradient-to-br from-gray-900 to-black">
                                                <div class="text-center">
                                                    <div class="inline-flex w-20 h-20 rounded-2xl bg-gradient-to-br from-orange-600 to-orange-700 items-center justify-center mb-6">
                                                        <component :is="slide.icon" :size="40" class="text-white" />
                                                    </div>
                                                    <h3 class="text-2xl font-bold text-white mb-4">{{ slide.title }}</h3>
                                                    <p class="text-gray-400 text-lg">{{ slide.description }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Slider Dots -->
                        <div class="flex justify-center gap-3 mt-8">
                            <button
                                v-for="(_, index) in sliderFeatures"
                                :key="index"
                                @click="previousSlide = currentSlide; currentSlide = index"
                                :class="[
                                    'h-2 rounded-full transition-all duration-300',
                                    currentSlide === index 
                                        ? 'bg-orange-600 w-12' 
                                        : 'bg-gray-700 hover:bg-gray-600 w-2'
                                ]"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-32 bg-gradient-to-b from-black to-gray-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20">
                    <h2 class="text-4xl sm:text-5xl font-bold mb-6">
                        {{ t('welcome.features.title') }}
                    </h2>
                    <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                        {{ t('welcome.features.subtitle') }}
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="group p-8 rounded-2xl bg-gradient-to-br from-gray-900 to-black border border-gray-800 hover:border-orange-600/50 transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LucideLink2 :size="28" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.features.customLinks.title') }}</h3>
                        <p class="text-gray-400 leading-relaxed">
                            {{ t('welcome.features.customLinks.description') }}
                        </p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="group p-8 rounded-2xl bg-gradient-to-br from-gray-900 to-black border border-gray-800 hover:border-orange-500/50 transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-600 to-orange-800 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LucideBarChart3 :size="28" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.features.analytics.title') }}</h3>
                        <p class="text-gray-400 leading-relaxed">
                            {{ t('welcome.features.analytics.description') }}
                        </p>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="group p-8 rounded-2xl bg-gradient-to-br from-gray-900 to-black border border-gray-800 hover:border-orange-600/50 transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LucideShield :size="28" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.features.security.title') }}</h3>
                        <p class="text-gray-400 leading-relaxed">
                            {{ t('welcome.features.security.description') }}
                        </p>
                    </div>
                    
                    <!-- Feature 4 -->
                    <div class="group p-8 rounded-2xl bg-gradient-to-br from-gray-900 to-black border border-gray-800 hover:border-orange-600/50 transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LucideQrCode :size="28" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.features.qrGenerator.title') }}</h3>
                        <p class="text-gray-400 leading-relaxed">
                            {{ t('welcome.features.qrGenerator.description') }}
                        </p>
                    </div>
                    
                    <!-- Feature 5 -->
                    <div class="group p-8 rounded-2xl bg-gradient-to-br from-gray-900 to-black border border-gray-800 hover:border-orange-500/50 transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-600 to-orange-800 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LucideLayout :size="28" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.features.blockPages.title') }}</h3>
                        <p class="text-gray-400 leading-relaxed">
                            {{ t('welcome.features.blockPages.description') }}
                        </p>
                    </div>
                    
                    <!-- Feature 6 -->
                    <div class="group p-8 rounded-2xl bg-gradient-to-br from-gray-900 to-black border border-gray-800 hover:border-orange-500/50 transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-600 to-orange-800 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LucideZap :size="28" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.features.fast.title') }}</h3>
                        <p class="text-gray-400 leading-relaxed">
                            {{ t('welcome.features.fast.description') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-32 bg-black">
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
                    <div class="relative p-8 rounded-2xl bg-gradient-to-br from-gray-900 to-black border border-gray-800 hover:border-orange-500/50 transition-all duration-300">
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
                            v-if="!isAuthenticated"
                            :href="route('register')"
                            class="block w-full text-center bg-gray-800 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
                        >
                            {{ t('welcome.pricing.free.cta') }}
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
                            v-if="!isAuthenticated"
                            :href="route('register')"
                            class="block w-full text-center bg-gradient-to-r from-orange-600 to-orange-800 hover:from-orange-500 hover:to-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition-all"
                        >
                            {{ t('welcome.pricing.professional.cta') }}
                        </a>
                    </div>
                    
                    <!-- Enterprise Plan -->
                    <div class="relative p-8 rounded-2xl bg-gradient-to-br from-gray-900 to-black border border-gray-800 hover:border-orange-500/50 transition-all duration-300">
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
                            href="mailto:contact@maglink.com"
                            class="block w-full text-center bg-gray-800 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
                        >
                            {{ t('welcome.pricing.enterprise.cta') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gradient-to-b from-black to-gray-950 border-t border-gray-800 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                    <!-- Product -->
                    <div>
                        <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">{{ t('welcome.footer.product') }}</h4>
                        <ul class="space-y-3">
                            <li><a href="#features" class="text-gray-400 hover:text-orange-600 transition-colors">{{ t('welcome.footer.features') }}</a></li>
                            <li><a href="#pricing" class="text-gray-400 hover:text-orange-600 transition-colors">{{ t('welcome.footer.pricing') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">{{ t('welcome.footer.api') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">{{ t('welcome.footer.documentation') }}</a></li>
                        </ul>
                    </div>
                    
                    <!-- Resources -->
                    <div>
                        <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">{{ t('welcome.footer.resources') }}</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">{{ t('welcome.footer.guide') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">{{ t('welcome.footer.blog') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">{{ t('welcome.footer.helpCenter') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">{{ t('welcome.footer.status') }}</a></li>
                        </ul>
                    </div>
                    
                    <!-- Company -->
                    <div>
                        <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">{{ t('welcome.footer.company') }}</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">{{ t('welcome.footer.about') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">{{ t('welcome.footer.careers') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">{{ t('welcome.footer.contact') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">{{ t('welcome.footer.pressKit') }}</a></li>
                        </ul>
                    </div>
                    
                    <!-- Community -->
                    <div>
                        <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">{{ t('welcome.footer.community') }}</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">{{ t('welcome.footer.discord') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">{{ t('welcome.footer.twitter') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">{{ t('welcome.footer.github') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">{{ t('welcome.footer.linkedin') }}</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Bottom Bar -->
                <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-orange-600 to-orange-800 flex items-center justify-center">
                            <LucideLink2 :size="16" class="text-white" />
                        </div>
                        <span class="text-gray-400 text-sm">{{ t('welcome.footer.copyright') }}</span>
                    </div>
                    <div class="flex gap-6">
                        <a href="#" class="text-gray-400 hover:text-orange-600 transition-colors text-sm">{{ t('welcome.footer.privacyPolicy') }}</a>
                        <a href="#" class="text-gray-400 hover:text-orange-600 transition-colors text-sm">{{ t('welcome.footer.termsOfService') }}</a>
                        <a href="#" class="text-gray-400 hover:text-orange-600 transition-colors text-sm">{{ t('welcome.footer.security') }}</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.bg-grid-white\/\[0\.02\] {
    background-image: linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                      linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
}
</style>
