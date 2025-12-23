<script setup lang="ts">
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { 
    LucideLink2, 
    LucideQrCode, 
    LucideBarChart3, 
    LucideLayout,
    LucideZap,
    LucideGlobe,
    LucideCheckCircle2
} from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps<{
    title?: string;
    description?: string;
}>();

const features = [
    {
        icon: LucideLink2,
        title: t('auth.features.linkShortening.title'),
        description: t('auth.features.linkShortening.description')
    },
    {
        icon: LucideQrCode,
        title: t('auth.features.qrCodes.title'),
        description: t('auth.features.qrCodes.description')
    },
    {
        icon: LucideBarChart3,
        title: t('auth.features.analytics.title'),
        description: t('auth.features.analytics.description')
    },
    {
        icon: LucideLayout,
        title: t('auth.features.blockPages.title'),
        description: t('auth.features.blockPages.description')
    }
];
</script>

<template>
    <div class="relative grid min-h-screen flex-col lg:grid-cols-2">
        <!-- Left Side - Branding & Features -->
        <div class="relative hidden h-full flex-col bg-gradient-to-br from-orange-600 via-orange-700 to-red-800 dark:from-orange-900 dark:via-orange-950 dark:to-red-950 p-10 text-white lg:flex overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-grid-white/[0.05] bg-[size:20px_20px]"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-orange-950/50 to-transparent"></div>
            
            <!-- Content -->
            <div class="relative z-10 flex flex-col h-full">
                <!-- Logo -->
                <Link :href="route('home')" class="flex items-center gap-3 group">
                    <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center group-hover:bg-white/20 transition-all duration-300 border border-white/20">
                        <LucideLink2 :size="24" class="text-white" />
                    </div>
                    <span class="text-2xl font-bold text-white">MagLink</span>
                </Link>

                <!-- Main Content -->
                <div class="flex-1 flex flex-col justify-center max-w-lg mx-auto w-full py-12">
                    <div class="space-y-6">
                        <div class="space-y-3">
                            <h2 class="text-4xl font-bold leading-tight">
                                {{ t('auth.tagline.part1') }}
                                <span class="bg-gradient-to-r from-orange-200 to-yellow-200 bg-clip-text text-transparent">
                                    {{ t('auth.tagline.highlight') }}
                                </span>
                                {{ t('auth.tagline.part2') }}
                            </h2>
                            <p class="text-lg text-orange-100">
                                {{ t('auth.subtitle') }}
                            </p>
                        </div>

                        <!-- Features -->
                        <div class="space-y-4 pt-8">
                            <div 
                                v-for="feature in features" 
                                :key="feature.title"
                                class="flex items-start gap-4 p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300"
                            >
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-orange-500/20 flex items-center justify-center">
                                    <component :is="feature.icon" :size="20" class="text-orange-200" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white mb-1">{{ feature.title }}</h3>
                                    <p class="text-sm text-orange-200">{{ feature.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-sm text-orange-200">
                    {{ t('auth.copyright') }}
                </div>
            </div>

            <!-- Decorative Elements -->
            <div class="absolute top-20 right-20 w-64 h-64 bg-orange-400/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 left-20 w-64 h-64 bg-red-400/20 rounded-full blur-3xl"></div>
        </div>

        <!-- Right Side - Form -->
        <div class="flex items-center justify-center p-8 bg-white dark:bg-gray-950">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[400px]">
                <!-- Mobile Logo -->
                <div class="lg:hidden flex flex-col items-center space-y-3 mb-6">
                    <Link :href="route('home')" class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-orange-600 dark:bg-orange-500 flex items-center justify-center">
                            <LucideLink2 :size="24" class="text-white" />
                        </div>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">MagLink</span>
                    </Link>
                </div>

                <!-- Form Header -->
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white" v-if="title">
                        {{ title }}
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400" v-if="description">
                        {{ description }}
                    </p>
                </div>

                <!-- Form Content -->
                <slot />
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-grid-white\/\[0\.05\] {
    background-image: linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                      linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
}
</style>
