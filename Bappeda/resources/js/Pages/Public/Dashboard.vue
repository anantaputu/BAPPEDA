<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Bar, Doughnut } from 'vue-chartjs';
import { 
    Chart as ChartJS, Title, Tooltip, Legend, 
    BarElement, CategoryScale, LinearScale, ArcElement 
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

defineOptions({ layout: AppLayout });

const page = usePage();
const user = computed(() => page.props.auth.user);

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_indikator: 0,
            data_valid: 0,
            total_tema: 0,
            total_urusan: 0,
            last_update: '-'
        })
    },
    temaChart: {
        type: Object,
        default: () => ({ labels: [], values: [] })
    }
});

// Summary Cards dengan Palet Warna Baru
const summaryCards = computed(() => [
    { label: 'Total Indikator', value: props.stats?.total_indikator || 0, color: 'text-[#00139E]', bg: 'bg-[#00139E]/10' },
    { label: 'Data Valid', value: props.stats?.data_valid || 0, color: 'text-green-600', bg: 'bg-green-50' },
    { label: 'Total Tema', value: props.stats?.total_tema || 0, color: 'text-[#000B58]', bg: 'bg-[#000B58]/5' },
    { label: 'Total Urusan', value: props.stats?.total_urusan || 0, color: 'text-[#A2B5CB]', bg: 'bg-[#A2B5CB]/10' },
]);

const chartData = computed(() => ({
    labels: props.temaChart?.labels || [],
    datasets: [{
        label: 'Jumlah Indikator',
        backgroundColor: '#00139E', // Secondary Color
        hoverBackgroundColor: '#000B58', // Primary Color saat hover
        borderRadius: 12,
        data: props.temaChart?.values || []
    }]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: { color: '#F1F5F9' },
            ticks: { font: { weight: 'bold' }, color: '#A2B5CB' }
        },
        x: {
            grid: { display: false },
            ticks: { font: { weight: 'bold' }, color: '#A2B5CB' }
        }
    }
};
</script>

<template>
    <Head title="Executive Dashboard" />

    <div class="space-y-8 max-w-[80%] mx-auto py-4">
        <div class="bg-[#000B58] p-12 rounded-[3rem] text-white shadow-2xl relative overflow-hidden">
            <div class="relative z-10">
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-[#A2B5CB] mb-4 block">Overview Strategis</span>
                <h1 class="text-5xl font-black mb-2 tracking-tight">Executive Overview</h1>
                <p class="text-[#A2B5CB] font-medium text-lg">Monitoring Data Pembangunan BAPPEDA Provinsi NTB</p>
            </div>
            <div class="absolute top-0 right-0 w-80 h-80 bg-[#00139E]/20 rounded-full -mr-20 -mt-20 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-[#A2B5CB]/10 rounded-full -ml-10 -mb-10 blur-2xl"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div v-for="card in summaryCards" :key="card.label" 
                 class="bg-white p-8 rounded-[2.5rem] border border-[#A2B5CB]/20 shadow-sm hover:shadow-xl transition-all duration-300">
                <p class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-widest mb-3">{{ card.label }}</p>
                <h2 class="text-4xl font-black text-[#000B58]">{{ card.value }}</h2>
                <div :class="[card.bg, 'h-2 w-full mt-6 rounded-full overflow-hidden']">
                    <div :class="[card.color.replace('text', 'bg'), 'h-full w-2/3 rounded-full']"></div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 bg-white p-10 rounded-[3rem] border border-[#A2B5CB]/20 shadow-sm h-[500px]">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="font-black text-[#000B58] text-xl tracking-tight">Indikator Per Tema</h3>
                    <span class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-widest">Data Real-time</span>
                </div>
                <div class="h-[340px] px-2">
                    <Bar :data="chartData" :options="chartOptions" />
                </div>
            </div>

            <div class="bg-white p-10 rounded-[3rem] border border-[#A2B5CB]/20 shadow-sm flex flex-col items-center justify-center">
                <h3 class="font-black text-[#000B58] text-xl mb-10 tracking-tight">Status Validasi</h3>
                <div class="w-full h-64 px-4 relative"> 
                    <Doughnut :data="{
                            labels: ['Valid', 'Pending'],
                            datasets: [{
                                data: [stats.data_valid, stats.total_indikator - stats.data_valid],
                                backgroundColor: ['#00139E', '#F8FAFC'],
                                hoverBackgroundColor: ['#000B58', '#F1F5F9'],
                                borderWidth: 0,
                                cutout: '75%'
                            }]
                        }" :options="{ ...chartOptions, plugins: { legend: { display: false } } }" />
                    <div class="absolute inset-0 flex flex-col items-center justify-center pt-8">
                        <p class="text-4xl font-black text-[#00139E]">
                            {{ stats.total_indikator > 0 ? Math.round((stats.data_valid / stats.total_indikator) * 100) : 0 }}%
                        </p>
                        <p class="text-[9px] font-black text-[#A2B5CB] uppercase tracking-tighter">Verified</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 w-full mt-10 pt-6 border-t border-gray-50">
                    <div class="text-center">
                        <p class="text-sm font-black text-[#00139E]">{{ stats.data_valid }}</p>
                        <p class="text-[9px] font-bold text-[#A2B5CB] uppercase">Valid</p>
                    </div>
                    <div class="text-center border-l border-gray-100">
                        <p class="text-sm font-black text-[#FF1414]">{{ stats.total_indikator - stats.data_valid }}</p>
                        <p class="text-[9px] font-bold text-[#A2B5CB] uppercase">Pending</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>