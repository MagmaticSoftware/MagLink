<script setup lang="ts">
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  type ChartData,
  type ChartOptions
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

interface PerformanceData {
  date: string;
  link_views: number;
  qrcode_views: number;
  total_items: number;
}

const props = defineProps<{
  data: PerformanceData[];
}>();

const chartData = computed<ChartData<'bar'>>(() => ({
  labels: props.data.map(d => {
    const date = new Date(d.date);
    return date.toLocaleDateString('it-IT', { day: 'numeric', month: 'short' });
  }),
  datasets: [
    {
      label: 'Click sui link',
      backgroundColor: 'rgba(59, 130, 246, 0.8)',
      hoverBackgroundColor: 'rgba(37, 99, 235, 0.9)',
      data: props.data.map(d => d.link_views),
      borderRadius: 6,
    },
    {
      label: 'Scansioni QR',
      backgroundColor: 'rgba(16, 185, 129, 0.8)',
      hoverBackgroundColor: 'rgba(5, 150, 105, 0.9)',
      data: props.data.map(d => d.qrcode_views),
      borderRadius: 6,
    }
  ]
}));

const chartOptions = computed<ChartOptions<'bar'>>(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'bottom'
    },
    tooltip: {
      mode: 'index',
      intersect: false
    }
  },
  scales: {
    y: {
      stacked: true,
      beginAtZero: true,
      ticks: {
        precision: 0
      },
      grid: {
        color: 'rgba(0, 0, 0, 0.05)'
      }
    },
    x: {
      stacked: true,
      grid: {
        display: false
      }
    }
  }
}));
</script>

<template>
  <div class="h-56 w-full">
    <Bar :data="chartData" :options="chartOptions" />
  </div>
</template>
