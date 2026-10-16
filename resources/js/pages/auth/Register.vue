<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, ChevronLeft, ChevronRight, User, Building2, UserRound, Megaphone, Check } from 'lucide-vue-next';
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
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n();

const props = defineProps<{
    honeypot: {
        enabled: boolean;
        nameFieldName: string;
        validFromFieldName: string;
        encryptedValidFrom: string;
    };
}>();

type AccountType = 'personal' | 'business';
type ActivityType = 'company' | 'freelancer' | 'agency';

const currentStep = ref(1);

const languages = [
    { label: 'Italiano', value: 'it' },
    { label: 'English', value: 'en' },
    { label: 'Français', value: 'fr' },
    { label: 'Deutsch', value: 'de' },
    { label: 'Español', value: 'es' }
];

// Fuso orario rilevato dal browser (non lo chiediamo più all'utente)
const detectedTimezone = (() => {
    try {
        return Intl.DateTimeFormat().resolvedOptions().timeZone || '';
    } catch {
        return '';
    }
})();

const form = useForm({
    // Profilo: privato o azienda (azienda, professionista, agenzia)
    account_type: 'personal' as AccountType,
    usage_type: 'personal' as string,

    // Account
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',

    // Spazio / Azienda
    slug: '',
    company_name: '',
    company_email: '',
    company_phone: '',
    company_industry: '',
    company_size: '',

    // Preferenze
    referral_source: '',
    main_goal: '',
    estimated_usage: '',
    timezone: detectedTimezone,
    language: languages.some((l) => l.value === locale.value) ? locale.value : 'it',
    terms_accepted: false,
    privacy_policy: false,
    newsletter_opt_in: false,

    // Honeypot fields
    [props.honeypot.nameFieldName]: '',
    [props.honeypot.validFromFieldName]: props.honeypot.encryptedValidFrom,

    errors: {},
});

const isBusiness = computed(() => form.account_type === 'business');
const activity = computed(() => (isBusiness.value ? (form.usage_type as ActivityType) : null));

const accountTypes = computed(() => [
    { value: 'personal' as AccountType, icon: User, title: t('auth.register.profiles.personal.title'), description: t('auth.register.profiles.personal.description') },
    { value: 'business' as AccountType, icon: Building2, title: t('auth.register.profiles.business.title'), description: t('auth.register.profiles.business.description') },
]);

const activityTypes = computed(() => [
    { value: 'company' as ActivityType, icon: Building2, title: t('auth.register.activities.company.title'), description: t('auth.register.activities.company.description') },
    { value: 'freelancer' as ActivityType, icon: UserRound, title: t('auth.register.activities.freelancer.title'), description: t('auth.register.activities.freelancer.description') },
    { value: 'agency' as ActivityType, icon: Megaphone, title: t('auth.register.activities.agency.title'), description: t('auth.register.activities.agency.description') },
]);

const selectAccountType = (type: AccountType) => {
    form.account_type = type;
    form.usage_type = type === 'personal' ? 'personal' : 'company';
};

// Opzioni delle select tradotte nella lingua corrente
const toOptions = (group: string, values: string[]) =>
    computed(() => values.map((value) => ({ label: t(`auth.register.options.${group}.${value}`), value })));

const industries = toOptions('industries', ['technology', 'marketing', 'ecommerce', 'education', 'healthcare', 'finance', 'real_estate', 'food', 'other']);
const companySizes = toOptions('companySizes', ['1-10', '11-50', '51-200', '200+']);
const referralSources = toOptions('referralSources', ['google', 'social', 'word_of_mouth', 'ad', 'blog', 'other']);
const mainGoals = toOptions('mainGoals', ['monitorare_campagne', 'gestire_qr', 'accorciare_link', 'altro']);
const estimatedUsages = toOptions('estimatedUsages', ['low', 'medium', 'high']);

// Privato: 2 step (Account, Il tuo spazio) · Azienda: 3 step (Account, Attività, Preferenze)
const steps = computed(() =>
    isBusiness.value
        ? [
              { value: 1, label: t('auth.register.steps.account') },
              { value: 2, label: t('auth.register.steps.company') },
              { value: 3, label: t('auth.register.steps.preferences') },
          ]
        : [
              { value: 1, label: t('auth.register.steps.account') },
              { value: 2, label: t('auth.register.steps.space') },
          ]
);
const lastStep = computed(() => steps.value.length);

