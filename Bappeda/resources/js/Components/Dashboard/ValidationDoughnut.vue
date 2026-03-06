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

// Options terenkapsulasi di dalam komponen
const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    cutout: '75%', // Membuat lubang tengah lebih besar untuk teks
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: '#1F3A63', // primary
            titleFont: { weight: 'bold' },
            bodyFont: { weight: 'bold' },
            padding: 12,
            cornerRadius: 8
        }
    }
}));

const top3Data = computed(() => {
    if (!props.doughnutData?.labels?.length) return [];
    
    const labels = props.doughnutData.labels;
    const values = props.doughnutData.datasets[0].data;
    const colors = props.doughnutData.datasets[0].backgroundColor;

    const mapped = labels.map((label, index) => ({
        label: label,
        value: values[index],
        color: colors[index]
    }));

    return mapped.sort((a, b) => b.value - a.value).slice(0, 3);
});
</script>

<template>
    <div class="bg-white p-8 rounded-xl border border-gray-400 shadow-sm flex flex-col h-full group">
        <h3 class="text-sm font-black text-primary uppercase tracking-[0.2em] border-l-4 border-secondary pl-4 mb-8">
            Proporsi Bidang
        </h3>
        
        <div class="flex-1 relative flex items-center justify-center px-2 min-h-[220px]">
            <Doughnut :data="doughnutData" :options="chartOptions" />
            
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none transition-transform group-hover:scale-110 duration-500">
                <span class="text-4xl font-black text-primary leading-none">{{ validationData.total }}</span>
                <span class="text-[10px] font-black text-textsecondary uppercase tracking-widest mt-1">Total Data</span>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-bgsoft">
            <div class="flex justify-around items-start">
                <div v-for="(item, index) in top3Data" :key="index" class="text-center flex flex-col items-center px-1">
                    <span class="text-xl font-black mb-1" :style="{ color: item.color }">
                        {{ item.value }}
                    </span>
                    <span class="text-[9px] font-black text-textsecondary uppercase leading-tight tracking-tighter text-center line-clamp-2 w-full" :title="item.label">
                        {{ item.label }}
                    </span>
                </div>
            </div>
            
            <div v-if="doughnutData?.labels?.length > 3" class="text-center mt-4">
                <span class="text-[10px] font-bold text-textsecondary/40 uppercase tracking-widest">
                    +{{ doughnutData.labels.length - 3 }} Bidang Lainnya
                </span>
            </div>
        </div>
    </div>
</template>