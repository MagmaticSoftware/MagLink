<script setup lang="ts">
import axios from 'axios';
import { reactive, ref, computed, watch } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import LinkBlock from '@/components/tenant/pageblocks/edit/Link.vue';
import HtmlBlock from '@/components/tenant/pageblocks/edit/Html.vue';
import TextBlock from '@/components/tenant/pageblocks/edit/Text.vue';
import ImageBlock from '@/components/tenant/pageblocks/edit/Image.vue';
import DefaultBlock from '@/components/tenant/pageblocks/edit/Default.vue';
import VideoBlock from '@/components/tenant/pageblocks/edit/Video.vue';
import TitleBlock from '@/components/tenant/pageblocks/edit/Title.vue';
import SeparatorBlock from '@/components/tenant/pageblocks/edit/Separator.vue';
import MapBlock from '@/components/tenant/pageblocks/edit/Map.vue';
import ButtonBlock from '@/components/tenant/pageblocks/edit/Button.vue';
import SocialBlock from '@/components/tenant/pageblocks/edit/Social.vue';
import Dialog from '@/components/volt/Dialog.vue';
import ConfirmDialog from '@/components/volt/ConfirmDialog.vue';
import Button from '@/components/volt/Button.vue';
import CreateBlock from '../pageblocks/Create.vue';
import InputText from '@/components/volt/InputText.vue';
import Textarea from '@/components/volt/Textarea.vue';
import ToggleSwitch from '@/components/volt/ToggleSwitch.vue';
import LimitReachedDialog from '@/components/LimitReachedDialog.vue';
import PlanSelectionModal from '@/components/PlanSelectionModal.vue';
import { usePage } from '@inertiajs/vue3';
import { useConfirm } from 'primevue/useconfirm';
import { 
    LucideSave, 
    LucidePlus, 
    LucideLayout, 
    LucideEye, 
    LucideCalendar,
    LucideToggleLeft,
    LucideToggleRight,
    LucideTrash2
} from 'lucide-vue-next';
import { GridLayout, GridItem } from 'grid-layout-plus';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const confirm = useConfirm();
const page = usePage();

const blockComponents: Record<string, any> = {
    link: LinkBlock,
    html: HtmlBlock,
    text: TextBlock,
    image: ImageBlock,
    video: VideoBlock,
    title: TitleBlock,
    separator: SeparatorBlock,
    map: MapBlock,
    button: ButtonBlock,
    social: SocialBlock,
};

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
    blocks: Array<{
        id: number;
        page_id: number;
        type: string;
        content: any;
        position: any;
        style?: any;
        settings?: any;
        is_active: boolean;
        title?: string;
        size?: any;
        created_at: string;
        updated_at: string;
    }>;
}>();

// Helper per estrarre solo la data (YYYY-MM-DD) da una stringa ISO datetime
const extractDate = (dateString: string | null): string => {
    if (!dateString) return '';
    return dateString.split('T')[0];
};

const form = useForm({
    title: props.page.title,
    slug: props.page.slug,
    description: props.page.description ?? '',
    style: props.page.style ?? {},
    settings: props.page.settings ?? {},
    is_active: props.page.is_active,
    published_at: extractDate(props.page.published_at),
});

// Theme colors palette
const themeColors = [
    { name: 'Blue', value: 'blue', class: 'bg-blue-500', hover: 'hover:border-blue-400', border: 'border-blue-400' },
    { name: 'Indigo', value: 'indigo', class: 'bg-indigo-500', hover: 'hover:border-indigo-400', border: 'border-indigo-400' },
    { name: 'Purple', value: 'purple', class: 'bg-purple-500', hover: 'hover:border-purple-400', border: 'border-purple-400' },
    { name: 'Pink', value: 'pink', class: 'bg-pink-500', hover: 'hover:border-pink-400', border: 'border-pink-400' },
    { name: 'Red', value: 'red', class: 'bg-red-500', hover: 'hover:border-red-400', border: 'border-red-400' },
    { name: 'Orange', value: 'orange', class: 'bg-orange-500', hover: 'hover:border-orange-400', border: 'border-orange-400' },
    { name: 'Amber', value: 'amber', class: 'bg-amber-500', hover: 'hover:border-amber-400', border: 'border-amber-400' },
    { name: 'Yellow', value: 'yellow', class: 'bg-yellow-500', hover: 'hover:border-yellow-400', border: 'border-yellow-400' },
    { name: 'Lime', value: 'lime', class: 'bg-lime-500', hover: 'hover:border-lime-400', border: 'border-lime-400' },
    { name: 'Green', value: 'green', class: 'bg-green-500', hover: 'hover:border-green-400', border: 'border-green-400' },
    { name: 'Emerald', value: 'emerald', class: 'bg-emerald-500', hover: 'hover:border-emerald-400', border: 'border-emerald-400' },
    { name: 'Teal', value: 'teal', class: 'bg-teal-500', hover: 'hover:border-teal-400', border: 'border-teal-400' },
    { name: 'Cyan', value: 'cyan', class: 'bg-cyan-500', hover: 'hover:border-cyan-400', border: 'border-cyan-400' },
    { name: 'Sky', value: 'sky', class: 'bg-sky-500', hover: 'hover:border-sky-400', border: 'border-sky-400' },
];

