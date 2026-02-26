<script setup>
import { Line } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    CategoryScale,
    LinearScale,
    PointElement,
    Filler
} from 'chart.js';

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    LineElement,
    CategoryScale,
    LinearScale,
    PointElement,
    Filler
);

const props = defineProps({
    chartData: {
        type: Object,
        required: true
    }
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false // Kita sembunyikan legend agar lebih clean
        },
        tooltip: {
            backgroundColor: '#000B58',
            titleFont: { size: 14 },
            bodyFont: { size: 13 },
            padding: 12,
            displayColors: false,
            callbacks: {
                label: (context) => ` ${context.raw} Dataset Baru`
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: {
                color: 'rgba(0, 0, 0, 0.05)',
                drawBorder: false
            },
            ticks: {
                stepSize: 1,
                font: { size: 11 }
            }
        },
        x: {
            grid: {
                display: false
            },
            ticks: {
                font: { size: 11 }
            }
        }
    }
};
</script>

<template>
    <div class="w-full h-full">
        <Line :data="chartData" :options="chartOptions" />
    </div>
</template>