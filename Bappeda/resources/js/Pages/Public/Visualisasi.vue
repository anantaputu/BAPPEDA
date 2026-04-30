<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import BarChartIndikator from '@/Components/Dashboard/BarChartIndikator.vue';
import LineChartTren from '@/Components/Dashboard/LineChartTren.vue';
import ValidationDoughnut from '@/Components/Dashboard/ValidationDoughnut.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    stats: { type: Object, default: () => ({}) },
    temaChart: { type: Object, default: () => ({ labels: [], values: [] }) },
    bidangChart: { type: Object, default: () => ({ labels: [], values: [] }) },
    frekuensiChart: { type: Object, default: () => ({ labels: [], values: [] }) },
    trenChart: { type: Object, default: () => ({ labels: [], values: [] }) },
    datasets: { type: Object, default: () => ({ popular: [], latest: [] }) },
    topics: { type: Array, default: () => [] },
});

const selectedTema = ref('all');
const temaLimit = ref(6);
const trendWindow = ref('12');

const temaRows = computed(() =>
    (props.temaChart.labels || [])
        .map((label, idx) => ({
            label,
            value: props.temaChart.values?.[idx] || 0,
        }))
);

const temaOptions = computed(() =>
    [...(props.temaChart.labels || [])].sort((a, b) => a.localeCompare(b))
);

const filteredTemaRows = computed(() => {
    let rows = [...temaRows.value];

    if (selectedTema.value !== 'all') {
        rows = rows.filter(item => item.label === selectedTema.value);
    }

    rows.sort((a, b) => b.value - a.value);
    return rows.slice(0, Number(temaLimit.value) || 6);
});

const totalTema = computed(() => filteredTemaRows.value.reduce((a, b) => a + b.value, 0) || 1);
const topTema = computed(() =>
    filteredTemaRows.value.map(item => ({
        ...item,
        pct: Math.round((item.value / totalTema.value) * 100),
    }))
);

const topBidang = computed(() =>
    (props.bidangChart.labels || []).map((label, idx) => ({
        label,
        value: props.bidangChart.values?.[idx] || 0,
    }))
        .filter(item => item.label !== 'Lainnya')
        .sort((a, b) => b.value - a.value)
);

const temaBarData = computed(() => ({
    labels: topTema.value.map(item => item.label),
    datasets: [
        {
            label: 'Jumlah Dataset',
            data: topTema.value.map(item => item.value),
            backgroundColor: '#0B3A7E',
            borderRadius: 10,
        },
    ],
}));

const trenLineData = computed(() => ({
    labels: (() => {
        const labels = props.trenChart.labels || [];
        if (trendWindow.value === 'all') return labels;
        return labels.slice(-Number(trendWindow.value));
    })(),
    datasets: [
        {
            label: 'Dataset Baru',
            borderColor: '#0284C7',
            backgroundColor: 'rgba(2, 132, 199, 0.16)',
            fill: true,
            tension: 0.35,
            pointRadius: 3,
            pointHoverRadius: 5,
            data: (() => {
                const values = props.trenChart.values || [];
                if (trendWindow.value === 'all') return values;
                return values.slice(-Number(trendWindow.value));
            })(),
        },
    ],
}));

const bidangDoughnutData = computed(() => ({
    labels: topBidang.value.map(item => item.label),
    datasets: [
        {
            data: topBidang.value.map(item => item.value),
            backgroundColor: ['#0B3A7E', '#0284C7', '#06B6D4', '#1D4ED8', '#22C55E', '#F59E0B', '#EF4444'],
            borderWidth: 0,
            cutout: '75%',
        },
    ],
}));

const maxFrekuensi = computed(() => Math.max(...(props.frekuensiChart.values || [0]), 1));
const frekuensiRows = computed(() =>
    (props.frekuensiChart.labels || []).map((label, idx) => {
        const value = props.frekuensiChart.values?.[idx] || 0;
        return {
            label,
            value,
            pct: Math.round((value / maxFrekuensi.value) * 100),
        };
    }).sort((a, b) => b.value - a.value)
);

const topPopularDatasets = computed(() => props.datasets?.popular?.slice(0, 5) || []);
const topLatestDatasets = computed(() => props.datasets?.latest?.slice(0, 5) || []);
const totalTrenTahunIni = computed(() =>
    (props.trenChart.values || []).reduce((a, b) => a + b, 0)
);
const temaFocusLabel = computed(() =>
    selectedTema.value === 'all' ? 'Semua Tema' : selectedTema.value
);