const selectedColor = ref(props.page.style?.primaryColor || 'blue');

const selectColor = (color: string) => {
    selectedColor.value = color;
    form.style = { ...form.style, primaryColor: color };
};

// Helper per formattare la data in formato YYYY-MM-DD
const formatDate = (date: Date): string => {
    return date.toISOString().split('T')[0];
};

// Watch per gestire la data di pubblicazione quando si attiva/disattiva la pagina
watch(() => form.is_active, (isActive, oldValue) => {
    if (isActive && !form.published_at) {
        // Se viene attivata e non c'è data, imposta la data odierna
        form.published_at = formatDate(new Date());
    } else if (!isActive && oldValue === true) {
        // Se viene disattivata, cancella la data di pubblicazione
        form.published_at = '';
    }
}, { immediate: true });

const getColorClasses = (color: string) => {
    const colorMap: Record<string, string> = {
        blue: 'border-blue-400 dark:border-blue-500',
        indigo: 'border-indigo-400 dark:border-indigo-500',
        purple: 'border-purple-400 dark:border-purple-500',
        pink: 'border-pink-400 dark:border-pink-500',
        red: 'border-red-400 dark:border-red-500',
        orange: 'border-orange-400 dark:border-orange-500',
        amber: 'border-amber-400 dark:border-amber-500',
        yellow: 'border-yellow-400 dark:border-yellow-500',
        lime: 'border-lime-400 dark:border-lime-500',
        green: 'border-green-400 dark:border-green-500',
        emerald: 'border-emerald-400 dark:border-emerald-500',
        teal: 'border-teal-400 dark:border-teal-500',
        cyan: 'border-cyan-400 dark:border-cyan-500',
        sky: 'border-sky-400 dark:border-sky-500',
    };
    return colorMap[color] || colorMap.blue;
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('pages.title'),
        href: route('pages.index'),
    },
    {
        title: t('pages.edit'),
        href: route('pages.edit', props.page.id),
    },
];

// Compute stats
const pageStats = computed(() => ({
    totalBlocks: layout.length,
    publishedBlocks: layout.filter((b: any) => b.is_active).length,
    views: props.page.views || 0,
}));

// Track dirty blocks (blocks with unsaved changes)
const dirtyBlocks = ref(new Set<number>());

const handleDirtyChange = (blockId: number, isDirty: boolean) => {
    if (isDirty) {
        dirtyBlocks.value.add(blockId);
    } else {
        dirtyBlocks.value.delete(blockId);
    }
};

const hasDirtyBlocks = computed(() => dirtyBlocks.value.size > 0);

