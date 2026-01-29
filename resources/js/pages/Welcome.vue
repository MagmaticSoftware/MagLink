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
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center">
                                <LucideLink2 :size="20" class="text-white" />
                            </div>
                            <span class="text-xl font-bold text-white">MagLink</span>
                        </Link>
                    </div>
                    
                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex items-center space-x-8">
                        <a href="#features" class="text-gray-300 hover:text-orange-500 transition-colors">{{ t('welcome.nav.features') }}</a>
                        <a href="#pricing" class="text-gray-300 hover:text-orange-500 transition-colors">{{ t('welcome.nav.pricing') }}</a>
                        
                        <div class="flex items-center space-x-4">
                            <!-- Language Selector -->
                            <LanguageSelector />
                            
                            <template v-if="isAuthenticated">
                                <a
                                    v-if="isTenant"
                                    :href="route('tenant.index', { tenant: page.props.auth.tenant })"
                                    class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-5 py-2.5 rounded-lg font-medium transition-all"
                                >
                                    {{ t('welcome.nav.dashboard') }}
                                </a>
                                <a v-else-if="isAdmin"
                                    :href="route('admin.index')"
                                    class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-5 py-2.5 rounded-lg font-medium transition-all"
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
                                    class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-5 py-2.5 rounded-lg font-medium transition-all"
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
                        <a href="#features" class="text-gray-300 hover:text-orange-500 transition-colors">{{ t('welcome.nav.features') }}</a>
                        <a href="#pricing" class="text-gray-300 hover:text-orange-500 transition-colors">{{ t('welcome.nav.pricing') }}</a>
                        
                        <!-- Language Selector Mobile -->
                        <div class="py-2">
                            <LanguageSelector />
                        </div>
                        
                        <div class="flex flex-col space-y-2 pt-4 border-t border-gray-800">
                            <Link
                                v-if="isAuthenticated && isTenant"
                                :href="route('tenant.index', { tenant: page.props.auth.tenant })"
                                class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center"
                            >
                                {{ t('welcome.nav.dashboard') }}
                            </Link>
                            <Link
                                v-else-if="isAuthenticated && isAdmin"
                                :href="route('admin.index')"
                                class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center"
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
                                <Link
                                    :href="route('register')"
                                    class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center"
                                >
                                    {{ t('welcome.nav.getStarted') }}
                                </Link>
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
                            <span class="block bg-gradient-to-r from-orange-400 to-orange-600 bg-clip-text text-transparent">
                                {{ t('welcome.hero.titleHighlight') }}
                            </span>
                            {{ t('welcome.hero.titleEnd') }}
                        </h1>
                        <p class="text-xl text-gray-400 mb-8 leading-relaxed">
                            {{ t('welcome.hero.subtitle') }}
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <Link
                                v-if="!isAuthenticated"
                                :href="route('register')"
                                class="inline-flex items-center justify-center bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-8 py-4 rounded-lg font-semibold transition-all text-lg"
                            >
                                {{ t('welcome.hero.getStarted') }}
                            </Link>
                            <Link
                                v-else
                                :href="route('tenant.index', { tenant: page.props.auth.tenant })"
                                class="inline-flex items-center justify-center bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-8 py-4 rounded-lg font-semibold transition-all text-lg"
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
                                                    <div class="inline-flex w-20 h-20 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 items-center justify-center mb-6">
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
                                        ? 'bg-orange-500 w-12' 
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
                    <div class="group p-8 rounded-2xl bg-gradient-to-br from-gray-900 to-black border border-gray-800 hover:border-orange-500/50 transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LucideLink2 :size="28" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.features.customLinks.title') }}</h3>
                        <p class="text-gray-400 leading-relaxed">
                            {{ t('welcome.features.customLinks.description') }}
                        </p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="group p-8 rounded-2xl bg-gradient-to-br from-gray-900 to-black border border-gray-800 hover:border-orange-500/50 transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LucideBarChart3 :size="28" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.features.analytics.title') }}</h3>
                        <p class="text-gray-400 leading-relaxed">
                            {{ t('welcome.features.analytics.description') }}
                        </p>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="group p-8 rounded-2xl bg-gradient-to-br from-gray-900 to-black border border-gray-800 hover:border-orange-500/50 transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LucideShield :size="28" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.features.security.title') }}</h3>
                        <p class="text-gray-400 leading-relaxed">
                            {{ t('welcome.features.security.description') }}
                        </p>
                    </div>
                    
                    <!-- Feature 4 -->
                    <div class="group p-8 rounded-2xl bg-gradient-to-br from-gray-900 to-black border border-gray-800 hover:border-orange-500/50 transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LucideQrCode :size="28" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.features.qrGenerator.title') }}</h3>
                        <p class="text-gray-400 leading-relaxed">
                            {{ t('welcome.features.qrGenerator.description') }}
                        </p>
                    </div>
                    
                    <!-- Feature 5 -->
                    <div class="group p-8 rounded-2xl bg-gradient-to-br from-gray-900 to-black border border-gray-800 hover:border-orange-500/50 transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LucideLayout :size="28" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ t('welcome.features.blockPages.title') }}</h3>
                        <p class="text-gray-400 leading-relaxed">
                            {{ t('welcome.features.blockPages.description') }}
                        </p>
                    </div>
                    
                    <!-- Feature 6 -->
                    <div class="group p-8 rounded-2xl bg-gradient-to-br from-gray-900 to-black border border-gray-800 hover:border-orange-500/50 transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
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
                    <!-- Essentials Plan -->
                    <div class="relative p-8 rounded-2xl bg-gradient-to-br from-gray-900 to-black border border-gray-800 hover:border-orange-500/50 transition-all duration-300">
                        <h3 class="text-2xl font-bold mb-2">{{ t('welcome.pricing.essentials.name') }}</h3>
                        <div class="flex items-baseline gap-2 mb-6">
                            <span class="text-5xl font-bold">{{ t('welcome.pricing.essentials.price') }}</span>
                            <span class="text-gray-400">{{ t('welcome.pricing.essentials.period') }}</span>
                        </div>
                        <p class="text-gray-400 mb-8">{{ t('welcome.pricing.essentials.description') }}</p>
                        
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-500 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.essentials.feature1') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-500 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.essentials.feature2') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-500 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.essentials.feature3') }}</span>
                            </li>
                        </ul>
                        
                        <Link
                            v-if="!isAuthenticated"
                            :href="route('register')"
                            class="block w-full text-center bg-gray-800 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
                        >
                            {{ t('welcome.pricing.essentials.cta') }}
                        </Link>
                    </div>
                    
                    <!-- Teams Plan - Most Popular -->
                    <div class="relative p-8 rounded-2xl bg-gradient-to-br from-orange-500/10 to-orange-600/5 border-2 border-orange-500 transform scale-105">
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <span class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-1 rounded-full text-sm font-semibold">
                                {{ t('welcome.pricing.teams.badge') }}
                            </span>
                        </div>
                        
                        <h3 class="text-2xl font-bold mb-2">{{ t('welcome.pricing.teams.name') }}</h3>
                        <div class="flex items-baseline gap-2 mb-6">
                            <span class="text-5xl font-bold">{{ t('welcome.pricing.teams.price') }}</span>
                            <span class="text-gray-400">{{ t('welcome.pricing.teams.period') }}</span>
                        </div>
                        <p class="text-gray-400 mb-8">{{ t('welcome.pricing.teams.description') }}</p>
                        
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-500 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.teams.feature1') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-500 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.teams.feature2') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-500 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.teams.feature3') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-500 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.teams.feature4') }}</span>
                            </li>
                        </ul>
                        
                        <Link
                            v-if="!isAuthenticated"
                            :href="route('register')"
                            class="block w-full text-center bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold py-3 px-6 rounded-lg transition-all"
                        >
                            {{ t('welcome.pricing.teams.cta') }}
                        </Link>
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
                                <LucideCheck :size="20" class="text-orange-500 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.enterprise.feature1') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-500 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.enterprise.feature2') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-500 mt-0.5 flex-shrink-0" />
                                <span class="text-gray-300">{{ t('welcome.pricing.enterprise.feature3') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <LucideCheck :size="20" class="text-orange-500 mt-0.5 flex-shrink-0" />
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
                            <li><a href="#features" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.features') }}</a></li>
                            <li><a href="#pricing" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.pricing') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.api') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.documentation') }}</a></li>
                        </ul>
                    </div>
                    
                    <!-- Resources -->
                    <div>
                        <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">{{ t('welcome.footer.resources') }}</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.guide') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.blog') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.helpCenter') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.status') }}</a></li>
                        </ul>
                    </div>
                    
                    <!-- Company -->
                    <div>
                        <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">{{ t('welcome.footer.company') }}</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.about') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.careers') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.contact') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.pressKit') }}</a></li>
                        </ul>
                    </div>
                    
                    <!-- Community -->
                    <div>
                        <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">{{ t('welcome.footer.community') }}</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.discord') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.twitter') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.github') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">{{ t('welcome.footer.linkedin') }}</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Bottom Bar -->
                <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center">
                            <LucideLink2 :size="16" class="text-white" />
                        </div>
                        <span class="text-gray-400 text-sm">{{ t('welcome.footer.copyright') }}</span>
                    </div>
                    <div class="flex gap-6">
                        <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors text-sm">{{ t('welcome.footer.privacyPolicy') }}</a>
                        <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors text-sm">{{ t('welcome.footer.termsOfService') }}</a>
                        <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors text-sm">{{ t('welcome.footer.security') }}</a>
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
