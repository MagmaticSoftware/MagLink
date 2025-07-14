<script setup lang="ts">    
    defineProps<{
        label: string;
    }>();
</script>

<template>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ label }}</label>
</template>
