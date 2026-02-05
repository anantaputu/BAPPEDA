<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Bar, Doughnut } from 'vue-chartjs';
import { 
    Chart as ChartJS, Title, Tooltip, Legend, 
    BarElement, CategoryScale, LinearScale, ArcElement 
} from 'chart.js';

// Registrasi komponen Chart.js
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

// Menggunakan Layout Utama Dashboard
defineOptions({ layout: AppLayout });

// =========================================
// DUMMY DATA (Untuk meniru tampilan gambar)
// =========================================

// 1. Data untuk 4 Kartu Statistik Atas
const statsCards = ref([
    { label: 'TOTAL DATASET', value: '832', icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4', color: 'blue', progress: 75 },
    { label: 'DATA VALID', value: '648', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', color: 'green', progress: 85 },
    { label: 'TOTAL VISUALISASI', value: '17', icon: 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z', color: 'purple', progress: 40 },
    { label: 'TOTAL ORGANISASI', value: '51', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', color: 'teal', progress: 60 },
]);

// Helper warna untuk kartu statistik
const colors = {
    blue: { bg: 'bg-blue-100', text: 'text-blue-600', bar: 'bg-blue-600' },
    green: { bg: 'bg-green-100', text: 'text-green-600', bar: 'bg-green-600' },
    purple: { bg: 'bg-purple-100', text: 'text-purple-600', bar: 'bg-purple-600' },
    teal: { bg: 'bg-teal-100', text: 'text-teal-600', bar: 'bg-teal-600' },
};

// 2. Data untuk Bar Chart (Indikator Per Tema)
const barChartData = {
    labels: ['Sosial', 'Ekonomi', 'Infrastruktur', 'Pemerintahan', 'Lingkungan', 'Pendidikan', 'Kesehatan', 'Pariwisata'],
    datasets: [{
        label: 'Jumlah Indikator',
        backgroundColor: '#000B58', // Warna biru tua sesuai gambar
        borderRadius: 6,
        data: [150, 120, 100, 80, 70, 60, 50, 40]
    }]
};
const barChartOptions = {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        y: { grid: { color: '#F1F5F9' }, ticks: { font: { size: 10 } } },
        x: { grid: { display: false }, ticks: { font: { size: 10 } } }
    }
};

// 3. Data untuk Doughnut Chart (Status Validasi)
const validationData = { valid: 648, total: 832 };
const percentValid = Math.round((validationData.valid / validationData.total) * 100);
const doughnutData = {
    labels: ['Valid', 'Belum Valid'],
    datasets: [{
        data: [validationData.valid, validationData.total - validationData.valid],
        backgroundColor: ['#00139E', '#E2E8F0'], // Biru tua dan Abu-abu
        borderWidth: 0, cutout: '75%'
    }]
};

// 4. Data Dummy Topik (Icon Grid)
const topicIcons = [
    { name: 'Sosial', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z' },
    { name: 'Ekonomi', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'Infrastruktur', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
    { name: 'Lingkungan', icon: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'Pendidikan', icon: 'M12 14l9-5-9-5-9 5 9 5z' },
    { name: 'Kesehatan', icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' },
];

// 5. Data Dummy Dataset Lists
const datasets = {
    popular: [
        { title: 'Perjanjian Kinerja Dinas Sosial 2024', tags: ['PDF', 'Sosial'], org: 'Dinas Sosial' },
        { title: 'Data Stunting Kabupaten Lombok Timur', tags: ['XLSX', 'Kesehatan'], org: 'Dinas Kesehatan' },
        { title: 'Realisasi Anggaran Pendapatan Daerah', tags: ['CSV', 'Keuangan'], org: 'BPKAD' },
    ],
    latest: [
        { title: 'Kunjungan Wisatawan Mancanegara Q1 2025', tags: ['XLSX', 'Pariwisata'], org: 'Dispar' },
        { title: 'Data Pokok Pendidikan (Dapodik) SD/SMP', tags: ['API', 'Pendidikan'], org: 'Dikbud' },
        { title: 'Indeks Kualitas Udara Kota Mataram', tags: ['JSON', 'Lingkungan'], org: 'DLHK' },
    ]
};
</script>

<template>
    <Head title="Dashboard Utama" />

    <div class="space-y-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="bg-[#000B58] rounded-[2rem] p-8 md:p-12 flex flex-col md:flex-row items-center justify-between relative overflow-hidden shadow-xl">
            <div class="text-white z-10 max-w-lg relative">
                 <span class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-200 mb-4 block opacity-80">Overview Strategis</span>
                <h1 class="text-3xl md:text-4xl font-extrabold mb-4 leading-tight">
                    Mau cari data tentang NTB?<br> kini bisa di mana saja.
                </h1>
                <p class="text-blue-200 mb-8 text-sm opacity-90">
                    Monitoring Data Pembangunan Daerah Provinsi NTB. Cari data dan set data terkait dengan cepat, mudah, dan akurat.
                </p>
                 <div class="relative max-w-md">
                    <input type="text" placeholder="Cari dataset..." class="w-full bg-white/10 border border-white/20 rounded-xl py-3 pl-12 pr-4 text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-white/30 backdrop-blur-sm transition">
                    <svg class="w-5 h-5 text-blue-300 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            <div class="hidden md:block z-10">
                 <img src="https://cdn3d.iconscout.com/3d/premium/thumb/business-team-doing-analysis-5696646-4751389.png?f=webp" alt="Analisis Data" class="w-64 h-auto object-contain drop-shadow-2xl animate-float">
            </div>
             <div class="absolute top-0 right-0 w-96 h-96 bg-[#00139E]/40 rounded-full blur-3xl -mr-20 -mt-40 pointer-events-none"></div>
             <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl -ml-20 -mb-20 pointer-events-none"></div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div v-for="(stat, index) in statsCards" :key="index" class="bg-white rounded-[1.5rem] p-6 shadow-sm border border-gray-100 hover:shadow-md transition flex flex-col justify-between h-48">
                <div>
                    <div class="flex items-center gap-4 mb-4">
                        <div :class="['w-12 h-12 rounded-xl flex items-center justify-center', colors[stat.color].bg, colors[stat.color].text]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.icon" /></svg>
                        </div>
                        <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-wider">{{ stat.label }}</h3>
                    </div>
                    <p class="text-4xl font-black text-gray-800">{{ stat.value }}</p>
                </div>
                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden mt-4">
                    <div :class="['h-full rounded-full', colors[stat.color].bar]" :style="{ width: stat.progress + '%' }"></div>
                </div>
            </div>
        </div>


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 bg-white p-8 rounded-[1.5rem] shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-lg font-bold text-gray-800 tracking-tight">Indikator Per Tema</h3>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tahun 2025</span>
                </div>
                <div class="h-[300px]">
                    <Bar :data="barChartData" :options="barChartOptions" />
                </div>
            </div>

            <div class="bg-white p-8 rounded-[1.5rem] shadow-sm border border-gray-100 flex flex-col">
                <h3 class="text-lg font-bold text-gray-800 tracking-tight mb-8">Status Validasi</h3>
                <div class="flex-1 relative flex items-center justify-center px-4">
                    <Doughnut :data="doughnutData" :options="{ responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }" />
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-4xl font-black text-[#00139E]">{{ percentValid }}%</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Tervalidasi</span>
                    </div>
                </div>
                <div class="flex justify-around mt-8 pt-6 border-t border-gray-50">
                    <div class="text-center">
                         <span class="block text-xl font-black text-[#00139E]">{{ validationData.valid }}</span>
                         <span class="text-[10px] font-bold text-gray-400 uppercase">Valid</span>
                    </div>
                    <div class="text-center">
                         <span class="block text-xl font-black text-gray-400">{{ validationData.total - validationData.valid }}</span>
                         <span class="text-[10px] font-bold text-gray-400 uppercase">Draft</span>
                    </div>
                 </div>
            </div>
        </div>


        <div class="py-8">
            <div class="text-center mb-10">
                 <h2 class="text-xl font-black text-gray-900">Kategori Data Pemerintahan</h2>
                 <p class="text-sm text-gray-500 mt-1">Jelajahi data berdasarkan topik pembangunan utama</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                <a href="#" v-for="(topic, index) in topicIcons" :key="index" class="group bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-100 transition flex flex-col items-center justify-center text-center">
                    <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                         <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="topic.icon" /></svg>
                    </div>
                    <span class="font-bold text-sm text-gray-700 group-hover:text-blue-800">{{ topic.name }}</span>
                </a>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <div class="bg-white rounded-[1.5rem] p-8 shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-50">
                     <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Dataset Populer
                     </h3>
                     <a href="#" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua</a>
                </div>
                <div class="space-y-6">
                    <div v-for="(item, index) in datasets.popular" :key="index" class="flex items-start gap-4 group">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500 flex-shrink-0">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                             <h4 class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition line-clamp-2 leading-snug mb-2">{{ item.title }}</h4>
                             <div class="flex gap-2 items-center">
                                <span v-for="tag in item.tags" :key="tag" class="text-[9px] font-bold uppercase bg-gray-100 text-gray-500 px-2 py-0.5 rounded">{{ tag }}</span>
                                <span class="text-[10px] text-gray-400">• {{ item.org }}</span>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[1.5rem] p-8 shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-50">
                     <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Dataset Terbaru
                     </h3>
                     <a href="#" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua</a>
                </div>
                <div class="space-y-6">
                    <div v-for="(item, index) in datasets.latest" :key="index" class="flex items-start gap-4 group">
                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-500 flex-shrink-0">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </div>
                        <div>
                             <h4 class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition line-clamp-2 leading-snug mb-2">{{ item.title }}</h4>
                             <div class="flex gap-2 items-center">
                                <span v-for="tag in item.tags" :key="tag" class="text-[9px] font-bold uppercase bg-blue-50 text-blue-500 px-2 py-0.5 rounded">{{ tag }}</span>
                                <span class="text-[10px] text-gray-400">• {{ item.org }}</span>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="h-12"></div>

    </div>
</template>

<style scoped>
/* Animasi float sederhana untuk ilustrasi di header */
@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
    100% { transform: translateY(0px); }
}
.animate-float {
    animation: float 4s ease-in-out infinite;
}
</style>