<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

import StatCard from '@/Components/Dashboard/StatCard.vue';
import BarChartIndikator from '@/Components/Dashboard/BarChartIndikator.vue';
import ValidationDoughnut from '@/Components/Dashboard/ValidationDoughnut.vue'; 
import LineChartTren from '@/Components/Dashboard/LineChartTren.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    auth: Object,
    stats: {
        type: Object,
        default: () => ({ 
            total_dataset: 0, 
            total_tema: 0, 
            total_urusan: 0, 
            total_bidang: 0,
            total_frekuensi: 0, 
            total_sumber: 0 
        })
    },
    temaChart: {
        type: Object,
        default: () => ({ labels: [], values: [] })
    },
    bidangChart: {
        type: Object,
        default: () => ({ labels: [], values: [] })
    },
    frekuensiChart: {
        type: Object,
        default: () => ({ labels: [], values: [] })
    },
    trenChart: {
        type: Object,
        default: () => ({ labels: [], values: [] })
    }
});

const colors = {
    navy: { 
        bg: 'bg-primary/5', 
        text: 'text-primary', 
        bar: 'bg-secondary'
    },
};

const statsCards = computed(() => [
    { 
        label: 'TOTAL DATA', 
        value: props.stats.total_dataset || 0, 
        icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4', 
        color: 'navy' 
    },
    { 
        label: 'CAKUPAN TEMA', 
        value: props.stats.total_tema || 0, 
        icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 
        color: 'navy' 
    },
    { 
        label: 'TOTAL URUSAN', 
        value: props.stats.total_urusan || 0, 
        icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 
        color: 'navy' 
    },
    { 
        label: 'TOTAL BIDANG', 
        value: props.stats.total_bidang || 0, 
        icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 
        color: 'navy' 
    },
    { 
        label: 'VARIASI FREKUENSI', 
        value: props.stats.total_frekuensi || 0, 
        icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 
        color: 'navy' 
    },
    { 
        label: 'SUMBER DATA', 
        value: props.stats.total_sumber || 0, 
        icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 
        color: 'navy' 
    },
]);

const barChartData = computed(() => ({
    labels: props.temaChart?.labels || [],
    datasets: [{
        label: 'Jumlah Data',
        backgroundColor: '#1F3A63',
        borderRadius: 8,
        data: props.temaChart?.values || []
    }]
}));

const doughnutData = computed(() => ({
    labels: props.frekuensiChart?.labels || [],
    datasets: [{
        data: props.frekuensiChart?.values || [],
        backgroundColor: ['#1F3A63', '#0284C7', '#C53030', '#15803D', '#D97706'],
        borderWidth: 0,
        cutout: '75%'
    }]
}));

const lineChartData = computed(() => ({
    labels: props.trenChart?.labels || [],
    datasets: [{
        label: 'Dataset Baru',
        borderColor: '#0284C7',
        backgroundColor: 'rgba(2, 132, 199, 0.1)',
        fill: true,
        tension: 0.4,
        data: props.trenChart?.values || []
    }]
}));
</script>

