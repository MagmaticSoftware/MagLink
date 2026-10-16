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
import PageHeader from '@/components/PageHeader.vue';
import CreateBlock from '../pageblocks/Create.vue';
import InputText from '@/components/volt/InputText.vue';
import Textarea from '@/components/volt/Textarea.vue';
import ToggleSetting from '@/components/ToggleSetting.vue';
import StatCard from '@/components/dashboard/StatCard.vue';
import LimitReachedDialog from '@/components/LimitReachedDialog.vue';
import PlanSelectionModal from '@/components/PlanSelectionModal.vue';
import { usePage } from '@inertiajs/vue3';
import { useConfirm } from 'primevue/useconfirm';
import { 
    LucideSave,
    LucideFileText,
    LucideSettings,
    LucideUpload,
    LucideMove,
    LucidePlus, 
    LucideLayout, 
    LucideEye, 
    LucideCalendar,
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
        <div class="flex w-full flex-col gap-4 sm:gap-5 lg:p-2">
            <PageHeader
                :title="t('pages.edit')"
                :subtitle="props.page.title"
                :icon="LucideFileText"
                icon-class="bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400"
                :back-href="route('pages.index')"
                :back-label="t('common.back')"
            >
                <template #actions>
                    <button type="button" class="ml-btn-secondary" @click="handleAddBlockClick">
                        <LucidePlus :size="16" /> {{ t('pages.addBlock') }}
                    </button>
                    <button type="submit" form="page-settings-form" class="ml-btn-primary" :disabled="form.processing">
                        <LucideSave :size="16" /> {{ t('pages.update') }}
                    </button>
                </template>
            </PageHeader>

            <!-- Statistiche -->
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-5">
                <StatCard
                    :label="t('pages.stats.totalBlocks')"
                    :value="blockLimit > 0 ? `${pageStats.totalBlocks} / ${blockLimit}` : String(pageStats.totalBlocks)"
                    :value-class="blockLimit > 0 && pageStats.totalBlocks >= blockLimit ? 'text-rose-600 dark:text-rose-400' : undefined"
                    :icon="LucideLayout"
                    icon-class="bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400"
                    color="#f97316"
                />
                <StatCard
                    :label="t('pages.stats.activeBlocks')"
                    :value="String(pageStats.publishedBlocks)"
                    :icon="LucideToggleRight"
                    icon-class="bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400"
                    color="#10b981"
                />
                <StatCard
                    :label="t('pages.stats.views')"
                    :value="String(pageStats.views)"
                    :icon="LucideEye"
                    icon-class="bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400"
                    color="#8b5cf6"
                />
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-[18rem_minmax(0,1fr)] 2xl:grid-cols-[20rem_minmax(0,1fr)]">
                <!-- Settings Sidebar -->
                <div class="min-w-0">
                    <div class="ml-card sticky top-20 p-5">
                        <h3 class="ml-section-title mb-4">
                            <LucideSettings :size="18" class="text-primary-500" /> {{ t('pages.settings') }}
                        </h3>
                        
                        <form id="page-settings-form" @submit.prevent="submitForm" class="space-y-4">
                            <div>
                                <label class="ml-label">
                                    {{ t('pages.form.title') }}
                                </label>
                                <InputText v-model="form.title" required class="w-full" />
                            </div>

                            <div>
                                <label class="ml-label">
                                    {{ t('pages.form.slug') }}
                                </label>
                                <InputText v-model="form.slug" required class="w-full font-mono text-sm" />
                                <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                                    {{ t('pages.form.publicUrl') }}: /{{ form.slug }}
                                </p>
                            </div>

                            <div>
                                <label class="ml-label">
                                    {{ t('pages.form.description') }}
                                </label>
                                <Textarea v-model="form.description" rows="3" class="w-full" />
                            </div>

                            <div>
                                <p class="ml-label">Immagine profilo (opzionale)</p>
                                <label class="ml-btn-secondary w-full cursor-pointer">
                                    <LucideUpload :size="15" /> {{ form.settings?.profile_image ? 'Cambia immagine' : 'Carica immagine' }}
                                    <input type="file" accept="image/*" class="sr-only" @change="handleProfileImageUpload" />
                                </label>
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
                                <label class="ml-label">
                                    {{ t('pages.form.published') }}
                                </label>
                                <input
                                    v-model="form.published_at"
                                    type="date"
                                    class="w-full rounded-lg border border-surface-200 bg-white px-3 py-2 text-sm text-surface-700 focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 focus:outline-none dark:border-surface-700 dark:bg-surface-900 dark:text-surface-100"
                                />
                            </div>

                            <ToggleSetting
                                v-model="form.is_active"
                                :icon="LucideToggleRight"
                                :title="t('pages.form.active')"
                                :description="form.is_active ? t('pages.form.activeHint') : t('pages.form.inactiveHint')"
                            />

                            <div class="border-t border-surface-100 pt-4 dark:border-surface-700">
                                <p class="ml-label">{{ t('pages.form.themeColor') }}</p>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="color in themeColors"
                                        :key="color.value"
                                        type="button"
                                        @click="selectColor(color.value)"
                                        :title="color.name"
                                        :aria-label="color.name"
                                        :class="[
                                            'h-7 w-7 rounded-lg border-2 transition-shadow',
                                            color.class,
                                            selectedColor === color.value
                                                ? 'border-white ring-2 ring-surface-900 dark:border-surface-900 dark:ring-surface-100'
                                                : 'border-transparent hover:ring-2 hover:ring-surface-300 dark:hover:ring-surface-600'
                                        ]"
                                    />
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Block Editor -->
                <div class="min-w-0">
                    <div class="ml-card p-5 sm:p-6">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <h3 class="ml-section-title">
                                <LucideLayout :size="18" class="text-primary-500" /> {{ t('pages.blockEditor') }}
                            </h3>
                            <span class="inline-flex items-center gap-1.5 text-xs text-surface-500 dark:text-surface-400">
                                <LucideMove :size="13" /> {{ t('pages.dragAndDrop') }}
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
                                    class="block-card overflow-hidden rounded-xl border border-surface-200 bg-white p-0 shadow-sm transition-shadow duration-200 hover:border-surface-300 hover:shadow-md dark:border-surface-700 dark:bg-surface-800 dark:hover:border-surface-600"
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

