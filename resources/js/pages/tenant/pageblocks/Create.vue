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
    ArrowLeft,
    Heading,
    Minus,
    MapPin,
    MousePointer,
    Share2
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

// Campi extra per blocchi specifici
const buttonBgColor = ref('#3b82f6');
const buttonTextColor = ref('#ffffff');
const socialType = ref('facebook');
const imageLinkUrl = ref('');

// Social network configurations
const socialConfig: Record<string, { name: string; color: string }> = {
    facebook: { name: 'Facebook', color: '#1877F2' },
    twitter: { name: 'Twitter / X', color: '#000000' },
    instagram: { name: 'Instagram', color: '#E4405F' },
    linkedin: { name: 'LinkedIn', color: '#0A66C2' },
    youtube: { name: 'YouTube', color: '#FF0000' },
    tiktok: { name: 'TikTok', color: '#000000' },
    pinterest: { name: 'Pinterest', color: '#E60023' },
    whatsapp: { name: 'WhatsApp', color: '#25D366' },
    telegram: { name: 'Telegram', color: '#26A5E4' },
    github: { name: 'GitHub', color: '#181717' },
    discord: { name: 'Discord', color: '#5865F2' },
    twitch: { name: 'Twitch', color: '#9146FF' },
    snapchat: { name: 'Snapchat', color: '#FFFC00' },
    reddit: { name: 'Reddit', color: '#FF4500' },
};

