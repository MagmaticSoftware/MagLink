<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { 
    LucideArrowLeft, 
    LucideEye, 
    LucideTrendingUp,
    LucideGlobe,
    LucideMonitor,
    LucideSmartphone,
    LucideTablet,
    LucideShield,
    LucideUsers,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { useRoute } from '@/composables/useRoute';
import { Bar, Doughnut } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    LineElement,
    PointElement,
    ArcElement,
    Title,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';
import Button from '@/components/volt/Button.vue';

// Registra componenti Chart.js
ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    LineElement,
    PointElement,
    ArcElement,
    Title,
    Tooltip,
    Legend,
    Filler
);

const route = useRoute();

const props = defineProps<{
    item: {
        id: number;
        title: string;
        slug: string;
        url: string;
        is_active: boolean;
        require_consent: boolean;
        created_at: string;
    };
    stats: {
        overview: {
            total: number;
            with_consent: number;
            anonymous: number;
            consent_percentage: number;
        };
        daily_views: Array<{
            date: string;
            total: number;
            with_consent: number;
            anonymous: number;
        }>;
        countries: Array<{ country: string; count: number }>;
        browsers: Array<{ browser: string; count: number }>;
        devices: Array<{ device_type: string; count: number }>;
        platforms: Array<{ platform: string; count: number }>;
        recent_views: Array<{
            id: number;
            country: string | null;
            city: string | null;
            browser: string | null;
            platform: string | null;
            device_type: string | null;
            created_at: string;
        }>;
    };
    type: 'link' | 'qrcode';
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Links',
        href: route('links.index') as string,
    },
    {
        title: props.item.title,
        href: route('links.edit', props.item.id) as string,
    },
    {
        title: 'Analytics',
        href: route('links.analytics', props.item.id) as string,
    },
];

// Daily views chart data
const dailyViewsChartData = computed(() => ({
    labels: props.stats.daily_views.map(d => new Date(d.date).toLocaleDateString('it-IT', { day: '2-digit', month: 'short' })),
    datasets: [
        {
            label: 'Con consenso',
            data: props.stats.daily_views.map(d => d.with_consent),
            backgroundColor: 'rgba(59, 130, 246, 0.7)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 2,
        },
        {
            label: 'Anonimi',
            data: props.stats.daily_views.map(d => d.anonymous),
            backgroundColor: 'rgba(156, 163, 175, 0.7)',
            borderColor: 'rgb(156, 163, 175)',
            borderWidth: 2,
        },
    ],
}));

const dailyViewsChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'top' as const,
        },
        tooltip: {
            mode: 'index' as const,
            intersect: false,
        },
    },
    scales: {
        x: {
            stacked: true,
        },
        y: {
            stacked: true,
            beginAtZero: true,
            ticks: {
                precision: 0,
            },
        },
    },
};

// Countries pie chart
const countriesChartData = computed(() => ({
    labels: props.stats.countries.map(c => c.country || 'Unknown'),
    datasets: [{
        data: props.stats.countries.map(c => c.count),
        backgroundColor: [
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(139, 92, 246, 0.8)',
            'rgba(236, 72, 153, 0.8)',
            'rgba(20, 184, 166, 0.8)',
            'rgba(251, 146, 60, 0.8)',
            'rgba(132, 204, 22, 0.8)',
            'rgba(168, 85, 247, 0.8)',
        ],
    }],
}));

const pieChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right' as const,
        },
    },
};

// Browsers chart
const browsersChartData = computed(() => ({
    labels: props.stats.browsers.map(b => b.browser || 'Unknown'),
    datasets: [{
        data: props.stats.browsers.map(b => b.count),
        backgroundColor: [
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(139, 92, 246, 0.8)',
            'rgba(236, 72, 153, 0.8)',
            'rgba(20, 184, 166, 0.8)',
            'rgba(251, 146, 60, 0.8)',
            'rgba(132, 204, 22, 0.8)',
            'rgba(168, 85, 247, 0.8)',
        ],
    }],
}));

