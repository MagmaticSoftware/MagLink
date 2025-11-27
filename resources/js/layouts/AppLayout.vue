<script setup lang="ts">
import AppSidebar from '@/components/AppSidebar.vue';
import AppTopBar from '@/components/AppTopBar.vue';
import { useI18n } from 'vue-i18n';
import type { BreadcrumbItemType } from '@/types';
interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const { t } = useI18n();
</script>

<template>
    <div class="flex min-h-screen w-full bg-surface-50 dark:bg-surface-900">
        <!-- Sidebar -->
        <AppSidebar />

        <!-- Main Content Area -->
        <div class="flex flex-1 flex-col ml-64">
            <!-- Top Bar -->
            <AppTopBar :breadcrumbs="breadcrumbs" />

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <slot />
            </main>

            <!-- Footer (optional) -->
            <footer class="bg-transparent px-6 py-6">
                <div class="flex items-center justify-between text-sm text-surface-500 dark:text-surface-400">
                    <p>&copy; {{ new Date().getFullYear() }} MagLink. {{ t('footer.copyright') }}</p>
                    <div class="flex gap-6">
                        <a href="#" class="hover:text-primary transition-colors">{{ t('footer.privacy') }}</a>
                        <a href="#" class="hover:text-primary transition-colors">{{ t('footer.terms') }}</a>
                        <a href="#" class="hover:text-primary transition-colors">{{ t('footer.support') }}</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>
