<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { 
    LucidePalette, 
    LucideImage, 
    LucideUpload,
    LucideX,
    LucideCrown,
    LucideLock
} from 'lucide-vue-next';
import Button from '@/components/volt/Button.vue';
import { type PageProps } from '@/types/inertia';
import axios from 'axios';

const page = usePage<PageProps>();

const props = defineProps<{
    modelValue: any;
    previewUrl: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: any];
    'update:previewUrl': [url: string];
}>();

// Get user QR customization permissions
const qrCustomization = computed(() => {
    const billing = page.props.billing as any;
    
    if (!billing || !billing.qr_customization) {
        return {
            colors: true,
            logo: false,
            remove_background: false,
            max_size: 512,
        };
    }
    return billing.qr_customization;
});

const currentPlan = computed(() => page.props.billing?.plan || 'free');

// Initialize customization from props or defaults
const initCustomization = () => {
    const saved = props.modelValue?.customization;
    return {
        colors: {
            foreground: saved?.colors?.foreground || '#000000',
            background: saved?.colors?.background || '#FFFFFF',
        },
        logo: saved?.logo || null,
        logo_size: saved?.logo_size || 'medium',
        logo_rounded: saved?.logo_rounded || false,
    };
};

// Customization state
const customization = ref(initCustomization());

// Logo upload state
const logoFile = ref<File | null>(null);
const logoPreview = ref<string | null>(null);
const uploading = ref(false);
const previousRemoveBackground = ref(false);

const logoSizes = [
    { value: 'small', label: 'Small (15%)' },
    { value: 'medium', label: 'Medium (20%)' },
    { value: 'large', label: 'Large (25%)' },
];

// Watch customization changes and emit
watch(customization, (newVal) => {
    emit('update:modelValue', { customization: newVal });
    updatePreviewUrl();
}, { deep: true });

// Watch for changes to base preview URL (when content changes in parent)
watch(() => props.previewUrl, () => {
    updatePreviewUrl();
});

// Update preview URL with customization params
const updatePreviewUrl = () => {
    const url = new URL(props.previewUrl, window.location.origin);
    url.searchParams.set('color_fg', customization.value.colors.foreground);
    url.searchParams.set('color_bg', customization.value.colors.background);
    
    if (customization.value.logo) {
        url.searchParams.set('logo', customization.value.logo);
        url.searchParams.set('logo_size', customization.value.logo_size);
        if (customization.value.logo_rounded) {
            url.searchParams.set('logo_rounded', '1');
        }
        // Add cache-busting timestamp to force image reload
        url.searchParams.set('_t', Date.now().toString());
    }
    
    emit('update:previewUrl', url.toString());
};

// Handle logo file selection
const handleLogoSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (!file) return;
    
    logoFile.value = file;
    
    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
        logoPreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
    
    // Upload file
    uploadLogo();
};

