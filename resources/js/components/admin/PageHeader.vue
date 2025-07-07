<script setup lang="ts">
    const props = defineProps({
        header: String,
    });
</script>

<template>
    <header class="px-6 py-2 bg-gray-200 md:flex md:items-center md:justify-between dark:bg-gray-800">
        <div class="flex-1 min-w-0">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white"><slot name="pageTitle" /></h2>
        </div>
    </header>
</template>