const businessNameLabel = computed(() =>
    activity.value === 'agency'
        ? t('auth.register.businessName.agency')
        : activity.value === 'freelancer'
          ? t('auth.register.businessName.freelancer')
          : t('auth.register.companyName')
);

// Dominio delle pagine pubbliche (es. maglink.it/)
const publicUrlPrefix = computed(() => route('home').replace(/^https?:\/\//, '').replace(/\/$/, '') + '/');

const canProceedStep1 = computed(() => {
    return form.first_name && form.last_name && form.email && form.password && form.password_confirmation;
});

const canProceedStep2 = computed(() => {
    if (!form.slug) return false;
    return isBusiness.value ? !!(form.company_name && form.company_email) : form.terms_accepted;
});

const canSubmit = computed(() => form.terms_accepted && !!form.slug);

const nextStep = () => {
    if (currentStep.value < lastStep.value) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const submit = () => {
    form.transform((data) => {
        const payload: Record<string, unknown> = { ...data };
        if (data.account_type === 'personal') {
            // Il privato non ha un'azienda: la Company viene creata col suo nome lato server
            payload.company_name = '';
            payload.company_email = '';
            payload.company_phone = '';
            payload.company_industry = '';
            payload.company_size = '';
        } else if (data.usage_type === 'freelancer') {
            payload.company_size = 'solo';
        }
        return payload;
    }).post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        onError: (errors) => {
            // Riporta l'utente al primo step con errori
            if (errors.first_name || errors.last_name || errors.email || errors.password) {
                currentStep.value = 1;
            } else if (errors.slug || errors.company_name || errors.company_email) {
                currentStep.value = 2;
            }
        },
    });
};

// Suggerimento automatico dello slug finché l'utente non lo modifica a mano
const slugTouched = ref(false);
const slugify = (value: string) =>
    value
        .normalize('NFD')
        .replace(/[̀-ͯ]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '')
        .slice(0, 50);

watch(
    () => (isBusiness.value ? form.company_name : `${form.first_name} ${form.last_name}`),
    (source) => {
        if (!slugTouched.value) {
            form.slug = slugify(source);
            lastCheckedSlug.value = '';
        }
    }
);

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
        form.errors.slug = t('auth.register.slugTooShort');
        return;
    } else if (!/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(form.slug)) {
        form.errors.slug = t('auth.register.slugInvalid');
        return;
    }

    // Evita chiamate duplicate per lo stesso slug
    if (lastCheckedSlug.value === form.slug) return;
    lastCheckedSlug.value = form.slug;

    slugChecking.value = true;
    try {
        const res = await fetch(`/api/check-slug?slug=${encodeURIComponent(form.slug)}`, { credentials: 'include' });
        const data = await res.json();
        if (data.exists) {
            form.errors.slug = t('auth.register.slugTaken');
        }
    } catch (e) {
        console.log(e)
        form.errors.slug = t('auth.register.slugCheckError');
    } finally {
        slugChecking.value = false;
    }
}, 500);

const onSlugInput = () => {
    slugTouched.value = true;
    checkSlug();
};

// Verifica lo slug suggerito quando si arriva allo step 2
watch(currentStep, (step) => {
    if (step === 2 && form.slug) checkSlug();
});
</script>

