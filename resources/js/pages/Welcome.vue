<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import type { SharedData } from '@/types';

const showMobileMenu = ref(false);
const page = usePage<SharedData>();

const isAuthenticated = computed(() => page.props.auth?.user);
const isAdmin = computed(() => page.props.auth?.isAdmin);
const isTenant = computed(() => page.props.auth?.tenant);
</script>

<template>
    <Head title="MagLink - Professional URL Shortener">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800">
        <!-- Header/Navigation -->
        <header class="bg-white/80 backdrop-blur-sm dark:bg-gray-900/80 sticky top-0 z-50 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-2xl font-bold text-blue-600 dark:text-blue-400">MagLink</h1>
                        </div>
                    </div>
                    
                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex items-center space-x-8">
                        <a href="#features" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors">Features</a>
                        <a href="#pricing" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors">Pricing</a>
                        <a href="#about" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors">About</a>
                        
                        <div class="flex items-center space-x-4">
                            <template v-if="isAuthenticated">
                                <a
                                    v-if="isTenant"
                                    :href="route('tenant.index', { tenant: page.props.auth.tenant })"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                                >
                                    Dashboard
                                </a>
                                <a v-else-if="isAdmin"
                                    :href="route('admin.index')"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                                >
                                    Admin Dashboard
                                </a>
                            </template>
                            <template v-else>
                                <Link
                                    :href="route('login')"
                                    class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors"
                                >
                                    Log in
                                </Link>
                                <Link
                                    :href="route('register')"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                                >
                                    Get Started
                                </Link>
                            </template>
                        </div>
                    </nav>
                    
                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button
                            @click="showMobileMenu = !showMobileMenu"
                            class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400"
                        >
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Mobile Navigation -->
                <div v-if="showMobileMenu" class="md:hidden py-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col space-y-4">
                        <a href="#features" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors">Features</a>
                        <a href="#pricing" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors">Pricing</a>
                        <a href="#about" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors">About</a>
                        
                        <div class="flex flex-col space-y-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <Link
                                v-if="isAuthenticated"
                                :href="route('tenant.index')"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center"
                            >
                                Dashboard
                            </Link>
                            <template v-else>
                                <Link
                                    :href="route('login')"
                                    class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors text-center"
                                >
                                    Log in
                                </Link>
                                <Link
                                    :href="route('register')"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center"
                                >
                                    Get Started
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative py-20 lg:py-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                        Shorten, Track, and 
                        <span class="text-blue-600 dark:text-blue-400">Optimize</span>
                        Your Links
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-3xl mx-auto">
                        Transform your long URLs into powerful, trackable short links. Get detailed analytics, 
                        custom branding, and enterprise-grade features for your business.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <!-- <Link
                            v-if="!isAuthenticated"
                            :href="route('register')"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors text-lg"
                        >
                            Start Free Trial
                        </Link>
                        <Link
                            v-else
                            :href="route('tenant.index')"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors text-lg"
                        >
                            Go to Dashboard
                        </Link>
                        <a
                            href="#features"
                            class="border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white px-8 py-3 rounded-lg font-medium transition-colors text-lg"
                        >
                            Learn More
                        </a> -->
                    </div>
                </div>
                
                <!-- URL Shortener Demo -->
                <div class="mt-16 max-w-2xl mx-auto">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-8">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <input
                                type="url"
                                placeholder="Enter your long URL here..."
                                class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                            />
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                Shorten URL
                            </button>
                        </div>
                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                🚀 Try it free • No registration required for basic usage
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Everything You Need to Manage Your Links
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        Powerful features designed for individuals, teams, and enterprises
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 text-center">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Custom Short Links</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Create branded short links with custom domains and memorable aliases
                        </p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 text-center">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Advanced Analytics</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Get detailed insights on clicks, geographic data, and user behavior
                        </p>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 text-center">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Enterprise Security</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Password protection, expiration dates, and advanced security features
                        </p>
                    </div>
                    
                    <!-- Feature 4 -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 text-center">
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Team Collaboration</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Share links with team members and manage permissions easily
                        </p>
                    </div>
                    
                    <!-- Feature 5 -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 text-center">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">API Integration</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Integrate with your existing tools using our powerful REST API
                        </p>
                    </div>
                    
                    <!-- Feature 6 -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 text-center">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Lightning Fast</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Global CDN ensures your short links redirect instantly worldwide
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-20 bg-gray-50 dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Simple, Transparent Pricing
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300">
                        Choose the plan that fits your needs
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Free Plan -->
                    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-8 text-center">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Free</h3>
                        <div class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                            $0
                            <span class="text-lg font-normal text-gray-600 dark:text-gray-300">/month</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">Perfect for personal use</p>
                        <ul class="text-left space-y-3 mb-8">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                100 links per month
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Basic analytics
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Standard support
                            </li>
                        </ul>
                        <Link
                            v-if="!isAuthenticated"
                            :href="route('register')"
                            class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-6 rounded-lg transition-colors inline-block"
                        >
                            Get Started
                        </Link>
                    </div>
                    
                    <!-- Pro Plan -->
                    <div class="bg-blue-600 text-white rounded-xl shadow-lg p-8 text-center relative transform scale-105">
                        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                            <span class="bg-orange-500 text-white px-4 py-1 rounded-full text-sm font-medium">
                                Most Popular
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold mb-4">Pro</h3>
                        <div class="text-4xl font-bold mb-2">
                            $19
                            <span class="text-lg font-normal opacity-75">/month</span>
                        </div>
                        <p class="opacity-75 mb-6">For growing businesses</p>
                        <ul class="text-left space-y-3 mb-8">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-blue-200 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                10,000 links per month
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-blue-200 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Advanced analytics
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-blue-200 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Custom domains
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-blue-200 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Priority support
                            </li>
                        </ul>
                        <Link
                            v-if="!isAuthenticated"
                            :href="route('register')"
                            class="w-full bg-white hover:bg-gray-100 text-blue-600 font-medium py-3 px-6 rounded-lg transition-colors inline-block"
                        >
                            Start Free Trial
                        </Link>
                    </div>
                    
                    <!-- Enterprise Plan -->
                    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-8 text-center">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Enterprise</h3>
                        <div class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                            $99
                            <span class="text-lg font-normal text-gray-600 dark:text-gray-300">/month</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">For large organizations</p>
                        <ul class="text-left space-y-3 mb-8">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Unlimited links
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Team management
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                API access
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                24/7 support
                            </li>
                        </ul>
                        <a
                            href="mailto:contact@maglink.com"
                            class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-6 rounded-lg transition-colors inline-block"
                        >
                            Contact Sales
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-20 bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-8">
                        Trusted by thousands of businesses worldwide
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-blue-600 dark:text-blue-400 mb-2">10M+</div>
                            <div class="text-gray-600 dark:text-gray-300">Links Created</div>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-blue-600 dark:text-blue-400 mb-2">50K+</div>
                            <div class="text-gray-600 dark:text-gray-300">Active Users</div>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-blue-600 dark:text-blue-400 mb-2">99.9%</div>
                            <div class="text-gray-600 dark:text-gray-300">Uptime</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-2xl font-bold text-blue-400 mb-4">MagLink</h3>
                        <p class="text-gray-400">
                            The most powerful URL shortener for modern businesses. 
                            Create, track, and optimize your links with ease.
                        </p>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Product</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#features" class="hover:text-white transition-colors">Features</a></li>
                            <li><a href="#pricing" class="hover:text-white transition-colors">Pricing</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">API</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Documentation</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Company</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#about" class="hover:text-white transition-colors">About</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Careers</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Support</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-white transition-colors">Help Center</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Community</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Status</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Security</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; 2024 MagLink. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>
