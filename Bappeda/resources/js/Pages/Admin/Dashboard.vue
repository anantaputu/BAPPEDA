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

// --- KONFIGURASI WARNA (Diselaraskan dengan Brand BAPPEDA) ---
const colors = {
    // Menggunakan skema Navy (Primary) untuk semua stat agar konsisten
    navy: { 
        bg: 'bg-primary/5', 
        text: 'text-primary', 
        bar: 'bg-secondary' 
    },
};

// 2. KONFIGURASI STATS CARDS (Diseragamkan warnanya)
const statsCards = computed(() => [
    { 
        label: 'TOTAL USER', 
        value: props.stats.total_user || 0, 
        icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 
        color: 'navy' 
    },
    { 
        label: 'TOTAL DATA', 
        value: props.stats.total_dataset || 0, 
        icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4', 
        color: 'navy' 
    }
]);

// 3. BAR CHART DATA
const barChartData = computed(() => ({
    labels: props.temaChart?.labels || [],
    datasets: [{
        label: 'Jumlah Indikator',
        backgroundColor: '#1F3A63', // primary
        borderRadius: 8,
        data: props.temaChart?.values || []
    }]
}));

// 4. DOUGHNUT CHART (Palet Biru Monokromatik)
const doughnutData = computed(() => {
    const hasData = props.bidangChart?.labels?.length > 0;
    const labels = hasData ? props.bidangChart.labels : ['Kosong'];
    const values = hasData ? props.bidangChart.values : [1];
    
    return {
        labels: labels,
        datasets: [{
            data: values,
            backgroundColor: ['#1F3A63', '#0284C7', '#4A6CF7', '#EEF2F5', '#A2B5CB'],
            borderWidth: 0,
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

    <div class="space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-5">
            <div>
                <h1 class="text-2xl font-black text-primary uppercase tracking-tight">Dashboard Kontrol</h1>
                <p class="text-sm text-textsecondary font-medium">Monitoring aktivitas sistem dan validasi data daerah.</p>
            </div>
            <div class="flex bg-bgsoft px-4 py-2 rounded-xl border border-gray-200 items-center gap-3 w-fit">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-inovasi opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-inovasi"></span>
                </span>
                <span class="text-[10px] font-black text-primary uppercase tracking-widest">Sistem Aktif</span>
            </div>
        </div>

        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
            <StatCard
                v-for="(stat, index) in statsCards" 
                :key="index" 
                v-bind="stat" 
                :colors="colors" 
            />
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <BarChartIndikator 
                class="lg:col-span-2"
                title="Distribusi Data"
                :chartData="barChartData" 
            />

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