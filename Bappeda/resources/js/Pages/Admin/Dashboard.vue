<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

// Import Komponen Dashboard
import StatCard from '@/Components/Dashboard/StatCard.vue';
import BarChartIndikator from '@/Components/Dashboard/BarChartIndikator.vue';
import ValidationDoughnut from '@/Components/Dashboard/ValidationDoughnut.vue';
import GrowthLineChart from '@/Components/Dashboard/GrowthLineChart.vue';

defineOptions({ layout: AppLayout });

// 1. TERIMA DATA DARI CONTROLLER (OverviewController)
const props = defineProps({
    auth: Object,
    errors: Object,
    stats: {
        type: Object,
        default: () => ({ total_dataset: 0, data_valid: 0, total_visual: 0, total_org: 0 })
    },
    temaChart: { // Nama harus sama dengan di Controller ('temaChart')
        type: Object,
        default: () => ({ labels: [], values: [] })
    },
    bidangChart: Object, // Data Chart Donat
    datasets: {          // Data List Populer & Terbaru
        type: Object,
        default: () => ({ popular: [], latest: [] })
    },
    topics: Array,
    growthChart: Object
});

// 2. KONFIGURASI STATS CARDS (Dynamic Data)
const statsCards = computed(() => [
    { 
        label: 'TOTAL DATASET', 
        value: props.stats.total_dataset || 0, 
        icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4', 
        color: 'blue', 
        progress: 75 
    },
    { 
        label: 'DATA VALID', 
        value: props.stats.data_valid || 0, 
        icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 
        color: 'green', 
        progress: (props.stats.total_dataset > 0) ? ((props.stats.data_valid / props.stats.total_dataset) * 100) : 0
    },
    { 
        label: 'TOTAL VISUALISASI', 
        value: props.stats.total_visual || 0, 
        icon: 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z', 
        color: 'purple', 
        progress: 40 
    },
    { 
        label: 'SUMBER DATA', 
        value: props.stats.total_org || 0, 
        icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 
        color: 'teal', 
        progress: 60 
    },
]);

const colors = {
    blue: { bg: 'bg-blue-100', text: 'text-blue-600', bar: 'bg-blue-600' },
    green: { bg: 'bg-green-100', text: 'text-green-600', bar: 'bg-green-600' },
    purple: { bg: 'bg-purple-100', text: 'text-purple-600', bar: 'bg-purple-600' },
    teal: { bg: 'bg-teal-100', text: 'text-teal-600', bar: 'bg-teal-600' },
};

// 3. BAR CHART CONFIG (Fix: Menggunakan props.temaChart)
const barChartData = computed(() => ({
    labels: props.temaChart?.labels || [],
    datasets: [{
        label: 'Jumlah Indikator',
        backgroundColor: '#000B58',
        borderRadius: 6,
        data: props.temaChart?.values || [] // Menggunakan 'values' sesuai controller
    }]
}));

const barChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        y: { grid: { color: '#F1F5F9' }, ticks: { font: { size: 10 } } },
        x: { grid: { display: false }, ticks: { font: { size: 10 } } }
    }
};

// 4. DOUGHNUT CHART CONFIG (Fix: Menggunakan Data Asli)
// 4. DOUGHNUT CHART CONFIG (Fix: Menggunakan Data Bidang yang Benar)
// ... imports tetap sama ...

// 4. DOUGHNUT CHART CONFIG (PERBAIKAN: Gunakan data Bidang)
const doughnutData = computed(() => {
    // Cek apakah ada data dari controller
    const hasData = props.bidangChart?.labels?.length > 0;
    
    const labels = hasData ? props.bidangChart.labels : ['Kosong'];
    const values = hasData ? props.bidangChart.values : [1]; // Dummy value biar chart muncul
    
    // Palette Warna
    const backgroundColors = [
        '#4A6CF7', // Biru
        '#F8B400', // Kuning
        '#FF6B6B', // Merah
        '#2BCBBA', // Tosca
        '#9D4EDD', // Ungu
        '#FF9F43', // Orange
        '#E2E8F0'  // Abu (untuk lainnya/kosong)
    ];

    return {
        labels: labels,
        datasets: [{
            data: values,
            backgroundColor: backgroundColors.slice(0, labels.length),
            borderWidth: 2,
            borderColor: '#ffffff',
            hoverOffset: 4,
            cutout: '75%'
        }]
    };
});

// Data untuk teks tengah (Total Keseluruhan)
const validationData = computed(() => ({
    total: props.stats.total_dataset || 0
}));

// percentValid tidak lagi dibutuhkan di Child baru, boleh dihapus
const percentValid = computed(() => {
    return validationData.value.total > 0 
        ? Math.round((validationData.value.valid / validationData.value.total) * 100) 
        : 0;
});

// 5. TOPIK GRID (Static Icons + Dynamic Logic if needed)
const topicIcons = [
    { name: 'Sosial', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z' },
    { name: 'Ekonomi', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'Infrastruktur', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
    { name: 'Lingkungan', icon: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'Pendidikan', icon: 'M12 14l9-5-9-5-9 5 9 5z' },
    { name: 'Kesehatan', icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' },
];
</script>

<template>
    <Head title="Dashboard Utama" />

    <div class="space-y-8 mx-auto px-4 sm:px-6 lg:px-8 py-8">

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
        <section class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div>
                    <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        Tren Pertumbuhan Data
                    </h3>
                    <p class="text-sm text-gray-400 mt-1">Jumlah dataset baru yang diinput setiap bulan.</p>
                </div>
                
                <div class="bg-blue-50 px-4 py-2 rounded-xl text-blue-600 font-bold text-xs flex items-center gap-2 self-start sm:self-center">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    12 Bulan Terakhir
                </div>
            </div>
            
            <div class="h-80 w-full">
                <GrowthLineChart :chartData="growthChart" />
            </div>
        </section>
        
        <div class="h-12"></div>
    </div>
</template>