<script setup lang="ts">
import { CreditCard, Zap } from 'lucide-vue-next';

interface TrialData {
    active: boolean;
    expired: boolean;
    days_left: number;
    ends_at: string | null;
}

const props = defineProps<{
    trial: TrialData;
}>();

const emit = defineEmits<{
    'show-plan-modal': [];
}>();

const getBannerClass = () => {
    if (props.trial.days_left > 7) {
        return 'from-blue-500/10 to-blue-600/10 border-blue-500/20';
    } else if (props.trial.days_left > 3) {
        return 'from-yellow-500/10 to-yellow-600/10 border-yellow-500/20';
    } else {
        return 'from-red-500/10 to-red-600/10 border-red-500/20';
    }
};

const getTextClass = () => {
    if (props.trial.days_left > 7) {
        return 'text-blue-700 dark:text-blue-300';
    } else if (props.trial.days_left > 3) {
        return 'text-yellow-700 dark:text-yellow-300';
    } else {
        return 'text-red-700 dark:text-red-300';
    }
};

const getIconClass = () => {
    if (props.trial.days_left > 7) {
        return 'text-blue-500';
    } else if (props.trial.days_left > 3) {
        return 'text-yellow-500';
    } else {
        return 'text-red-500';
    }
};
</script>

<template>
    <div v-if="trial.active" 
        :class="getBannerClass()" 
        class="bg-gradient-to-br border rounded-lg p-3 mx-2 mb-4 relative overflow-hidden">
        
        <!-- Background pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, currentColor 1px, transparent 0); background-size: 12px 12px;"></div>
        </div>
        
        <div class="relative">
            <!-- Header -->
            <div class="flex items-center mb-3">
                <div class="flex items-center">
                    <Zap :class="getIconClass()" class="w-4 h-4 mr-2" />
                    <span :class="getTextClass()" class="text-xs font-bold uppercase tracking-wide">Prova Gratuita</span>
                </div>
            </div>
            
            <!-- Days left with circular design -->
            <div class="flex items-center mb-3">
                <!-- Circular counter -->
                <div :class="getTextClass()" class="flex-shrink-0 w-12 h-12 rounded-full border-3 flex items-center justify-center mr-3"
                     :style="`border-color: ${trial.days_left > 7 ? 'rgb(59 130 246)' : trial.days_left > 3 ? 'rgb(234 179 8)' : 'rgb(239 68 68)'}`">
                    <span class="text-lg font-black">{{ trial.days_left }}</span>
                </div>
                
                <!-- Text on the right -->
                <div class="flex-1">
                    <p :class="getTextClass()" class="text-xs font-medium leading-tight">
                        {{ trial.days_left === 1 ? 'giorno rimane' : 'giorni rimanenti' }}
                    </p>
                    <p :class="getTextClass()" class="text-xs opacity-75 leading-tight">
                        nella tua prova gratuita
                    </p>
                </div>
            </div>
            
            <!-- Progress bar for last 7 days -->
            <div v-if="trial.days_left <= 7" class="mb-3">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                    <div 
                        :class="trial.days_left > 3 ? 'bg-yellow-500' : 'bg-red-500'"
                        class="h-1.5 rounded-full transition-all duration-300"
                        :style="`width: ${(trial.days_left / 30) * 100}%`">
                    </div>
                </div>
            </div>
            
            <!-- CTA Button -->
            <button 
                @click="emit('show-plan-modal')"
                class="w-full inline-flex items-center justify-center px-3 py-1.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white text-xs font-semibold rounded-md transition-all duration-200 shadow-sm hover:shadow-md cursor-pointer">
                <CreditCard class="w-3 h-3 mr-1.5" />
                Scegli Piano
            </button>
        </div>
    </div>
</template>