const blockTypes = [
    {
        type: 'text',
        name: 'Testo',
        description: 'Blocco di testo semplice con formattazione',
        icon: Type,
        color: 'blue',
    },
    {
        type: 'title',
        name: 'Titolo',
        description: 'Titolo di sezione per organizzare i contenuti',
        icon: Heading,
        color: 'indigo',
    },
    {
        type: 'separator',
        name: 'Separatore',
        description: 'Linea divisoria per separare visivamente le sezioni',
        icon: Minus,
        color: 'slate',
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
    {
        type: 'map',
        name: 'Mappa',
        description: 'Mappa interattiva con OpenStreetMap',
        icon: MapPin,
        color: 'cyan',
    },
    {
        type: 'button',
        name: 'Bottone',
        description: 'Pulsante personalizzato con colore di sfondo',
        icon: MousePointer,
        color: 'blue',
    },
    {
        type: 'social',
        name: 'Social',
        description: 'Link ai social network con icone e colori nativi',
        icon: Share2,
        color: 'pink',
    },
];

const selectBlockType = (blockType: string) => {
    type.value = blockType;
    // Separator viene inserito direttamente senza configurazione
    if (blockType === 'separator') {
        createBlockDirect(blockType, '', '');
    } else {
        step.value = 'configure';
    }
};

const goBack = () => {
    step.value = 'select';
};

const createBlockDirect = async (blockType: string, blockTitle: string, blockContent: string, blockSettings: any = {}) => {
    loading.value = true;
    try {
        const response = await axios.post(route('page-blocks.store'), {
            page_id: props.page.id,
            type: blockType,
            title: blockTitle,
            content: blockContent,
            style: JSON.stringify({}),
            settings: JSON.stringify(blockSettings),
            is_active: true,
        });
        emit('created', response.data.block);
        // Reset campi
        step.value = 'select';
        type.value = 'text';
        title.value = '';
        content.value = '';
        buttonBgColor.value = '#3b82f6';
        buttonTextColor.value = '#ffffff';
        socialType.value = 'facebook';
        imageLinkUrl.value = '';
    } catch (error) {
        console.error('Errore creazione blocco:', error);
    } finally {
        loading.value = false;
    }
};

const createBlock = async () => {
    let settings = {};
    let blockTitle = title.value;
    let blockContent = content.value;
    
    // Gestione specifica per ogni tipo di blocco
    if (type.value === 'button') {
        console.log('Creating button with colors:', buttonBgColor.value, buttonTextColor.value);
        settings = {
            backgroundColor: buttonBgColor.value,
            textColor: buttonTextColor.value,
        };
    } else if (type.value === 'social') {
        console.log('Creating social with type:', socialType.value);
        settings = {
            socialType: socialType.value,
        };
        // Se non c'è un titolo, usa il nome del social
        if (!blockTitle) {
            blockTitle = socialConfig[socialType.value].name;
        }
    } else if (type.value === 'image') {
        settings = {
            link: imageLinkUrl.value,
        };
    } else if (type.value === 'title') {
        // Per il titolo, metti il contenuto sia in title che in content
        blockContent = title.value;
    }
    
    await createBlockDirect(type.value, blockTitle, blockContent, settings);
};

const getColorClasses = (color: string) => {
    const colors: Record<string, { bg: string; border: string; text: string; hover: string }> = {
        blue: {
            bg: 'bg-blue-50 dark:bg-blue-950/30',
            border: 'border-blue-200 dark:border-blue-800',
            text: 'text-blue-600 dark:text-blue-400',
            hover: 'hover:border-blue-400 dark:hover:border-blue-600',
        },
        indigo: {
            bg: 'bg-indigo-50 dark:bg-indigo-950/30',
            border: 'border-indigo-200 dark:border-indigo-800',
            text: 'text-indigo-600 dark:text-indigo-400',
            hover: 'hover:border-indigo-400 dark:hover:border-indigo-600',
        },
        slate: {
            bg: 'bg-slate-50 dark:bg-slate-950/30',
            border: 'border-slate-200 dark:border-slate-800',
            text: 'text-slate-600 dark:text-slate-400',
            hover: 'hover:border-slate-400 dark:hover:border-slate-600',
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
        cyan: {
            bg: 'bg-cyan-50 dark:bg-cyan-950/30',
            border: 'border-cyan-200 dark:border-cyan-800',
            text: 'text-cyan-600 dark:text-cyan-400',
            hover: 'hover:border-cyan-400 dark:hover:border-cyan-600',
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
                <!-- Titolo - Mostrato per tutti tranne title -->
                <div v-if="type !== 'title'">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                        {{ type === 'text' || type === 'image' ? 'Titolo (facoltativo)' : 
                           type === 'button' ? 'Testo del Bottone' :
                           type === 'social' ? 'Testo (facoltativo)' :
                           'Titolo' }}
                    </label>
                    <input 
                        v-model="title" 
                        type="text" 
                        :required="type !== 'text' && type !== 'image' && type !== 'social'"
                        class="w-full px-4 py-2.5 rounded-lg border border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-900 text-surface-900 dark:text-surface-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent transition-all"
                        :placeholder="
                            type === 'button' ? 'es. Visita il sito' :
                            type === 'social' ? 'Lascia vuoto per usare il nome del social' :
                            'Inserisci un titolo...'
                        "
                    />
                    <p v-if="type === 'social'" class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                        Se lasci vuoto, verrà usato il nome del social network
                    </p>
                </div>

                <!-- Campo specifico per Title: Testo del Titolo -->
                <div v-if="type === 'title'">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                        Testo del Titolo
                    </label>
                    <input 
                        v-model="title" 
                        type="text" 
                        required
                        class="w-full px-4 py-2.5 rounded-lg border border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-900 text-surface-900 dark:text-surface-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent transition-all"
                        placeholder="Inserisci il testo del titolo..."
                    />
                </div>

                <!-- Dropdown Social -->
                <div v-if="type === 'social'">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                        Social Network
                    </label>
                    <select 
                        v-model="socialType"
                        class="w-full px-4 py-2.5 rounded-lg border border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-900 text-surface-900 dark:text-surface-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent transition-all"
                    >
                        <option v-for="(config, key) in socialConfig" :key="key" :value="key">
                            {{ config.name }}
                        </option>
                    </select>
                </div>

                <!-- Colori per Button -->
                <div v-if="type === 'button'" class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            Colore Sfondo
                        </label>
                        <div class="flex gap-2">
                            <input 
                                v-model="buttonBgColor" 
                                type="color"
                                class="h-11 w-14 rounded border border-surface-300 dark:border-surface-600 cursor-pointer"
                            />
                            <input 
                                v-model="buttonBgColor" 
                                type="text"
                                class="flex-1 px-4 py-2.5 rounded-lg border border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-900 text-surface-900 dark:text-surface-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent transition-all font-mono text-sm"
                                placeholder="#3b82f6"
                            />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            Colore Testo
                        </label>
                        <div class="flex gap-2">
                            <input 
                                v-model="buttonTextColor" 
                                type="color"
                                class="h-11 w-14 rounded border border-surface-300 dark:border-surface-600 cursor-pointer"
                            />
                            <input 
                                v-model="buttonTextColor" 
                                type="text"
                                class="flex-1 px-4 py-2.5 rounded-lg border border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-900 text-surface-900 dark:text-surface-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent transition-all font-mono text-sm"
                                placeholder="#ffffff"
                            />
                        </div>
                    </div>
                </div>

                <!-- Contenuto/URL - Non mostrato per title -->
                <div v-if="type !== 'title'">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                        {{ type === 'image' || type === 'link' || type === 'button' || type === 'social' ? 'URL' : 'Contenuto' }}
                    </label>
                    <textarea 
                        v-if="type === 'text' || type === 'html'"
                        v-model="content" 
                        rows="4"
                        required
                        class="w-full px-4 py-2.5 rounded-lg border border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-900 text-surface-900 dark:text-surface-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent transition-all resize-none"
                        :placeholder="
                            type === 'text' ? 'Inserisci il testo...' :
                            type === 'html' ? 'Inserisci il codice HTML...' :
                            'Inserisci il contenuto...'
                        "
                    />
                    <input 
                        v-else
                        v-model="content" 
                        type="url"
                        required
                        class="w-full px-4 py-2.5 rounded-lg border border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-900 text-surface-900 dark:text-surface-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent transition-all font-mono"
                        :placeholder="
                            type === 'image' ? 'https://example.com/image.jpg' :
                            type === 'link' ? 'https://example.com' :
                            type === 'video' ? 'https://www.youtube.com/watch?v=...' :
                            type === 'button' ? 'https://example.com' :
                            type === 'social' ? 'https://facebook.com/yourprofile' :
                            'https://example.com'
                        "
                    />
                </div>

                <!-- Link URL opzionale per Image -->
                <div v-if="type === 'image'">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                        Link URL (facoltativo)
                    </label>
                    <input 
                        v-model="imageLinkUrl" 
                        type="url"
                        class="w-full px-4 py-2.5 rounded-lg border border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-900 text-surface-900 dark:text-surface-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent transition-all font-mono"
                        placeholder="https://example.com"
                    />
                    <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                        L'immagine diventerà cliccabile se inserisci un link
                    </p>
                </div>

                <!-- Anteprima per Button -->
                <div v-if="type === 'button' && title">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                        Anteprima
                    </label>
                    <div 
                        class="w-full px-6 py-3 rounded-lg text-center font-semibold cursor-default transition-all"
                        :style="{ 
                            backgroundColor: buttonBgColor,
                            color: buttonTextColor 
                        }"
                    >
                        {{ title || 'Button Preview' }}
                    </div>
                </div>

                <!-- Anteprima per Social -->
                <div v-if="type === 'social'">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                        Anteprima
                    </label>
                    <div 
                        class="w-full px-6 py-3 rounded-lg flex items-center justify-center gap-3 cursor-default transition-all"
                        :style="{ 
                            backgroundColor: socialConfig[socialType].color,
                            color: socialType === 'snapchat' ? '#000000' : '#ffffff'
                        }"
                    >
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-white/20 flex items-center justify-center">
                            <span class="text-xs font-bold">{{ socialType.charAt(0).toUpperCase() }}</span>
                        </div>
                        <span class="text-base font-semibold">
                            {{ title || socialConfig[socialType].name }}
                        </span>
                    </div>
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