const resetFilters = () => {
    selectedTema.value = 'all';
    temaLimit.value = 6;
    trendWindow.value = '12';
};
</script>

<template>
    <Head title="Ragam Visualisasi Data" />

    <div class="mx-auto max-w-[82%] pt-24 pb-12 space-y-8">
        <section class="rounded-3xl border border-gray-300 bg-[linear-gradient(120deg,#001a3f_0%,#0b2e59_45%,#0ea5e9_130%)] p-8 md:p-12 text-white overflow-hidden relative">
            <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -left-16 -bottom-24 h-72 w-72 rounded-full bg-cyan-300/20 blur-3xl"></div>
            <h1 class="text-3xl md:text-5xl font-black uppercase tracking-tight leading-tight">
                Ragam Visualisasi
            </h1>
            <p class="mt-3 max-w-2xl text-sm md:text-base text-white/80 font-medium">
                Portal visual publik untuk membaca komposisi data pembangunan secara cepat tanpa masuk ke panel admin.
            </p>
            <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="rounded-2xl bg-white/10 border border-white/20 p-4">
                    <p class="text-[10px] uppercase tracking-widest text-white/70 font-black">Total Dataset</p>
                    <p class="text-3xl font-black mt-1">{{ stats.total_dataset || 0 }}</p>
                </div>
                <div class="rounded-2xl bg-white/10 border border-white/20 p-4">
                    <p class="text-[10px] uppercase tracking-widest text-white/70 font-black">Tema</p>
                    <p class="text-3xl font-black mt-1">{{ stats.total_tema || 0 }}</p>
                </div>
                <div class="rounded-2xl bg-white/10 border border-white/20 p-4">
                    <p class="text-[10px] uppercase tracking-widest text-white/70 font-black">Bidang</p>
                    <p class="text-3xl font-black mt-1">{{ stats.total_bidang || 0 }}</p>
                </div>
                <div class="rounded-2xl bg-white/10 border border-white/20 p-4">
                    <p class="text-[10px] uppercase tracking-widest text-white/70 font-black">Sumber Data</p>
                    <p class="text-3xl font-black mt-1">{{ stats.total_sumber || 0 }}</p>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-gray-300 bg-white p-5 md:p-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <h2 class="text-base font-black uppercase tracking-tight text-primary">Kontrol Visualisasi</h2>
                <button
                    @click="resetFilters"
                    class="px-4 py-2 rounded-xl border border-gray-300 bg-slate-50 hover:bg-slate-100 text-[10px] font-black uppercase tracking-widest text-textsecondary"
                >
                    Reset Filter
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-textsecondary">Fokus Tema</label>
                    <select v-model="selectedTema" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm font-bold text-primary">
                        <option value="all">Semua Tema</option>
                        <option v-for="tema in temaOptions" :key="tema" :value="tema">{{ tema }}</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-textsecondary">Jumlah Tema Ditampilkan</label>
                    <select v-model="temaLimit" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm font-bold text-primary">
                        <option :value="4">Top 4</option>
                        <option :value="6">Top 6</option>
                        <option :value="8">Top 8</option>
                        <option :value="12">Top 12</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-textsecondary">Jendela Tren</label>
                    <select v-model="trendWindow" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm font-bold text-primary">
                        <option value="3">3 bulan terakhir</option>
                        <option value="6">6 bulan terakhir</option>
                        <option value="12">12 bulan terakhir</option>
                        <option value="all">Semua data</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="rounded-full bg-slate-100 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-textsecondary">
                    Tema: {{ temaFocusLabel }}
                </span>
                <span class="rounded-full bg-slate-100 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-textsecondary">
                    Top Tema: {{ temaLimit }}
                </span>
                <span class="rounded-full bg-slate-100 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-textsecondary">
                    Tren: {{ trendWindow === 'all' ? 'Semua Data' : `${trendWindow} Bulan` }}
                </span>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2">
                <BarChartIndikator
                    title="Distribusi Dataset per Tema"
                    :chartData="temaBarData"
                />
            </div>

            <div class="rounded-3xl border border-gray-300 bg-white p-7">
                <h2 class="text-lg font-black uppercase tracking-tight text-primary mb-5">Peringkat Tema</h2>
                <div class="space-y-3">
                    <div
                        v-for="(tema, index) in topTema"
                        :key="tema.label"
                        class="rounded-2xl border border-slate-200 bg-slate-50 p-3"
                    >
                        <div class="flex items-center justify-between text-xs font-black uppercase">
                            <span class="text-primary">{{ index + 1 }}. {{ tema.label }}</span>
                            <span class="text-textsecondary">{{ tema.value }} ({{ tema.pct }}%)</span>
                        </div>
                        <div class="mt-2 h-2 w-full rounded-full bg-slate-200 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-cyan-600 to-blue-800" :style="{ width: `${tema.pct}%` }"></div>
                        </div>
                    </div>
                    <p v-if="topTema.length === 0" class="text-sm text-textsecondary font-bold">Belum ada data tema.</p>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 rounded-3xl border border-gray-300 bg-white p-7">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-black uppercase tracking-tight text-primary">Tren Dataset Tahunan</h2>
                    <span class="text-[10px] font-black uppercase tracking-widest text-textsecondary">
                        Total Tahun Ini: {{ totalTrenTahunIni }} | Window: {{ trendWindow }}
                    </span>
                </div>
                <div class="h-[330px]">
                    <LineChartTren :chartData="trenLineData" />
                </div>
            </div>

            <ValidationDoughnut
                :doughnutData="bidangDoughnutData"
                :validationData="{ total: stats.total_dataset || 0 }"
            />
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-3xl border border-gray-300 bg-white p-7">
                <h2 class="text-lg font-black uppercase tracking-tight text-primary mb-5">Frekuensi Pembaruan</h2>
                <div class="space-y-3">
                    <div
                        v-for="row in frekuensiRows"
                        :key="row.label"
                        class="rounded-2xl border border-slate-200 bg-slate-50 p-4"
                    >
                        <div class="flex justify-between items-center text-xs font-black uppercase">
                            <span class="text-primary">{{ row.label }}</span>
                            <span class="text-textsecondary">{{ row.value }}</span>
                        </div>
                        <div class="mt-2 h-2.5 w-full rounded-full bg-slate-200 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-cyan-600" :style="{ width: `${row.pct}%` }"></div>
                        </div>
                    </div>
                    <p v-if="!frekuensiRows.length" class="text-sm text-textsecondary font-bold">Belum ada data frekuensi.</p>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-300 bg-white p-7">
                <h2 class="text-lg font-black uppercase tracking-tight text-primary mb-5">Dataset Pilihan</h2>
                <div class="space-y-5">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-textsecondary mb-3">Populer</p>
                        <div class="space-y-2">
                            <div
                                v-for="item in topPopularDatasets"
                                :key="`popular-${item.id}`"
                                class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2"
                            >
                                <p class="text-xs font-black text-primary line-clamp-1">{{ item.title }}</p>
                                <div class="mt-1 flex items-center justify-between gap-3">
                                    <p class="text-[10px] font-bold text-textsecondary">{{ item.org }}</p>
                                    <span class="rounded-full bg-cyan-100 px-2 py-1 text-[9px] font-black uppercase tracking-wider text-cyan-800">
                                        {{ item.pin_count || 0 }} Pin
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-textsecondary mb-3">Terbaru</p>
                        <div class="space-y-2">
                            <div
                                v-for="item in topLatestDatasets"
                                :key="`latest-${item.id}`"
                                class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2"
                            >
                                <p class="text-xs font-black text-primary line-clamp-1">{{ item.title }}</p>
                                <p class="text-[10px] font-bold text-textsecondary mt-1">{{ item.org }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-gray-300 bg-white p-7">
            <h2 class="text-lg font-black uppercase tracking-tight text-primary mb-5">Topik Cepat</h2>
            <div class="flex flex-wrap gap-3">
                <span
                    v-for="topic in topics"
                    :key="topic.name"
                    class="px-4 py-2 rounded-xl border border-cyan-200 bg-cyan-50 text-cyan-800 text-[11px] font-black uppercase tracking-wider"
                >
                    {{ topic.name }}
                </span>
                <p v-if="!topics.length" class="text-sm text-textsecondary font-bold">Belum ada topik.</p>
            </div>
        </section>
    </div>
</template>