<template>
    <Head title="Dashboard Utama" />

    <div class="space-y-8 max-w-[85%] mx-auto mt-20">
        <section class="bg-primary rounded-xl p-8 md:p-12 flex flex-col md:flex-row items-center justify-between relative overflow-hidden shadow-xl shadow-primary/10">
            <div class="text-white z-10 max-w-lg relative">
                <h1 class="text-3xl md:text-4xl font-black mb-4 leading-tight uppercase">
                    Eksplorasi Data <br>Bappeda Nusa Tenggara Barat.
                </h1>
                <p class="text-white/70 text-sm font-medium leading-relaxed">
                    Akses data pembangunan daerah dengan cepat, transparan, dan akurat. Semua data telah divalidasi OPD.
                </p>
            </div> 
            <div class="absolute top-0 right-0 w-96 h-96 bg-secondary/20 rounded-full blur-3xl -mr-20 -mt-40 pointer-events-none"></div>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
            <StatCard 
                v-for="(stat, index) in statsCards" 
                :key="index" 
                v-bind="stat" 
                :colors="colors" 
                class="!rounded-xl border border-bgsoft shadow-sm"
            />
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <BarChartIndikator 
                title="Distribusi Data"
                :chartData="barChartData" 
                class="lg:col-span-2"
            />

            <div class="bg-primary text-white p-10 rounded-xl shadow-xl relative overflow-hidden flex flex-col h-full">
                <div class="text-center mb-12">
                    <h3 class="text-sm font-black uppercase tracking-[0.3em] text-secondary inline-block border-b-2 border-secondary pb-2">
                        Top Ranking Bidang
                    </h3>
                </div>

                <div class="flex items-end justify-center gap-4 mb-12 h-64 relative z-10">
                    <div v-if="bidangChart.labels[1]" class="flex-1 flex flex-col items-center group">
                        <div class="text-[10px] font-black text-white/70 mb-3 text-center line-clamp-2 px-2 h-8 leading-tight uppercase">
                            {{ bidangChart.labels[1] }}
                        </div>
                        <div class="bg-white/10 w-full rounded-t-xl flex flex-col items-center justify-center border-x border-t border-white/10 backdrop-blur-sm transition-all" style="height: 70%;">
                            <p class="text-2xl font-black mb-1">{{ bidangChart.values[1] }}</p>
                            <p class="text-[8px] uppercase tracking-widest font-bold text-secondary">Indikator</p>
                        </div>
                    </div>

                    <div v-if="bidangChart.labels[0]" class="flex-1 flex flex-col items-center group">
                        <div class="mb-2 animate-bounce">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-profesional" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <div class="text-[11px] font-black text-secondary mb-3 text-center line-clamp-2 px-2 h-8 leading-tight uppercase tracking-wide">
                            {{ bidangChart.labels[0] }}
                        </div>
                        <div class="bg-gradient-to-b from-secondary to-primary w-full rounded-t-xl flex flex-col items-center justify-center border-x border-t border-white/20 shadow-lg shadow-secondary/20" style="height: 100%;">
                            <p class="text-4xl font-black text-white mb-2">{{ bidangChart.values[0] }}</p>
                            <p class="text-[10px] uppercase tracking-widest text-white font-black">Utama</p>
                        </div>
                    </div>

                    <div v-if="bidangChart.labels[2]" class="flex-1 flex flex-col items-center group">
                        <div class="text-[10px] font-black text-white/70 mb-3 text-center line-clamp-2 px-2 h-8 leading-tight uppercase">
                            {{ bidangChart.labels[2] }}
                        </div>
                        <div class="bg-white/5 w-full rounded-t-xl flex flex-col items-center justify-center border-x border-t border-white/5 backdrop-blur-sm transition-all" style="height: 50%;">
                            <p class="text-xl font-black mb-1">{{ bidangChart.values[2] }}</p>
                            <p class="text-[8px] uppercase tracking-widest font-bold text-secondary">Indikator</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 mt-auto">
                    <div v-for="(label, i) in bidangChart.labels.slice(3, 5)" :key="i" 
                         class="flex justify-between items-center bg-white/5 p-4 rounded-xl border border-white/10">
                        <div class="flex items-center gap-4">
                            <div class="bg-secondary/20 w-8 h-8 rounded-lg flex items-center justify-center font-black text-xs text-secondary">
                                {{ i + 4 }}
                            </div>
                            <p class="text-[10px] text-white font-black uppercase tracking-wider line-clamp-1 max-w-[150px]">
                                {{ label }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-md font-black leading-none">{{ bidangChart.values[i + 3] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-8 rounded-xl border border-gray-400 shadow-sm flex flex-col h-full">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-sm font-black text-primary uppercase tracking-[0.2em] border-l-4 border-secondary pl-4">
                        Pertumbuhan Data
                    </h3>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-textsecondary block leading-none uppercase">Total Tren</span>
                        <span class="text-lg font-black text-secondary">+{{ trenChart.values.reduce((a, b) => a + b, 0) }}</span>
                    </div>
                </div>
                <div class="flex-grow min-h-[350px]">
                    <LineChartTren :chartData="lineChartData"/>
                </div>
            </div>

            <div class="bg-white p-8 rounded-xl border border-gray-400 shadow-sm flex flex-col h-full">
                <h3 class="text-sm font-black text-primary mb-8 uppercase tracking-[0.2em] border-l-4 border-secondary pl-4">
                    Kemutakhiran Data
                </h3>
                <div class="space-y-4">
                    <div v-for="(label, i) in doughnutData.labels" :key="i" 
                         class="flex justify-between items-center px-6 py-4 rounded-xl bg-bgsoft border border-gray-100 group hover:border-secondary transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: doughnutData.datasets[0].backgroundColor[i] }"></div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black text-textsecondary uppercase tracking-widest leading-none mb-1">Frekuensi</span>
                                <span class="text-xs font-bold text-primary uppercase">{{ label }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-black text-primary block leading-none">{{ doughnutData.datasets[0].data[i] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <div class="h-8"></div>
    </div>
</template>