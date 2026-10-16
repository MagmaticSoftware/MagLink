<script setup lang="ts">
import { usePage, Link } from '@inertiajs/vue3';
import { Moon, Sun, ChevronRight, Home, Menu as MenuIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useDark, useToggle } from '@vueuse/core';
import { useI18n } from 'vue-i18n';
import Breadcrumb from '@/components/volt/Breadcrumb.vue';
import Menu from '@/components/volt/Menu.vue';
import LanguageSelector from '@/components/LanguageSelector.vue';
import { useRoute } from '@/composables/useRoute';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const emit = defineEmits<{ (e: 'toggle-sidebar'): void }>();

const page = usePage();
const user = computed(() => (page.props as any).auth?.user);
const { t } = useI18n();
const appRoute = useRoute();

// Convert breadcrumbs format for Volt component
const voltBreadcrumbs = computed(() => {
    return props.breadcrumbs?.map(item => ({
        label: item.title,
        url: item.href
    })) || [];
});

// Dark mode
const isDark = useDark({
    selector: 'html',
    attribute: 'class',
    valueDark: 'dark',
    valueLight: 'light',
});
const toggleDark = useToggle(isDark);

// User menu
const userMenu = ref();
const userMenuItems = computed(() => [
    {
        label: t('common.settings'),
        icon: 'pi pi-cog',
        command: () => {
            window.location.href = appRoute('profile.edit', { tenant: page.props.auth.tenant });
        }
    },
    {
        label: 'Piani e Prezzi',
        icon: 'pi pi-credit-card',
        command: () => {
            window.location.href = appRoute('plans.index', { tenant: page.props.auth.tenant });
        }
    },
    {
        separator: true
    },
    {
        label: t('common.logout'),
        icon: 'pi pi-sign-out',
        command: () => {
            // Trigger Inertia logout
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = route('logout');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfToken) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_token';
                input.value = csrfToken;
                form.appendChild(input);
            }
            document.body.appendChild(form);
            form.submit();
        }
    }
]);

const toggleUserMenu = (event: Event) => {
    userMenu.value.toggle(event);
};

const getUserInitials = () => {
    if (!user.value) return '';
    const first = user.value.first_name?.charAt(0) || '';
    const last = user.value.last_name?.charAt(0) || '';
    return (first + last).toUpperCase();
};
</script>

<template>
    <header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-2 sm:gap-4 bg-white/80 dark:bg-surface-950/80 backdrop-blur-md shadow-sm px-3 sm:px-6">
        <!-- Mobile menu button -->
        <button
            type="button"
            class="md:hidden flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-surface-700 dark:text-surface-200 hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors"
            aria-label="Menu"
            @click="emit('toggle-sidebar')"
        >
            <MenuIcon :size="20" />
        </button>

        <!-- Breadcrumbs (nascosti su mobile, troppo stretti) -->
        <div class="hidden sm:flex min-w-0 flex-1 flex-row justify-start items-center overflow-hidden">
            <Link :href="route('tenant.index', { tenant: page.props.auth.tenant })" class="inline-flex items-center mr-3">
                <Home :size="18" class="text-surface-400 dark:text-surface-400 hover:text-primary dark:hover:text-primary transition-colors" />
            </Link>
            <ChevronRight :size="16" class="text-surface-400 dark:text-surface-500" />
            <Breadcrumb 
                v-if="voltBreadcrumbs && voltBreadcrumbs.length > 0" 
                :model="voltBreadcrumbs"
                class="bg-transparent px-2"
            >
                <template #separator>
                    <ChevronRight :size="16" class="text-surface-400 dark:text-surface-500" />
                </template>
            </Breadcrumb>
        </div>

        <div class="flex-1 sm:hidden"></div>

        <!-- Right Side Actions -->
        <div class="flex shrink-0 items-center gap-1 sm:gap-3">
            <!-- Language Selector -->
            <LanguageSelector />
            
            <!-- Dark Mode Toggle -->
            <button
                @click="toggleDark()"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors"
                :title="isDark ? t('common.lightMode') : t('common.darkMode')"
            >
                <Moon v-if="!isDark" :size="18" />
                <Sun v-else :size="18" />
            </button>

            <!-- User Menu -->
            <div class="relative">
                <button
                    @click="toggleUserMenu"
                    class="flex items-center gap-3 rounded-lg px-1 sm:px-3 py-2 hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors"
                >
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-primary-contrast text-sm font-semibold">
                        {{ getUserInitials() }}
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-medium text-surface-900 dark:text-surface-50">
                            {{ user?.first_name }} {{ user?.last_name }}
                        </p>
                        <p class="text-xs text-surface-500 dark:text-surface-400">
                            {{ user?.email }}
                        </p>
                    </div>
                </button>
                
                <Menu ref="userMenu" :model="userMenuItems" popup />
            </div>
        </div>
    </header>
</template>