<template>
    <AuthBase :title="t('auth.register.title')" :description="t('auth.register.description')">
        <Head :title="t('auth.register.headTitle')" />

        <div class="w-full">
            <!-- Honeypot fields (hidden from users, visible to bots) -->
            <template v-if="honeypot.enabled">
                <div :id="`${honeypot.nameFieldName}_wrap`" style="display: none" aria-hidden="true">
                    <input
                        :name="honeypot.nameFieldName"
                        v-model="form[honeypot.nameFieldName]"
                        type="text"
                        autocomplete="nope"
                        tabindex="-1"
                    />
                    <input
                        :name="honeypot.validFromFieldName"
                        v-model="form[honeypot.validFromFieldName]"
                        type="text"
                        autocomplete="off"
                        tabindex="-1"
                    />
                </div>
            </template>

            <Stepper v-model:value="currentStep" class="mb-6 sm:mb-8">
                <StepList class="mb-4 gap-1 sm:gap-2">
                    <Step v-for="step in steps" :key="step.value" :value="step.value" class="!p-1 sm:!p-2">
                        <div class="flex min-w-0 items-center gap-1.5 sm:gap-2">
                            <span
                                class="flex h-7 w-7 sm:h-8 sm:w-8 shrink-0 items-center justify-center rounded-full border-2 text-xs sm:text-sm font-semibold transition-colors"
                                :class="currentStep >= step.value
                                    ? 'border-orange-600 bg-orange-600 text-white'
                                    : 'border-gray-300 dark:border-gray-700 text-gray-500 dark:text-gray-400'"
                            >{{ step.value }}</span>
                            <span
                                class="truncate text-xs sm:text-sm font-medium"
                                :class="currentStep === step.value ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                            >{{ step.label }}</span>
                        </div>
                    </Step>
                </StepList>

                <StepPanels>
                    <!-- STEP 1: profilo + account -->
                    <StepPanel :value="1" class="!bg-transparent !p-0">
                        <div class="flex flex-col gap-6">
                            <div>
                                <p class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('auth.register.profileQuestion') }}</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" role="radiogroup">
                                    <button
                                        v-for="type in accountTypes"
                                        :key="type.value"
                                        type="button"
                                        role="radio"
                                        :aria-checked="form.account_type === type.value"
                                        class="relative flex items-start gap-3 rounded-xl border-2 p-4 text-left transition-colors"
                                        :class="form.account_type === type.value
                                            ? 'border-orange-600 bg-orange-50 dark:bg-orange-950/30'
                                            : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'"
                                        @click="selectAccountType(type.value)"
                                    >
                                        <component :is="type.icon" class="mt-0.5 h-5 w-5 shrink-0" :class="form.account_type === type.value ? 'text-orange-600' : 'text-gray-400'" />
                                        <span class="min-w-0">
                                            <span class="block font-semibold text-gray-900 dark:text-white">{{ type.title }}</span>
                                            <span class="block text-xs text-gray-500 dark:text-gray-400">{{ type.description }}</span>
                                        </span>
                                        <Check v-if="form.account_type === type.value" class="absolute right-3 top-3 h-4 w-4 text-orange-600" />
                                    </button>
                                </div>
                            </div>

                            <Fieldset :legend="t('auth.register.accountInfo')">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col gap-2">
                                        <label for="first_name" class="text-gray-700 dark:text-gray-300">{{ t('auth.register.firstName') }}</label>
                                        <InputText id="first_name" v-model="form.first_name" autocomplete="given-name" class="dark:bg-gray-900 dark:border-gray-700 dark:text-white" />
                                        <Message v-if="form.errors.first_name" size="small" severity="error" variant="simple">{{ form.errors.first_name }}</Message>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <label for="last_name" class="text-gray-700 dark:text-gray-300">{{ t('auth.register.lastName') }}</label>
                                        <InputText id="last_name" v-model="form.last_name" autocomplete="family-name" class="dark:bg-gray-900 dark:border-gray-700 dark:text-white" />
                                        <Message v-if="form.errors.last_name" size="small" severity="error" variant="simple">{{ form.errors.last_name }}</Message>
                                    </div>

                                    <div class="flex flex-col gap-2 md:col-span-2">
                                        <label for="email" class="text-gray-700 dark:text-gray-300">{{ t('auth.register.email') }}</label>
                                        <InputText id="email" v-model="form.email" type="email" autocomplete="email" class="dark:bg-gray-900 dark:border-gray-700 dark:text-white" />
                                        <Message v-if="form.errors.email" size="small" severity="error" variant="simple">{{ form.errors.email }}</Message>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <label for="password" class="text-gray-700 dark:text-gray-300">{{ t('auth.register.password') }}</label>
                                        <Password v-model="form.password" toggleMask fluid />
                                        <Message v-if="form.errors.password" size="small" severity="error" variant="simple">{{ form.errors.password }}</Message>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <label for="password_confirmation" class="text-gray-700 dark:text-gray-300">{{ t('auth.register.confirmPassword') }}</label>
                                        <Password v-model="form.password_confirmation" :feedback="false" toggleMask fluid />
                                        <Message v-if="form.errors.password_confirmation" size="small" severity="error" variant="simple">{{ form.errors.password_confirmation }}</Message>
                                    </div>
                                </div>
                            </Fieldset>

                            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                                <Button class="w-full sm:w-auto" @click="nextStep" :disabled="!canProceedStep1">
                                    {{ t('auth.register.next') }} <ChevronRight class="w-4 h-4 ml-2" />
                                </Button>
                            </div>
                        </div>
                    </StepPanel>

                    <!-- STEP 2: spazio (privato, ultimo step) oppure attività (azienda) -->
                    <StepPanel :value="2" class="!bg-transparent !p-0">
                        <div class="flex flex-col gap-6">
                            <!-- Tipo di attività (solo azienda) -->
                            <div v-if="isBusiness">
                                <p class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('auth.register.activityType') }}</p>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3" role="radiogroup">
                                    <button
                                        v-for="type in activityTypes"
                                        :key="type.value"
                                        type="button"
                                        role="radio"
                                        :aria-checked="form.usage_type === type.value"
                                        class="relative flex sm:flex-col items-start gap-3 sm:gap-2 rounded-xl border-2 p-3 text-left transition-colors"
                                        :class="form.usage_type === type.value
                                            ? 'border-orange-600 bg-orange-50 dark:bg-orange-950/30'
                                            : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'"
                                        @click="form.usage_type = type.value"
                                    >
                                        <component :is="type.icon" class="mt-0.5 h-5 w-5 shrink-0" :class="form.usage_type === type.value ? 'text-orange-600' : 'text-gray-400'" />
                                        <span class="min-w-0">
                                            <span class="block text-sm font-semibold text-gray-900 dark:text-white">{{ type.title }}</span>
                                            <span class="block text-xs text-gray-500 dark:text-gray-400">{{ type.description }}</span>
                                        </span>
                                        <Check v-if="form.usage_type === type.value" class="absolute right-3 top-3 h-4 w-4 text-orange-600" />
                                    </button>
                                </div>
                            </div>

                            <Fieldset :legend="isBusiness ? t('auth.register.companyInfo') : t('auth.register.spaceInfo')">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div v-if="isBusiness" class="flex flex-col gap-2 md:col-span-2">
                                        <label for="company_name" class="text-gray-700 dark:text-gray-300">{{ businessNameLabel }}</label>
                                        <InputText id="company_name" v-model="form.company_name" autocomplete="organization" class="dark:bg-gray-900 dark:border-gray-700 dark:text-white" />
                                        <Message v-if="form.errors.company_name" size="small" severity="error" variant="simple">{{ form.errors.company_name }}</Message>
                                    </div>

                                    <div class="flex flex-col gap-2 md:col-span-2">
                                        <label for="slug" class="text-gray-700 dark:text-gray-300">{{ t('auth.register.publicUrl') }}</label>
                                        <div class="flex items-stretch w-full min-w-0">
                                            <span class="flex shrink-0 items-center justify-center px-2 sm:px-3 border-y border-s border-surface-300 dark:border-surface-700 bg-surface-0 dark:bg-surface-950 text-surface-400 dark:text-surface-500 rounded-s-md text-sm">{{ publicUrlPrefix }}</span>
                                            <InputText id="slug" v-model="form.slug" pt:root="min-w-0 flex-1 rounded-s-none rounded-e-md dark:bg-gray-900 dark:border-gray-700 dark:text-white" :placeholder="isBusiness ? 'nomeazienda' : 'nome-cognome'" @input="onSlugInput" />
                                        </div>
                                        <Message v-if="form.errors.slug" size="small" severity="error" variant="simple">{{ form.errors.slug }}</Message>
                                        <p v-else class="text-xs text-gray-500 dark:text-gray-400">{{ t('auth.register.publicUrlHint') }}</p>
                                    </div>

                                    <template v-if="isBusiness">
                                        <div class="flex flex-col gap-2">
                                            <label for="company_email" class="text-gray-700 dark:text-gray-300">{{ t('auth.register.companyEmail') }}</label>
                                            <InputText id="company_email" v-model="form.company_email" type="email" class="dark:bg-gray-900 dark:border-gray-700 dark:text-white" />
                                            <Message v-if="form.errors.company_email" size="small" severity="error" variant="simple">{{ form.errors.company_email }}</Message>
                                        </div>

                                        <div class="flex flex-col gap-2">
                                            <label for="company_phone" class="text-gray-700 dark:text-gray-300">{{ t('auth.register.companyPhone') }}</label>
                                            <InputText id="company_phone" v-model="form.company_phone" type="tel" autocomplete="tel" class="dark:bg-gray-900 dark:border-gray-700 dark:text-white" />
                                            <Message v-if="form.errors.company_phone" size="small" severity="error" variant="simple">{{ form.errors.company_phone }}</Message>
                                        </div>

                                        <div class="flex flex-col gap-2" :class="{ 'md:col-span-2': activity === 'freelancer' }">
                                            <label for="company_industry" class="text-gray-700 dark:text-gray-300">{{ t('auth.register.industry') }}</label>
                                            <Select v-model="form.company_industry" :options="industries" option-label="label" option-value="value" :placeholder="t('auth.register.selectIndustry')" />
                                            <Message v-if="form.errors.company_industry" size="small" severity="error" variant="simple">{{ form.errors.company_industry }}</Message>
                                        </div>

                                        <div v-if="activity !== 'freelancer'" class="flex flex-col gap-2">
                                            <label for="company_size" class="text-gray-700 dark:text-gray-300">{{ t('auth.register.companySize') }}</label>
                                            <Select v-model="form.company_size" :options="companySizes" option-label="label" option-value="value" :placeholder="t('auth.register.selectSize')" />
                                            <Message v-if="form.errors.company_size" size="small" severity="error" variant="simple">{{ form.errors.company_size }}</Message>
                                        </div>
                                    </template>

                                    <!-- Privato: preferenze essenziali direttamente qui -->
                                    <template v-else>
                                        <div class="flex flex-col gap-2">
                                            <label for="main_goal" class="text-gray-700 dark:text-gray-300">{{ t('auth.register.mainGoal') }}</label>
                                            <Select v-model="form.main_goal" :options="mainGoals" option-label="label" option-value="value" :placeholder="t('auth.register.selectMainGoal')" />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label for="referral_source" class="text-gray-700 dark:text-gray-300">{{ t('auth.register.referralSource') }}</label>
                                            <Select v-model="form.referral_source" :options="referralSources" option-label="label" option-value="value" :placeholder="t('auth.register.selectReferralSource')" />
                                        </div>
                                    </template>
                                </div>
                            </Fieldset>

                            <!-- Consensi: ultimo step per il privato -->
                            <Fieldset v-if="!isBusiness" :legend="t('auth.register.consentsTitle')">
                                <div class="space-y-4">
                                    <div class="flex items-start gap-3">
                                        <Checkbox v-model="form.terms_accepted" binary input-id="terms_personal" />
                                        <label for="terms_personal" class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ t('auth.register.acceptTermsPrefix') }} <a :href="route('terms-of-service')" target="_blank" rel="noopener" class="underline text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">{{ t('auth.register.termsOfService') }}</a>
                                        </label>
                                    </div>
                                    <Message v-if="form.errors.terms_accepted" size="small" severity="error" variant="simple">{{ form.errors.terms_accepted }}</Message>
                                    <div class="flex items-start gap-3">
                                        <Checkbox v-model="form.privacy_policy" binary input-id="privacy_personal" />
                                        <label for="privacy_personal" class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ t('auth.register.acceptPrivacyPrefix') }} <a :href="route('privacy-policy')" target="_blank" rel="noopener" class="underline text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">{{ t('auth.register.privacyPolicy') }}</a>
                                        </label>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <Checkbox v-model="form.newsletter_opt_in" binary input-id="newsletter_personal" />
                                        <label for="newsletter_personal" class="text-sm text-gray-700 dark:text-gray-300">{{ t('auth.register.newsletter') }}</label>
                                    </div>
                                </div>
                            </Fieldset>

                            <div class="flex flex-col-reverse sm:flex-row sm:justify-between gap-3">
                                <Button class="w-full sm:w-auto" variant="outline" @click="prevStep">
                                    <ChevronLeft class="w-4 h-4 mr-2" /> {{ t('auth.register.back') }}
                                </Button>
                                <Button v-if="isBusiness" class="w-full sm:w-auto" @click="nextStep" :disabled="!canProceedStep2">
                                    {{ t('auth.register.next') }} <ChevronRight class="w-4 h-4 ml-2" />
                                </Button>
                                <Button v-else class="w-full sm:w-auto" @click="submit" :disabled="!canProceedStep2 || form.processing">
                                    <LoaderCircle v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" />
                                    {{ t('auth.register.createAccount') }}
                                </Button>
                            </div>
                        </div>
                    </StepPanel>

                    <!-- STEP 3: preferenze e consensi (solo azienda) -->
                    <StepPanel v-if="isBusiness" :value="3" class="!bg-transparent !p-0">
                        <div class="flex flex-col gap-6">
                            <Fieldset :legend="t('auth.register.preferencesTitle')">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col gap-2">
                                        <label for="main_goal" class="text-gray-700 dark:text-gray-300">{{ t('auth.register.mainGoal') }}</label>
                                        <Select v-model="form.main_goal" :options="mainGoals" option-label="label" option-value="value" :placeholder="t('auth.register.selectMainGoal')" />
                                        <Message v-if="form.errors.main_goal" size="small" severity="error" variant="simple">{{ form.errors.main_goal }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label for="estimated_usage" class="text-gray-700 dark:text-gray-300">{{ t('auth.register.estimatedUsage') }}</label>
                                        <Select v-model="form.estimated_usage" :options="estimatedUsages" option-label="label" option-value="value" :placeholder="t('auth.register.selectEstimatedUsage')" />
                                        <Message v-if="form.errors.estimated_usage" size="small" severity="error" variant="simple">{{ form.errors.estimated_usage }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label for="referral_source" class="text-gray-700 dark:text-gray-300">{{ t('auth.register.referralSource') }}</label>
                                        <Select v-model="form.referral_source" :options="referralSources" option-label="label" option-value="value" :placeholder="t('auth.register.selectReferralSource')" />
                                        <Message v-if="form.errors.referral_source" size="small" severity="error" variant="simple">{{ form.errors.referral_source }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label for="language" class="text-gray-700 dark:text-gray-300">{{ t('auth.register.language') }}</label>
                                        <Select v-model="form.language" :options="languages" option-label="label" option-value="value" :placeholder="t('auth.register.selectLanguage')" />
                                        <Message v-if="form.errors.language" size="small" severity="error" variant="simple">{{ form.errors.language }}</Message>
                                    </div>
                                </div>

                                <div class="mt-6 space-y-4">
                                    <div class="flex items-start gap-3">
                                        <Checkbox v-model="form.terms_accepted" binary input-id="terms_business" />
                                        <label for="terms_business" class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ t('auth.register.acceptTermsPrefix') }} <a :href="route('terms-of-service')" target="_blank" rel="noopener" class="underline text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">{{ t('auth.register.termsOfService') }}</a>
                                        </label>
                                    </div>
                                    <Message v-if="form.errors.terms_accepted" size="small" severity="error" variant="simple">{{ form.errors.terms_accepted }}</Message>
                                    <div class="flex items-start gap-3">
                                        <Checkbox v-model="form.privacy_policy" binary input-id="privacy_business" />
                                        <label for="privacy_business" class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ t('auth.register.acceptPrivacyPrefix') }} <a :href="route('privacy-policy')" target="_blank" rel="noopener" class="underline text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">{{ t('auth.register.privacyPolicy') }}</a>
                                        </label>
                                    </div>
                                    <Message v-if="form.errors.privacy_policy" size="small" severity="error" variant="simple">{{ form.errors.privacy_policy }}</Message>
                                    <div class="flex items-start gap-3">
                                        <Checkbox v-model="form.newsletter_opt_in" binary input-id="newsletter_business" />
                                        <label for="newsletter_business" class="text-sm text-gray-700 dark:text-gray-300">{{ t('auth.register.newsletter') }}</label>
                                    </div>
                                </div>
                            </Fieldset>

                            <div class="flex flex-col-reverse sm:flex-row sm:justify-between gap-3">
                                <Button class="w-full sm:w-auto" variant="outline" @click="prevStep">
                                    <ChevronLeft class="w-4 h-4 mr-2" /> {{ t('auth.register.back') }}
                                </Button>
                                <Button class="w-full sm:w-auto" @click="submit" :disabled="!canSubmit || form.processing">
                                    <LoaderCircle v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" />
                                    {{ t('auth.register.createAccount') }}
                                </Button>
                            </div>
                        </div>
                    </StepPanel>
                </StepPanels>
            </Stepper>

            <div class="text-center text-sm text-gray-600 dark:text-gray-400 mt-6">
                {{ t('auth.register.alreadyHaveAccount') }}
                <TextLink :href="route('login')" class="underline underline-offset-4 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">{{ t('auth.register.logIn') }}</TextLink>
            </div>
        </div>
    </AuthBase>
</template>
