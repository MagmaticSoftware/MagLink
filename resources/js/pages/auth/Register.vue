<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import Fieldset from '@/components/volt//Fieldset.vue';
import InputText from '@/components/volt//InputText.vue';
import Message from '@/components/volt//Message.vue';
import Password from '@/components/volt//Password.vue';
import Stepper from '@/components/volt//Stepper.vue';
import StepList from '@/components/volt//StepList.vue';
import Step from '@/components/volt//Step.vue';
import StepPanels from '@/components/volt//StepPanels.vue';
import StepPanel from '@/components/volt//StepPanel.vue';
import Select from '@/components/volt//Select.vue';
import Checkbox from '@/components/volt//Checkbox.vue';
import { ref, computed } from 'vue';

const currentStep = ref(1);

const form = useForm({
    // Step 1: Dati Utente
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',

    // Step 2: Dati Azienda
    slug: '',
    company_name: '',
    company_email: '',
    company_phone: '',
    company_industry: '',
    company_size: '',

    // Step 3: Configurazione & Preferenze (tutti i campi da migration)
    usage_type: '',
    referral_source: '', // alias discovery_source
    main_goal: '',
    estimated_usage: '',
    team_size: '',
    timezone: '',
    language: 'it',
    terms_accepted: false, // alias terms_and_conditions
    privacy_policy: false,
    newsletter_opt_in: false, // alias newsletter

    errors: {},
});

const industries = [
    { label: 'Technology', value: 'technology' },
    { label: 'Marketing & Advertising', value: 'marketing' },
    { label: 'E-commerce', value: 'ecommerce' },
    { label: 'Education', value: 'education' },
    { label: 'Healthcare', value: 'healthcare' },
    { label: 'Finance', value: 'finance' },
    { label: 'Real Estate', value: 'real_estate' },
    { label: 'Food & Beverage', value: 'food' },
    { label: 'Other', value: 'other' }
];

const companySizes = [
    { label: '1-10 employees', value: '1-10' },
    { label: '11-50 employees', value: '11-50' },
    { label: '51-200 employees', value: '51-200' },
    { label: '200+ employees', value: '200+' }
];

const usageTypes = [
    { label: 'Personale', value: 'personal' },
    { label: 'Azienda', value: 'company' },
    { label: 'Agenzia', value: 'agency' },
    { label: 'Altro', value: 'other' }
];

const referralSources = [
    { label: 'Google', value: 'google' },
    { label: 'Social', value: 'social' },
    { label: 'Passaparola', value: 'word_of_mouth' },
    { label: 'Pubblicità', value: 'ad' },
    { label: 'Blog/Articolo', value: 'blog' },
    { label: 'Altro', value: 'other' }
];

const mainGoals = [
    { label: 'Monitorare campagne', value: 'monitorare_campagne' },
    { label: 'Gestire QR', value: 'gestire_qr' },
    { label: 'Accorciare link', value: 'accorciare_link' },
    { label: 'Altro', value: 'altro' }
];

const estimatedUsages = [
    { label: 'Basso', value: 'low' },
    { label: 'Medio', value: 'medium' },
    { label: 'Alto', value: 'high' }
];

const teamSizes = [
    { label: 'Solo', value: 'solo' },
    { label: '1-5', value: '1-5' },
    { label: '6-10', value: '6-10' },
    { label: '11-20', value: '11-20' },
    { label: '21+', value: '21+' }
];

const languages = [
    { label: 'Italiano', value: 'it' },
    { label: 'English', value: 'en' },
    { label: 'Français', value: 'fr' },
    { label: 'Deutsch', value: 'de' },
    { label: 'Español', value: 'es' }
];

const timezones = [
    { label: 'Europe/Rome', value: 'Europe/Rome' },
    { label: 'America/New_York', value: 'America/New_York' },
    { label: 'Europe/London', value: 'Europe/London' },
    { label: 'Asia/Tokyo', value: 'Asia/Tokyo' },
    { label: 'Altro', value: '' }
];

