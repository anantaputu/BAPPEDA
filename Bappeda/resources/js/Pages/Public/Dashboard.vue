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

// Sesuaikan props dengan data yang dikirim DashboardController.php
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

// Perbaikan: Pastikan properti stats sudah ada sebelum dibaca
const summaryCards = computed(() => [
    { label: 'Total Indikator', value: props.stats?.total_indikator || 0, color: 'text-indigo-600', bg: 'bg-indigo-50' },
    { label: 'Data Valid', value: props.stats?.data_valid || 0, color: 'text-emerald-600', bg: 'bg-emerald-50' },
    { label: 'Total Tema', value: props.stats?.total_tema || 0, color: 'text-amber-600', bg: 'bg-amber-50' },
    { label: 'Total Urusan', value: props.stats?.total_urusan || 0, color: 'text-rose-600', bg: 'bg-rose-50' },
]);

const chartData = computed(() => ({
    labels: props.temaChart?.labels || [],
    datasets: [{
        label: 'Jumlah Indikator',
        backgroundColor: '#4F46E5',
        borderRadius: 12,
        data: props.temaChart?.values || []
    }]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    }
};
</script>

<template>
    <Head title="Executive Dashboard" />

    <div class="mt-32 mb-32 space-y-8 max-w-[90%] mx-auto py-4">
        <div class="bg-gradient-to-r from-[#4F46E5] to-[#7C3AED] p-10 rounded-[2.5rem] text-white shadow-xl relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-4xl font-black mb-2">Executive Overview</h1>
                <p class="text-indigo-100 font-medium">Monitoring Data Pembangunan BAPPEDA Provinsi NTB</p>
            </div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div v-for="card in summaryCards" :key="card.label" 
                 class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ card.label }}</p>
                <h2 class="text-3xl font-black text-gray-900">{{ card.value }}</h2>
                <div :class="[card.bg, 'h-1.5 w-full mt-4 rounded-full overflow-hidden']">
                    <div :class="[card.color.replace('text', 'bg'), 'h-full w-2/3']"></div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm h-[450px]">
            <h3 class="font-bold text-gray-900 mb-8 ml-2">Indikator Per Tema</h3>
            <div class="h-[320px] px-2 pb-4">
                <Bar :data="chartData" :options="chartOptions" />
            </div>
        </div>

        <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col items-center justify-center">
            <h3 class="font-bold text-gray-900 mb-8">Persentase Validasi</h3>
            <div class="w-full h-56 px-4"> <Doughnut :data="{
                    labels: ['Valid', 'Pending'],
                    datasets: [{
                        data: [stats.data_valid, stats.total_indikator - stats.data_valid],
                        backgroundColor: ['#10B981', '#F3F4F6'],
                        borderWidth: 0,
                        hoverOffset: 10 // Efek saat kursor di atasnya
                    }]
                }" :options="{ ...chartOptions, cutout: '70%' }" /> </div>
            <div class="text-center mt-6">
                <p class="text-2xl font-black text-emerald-600">
                    {{ stats.total_indikator > 0 ? Math.round((stats.data_valid / stats.total_indikator) * 100) : 0 }}%
                </p>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Data Terverifikasi</p>
            </div>
        </div>
    </div>
</template>