<script setup>
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

defineProps({ chartData: Object, chartOptions: Object });
</script>

<template>
    <div class="lg:col-span-2 bg-white p-8 rounded-[1.5rem] shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-lg font-bold text-gray-800 tracking-tight">Indikator Per Tema</h3>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tahun 2025</span>
        </div>
        <div class="h-[300px]">
            <Bar :data="chartData" :options="chartOptions" />
        </div>
    </div>
</template>