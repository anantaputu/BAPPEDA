<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import IconifyIcon from '@/Components/Base/IconifyIcon.vue';

import StatCard from '@/Components/Dashboard/StatCard.vue';
import BarChartIndikator from '@/Components/Dashboard/BarChartIndikator.vue';
import ValidationDoughnut from '@/Components/Dashboard/ValidationDoughnut.vue';
import GrowthLineChart from '@/Components/Dashboard/GrowthLineChart.vue';
import ActivityLog from '@/Components/Dashboard/ActivityLog.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    stats: { type: Object, default: () => ({}) },
    temaChart: { type: Object, default: () => ({ labels: [], values: [] }) },
    bidangChart: { type: Object, default: () => ({ labels: [], values: [] }) },
    roleChart: { type: Object, default: () => ({ labels: [], values: [] }) },
    sourceChart: { type: Object, default: () => ({ labels: [], values: [] }) },
    growthChart: { type: Object, default: () => ({ labels: [], values: [] }) },
    recentActivities: { type: Array, default: () => [] },
    recentUsers: { type: Array, default: () => [] },
    recentDatasets: { type: Array, default: () => [] },
    pinnedData: { type: Array, default: () => [] },
    dataHealth: { type: Object, default: () => ({}) },
});

const colors = {
    navy: { bg: 'bg-primary/5', text: 'text-primary', bar: 'bg-secondary' },
};

const statsCards = computed(() => [
    { label: 'Total User', value: props.stats.total_user || 0, icon: 'solar:users-group-rounded-bold', color: 'navy' },
    { label: 'User Aktif', value: props.stats.user_active || 0, icon: 'solar:user-check-bold', color: 'navy' },
    { label: 'Total Dataset', value: props.stats.total_dataset || 0, icon: 'solar:database-bold', color: 'navy' },
    { label: 'Input Hari Ini', value: props.stats.input_today || 0, icon: 'solar:calendar-mark-bold', color: 'navy' },
    { label: 'Total Sumber', value: props.stats.total_org || 0, icon: 'solar:buildings-3-bold', color: 'navy' },
    { label: 'Total Log', value: props.stats.total_logs || 0, icon: 'solar:document-text-bold', color: 'navy' },
]);

const temaBarData = computed(() => ({
    labels: props.temaChart.labels || [],
    datasets: [{ label: 'Dataset', backgroundColor: '#1F3A63', borderRadius: 8, data: props.temaChart.values || [] }],
}));

const roleBarData = computed(() => ({
    labels: props.roleChart.labels || [],
    datasets: [{ label: 'User', backgroundColor: '#0284C7', borderRadius: 8, data: props.roleChart.values || [] }],
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
    }))
);
</script>

