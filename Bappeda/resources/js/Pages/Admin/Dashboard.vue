<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import IconifyIcon from '@/Components/Base/IconifyIcon.vue';

import StatCard from '@/Components/Dashboard/StatCard.vue';
import BarChartIndikator from '@/Components/Dashboard/BarChartIndikator.vue';
import ValidationDoughnut from '@/Components/Dashboard/ValidationDoughnut.vue';
import GrowthLineChart from '@/Components/Dashboard/GrowthLineChart.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    stats: { type: Object, default: () => ({}) },
    temaChart: { type: Object, default: () => ({ labels: [], values: [] }) },
    bidangChart: { type: Object, default: () => ({ labels: [], values: [] }) },
    roleChart: { type: Object, default: () => ({ labels: [], values: [] }) },
    pinnedData: {
        type: Array,
        default: () => [] 
    },
    sourceChart: { type: Object, default: () => ({ labels: [], values: [] }) },
    growthChart: { type: Object, default: () => ({ labels: [], values: [] }) },
    recentActivities: { type: Array, default: () => [] },
    recentDatasets: { type: Array, default: () => [] },
    dataHealth: { type: Object, default: () => ({}) },
});

const colors = {
    navy: { bg: 'bg-primary/5', text: 'text-primary', bar: 'bg-secondary' },
};

const statsCards = computed(() => [
    { label: 'Total User', value: props.stats.total_user || 0, icon: 'solar:users-group-rounded-bold', color: 'navy' },
    { label: 'Total Dataset', value: props.stats.total_dataset || 0, icon: 'solar:database-bold', color: 'navy' },
    { label: 'Input Hari Ini', value: props.stats.input_today || 0, icon: 'solar:calendar-mark-bold', color: 'navy' },
    { label: 'Total Log', value: props.stats.total_logs || 0, icon: 'solar:document-text-bold', color: 'navy' },
]);

const temaBarData = computed(() => ({
    labels: props.temaChart.labels || [],
    datasets: [{ label: 'Dataset', backgroundColor: '#1F3A63', borderRadius: 8, data: props.temaChart.values || [] }],
}));

const bidangDoughnutData = computed(() => ({
    labels: props.bidangChart.labels || [],
    datasets: [{
        data: props.bidangChart.values || [],
        backgroundColor: ['#1F3A63', '#0284C7', '#4A6CF7', '#16A34A', '#F59E0B', '#E11D48'],
        borderWidth: 0,
        cutout: '75%',
    }],
}));

const validationData = computed(() => ({ total: props.stats.total_dataset || 0 }));

const sourceRows = computed(() =>
    (props.sourceChart.labels || []).map((label, i) => ({
        label,
        value: props.sourceChart.values?.[i] || 0,
    })).slice(0, 5)
);

const recentDatasetRows = computed(() => (props.recentDatasets || []).slice(0, 5));
</script>

