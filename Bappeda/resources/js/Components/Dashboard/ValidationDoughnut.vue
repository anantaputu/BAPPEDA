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
    },
    categoryLabel: {
        type: String,
        default: 'Bidang'
    },
    valueLabel: {
        type: String,
        default: 'Jumlah Dataset'
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
    <div class="ui-panel flex h-full flex-col p-8 group border border-gray-400 bg-white" style="border-radius: var(--radius-panel);">
        <h3 class="ui-eyebrow mb-8 border-l-4 border-secondary pl-4">
            Proporsi Bidang
        </h3>
        
        <div class="flex-1 relative flex items-center justify-center px-2 min-h-[220px]">
            <Doughnut :data="doughnutData" :options="chartOptions" />
            
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none transition-transform group-hover:scale-110 duration-500">
                <span class="ui-title-md leading-none">{{ validationData.total }}</span>
                <span class="ui-eyebrow mt-2">Total Data</span>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-bgsoft">
            <div class="mb-4 flex items-center justify-center gap-6 text-[0.72rem] font-black uppercase tracking-[0.16em] text-textsecondary">
                <span>Kategori: {{ categoryLabel }}</span>
                <span>Nilai: {{ valueLabel }}</span>
            </div>
            <div class="flex justify-around items-start">
                <div v-for="(item, index) in top3Data" :key="index" class="text-center flex flex-col items-center px-1">
                    <span class="mb-1 text-[1.375rem] font-black leading-none" :style="{ color: item.color }">
                        {{ item.value }}
                    </span>
                    <span class="ui-eyebrow w-full text-center leading-tight line-clamp-2" :title="item.label">
                        {{ item.label }}
                    </span>
                </div>
            </div>
            
            <div v-if="doughnutData?.labels?.length > 3" class="text-center mt-4">
                <span class="ui-eyebrow text-textsecondary/50">
                    +{{ doughnutData.labels.length - 3 }} Bidang Lainnya
                </span>
            </div>
        </div>
    </div>
</template>