const handleProfileImageUpload = async (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (!file) return;
    
    const formData = new FormData();
    formData.append('profile_image', file);
    
    try {
        const response = await axios.post(route('pages.upload-profile-image', props.page.slug), formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        
        // L'immagine è già stata salvata nel database dal backend
        // Aggiorniamo solo il form per mostrare l'anteprima
        form.settings = {
            ...form.settings,
            profile_image: response.data.url
        };
        
        console.log('Profile image uploaded successfully:', response.data.url);
    } catch (error) {
        console.error('Failed to upload profile image:', error);
    }
};

const handleDeleteProfileImage = async () => {
    confirm.require({
        message: 'Vuoi eliminare definitivamente l\'immagine del profilo?',
        header: 'Conferma eliminazione',
        acceptProps: {
            label: 'Elimina',
            severity: 'danger'
        },
        rejectProps: {
            label: 'Annulla'
        },
        accept: async () => {
            try {
                await axios.delete(route('pages.delete-profile-image', props.page.slug));
                
                // Rimuovi l'immagine dal form
                form.settings = {
                    ...form.settings,
                    profile_image: null
                };
                
                console.log('Profile image deleted successfully');
            } catch (error) {
                console.error('Failed to delete profile image:', error);
            }
        }
    });
};

const doSubmitForm = () => {
    form.put(route('pages.update', props.page.slug), {
        onSuccess: () => {
            // Non resettiamo il form per mantenere le settings
        },
        onError: (errors: Record<string, any>) => {
            console.error('Form submission errors:', errors);
        },
    });
};

const submitForm = () => {
    if (hasDirtyBlocks.value) {
        confirm.require({
            message: `Hai ${dirtyBlocks.value.size} blocco/i con modifiche non salvate. Vuoi procedere a salvare la pagina senza salvare i blocchi?`,
            header: 'Modifiche non salvate',
            acceptProps: {
                label: 'Salva comunque',
                severity: 'warn'
            },
            rejectProps: {
                label: 'Annulla'
            },
            accept: () => {
                doSubmitForm();
            }
        });
    } else {
        doSubmitForm();
    }
};

let layout = reactive(
    Array.isArray(props.blocks)
        ? props.blocks.map((block, index) => {
            const x = block.position?.x ?? (index % 2);
            const y = block.position?.y ?? Math.floor(index / 2);
            // Title e Separator hanno altezza 1 forzata
            const height = (block.type === 'title' || block.type === 'separator') ? 1 : (block.size?.height ?? 2);
            return {
                x,
                y,
                w: block.size?.width ?? 1,
                h: height,
                i: String(block.id),
                id: block.id,
                page_id: block.page_id,
                type: block.type,
                title: block.title,
                content: block.content,
                position: { x, y },
                size: block.size ?? { width: 1, height: 2 },
                style: block.style,
                settings: block.settings,
                is_active: block.is_active,
                static: false
            };
        })
        : []
);


const updateAllPositions = () => {
    layout.forEach(item => {
        item.position.x = item.x;
        item.position.y = item.y;
    });
    const positions = layout.map(item => ({
        id: item.id,
        x: item.x,
        y: item.y
    }));
    axios.post(route('blocks.positions'), { positions });
};

const updateSize = (item: any) => {
    const block = props.blocks.find((b: any) => b.id == item.i);
    if (block) {
        if (!block.size) block.size = {};
        block.size.width = item.w;
        block.size.height = item.h;
        // Chiamata API per aggiornare grandezza
        axios.post(route('blocks.size', block.id), {
            width: item.w,
            height: item.h
        });
    }
};

const visible = ref(false);
const showLimitDialog = ref(false);
const showPlanModal = ref(false);

const handleShowPlans = () => {
    showPlanModal.value = true;
};

// Computed per gestire i limiti
const blockLimit = computed(() => {
    return page.props.billing?.limits?.blocks_per_page || 0;
});

const getLimitStatus = (current: number, limit: number) => {
    if (limit === 0) return 'normal';
    if (current >= limit) return 'exceeded';
    if (current >= limit - 1) return 'warning';
    return 'normal';
};

const getLimitClasses = (status: string) => {
    if (status === 'exceeded') return 'text-red-700 dark:text-red-400';
    if (status === 'warning') return 'text-orange-600 dark:text-orange-400';
    return 'text-surface-900 dark:text-surface-50';
};

const handleAddBlockClick = () => {
    const limits = page.props.billing?.limits;
    const currentBlockCount = layout.length;
    const blockLimit = limits?.blocks_per_page || 0;
    
    // Se il limite è -1 (illimitato) o 0 (non impostato) o non raggiunto, mostra il dialog di creazione
    if (blockLimit === -1 || blockLimit === 0 || currentBlockCount < blockLimit) {
        visible.value = true;
    } else {
        // Altrimenti mostra la dialog di limite raggiunto
        showLimitDialog.value = true;
    }
};

// Trova il primo spazio vuoto disponibile nella griglia
function findFirstEmptySpace(blockWidth: number, blockHeight: number): { x: number; y: number } {
    const gridCols = 4; // La griglia ha 4 colonne
    
    // Crea una mappa di occupazione della griglia
    const occupiedCells = new Set<string>();
    layout.forEach(item => {
        for (let y = item.y; y < item.y + item.h; y++) {
            for (let x = item.x; x < item.x + item.w; x++) {
                occupiedCells.add(`${x},${y}`);
            }
        }
    });
    
    // Trova il primo spazio libero che può contenere il blocco
    let maxRow = 0;
    if (layout.length > 0) {
        maxRow = Math.max(...layout.map(item => item.y + item.h));
    }
    
    // Cerca nelle righe esistenti e in quelle nuove (fino a maxRow + 10)
    for (let y = 0; y < maxRow + 10; y++) {
        for (let x = 0; x <= gridCols - blockWidth; x++) {
            // Verifica se questo spazio è libero per l'intero blocco
            let isFree = true;
            for (let dy = 0; dy < blockHeight; dy++) {
                for (let dx = 0; dx < blockWidth; dx++) {
                    if (occupiedCells.has(`${x + dx},${y + dy}`)) {
                        isFree = false;
                        break;
                    }
                }
                if (!isFree) break;
            }
            
            if (isFree) {
                return { x, y };
            }
        }
    }
    
    // Se non trova spazio, metti in coda
    return { x: 0, y: maxRow };
}

function handleBlockCreated(newBlock: any) {
    // Trova il primo spazio vuoto disponibile
    const size = newBlock.size ?? { width: 1, height: 2 };
    const position = findFirstEmptySpace(size.width, size.height);
    
    const item = {
        x: position.x,
        y: position.y,
        w: size.width,
        h: size.height,
        i: String(newBlock.id),
        id: newBlock.id,
        page_id: newBlock.page_id,
        type: newBlock.type,
        title: newBlock.title,
        content: newBlock.content,
        position: position,
        size: size,
        style: newBlock.style,
        settings: newBlock.settings,
        is_active: newBlock.is_active,
        static: false
    };
    layout.push(item);
    
    // Salva immediatamente la posizione calcolata nel database
    axios.put(route('page-blocks.update', newBlock.id), {
        position: position,
        size: size
    }).catch(error => {
        console.error('Failed to save block position:', error);
    });
    
    visible.value = false;
}

function handleBlockDeleted(blockId: number) {
    const index = layout.findIndex(item => item.id === blockId);
    if (index !== -1) {
        layout.splice(index, 1);
    }
}
</script>

<template>
    <Head :title="t('pages.edit')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-surface-900 dark:text-surface-50">{{ t('pages.edit') }}</h1>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">{{ props.page.title }}</p>
                </div>
                <div class="flex gap-2">
                    <Button 
                        type="button" 
                        severity="secondary"
                        @click="handleAddBlockClick"
                        class="flex items-center gap-2"
                    >
                        <LucidePlus :size="16" />
                        {{ t('pages.addBlock') }}
                    </Button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-surface-600 dark:text-surface-400">{{ t('pages.stats.totalBlocks') }}</p>
                            <p 
                                :class="[
                                    'text-3xl font-bold mt-2',
                                    blockLimit > 0 ? getLimitClasses(getLimitStatus(pageStats.totalBlocks, blockLimit)) : 'text-surface-900 dark:text-surface-50'
                                ]"
                            >
                                {{ pageStats.totalBlocks }}<span v-if="blockLimit > 0" class="text-lg"> / {{ blockLimit }}</span>
                            </p>
                            <p 
                                v-if="blockLimit > 0 && getLimitStatus(pageStats.totalBlocks, blockLimit) === 'exceeded'"
                                class="text-xs text-red-700 dark:text-red-400 mt-1"
                            >
                                Limite raggiunto per il piano free
                            </p>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <LucideLayout :size="24" class="text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-surface-600 dark:text-surface-400">{{ t('pages.stats.activeBlocks') }}</p>
                            <p class="text-3xl font-bold text-surface-900 dark:text-surface-50 mt-2">{{ pageStats.publishedBlocks }}</p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <component :is="form.is_active ? LucideToggleRight : LucideToggleLeft" :size="24" class="text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-surface-600 dark:text-surface-400">{{ t('pages.stats.views') }}</p>
                            <p class="text-3xl font-bold text-surface-900 dark:text-surface-50 mt-2">{{ pageStats.views }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <LucideEye :size="24" class="text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Settings Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-surface-900 rounded-xl p-4 shadow-sm border border-surface-200 dark:border-surface-800 sticky top-6">
                        <h3 class="text-lg font-semibold text-surface-900 dark:text-surface-50 mb-4">
                            {{ t('pages.settings') }}
                        </h3>
                        
                        <form @submit.prevent="submitForm" class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    {{ t('pages.form.title') }}
                                </label>
                                <InputText v-model="form.title" required class="w-full" />
                            </div>

                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    Slug (URL)
                                </label>
                                <InputText v-model="form.slug" required class="w-full font-mono text-sm" />
                                <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                                    Public URL: /{{ form.slug }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    {{ t('pages.form.description') }}
                                </label>
                                <Textarea v-model="form.description" rows="3" class="w-full" />
                            </div>

                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    Immagine Profilo (opzionale)
                                </label>
                                <input 
                                    type="file" 
                                    accept="image/*"
                                    @change="handleProfileImageUpload"
                                    class="w-full text-sm text-surface-700 dark:text-surface-300
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-lg file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-primary-50 file:text-primary-700
                                        hover:file:bg-primary-100
                                        dark:file:bg-primary-900/30 dark:file:text-primary-400"
                                />
                                <div v-if="form.settings?.profile_image" class="mt-3 relative inline-block">
                                    <img 
                                        :src="form.settings.profile_image"
                                        alt="Profile preview"
                                        class="w-24 h-24 rounded-full object-cover ring-2 ring-surface-200 dark:ring-surface-700"
                                    />
                                    <button
                                        @click="handleDeleteProfileImage"
                                        type="button"
                                        class="absolute -top-1 -right-1 p-1.5 bg-red-500 hover:bg-red-600 rounded-full shadow-lg transition-colors"
                                        title="Elimina immagine"
                                    >
                                        <LucideTrash2 :size="14" class="text-white" />
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-2">
                                    {{ t('pages.form.published') }}
                                </label>
                                <input 
                                    v-model="form.published_at" 
                                    type="date" 
                                    class="w-full rounded-md border border-surface-300 dark:border-surface-700 bg-surface-0 dark:bg-surface-950 text-surface-700 dark:text-surface-0 px-3 py-2 text-sm focus:border-primary focus:outline-none"
                                />
                            </div>

                            <div class="flex items-center justify-between p-3 bg-surface-50 dark:bg-surface-800 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <component 
                                        :is="form.is_active ? LucideToggleRight : LucideToggleLeft" 
                                        :size="18" 
                                        :class="form.is_active ? 'text-green-500' : 'text-surface-400'"
                                    />
                                    <div>
                                        <p class="text-sm font-medium text-surface-700 dark:text-surface-300">
                                            {{ t('pages.form.active') }}
                                        </p>
                                        <p class="text-xs text-surface-500 dark:text-surface-400">
                                            {{ form.is_active ? t('pages.form.activeHint') : t('pages.form.inactiveHint') }}
                                        </p>
                                    </div>
                                </div>
                                <ToggleSwitch v-model="form.is_active" />
                            </div>

                            <div class="pt-4 border-t border-surface-200 dark:border-surface-800">
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 block mb-3">
                                    Colore Tema
                                </label>
                                <div class="grid grid-cols-7 gap-2">
                                    <button
                                        v-for="color in themeColors"
                                        :key="color.value"
                                        type="button"
                                        @click="selectColor(color.value)"
                                        :title="color.name"
                                        :class="[
                                            'w-8 h-8 rounded-lg transition-all duration-200',
                                            color.class,
                                            'hover:scale-110 active:scale-95',
                                            'border-2',
                                            selectedColor === color.value
                                                ? 'border-surface-900 dark:border-surface-100 ring-2 ring-offset-2 ring-surface-400 dark:ring-surface-600 scale-110'
                                                : 'border-transparent hover:border-surface-300 dark:hover:border-surface-600'
                                        ]"
                                    />
                                </div>
                            </div>

                            <div class="pt-4 border-t border-surface-200 dark:border-surface-800">
                                <Button 
                                    type="submit" 
                                    class="w-full flex items-center justify-center gap-2"
                                >
                                    <LucideSave :size="16" />
                                    {{ t('pages.update') }}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Block Editor -->
                <div class="lg:col-span-4">
                    <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-surface-900 dark:text-surface-50">
                                {{ t('pages.blockEditor') }}
                            </h3>
                            <span class="text-sm text-surface-500 dark:text-surface-400">
                                {{ t('pages.dragAndDrop') }}
                            </span>
                        </div>

                        <div class="min-h-[600px] relative">
                            <GridLayout 
                                v-model:layout="layout" 
                                :col-num="4" 
                                :row-height="120" 
                                :is-draggable="true"
                                :is-resizable="true"
                                :vertical-compact="false"
                                :prevent-collision="true"
                                :use-css-transforms="true"
                                :margin="[32, 32]"
                                class="w-full"
                            >
                                <GridItem 
                                    v-for="item in layout" 
                                    :key="item.i" 
                                    :x="item.x" 
                                    :y="item.y" 
                                    :w="item.w" 
                                    :h="item.h"
                                    :i="item.i" 
                                    @resized="updateSize(item)"  
                                    @moved="updateAllPositions"
                                    :class="[
                                        'bg-gradient-to-br from-surface-50 to-surface-100 dark:from-surface-800 dark:to-surface-900 p-0 overflow-auto shadow-md hover:shadow-lg rounded-xl border-2 border-surface-200 dark:border-surface-700 transition-all duration-200',
                                        'hover:' + getColorClasses(selectedColor)
                                    ]"
                                >
                                    <component 
                                        :is="blockComponents[item.type] ?? DefaultBlock" 
                                        :id="item.id"
                                        :page-id="item.page_id" 
                                        :type="item.type" 
                                        :title="item.title" 
                                        :content="item.content"
                                        :position="item.position" 
                                        :size="item.size?.width ?? item.w" 
                                        :style="item.style"
                                        :settings="item.settings" 
                                        :is-active="item.is_active"
                                        @deleted="handleBlockDeleted"
                                        @dirty-change="handleDirtyChange"
                                    />
                                </GridItem>
                            </GridLayout>

                            <!-- Empty State -->
                            <div 
                                v-if="layout.length === 0" 
                                class="absolute inset-0 flex items-center justify-center"
                            >
                                <div class="text-center">
                                    <LucideLayout :size="48" class="mx-auto text-surface-400 mb-4" />
                                    <p class="text-surface-600 dark:text-surface-400 mb-4">
                                        {{ t('pages.noBlocks') }}
                                    </p>
                                    <Button 
                                        type="button" 
                                        @click="handleAddBlockClick"
                                        class="flex items-center gap-2 mx-auto"
                                    >
                                        <LucidePlus :size="16" />
                                        {{ t('pages.addFirstBlock') }}
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Block Dialog -->
        <Dialog 
            v-model:visible="visible" 
            modal 
            :header="t('pages.addBlock')" 
            class="max-w-2xl"
        >
            <CreateBlock :page="props.page" @created="handleBlockCreated" />
        </Dialog>

        <!-- Limit Reached Dialog -->
        <LimitReachedDialog
            v-model:visible="showLimitDialog"
            type="blocks"
            @show-plans="handleShowPlans"
        />

        <!-- Plan Selection Modal -->
        <PlanSelectionModal
            v-model:visible="showPlanModal"
            :plans="page.props.plans || {}"
            :is-new-user="page.props.billing?.isNewUser || false"
            :has-active-trial="page.props.billing?.hasActiveTrial || false"
            :can-start-trial="page.props.billing?.canStartTrial || false"
            :is-subscribed="page.props.billing?.isSubscribed || false"
            :current-plan-key="page.props.billing?.currentPlanKey || null"
            :on-free-plan="page.props.billing?.onFreePlan || false"
        />

        <!-- Confirm Dialog for unsaved changes -->
        <ConfirmDialog />
    </AppLayout>
