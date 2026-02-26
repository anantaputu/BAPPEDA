<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

// Import Komponen Dashboard
import StatCard from '@/Components/Dashboard/StatCard.vue';
import BarChartIndikator from '@/Components/Dashboard/BarChartIndikator.vue';
import ValidationDoughnut from '@/Components/Dashboard/ValidationDoughnut.vue'; 
import LineChartTren from '@/Components/Dashboard/LineChartTren.vue'; // Pastikan Anda membuat file ini

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
    },
    datasets: {
        type: Object,
        default: () => ({ popular: [], latest: [] })
    }
});

// --- KONFIGURASI WARNA ---
const colors = {
    blue: { bg: 'bg-[#F0F2FF]', text: 'text-[#000B58]', bar: 'bg-[#00139E]' },
    navy: { bg: 'bg-[#000B58]/10', text: 'text-[#000B58]', bar: 'bg-[#000B58]' },
    royal: { bg: 'bg-[#00139E]/10', text: 'text-[#00139E]', bar: 'bg-[#00139E]' },
};

// --- KPI / STAT CARDS (Sesuai Permintaan Pembimbing) ---
const statsCards = computed(() => [
    { 
        label: 'TOTAL INDIKATOR', 
        value: props.stats.total_dataset || 0, 
        icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4', 
        color: 'navy',
        progress: 100 
    },
    { 
        label: 'CAKUPAN TEMA', 
        value: props.stats.total_tema || 0, 
        icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 
        color: 'royal',
        progress: 100
    },
    { 
        label: 'TOTAL URUSAN', 
        value: props.stats.total_urusan || 0, 
        icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 
        color: 'navy', 
        progress: 100 
    },
    // --- TAMBAHAN BARU: TOTAL BIDANG ---
    { 
        label: 'TOTAL BIDANG', 
        value: props.stats.total_bidang || 0, 
        icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 
        color: 'royal', 
        progress: 100 
    },
    // --- TAMBAHAN BARU: FREKUENSI DATA ---
    { 
        label: 'VARIASI FREKUENSI', 
        value: props.stats.total_frekuensi || 0, 
        icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 
        color: 'navy', 
        progress: 100 
    },
    { 
        label: 'SUMBER DATA', 
        value: props.stats.total_sumber || 0, 
        icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 
        color: 'royal', 
        progress: 100 
    },
]);

// --- BAR CHART DATA (TEMA) ---
const barChartData = computed(() => ({
    labels: props.temaChart?.labels || [],
    datasets: [{
        label: 'Jumlah Indikator',
        backgroundColor: '#000B58',
        borderRadius: 6,
        data: props.temaChart?.values || []
    }]
}));

// --- DOUGHNUT CHART (FREKUENSI DATA) ---
const doughnutData = computed(() => ({
    labels: props.frekuensiChart?.labels || [],
    datasets: [{
        data: props.frekuensiChart?.values || [],
        backgroundColor: ['#000B58', '#00139E', '#4A6CF7', '#85E6C5', '#A2B5CB'],
        borderWidth: 0,
        cutout: '70%'
    }]
}));

// --- LINE CHART (TREN INPUT) ---
const lineChartData = computed(() => ({
    labels: props.trenChart?.labels || [],
    datasets: [{
        label: 'Dataset Baru',
        borderColor: '#00139E',
        backgroundColor: 'rgba(0, 19, 158, 0.1)',
        fill: true,
        tension: 0.4,
        data: props.trenChart?.values || []
    }]
}));
</script>