/* Sfondo a puntini dell'area di lavoro */
.vgl-layout::before {
  position: absolute;
  inset: 0;
  content: '';
  background-image: radial-gradient(rgba(148, 163, 184, 0.35) 1px, transparent 1px);
  background-size: 20px 20px;
  border-radius: 0.75rem;
  pointer-events: none;
  z-index: 0;
}

:root.dark .vgl-layout::before {
  background-image: radial-gradient(rgba(113, 113, 122, 0.35) 1px, transparent 1px);
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

/* Angolo di ridimensionamento discreto al posto di quello nero di default */
:deep(.vgl-item__resizer)::before {
  content: '';
  position: absolute;
  right: 6px;
  bottom: 6px;
  width: 8px;
  height: 8px;
  border: 0;
  border-right: 2px solid rgb(161 161 170 / 0.8);
  border-bottom: 2px solid rgb(161 161 170 / 0.8);
  border-bottom-right-radius: 2px;
  inset: auto 6px 6px auto;
}

/* Durante il trascinamento: leggermente sollevata, niente rotazione */
:deep(.vgl-item.dragging) {
  opacity: 0.95;
  box-shadow: 0 12px 32px -8px rgba(15, 23, 42, 0.25);
  z-index: 1000;
}

:deep(.vgl-item.resizing) {
  opacity: 0.9;
  z-index: 1000;
}

/* Segnaposto durante lo spostamento, nel colore del brand */
:deep(.vgl-item.placeholder) {
  background: color-mix(in srgb, var(--p-primary-500) 10%, transparent) !important;
  border: 2px dashed var(--p-primary-400) !important;
  border-radius: 0.75rem;
  opacity: 1 !important;
}

/* Ensure items have proper z-index */
:deep(.vgl-item) {
  position: absolute;
  z-index: 1;
  transition: all 0.2s ease;
}
</style>