// Devices chart
const devicesChartData = computed(() => ({
    labels: props.stats.devices.map(d => {
        const type = d.device_type || 'Unknown';
        return type.charAt(0).toUpperCase() + type.slice(1);
    }),
    datasets: [{
        data: props.stats.devices.map(d => d.count),
        backgroundColor: [
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(239, 68, 68, 0.8)',
        ],
    }],
}));

// Platforms chart
const platformsChartData = computed(() => ({
    labels: props.stats.platforms.map(p => p.platform || 'Unknown'),
    datasets: [{
        data: props.stats.platforms.map(p => p.count),
        backgroundColor: [
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(139, 92, 246, 0.8)',
            'rgba(236, 72, 153, 0.8)',
            'rgba(20, 184, 166, 0.8)',
            'rgba(251, 146, 60, 0.8)',
            'rgba(132, 204, 22, 0.8)',
            'rgba(168, 85, 247, 0.8)',
        ],
    }],
}));

const formatDate = (date: string) => {
    return new Date(date).toLocaleString('it-IT', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getDeviceIcon = (deviceType: string | null) => {
    if (!deviceType) return LucideMonitor;
    switch (deviceType.toLowerCase()) {
        case 'mobile':
            return LucideSmartphone;
        case 'tablet':
            return LucideTablet;
        default:
            return LucideMonitor;
    }
};
</script>

<template>
    <Head :title="`Analytics - ${item.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-surface-900 dark:text-surface-50">
                        Analytics: {{ item.title }}
                    </h1>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">
                        Statistiche dettagliate e insights
                    </p>
                </div>
                <Button 
                    variant="outline" 
                    :href="route('links.index')"
                    as="a"
                >
                    <LucideArrowLeft :size="16" class="mr-2" />
                    Indietro
                </Button>
            </div>

            <!-- Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-surface-600 dark:text-surface-400">Views Totali</span>
                        <LucideEye :size="20" class="text-primary" />
                    </div>
                    <div class="text-3xl font-bold text-surface-900 dark:text-surface-50">
                        {{ stats.overview.total }}
                    </div>
                </div>

                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-surface-600 dark:text-surface-400">Con Consenso</span>
                        <LucideShield :size="20" class="text-blue-600" />
                    </div>
                    <div class="text-3xl font-bold text-blue-600">
                        {{ stats.overview.with_consent }}
                    </div>
                    <div class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                        {{ stats.overview.consent_percentage }}% del totale
                    </div>
                </div>

                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-surface-600 dark:text-surface-400">Anonimi</span>
                        <LucideUsers :size="20" class="text-gray-500" />
                    </div>
                    <div class="text-3xl font-bold text-gray-500">
                        {{ stats.overview.anonymous }}
                    </div>
                    <div class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                        {{ (100 - stats.overview.consent_percentage).toFixed(1) }}% del totale
                    </div>
                </div>

                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-surface-600 dark:text-surface-400">Tasso Consenso</span>
                        <LucideTrendingUp :size="20" class="text-green-600" />
                    </div>
                    <div class="text-3xl font-bold text-green-600">
                        {{ stats.overview.consent_percentage }}%
                    </div>
                    <div class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                        Percentuale consenso
                    </div>
                </div>
            </div>

            <!-- Daily Views Chart -->
            <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                <h2 class="text-xl font-bold text-surface-900 dark:text-surface-50 mb-4">
                    Views Giornaliere (Ultimi 30 giorni)
                </h2>
                <div style="height: 350px">
                    <Bar :data="dailyViewsChartData" :options="dailyViewsChartOptions" />
                </div>
            </div>

            <!-- Detailed Stats Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Countries -->
                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <h2 class="text-xl font-bold text-surface-900 dark:text-surface-50 mb-4 flex items-center gap-2">
                        <LucideGlobe :size="20" class="text-primary" />
                        Paesi (solo dati con consenso)
                    </h2>
                    <div v-if="stats.countries.length > 0" style="height: 300px">
                        <Doughnut :data="countriesChartData" :options="pieChartOptions" />
                    </div>
                    <div v-else class="text-center py-12 text-surface-500 dark:text-surface-400">
                        Nessun dato disponibile
                    </div>
                </div>

                <!-- Devices -->
                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <h2 class="text-xl font-bold text-surface-900 dark:text-surface-50 mb-4 flex items-center gap-2">
                        <LucideMonitor :size="20" class="text-primary" />
                        Dispositivi
                    </h2>
                    <div v-if="stats.devices.length > 0" style="height: 300px">
                        <Doughnut :data="devicesChartData" :options="pieChartOptions" />
                    </div>
                    <div v-else class="text-center py-12 text-surface-500 dark:text-surface-400">
                        Nessun dato disponibile
                    </div>
                </div>

                <!-- Browsers -->
                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <h2 class="text-xl font-bold text-surface-900 dark:text-surface-50 mb-4">
                        Browser
                    </h2>
                    <div v-if="stats.browsers.length > 0" style="height: 300px">
                        <Doughnut :data="browsersChartData" :options="pieChartOptions" />
                    </div>
                    <div v-else class="text-center py-12 text-surface-500 dark:text-surface-400">
                        Nessun dato disponibile
                    </div>
                </div>

                <!-- Platforms -->
                <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                    <h2 class="text-xl font-bold text-surface-900 dark:text-surface-50 mb-4">
                        Sistemi Operativi
                    </h2>
                    <div v-if="stats.platforms.length > 0" style="height: 300px">
                        <Doughnut :data="platformsChartData" :options="pieChartOptions" />
                    </div>
                    <div v-else class="text-center py-12 text-surface-500 dark:text-surface-400">
                        Nessun dato disponibile
                    </div>
                </div>
            </div>

            <!-- Recent Views Table -->
            <div class="bg-white dark:bg-surface-900 rounded-xl p-6 shadow-sm border border-surface-200 dark:border-surface-800">
                <h2 class="text-xl font-bold text-surface-900 dark:text-surface-50 mb-4">
                    Visualizzazioni Recenti (con consenso)
                </h2>
                <div v-if="stats.recent_views.length > 0" class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-surface-200 dark:border-surface-800">
                                <th class="text-left py-3 px-4 text-sm font-semibold text-surface-700 dark:text-surface-300">Data</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-surface-700 dark:text-surface-300">Località</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-surface-700 dark:text-surface-300">Browser</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-surface-700 dark:text-surface-300">Sistema</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-surface-700 dark:text-surface-300">Dispositivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="view in stats.recent_views" 
                                :key="view.id"
                                class="border-b border-surface-100 dark:border-surface-800 hover:bg-surface-50 dark:hover:bg-surface-800/50"
                            >
                                <td class="py-3 px-4 text-sm text-surface-600 dark:text-surface-400">
                                    {{ formatDate(view.created_at) }}
                                </td>
                                <td class="py-3 px-4 text-sm text-surface-900 dark:text-surface-50">
                                    <span v-if="view.city && view.country">
                                        {{ view.city }}, {{ view.country }}
                                    </span>
                                    <span v-else-if="view.country">
                                        {{ view.country }}
                                    </span>
                                    <span v-else class="text-surface-400">
                                        N/A
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm text-surface-900 dark:text-surface-50">
                                    {{ view.browser || 'N/A' }}
                                </td>
                                <td class="py-3 px-4 text-sm text-surface-900 dark:text-surface-50">
                                    {{ view.platform || 'N/A' }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <component :is="getDeviceIcon(view.device_type)" :size="16" class="text-surface-500" />
                                        <span class="text-sm text-surface-900 dark:text-surface-50">
                                            {{ view.device_type ? view.device_type.charAt(0).toUpperCase() + view.device_type.slice(1) : 'N/A' }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="text-center py-12 text-surface-500 dark:text-surface-400">
                    Nessuna visualizzazione con consenso registrata
                </div>
            </div>

            <!-- GDPR Notice -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <LucideShield :size="20" class="text-blue-600 flex-shrink-0 mt-0.5" />
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">
                            Privacy e GDPR
                        </h3>
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            I dati dettagliati (paese, browser, dispositivo) vengono raccolti solo quando l'utente fornisce esplicito consenso.
                            Le {{ stats.overview.anonymous }} visualizzazioni anonime non includono informazioni personali o di tracciamento.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
