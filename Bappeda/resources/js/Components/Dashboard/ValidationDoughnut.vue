<script setup>
import { Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend);

defineProps({ doughnutData: Object, validationData: Object, percentValid: Number });
</script>

<template>
    <div class="bg-white p-8 rounded-[1.5rem] shadow-sm border border-gray-400 flex flex-col">
        <h3 class="text-lg font-bold text-gray-800 tracking-tight mb-8">Status Bidang</h3>
        <div class="flex-1 relative flex items-center justify-center px-4">
            <Doughnut :data="doughnutData" :options="{ responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }" />
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <span class="text-4xl font-black text-[#00139E]">{{ percentValid }}%</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase">Tervalidasi</span>
            </div>
        </div>
        <div class="flex justify-around mt-8 pt-6 border-t border-gray-50">
            <div class="text-center">
                <span class="block text-xl font-black text-[#00139E]">{{ validationData.valid }}</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase">Valid</span>
            </div>
            <div class="text-center">
                <span class="block text-xl font-black text-gray-400">{{ validationData.total - validationData.valid }}</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase">Draft</span>
            </div>
        </div>
    </div>
</template>