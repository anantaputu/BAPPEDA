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
import { computed } from 'vue';

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

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false
        },
        tooltip: {
            backgroundColor: '#1F3A63',
            titleFont: { size: 14, weight: 'bold' },
            bodyFont: { size: 13, weight: 'bold' },
            padding: 12,
            displayColors: false,
            cornerRadius: 8,
            callbacks: {
                label: (context) => ` ${context.raw} Data Baru`
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: {
                display: false,
                color: '#EEF2F5',
                lineWidth: 2,
                drawBorder: false
            },
            ticks: {
                stepSize: 1,
                font: { 
                    size: 11,
                    weight: 'bold'
                },
                color: '#4B5563'
            }
        },
        x: {
            grid: {
                display: false,
                color: '#EEF2F5',
                lineWidth: 2,
                drawBorder: false
            },
            ticks: {
                font: { 
                    size: 11,
                    weight: 'bold'
                },
                color: '#4B5563'
            }
        }
    }
}));
</script>

<template>
    <div class="w-full h-full">
        <Line :data="chartData" :options="chartOptions" />
    </div>
</template>