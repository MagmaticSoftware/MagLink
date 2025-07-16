<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { LucideEdit, LucideTrash2, LucidePlus } from 'lucide-vue-next';

const props = defineProps<{
    links: any[]; // Adjust type as necessary
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Links',
        href: route('links.index'),
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            
            <div class="mt-6 flex justify-end">
                <Link
                    :href="route('links.create')"
                    class="btn btn-primary flex items-center gap-2"
                >
                    <LucidePlus class="w-4 h-4" /> Aggiungi nuovo link
                </Link>
            </div>
            <div class="flex flex-col gap-4">
                <div
                    v-for="link in props.links"
                    :key="link.id"
                    class="flex items-center justify-between rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 shadow transition hover:shadow-md"
                >
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ link.title }}</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-1">{{ link.description }}</p>
                        <div class="flex items-center gap-2 text-xs text-blue-600 dark:text-blue-400">
                            <span class="font-medium">URL:</span>
                            <a
                                :href="`/links/${link.slug}`"
                                target="_blank"
                                class="underline hover:text-blue-800"
                            >
                                {{ `/links/${link.slug}` }}
                            </a>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <Link
                            :href="route('links.edit', link.id)"
                            class="btn btn-sm btn-secondary flex items-center gap-1"
                        >
                            <LucideEdit class="w-4 h-4 mr-1" /> Modifica
                        </Link>
                        <Link
                            :href="route('links.destroy', link.id)"
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
