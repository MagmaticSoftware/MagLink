<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { LucideSparkles, LucideX } from 'lucide-vue-next';
import type { PageProps } from '@/types/inertia';

const props = defineProps<{
    message?: string;
    show?: boolean;
}>();

const emit = defineEmits<{
    close: [];
}>();

const page = usePage<PageProps>();

const isVisible = computed(() => {
    return props.show !== false;
});

const currentPlan = computed(() => {
    return page.props.billing?.currentPlanName || 'Free';
});

const displayMessage = computed(() => {
    return props.message || 'Hai raggiunto il limite del tuo piano. Effettua l\'upgrade per continuare!';
});
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="transform translate-y-[-100%] opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="transform translate-y-0 opacity-100"
        leave-to-class="transform translate-y-[-100%] opacity-0"
    >
        <div 
            v-if="isVisible"
            class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white shadow-lg"
        >
            <div class="max-w-7xl mx-auto px-4 py-3 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between flex-wrap">
                    <div class="flex items-center flex-1 min-w-0">
                        <span class="flex p-2 rounded-lg bg-white/20">
                            <LucideSparkles class="h-6 w-6" />
                        </span>
                        <p class="ml-3 font-medium text-white truncate">
                            <span class="md:hidden">{{ displayMessage }}</span>
                            <span class="hidden md:inline">
                                {{ displayMessage }}
                            </span>
                        </p>
                    </div>
                    <div class="flex items-center gap-3 mt-2 sm:mt-0">
                        <Link
                            :href="route('plans.index', { tenant: page.props.auth.tenant })"
                            class="inline-flex items-center px-4 py-2 border-2 border-white text-sm font-medium rounded-lg text-white hover:bg-white hover:text-purple-600 transition-all"
                        >
                            <LucideSparkles class="w-4 h-4 mr-2" />
                            Vedi i Piani
                        </Link>
                        <button
                            @click="emit('close')"
                            type="button"
                            class="flex p-2 rounded-lg hover:bg-white/20 transition-colors"
                        >
                            <span class="sr-only">Chiudi</span>
                            <LucideX class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
