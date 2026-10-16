<script setup lang="ts">
import { ArrowUpDown, Filter, Search, X } from 'lucide-vue-next';

interface Option {
    label: string;
    value: string;
}

defineProps<{
    searchPlaceholder: string;
    sortOptions: Option[];
    filterOptions: Option[];
    sortLabel: string;
    filterLabel: string;
}>();

const search = defineModel<string>('search', { required: true });
const sort = defineModel<string>('sort', { required: true });
const filter = defineModel<string>('filter', { required: true });

const selectClass =
    'h-10 w-full cursor-pointer appearance-none rounded-lg border border-surface-200 bg-white pr-8 pl-9 text-sm text-surface-700 focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 focus:outline-none dark:border-surface-700 dark:bg-surface-800 dark:text-surface-200';
</script>

<template>
    <div class="ml-card flex flex-col gap-2 p-2 md:flex-row md:items-center">
        <label class="relative flex-1">
            <span class="sr-only">{{ searchPlaceholder }}</span>
            <Search :size="17" class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-surface-400" />
            <input
                v-model="search"
                type="search"
                :placeholder="searchPlaceholder"
                class="h-10 w-full rounded-lg border border-transparent bg-surface-50 pr-9 pl-9 text-sm text-surface-900 placeholder:text-surface-400 focus:border-primary-400 focus:bg-white focus:ring-2 focus:ring-primary-500/20 focus:outline-none dark:bg-surface-900/60 dark:text-white dark:focus:bg-surface-900"
            />
            <button
                v-if="search"
                type="button"
                class="absolute top-1/2 right-2 -translate-y-1/2 rounded p-1 text-surface-400 hover:text-surface-700 dark:hover:text-surface-200"
                @click="search = ''"
            >
                <X :size="14" />
            </button>
        </label>

        <div class="grid grid-cols-2 gap-2 md:flex md:w-auto">
            <label class="relative md:w-48">
                <span class="sr-only">{{ sortLabel }}</span>
                <ArrowUpDown :size="15" class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-surface-400" />
                <select v-model="sort" :class="selectClass">
                    <option v-for="option in sortOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                </select>
                <svg class="pointer-events-none absolute top-1/2 right-2.5 h-4 w-4 -translate-y-1/2 text-surface-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </label>
            <label class="relative md:w-48">
                <span class="sr-only">{{ filterLabel }}</span>
                <Filter :size="15" class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-surface-400" />
                <select v-model="filter" :class="selectClass">
                    <option v-for="option in filterOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                </select>
                <svg class="pointer-events-none absolute top-1/2 right-2.5 h-4 w-4 -translate-y-1/2 text-surface-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </label>
        </div>
    </div>
</template>
