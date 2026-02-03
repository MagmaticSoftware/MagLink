<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { LucideHome, LucideSearch, LucideArrowLeft, LucideAlertCircle, LucideShieldAlert, LucideServerCrash } from 'lucide-vue-next';
import { ref, onMounted, computed } from 'vue';
import Button from '@/components/volt/Button.vue';

const props = defineProps<{
    status?: number;
    message?: string;
}>();

const floatingElements = ref<Array<{ x: number; y: number; delay: number }>>([]);

onMounted(() => {
    // Generate random floating elements for background animation
    for (let i = 0; i < 8; i++) {
        floatingElements.value.push({
            x: Math.random() * 100,
            y: Math.random() * 100,
            delay: Math.random() * 5,
        });
    }
});

const errorConfig = computed(() => {
    const status = props.status || 404;
    
    const configs: Record<number, { title: string; description: string; icon: any; gradient: string }> = {
        403: {
            title: 'Accesso Negato',
            description: 'Non hai i permessi necessari per accedere a questa pagina.',
            icon: LucideShieldAlert,
            gradient: 'from-red-500 via-orange-500 to-yellow-500',
        },
        404: {
            title: 'Pagina non trovata',
            description: props.message || 'La pagina che stai cercando non esiste o è stata spostata.',
            icon: LucideAlertCircle,
            gradient: 'from-primary via-purple-500 to-pink-500',
        },
        500: {
            title: 'Errore del Server',
            description: 'Si è verificato un errore interno. Stiamo lavorando per risolverlo.',
            icon: LucideServerCrash,
            gradient: 'from-red-600 via-red-500 to-orange-500',
        },
        503: {
            title: 'Servizio non disponibile',
            description: 'Il servizio è temporaneamente non disponibile. Riprova tra qualche minuto.',
            icon: LucideServerCrash,
            gradient: 'from-orange-600 via-orange-500 to-yellow-500',
        },
    };
    
    return configs[status] || configs[404];
});

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <Head :title="`${status} - ${errorConfig.title}`">
        <link rel="icon" href="/favicon.ico" />
    </Head>
    
    <div class="min-h-screen bg-gradient-to-br from-surface-50 via-white to-primary/5 dark:from-surface-950 dark:via-surface-900 dark:to-primary/10 flex items-center justify-center p-4 overflow-hidden relative">
        <!-- Animated background elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div
                v-for="(element, index) in floatingElements"
                :key="index"
                class="absolute w-2 h-2 bg-primary/10 dark:bg-primary/20 rounded-full animate-float"
                :style="{
                    left: `${element.x}%`,
                    top: `${element.y}%`,
                    animationDelay: `${element.delay}s`,
                }"
            />
        </div>

        <!-- Main content -->
        <div class="max-w-2xl w-full text-center relative z-10">
            <!-- Error Number with gradient -->
            <div class="mb-8 relative">
                <h1 
                    class="text-[150px] md:text-[200px] font-black leading-none bg-gradient-to-br bg-clip-text text-transparent animate-pulse-slow"
                    :class="errorConfig.gradient"
                >
                    {{ status || 404 }}
                </h1>
                <div class="absolute inset-0 flex items-center justify-center opacity-20">
                    <component :is="errorConfig.icon" :size="120" class="text-primary animate-spin-slow" />
                </div>
            </div>

            <!-- Message -->
            <div class="mb-8 space-y-4">
                <h2 class="text-3xl md:text-4xl font-bold text-surface-900 dark:text-surface-50">
                    Oops! {{ errorConfig.title }}
                </h2>
                <p class="text-lg text-surface-600 dark:text-surface-400 max-w-md mx-auto">
                    {{ errorConfig.description }}
                </p>
            </div>

            <!-- Illustration -->
            <div class="mb-12 flex justify-center">
                <div class="relative w-64 h-64">
                    <!-- Floating link icon -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-32 h-32 bg-gradient-to-br from-primary/20 to-purple-500/20 dark:from-primary/30 dark:to-purple-500/30 rounded-full flex items-center justify-center backdrop-blur-sm border border-primary/20 animate-bounce-slow">
                            <svg 
                                class="w-16 h-16 text-primary"
                                fill="none" 
                                viewBox="0 0 24 24" 
                                stroke="currentColor"
                            >
                                <path 
                                    stroke-linecap="round" 
                                    stroke-linejoin="round" 
                                    stroke-width="2" 
                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"
                                />
                            </svg>
                        </div>
                    </div>
                    <!-- Decorative circles -->
                    <div class="absolute top-0 left-0 w-20 h-20 bg-purple-500/10 rounded-full animate-ping-slow" />
                    <div class="absolute bottom-0 right-0 w-16 h-16 bg-primary/10 rounded-full animate-ping-slow" style="animation-delay: 1s" />
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <Button
                    variant="default"
                    size="lg"
                    :href="route('home')"
                    as="a"
                    class="min-w-[180px]"
                >
                    <LucideHome :size="20" class="mr-2" />
                    Torna alla Home
                </Button>
                
                <Button
                    variant="outline"
                    size="lg"
                    @click="goBack"
                    class="min-w-[180px]"
                >
                    <LucideArrowLeft :size="20" class="mr-2" />
                    Torna Indietro
                </Button>
            </div>

            <!-- Help text -->
            <div class="mt-12 pt-8 border-t border-surface-200 dark:border-surface-800">
                <p class="text-sm text-surface-500 dark:text-surface-400">
                    Se pensi che questo sia un errore, 
                    <a href="mailto:support@maglink.com" class="text-primary hover:underline font-medium">
                        contatta il supporto
                    </a>
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes float {
    0%, 100% {
        transform: translateY(0px) translateX(0px);
    }
    25% {
        transform: translateY(-20px) translateX(10px);
    }
    50% {
        transform: translateY(-10px) translateX(-10px);
    }
    75% {
        transform: translateY(-30px) translateX(5px);
    }
}

@keyframes pulse-slow {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

@keyframes spin-slow {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@keyframes bounce-slow {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-15px);
    }
}

@keyframes ping-slow {
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-pulse-slow {
    animation: pulse-slow 3s ease-in-out infinite;
}

.animate-spin-slow {
    animation: spin-slow 20s linear infinite;
}

.animate-bounce-slow {
    animation: bounce-slow 3s ease-in-out infinite;
}

.animate-ping-slow {
    animation: ping-slow 3s cubic-bezier(0, 0, 0.2, 1) infinite;
}
</style>
