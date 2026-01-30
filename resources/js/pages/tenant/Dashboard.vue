<script setup lang="ts">

import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Link2, QrCode, BarChart2, Zap, Gem, Plus, Box, TrendingUp, FileText } from 'lucide-vue-next';
import PlanLimitsCard from '@/components/PlanLimitsCard.vue';
import PerformanceChart from '@/components/PerformanceChart.vue';
import { useRoute } from '@/composables/useRoute';

const route = useRoute();

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

interface Link {
  id: number;
  title: string;
  url: string;
  created_at: string;
}

interface QrCodeType {
  id: number;
  name: string;
  scans: number;
  created_at: string;
}

interface Page {
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
  };
}

interface PerformanceData {
  date: string;
  total_items: number;
}

const props = defineProps<{
  stats: Stats;
  latest_links: Link[];
  latest_qrcodes: QrCodeType[];
  latest_pages: Page[];
  latest_blocks: Block[];
  performance: PerformanceData[];
}>();

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('it-IT', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatNumber = (num: number) => {
  return new Intl.NumberFormat('it-IT').format(num);
};

// Debug: verifica dati grafico
console.log('Performance data:', props.performance);
</script>


<template>

    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 rounded-2xl p-0">
            
            <!-- Welcome Banner nella griglia -->
            <div class="p-8 space-y-8">
                <!-- Welcome + Stato piano + Notifiche -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Welcome Banner: span 2 colonne -->
                    <div
                        class="lg:col-span-2 rounded-xl bg-primary-50 dark:bg-gray-900 shadow-sm p-7 flex flex-row items-center border border-primary-100 dark:border-gray-800 min-h-[170px]">
                        <div class="flex flex-col sm:flex-row items-center gap-6 w-full">
                            <div class="flex flex-col items-center justify-center mr-0 sm:mr-4">
                                <span class="text-5xl">👋</span>
                            </div>
                            <div class="flex-1 flex flex-col justify-center">
                                <h1
                                    class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-1 tracking-tight">
                                    Benvenuto su <span
                                        class="text-primary-600 dark:text-primary-400">MagLink</span></h1>
                                <p class="text-gray-500 dark:text-gray-300 text-base md:text-lg">Gestisci i tuoi link,
                                    QR Code e pagine in modo semplice e moderno.</p>
                            </div>
                            <div class="flex flex-col gap-2 min-w-[180px]">
                                <Link :href="route('links.create')"
                                    class="rounded-full bg-primary text-primary-contrast dark:bg-primary-900 dark:text-primary-200 px-5 py-2 font-semibold hover:bg-primary-700 dark:hover:bg-primary-800 transition flex items-center justify-center gap-2">
                                <Plus :size="16" /> Nuovo Link
                                </Link>
                                <Link :href="route('qrcodes.create')"
                                    class="rounded-full border border-gray-300 dark:border-gray-700 bg-white/80 dark:bg-gray-800 text-gray-700 dark:text-gray-200 px-5 py-2 font-semibold hover:bg-gray-100 dark:hover:bg-gray-700 transition flex items-center justify-center gap-2">
                                <QrCode :size="16" /> Nuovo QR Code
                                </Link>
                            </div>
                        </div>
                    </div>
                    <!-- Stato piano -->
                    <PlanLimitsCard :stats="stats" />
                    <!-- Riepilogo attività -->
                    <div
                        class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col gap-4 border border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-2 font-semibold mb-3 text-lg">
                            <TrendingUp class="w-5 h-5 text-indigo-500" /> Riepilogo attività
                        </div>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center justify-between p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <span class="text-gray-700 dark:text-gray-300">Link attivi</span>
                                <span class="font-bold text-blue-600 dark:text-blue-400">{{ stats.links_count }}</span>
                            </div>
                            <div class="flex items-center justify-between p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <span class="text-gray-700 dark:text-gray-300">QR Code generati</span>
                                <span class="font-bold text-green-600 dark:text-green-400">{{ stats.qrcodes_count }}</span>
                            </div>
                            <div class="flex items-center justify-between p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                <span class="text-gray-700 dark:text-gray-300">Pagine pubblicate</span>
                                <span class="font-bold text-purple-600 dark:text-purple-400">{{ stats.pages_count }}</span>
                            </div>
                            <div class="flex items-center justify-between p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                                <span class="text-gray-700 dark:text-gray-300">Visite totali</span>
                                <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ formatNumber(stats.total_views) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Statistiche principali -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mt-4">
                  <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col items-center relative overflow-hidden group border border-gray-200 dark:border-gray-800 hover:shadow-md transition-shadow">
                    <div class="absolute right-4 top-4">
                      <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900">
                        <Link2 class="w-6 h-6 text-blue-600 dark:text-blue-300" />
                      </span>
                    </div>
                    <div class="text-4xl font-extrabold text-blue-700 dark:text-blue-400 mb-1">{{ formatNumber(stats.links_count) }}</div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Link creati</div>
                  </div>
                  <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col items-center relative overflow-hidden group border border-gray-200 dark:border-gray-800 hover:shadow-md transition-shadow">
                    <div class="absolute right-4 top-4">
                      <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-green-100 dark:bg-green-900">
                        <QrCode class="w-6 h-6 text-green-600 dark:text-green-300" />
                      </span>
                    </div>
                    <div class="text-4xl font-extrabold text-green-700 dark:text-green-400 mb-1">{{ formatNumber(stats.qrcodes_count) }}</div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">QR Code generati</div>
                  </div>
                  <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col items-center relative overflow-hidden group border border-gray-200 dark:border-gray-800 hover:shadow-md transition-shadow">
                    <div class="absolute right-4 top-4">
                      <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-purple-100 dark:bg-purple-900">
                        <FileText class="w-6 h-6 text-purple-600 dark:text-purple-300" />
                      </span>
                    </div>
                    <div class="text-4xl font-extrabold text-purple-700 dark:text-purple-400 mb-1">{{ formatNumber(stats.pages_count) }}</div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Pagine create</div>
                  </div>
                  <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col items-center relative overflow-hidden group border border-gray-200 dark:border-gray-800 hover:shadow-md transition-shadow">
                    <div class="absolute right-4 top-4">
                      <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-orange-100 dark:bg-orange-900">
                        <Box class="w-6 h-6 text-orange-600 dark:text-orange-300" />
                      </span>
                    </div>
                    <div class="text-4xl font-extrabold text-orange-700 dark:text-orange-400 mb-1">{{ formatNumber(stats.blocks_count) }}</div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Blocchi totali</div>
                  </div>
                  <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col items-center relative overflow-hidden group border border-gray-200 dark:border-gray-800 hover:shadow-md transition-shadow">
                    <div class="absolute right-4 top-4">
                      <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900">
                        <TrendingUp class="w-6 h-6 text-indigo-600 dark:text-indigo-300" />
                      </span>
                    </div>
                    <div class="text-4xl font-extrabold text-indigo-700 dark:text-indigo-400 mb-1">{{ formatNumber(stats.total_views) }}</div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Visualizzazioni totali</div>
                  </div>
                </div>
                <!-- Performance recenti -->
                <div class="grid grid-cols-1 gap-6">
                    <div
                        class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col border border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-2 font-semibold mb-4 text-lg">
                            <BarChart2 class="w-5 h-5 text-blue-500" /> Attività ultimi 7 giorni (link creati)
                        </div>
                        <PerformanceChart v-if="performance.length > 0" :data="performance" />
                        <div v-else class="h-48 w-full flex items-center justify-center bg-gray-50 dark:bg-gray-800 rounded-2xl">
                            <p class="text-gray-500 dark:text-gray-400">Nessun dato disponibile per questo periodo</p>
                        </div>
                    </div>
                </div>
                <!-- Ultimi contenuti -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    <div
                        class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col gap-3 border border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-2 font-semibold mb-3 text-lg">
                            <Link2 class="w-5 h-5 text-blue-500" /> Ultimi link
                        </div>
                        <ul v-if="latest_links.length > 0" class="space-y-3 text-sm">
                            <li v-for="link in latest_links" :key="link.id" class="border-b border-gray-100 dark:border-gray-800 pb-2">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-semibold text-gray-900 dark:text-white truncate flex-1">{{ link.title }}</span>
                                </div>
                                <a :href="link.url" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline text-xs truncate block">{{ link.url }}</a>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ formatDate(link.created_at) }}</span>
                            </li>
                        </ul>
                        <p v-else class="text-gray-500 dark:text-gray-400 text-sm">Nessun link creato</p>
                    </div>
                    <div
                        class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col gap-3 border border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-2 font-semibold mb-3 text-lg">
                            <QrCode class="w-5 h-5 text-green-500" /> Ultimi QR Code
                        </div>
                        <ul v-if="latest_qrcodes.length > 0" class="space-y-3 text-sm">
                            <li v-for="qr in latest_qrcodes" :key="qr.id" class="border-b border-gray-100 dark:border-gray-800 pb-2">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-semibold text-gray-900 dark:text-white truncate flex-1">{{ qr.name || 'QR Code senza nome' }}</span>
                                    <span class="bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 px-2 py-0.5 rounded-full text-xs font-bold ml-2">{{ qr.scans }} scan</span>
                                </div>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ formatDate(qr.created_at) }}</span>
                            </li>
                        </ul>
                        <p v-else class="text-gray-500 dark:text-gray-400 text-sm">Nessun QR Code generato</p>
                    </div>
                    <div
                        class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col gap-3 border border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-2 font-semibold mb-3 text-lg">
                            <FileText class="w-5 h-5 text-purple-500" /> Ultime pagine
                        </div>
                        <ul v-if="latest_pages.length > 0" class="space-y-3 text-sm">
                            <li v-for="page in latest_pages" :key="page.id" class="border-b border-gray-100 dark:border-gray-800 pb-2">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-semibold text-gray-900 dark:text-white truncate flex-1">{{ page.title }}</span>
                                    <span v-if="page.is_active" class="bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 px-2 py-0.5 rounded-full text-xs font-bold ml-2">Attiva</span>
                                    <span v-else class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded-full text-xs font-bold ml-2">Inattiva</span>
                                </div>
                                <span class="text-xs text-blue-600 dark:text-blue-400">/{{ page.slug }}</span> · 
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ page.views }} visualizzazioni</span>
                                <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ formatDate(page.created_at) }}</div>
                            </li>
                        </ul>
                        <p v-else class="text-gray-500 dark:text-gray-400 text-sm">Nessuna pagina creata</p>
                    </div>
                    <div
                        class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col gap-3 border border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-2 font-semibold mb-3 text-lg">
                            <Box class="w-5 h-5 text-orange-500" /> Ultimi blocchi
                        </div>
                        <ul v-if="latest_blocks.length > 0" class="space-y-3 text-sm">
                            <li v-for="block in latest_blocks" :key="block.id" class="border-b border-gray-100 dark:border-gray-800 pb-2">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-semibold text-gray-900 dark:text-white truncate flex-1">{{ block.title || 'Blocco senza titolo' }}</span>
                                    <span class="bg-orange-100 dark:bg-orange-900 text-orange-600 dark:text-orange-300 px-2 py-0.5 rounded-full text-xs font-bold ml-2">{{ block.type }}</span>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Pagina: {{ block.page.title }}</span>
                                <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ formatDate(block.created_at) }}</div>
                            </li>
                        </ul>
                        <p v-else class="text-gray-500 dark:text-gray-400 text-sm">Nessun blocco creato</p>
                    </div>
                </div>
            </div>

            <!-- ...existing code... -->
        </div>
    </AppLayout>
</template>