const canProceedStep1 = computed(() => {
    return form.first_name && form.last_name && form.email && form.password && form.password_confirmation;
});

const canProceedStep2 = computed(() => {
    return form.slug && form.company_name && form.company_email;
});

const canProceedStep3 = computed(() => {
    return form.terms_accepted;
});

const nextStep = () => {
    if (currentStep.value < 3) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 0) {
        currentStep.value--;
    }
};

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
    <AuthBase title="Create an account" description="Complete the steps below to create your account">
        <Head title="Register" />

        <div class="container mx-auto">
            <Stepper v-model:value="currentStep" class="mb-8">
                <StepList>
                    <Step :value="1">Account</Step>
                    <Step :value="2">Company</Step>
                    <Step :value="3">Preferences</Step>
                </StepList>

                <StepPanels>
                    <StepPanel :value="1" class="!bg-transparent">
                        <div class="flex flex-col gap-6">
                            <Fieldset legend="Account Information">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col gap-2">
                                        <label for="first_name" class="text-gray-700 dark:text-gray-300">First Name</label>
                                        <InputText id="first_name" v-model="form.first_name" class="dark:bg-gray-900 dark:border-gray-700 dark:text-white" />
                                        <Message v-if="form.errors.first_name" size="small" severity="error" variant="simple">{{ form.errors.first_name }}</Message>
                                    </div>
                                    
                                    <div class="flex flex-col gap-2">
                                        <label for="last_name" class="text-gray-700 dark:text-gray-300">Last Name</label>
                                        <InputText id="last_name" v-model="form.last_name" class="dark:bg-gray-900 dark:border-gray-700 dark:text-white" />
                                        <Message v-if="form.errors.last_name" size="small" severity="error" variant="simple">{{ form.errors.last_name }}</Message>
                                    </div>
                                    
                                    <div class="flex flex-col gap-2 md:col-span-2">
                                        <label for="email" class="text-gray-700 dark:text-gray-300">Email</label>
                                        <InputText id="email" v-model="form.email" type="email" class="dark:bg-gray-900 dark:border-gray-700 dark:text-white" />
                                        <Message v-if="form.errors.email" size="small" severity="error" variant="simple">{{ form.errors.email }}</Message>
                                    </div>
                                    
                                    <div class="flex flex-col gap-2">
                                        <label for="password" class="text-gray-700 dark:text-gray-300">Password</label>
                                        <Password v-model="form.password" toggleMask fluid />
                                        <Message v-if="form.errors.password" size="small" severity="error" variant="simple">{{ form.errors.password }}</Message>
                                    </div>
                                    
                                    <div class="flex flex-col gap-2">
                                        <label for="password_confirmation" class="text-gray-700 dark:text-gray-300">Confirm Password</label>
                                        <Password v-model="form.password_confirmation" toggleMask fluid />
                                        <Message v-if="form.errors.password_confirmation" size="small" severity="error" variant="simple">{{ form.errors.password_confirmation }}</Message>
                                    </div>
                                </div>
                            </Fieldset>
                            
                            <div class="flex justify-end">
                                <Button @click="nextStep" :disabled="!canProceedStep1">
                                    Next <ChevronRight class="w-4 h-4 ml-2" />
                                </Button>
                            </div>
                        </div>
                    </StepPanel>
                    
                    <StepPanel :value="2" class="!bg-transparent">
                        <div class="flex flex-col gap-6">
                            <Fieldset legend="Company Information">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col gap-2 md:col-span-2">
                                        <label for="slug" class="text-gray-700 dark:text-gray-300">Public URL</label>
                                        <div class="flex items-stretch w-full">
                                            <span class="flex items-center justify-center px-3 border-y border-s border-surface-300 dark:border-surface-700 bg-surface-0 dark:bg-surface-950 text-surface-400 dark:text-surface-500 rounded-s-md text-sm">maglink.com/</span>
                                            <InputText id="slug" v-model="form.slug" pt:root="flex-1 rounded-s-none rounded-e-md dark:bg-gray-900 dark:border-gray-700 dark:text-white" placeholder="yourcompany" @keyup="checkSlug" />
                                        </div>
                                        <Message v-if="form.errors.slug" size="small" severity="error" variant="simple">{{ form.errors.slug }}</Message>
                                    </div>
                                    
                                    <div class="flex flex-col gap-2 md:col-span-2">
                                        <label for="company_name" class="text-gray-700 dark:text-gray-300">Company Name</label>
                                        <InputText id="company_name" v-model="form.company_name" class="dark:bg-gray-900 dark:border-gray-700 dark:text-white" />
                                        <Message v-if="form.errors.company_name" size="small" severity="error" variant="simple">{{ form.errors.company_name }}</Message>
                                    </div>
                                    
                                    <div class="flex flex-col gap-2">
                                        <label for="company_email" class="text-gray-700 dark:text-gray-300">Company Email</label>
                                        <InputText id="company_email" v-model="form.company_email" type="email" class="dark:bg-gray-900 dark:border-gray-700 dark:text-white" />
                                        <Message v-if="form.errors.company_email" size="small" severity="error" variant="simple">{{ form.errors.company_email }}</Message>
                                    </div>
                                    
                                    <div class="flex flex-col gap-2">
                                        <label for="company_phone" class="text-gray-700 dark:text-gray-300">Company Phone</label>
                                        <InputText id="company_phone" v-model="form.company_phone" class="dark:bg-gray-900 dark:border-gray-700 dark:text-white" />
                                        <Message v-if="form.errors.company_phone" size="small" severity="error" variant="simple">{{ form.errors.company_phone }}</Message>
                                    </div>
                                    
                                    <div class="flex flex-col gap-2">
                                        <label for="company_industry" class="text-gray-700 dark:text-gray-300">Industry</label>
                                        <Select v-model="form.company_industry" :options="industries" option-label="label" option-value="value" placeholder="Select industry" />
                                        <Message v-if="form.errors.company_industry" size="small" severity="error" variant="simple">{{ form.errors.company_industry }}</Message>
                                    </div>
                                    
                                    <div class="flex flex-col gap-2">
                                        <label for="company_size" class="text-gray-700 dark:text-gray-300">Company Size</label>
                                        <Select v-model="form.company_size" :options="companySizes" option-label="label" option-value="value" placeholder="Select size" />
                                        <Message v-if="form.errors.company_size" size="small" severity="error" variant="simple">{{ form.errors.company_size }}</Message>
                                    </div>
                                </div>
                            </Fieldset>
                            
                            <div class="flex justify-between">
                                <Button variant="outline" @click="prevStep">
                                    <ChevronLeft class="w-4 h-4 mr-2" /> Back
                                </Button>
                                <Button @click="nextStep" :disabled="!canProceedStep2">
                                    Next <ChevronRight class="w-4 h-4 ml-2" />
                                </Button>
                            </div>
                        </div>
                    </StepPanel>
                    
                    <StepPanel :value="3" class="!bg-transparent">
                        <div class="flex flex-col gap-6">
                            <Fieldset legend="Preferences & Configuration">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col gap-2">
                                        <label for="usage_type" class="text-gray-700 dark:text-gray-300">Tipo di utilizzo</label>
                                        <Select v-model="form.usage_type" :options="usageTypes" option-label="label" option-value="value" placeholder="Seleziona tipo" />
                                        <Message v-if="form.errors.usage_type" size="small" severity="error" variant="simple">{{ form.errors.usage_type }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label for="referral_source" class="text-gray-700 dark:text-gray-300">Come ci hai scoperto?</label>
                                        <Select v-model="form.referral_source" :options="referralSources" option-label="label" option-value="value" placeholder="Seleziona fonte" />
                                        <Message v-if="form.errors.referral_source" size="small" severity="error" variant="simple">{{ form.errors.referral_source }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label for="main_goal" class="text-gray-700 dark:text-gray-300">Obiettivo principale</label>
                                        <Select v-model="form.main_goal" :options="mainGoals" option-label="label" option-value="value" placeholder="Seleziona obiettivo" />
                                        <Message v-if="form.errors.main_goal" size="small" severity="error" variant="simple">{{ form.errors.main_goal }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label for="estimated_usage" class="text-gray-700 dark:text-gray-300">Utilizzo stimato</label>
                                        <Select v-model="form.estimated_usage" :options="estimatedUsages" option-label="label" option-value="value" placeholder="Seleziona utilizzo" />
                                        <Message v-if="form.errors.estimated_usage" size="small" severity="error" variant="simple">{{ form.errors.estimated_usage }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label for="team_size" class="text-gray-700 dark:text-gray-300">Dimensione team</label>
                                        <Select v-model="form.team_size" :options="teamSizes" option-label="label" option-value="value" placeholder="Seleziona dimensione" />
                                        <Message v-if="form.errors.team_size" size="small" severity="error" variant="simple">{{ form.errors.team_size }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label for="timezone" class="text-gray-700 dark:text-gray-300">Fuso orario</label>
                                        <Select v-model="form.timezone" :options="timezones" option-label="label" option-value="value" placeholder="Seleziona fuso orario" />
                                        <Message v-if="form.errors.timezone" size="small" severity="error" variant="simple">{{ form.errors.timezone }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label for="language" class="text-gray-700 dark:text-gray-300">Lingua</label>
                                        <Select v-model="form.language" :options="languages" option-label="label" option-value="value" placeholder="Seleziona lingua" />
                                        <Message v-if="form.errors.language" size="small" severity="error" variant="simple">{{ form.errors.language }}</Message>
                                    </div>
                                </div>

                                <div class="mt-6 space-y-4">
                                    <div class="flex items-center gap-3">
                                        <Checkbox v-model="form.terms_accepted" binary />
                                        <label class="text-sm text-gray-700 dark:text-gray-300">
                                            Accetto i <TextLink href="#" class="underline text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">Termini di servizio</TextLink>
                                        </label>
                                    </div>
                                    <Message v-if="form.errors.terms_accepted" size="small" severity="error" variant="simple">{{ form.errors.terms_accepted }}</Message>
                                    <div class="flex items-center gap-3">
                                        <Checkbox v-model="form.privacy_policy" binary />
                                        <label class="text-sm text-gray-700 dark:text-gray-300">
                                            Accetto la <TextLink href="#" class="underline text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">Privacy Policy</TextLink>
                                        </label>
                                    </div>
                                    <Message v-if="form.errors.privacy_policy" size="small" severity="error" variant="simple">{{ form.errors.privacy_policy }}</Message>
                                    <div class="flex items-center gap-3">
                                        <Checkbox v-model="form.newsletter_opt_in" binary />
                                        <label class="text-sm text-gray-700 dark:text-gray-300">Iscrivimi alla newsletter</label>
                                    </div>
                                </div>
                            </Fieldset>
                            
                            <div class="flex justify-between">
                                <Button variant="outline" @click="prevStep">
                                    <ChevronLeft class="w-4 h-4 mr-2" /> Back
                                </Button>
                                <Button @click="submit" :disabled="!canProceedStep3 || form.processing">
                                    <LoaderCircle v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" />
                                    Create Account
                                </Button>
                            </div>
                        </div>
                    </StepPanel>
                </StepPanels>
            </Stepper>

            <div class="text-center text-sm text-gray-600 dark:text-gray-400 mt-6">
                Already have an account?
                <TextLink :href="route('login')" class="underline underline-offset-4 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Log in</TextLink>
            </div>
        </div>
    </AuthBase>
</template>
