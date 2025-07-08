<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import Fieldset from '@volt/Fieldset.vue' ;
import InputText from '@volt/InputText.vue';
import Message from '@volt/Message.vue';
import Password from '@volt/Password.vue';
import { ref } from 'vue';

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    slug: '',
    company_name: '',
    errors: {},
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

// Throttle utility
function throttle(fn: (...args: any[]) => void, wait: number) {
    let timeout: ReturnType<typeof setTimeout> | null = null;
    let lastArgs: any[];
    return function(this: any, ...args: any[]) {
        lastArgs = args;
        if (!timeout) {
            fn.apply(this, lastArgs);
            timeout = setTimeout(() => {
                timeout = null;
            }, wait);
        }
    };
}

const slugChecking = ref(false);
const lastCheckedSlug = ref('');

const checkSlug = throttle(async () => {
    form.errors.slug = '';
    if (form.slug.length < 3) {
        form.errors.slug = 'URL must be at least 3 characters long.';
        return;
    } else if (!/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(form.slug)) {
        form.errors.slug = 'URL can only contain lowercase letters, numbers, and hyphens.';
        return;
    }

    // Evita chiamate duplicate per lo stesso slug
    if (lastCheckedSlug.value === form.slug) return;
    lastCheckedSlug.value = form.slug;

    slugChecking.value = true;
    try {
        // Chiamata AJAX a una rotta finta
        const res = await fetch(`/api/check-slug?slug=${encodeURIComponent(form.slug)}`, { credentials: 'include' });
        const data = await res.json();
        if (data.exists) {
            form.errors.slug = 'This URL is already taken.';
        }
    } catch (e) {
        console.log(e)
        form.errors.slug = 'Error checking URL.';
    } finally {
        slugChecking.value = false;
    }
}, 500);
</script>

<template>
    <AuthBase title="Create an account" description="Enter your details below to create your account">
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6" autocomplete="off">
            <Fieldset legend="Company" class="mb-4">
                <div class="flex flex-col justify-start gap-y-4">
                    <div class="flex flex-col gap-2 w-full">
                        <label for="name">URL</label>
                        <div class="flex items-stretch w-full">
                            <span class="flex items-center justify-center px-2 border-y border-s border-surface-300 dark:border-surface-700 bg-surface-0 dark:bg-surface-950 text-surface-400 rounded-s-md text-sm">maglink.com/</span>
                            <InputText id="name" v-model="form.slug" pt:root="flex-1 rounded-s-none rounded-e-md" aria-describedby="name-help" placeholder="yourname" class="w-full" @keyup="checkSlug" />
                        </div>
                        <Message size="small" severity="secondary" variant="simple">Enter your public account URL</Message>
                        <Message v-if="form.errors.slug" size="small" severity="error" variant="simple">{{ form.errors.slug }}</Message>
                    </div>
                    
                    <div class="flex flex-col gap-2 w-full">
                        <label for="name">Company name</label>
                        <InputText id="name" v-model="form.company_name" aria-describedby="name-help" placeholder="Your company name" class="w-full" />
                        <Message v-if="form.errors.company_name" size="small" severity="error" variant="simple">{{ form.errors.company_name }}</Message>
                    </div>
                </div>
            </Fieldset>
            <Fieldset legend="Account Information" class="mb-4">
                <div class="flex flex-col justify-start gap-y-4">
                    <div class="flex flex-col gap-2 w-full">
                        <label for="first_name">First Name</label>
                        <InputText id="first_name" v-model="form.first_name" aria-describedby="first_name-help"/>
                        <Message size="small" severity="secondary" variant="simple">Enter your first name.</Message>
                        <Message v-if="form.errors.first_name" size="small" severity="error" variant="simple">{{ form.errors.first_name }}</Message>
                    </div>
                    
                    <div class="flex flex-col gap-2 w-full">
                        <label for="last_name">Last Name</label>
                        <InputText id="last_name" v-model="form.last_name" aria-describedby="last_name-help"/>
                        <Message size="small" severity="secondary" variant="simple">Enter your last name.</Message>
                        <Message v-if="form.errors.last_name" size="small" severity="error" variant="simple">{{ form.errors.last_name }}</Message>
                    </div>

                    <div class="flex flex-col gap-2 w-full">
                        <label for="email">Email</label>
                        <InputText id="email" v-model="form.email" aria-describedby="email-help" type="email" />
                        <Message v-if="form.errors.email" size="small" severity="error" variant="simple">{{ form.errors.email }}</Message>
                    </div>

                    <div class="flex flex-col gap-2 w-full">
                        <label for="password">Password</label>
                        <Password v-model="form.password" toggleMask fluid />
                        <Message v-if="form.errors.password" size="small" severity="error" variant="simple">{{ form.errors.password }}</Message>
                    </div>
                    
                    <div class="flex flex-col gap-2 w-full">
                        <label for="password">Confirm password</label>
                        <Password v-model="form.password_confirmation" toggleMask fluid />
                        <Message v-if="form.errors.password_confirmation" size="small" severity="error" variant="simple">{{ form.errors.password_confirmation }}</Message>
                    </div>
                </div>
            </Fieldset>
            

            <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                Create account
            </Button>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6">Log in</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
