<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    Link2,
    QrCode,
    Plus,
    Box,
    TrendingUp,
    FileText,
    Eye,
    BarChart3,
} from 'lucide-vue-next';
import PlanLimitsCard from '@/components/PlanLimitsCard.vue';
import PerformanceChart from '@/components/PerformanceChart.vue';
import StatCard, { type Trend } from '@/components/dashboard/StatCard.vue';
import ActivitySummary from '@/components/dashboard/ActivitySummary.vue';
import TipsCarousel from '@/components/dashboard/TipsCarousel.vue';
import RecentCard from '@/components/dashboard/RecentCard.vue';
import RowMenu from '@/components/dashboard/RowMenu.vue';
import Sparkline from '@/components/dashboard/Sparkline.vue';
import {
    Calendar,
    Code,
    ExternalLink,
    Heading,
    Image as ImageIcon,
    MapPin,
    Minus,
    MousePointerClick,
    Pencil,
    PlayCircle,
    Share2,
    Type,
} from 'lucide-vue-next';
import type { Component } from 'vue';
import TopLinks, { type TopLink } from '@/components/dashboard/TopLinks.vue';
import { METRIC_COLORS, SERIES_COLORS } from '@/components/dashboard/colors';
import { useRoute } from '@/composables/useRoute';
import heroMountains from '../../../images/dashboard/hero-mountains.webp';

const route = useRoute();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/',
    },
];

interface Stats {
    links_count: number;
    qrcodes_count: number;
    pages_count: number;
    blocks_count: number;
    total_views: number;
}

interface LinkItem {
    id: number;
    title: string;
    url: string;
    views_count: number;
    created_at: string;
}

interface QrCodeItem {
    id: number;
    name: string;
    slug: string;
    scans: number;
    created_at: string;
}

interface PageItem {
    id: number;
    title: string;
    slug: string;
    views: number;
    is_active: boolean;
    created_at: string;
}

interface Block {
    id: number;
    page_id: number;
    type: string;
    title: string;
    position: number;
    created_at: string;
    page: {
        id: number;
        title: string;
        slug: string;
    };
}

interface PerformanceData {
    date: string;
    link_views: number;
    qrcode_views: number;
    total_items: number;
}

const props = defineProps<{
    stats: Stats;
    latest_links: LinkItem[];
    latest_qrcodes: QrCodeItem[];
    latest_pages: PageItem[];
    latest_blocks: Block[];
    performance: PerformanceData[];
    trends: Record<'links' | 'qrcodes' | 'pages' | 'blocks' | 'views', Trend>;
    top_links: TopLink[];
    latest_sparklines: { links: Record<number, number[]>; qrcodes: Record<number, number[]> };
    shortUrl: string;
    period: number;
    periods: number[];
}>();

