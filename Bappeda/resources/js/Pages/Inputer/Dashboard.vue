<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

import StatCard from '@/Components/Dashboard/StatCard.vue';
import BarChartIndikator from '@/Components/Dashboard/BarChartIndikator.vue';
import ValidationDoughnut from '@/Components/Dashboard/ValidationDoughnut.vue';
import GrowthLineChart from '@/Components/Dashboard/GrowthLineChart.vue';
import ActivityLog from '@/Components/Dashboard/ActivityLog.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    auth: Object,
    stats: Object,
    growthChart: Object,
    myRecentActivities: Array,
});

const colors = {
    emerald: { bg: 'bg-emerald-50', text: 'text-emerald-600', bar: 'bg-emerald-600' },
    amber: { bg: 'bg-amber-50', text: 'text-amber-600', bar: 'bg-amber-600' },
    rose: { bg: 'bg-rose-50', text: 'text-rose-600', bar: 'bg-rose-600' },
    blue: { bg: 'bg-blue-50', text: 'text-blue-600', bar: 'bg-blue-600' },
};

const statsCards = computed(() => [
    { 
        label: 'TOTAL DATA DIINPUT', 
        value: props.stats.total_input || 0, 
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 
        color: 'blue', 
        progress: 100 
    },
    { 
        label: 'DATA DISETUJUI', 
        value: props.stats.data_approved || 0, 
        icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 
        color: 'emerald', 
        progress: (props.stats.total_input > 0) ? ((props.stats.data_approved / props.stats.total_input) * 100) : 0
    },
    { 
        label: 'MENUNGGU VALIDASI', 
        value: props.stats.data_pending || 0, 
        icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 
        color: 'amber', 
        progress: (props.stats.total_input > 0) ? ((props.stats.data_pending / props.stats.total_input) * 100) : 0
    },
    { 
        label: 'DATA DITOLAK', 
        value: props.stats.data_rejected || 0, 
        icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z', 
        color: 'rose', 
        progress: (props.stats.total_input > 0) ? ((props.stats.data_rejected / props.stats.total_input) * 100) : 0
    },
]);
</script>

<template>
    <Head title="Dashboard Inputer" />

    <div class="max-w-full overflow-hidden space-y-8 px-4 sm:px-6 lg:px-8 py-4">
        
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-[#000B58] uppercase tracking-tight">Ruang Kerja Inputer</h1>
                <p class="text-sm text-slate-400 font-medium italic">Monitoring performa input data Anda.</p>
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
        
        <div class="w-full">
            <ActivityLog 
                title="Riwayat Aktivitas Saya" 
                :activities="props.myRecentActivities" 
            />
        </div>
    </div>
</template>

<style scoped>
/* Memastikan kontainer tidak meluap saat sidebar transisi */
div {
    transition: width 0.3s ease;
}
</style>