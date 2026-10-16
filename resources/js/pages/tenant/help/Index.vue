<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, type Component } from 'vue';
import {
    BarChart3,
    BookOpen,
    CreditCard,
    FileText,
    LayoutDashboard,
    LifeBuoy,
    Lightbulb,
    Link2,
    Mail,
    QrCode,
    Search,
    SearchX,
    Settings,
    ShieldCheck,
    UtensilsCrossed,
} from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import { useRoute } from '@/composables/useRoute';

const route = useRoute();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Aiuto e supporto', href: route('help.index') }];

interface Section {
    id: string;
    title: string;
    icon: Component;
    tile: string;
    intro: string;
    // Passaggi numerati (facoltativi)
    steps?: { title: string; text: string }[];
    // Punti chiave
    items?: { title: string; text: string }[];
    tip?: string;
    // Collegamento rapido alla sezione dell'app
    action?: { label: string; route: string };
}

const sections: Section[] = [
    {
        id: 'primi-passi',
        title: 'Primi passi',
        icon: LayoutDashboard,
        tile: 'bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400',
        intro: 'MagLink riunisce in un unico spazio link brevi, QR Code e pagine. La dashboard ti mostra a colpo d’occhio cosa hai creato, come sta andando e quanto del tuo piano stai usando.',
        items: [
            { title: 'Dashboard', text: 'Totali, andamento rispetto al periodo precedente, grafico di click e scansioni (7 o 30 giorni), link più performanti e ultimi contenuti creati.' },
            { title: 'Utilizzo piano', text: 'Indica quanti link, QR Code e pagine hai creato rispetto ai limiti del piano. Quando ti avvicini o raggiungi un limite comparirà un avviso.' },
            { title: 'Menu laterale', text: 'Da qui raggiungi Link, QR Code, Pagine, le impostazioni del profilo e questa guida.' },
        ],
        action: { label: 'Vai alla dashboard', route: 'tenant.index' },
    },
    {
        id: 'link',
        title: 'Link brevi',
        icon: Link2,
        tile: 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
        intro: 'Un link breve sostituisce un indirizzo lungo con uno corto e facile da condividere, e ti permette di contare i click ricevuti.',
        steps: [
            { title: 'Crea il link', text: 'Da Link → “Aggiungi nuovo link” inserisci un titolo e l’URL di destinazione. Il codice breve viene generato automaticamente.' },
            { title: 'Scegli le impostazioni', text: 'Decidi se il link è attivo e se mostrare la pagina di consenso privacy prima del reindirizzamento.' },
            { title: 'Salva e condividi', text: 'Usa “Salva” in alto. Nell’elenco puoi copiare il link breve con un clic o aprirlo in una nuova scheda.' },
        ],
        items: [
            { title: 'Cerca, ordina e filtra', text: 'Nell’elenco trovi i link per titolo, descrizione, codice o destinazione, e li ordini per data, click o nome.' },
            { title: 'Disattivare un link', text: 'Un link disattivato smette di reindirizzare (restituisce “pagina non trovata”) ma non perdi i dati raccolti.' },
            { title: 'Esporta statistiche', text: 'Con i piani a pagamento puoi esportare le statistiche dei link in PDF o CSV.' },
        ],
        tip: 'Il codice breve non si può cambiare dopo la creazione: così i link già condivisi continuano sempre a funzionare.',
        action: { label: 'Apri i link', route: 'links.index' },
    },
    {
        id: 'qr-code',
        title: 'QR Code',
        icon: QrCode,
        tile: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
        intro: 'Puoi generare QR Code per siti web, testi, email, numeri di telefono, SMS e biglietti da visita (vCard).',
        items: [
            { title: 'QR statico', text: 'Il contenuto è scritto direttamente nel codice: funziona anche offline ma non si può modificare e le scansioni non vengono contate.' },
            { title: 'QR dinamico', text: 'Il codice punta a un indirizzo MagLink: puoi cambiarne il contenuto in qualsiasi momento senza ristamparlo e ogni scansione viene registrata.' },
            { title: 'Personalizzazione', text: 'Scegli i colori del QR. Logo e sfondo trasparente sono disponibili con i piani a pagamento.' },
            { title: 'Download', text: 'Scarica il QR in PNG, JPG o SVG (consigliato per la stampa) dalla pagina di modifica o di dettaglio.' },
        ],
        tip: 'Se pensi di dover cambiare la destinazione in futuro (menù, promozioni, volantini) scegli sempre un QR dinamico.',
        action: { label: 'Apri i QR Code', route: 'qrcodes.index' },
    },
    {
        id: 'pagine',
        title: 'Pagine',
        icon: FileText,
        tile: 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400',
        intro: 'Le pagine sono micro landing page composte da blocchi: perfette come “link in bio” o come vetrina rapida per un evento o un prodotto.',
        steps: [
            { title: 'Crea la pagina', text: 'Inserisci titolo, indirizzo (slug) e descrizione, scegli il colore del tema e se pubblicarla subito.' },
            { title: 'Aggiungi i blocchi', text: 'Nell’editor usa “Aggiungi blocco”: titoli, testi, pulsanti, link, social, immagini, video, mappe, separatori e HTML personalizzato.' },
            { title: 'Organizza e pubblica', text: 'Trascina i blocchi per riordinarli e ridimensionarli, poi salva. Con “Anteprima” vedi la pagina come la vedranno i visitatori.' },
        ],
        tip: 'Il piano Free consente una pagina con un massimo di 6 blocchi; i piani a pagamento ne permettono di più.',
        action: { label: 'Apri le pagine', route: 'pages.index' },
    },
    {
        id: 'statistiche',
        title: 'Statistiche e privacy',
        icon: BarChart3,
        tile: 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400',
        intro: 'Ogni link e QR dinamico ha una pagina “Analytics” con l’andamento nel tempo e il dettaglio di dispositivi, browser, paesi e provenienza dei visitatori.',
        items: [
            { title: 'Pagina di consenso', text: 'Se attiva, prima del reindirizzamento il visitatore sceglie se condividere i propri dati. Se accetta raccogliamo statistiche dettagliate.' },
            { title: 'Visite anonime', text: 'Se il visitatore rifiuta, o se la pagina di consenso è disattivata, la visita viene comunque contata ma senza dati personali.' },
            { title: 'Bot e crawler', text: 'Le visite automatiche (motori di ricerca, anteprime dei social) vengono escluse dalle statistiche.' },
        ],
        tip: 'Nell’elenco dei link l’icona con lo scudo indica le visite con consenso, quella con l’occhio barrato le visite anonime.',
    },
    {
        id: 'ricerca',
        title: 'Ricerca rapida',
        icon: Search,
        tile: 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400',
        intro: 'Il campo di ricerca in alto cerca contemporaneamente tra link, QR Code e pagine: titoli, descrizioni, codici brevi e destinazioni.',
        items: [
            { title: 'Scorciatoia da tastiera', text: 'Premi ⌘K su Mac o Ctrl+K su Windows e Linux per aprire la ricerca da qualsiasi pagina.' },
            { title: 'Navigazione', text: 'Usa le frecce ↑↓ per scorrere i risultati, Invio per aprire quello selezionato ed Esc per chiudere.' },
        ],
    },
    {
        id: 'piani',
        title: 'Piani e limiti',
        icon: CreditCard,
        tile: 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400',
        intro: 'Ogni nuovo account può provare gratuitamente le funzionalità premium per 30 giorni. Poi puoi scegliere il piano Free o uno a pagamento.',
        items: [
            { title: 'Piano Free', text: 'Fino a 5 link, 5 QR Code (di cui 1 dinamico) e 1 pagina con 6 blocchi.' },
            { title: 'Limite raggiunto', text: 'Se superi un limite (ad esempio dopo un cambio di piano) i contenuti esistenti restano attivi, ma non puoi crearne di nuovi finché non passi a un piano superiore.' },
            { title: 'Gestire l’abbonamento', text: 'Dal menu utente in alto a destra apri “Piani e Prezzi” per cambiare piano o gestire la fatturazione.' },
        ],
        action: { label: 'Vedi i piani', route: 'plans.index' },
    },
    {
        id: 'account',
        title: 'Account e impostazioni',
        icon: Settings,
        tile: 'bg-surface-100 text-surface-600 dark:bg-surface-700/60 dark:text-surface-300',
        intro: 'Dalle impostazioni puoi aggiornare nome ed email, cambiare la password e scegliere l’aspetto dell’interfaccia.',
        items: [
            { title: 'Tema chiaro o scuro', text: 'Usa l’icona luna/sole nella barra in alto per cambiare tema in qualsiasi momento.' },
            { title: 'Lingua', text: 'Seleziona la lingua dell’interfaccia dalla bandierina nella barra in alto.' },
            { title: 'Eliminare l’account', text: 'In fondo alla pagina del profilo trovi l’opzione per eliminare l’account (ti verrà chiesta la password).' },
        ],
        action: { label: 'Apri le impostazioni', route: 'profile.edit' },
    },
    {
        id: 'menu',
        title: 'Menù digitale (in arrivo)',
        icon: UtensilsCrossed,
        tile: 'bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400',
        intro: 'Stiamo preparando un’area dedicata alla ristorazione per creare menù digitali collegati ai QR Code. La trovi già nel menu laterale con l’etichetta “Presto”.',
        action: { label: 'Scopri di più', route: 'menu.index' },
    },
];

