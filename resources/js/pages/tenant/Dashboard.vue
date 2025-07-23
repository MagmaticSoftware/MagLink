<script setup lang="ts">

import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Link2, QrCode, Eye, Globe, BarChart2, Clock, Zap, Gem, Bell, Plus, AlertTriangle, Box } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Fake data for dashboard blocks
const recentLogins = [
    { id: 1, date: '2025-07-21 10:12', ip: '192.168.1.10' },
    { id: 2, date: '2025-07-20 18:45', ip: '192.168.1.11' },
    { id: 3, date: '2025-07-19 09:30', ip: '192.168.1.12' },
];
const latestLinks = [
    { id: 1, title: 'Google', url: 'https://google.com' },
    { id: 2, title: 'GitHub', url: 'https://github.com' },
    { id: 3, title: 'MagLink', url: 'https://maglink.app' },
];
const latestQRCodes = [
    { id: 1, label: 'QR Google' },
    { id: 2, label: 'QR GitHub' },
    { id: 3, label: 'QR MagLink' },
];
const latestBlocks = [
    { id: 1, type: 'html', position: '0-0' },
    { id: 2, type: 'image', position: '1-0' },
    { id: 3, type: 'link', position: '0-1' },
];
</script>


<template>

    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 rounded-2xl p-0 bg-[url('/grid.svg')] bg-repeat bg-[length:32px_32px] bg-[#f6f7fb] dark:bg-[#181a20]">
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
                                    class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white mb-1 tracking-tight">
                                    Benvenuto su <span
                                        class="text-primary-600 dark:text-primary-400 drop-shadow">MagLink</span></h1>
                                <p class="text-gray-500 dark:text-gray-300 text-base md:text-lg">Gestisci i tuoi link,
                                    QR Code e pagine in modo semplice e moderno.</p>
                            </div>
                            <div class="flex flex-col gap-2 min-w-[180px]">
                                <Link :href="route('links.create')"
                                    class="rounded-full bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-200 px-5 py-2 font-semibold shadow hover:bg-primary-200 dark:hover:bg-primary-800 transition flex items-center gap-2">
                                <Plus class="w-4 h-4" /> Nuovo Link
                                </Link>
                                <Link :href="route('qrcodes.create')"
                                    class="rounded-full border border-gray-300 dark:border-gray-700 bg-white/80 dark:bg-gray-800 text-gray-700 dark:text-gray-200 px-5 py-2 font-semibold shadow hover:bg-gray-100 dark:hover:bg-gray-700 transition flex items-center gap-2">
                                <QrCode class="w-4 h-4" /> Nuovo QR Code
                                </Link>
                            </div>
                        </div>
                    </div>
                    <!-- Stato piano -->
                    <div
                        class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col gap-4 border border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-2 font-semibold mb-3 text-lg">
                            <Gem class="w-5 h-5 text-yellow-500" /> Stato piano
                        </div>
                        <p class="text-base mb-2 text-gray-700 dark:text-gray-300">
                            Piano attuale: <strong class="text-green-600 dark:text-green-400">Free</strong><br>
                            Link disponibili: <span class="font-semibold">3 / 10</span><br>
                            QR Code disponibili: <span class="font-semibold">5 / 10</span>
                        </p>
                        <button
                            class="rounded-full border border-yellow-400 dark:border-yellow-400 text-yellow-700 dark:text-yellow-300 bg-yellow-50 dark:bg-yellow-900 px-5 py-2 font-semibold shadow hover:bg-yellow-100 dark:hover:bg-yellow-800 transition w-full">
                            <Zap class="w-4 h-4" /> Passa al piano PRO
                        </button>
                    </div>
                    <!-- Notifiche importanti -->
                    <div
                        class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col gap-4 border border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-2 font-semibold mb-3 text-lg">
                            <Bell class="w-5 h-5 text-red-500" /> Notifiche importanti
                        </div>
                        <ul class="text-base space-y-2 text-red-600 dark:text-red-400">
                            <li class="flex items-center gap-2">
                                <AlertTriangle class="w-4 h-4" /> Hai quasi raggiunto il limite massimo di link
                            </li>
                            <li class="flex items-center gap-2">
                                <AlertTriangle class="w-4 h-4" /> Mancano 3 giorni al rinnovo
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Statistiche principali -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-4">
                  <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col items-center relative overflow-hidden group border border-gray-200 dark:border-gray-800">
                    <div class="absolute right-4 top-4">
                      <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900">
                        <Link2 class="w-6 h-6 text-blue-600 dark:text-blue-300" />
                      </span>
                    </div>
                    <div class="text-4xl font-extrabold text-blue-700 dark:text-blue-400 mb-1">123</div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Link creati</div>
                  </div>
                  <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col items-center relative overflow-hidden group border border-gray-200 dark:border-gray-800">
                    <div class="absolute right-4 top-4">
                      <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-green-100 dark:bg-green-900">
                        <QrCode class="w-6 h-6 text-green-600 dark:text-green-300" />
                      </span>
                    </div>
                    <div class="text-4xl font-extrabold text-green-700 dark:text-green-400 mb-1">45</div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">QR Code generati</div>
                  </div>
                  <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col items-center relative overflow-hidden group border border-gray-200 dark:border-gray-800">
                    <div class="absolute right-4 top-4">
                      <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-purple-100 dark:bg-purple-900">
                        <Eye class="w-6 h-6 text-purple-600 dark:text-purple-300" />
                      </span>
                    </div>
                    <div class="text-4xl font-extrabold text-purple-700 dark:text-purple-400 mb-1">789</div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Visitatori unici</div>
                  </div>
                  <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col items-center relative overflow-hidden group border border-gray-200 dark:border-gray-800">
                    <div class="absolute right-4 top-4">
                      <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-emerald-100 dark:bg-emerald-900">
                        <Globe class="w-6 h-6 text-emerald-600 dark:text-emerald-300" />
                      </span>
                    </div>
                    <div class="text-2xl font-bold text-green-700 dark:text-green-400 mb-1 flex items-center gap-2">
                      <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse inline-block"></span>Online
                    </div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Pagina pubblica</div>
                  </div>
                </div>
                <!-- Performance recenti -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div
                        class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col border border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-2 font-semibold mb-4 text-lg">
                            <BarChart2 class="w-5 h-5 text-blue-500" /> Performance ultimi 7 giorni
                            <span
                                class="ml-auto bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 text-xs font-bold px-2 py-0.5 rounded-full">Demo</span>
                        </div>
                        <!-- Placeholder grafico -->
                        <div
                            class="h-40 w-full bg-gradient-to-r from-blue-100/60 to-indigo-100/60 dark:from-gray-800 dark:to-gray-700 rounded-2xl flex items-end gap-2 px-4">
                            <div class="h-2/3 w-1/6 bg-blue-500/80 rounded-t-lg"></div>
                            <div class="h-1/2 w-1/6 bg-blue-400/80 rounded-t-lg"></div>
                            <div class="h-3/4 w-1/6 bg-blue-600/80 rounded-t-lg"></div>
                            <div class="h-1/3 w-1/6 bg-blue-300/80 rounded-t-lg"></div>
                            <div class="h-2/3 w-1/6 bg-blue-500/80 rounded-t-lg"></div>
                            <div class="h-1/2 w-1/6 bg-blue-400/80 rounded-t-lg"></div>
                        </div>
                    </div>
                    <div
                        class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col border border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-2 font-semibold mb-4 text-lg">
                            <Clock class="w-5 h-5 text-green-500" /> Accessi recenti
                        </div>
                        <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                            <li v-for="log in recentLogins" :key="log.id"
                                class="py-3 text-base text-gray-600 dark:text-gray-300 flex items-center gap-3">
                                <span class="font-mono text-xs text-gray-400 dark:text-gray-500">{{ log.date }}</span>
                                <span class="inline-block w-2 h-2 rounded-full bg-green-400 ml-1"></span>
                                <span>{{ log.ip }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Ultimi contenuti -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <div
                        class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col gap-3 border border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-2 font-semibold mb-3 text-lg">
                            <Link2 class="w-5 h-5 text-blue-500" /> Ultimi link
                        </div>
                        <ul class="space-y-3 text-base">
                            <li v-for="link in latestLinks" :key="link.id" class="truncate flex items-center gap-3">
                                <span
                                    class="bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 px-3 py-1 rounded-full text-xs font-bold">LINK</span>
                                <a :href="link.url" target="_blank"
                                    class="text-blue-700 dark:text-blue-300 hover:underline font-medium">{{ link.title
                                    }}</a>
                            </li>
                        </ul>
                    </div>
                    <div
                        class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col gap-3 border border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-2 font-semibold mb-3 text-lg">
                            <QrCode class="w-5 h-5 text-green-500" /> Ultimi QR Code
                        </div>
                        <ul class="space-y-3 text-base">
                            <li v-for="qr in latestQRCodes" :key="qr.id" class="truncate flex items-center gap-3">
                                <span
                                    class="bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 px-3 py-1 rounded-full text-xs font-bold">QR</span>
                                <span>{{ qr.label || 'QR senza nome' }}</span>
                            </li>
                        </ul>
                    </div>
                    <div
                        class="rounded-xl bg-white dark:bg-gray-900 shadow-sm p-7 flex flex-col gap-3 border border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-2 font-semibold mb-3 text-lg">
                            <Box class="w-5 h-5 text-purple-500" /> Ultimi blocchi pagina
                        </div>
                        <ul class="space-y-3 text-base">
                            <li v-for="block in latestBlocks" :key="block.id" class="flex items-center gap-3">
                                <span
                                    class="bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300 px-3 py-1 rounded-full text-xs font-bold">BLOCK</span>
                                <span>{{ block.type }}</span>
                                <span class="text-xs text-gray-400 ml-auto">pos: {{ block.position }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ...existing code... -->
        </div>
    </AppLayout>
</template>
