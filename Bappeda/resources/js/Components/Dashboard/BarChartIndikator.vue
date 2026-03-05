<script setup>
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

defineProps({ chartData: Object, chartOptions: Object });
</script>

<template>
    <div class="lg:col-span-2 bg-white p-8 rounded-xl border border-gray-400">
        <div class="flex justify-between items-center mb-8">
        </div>
        <div class="h-[350px] w-full">
            <Bar :data="chartData" :options="chartOptions" />
        </div>
    </div>
</template>