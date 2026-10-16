<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, type Component } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    BarChart3,
    ChevronLeft,
    ChevronRight,
    Copy,
    Eye,
    Image,
    Lightbulb,
    Link2,
    Lock,
    MousePointerClick,
    Palette,
    QrCode,
    RefreshCcw,
    ShieldCheck,
    Star,
    UserCheck,
} from 'lucide-vue-next';
import { useRoute } from '@/composables/useRoute';

const props = defineProps<{
    shortUrl?: string;
}>();

const route = useRoute();

interface Point {
    icon: Component;
    title: string;
    text: string;
    tile: string;
}

interface Tip {
    key: 'gdpr' | 'qr' | 'pages';
    title: string;
    text: string;
    points: Point[];
    cta: { label: string; route: string };
}

// Suggerimenti ispirati alle funzionalità presentate sul sito pubblico
const tips: Tip[] = [
    {
        key: 'gdpr',
        title: 'Link brevi, sicuri e conformi al GDPR',
        text: 'Attiva la pagina di consenso su link e QR Code: chi accetta ti regala statistiche dettagliate, chi rifiuta viene contato in modo anonimo.',
        points: [
            { icon: ShieldCheck, title: 'Privacy by design', text: 'Dati al sicuro fin dall’inizio', tile: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' },
            { icon: UserCheck, title: 'Pagina di consenso', text: 'Il visitatore sceglie cosa condividere', tile: 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400' },
            { icon: Eye, title: 'Visite anonime', text: 'Contate anche senza consenso', tile: 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' },
        ],
        cta: { label: 'Crea un link', route: 'links.create' },
    },
    {
        key: 'qr',
        title: 'QR Code dinamici: aggiorna senza ristampare',
        text: 'Con un QR dinamico puoi cambiare la destinazione quando vuoi: volantini, menù e vetrine restano validi e ogni scansione viene tracciata.',
        points: [
            { icon: RefreshCcw, title: 'Sempre aggiornabile', text: 'Cambi il contenuto, non la stampa', tile: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' },
            { icon: BarChart3, title: 'Scansioni tracciate', text: 'Analytics integrate per ogni QR', tile: 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400' },
            { icon: Palette, title: 'Il tuo stile', text: 'Colori e logo personalizzati', tile: 'bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400' },
        ],
        cta: { label: 'Nuovo QR Code', route: 'qrcodes.create' },
    },
    {
        key: 'pages',
        title: 'Una pagina a blocchi per tutti i tuoi link',
        text: 'Crea in pochi minuti una micro landing page: social, pulsanti, immagini e testi, da condividere con un solo link o un QR Code.',
        points: [
            { icon: Link2, title: 'Link e social', text: 'Tutti i tuoi canali in un posto', tile: 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' },
            { icon: Image, title: 'Immagini e video', text: 'Contenuti ricchi senza codice', tile: 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400' },
            { icon: MousePointerClick, title: 'Pulsanti', text: 'Inviti all’azione ben visibili', tile: 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400' },
        ],
        cta: { label: 'Crea una pagina', route: 'pages.create' },
    },
];

const current = ref(0);
const tip = computed(() => tips[current.value]);
const shortHost = computed(() => (props.shortUrl ?? 'maglink.it').replace(/^https?:\/\//, ''));

const go = (index: number) => {
    current.value = (index + tips.length) % tips.length;
};

// Scorrimento automatico, in pausa al passaggio del mouse e se l'utente preferisce meno animazioni
let timer: ReturnType<typeof setInterval> | undefined;
const paused = ref(false);
onMounted(() => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    timer = setInterval(() => {
        if (!paused.value) go(current.value + 1);
    }, 8000);
});
onBeforeUnmount(() => clearInterval(timer));

// Stelle disposte a cerchio (illustrazione GDPR)
const stars = Array.from({ length: 12 }, (_, i) => {
    const angle = (i / 12) * Math.PI * 2;
    return { left: `${50 + 44 * Math.cos(angle)}%`, top: `${50 + 44 * Math.sin(angle)}%` };
});
</script>

<template>
    <section
        class="ml-card @container relative isolate flex flex-col overflow-hidden p-6 sm:p-7"
        aria-roledescription="carousel"
        aria-label="Suggerimenti"
        @mouseenter="paused = true"
        @mouseleave="paused = false"
    >
        <!-- Sfondo sfumato -->
        <div aria-hidden="true" class="absolute inset-0 -z-10 bg-gradient-to-br from-white via-white to-blue-50/80 dark:from-surface-800 dark:via-surface-800 dark:to-blue-500/10"></div>
        <div aria-hidden="true" class="absolute -right-24 -bottom-24 -z-10 h-72 w-72 rounded-full bg-blue-100/60 blur-3xl dark:bg-blue-500/10"></div>

        <Transition
            mode="out-in"
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-1"
            leave-active-class="transition duration-200 ease-in"
            leave-to-class="opacity-0"
        >
            <div :key="tip.key" class="grid flex-1 items-center gap-6 @[44rem]:grid-cols-[minmax(0,1fr)_14rem]">
                <div class="min-w-0">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-50 px-2.5 py-1 text-xs font-semibold text-orange-600 dark:bg-orange-500/10 dark:text-orange-400">
                        <Lightbulb :size="13" /> Lo sapevi che?
                    </span>
                    <h3 class="mt-3 text-xl leading-snug font-semibold tracking-tight text-surface-900 dark:text-white">{{ tip.title }}</h3>
                    <p class="mt-2 text-sm text-surface-600 dark:text-surface-300">{{ tip.text }}</p>

                    <ul class="mt-5 grid gap-4 @[30rem]:grid-cols-3">
                        <li v-for="point in tip.points" :key="point.title" class="flex items-start gap-3 @[30rem]:flex-col @[30rem]:gap-2">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg" :class="point.tile">
                                <component :is="point.icon" :size="17" />
                            </span>
                            <span>
                                <span class="block text-[13px] font-semibold text-surface-900 dark:text-white">{{ point.title }}</span>
                                <span class="block text-xs text-surface-500 dark:text-surface-400">{{ point.text }}</span>
                            </span>
                        </li>
                    </ul>
                </div>

                <!-- Illustrazione (solo se la card è abbastanza larga) -->
                <div aria-hidden="true" class="relative hidden h-44 @[44rem]:block">
                    <!-- GDPR: link + scudo + stelle UE -->
                    <template v-if="tip.key === 'gdpr'">
                        <div class="absolute top-2 right-0 h-36 w-36">
                            <Star v-for="(star, i) in stars" :key="i" :size="11" class="absolute -translate-x-1/2 -translate-y-1/2 fill-amber-400 text-amber-400" :style="star" />
                        </div>
                        <div class="absolute top-8 left-0 w-44 -rotate-3 rounded-xl border border-white bg-white/95 p-3 shadow-lg shadow-blue-900/10 dark:border-surface-600 dark:bg-surface-800/95">
                            <div class="flex items-center gap-2 rounded-lg border border-surface-100 px-2 py-1.5 dark:border-surface-700">
                                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-500/15"><Link2 :size="11" /></span>
                                <span class="flex-1 truncate text-[10px] font-semibold text-surface-700 dark:text-surface-200">{{ shortHost }}/7aB3</span>
                                <Copy :size="10" class="text-surface-400" />
                            </div>
                            <div class="mt-2.5 space-y-1.5">
                                <div class="h-1.5 w-3/4 rounded-full bg-surface-100 dark:bg-surface-700"></div>
                                <div class="h-1.5 w-1/2 rounded-full bg-surface-100 dark:bg-surface-700"></div>
                            </div>
                        </div>
                        <div class="absolute right-7 bottom-1 flex h-24 w-20 flex-col items-center justify-center gap-1 rounded-b-[2.5rem] rounded-t-xl bg-gradient-to-b from-blue-500 to-blue-700 text-white shadow-xl shadow-blue-700/30">
                            <Lock :size="22" :stroke-width="2.5" />
                            <span class="text-[11px] font-bold tracking-wide">GDPR</span>
                        </div>
                    </template>

                    <!-- QR dinamico -->
                    <template v-else-if="tip.key === 'qr'">
                        <div class="absolute top-3 left-6 flex h-32 w-32 -rotate-6 items-center justify-center rounded-2xl border border-white bg-white/95 shadow-lg shadow-emerald-900/10 dark:border-surface-600 dark:bg-surface-800/95">
                            <QrCode :size="76" :stroke-width="1.4" class="text-surface-800 dark:text-surface-100" />
                        </div>
                        <div class="absolute right-2 bottom-4 flex rotate-3 items-center gap-2 rounded-xl border border-white bg-white/95 px-3 py-2 shadow-lg shadow-emerald-900/10 dark:border-surface-600 dark:bg-surface-800/95">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-500 text-white"><RefreshCcw :size="14" /></span>
                            <span class="text-[10px] leading-tight font-semibold text-surface-700 dark:text-surface-200">Destinazione<br />aggiornata</span>
                        </div>
                        <div class="absolute top-0 right-6 flex items-center gap-1 rounded-lg bg-emerald-50 px-2 py-1 text-[10px] font-semibold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                            <BarChart3 :size="11" /> +128 scansioni
                        </div>
                    </template>

                    <!-- Pagina a blocchi -->
                    <template v-else>
                        <div class="absolute top-0 left-1/2 w-36 -translate-x-1/2 rotate-2 rounded-2xl border border-white bg-white/95 p-3 shadow-lg shadow-violet-900/10 dark:border-surface-600 dark:bg-surface-800/95">
                            <div class="mx-auto h-8 w-8 rounded-full bg-gradient-to-br from-primary-400 to-rose-400"></div>
                            <div class="mx-auto mt-1.5 h-1.5 w-16 rounded-full bg-surface-200 dark:bg-surface-700"></div>
                            <div class="mt-3 space-y-1.5">
                                <div class="h-4 rounded-md bg-violet-100 dark:bg-violet-500/20"></div>
                                <div class="h-4 rounded-md bg-blue-100 dark:bg-blue-500/20"></div>
                                <div class="grid grid-cols-2 gap-1.5">
                                    <div class="h-8 rounded-md bg-rose-100 dark:bg-rose-500/20"></div>
                                    <div class="h-8 rounded-md bg-amber-100 dark:bg-amber-500/20"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </Transition>

        <!-- Controlli -->
        <div class="mt-5 flex items-center justify-between gap-3">
            <Link :href="route(tip.cta.route)" class="inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400">
                {{ tip.cta.label }} <ArrowRight :size="15" />
            </Link>
            <div class="flex items-center gap-3">
                <button type="button" class="ml-icon-btn h-8 w-8 rounded-full border border-surface-200 bg-white dark:border-surface-600 dark:bg-surface-800" aria-label="Suggerimento precedente" @click="go(current - 1)">
                    <ChevronLeft :size="16" />
                </button>
                <div class="flex items-center gap-1.5">
                    <button
                        v-for="(item, index) in tips"
                        :key="item.key"
                        type="button"
                        class="h-1.5 rounded-full transition-all"
                        :class="index === current ? 'w-5 bg-blue-500' : 'w-1.5 bg-surface-300 hover:bg-surface-400 dark:bg-surface-600'"
                        :aria-label="`Suggerimento ${index + 1}`"
                        :aria-current="index === current"
                        @click="go(index)"
                    ></button>
                </div>
                <button type="button" class="ml-icon-btn h-8 w-8 rounded-full border border-surface-200 bg-white dark:border-surface-600 dark:bg-surface-800" aria-label="Suggerimento successivo" @click="go(current + 1)">
                    <ChevronRight :size="16" />
                </button>
            </div>
        </div>
    </section>
</template>