<template>
    <Head title="Dashboard Admin" />

    <div class="space-y-6">
        <section class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-primary uppercase tracking-tight">Dashboard Monitoring Admin</h1>
                <p class="text-sm text-textsecondary font-medium">Pantau user, kualitas data, aktivitas sistem, dan tren dataset secara terpusat.</p>
            </div>
            <Link href="/admin/data" class="px-5 py-3 rounded-xl bg-primary text-white text-xs font-black uppercase tracking-widest hover:bg-secondary transition-colors">
                Kelola Data
            </Link>
        </section>

        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            <StatCard
                v-for="(stat, index) in statsCards"
                :key="index"
                v-bind="stat"
                :colors="colors"
            />
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="rounded-xl border border-gray-400 bg-white p-5">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary">Coverage Nilai Data</p>
                <p class="mt-3 text-3xl font-black text-primary">{{ dataHealth.coverage_value_percent || 0 }}%</p>
                <p class="mt-1 text-xs font-bold text-textsecondary">Dataset yang sudah memiliki nilai tahunan.</p>
            </div>
            <div class="rounded-xl border border-gray-400 bg-white p-5">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary">Dataset Tanpa Sumber</p>
                <p class="mt-3 text-3xl font-black text-integritas">{{ dataHealth.without_source_count || 0 }}</p>
                <p class="mt-1 text-xs font-bold text-textsecondary">Perlu dilengkapi untuk validitas metadata.</p>
            </div>
            <div class="rounded-xl border border-gray-400 bg-white p-5">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary">Dataset Tanpa Tahun</p>
                <p class="mt-3 text-3xl font-black text-profesional">{{ dataHealth.without_tahun_count || 0 }}</p>
                <p class="mt-1 text-xs font-bold text-textsecondary">Perlu normalisasi tahun terbit.</p>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2">
                <BarChartIndikator title="Distribusi Dataset per Tema" :chartData="temaBarData" />
            </div>
            <ValidationDoughnut :doughnutData="bidangDoughnutData" :validationData="validationData" />
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2">
                <GrowthLineChart :chartData="growthChart" />
            </div>
            <BarChartIndikator title="Distribusi User per Role" :chartData="roleBarData" />
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="rounded-xl border border-gray-400 bg-white p-6">
                <h3 class="text-sm font-black text-primary uppercase tracking-[0.2em] border-l-4 border-secondary pl-4 mb-5">Dataset Terbaru Diupdate</h3>
                <div class="space-y-3">
                    <div v-for="item in recentDatasets" :key="item.id" class="rounded-xl border border-gray-100 bg-bgsoft/50 p-3">
                        <p class="text-sm font-black text-primary line-clamp-1">{{ item.name }}</p>
                        <p class="text-[11px] font-bold text-textsecondary mt-1">{{ item.tema }} • {{ item.bidang }}</p>
                        <p class="text-[10px] font-bold text-secondary uppercase tracking-widest mt-2">{{ item.updated_at }}</p>
                    </div>
                    <p v-if="!recentDatasets.length" class="text-xs font-bold text-textsecondary">Belum ada data terbaru.</p>
                </div>
            </div>

            <div class="rounded-xl border border-gray-400 bg-white p-6">
                <h3 class="text-sm font-black text-primary uppercase tracking-[0.2em] border-l-4 border-secondary pl-4 mb-5">User Terbaru</h3>
                <div class="space-y-3">
                    <div v-for="user in recentUsers" :key="user.id" class="rounded-xl border border-gray-100 bg-bgsoft/50 p-3 flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-black text-primary">{{ user.name }}</p>
                            <p class="text-[11px] font-bold text-textsecondary">{{ user.email }}</p>
                            <p class="text-[10px] font-black uppercase tracking-widest text-secondary mt-1">{{ user.role }}</p>
                        </div>
                        <span class="text-[10px] font-black uppercase px-2 py-1 rounded-lg border"
                            :class="user.status_aktif ? 'bg-inovasi/10 text-inovasi border-inovasi/20' : 'bg-integritas/10 text-integritas border-integritas/20'">
                            {{ user.status_aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <p v-if="!recentUsers.length" class="text-xs font-bold text-textsecondary">Belum ada data user terbaru.</p>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2">
                <ActivityLog :activities="recentActivities" />
            </div>

            <div class="rounded-xl border border-gray-400 bg-white p-6">
                <h3 class="text-sm font-black text-primary uppercase tracking-[0.2em] border-l-4 border-secondary pl-4 mb-5">Top Sumber Data</h3>
                <div class="space-y-3">
                    <div v-for="row in sourceRows" :key="row.label" class="rounded-xl border border-gray-100 bg-bgsoft/50 p-3 flex items-center justify-between gap-3">
                        <p class="text-xs font-black text-primary uppercase line-clamp-1">{{ row.label }}</p>
                        <span class="text-sm font-black text-secondary">{{ row.value }}</span>
                    </div>
                    <p v-if="!sourceRows.length" class="text-xs font-bold text-textsecondary">Belum ada sumber data.</p>
                </div>
            </div>
        </section>

        <section class="animate-in fade-in duration-500">
            <div class="flex items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500 shadow-sm border border-amber-100">
                        <IconifyIcon icon="solar:bookmark-bold" width="20" height="20" />
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-primary uppercase tracking-widest">Pin Indikator Favorit</h3>
                        <p class="text-[10px] text-textsecondary font-bold uppercase tracking-wider mt-0.5">Akses cepat indikator prioritas</p>
                    </div>
                </div>

                <Link href="/inputer/data" class="text-[10px] font-black text-secondary hover:text-primary transition-colors uppercase tracking-widest bg-secondary/5 px-4 py-2 rounded-xl">
                    Jelajahi Data
                </Link>
            </div>

            <div v-if="pinnedData.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <Link
                    v-for="item in pinnedData"
                    :key="item.id_data"
                    :href="`/dataset/${item.id_data}`"
                    class="group bg-white p-4 rounded-xl border border-gray-400 hover:border-secondary/40 transition-all"
                >
                    <p class="text-sm font-black text-primary line-clamp-2">{{ item.nama_indikator }}</p>
                    <p class="text-[10px] font-bold text-textsecondary mt-3 uppercase tracking-widest">Tahun {{ item.tahun_terbit || '-' }}</p>
                </Link>
            </div>

            <div v-else class="w-full bg-white border border-dashed border-gray-300 rounded-xl p-6 text-center">
                <p class="text-xs font-black text-textsecondary uppercase tracking-[0.2em]">Belum ada pin indikator.</p>
            </div>
        </section>
    </div>
</template>