</template>

<style scoped>
.vgl-layout {
  background-color: transparent;
  position: relative;
  min-height: 600px;
}

/* Grid background pattern */
.vgl-layout::before {
  position: absolute;
  inset: 0;
  content: '';
  background-image: 
    linear-gradient(to right, rgba(148, 163, 184, 0.1) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(148, 163, 184, 0.1) 1px, transparent 1px);
  background-repeat: repeat;
  background-size: calc(50% - 8px) 136px;
  pointer-events: none;
  z-index: 0;
}

:root.dark .vgl-layout::before {
  background-image: 
    linear-gradient(to right, rgba(71, 85, 105, 0.2) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(71, 85, 105, 0.2) 1px, transparent 1px);
}

/* Resize handle styling */
:deep(.vgl-item__resizer) {
  right: 0;
  bottom: 0;
  width: 20px;
  height: 20px;
  background-image: none !important;
  background: transparent !important;
}

/* Dragging state */
:deep(.vgl-item.dragging) {
  opacity: 0.8;
  transform: rotate(2deg);
  z-index: 1000;
}

:deep(.vgl-item.resizing) {
  opacity: 0.9;
  z-index: 1000;
}

/* Placeholder styling */
:deep(.vgl-item.placeholder) {
  background: rgb(59, 130, 246, 0.2) !important;
  border: 2px dashed rgb(59, 130, 246) !important;
  border-radius: 0.75rem;
}

/* Ensure items have proper z-index */
:deep(.vgl-item) {
  position: absolute;
  z-index: 1;
  transition: all 0.2s ease;
}
</style>