<template>
    <Head title="Dashboard Admin" />

    <div class="space-y-6">
        <section class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="ui-title-md">Dashboard Monitoring Admin</h1>
                <p class="ui-body mt-2">Pantau user, kualitas data, aktivitas sistem, dan tren dataset dari panel yang lebih ringkas.</p>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard
                v-for="(stat, index) in statsCards"
                :key="index"
                v-bind="stat"
                :colors="colors"
                class="border-gray-400"
            />
        </section>

        <section class="mt-2 animate-in fade-in duration-500">
            <div class="mb-5 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-amber-100 bg-amber-50 text-amber-500 shadow-sm">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                    </div>
                    <div>
                        <h3 class="ui-title-sm">Pin Indikator Favorit</h3>
                        <p class="ui-body mt-1">Akses cepat ke data pantauan Anda.</p>
                    </div>
                </div>
                
                <Link href="/inputer/data" class="ui-chip bg-secondary/5 px-4 py-2 text-secondary hover:text-primary" style="border-radius: var(--radius-soft);">
                    Jelajahi Data
                </Link>
            </div>

            <div v-if="pinnedData && pinnedData.length > 0" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                <Link v-for="item in pinnedData.slice(0, 3)" :key="item.id_data" :href="`/dataset/${item.id_data}`" 
                    class="ui-panel bg-white border border-gray-400 group flex min-h-[120px] flex-col justify-between p-5 transition-all duration-300 hover:-translate-y-1 hover:border-secondary/40 hover:shadow-xl"
                    style="border-radius: var(--radius-panel);">
                    <div>
                        <div class="mb-2 flex items-start justify-between gap-3">
                            <h4 class="text-[0.95rem] font-black leading-tight text-primary transition-colors group-hover:text-secondary line-clamp-2">
                                {{ item.nama_data }}
                            </h4>
                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-50 transition-colors group-hover:bg-amber-100">
                                <svg class="h-3 w-3 text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-3">
                        <span class="ui-eyebrow">Tahun Data</span>
                        <span class="ui-chip border border-primary/10 bg-primary/5 px-3 py-1.5 text-secondary" style="border-radius: 0.7rem;">
                            {{ item.tahun_terbit || '-' }}
                        </span>
                    </div>
                </Link>
            </div>

            <div v-else class="ui-panel flex w-full flex-col items-center justify-center p-8 text-center" style="border-radius: var(--radius-panel); border-style: dashed;">
                <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-50 text-gray-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                </div>
                <h4 class="ui-eyebrow text-textsecondary">Belum ada data yang disematkan</h4>
                <p class="ui-body mt-2">Buka halaman detail data dan gunakan pin untuk menaruh indikator penting di sini.</p>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="ui-panel p-5 border-gray-400" style="border-radius: var(--radius-panel);">
                <p class="ui-eyebrow">Coverage Nilai Data</p>
                <p class="mt-3 text-[2.375rem] font-black leading-none text-primary">{{ dataHealth.coverage_value_percent || 0 }}%</p>
                <p class="ui-body mt-2">Dataset yang sudah memiliki nilai tahunan.</p>
            </div>
            <div class="ui-panel p-5 border-gray-400" style="border-radius: var(--radius-panel);">
                <p class="ui-eyebrow">Dataset Tanpa Sumber</p>
                <p class="mt-3 text-[2.375rem] font-black leading-none text-integritas">{{ dataHealth.without_source_count || 0 }}</p>
                <p class="ui-body mt-2">Perlu dilengkapi untuk validitas metadata.</p>
            </div>
            <div class="ui-panel p-5 border-gray-400" style="border-radius: var(--radius-panel);">
                <p class="ui-eyebrow">Dataset Tanpa Tahun</p>
                <p class="mt-3 text-[2.375rem] font-black leading-none text-profesional">{{ dataHealth.without_tahun_count || 0 }}</p>
                <p class="ui-body mt-2">Perlu normalisasi tahun terbit.</p>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2">
                <BarChartIndikator
                    title="Distribusi Dataset per Tema"
                    :chartData="temaBarData"
                    xAxisLabel="Tema"
                    yAxisLabel="Jumlah Dataset"
                />
            </div>
            <ValidationDoughnut
                :doughnutData="bidangDoughnutData"
                :validationData="validationData"
                categoryLabel="Bidang"
                valueLabel="Jumlah Dataset"
            />
        </section>

        <div class="w-full">
            <GrowthLineChart
                :chartData="growthChart"
                xAxisLabel="Periode Bulanan"
                yAxisLabel="Jumlah Dataset Baru"
            />
        </div>

        <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="ui-panel p-6 border-gray-400" style="border-radius: var(--radius-panel);">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <h3 class="ui-eyebrow border-l-4 border-secondary pl-4">Dataset Terbaru Diupdate</h3>
                    <Link href="/inputer/data" class="ui-chip border border-slate-200 px-3 py-2 text-secondary" style="border-radius: var(--radius-soft);">
                        Buka Data
                    </Link>
                </div>
                <div class="space-y-3">
                    <div v-for="item in recentDatasetRows" :key="item.id" class="ui-surface p-3" style="border-radius: var(--radius-soft);">
                        <p class="text-[0.95rem] font-black text-primary line-clamp-1">{{ item.name }}</p>
                        <p class="mt-1 text-[0.82rem] font-bold text-textsecondary">{{ item.tema }} • {{ item.bidang }}</p>
                        <p class="mt-2 text-[0.76rem] font-bold uppercase tracking-[0.16em] text-secondary">{{ item.updated_at }}</p>
                    </div>
                    <p v-if="!recentDatasetRows.length" class="ui-body">Belum ada data terbaru.</p>
                </div>
            </div>

            <div class="ui-panel p-6 border-gray-400" style="border-radius: var(--radius-panel);">
                <h3 class="ui-eyebrow mb-5 border-l-4 border-secondary pl-4">Top Sumber Data</h3>
                <div class="space-y-3">
                    <div v-for="row in sourceRows" :key="row.label" class="ui-surface flex items-center justify-between gap-3 p-3" style="border-radius: var(--radius-soft);">
                        <p class="text-[0.82rem] font-black uppercase text-primary line-clamp-1">{{ row.label }}</p>
                        <span class="text-[1rem] font-black text-secondary">{{ row.value }}</span>
                    </div>
                    <p v-if="!sourceRows.length" class="ui-body">Belum ada sumber data.</p>
                </div>
            </div>
        </section>

        
    </div>
</template>
