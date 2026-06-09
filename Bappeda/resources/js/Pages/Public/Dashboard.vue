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
        borderRadius: 6,
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
        <section class="ui-shell relative flex flex-col items-center justify-between overflow-hidden bg-primary p-8 shadow-xl shadow-primary/10 md:flex-row md:p-12">
            <div class="text-white z-10 max-w-lg relative">
                <h1 class="mb-4 text-[2.375rem] font-black uppercase leading-[1.02] tracking-tight text-white md:text-[3.875rem]">
                    Eksplorasi Data <br>Bappeda Nusa Tenggara Barat.
                </h1>
                <p class="max-w-md text-[1rem] font-medium leading-7 text-white/74">
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
                xAxisLabel="Tema"
                yAxisLabel="Jumlah Dataset"
                class="lg:col-span-2"
            />

            <div class="relative flex h-full flex-col overflow-hidden bg-primary p-10 text-white shadow-xl" style="border-radius: var(--radius-panel);">
                <div class="text-center mb-12">
                    <h3 class="ui-eyebrow inline-block border-b-2 border-secondary pb-2 text-secondary">
                        Top Ranking Bidang
                    </h3>
                </div>

                <div class="flex items-end justify-center gap-4 mb-12 h-64 relative z-10">
                    <div v-if="bidangChart.labels[1]" class="flex-1 flex flex-col items-center group">
                        <div class="mb-3 h-8 px-2 text-center text-[0.76rem] font-black uppercase leading-tight tracking-[0.16em] text-white/70 line-clamp-2">
                            {{ bidangChart.labels[1] }}
                        </div>
                        <div class="flex w-full flex-col items-center justify-center border-x border-t border-white/10 bg-white/10 backdrop-blur-sm transition-all" style="height: 70%; border-top-left-radius: var(--radius-soft); border-top-right-radius: var(--radius-soft);">
                            <p class="mb-1 text-[1.75rem] font-black leading-none">{{ bidangChart.values[1] }}</p>
                            <p class="ui-eyebrow text-secondary">Indikator</p>
                        </div>
                    </div>

                    <div v-if="bidangChart.labels[0]" class="flex-1 flex flex-col items-center group">
                        <div class="mb-2 animate-bounce">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-profesional" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <div class="mb-3 h-8 px-2 text-center text-[0.82rem] font-black uppercase leading-tight tracking-[0.16em] text-secondary line-clamp-2">
                            {{ bidangChart.labels[0] }}
                        </div>
                        <div class="flex w-full flex-col items-center justify-center border-x border-t border-white/20 bg-gradient-to-b from-secondary to-primary shadow-lg shadow-secondary/20" style="height: 100%; border-top-left-radius: var(--radius-soft); border-top-right-radius: var(--radius-soft);">
                            <p class="mb-2 text-[2.875rem] font-black leading-none text-white">{{ bidangChart.values[0] }}</p>
                            <p class="ui-eyebrow text-white">Utama</p>
                        </div>
                    </div>

                    <div v-if="bidangChart.labels[2]" class="flex-1 flex flex-col items-center group">
                        <div class="mb-3 h-8 px-2 text-center text-[0.76rem] font-black uppercase leading-tight tracking-[0.16em] text-white/70 line-clamp-2">
                            {{ bidangChart.labels[2] }}
                        </div>
                        <div class="flex w-full flex-col items-center justify-center border-x border-t border-white/5 bg-white/5 backdrop-blur-sm transition-all" style="height: 50%; border-top-left-radius: var(--radius-soft); border-top-right-radius: var(--radius-soft);">
                            <p class="mb-1 text-[1.375rem] font-black leading-none">{{ bidangChart.values[2] }}</p>
                            <p class="ui-eyebrow text-secondary">Indikator</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 mt-auto">
                    <div v-for="(label, i) in bidangChart.labels.slice(3, 5)" :key="i" 
                         class="flex items-center justify-between border border-white/10 bg-white/5 p-4" style="border-radius: var(--radius-soft);">
                        <div class="flex items-center gap-4">
                            <div class="flex h-8 w-8 items-center justify-center bg-secondary/20 text-[0.82rem] font-black text-secondary" style="border-radius: 0.6rem;">
                                {{ i + 4 }}
                            </div>
                            <p class="max-w-[150px] text-[0.76rem] font-black uppercase tracking-[0.16em] text-white line-clamp-1">
                                {{ label }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-[1.125rem] font-black leading-none">{{ bidangChart.values[i + 3] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="ui-panel flex h-full flex-col p-8 lg:col-span-2" style="border-radius: var(--radius-panel);">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="ui-eyebrow border-l-4 border-secondary pl-4">
                        Pertumbuhan Data
                    </h3>
                    <div class="text-right">
                        <span class="ui-eyebrow block leading-none">Total Tren</span>
                        <span class="text-[1.375rem] font-black leading-none text-secondary">+{{ trenChart.values.reduce((a, b) => a + b, 0) }}</span>
                    </div>
                </div>
                <div class="flex-grow min-h-[350px]">
                    <LineChartTren
                        :chartData="lineChartData"
                        xAxisLabel="Periode Bulanan"
                        yAxisLabel="Jumlah Dataset Baru"
                    />
                </div>
            </div>

            <div class="ui-panel flex h-full flex-col p-8" style="border-radius: var(--radius-panel);">
                <h3 class="ui-eyebrow mb-8 border-l-4 border-secondary pl-4">
                    Kemutakhiran Data
                </h3>
                <div class="space-y-4">
                    <div v-for="(label, i) in doughnutData.labels" :key="i" 
                         class="flex items-center justify-between border border-slate-200 bg-bgsoft px-6 py-4 transition-all group hover:border-secondary" style="border-radius: var(--radius-soft);">
                        <div class="flex items-center gap-4">
                            <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: doughnutData.datasets[0].backgroundColor[i] }"></div>
                            <div class="flex flex-col">
                                <span class="ui-eyebrow mb-1 leading-none">Frekuensi</span>
                                <span class="text-[0.92rem] font-bold uppercase text-primary">{{ label }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block text-[1.375rem] font-black leading-none text-primary">{{ doughnutData.datasets[0].data[i] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <div class="h-8"></div>
    </div>
</template>
