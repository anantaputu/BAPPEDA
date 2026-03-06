<script setup>
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';
import { computed } from 'vue';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({ 
    chartData: Object, 
    title: String
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
            titleFont: { weight: 'bold' },
            bodyFont: { weight: 'bold' },
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1,
                font: { weight: 'bold', size: 11 },
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
            ticks: {
                font: { weight: 'bold', size: 10 },
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
    <div class="bg-white p-8 rounded-xl border border-gray-400 shadow-sm flex flex-col">
        <div v-if="title" class="flex justify-between items-center mb-8">
            <h3 class="text-sm font-black text-primary uppercase tracking-[0.2em] border-l-4 border-secondary pl-4">
                {{ title }}
            </h3>
        </div>
        
        <div class="h-[350px]">
            <Bar :data="chartData" :options="defaultOptions" />
        </div>
    </div>
</template>