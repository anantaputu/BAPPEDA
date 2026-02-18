<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

import StatCard from '@/Components/Dashboard/StatCard.vue';
import BarChartIndikator from '@/Components/Dashboard/BarChartIndikator.vue';
import ValidationDoughnut from '@/Components/Dashboard/ValidationDoughnut.vue';
import CategoryGrid from '@/Components/Dashboard/CategoryGrid.vue';
import DatasetList from '@/Components/Dashboard/DatasetList.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    auth: Object,
    errors: Object,
    stats: {
        type: Object,
        default: () => ({ total_dataset: 0, data_valid: 0, total_visual: 0, total_org: 0 })
    },
    temaChart: {
        type: Object,
        default: () => ({ labels: [], values: [] })
    },
    bidangChart: {
        type: Object,
        default: () => ({ labels: [], values: [] })
    },
    datasets: {
        type: Object,
        default: () => ({ popular: [], latest: [] })
    },
    topics: Array
});

// --- KONFIGURASI WARNA BIRU GELAP ---
const colors = {
    // Menggunakan kombinasi Biru Navy (#000B58) dan Biru Royal (#00139E)
    blue: { bg: 'bg-[#F0F2FF]', text: 'text-[#000B58]', bar: 'bg-[#00139E]' },
    navy: { bg: 'bg-[#000B58]/10', text: 'text-[#000B58]', bar: 'bg-[#000B58]' },
    royal: { bg: 'bg-[#00139E]/10', text: 'text-[#00139E]', bar: 'bg-[#00139E]' },
    soft: { bg: 'bg-slate-100', text: 'text-slate-700', bar: 'bg-slate-600' },
};

// --- KPI / STAT CARDS ---
const statsCards = computed(() => [
    { 
        label: 'TOTAL DATASET', 
        value: props.stats.total_dataset || 0, 
        icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4', 
        color: 'navy', // Biru Navy
        progress: 100 
    },
    { 
        label: 'DATA VALID', 
        value: props.stats.data_valid || 0, 
        icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 
        color: 'royal', // Biru Royal
        progress: (props.stats.total_dataset > 0) ? ((props.stats.data_valid / props.stats.total_dataset) * 100) : 0
    },
    { 
        label: 'TOTAL VISUALISASI', 
        value: props.stats.total_visual || 0, 
        icon: 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z', 
        color: 'navy', 
        progress: 40 
    },
    { 
        label: 'SUMBER DATA', 
        value: props.stats.total_org || 0, 
        icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 
        color: 'royal', 
        progress: 60 
    },
]);

// --- BAR CHART ---
const barChartData = computed(() => ({
    labels: props.temaChart?.labels || [],
    datasets: [{
        label: 'Jumlah Indikator',
        backgroundColor: '#000B58', // Biru Navy
        borderRadius: 6,
        data: props.temaChart?.values || []
    }]
}));

const barChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        y: { 
            beginAtZero: true,
            grid: { color: '#f1f5f9' },
            border: { display: false },
            ticks: { font: { size: 10, weight: 'bold' }, color: '#64748b' } 
        },
        x: { 
            grid: { display: false },
            ticks: { font: { size: 10, weight: 'bold' }, color: '#64748b' } 
        }
    }
};

// --- DOUGHNUT CHART ---
const doughnutData = computed(() => {
    const labels = props.bidangChart?.labels?.length > 0 ? props.bidangChart.labels : ['Valid', 'Belum Valid'];
    const values = props.bidangChart?.values?.length > 0 ? props.bidangChart.values : [props.stats.data_valid, Math.max(0, props.stats.total_dataset - props.stats.data_valid)];
    
    return {
        labels: labels,
        datasets: [{
            data: values,
            backgroundColor: ['#000B58', '#00139E', '#4A6CF7', '#85E6C5', '#A2B5CB'], // Skema gradasi biru
            borderWidth: 0,
            cutout: '75%'
        }]
    };
});

const validationData = computed(() => ({
    valid: props.stats.data_valid || 0,
    total: props.stats.total_dataset || 0
}));

const percentValid = computed(() => {
    return validationData.value.total > 0 
        ? Math.round((validationData.value.valid / validationData.value.total) * 100) 
        : 0;
});

const topicIcons = [
     { name: 'Bidang A', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z' },
    { name: 'Bidang B', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'Bidang C', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
    { name: 'Bidang D', icon: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'Bidang E', icon: 'M12 14l9-5-9-5-9 5 9 5z' },
    { name: 'Bidang F', icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' },
];
</script>

<template>
    <Head title="Dashboard Utama" />

    <div class="space-y-8 max-w-[80%] mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-20">
        <section class="bg-[#000B58] rounded-[2rem] p-8 md:p-12 flex flex-col md:flex-row items-center justify-between relative overflow-hidden shadow-2xl">
            <div class="text-white z-10 max-w-lg relative">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-300 mb-4 block opacity-80">Portal Data Terintegrasi</span>
                <h1 class="text-3xl md:text-4xl font-extrabold mb-4 leading-tight uppercase">
                    Eksplorasi Data <br>Provinsi NTB.
                </h1>
                <p class="text-blue-100/80 mb-8 text-sm font-medium leading-relaxed">
                    Monitoring Data Pembangunan Daerah Provinsi Nusa Tenggara Barat. Akses set data publik dengan cepat, transparan, dan akurat.
                </p>
                <div>
                    <Link 
                        href="/cari" 
                        class="bg-white/10 hover:bg-white/20 border border-white/20 rounded-[3rem] py-3 pl-12 pr-6 text-white text-left backdrop-blur-md transition-all flex items-center group cursor-pointer"
                    >
                        <span class="text-blue-200 group-hover:text-white transition font-bold uppercase text-xs tracking-widest">Mulai Eksplor Data</span>
                    </Link>
                </div>
            </div> 
            <div class="absolute top-0 right-0 w-96 h-96 bg-[#00139E]/30 rounded-full blur-3xl -mr-20 -mt-40 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-400/10 rounded-full blur-3xl -ml-20 -mb-20 pointer-events-none"></div>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard 
                v-for="(stat, index) in statsCards" 
                :key="index" 
                v-bind="stat" 
                :colors="colors" 
            />
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <BarChartIndikator 
                :chartData="barChartData" 
                :chartOptions="barChartOptions" 
            />
            <ValidationDoughnut 
                :doughnutData="doughnutData" 
                :validationData="validationData" 
                :percentValid="percentValid" 
            />
        </section>

        <!-- <CategoryGrid :topics="topicIcons" /> -->

        <!-- <section class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <DatasetList title="Dataset Populer" :items="datasets.popular" type="popular">
                <template #icon>
                    <svg class="w-5 h-5 text-[#00139E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </template>
            </DatasetList>

            <DatasetList title="Dataset Terbaru" :items="datasets.latest" type="latest">
                <template #icon>
                    <svg class="w-5 h-5 text-[#000B58]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
            </DatasetList>
        </section> -->
        
        <div class="h-12"></div>
    </div>
</template>