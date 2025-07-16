<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { LucideEdit, LucideTrash2, LucidePlus } from 'lucide-vue-next';

const props = defineProps<{
    pages: any[]; // Adjust type as necessary
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pages',
        href: route('pages.index'),
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mt-6 flex justify-end">
                <Link
                    :href="route('pages.create')"
                    class="btn btn-primary flex items-center gap-2"
                >
                    <LucidePlus class="w-4 h-4" /> Aggiungi nuova pagina
                </Link>
            </div>
            <div class="flex flex-col gap-4">
                <div
                    v-for="page in props.pages"
                    :key="page.id"
                    class="flex items-center justify-between rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 shadow transition hover:shadow-md"
                >
                    <div class="flex flex-col">
                        <h3 class="text-lg font-semibold">{{ page.title }}</h3>
                        <p class="text-sm text-gray-500">{{ page.description }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <Link
                            :href="route('pages.edit', page.id)"
                            class="btn btn-sm btn-secondary flex items-center gap-1"
                        >
                            <LucideEdit class="w-4 h-4 mr-1" /> Modifica
                        </Link>
                        <Link
                            :href="route('pages.destroy', page.id)"
                            method="delete"
                            as="button"
                            class="btn btn-sm btn-danger flex items-center gap-1"
                        >
                            <LucideTrash2 class="w-4 h-4 mr-1" /> Elimina
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
