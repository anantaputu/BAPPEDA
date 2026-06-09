<script setup>
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';
import { computed } from 'vue';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({ 
    chartData: Object, 
    title: String,
    xAxisLabel: {
        type: String,
        default: 'Kategori'
    },
    yAxisLabel: {
        type: String,
        default: 'Jumlah Data'
    }
});

const defaultOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            backgroundColor: '#1F3A63', // primary
            titleFont: { weight: 'bold', size: 13 },
            bodyFont: { weight: 'bold', size: 12 },
            cornerRadius: 10,
            padding: 12,
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            title: {
                display: true,
                text: props.yAxisLabel,
                color: '#1F3A63',
                font: { weight: 'bold', size: 12 }
            },
            ticks: {
                stepSize: 1,
                font: { weight: 'bold', size: 12 },
                color: '#4B5563' // textsecondary
            },
            grid: {
                display: false,
                lineWidth: 2, // Garis grid tebal
                color: '#EEF2F5', // bgsoft
                drawBorder: false
            }
        },
        x: {
            title: {
                display: true,
                text: props.xAxisLabel,
                color: '#1F3A63',
                font: { weight: 'bold', size: 12 }
            },
            ticks: {
                font: { weight: 'bold', size: 11 },
                color: '#4B5563' // textsecondary
            },
            grid: {
                display: false,
                lineWidth: 2, // Garis grid tebal
                color: '#EEF2F5', // bgsoft
                drawBorder: false
            }
        }
    }
}));
</script>

<template>
    <div class="ui-panel flex flex-col p-8 border border-gray-400 bg-white" style="border-radius: var(--radius-panel);">
        <div v-if="title" class="flex justify-between items-center mb-8">
            <h3 class="ui-eyebrow border-l-4 border-secondary pl-4">
                {{ title }}
            </h3>
        </div>
        
        <div class="h-[350px]">
            <Bar :data="chartData" :options="defaultOptions" />
        </div>
    </div>
</template>
