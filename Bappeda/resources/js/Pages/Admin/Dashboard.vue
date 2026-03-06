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
    // [BARU] Menangkap data bookmark/pin dari Controller
    pinnedData: {
        type: Array,
        default: () => [] 
    }
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
        </div>

        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
            <StatCard
                v-for="(stat, index) in statsCards" 
                :key="index" 
                v-bind="stat" 
                :colors="colors" 
            />
        </section>

        <section class="mt-8 animate-in fade-in duration-500">
            <div class="flex items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500 shadow-sm border border-amber-100">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-primary uppercase tracking-widest">Pin Indikator Favorit</h3>
                        <p class="text-[10px] text-textsecondary font-bold uppercase tracking-wider mt-0.5">Akses cepat ke data pantauan Anda</p>
                    </div>
                </div>
                
                <Link href="/inputer/data" class="text-[10px] font-black text-secondary hover:text-primary transition-colors uppercase tracking-widest bg-secondary/5 px-4 py-2 rounded-xl">
                    Jelajahi Data &rarr;
                </Link>
            </div>

            <div v-if="pinnedData && pinnedData.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <Link v-for="item in pinnedData" :key="item.id_data" :href="`/dataset/${item.id_data}`" 
                    class="group relative bg-white p-5 rounded-[1.5rem] border border-gray-200 shadow-sm hover:border-secondary/40 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col justify-between min-h-[120px]">
                    
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-secondary opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <div>
                        <div class="flex justify-between items-start mb-2 gap-3">
                            <h4 class="text-[13px] font-black text-primary line-clamp-2 leading-tight group-hover:text-secondary transition-colors">
                                {{ item.nama_indikator }}
                            </h4>
                            <div class="w-6 h-6 rounded-full bg-amber-50 flex items-center justify-center shrink-0 group-hover:bg-amber-100 transition-colors">
                                <svg class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex items-center justify-between border-t border-gray-50 pt-3">
                        <span class="text-[10px] font-bold text-textsecondary uppercase tracking-widest">Tahun Data</span>
                        <span class="text-[11px] font-black text-secondary bg-primary/5 px-3 py-1.5 rounded-lg border border-primary/10">
                            {{ item.tahun_terbit || '-' }}
                        </span>
                    </div>
                </Link>
            </div>

            <div v-else class="w-full bg-white border border-dashed border-gray-300 rounded-[1.5rem] p-8 text-center flex flex-col items-center justify-center">
                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                </div>
                <h4 class="text-sm font-black text-textsecondary uppercase tracking-widest">Belum ada data yang disematkan</h4>
                <p class="text-[11px] text-gray-400 mt-2 font-medium">Buka halaman detail data dan klik ikon <span class="inline-block mx-1 w-3 h-3 text-amber-500"><svg fill="currentColor" viewBox="0 0 24 24"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg></span> untuk memantau indikator penting di sini.</p>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 pt-4">
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