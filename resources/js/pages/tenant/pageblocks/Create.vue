<script lang="ts" setup>
import { ref } from 'vue';
import axios from 'axios';
import { 
    Type, 
    Code, 
    Image, 
    Link2, 
    Video,
    CheckCircle2,
    ArrowRight,
    ArrowLeft
} from 'lucide-vue-next';

const emit = defineEmits(['created']);

const props = defineProps<{
    page: {
        id: number;
        user_id: number;
        company_id: number;
        tenant_id: string;
        slug: string;
        title: string;
        description: string | null;
        style: any;
        settings: any;
        is_active: boolean;
        views: number;
        last_viewed_at: string | null;
        published_at: string | null;
    };
}>();

const step = ref<'select' | 'configure'>('select');
const type = ref('text');
const title = ref('');
const content = ref('');
const loading = ref(false);

const blockTypes = [
    {
        type: 'text',
        name: 'Testo',
        description: 'Blocco di testo semplice con formattazione',
        icon: Type,
        color: 'blue',
    },
    {
        type: 'html',
        name: 'HTML',
        description: 'Codice HTML personalizzato',
        icon: Code,
        color: 'purple',
    },
    {
        type: 'image',
        name: 'Immagine',
        description: 'Visualizza un\'immagine',
        icon: Image,
        color: 'green',
    },
    {
        type: 'link',
        name: 'Link',
        description: 'Collegamento cliccabile con anteprima',
        icon: Link2,
        color: 'orange',
    },
    {
        type: 'video',
        name: 'Video',
        description: 'Embed video da YouTube o Vimeo',
        icon: Video,
        color: 'red',
    },
];

const selectBlockType = (blockType: string) => {
    type.value = blockType;
    step.value = 'configure';
};

const goBack = () => {
    step.value = 'select';
};

const createBlock = async () => {
    loading.value = true;
    try {
        const response = await axios.post(route('page-blocks.store'), {
            page_id: props.page.id,
            type: type.value,
            title: title.value,
            content: content.value,
            style: JSON.stringify({}),
            settings: JSON.stringify({}),
            is_active: true,
        });
        emit('created', response.data.block);
        // Reset campi
        step.value = 'select';
        type.value = 'text';
        title.value = '';
        content.value = '';
    } catch (error) {
        console.error('Errore creazione blocco:', error);
    } finally {
        loading.value = false;
    }
};

const getColorClasses = (color: string) => {
    const colors: Record<string, { bg: string; border: string; text: string; hover: string }> = {
        blue: {
            bg: 'bg-blue-50 dark:bg-blue-950/30',
            border: 'border-blue-200 dark:border-blue-800',
            text: 'text-blue-600 dark:text-blue-400',
            hover: 'hover:border-blue-400 dark:hover:border-blue-600',
        },
        purple: {
            bg: 'bg-purple-50 dark:bg-purple-950/30',
            border: 'border-purple-200 dark:border-purple-800',
            text: 'text-purple-600 dark:text-purple-400',
            hover: 'hover:border-purple-400 dark:hover:border-purple-600',
        },
        green: {
            bg: 'bg-green-50 dark:bg-green-950/30',
            border: 'border-green-200 dark:border-green-800',
            text: 'text-green-600 dark:text-green-400',
            hover: 'hover:border-green-400 dark:hover:border-green-600',
        },
        orange: {
            bg: 'bg-orange-50 dark:bg-orange-950/30',
            border: 'border-orange-200 dark:border-orange-800',
            text: 'text-orange-600 dark:text-orange-400',
            hover: 'hover:border-orange-400 dark:hover:border-orange-600',
        },
        red: {
            bg: 'bg-red-50 dark:bg-red-950/30',
            border: 'border-red-200 dark:border-red-800',
            text: 'text-red-600 dark:text-red-400',
            hover: 'hover:border-red-400 dark:hover:border-red-600',
        },
    };
    return colors[color] || colors.blue;
};
</script>

