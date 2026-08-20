<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
    <AuthLayout :title="t('auth.verifyEmail.title')" :description="t('auth.verifyEmail.description')">
        <Head :title="t('auth.verifyEmail.headTitle')" />

        <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ t('auth.verifyEmail.linkSent') }}
        </div>

        <form @submit.prevent="submit" class="space-y-6 text-center">
            <Button class="w-full bg-gradient-to-r from-orange-600 to-orange-800 hover:from-orange-600 hover:to-orange-700 dark:from-orange-600 dark:to-orange-700 dark:hover:from-orange-700 dark:hover:to-orange-800 text-white" :disabled="form.processing">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                {{ t('auth.verifyEmail.resend') }}
            </Button>

            <TextLink :href="route('logout')" method="post" as="button" class="mx-auto block text-sm">{{ t('auth.verifyEmail.logout') }}</TextLink>
        </form>
    </AuthLayout>
</template>
