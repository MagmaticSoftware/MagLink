<script setup lang="ts">
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { availableLocales, setLocale } from '@/i18n';
import Menu from '@/components/volt/Menu.vue';
import { Globe } from 'lucide-vue-next';

const { locale } = useI18n();
const languageMenu = ref();

const languageMenuItems = ref(
    availableLocales.map(loc => ({
        label: `${loc.flag} ${loc.name}`,
        command: () => {
            setLocale(loc.code);
        }
    }))
);

const toggleLanguageMenu = (event: Event) => {
    languageMenu.value.toggle(event);
};

const currentLocale = () => {
    return availableLocales.find(loc => loc.code === locale.value);
};
</script>

<template>
    <div class="relative">
        <button
            @click="toggleLanguageMenu"
            class="flex h-9 w-9 items-center justify-center rounded-lg text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors"
            :title="currentLocale()?.name"
        >
            <Globe :size="18" />
        </button>
        
        <Menu ref="languageMenu" :model="languageMenuItems" popup />
    </div>
</template>