// Upload logo to server
const uploadLogo = async () => {
    if (!logoFile.value) return;
    
    uploading.value = true;
    
    const formData = new FormData();
    formData.append('logo', logoFile.value);
    
    try {
        const tenant = page.props.auth?.tenant || '';
        const response = await axios.post(route('qrcodes.logo.upload', { tenant }), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        
        customization.value.logo = response.data.path;
        logoPreview.value = response.data.url;
        
        // Force preview update after logo upload
        updatePreviewUrl();
    } catch (error: any) {
        console.error('Logo upload failed:', error);
        alert(error.response?.data?.error || 'Failed to upload logo');
    } finally {
        uploading.value = false;
    }
};

// Remove logo
const removeLogo = async () => {
    if (!customization.value.logo) return;
    
    try {
        const tenant = page.props.auth?.tenant || '';
        await axios.delete(route('qrcodes.logo.delete', { tenant }), {
            data: { path: customization.value.logo },
        });
    } catch (error) {
        console.error('Logo deletion failed:', error);
    }
    
    customization.value.logo = null;
    logoPreview.value = null;
    logoFile.value = null;
    
    // Force preview update after logo removal
    updatePreviewUrl();
};

// Get upgrade message for locked features
const getUpgradeMessage = (feature: string) => {
    if (feature === 'logo') {
        return currentPlan.value === 'free' ? 'Upgrade to Professional or Enterprise' : '';
    }
    return 'Upgrade to unlock';
};

// Initialize logo preview on mount if logo exists
onMounted(() => {
    if (customization.value.logo) {
        // Set logo preview URL from storage path
        logoPreview.value = `/storage/user-uploads/${customization.value.logo}`;
    }
    
    // Update preview URL with initial customizations
    updatePreviewUrl();
});
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center gap-2 text-lg font-semibold text-surface-900 dark:text-surface-50">
            <LucidePalette :size="20" />
            QR Code Customization
        </div>

        <!-- Colors Section (Available to all) -->
        <div class="space-y-4">
            <div class="flex items-center gap-2">
                <LucidePalette :size="18" class="text-surface-600 dark:text-surface-400" />
                <h3 class="font-medium text-surface-900 dark:text-surface-50">Colors</h3>
                <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                    All Plans
                </span>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                        Foreground Color
                    </label>
                    <div class="flex items-center gap-2">
                        <input 
                            type="color" 
                            v-model="customization.colors.foreground"
                            class="w-12 h-10 rounded border border-surface-300 dark:border-surface-600 cursor-pointer"
                        />
                        <input 
                            type="text" 
                            v-model="customization.colors.foreground"
                            class="flex-1 px-3 py-2 bg-surface-50 dark:bg-surface-800 border border-surface-300 dark:border-surface-600 rounded-lg text-sm"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                        Background Color
                    </label>
                    <div class="flex items-center gap-2">
                        <input 
                            type="color" 
                            v-model="customization.colors.background"
                            class="w-12 h-10 rounded border border-surface-300 dark:border-surface-600 cursor-pointer"
                        />
                        <input 
                            type="text" 
                            v-model="customization.colors.background"
                            class="flex-1 px-3 py-2 bg-surface-50 dark:bg-surface-800 border border-surface-300 dark:border-surface-600 rounded-lg text-sm"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Logo Section (Professional+) -->
        <div class="space-y-4" :class="{ 'opacity-50': !qrCustomization.logo }">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <LucideImage :size="18" class="text-surface-600 dark:text-surface-400" />
                    <h3 class="font-medium text-surface-900 dark:text-surface-50">Logo</h3>
                    <span 
                        v-if="qrCustomization.logo"
                        class="text-xs px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400"
                    >
                        Professional+
                    </span>
                    <div 
                        v-else
                        class="flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400"
                    >
                        <LucideLock :size="12" />
                        {{ getUpgradeMessage('logo') }}
                    </div>
                </div>
            </div>

            <template v-if="qrCustomization.logo">
                <!-- Logo Upload -->
                <div v-if="!logoPreview" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-lg p-6 text-center">
                    <LucideUpload :size="32" class="mx-auto mb-2 text-surface-400" />
                    <p class="text-sm text-surface-600 dark:text-surface-400 mb-3">
                        Upload your logo (PNG, JPG, SVG)
                    </p>
                    <input 
                        type="file" 
                        accept="image/png,image/jpeg,image/jpg,image/svg+xml"
                        @change="handleLogoSelect"
                        class="hidden"
                        id="logo-upload"
                    />
                    <label for="logo-upload">
                        <Button as="span" variant="outline" size="sm" class="cursor-pointer">
                            Choose File
                        </Button>
                    </label>
                </div>

                <!-- Logo Preview -->
                <div v-else class="flex items-center gap-4 p-4 bg-surface-50 dark:bg-surface-800 rounded-lg border border-surface-200 dark:border-surface-700">
                    <img :src="logoPreview" alt="Logo preview" class="w-16 h-16 object-contain bg-white rounded" />
                    <div class="flex-1">
                        <p class="text-sm font-medium text-surface-900 dark:text-surface-50">Logo uploaded</p>
                        <p class="text-xs text-surface-500 dark:text-surface-400">{{ logoFile?.name }}</p>
                    </div>
                    <Button variant="ghost" size="sm" @click="removeLogo">
                        <LucideX :size="16" />
                    </Button>
                </div>

                <!-- Logo Size -->
                <div v-if="logoPreview">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                        Logo Size
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <button
                            type="button"
                            v-for="size in logoSizes"
                            :key="size.value"
                            @click="customization.logo_size = size.value"
                            :class="[
                                'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                customization.logo_size === size.value
                                    ? 'bg-primary text-white'
                                    : 'bg-surface-100 dark:bg-surface-800 text-surface-700 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-700'
                            ]"
                        >
                            {{ size.label }}
                        </button>
                    </div>
                </div>

                <!-- Rounded Corners -->
                <div v-if="logoPreview" class="flex items-center gap-2">
                    <input 
                        type="checkbox" 
                        v-model="customization.logo_rounded"
                        id="logo-rounded"
                        class="w-4 h-4 rounded border-surface-300 dark:border-surface-600"
                    />
                    <label for="logo-rounded" class="text-sm text-surface-700 dark:text-surface-300">
                        Rounded corners
                    </label>
                </div>
            </template>

            <!-- Locked State -->
            <div v-else class="p-4 bg-surface-50 dark:bg-surface-800 rounded-lg border border-surface-200 dark:border-surface-700 text-center">
                <LucideCrown :size="32" class="mx-auto mb-2 text-amber-500" />
                <p class="text-sm font-medium text-surface-900 dark:text-surface-50 mb-1">Logo customization locked</p>
                <p class="text-xs text-surface-600 dark:text-surface-400 mb-3">
                    Upgrade to Professional or Enterprise to add your logo
                </p>
                <Button variant="primary" size="sm" :href="route('billing.portal')" as="a">
                    <LucideCrown :size="14" class="mr-1" />
                    Upgrade Plan
                </Button>
            </div>
        </div>

        <!-- Export Quality Info -->
        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <p class="text-xs text-blue-900 dark:text-blue-200">
                <strong>Export Quality:</strong> Your plan allows exports up to {{ qrCustomization.max_size }}px
            </p>
        </div>
    </div>
</template>
