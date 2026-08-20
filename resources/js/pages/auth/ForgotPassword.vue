<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout :title="t('auth.forgotPassword.title')" :description="t('auth.forgotPassword.description')">
        <Head :title="t('auth.forgotPassword.headTitle')" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <div class="space-y-6">
            <form @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="email" class="text-gray-700 dark:text-gray-300">{{ t('auth.forgotPassword.emailLabel') }}</Label>
                    <Input id="email" type="email" name="email" autocomplete="off" v-model="form.email" autofocus placeholder="email@example.com" class="dark:bg-gray-900 dark:border-gray-700 dark:text-white dark:placeholder-gray-500" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button class="w-full bg-gradient-to-r from-orange-600 to-orange-800 hover:from-orange-600 hover:to-orange-700 dark:from-orange-600 dark:to-orange-700 dark:hover:from-orange-700 dark:hover:to-orange-800 text-white" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        {{ t('auth.forgotPassword.submit') }}
                    </Button>
                </div>
            </form>

            <div class="space-x-1 text-center text-sm text-gray-600 dark:text-gray-400">
                <span>{{ t('auth.forgotPassword.orReturn') }}</span>
                <TextLink :href="route('login')" class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300">{{ t('auth.forgotPassword.logIn') }}</TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
