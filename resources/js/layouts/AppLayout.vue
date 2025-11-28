<script setup lang="ts">
import AppSidebar from '@/components/AppSidebar.vue';
import AppTopBar from '@/components/AppTopBar.vue';
import MadeWithLove from '@/components/MadeWithLove.vue';
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

            <!-- Footer -->
            <footer class="bg-transparent">
                <MadeWithLove />
            </footer>
        </div>
    </div>
</template>
