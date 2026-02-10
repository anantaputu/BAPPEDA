<script setup>
import { Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import { computed } from 'vue';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps({ 
    doughnutData: {
        type: Object,
        default: () => ({ labels: [], datasets: [] })
    }, 
    validationData: {
        type: Object,
        default: () => ({ total: 0 })
    }
});

// Helper untuk mengambil data Top 3 agar tampilan tidak berantakan
const top3Data = computed(() => {
    if (!props.doughnutData?.labels?.length) return [];
    
    const labels = props.doughnutData.labels;
    const values = props.doughnutData.datasets[0].data;
    const colors = props.doughnutData.datasets[0].backgroundColor;

    // Map menjadi object agar mudah di-loop
    const mapped = labels.map((label, index) => ({
        label: label,
        value: values[index],
        color: colors[index]
    }));

    // Urutkan dari nilai terbesar, ambil 3 teratas
    return mapped.sort((a, b) => b.value - a.value).slice(0, 3);
});
</script>

<template>
    <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-gray-100 flex flex-col h-full">
        <h3 class="text-lg font-bold text-gray-800 tracking-tight mb-6">Proporsi Bidang</h3>
        
        <div class="flex-1 relative flex items-center justify-center px-2 min-h-[200px]">
            <Doughnut :data="doughnutData" :options="{ responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }" />
            
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <span class="text-3xl font-black text-[#00139E]">{{ validationData.total }}</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase">Total Data</span>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-50">
            <div class="flex justify-around items-start">
                <div v-for="(item, index) in top3Data" :key="index" class="text-center flex flex-col items-center">
                    <span class="text-lg font-black" :style="{ color: item.color }">{{ item.value }}</span>
                    <span class="text-[9px] font-bold text-gray-400 uppercase max-w-[60px] truncate" :title="item.label">
                        {{ item.label }}
                    </span>
                </div>
            </div>
            
            <div v-if="doughnutData?.labels?.length > 3" class="text-center mt-2">
                <span class="text-[9px] text-gray-300 italic">+{{ doughnutData.labels.length - 3 }} bidang lainnya</span>
            </div>
        </div>

        <div class="pb-6"></div>
    </div>
</template>