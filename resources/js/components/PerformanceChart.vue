<script setup lang="ts">
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import { useDark } from '@vueuse/core';
import {
    Chart as ChartJS,
    Tooltip,
    Filler,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    type ChartData,
    type ChartOptions,
    type ScriptableContext,
} from 'chart.js';
import { SERIES_COLORS } from '@/components/dashboard/colors';

ChartJS.register(Tooltip, Filler, LineElement, PointElement, CategoryScale, LinearScale);

interface PerformanceData {
    date: string;
    link_views: number;
    qrcode_views: number;
    total_items: number;
}

const props = defineProps<{
    data: PerformanceData[];
}>();

// Stesse opzioni di AppTopBar, così il grafico segue il toggle del tema
const isDark = useDark({
    selector: 'html',
    attribute: 'class',
    valueDark: 'dark',
    valueLight: 'light',
});

// Riempimento sfumato sotto la linea, calcolato sull'area reale del grafico
const gradientFill = (color: string) => (context: ScriptableContext<'line'>) => {
    const { ctx, chartArea } = context.chart;
    if (!chartArea) return 'transparent';
    const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
    gradient.addColorStop(0, `${color}38`);
    gradient.addColorStop(1, `${color}00`);
    return gradient;
};

const dataset = (label: string, color: string, values: number[]) => ({
    label,
    data: values,
    borderColor: color,
    backgroundColor: gradientFill(color),
    borderWidth: 2,
    fill: true,
    tension: 0.4,
    pointRadius: 0,
    pointHoverRadius: 4,
    pointHoverBorderWidth: 2,
    pointHoverBackgroundColor: isDark.value ? '#18181b' : '#ffffff',
    pointHoverBorderColor: color,
});

const chartData = computed<ChartData<'line'>>(() => ({
    labels: props.data.map((d) => new Date(d.date).toLocaleDateString('it-IT', { day: 'numeric', month: 'short' })),
    datasets: [
        dataset('Click sui link', SERIES_COLORS.links, props.data.map((d) => d.link_views)),
        dataset('Scansioni QR', SERIES_COLORS.qrcodes, props.data.map((d) => d.qrcode_views)),
    ],
}));

const chartOptions = computed<ChartOptions<'line'>>(() => {
    const tickColor = isDark.value ? '#a1a1aa' : '#71717a';
    const gridColor = isDark.value ? 'rgba(255, 255, 255, 0.06)' : 'rgba(24, 24, 27, 0.06)';

    return {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: isDark.value ? '#27272a' : '#ffffff',
                titleColor: isDark.value ? '#fafafa' : '#18181b',
                bodyColor: isDark.value ? '#d4d4d8' : '#3f3f46',
                borderColor: isDark.value ? '#3f3f46' : '#e4e4e7',
                borderWidth: 1,
                padding: 10,
                cornerRadius: 8,
                boxPadding: 4,
                usePointStyle: true,
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                border: { display: false },
                ticks: { precision: 0, color: tickColor, font: { size: 11 }, maxTicksLimit: 5 },
                grid: { color: gridColor },
            },
            x: {
                border: { display: false },
                ticks: { color: tickColor, font: { size: 11 }, maxRotation: 0, autoSkip: true, maxTicksLimit: 8 },
                grid: { display: false },
            },
        },
    };
});
</script>

<template>
    <div class="h-64 w-full">
        <Line :data="chartData" :options="chartOptions" />
    </div>
</template>
