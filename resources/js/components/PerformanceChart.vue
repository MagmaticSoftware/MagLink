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
      label: 'Link creati',
      backgroundColor: 'rgba(59, 130, 246, 0.8)',
      hoverBackgroundColor: 'rgba(37, 99, 235, 0.9)',
      data: props.data.map(d => d.total_items),
      borderRadius: 8,
      borderSkipped: false,
    }
  ]
}));

const chartOptions = computed<ChartOptions<'bar'>>(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      callbacks: {
        label: (context) => {
          return `${context.parsed.y} link`;
        }
      }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
        precision: 0
      },
      grid: {
        color: 'rgba(0, 0, 0, 0.05)'
      }
    },
    x: {
      grid: {
        display: false
      }
    }
  }
}));
</script>

<template>
  <div class="h-48 w-full">
    <Bar :data="chartData" :options="chartOptions" />
  </div>
</template>