const firstName = computed(() => (page.props as any).auth?.user?.first_name ?? '');

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('it-IT', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatNumber = (num: number) => new Intl.NumberFormat('it-IT').format(num);
const formatCompact = (num: number) =>
    new Intl.NumberFormat('it-IT', { notation: 'compact', maximumFractionDigits: 1 }).format(num);

// Colori a rotazione per le righe degli ultimi contenuti (riquadro icona + sparkline)
const rowPalette = [
    { tile: 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400', color: '#3b82f6' },
    { tile: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400', color: '#10b981' },
    { tile: 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400', color: '#8b5cf6' },
    { tile: 'bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400', color: '#f97316' },
    { tile: 'bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400', color: '#0ea5e9' },
];
const palette = (index: number) => rowPalette[index % rowPalette.length];

// Icona e colore per tipo di blocco
const blockTypes: Record<string, { icon: Component; tile: string; badge: string }> = {
    social: { icon: Share2, tile: 'bg-pink-50 text-pink-600 dark:bg-pink-500/10 dark:text-pink-400', badge: 'bg-sky-50 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300' },
    button: { icon: MousePointerClick, tile: 'bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400', badge: 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300' },
    link: { icon: Link2, tile: 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400', badge: 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300' },
    text: { icon: Type, tile: 'bg-surface-100 text-surface-600 dark:bg-surface-700/60 dark:text-surface-300', badge: 'bg-surface-100 text-surface-600 dark:bg-surface-700/60 dark:text-surface-300' },
    title: { icon: Heading, tile: 'bg-surface-100 text-surface-600 dark:bg-surface-700/60 dark:text-surface-300', badge: 'bg-surface-100 text-surface-600 dark:bg-surface-700/60 dark:text-surface-300' },
    image: { icon: ImageIcon, tile: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400', badge: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' },
    video: { icon: PlayCircle, tile: 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400', badge: 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-300' },
    map: { icon: MapPin, tile: 'bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400', badge: 'bg-teal-50 text-teal-700 dark:bg-teal-500/10 dark:text-teal-300' },
    separator: { icon: Minus, tile: 'bg-surface-100 text-surface-600 dark:bg-surface-700/60 dark:text-surface-300', badge: 'bg-surface-100 text-surface-600 dark:bg-surface-700/60 dark:text-surface-300' },
    html: { icon: Code, tile: 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400', badge: 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' },
};
const blockType = (type: string) => blockTypes[type] ?? { icon: Box, tile: rowPalette[3].tile, badge: 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300' };

// URL pubblico di una pagina (dominio principale, senza "app.")
const publicPageUrl = (slug: string) => {
    const { protocol, hostname, port } = window.location;
    return `${protocol}//${hostname.replace(/^app\./, '')}${port ? `:${port}` : ''}/${slug}`;
};

const formatShortDate = (date: string) =>
    new Date(date).toLocaleDateString('it-IT', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });

const hasRecentActivity = computed(() => props.performance.some((d) => d.total_items > 0));
const periodTotals = computed(() => ({
    links: props.performance.reduce((sum, d) => sum + d.link_views, 0),
    qrcodes: props.performance.reduce((sum, d) => sum + d.qrcode_views, 0),
}));

// Cambio periodo: ricarica solo i dati che dipendono dal periodo
const changePeriod = (event: Event) => {
    const days = Number((event.target as HTMLSelectElement).value);
    router.get(
        route('tenant.index'),
        { days },
        { preserveState: true, preserveScroll: true, replace: true, only: ['performance', 'trends', 'top_links', 'period'] },
    );
};

const statCards = computed(() => [
    {
        label: 'Link creati',
        value: formatNumber(props.stats.links_count),
        icon: Link2,
        iconClass: 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
        color: METRIC_COLORS.links,
        trend: props.trends.links,
    },
    {
        label: 'QR Code generati',
        value: formatNumber(props.stats.qrcodes_count),
        icon: QrCode,
        iconClass: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
        color: METRIC_COLORS.qrcodes,
        trend: props.trends.qrcodes,
    },
    {
        label: 'Pagine create',
        value: formatNumber(props.stats.pages_count),
        icon: FileText,
        iconClass: 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400',
        color: METRIC_COLORS.pages,
        trend: props.trends.pages,
    },
    {
        label: 'Blocchi totali',
        value: formatNumber(props.stats.blocks_count),
        icon: Box,
        iconClass: 'bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400',
        color: METRIC_COLORS.blocks,
        trend: props.trends.blocks,
    },
    {
        label: 'Visualizzazioni',
        value: formatNumber(props.stats.total_views),
        icon: TrendingUp,
        iconClass: 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400',
        color: METRIC_COLORS.views,
        trend: props.trends.views,
    },
]);

</script>

<template>
    <Head title="Dashboard">
        <link rel="icon" href="/favicon.ico" />
    </Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <!--
            Tutta la larghezza; dai 1760px in su i widget di riepilogo passano in una colonna laterale.
            Prima riga: benvenuto + suggerimenti | utilizzo piano (stessa altezza). Seconda riga: contenuti | colonna laterale.
        -->
        <div class="grid w-full grid-cols-1 gap-4 sm:gap-5 lg:p-2 3xl:grid-cols-[minmax(0,1fr)_23rem]">
            <!-- Hero + Utilizzo piano + Riepilogo attività (questi ultimi nella colonna laterale su 3xl) -->
            <div class="grid min-w-0 grid-cols-1 gap-4 sm:gap-5 md:grid-cols-2 2xl:grid-cols-4 3xl:grid-cols-5">
                <section
                    class="relative isolate min-h-[13rem] overflow-hidden rounded-xl border border-surface-200/80 bg-white md:col-span-2 3xl:col-span-3 dark:border-surface-700/70 dark:bg-surface-800"
                >
                    <img
                        :src="heroMountains"
                        alt=""
                        aria-hidden="true"
                        class="absolute inset-0 -z-10 h-full w-full object-cover object-[75%_60%] dark:opacity-60"
                    />
                    <!-- Velo per mantenere il testo leggibile sopra la foto -->
                    <div
                        class="absolute inset-0 -z-10 bg-gradient-to-r from-white/95 via-white/75 to-white/0 dark:from-surface-900/95 dark:via-surface-900/75 dark:to-surface-900/10"
                    ></div>

                    <div class="flex h-full items-center gap-6 p-6 sm:p-8">
                        <div class="min-w-0 flex-1">
                            <p class="mb-2 text-xs font-semibold tracking-wide text-primary-600 uppercase dark:text-primary-400">
                                Bentornato{{ firstName ? `, ${firstName}` : '' }}
                            </p>
                            <h1 class="text-2xl font-semibold tracking-tight text-surface-900 sm:text-[30px] sm:leading-tight dark:text-white">
                                I tuoi link, <span class="text-primary-500">più di un semplice clic.</span>
                            </h1>
                            <p class="mt-2 max-w-md text-sm text-surface-600 dark:text-surface-300">
                                Crea, gestisci e monitora link corti, QR Code e pagine. Tutto in un'unica piattaforma.
                            </p>
                            <div class="mt-5 flex flex-wrap gap-2.5">
                                <Link :href="route('links.create')" class="ml-btn-primary">
                                    <Plus :size="16" :stroke-width="2.5" /> Nuovo link
                                </Link>
                                <Link :href="route('qrcodes.create')" class="ml-btn-secondary bg-white/90 backdrop-blur dark:bg-surface-800/80">
                                    <QrCode :size="16" /> Nuovo QR Code
                                </Link>
                            </div>
                        </div>

                        <!-- Illustrazione decorativa (solo dove c'è spazio) -->
                        <div aria-hidden="true" class="relative mr-2 hidden h-36 w-40 shrink-0 sm:block">
                            <div
                                class="absolute top-0 left-0 flex h-32 w-28 -rotate-6 flex-col items-center justify-center gap-2 rounded-xl border border-white/70 bg-white/90 shadow-lg shadow-surface-900/15 backdrop-blur dark:border-surface-600 dark:bg-surface-800/90"
                            >
                                <QrCode :size="52" :stroke-width="1.5" class="text-surface-800 dark:text-surface-100" />
                                <span class="h-1.5 w-14 rounded-full bg-primary-200 dark:bg-primary-500/40"></span>
                            </div>
                            <div
                                class="absolute right-0 bottom-0 flex h-20 w-20 rotate-6 items-center justify-center rounded-xl border border-white/70 bg-white/90 shadow-lg shadow-surface-900/15 backdrop-blur dark:border-surface-600 dark:bg-surface-800/90"
                            >
                                <BarChart3 :size="34" :stroke-width="2" class="text-emerald-500" />
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Su schermi molto larghi i suggerimenti affiancano il benvenuto -->
                <TipsCarousel :short-url="shortUrl" class="hidden 3xl:col-span-2 3xl:flex" />

                <PlanLimitsCard :stats="stats" class="3xl:hidden" />
                <ActivitySummary :period="period" :trends="trends" class="3xl:hidden" />
            </div>

            <PlanLimitsCard :stats="stats" class="hidden 3xl:flex" />

          <div class="flex min-w-0 flex-col gap-4 sm:gap-5">
            <!-- Statistiche principali -->
            <div class="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-6 2xl:grid-cols-5">
                <StatCard
                    v-for="(card, index) in statCards"
                    :key="card.label"
                    v-bind="card"
                    :class="[
                        // Su lg: 3 card + 2 card che riempiono la riga, su 2xl: 5 in fila
                        index < 3 ? 'lg:col-span-2' : 'lg:col-span-3',
                        index === statCards.length - 1 ? 'col-span-2' : '',
                        '2xl:col-span-1',
                    ]"
                />
            </div>

            <!-- Andamento del periodo + Link più performanti (nella colonna laterale su 3xl) -->
            <div class="grid grid-cols-1 gap-4 sm:gap-5 xl:grid-cols-3">
            <section class="ml-card p-5 sm:p-6 xl:col-span-2 3xl:col-span-3">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="ml-section-title">
                            <TrendingUp :size="18" class="text-primary-500" />
                            Click e scansioni
                        </h3>
                        <div class="mt-1.5 flex flex-wrap items-center gap-4 text-xs text-surface-500 dark:text-surface-400">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="h-2 w-2 rounded-full" :style="{ backgroundColor: SERIES_COLORS.links }"></span>
                                Click sui link
                                <strong class="font-semibold text-surface-800 tabular-nums dark:text-surface-100">{{ formatNumber(periodTotals.links) }}</strong>
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <span class="h-2 w-2 rounded-full" :style="{ backgroundColor: SERIES_COLORS.qrcodes }"></span>
                                Scansioni QR
                                <strong class="font-semibold text-surface-800 tabular-nums dark:text-surface-100">{{ formatNumber(periodTotals.qrcodes) }}</strong>
                            </span>
                        </div>
                    </div>
                    <label class="relative">
                        <span class="sr-only">Periodo</span>
                        <select
                            :value="period"
                            class="cursor-pointer appearance-none rounded-lg border border-surface-200 bg-white py-1.5 pr-8 pl-3 text-xs font-medium text-surface-700 shadow-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 focus:outline-none dark:border-surface-600 dark:bg-surface-800 dark:text-surface-200"
                            @change="changePeriod"
                        >
                            <option v-for="days in periods" :key="days" :value="days">Ultimi {{ days }} giorni</option>
                        </select>
                        <svg class="pointer-events-none absolute top-1/2 right-2.5 h-3.5 w-3.5 -translate-y-1/2 text-surface-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </label>
                </div>
                <PerformanceChart v-if="hasRecentActivity" :data="performance" />
                <div
                    v-else
                    class="flex h-64 w-full flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-surface-200 text-center dark:border-surface-700"
                >
                    <TrendingUp :size="22" class="text-surface-300 dark:text-surface-600" />
                    <p class="px-4 text-sm text-surface-500 dark:text-surface-400">Nessun click o scansione negli ultimi {{ period }} giorni</p>
                </div>
            </section>
            <TopLinks :links="top_links" :period="period" class="3xl:hidden" />
            </div>

            <!-- Ultimi contenuti: 4 colonne solo sugli schermi molto larghi, altrimenti 2 -->
            <div class="grid grid-cols-1 gap-4 sm:gap-5 md:grid-cols-2 3xl:grid-cols-4">
                <!-- Link -->
                <RecentCard
                    title="Ultimi link"
                    subtitle="I tuoi link più recenti, sempre a portata di mano."
                    :icon="Link2"
                    tone="blue"
                    :view-all-href="route('links.index')"
                    footer-label="Gestisci tutti i tuoi link"
                    :footer-href="route('links.index')"
                    :footer-icon="Link2"
                    :empty="!latest_links.length"
                    empty-text="Nessun link creato"
                >
                    <ul class="divide-y divide-surface-100 dark:divide-surface-700/60">
                        <li v-for="(link, index) in latest_links" :key="link.id" class="flex items-center gap-3 py-3 first:pt-0">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl" :class="palette(index).tile">
                                <Link2 :size="18" />
                            </span>
                            <Link :href="route('links.edit', { link: link.id })" class="min-w-0 flex-1">
                                <span class="block truncate text-[13px] font-semibold text-surface-900 hover:text-primary-600 dark:text-white">{{ link.title }}</span>
                                <span class="block truncate text-xs text-blue-600 dark:text-blue-400">{{ link.url }}</span>
                                <span class="mt-0.5 flex items-center gap-1 text-[11px] whitespace-nowrap text-surface-400 dark:text-surface-500">
                                    <Calendar :size="11" /> {{ formatShortDate(link.created_at) }}
                                </span>
                            </Link>
                            <div class="flex shrink-0 flex-col items-end gap-1">
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-surface-500 tabular-nums dark:text-surface-400" :title="`${link.views_count} click`">
                                    <Eye :size="13" /> {{ formatCompact(link.views_count ?? 0) }}
                                </span>
                                <Sparkline :values="latest_sparklines.links[link.id] ?? []" :color="palette(index).color" :width="56" :height="20" />
                            </div>
                            <RowMenu
                                :items="[
                                    { label: 'Modifica', href: route('links.edit', { link: link.id }), icon: Pencil },
                                    { label: 'Analytics', href: route('links.analytics', { link: link.id }), icon: BarChart3 },
                                    { label: 'Apri destinazione', href: link.url, icon: ExternalLink, external: true },
                                ]"
                            />
                        </li>
                    </ul>
                </RecentCard>

                <!-- QR Code -->
                <RecentCard
                    title="Ultimi QR Code"
                    subtitle="I tuoi QR code più recenti e le loro performance."
                    :icon="QrCode"
                    tone="emerald"
                    :view-all-href="route('qrcodes.index')"
                    footer-label="Crea un nuovo QR Code"
                    :footer-href="route('qrcodes.create')"
                    :footer-icon="QrCode"
                    :empty="!latest_qrcodes.length"
                    empty-text="Nessun QR Code generato"
                >
                    <ul class="divide-y divide-surface-100 dark:divide-surface-700/60">
                        <li v-for="(qr, index) in latest_qrcodes" :key="qr.id" class="flex items-center gap-3 py-3 first:pt-0">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl" :class="palette(index + 1).tile">
                                <QrCode :size="18" />
                            </span>
                            <Link :href="route('qrcodes.edit', { qrcode: qr.slug })" class="min-w-0 flex-1">
                                <span class="block truncate text-[13px] font-semibold text-surface-900 hover:text-primary-600 dark:text-white">{{ qr.name || 'QR Code senza nome' }}</span>
                                <span class="mt-0.5 block truncate text-[11px] text-surface-400 dark:text-surface-500">Creato il {{ formatShortDate(qr.created_at) }}</span>
                            </Link>
                            <div class="flex shrink-0 flex-col items-end gap-1">
                                <span class="ml-badge tabular-nums" :class="palette(index + 1).tile">{{ formatCompact(qr.scans) }} scan</span>
                                <Sparkline :values="latest_sparklines.qrcodes[qr.id] ?? []" :color="palette(index + 1).color" :width="56" :height="20" />
                            </div>
                            <RowMenu
                                :items="[
                                    { label: 'Modifica', href: route('qrcodes.edit', { qrcode: qr.slug }), icon: Pencil },
                                    { label: 'Analytics', href: route('qrcodes.analytics', { qrcode: qr.slug }), icon: BarChart3 },
                                    { label: 'Visualizza', href: route('qrcodes.show', { qrcode: qr.slug }), icon: Eye },
                                ]"
                            />
                        </li>
                    </ul>
                </RecentCard>

                <!-- Pagine -->
                <RecentCard
                    title="Ultime pagine"
                    subtitle="Le tue pagine, sempre aggiornate."
                    :icon="FileText"
                    tone="violet"
                    :view-all-href="route('pages.index')"
                    footer-label="Gestisci tutte le pagine"
                    :footer-href="route('pages.index')"
                    :footer-icon="FileText"
                    :empty="!latest_pages.length"
                    empty-text="Nessuna pagina creata"
                >
                    <ul class="divide-y divide-surface-100 dark:divide-surface-700/60">
                        <li v-for="(item, index) in latest_pages" :key="item.id" class="flex items-center gap-3 py-3 first:pt-0">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl" :class="palette(index + 2).tile">
                                <FileText :size="18" />
                            </span>
                            <Link :href="route('pages.edit', { page: item.slug })" class="min-w-0 flex-1">
                                <span class="block truncate text-[13px] font-semibold text-surface-900 hover:text-primary-600 dark:text-white">{{ item.title }}</span>
                                <span class="block truncate text-xs text-violet-600 dark:text-violet-400">/{{ item.slug }} <span class="text-surface-400">· {{ formatNumber(item.views) }} visite</span></span>
                                <span class="mt-0.5 flex items-center gap-1 text-[11px] whitespace-nowrap text-surface-400 dark:text-surface-500">
                                    <Calendar :size="11" /> {{ formatShortDate(item.created_at) }}
                                </span>
                            </Link>
                            <span
                                class="ml-badge shrink-0"
                                :class="item.is_active
                                    ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300'
                                    : 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300'"
                            >
                                {{ item.is_active ? 'Attiva' : 'Bozza' }}
                            </span>
                            <RowMenu
                                :items="[
                                    { label: 'Modifica', href: route('pages.edit', { page: item.slug }), icon: Pencil },
                                    { label: 'Anteprima', href: route('pages.show', { page: item.slug }), icon: Eye },
                                    { label: 'Apri pagina pubblica', href: publicPageUrl(item.slug), icon: ExternalLink, external: true },
                                ]"
                            />
                        </li>
                    </ul>
                </RecentCard>

                <!-- Blocchi -->
                <RecentCard
                    title="Ultimi blocchi"
                    subtitle="I blocchi aggiunti di recente alle tue pagine."
                    :icon="Box"
                    tone="orange"
                    :empty="!latest_blocks.length"
                    empty-text="Nessun blocco creato"
                >
                    <ul class="divide-y divide-surface-100 dark:divide-surface-700/60">
                        <li v-for="block in latest_blocks" :key="block.id" class="flex items-center gap-3 py-3 first:pt-0">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl" :class="blockType(block.type).tile">
                                <component :is="blockType(block.type).icon" :size="18" />
                            </span>
                            <Link :href="route('pages.edit', { page: block.page.slug })" class="min-w-0 flex-1">
                                <span class="block truncate text-[13px] font-semibold text-surface-900 hover:text-primary-600 dark:text-white">{{ block.title || 'Blocco senza titolo' }}</span>
                                <span class="block truncate text-xs text-surface-500 dark:text-surface-400">Pagina: {{ block.page?.title }}</span>
                                <span class="mt-0.5 block text-[11px] text-surface-400 dark:text-surface-500">{{ formatShortDate(block.created_at) }}</span>
                            </Link>
                            <span class="ml-badge shrink-0" :class="blockType(block.type).badge">{{ block.type }}</span>
                            <RowMenu :items="[{ label: 'Modifica pagina', href: route('pages.edit', { page: block.page.slug }), icon: Pencil }]" />
                        </li>
                    </ul>
                </RecentCard>
            </div>

            <!-- Suggerimenti in fondo alla pagina sugli schermi più piccoli -->
            <TipsCarousel :short-url="shortUrl" class="3xl:hidden" />
          </div>

          <!-- Colonna laterale (solo schermi molto larghi) -->
          <aside class="hidden flex-col gap-5 3xl:flex">
              <ActivitySummary :period="period" :trends="trends" />
              <TopLinks :links="top_links" :period="period" />
          </aside>
        </div>
    </AppLayout>
</template>
