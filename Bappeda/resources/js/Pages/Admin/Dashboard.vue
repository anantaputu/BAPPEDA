<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

// Import Komponen Dashboard
import StatCard from '@/Components/Dashboard/StatCard.vue';
import BarChartIndikator from '@/Components/Dashboard/BarChartIndikator.vue';
import ValidationDoughnut from '@/Components/Dashboard/ValidationDoughnut.vue';
import GrowthLineChart from '@/Components/Dashboard/GrowthLineChart.vue';
import ActivityLog from '@/Components/Dashboard/ActivityLog.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    auth: Object,
    errors: Object,
    stats: {
        type: Object,
        default: () => ({ 
            total_dataset: 0, 
            data_valid: 0, 
            total_visual: 0, 
            total_org: 0,
            total_user: 0,
            user_active: 0,
            user_inactive: 0
        })
    },
    temaChart: { 
        type: Object,
        default: () => ({ labels: [], values: [] })
    },
    bidangChart: Object,
    datasets: {
        type: Object,
        default: () => ({ popular: [], latest: [] })
    },
    topics: Array,
    growthChart: Object,
    recentActivities: {
        type: Array,
        default: () => []
    },
});

// --- KONFIGURASI WARNA BIRU ADMIN ---
const colors = {
    // Navy untuk elemen utama/total
    navy: { bg: 'bg-[#000B58]/10', text: 'text-[#000B58]', bar: 'bg-[#000B58]' },
    // Royal untuk elemen aktif/proses
    royal: { bg: 'bg-[#00139E]/10', text: 'text-[#00139E]', bar: 'bg-[#00139E]' },
    // Biru Terang untuk variasi
    bright: { bg: 'bg-blue-50', text: 'text-blue-600', bar: 'bg-blue-600' },
};

// 2. KONFIGURASI STATS CARDS (Dynamic Data dengan Skema Biru)
const statsCards = computed(() => [
    { 
        label: 'TOTAL USER', 
        value: props.stats.total_user || 0, 
        icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 
        color: 'navy', 
        progress: 100 
    },
    { 
        label: 'USER AKTIF', 
        value: props.stats.user_active || 0, 
        icon: 'M9 12l2 2 4-4m5.618-4.016A9 9 0 112.182 12a9 9 0 0115.818-4.016z', 
        color: 'royal', 
        progress: (props.stats.total_user > 0) ? ((props.stats.user_active / props.stats.total_user) * 100) : 0
    },
    { 
        label: 'TOTAL DATASET', 
        value: props.stats.total_dataset || 0, 
        icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4', 
        color: 'navy', 
        progress: 100 
    },
    { 
        label: 'DATA VALID', 
        value: props.stats.data_valid || 0, 
        icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 
        color: 'royal', 
        progress: (props.stats.total_dataset > 0) ? ((props.stats.data_valid / props.stats.total_dataset) * 100) : 0
    },
]);

// 3. BAR CHART CONFIG
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
            grid: { color: '#F1F5F9' }, 
            ticks: { font: { size: 10, weight: 'bold' }, color: '#64748b' } 
        },
        x: { 
            grid: { display: false }, 
            ticks: { font: { size: 10, weight: 'bold' }, color: '#64748b' } 
        }
    }
};

// 4. DOUGHNUT CHART CONFIG (Palet Biru Monokromatik)
const doughnutData = computed(() => {
    const hasData = props.bidangChart?.labels?.length > 0;
    const labels = hasData ? props.bidangChart.labels : ['Kosong'];
    const values = hasData ? props.bidangChart.values : [1];
    
    // Gradasi Biru
    const backgroundColors = [
        '#000B58', // Navy
        '#00139E', // Royal
        '#2563EB', // Blue 600
        '#3B82F6', // Blue 500
        '#60A5FA', // Blue 400
        '#93C5FD'  // Blue 300
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

const validationData = computed(() => ({
    total: props.stats.total_dataset || 0
}));

const percentValid = computed(() => {
    return props.stats.total_dataset > 0 
        ? Math.round((props.stats.data_valid / props.stats.total_dataset) * 100) 
        : 0;
});
</script>

<template>
    <Head title="Dashboard Admin" />

    <div class="space-y-8 px-4 sm:px-0">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-black text-[#000B58] uppercase tracking-tight">Dashboard Kontrol</h1>
                <p class="text-sm text-slate-400 font-medium italic">Monitoring aktivitas sistem dan validasi data daerah.</p>
            </div>
            <div class="hidden md:flex bg-white px-4 py-2 rounded-2xl border border-slate-200 items-center gap-3">
                <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
                <span class="text-xs font-bold text-[#000B58] uppercase tracking-widest">Sistem Aktif</span>
            </div>
        </div>

        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard
                v-for="(stat, index) in statsCards" 
                :key="index" 
                v-bind="stat" 
                :colors="colors" 
            />
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-400">
                <h3 class="text-xs font-black text-[#000B58] uppercase tracking-widest mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-[#00139E] rounded-full"></span>
                    Distribusi Indikator per Tema
                </h3>
                <div>
                    <BarChartIndikator 
                        :chartData="barChartData" 
                        :chartOptions="barChartOptions" 
                    />
                </div>
            </div>

            <ValidationDoughnut 
                :doughnutData="doughnutData" 
                :validationData="validationData" 
                :percentValid="percentValid" 
            />
        </section>
        
        <GrowthLineChart :chartData="growthChart" />

        <ActivityLog :activities="recentActivities" />

    </div>
</template>