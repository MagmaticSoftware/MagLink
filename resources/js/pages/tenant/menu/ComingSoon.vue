<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import {
    BarChart3,
    BellRing,
    CalendarClock,
    ChefHat,
    Languages,
    Leaf,
    Palette,
    QrCode,
    RefreshCcw,
    Smartphone,
    Sparkles,
    UtensilsCrossed,
    Wheat,
    Wine,
} from 'lucide-vue-next';
import { useRoute } from '@/composables/useRoute';
import heroWaves from '../../../../images/dashboard/hero-waves.webp';

const route = useRoute();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Menù', href: route('menu.index') }];

const features = [
    { icon: QrCode, title: 'QR Code al tavolo', text: 'Un QR per locale, sala o tavolo: il cliente inquadra e il menù si apre subito, senza app.', tile: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' },
    { icon: RefreshCcw, title: 'Aggiornamenti in tempo reale', text: 'Prezzi, piatti esauriti e novità si cambiano in un attimo, senza ristampare nulla.', tile: 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' },
    { icon: Languages, title: 'Più lingue', text: 'Un menù pensato anche per i clienti stranieri, nella loro lingua.', tile: 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400' },
    { icon: Wheat, title: 'Allergeni e diete', text: 'Allergeni ben visibili e indicazioni per vegetariani, vegani e senza glutine.', tile: 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400' },
    { icon: Palette, title: 'Il tuo stile', text: 'Logo, colori e foto del locale per un menù che ti somiglia.', tile: 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400' },
    { icon: BarChart3, title: 'Statistiche', text: 'Scopri quando viene consultato il menù e quali sezioni interessano di più.', tile: 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400' },
];

const steps = [
    { icon: ChefHat, title: 'Componi il menù', text: 'Categorie, piatti, descrizioni e prezzi.' },
    { icon: QrCode, title: 'Stampa il QR', text: 'Da mettere su tavoli, vetrina o volantini.' },
    { icon: Smartphone, title: 'I clienti ordinano', text: 'Consultano il menù dal proprio telefono.' },
];

// Piatti di esempio per l'anteprima del telefono
const sampleDishes = [
    { name: 'Tagliatelle al ragù', price: '12', tag: 'Della casa' },
    { name: 'Risotto ai funghi', price: '14', tag: 'Vegetariano' },
    { name: 'Tagliata di manzo', price: '19', tag: null },
];
</script>

<template>
    <Head title="Menù digitale" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex w-full flex-col gap-5 lg:p-2">
            <!-- Hero -->
            <section class="relative isolate overflow-hidden rounded-2xl border border-surface-200/80 bg-white dark:border-surface-700/70 dark:bg-surface-800">
                <img :src="heroWaves" alt="" aria-hidden="true" class="absolute inset-0 -z-10 h-full w-full object-cover object-right-bottom dark:opacity-40" />
                <div class="absolute inset-0 -z-10 bg-gradient-to-r from-white via-white/85 to-white/10 dark:from-surface-900 dark:via-surface-900/85 dark:to-surface-900/20"></div>

                <div class="grid items-center gap-10 p-6 sm:p-10 lg:grid-cols-2 lg:p-14">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-primary-500 to-rose-500 px-3 py-1 text-xs font-semibold text-white shadow-sm">
                            <Sparkles :size="13" /> In arrivo
                        </span>
                        <h1 class="mt-4 text-3xl font-semibold tracking-tight text-surface-900 sm:text-4xl lg:text-5xl dark:text-white">
                            Il menù del tuo locale, <span class="text-primary-500">a portata di QR.</span>
                        </h1>
                        <p class="mt-4 max-w-xl text-base text-surface-600 sm:text-lg dark:text-surface-300">
                            Stiamo preparando un'area dedicata alla ristorazione: menù digitali sempre aggiornati, belli da sfogliare e semplici da gestire, integrati con i QR Code e le statistiche di MagLink.
                        </p>
                        <div class="mt-7 flex flex-wrap gap-3">
                            <Link :href="route('qrcodes.create')" class="ml-btn-primary px-5 py-2.5">
                                <QrCode :size="16" /> Intanto crea un QR Code
                            </Link>
                            <Link :href="route('tenant.index')" class="ml-btn-secondary px-5 py-2.5">Torna alla dashboard</Link>
                        </div>
                    </div>

                    <!-- Illustrazione: telefono con anteprima del menù -->
                    <div aria-hidden="true" class="relative mx-auto hidden h-[26rem] w-full max-w-md sm:block">
                        <div class="absolute top-6 left-1/2 w-60 -translate-x-1/2 rotate-2 rounded-[2.25rem] border-[6px] border-surface-900 bg-white p-4 shadow-2xl shadow-surface-900/25 dark:border-surface-950 dark:bg-surface-900">
                            <div class="mx-auto mb-4 h-1.5 w-16 rounded-full bg-surface-200 dark:bg-surface-700"></div>
                            <div class="mb-3 flex items-center gap-2">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-50 text-primary-600 dark:bg-primary-500/15 dark:text-primary-400">
                                    <UtensilsCrossed :size="15" />
                                </span>
                                <div>
                                    <p class="text-xs font-semibold text-surface-900 dark:text-white">Trattoria da Mario</p>
                                    <p class="text-[10px] text-surface-500">Menù del giorno</p>
                                </div>
                            </div>
                            <div class="mb-3 flex gap-1.5">
                                <span class="rounded-full bg-primary-500 px-2 py-0.5 text-[9px] font-semibold text-white">Primi</span>
                                <span class="rounded-full bg-surface-100 px-2 py-0.5 text-[9px] font-medium text-surface-600 dark:bg-surface-800 dark:text-surface-300">Secondi</span>
                                <span class="rounded-full bg-surface-100 px-2 py-0.5 text-[9px] font-medium text-surface-600 dark:bg-surface-800 dark:text-surface-300">Dolci</span>
                            </div>
                            <ul class="space-y-2">
                                <li v-for="dish in sampleDishes" :key="dish.name" class="rounded-xl bg-surface-50 p-2.5 dark:bg-surface-800">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-[11px] font-semibold text-surface-900 dark:text-white">{{ dish.name }}</p>
                                        <p class="text-[11px] font-semibold text-primary-600 dark:text-primary-400">€{{ dish.price }}</p>
                                    </div>
                                    <span v-if="dish.tag" class="mt-1 inline-flex items-center gap-1 text-[9px] text-emerald-600 dark:text-emerald-400">
                                        <Leaf :size="9" /> {{ dish.tag }}
                                    </span>
                                    <div v-else class="mt-1.5 h-1 w-20 rounded-full bg-surface-200 dark:bg-surface-700"></div>
                                </li>
                            </ul>
                        </div>
                        <!-- QR al tavolo -->
                        <div class="absolute bottom-6 left-0 flex h-28 w-28 -rotate-6 flex-col items-center justify-center gap-1.5 rounded-2xl border border-white/70 bg-white/95 shadow-xl shadow-surface-900/15 backdrop-blur dark:border-surface-600 dark:bg-surface-800/95">
                            <QrCode :size="48" :stroke-width="1.5" class="text-surface-800 dark:text-surface-100" />
                            <span class="text-[9px] font-semibold text-surface-500">Tavolo 12</span>
                        </div>
                        <!-- Notifica aggiornamento -->
                        <div class="absolute top-16 right-0 flex rotate-3 items-center gap-2 rounded-xl border border-white/70 bg-white/95 px-3 py-2 shadow-xl shadow-surface-900/15 backdrop-blur dark:border-surface-600 dark:bg-surface-800/95">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400">
                                <RefreshCcw :size="13" />
                            </span>
                            <span class="text-[10px] font-semibold text-surface-700 dark:text-surface-200">Menù aggiornato</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Funzionalità -->
            <section class="ml-card p-6 sm:p-10">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-xs font-semibold tracking-wider text-primary-600 uppercase dark:text-primary-400">Cosa stiamo preparando</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-surface-900 sm:text-3xl dark:text-white">Tutto quello che serve a un menù moderno</h2>
                    <p class="mt-3 text-surface-600 dark:text-surface-300">Le funzionalità definitive le scopriremo insieme strada facendo: questa è la direzione.</p>
                </div>
                <div class="mt-10 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    <div v-for="feature in features" :key="feature.title" class="rounded-xl border border-surface-100 bg-surface-50/60 p-5 dark:border-surface-700/60 dark:bg-surface-800/40">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl" :class="feature.tile">
                            <component :is="feature.icon" :size="21" />
                        </span>
                        <h3 class="mt-4 font-semibold text-surface-900 dark:text-white">{{ feature.title }}</h3>
                        <p class="mt-1.5 text-sm text-surface-600 dark:text-surface-400">{{ feature.text }}</p>
                    </div>
                </div>
            </section>

            <!-- Sezione alternata 1: testo a sinistra, illustrazione a destra -->
            <section class="ml-card grid items-center gap-10 overflow-hidden p-6 sm:p-10 lg:grid-cols-2">
                <div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                        <Wine :size="21" />
                    </span>
                    <h2 class="mt-4 text-2xl font-semibold tracking-tight text-surface-900 dark:text-white">Dal pranzo alla carta dei vini</h2>
                    <p class="mt-3 text-surface-600 dark:text-surface-300">
                        Più menù per lo stesso locale, ognuno con le sue sezioni: pranzo di lavoro, cena, carta dei vini, menù degustazione. Ogni menù potrà avere il suo QR Code oppure condividerne uno solo.
                    </p>
                </div>
                <div aria-hidden="true" class="grid grid-cols-2 gap-3">
                    <div v-for="(label, i) in ['Pranzo', 'Cena', 'Vini', 'Dolci']" :key="label" class="rounded-xl border border-surface-200/80 bg-white p-4 shadow-sm dark:border-surface-700 dark:bg-surface-800" :class="i % 2 ? 'translate-y-4' : ''">
                        <p class="text-sm font-semibold text-surface-900 dark:text-white">{{ label }}</p>
                        <div class="mt-3 space-y-1.5">
                            <div class="h-1.5 w-full rounded-full bg-surface-100 dark:bg-surface-700"></div>
                            <div class="h-1.5 w-3/4 rounded-full bg-surface-100 dark:bg-surface-700"></div>
                            <div class="h-1.5 w-1/2 rounded-full bg-primary-200 dark:bg-primary-500/30"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sezione alternata 2: illustrazione a sinistra, testo a destra -->
            <section class="ml-card grid items-center gap-10 overflow-hidden p-6 sm:p-10 lg:grid-cols-2">
                <div aria-hidden="true" class="order-2 lg:order-1">
                    <div class="rounded-xl border border-surface-200/80 bg-surface-50 p-5 dark:border-surface-700 dark:bg-surface-800/60">
                        <div class="mb-4 flex items-center justify-between">
                            <p class="text-sm font-semibold text-surface-900 dark:text-white">Consultazioni del menù</p>
                            <span class="ml-badge bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">Settimana</span>
                        </div>
                        <div class="flex h-32 items-end gap-2">
                            <div v-for="(h, i) in [35, 50, 42, 68, 90, 100, 74]" :key="i" class="flex-1 rounded-t-md bg-gradient-to-t from-primary-500 to-primary-300 dark:from-primary-600 dark:to-primary-400" :style="{ height: `${h}%` }"></div>
                        </div>
                        <div class="mt-2 flex justify-between text-[10px] text-surface-400">
                            <span v-for="(d, i) in ['L', 'M', 'M', 'G', 'V', 'S', 'D']" :key="i">{{ d }}</span>
                        </div>
                    </div>
                </div>
                <div class="order-1 lg:order-2">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                        <CalendarClock :size="21" />
                    </span>
                    <h2 class="mt-4 text-2xl font-semibold tracking-tight text-surface-900 dark:text-white">Capire i tuoi clienti</h2>
                    <p class="mt-3 text-surface-600 dark:text-surface-300">
                        Con le statistiche di MagLink saprai in quali giorni e orari il menù viene consultato di più, così potrai organizzare meglio offerte, turni e cucina.
                    </p>
                </div>
            </section>

            <!-- Come funzionerà -->
            <section class="ml-card p-6 sm:p-10">
                <h2 class="text-center text-2xl font-semibold tracking-tight text-surface-900 dark:text-white">Come funzionerà</h2>
                <ol class="mt-8 grid gap-6 md:grid-cols-3">
                    <li v-for="(step, index) in steps" :key="step.title" class="flex flex-col items-center text-center">
                        <span class="relative flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400">
                            <component :is="step.icon" :size="24" />
                            <span class="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full bg-primary-500 text-[11px] font-bold text-white">{{ index + 1 }}</span>
                        </span>
                        <h3 class="mt-4 font-semibold text-surface-900 dark:text-white">{{ step.title }}</h3>
                        <p class="mt-1 text-sm text-surface-600 dark:text-surface-400">{{ step.text }}</p>
                    </li>
                </ol>
            </section>

            <!-- Chiusura -->
            <section class="relative isolate overflow-hidden rounded-2xl bg-gradient-to-r from-primary-500 to-rose-500 p-8 text-white sm:p-10">
                <div class="flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
                    <div class="flex items-start gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/15">
                            <BellRing :size="22" />
                        </span>
                        <div>
                            <h2 class="text-xl font-semibold">Ti avviseremo appena sarà pronto</h2>
                            <p class="mt-1 text-white/85">La nuova area comparirà qui, nel menù laterale, insieme a link, QR Code e pagine.</p>
                        </div>
                    </div>
                    <Link :href="route('tenant.index')" class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-primary-600 shadow-sm transition-colors hover:bg-white/90">
                        Torna alla dashboard
                    </Link>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
