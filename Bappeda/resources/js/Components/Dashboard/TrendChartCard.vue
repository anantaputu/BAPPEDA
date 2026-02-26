<script setup>
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement } from 'chart.js';
import { computed } from 'vue';

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement);

const props = defineProps({
    title: String,
    labels: Array, // Tahun (2020, 2021, ...)
    values: Array  // Nilai dari data_values
});

const chartData = computed(() => ({
    labels: props.labels,
    datasets: [{
        label: props.title,
        data: props.values,
        borderColor: '#4A6CF7',
        backgroundColor: 'rgba(74, 108, 247, 0.1)',
        fill: true,
        tension: 0.4,
        pointRadius: 4,
        pointBackgroundColor: '#000B58'
    }]
}));

const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
        x: { grid: { display: false } }
    }
};
</script>

<template>
    <div class="bg-white rounded-[3rem] border border-gray-400 p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-sm font-black text-[#000B58] uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-5 bg-[#00139E] rounded-full"></span>
                Analisis Tren Tahunan
            </h3>
        </div>
        <div class="h-[300px]">
            <Line :data="chartData" :options="options" />
        </div>
    </div>
</template>