// Filtro testuale sulle sezioni della guida
const query = ref('');
const filteredSections = computed(() => {
    const q = query.value.trim().toLowerCase();
    if (!q) return sections;
    return sections.filter((section) =>
        [section.title, section.intro, section.tip ?? '', ...(section.steps ?? []).flatMap((s) => [s.title, s.text]), ...(section.items ?? []).flatMap((i) => [i.title, i.text])]
            .join(' ')
            .toLowerCase()
            .includes(q),
    );
});

// Evidenzia nell'indice la sezione visibile
const activeId = ref(sections[0].id);
let observer: IntersectionObserver | undefined;
onMounted(() => {
    observer = new IntersectionObserver(
        (entries) => {
            const visible = entries.filter((e) => e.isIntersecting).sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top);
            if (visible[0]) activeId.value = visible[0].target.id;
        },
        { rootMargin: '-80px 0px -60% 0px' },
    );
    document.querySelectorAll('[data-help-section]').forEach((el) => observer?.observe(el));
});
onBeforeUnmount(() => observer?.disconnect());
</script>

<template>
    <Head title="Aiuto e supporto" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex w-full flex-col gap-5 lg:p-2">
            <PageHeader
                title="Aiuto e supporto"
                subtitle="Come usare le sezioni di MagLink, passo dopo passo"
                :icon="LifeBuoy"
                icon-class="bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400"
            />

            <div class="grid gap-5 lg:grid-cols-[15rem_minmax(0,1fr)] xl:grid-cols-[17rem_minmax(0,1fr)]">
                <!-- Indice -->
                <aside class="lg:sticky lg:top-20 lg:self-start">
                    <div class="ml-card p-3">
                        <label class="relative block">
                            <span class="sr-only">Cerca nella guida</span>
                            <Search :size="15" class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-surface-400" />
                            <input
                                v-model="query"
                                type="search"
                                placeholder="Cerca nella guida…"
                                class="h-9 w-full rounded-lg border border-transparent bg-surface-50 pr-3 pl-9 text-sm text-surface-900 placeholder:text-surface-400 focus:border-primary-400 focus:bg-white focus:ring-2 focus:ring-primary-500/20 focus:outline-none dark:bg-surface-900/60 dark:text-white"
                            />
                        </label>
                        <nav class="mt-2 hidden lg:block" aria-label="Indice della guida">
                            <ul class="space-y-0.5">
                                <li v-for="section in filteredSections" :key="section.id">
                                    <a
                                        :href="`#${section.id}`"
                                        class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm transition-colors"
                                        :class="activeId === section.id
                                            ? 'bg-primary-50 font-medium text-primary-700 dark:bg-primary-500/15 dark:text-primary-300'
                                            : 'text-surface-600 hover:bg-surface-100 hover:text-surface-900 dark:text-surface-300 dark:hover:bg-surface-800'"
                                    >
                                        <component :is="section.icon" :size="16" class="shrink-0" />
                                        <span class="truncate">{{ section.title }}</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>

                    <div class="ml-card mt-4 hidden p-4 lg:block">
                        <p class="flex items-center gap-2 text-sm font-semibold text-surface-900 dark:text-white">
                            <Mail :size="16" class="text-primary-500" /> Serve altro aiuto?
                        </p>
                        <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">Scrivici e ti risponderemo il prima possibile.</p>
                        <a href="mailto:support@magmatic.it" class="mt-3 inline-flex text-xs font-semibold text-primary-600 hover:underline dark:text-primary-400">support@magmatic.it</a>
                    </div>
                </aside>

                <!-- Contenuti -->
                <div class="flex min-w-0 flex-col gap-5">
                    <section
                        v-for="section in filteredSections"
                        :id="section.id"
                        :key="section.id"
                        data-help-section
                        class="ml-card scroll-mt-20 p-5 sm:p-7"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl" :class="section.tile">
                                    <component :is="section.icon" :size="21" />
                                </span>
                                <h2 class="text-lg font-semibold tracking-tight text-surface-900 sm:text-xl dark:text-white">{{ section.title }}</h2>
                            </div>
                            <Link v-if="section.action" :href="route(section.action.route)" class="ml-btn-secondary hidden shrink-0 px-3 py-1.5 text-[13px] sm:inline-flex">
                                {{ section.action.label }}
                            </Link>
                        </div>

                        <p class="mt-4 max-w-3xl text-[15px] leading-relaxed text-surface-600 dark:text-surface-300">{{ section.intro }}</p>

                        <ol v-if="section.steps" class="mt-5 grid gap-3 md:grid-cols-3">
                            <li v-for="(step, index) in section.steps" :key="step.title" class="rounded-xl border border-surface-200/80 bg-surface-50/60 p-4 dark:border-surface-700 dark:bg-surface-900/40">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-primary-500 text-xs font-bold text-white">{{ index + 1 }}</span>
                                <p class="mt-3 text-sm font-semibold text-surface-900 dark:text-white">{{ step.title }}</p>
                                <p class="mt-1 text-sm text-surface-600 dark:text-surface-400">{{ step.text }}</p>
                            </li>
                        </ol>

                        <dl v-if="section.items" class="mt-5 grid gap-x-8 gap-y-4 md:grid-cols-2">
                            <div v-for="item in section.items" :key="item.title" class="border-l-2 border-surface-200 pl-4 dark:border-surface-700">
                                <dt class="text-sm font-semibold text-surface-900 dark:text-white">{{ item.title }}</dt>
                                <dd class="mt-1 text-sm text-surface-600 dark:text-surface-400">{{ item.text }}</dd>
                            </div>
                        </dl>

                        <p v-if="section.tip" class="mt-5 flex gap-2.5 rounded-lg border border-amber-200 bg-amber-50 p-3.5 text-sm text-amber-900 dark:border-amber-500/25 dark:bg-amber-500/10 dark:text-amber-100">
                            <Lightbulb :size="17" class="mt-0.5 shrink-0 text-amber-500" />
                            <span><strong class="font-semibold">Consiglio:</strong> {{ section.tip }}</span>
                        </p>

                        <Link v-if="section.action" :href="route(section.action.route)" class="ml-btn-secondary mt-5 px-3 py-1.5 text-[13px] sm:hidden">
                            {{ section.action.label }}
                        </Link>
                    </section>

                    <div v-if="!filteredSections.length" class="ml-card flex flex-col items-center gap-2 py-14 text-center">
                        <SearchX :size="28" class="text-surface-300 dark:text-surface-600" />
                        <p class="font-medium text-surface-700 dark:text-surface-200">Nessun argomento trovato</p>
                        <p class="text-sm text-surface-500 dark:text-surface-400">Prova con un’altra parola o scrivici a support@magmatic.it</p>
                    </div>

                    <div class="ml-card flex items-start gap-3 p-5 lg:hidden">
                        <BookOpen :size="18" class="mt-0.5 shrink-0 text-primary-500" />
                        <p class="text-sm text-surface-600 dark:text-surface-300">
                            Non hai trovato quello che cercavi? Scrivici a
                            <a href="mailto:support@magmatic.it" class="font-semibold text-primary-600 hover:underline dark:text-primary-400">support@magmatic.it</a>.
                        </p>
                    </div>

                    <p class="flex items-center gap-2 text-xs text-surface-400 dark:text-surface-500">
                        <ShieldCheck :size="14" /> La guida si aggiorna insieme a MagLink: le nuove funzionalità compariranno qui.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