<template>
    <Head title="Dashboard Utama" />

    <div class="space-y-8 max-w-[90%] mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-20">
        <section class="bg-[#000B58] rounded-[2rem] p-8 md:p-12 flex flex-col md:flex-row items-center justify-between relative overflow-hidden shadow-2xl">
            <div class="text-white z-10 max-w-lg relative">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-300 mb-4 block opacity-80">Portal Data Terintegrasi</span>
                <h1 class="text-3xl md:text-4xl font-extrabold mb-4 leading-tight uppercase">
                    Eksplorasi Data <br>Provinsi NTB.
                </h1>
                <p class="text-blue-100/80 mb-8 text-sm font-medium leading-relaxed">
                    Akses set data pembangunan daerah dengan cepat, transparan, dan akurat. Semua data telah melalui proses standardisasi metadata.
                </p>
                <div>
                    <Link href="/cari" class="bg-white/10 hover:bg-white/20 border border-white/20 rounded-[3rem] py-3 px-8 text-white text-left backdrop-blur-md transition-all inline-flex items-center group">
                        <span class="text-blue-200 group-hover:text-white transition font-bold uppercase text-xs tracking-widest">Mulai Eksplor Data</span>
                    </Link>
                </div>
            </div> 
            <div class="absolute top-0 right-0 w-96 h-96 bg-[#00139E]/30 rounded-full blur-3xl -mr-20 -mt-40 pointer-events-none"></div>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
            <StatCard 
                v-for="(stat, index) in statsCards" 
                :key="index" 
                v-bind="stat" 
                :colors="colors" 
            />
        </section>

        

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
             <div class="lg:col-span-2">
                <BarChartIndikator 
                    title="Distribusi Indikator Berdasarkan Tema"
                    :chartData="barChartData" 
                />
            </div>

            <div class="bg-[#000B58] text-white p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden flex flex-col h-full">
                <div class="text-center mb-12">
                    <h3 class="text-sm font-black uppercase tracking-[0.3em] text-blue-300 inline-block border-b-2 border-blue-500 pb-2">
                        Top Ranking Bidang
                    </h3>
                </div>

                <div class="flex items-end justify-center gap-4 mb-12 h-64 relative z-10">
                    <div v-if="bidangChart.labels[1]" class="flex-1 flex flex-col items-center group">
                        <div class="text-[11px] font-black text-blue-200 mb-3 text-center line-clamp-2 px-2 h-8 leading-tight uppercase">
                            {{ bidangChart.labels[1] }}
                        </div>
                        <div class="bg-white/10 w-full rounded-t-[2rem] flex flex-col items-center justify-center relative border-x border-t border-white/10 backdrop-blur-sm transition-all group-hover:bg-white/20" style="height: 75%;">
                            <p class="text-3xl font-black leading-none mb-1">{{ bidangChart.values[1] }}</p>
                            <p class="text-[10px] uppercase tracking-[0.2em] font-bold text-blue-300">Indikator</p>
                        </div>
                    </div>

                    <div v-if="bidangChart.labels[0]" class="flex-1 flex flex-col items-center group">
                        <div class="mb-2 animate-bounce">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <div class="text-[12px] font-black text-yellow-400 mb-3 text-center line-clamp-2 px-2 h-8 leading-tight uppercase tracking-wide">
                            {{ bidangChart.labels[0] }}
                        </div>
                        <div class="bg-gradient-to-b from-[#00139E] to-[#000B58] w-full rounded-t-[2.5rem] flex flex-col items-center justify-center relative border-x border-t border-blue-400/40 shadow-[0_-15px_30px_rgba(0,19,158,0.4)] transition-all group-hover:from-[#001bc9]" style="height: 100%;">
                            <p class="text-5xl font-black leading-none text-white mb-2">{{ bidangChart.values[0] }}</p>
                            <p class="text-[11px] uppercase tracking-[0.3em] text-yellow-400 font-black">Utama</p>
                        </div>
                    </div>

                    <div v-if="bidangChart.labels[2]" class="flex-1 flex flex-col items-center group">
                        <div class="text-[11px] font-black text-blue-200 mb-3 text-center line-clamp-2 px-2 h-8 leading-tight uppercase">
                            {{ bidangChart.labels[2] }}
                        </div>
                        <div class="bg-white/5 w-full rounded-t-[2rem] flex flex-col items-center justify-center relative border-x border-t border-white/5 backdrop-blur-sm transition-all group-hover:bg-white/10" style="height: 55%;">
                            <p class="text-2xl font-black leading-none mb-1">{{ bidangChart.values[2] }}</p>
                            <p class="text-[10px] uppercase tracking-[0.2em] font-bold text-blue-300 text-center">Indikator</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 mt-auto">
                    <div v-for="(label, i) in bidangChart.labels.slice(3, 5)" :key="i" 
                         class="flex justify-between items-center bg-white/5 p-5 rounded-[2rem] border border-white/10 hover:border-blue-400/50 transition-all group">
                        <div class="flex items-center gap-5">
                            <div class="bg-[#00139E] w-10 h-10 rounded-full flex items-center justify-center font-black text-sm border border-blue-400/30">
                                {{ i + 4 }}
                            </div>
                            <p class="text-xs text-blue-50 font-black uppercase tracking-wider line-clamp-1 max-w-[180px]">
                                {{ label }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-black leading-none">{{ bidangChart.values[i + 3] }}</p>
                            <p class="text-[8px] uppercase font-bold text-blue-400 tracking-tighter">Indikator</p>
                        </div>
                    </div>
                </div>

                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[150%] h-full bg-radial-gradient from-blue-500/10 via-transparent to-transparent pointer-events-none"></div>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-400 flex flex-col h-full">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-sm font-black text-[#000B58] uppercase tracking-[0.2em] border-l-4 border-[#00139E] pl-4">
                        Pertumbuhan Dataset (Tahun Ini)
                    </h3>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-slate-400 block leading-none">TOTAL TREN</span>
                        <span class="text-lg font-black text-[#00139E]">+{{ trenChart.values.reduce((a, b) => a + b, 0) }}</span>
                    </div>
                </div>

                <div class="flex-grow min-h-[400px]">
                    <LineChartTren 
                        :chartData="lineChartData" 
                        :options="{ maintainAspectRatio: false, responsive: true }"
                    />
                </div>
            </div>

            <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-gray-400 flex flex-col h-full">
                <h3 class="text-sm font-black text-[#000B58] mb-8 uppercase tracking-[0.2em] border-l-4 border-[#00139E] pl-4">
                    Kemutakhiran Data
                </h3>

                <div class="flex-grow flex flex-col justify-between">
                    <div class="flex-grow flex flex-col gap-4">
                        <div v-for="(label, i) in doughnutData.labels" :key="i" 
                             class="flex-grow flex justify-between items-center px-6 py-4 rounded-3xl bg-slate-50 border border-slate-100 group hover:bg-[#000B58]/5 transition-all">
                            
                            <div class="flex items-center gap-4">
                                <div class="w-4 h-4 rounded-full shadow-sm" :style="{ backgroundColor: doughnutData.datasets[0].backgroundColor[i] }"></div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Status</span>
                                    <span class="text-sm font-bold text-[#000B58] uppercase">{{ label }}</span>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="text-2xl font-black text-[#000B58] block leading-none">{{ doughnutData.datasets[0].data[i] }}</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Indikator</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <div class="h-12"></div>
    </div>
</template>