<template>
    <div class="p-4">
        <!-- Step 1: Selezione tipo blocco -->
        <div v-if="step === 'select'" class="space-y-4 w-2xl">
            <p class="text-sm text-surface-600 dark:text-surface-400 mb-6">
                Seleziona il tipo di blocco che vuoi aggiungere alla tua pagina
            </p>
            
            <div class="grid grid-cols-1 gap-3">
                <button
                    v-for="block in blockTypes"
                    :key="block.type"
                    @click="selectBlockType(block.type)"
                    :class="[
                        'group relative flex items-start gap-4 p-4 rounded-xl border-2 transition-all duration-200',
                        'hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]',
                        getColorClasses(block.color).bg,
                        getColorClasses(block.color).border,
                        getColorClasses(block.color).hover,
                    ]"
                >
                    <div 
                        :class="[
                            'flex items-center justify-center w-12 h-12 rounded-lg shrink-0',
                            'bg-white dark:bg-surface-900 border-2',
                            getColorClasses(block.color).border,
                        ]"
                    >
                        <component 
                            :is="block.icon" 
                            :class="['w-6 h-6', getColorClasses(block.color).text]"
                        />
                    </div>
                    
                    <div class="flex-1 text-left">
                        <h3 :class="['font-semibold text-lg mb-1', getColorClasses(block.color).text]">
                            {{ block.name }}
                        </h3>
                        <p class="text-sm text-surface-600 dark:text-surface-400">
                            {{ block.description }}
                        </p>
                    </div>
                    
                    <div class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                        <div class="w-8 h-8 rounded-full bg-white dark:bg-surface-900 flex items-center justify-center border-2 border-surface-300 dark:border-surface-700">
                            <span class="text-surface-600 dark:text-surface-400"><ArrowRight :size="16" /></span>
                        </div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Step 2: Configurazione blocco -->
        <div v-else class="space-y-4 w-2xl">
            <div class="flex items-center gap-3 mb-6">
                <button
                    @click="goBack"
                    class="flex items-center justify-center w-8 h-8 rounded-lg bg-surface-100 dark:bg-surface-800 hover:bg-surface-200 dark:hover:bg-surface-700 transition-colors"
                >
                    <span class="text-surface-600 dark:text-surface-400"><ArrowLeft :size="16" /></span>
                </button>
                
                <div class="flex items-center gap-3">
                    <div 
                        v-if="blockTypes.find(b => b.type === type)"
                        :class="[
                            'flex items-center justify-center w-10 h-10 rounded-lg border-2',
                            'bg-white dark:bg-surface-900',
                            getColorClasses(blockTypes.find(b => b.type === type)?.color || 'blue').border,
                        ]"
                    >
                        <component 
                            :is="blockTypes.find(b => b.type === type)?.icon" 
                            :class="[
                                'w-5 h-5',
                                getColorClasses(blockTypes.find(b => b.type === type)?.color || 'blue').text
                            ]"
                        />
                    </div>
                    <div>
                        <h3 class="font-semibold text-surface-900 dark:text-surface-100">
                            {{ blockTypes.find(b => b.type === type)?.name }}
                        </h3>
                        <p class="text-xs text-surface-500 dark:text-surface-400">
                            {{ blockTypes.find(b => b.type === type)?.description }}
                        </p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="createBlock" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                        Titolo
                    </label>
                    <input 
                        v-model="title" 
                        type="text" 
                        required
                        class="w-full px-4 py-2.5 rounded-lg border border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-900 text-surface-900 dark:text-surface-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent transition-all"
                        placeholder="Inserisci un titolo..."
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                        Contenuto
                    </label>
                    <textarea 
                        v-model="content" 
                        rows="4"
                        required
                        class="w-full px-4 py-2.5 rounded-lg border border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-900 text-surface-900 dark:text-surface-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent transition-all resize-none"
                        :placeholder="
                            type === 'text' ? 'Inserisci il testo...' :
                            type === 'html' ? 'Inserisci il codice HTML...' :
                            type === 'image' ? 'Inserisci l\'URL dell\'immagine...' :
                            type === 'link' ? 'Inserisci l\'URL del link...' :
                            type === 'video' ? 'Inserisci l\'URL del video (YouTube o Vimeo)...' :
                            'Inserisci il contenuto...'
                        "
                    />
                </div>

                <div class="flex gap-3 pt-4">
                    <button
                        type="button"
                        @click="goBack"
                        class="flex-1 px-4 py-2.5 rounded-lg border-2 border-surface-300 dark:border-surface-700 text-surface-700 dark:text-surface-300 font-medium hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors"
                        :disabled="loading"
                    >
                        Annulla
                    </button>
                    <button
                        type="submit"
                        :class="[
                            'flex-1 px-4 py-2.5 rounded-lg font-medium transition-all flex items-center justify-center gap-2',
                            'bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700',
                            'text-white shadow-lg shadow-blue-600/30',
                            'disabled:opacity-50 disabled:cursor-not-allowed',
                        ]"
                        :disabled="loading"
                    >
                        <CheckCircle2 v-if="!loading" class="w-5 h-5" />
                        <span v-if="loading" class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        {{ loading ? 'Creazione...' : 'Crea blocco' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>