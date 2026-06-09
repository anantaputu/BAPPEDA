<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref, watch, onMounted, onUnmounted } from 'vue';
import debounce from 'lodash/debounce';
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
    listTema: { type: Array, default: () => [] },
    listUrusan: { type: Array, default: () => [] },
    listBidang: { type: Array, default: () => [] },
    listFrekuensi: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const isFilterExpanded = ref(false);
const openDropdown = ref(null);
const temaLimit = ref(6);
const trendWindow = ref('12');

const form = ref({
    tema: props.filters?.tema || '',
    urusan: props.filters?.urusan || '',
    bidang: props.filters?.bidang || '',
    frekuensi: props.filters?.frekuensi || '',
});

const toggleDropdown = (name) => {
    openDropdown.value = openDropdown.value === name ? null : name;
};

const selectOption = (field, value) => {
    form.value[field] = value;
    openDropdown.value = null;
};

const getSelectedName = (list, id, fieldId, fieldName, defaultLabel) => {
    if (!list) return defaultLabel;
    const found = list.find(item => item[fieldId] == id);
    return found ? found[fieldName] : defaultLabel;
};

const performFilter = debounce(() => {
    const params = {};
    if (form.value.tema) params.tema = form.value.tema;
    if (form.value.urusan) params.urusan = form.value.urusan;
    if (form.value.bidang) params.bidang = form.value.bidang;
    if (form.value.frekuensi) params.frekuensi = form.value.frekuensi;

    router.get('/visualisasi', params, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 300);

watch(form, () => performFilter(), { deep: true });

const temaRows = computed(() =>
    (props.temaChart.labels || [])
        .map((label, idx) => ({
            label,
            value: props.temaChart.values?.[idx] || 0,
        }))
);

const filteredTemaRows = computed(() => {
    let rows = [...temaRows.value];
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
            borderRadius: 6,
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
const temaFocusLabel = computed(() => {
    if (!form.value.tema) return 'Semua Tema';
    const found = props.listTema.find(t => String(t.id_tema) === String(form.value.tema));
    return found ? found.nama_tema : 'Tema';
});

const resetFilters = () => {
    form.value = {
        tema: '',
        urusan: '',
        bidang: '',
        frekuensi: '',
    };
    temaLimit.value = 6;
    trendWindow.value = '12';
};

const closeOnOutsideClick = (e) => {
    if (!e.target.closest('.custom-select-container')) {
        openDropdown.value = null;
    }
};

onMounted(() => window.addEventListener('click', closeOnOutsideClick));
onUnmounted(() => window.removeEventListener('click', closeOnOutsideClick));
</script>

<template>
    <Head title="Ragam Visualisasi Data" />

    <div class="mx-auto max-w-[82%] space-y-8 pb-12 pt-24">
        <section class="ui-shell relative overflow-hidden border border-gray-400 bg-[linear-gradient(120deg,#001a3f_0%,#0b2e59_45%,#0ea5e9_130%)] p-8 text-white md:p-12">
            <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -left-16 -bottom-24 h-72 w-72 rounded-full bg-cyan-300/20 blur-3xl"></div>
            <h1 class="text-[2.375rem] font-black uppercase leading-[1.02] tracking-tight text-white md:text-[4.625rem]">
                Ragam Visualisasi
            </h1>
            <p class="mt-4 max-w-2xl text-[1rem] font-medium leading-7 text-white/80 md:text-[1.125rem]">
                Portal visual publik untuk membaca komposisi data pembangunan secara cepat tanpa masuk ke panel admin.
            </p>
            <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="border border-white/20 bg-white/10 p-4 backdrop-blur-sm" style="border-radius: var(--radius-panel);">
                    <p class="ui-eyebrow text-white/70">Total Dataset</p>
                    <p class="mt-2 text-[2.375rem] font-black leading-none text-white">{{ stats.total_dataset || 0 }}</p>
                </div>
                <div class="border border-white/20 bg-white/10 p-4 backdrop-blur-sm" style="border-radius: var(--radius-panel);">
                    <p class="ui-eyebrow text-white/70">Tema</p>
                    <p class="mt-2 text-[2.375rem] font-black leading-none text-white">{{ stats.total_tema || 0 }}</p>
                </div>
                <div class="border border-white/20 bg-white/10 p-4 backdrop-blur-sm" style="border-radius: var(--radius-panel);">
                    <p class="ui-eyebrow text-white/70">Bidang</p>
                    <p class="mt-2 text-[2.375rem] font-black leading-none text-white">{{ stats.total_bidang || 0 }}</p>
                </div>
                <div class="border border-white/20 bg-white/10 p-4 backdrop-blur-sm" style="border-radius: var(--radius-panel);">
                    <p class="ui-eyebrow text-white/70">Sumber Data</p>
                    <p class="mt-2 text-[2.375rem] font-black leading-none text-white">{{ stats.total_sumber || 0 }}</p>
                </div>
            </div>
        </section>

        <section class="ui-panel p-6 shadow-xl shadow-primary/5 bg-white border border-gray-400" style="border-radius: var(--radius-panel);">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-6">
                <div>
                    <h2 class="ui-title-sm">Kontrol Visualisasi</h2>
                    <p class="text-xs font-bold text-textsecondary mt-1">Saring dan sesuaikan data visualisasi pembangunan secara dinamis.</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <button @click="isFilterExpanded = !isFilterExpanded" 
                        class="px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest flex items-center justify-center gap-2 transition-all border border-gray-400 whitespace-nowrap"
                        :class="isFilterExpanded ? 'bg-secondary text-white border-secondary shadow-lg shadow-secondary/20' : 'bg-white text-primary hover:bg-bgsoft'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        {{ isFilterExpanded ? 'Tutup Filter' : 'Filter Lanjutan' }}
                    </button>

                    <button @click="resetFilters"
                        class="px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest bg-white border border-gray-400 text-textsecondary hover:bg-slate-50 transition-all">
                        Reset Filter
                    </button>
                </div>
            </div>

            <!-- Panel Filter Lanjutan (Expandable) -->
            <transition 
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="transform opacity-0 -translate-y-4"
                enter-to-class="transform opacity-100 translate-y-0"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="transform opacity-100 translate-y-0"
                leave-to-class="transform opacity-0 -translate-y-4">
                
                <div v-if="isFilterExpanded" class="border-t border-gray-200 pt-6 mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div v-for="filter in [
                            { name: 'urusan', list: listUrusan, key: 'id_urusan', label: 'Semua Urusan', display: 'nama_urusan', title: 'Urusan' },
                            { name: 'bidang', list: listBidang, key: 'id_bidang', label: 'Semua Bidang', display: 'nama_bidang', title: 'Bidang' },
                            { name: 'tema', list: listTema, key: 'id_tema', label: 'Semua Tema', display: 'nama_tema', title: 'Tema' },
                            { name: 'frekuensi', list: listFrekuensi, key: 'id_frekuensi', label: 'Semua Frekuensi', display: 'nama_frekuensi', title: 'Frekuensi' }
                        ]" :key="filter.name" class="relative custom-select-container">
                            
                            <label class="block text-[10px] font-black text-primary/40 uppercase tracking-[0.2em] mb-3 ml-1">{{ filter.title }}</label>
                            
                            <div @click="toggleDropdown(filter.name)"
                                class="w-full px-5 py-3 bg-white border border-gray-400 rounded-xl font-bold text-primary flex justify-between items-center cursor-pointer hover:border-secondary transition-all text-xs"
                                :class="{'ring-2 ring-secondary border-transparent shadow-lg': openDropdown === filter.name}">
                                <span class="truncate text-[11px] uppercase tracking-tight">{{ getSelectedName(filter.list, form[filter.name], filter.key, filter.display, filter.label) }}</span>
                                <svg class="w-4 h-4 text-secondary transition-transform duration-300" :class="{'rotate-180': openDropdown === filter.name}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            <div v-if="openDropdown === filter.name" class="absolute z-50 w-full mt-2 bg-white border border-gray-400 rounded-xl py-2 max-h-64 overflow-y-auto shadow-2xl">
                                <div @click="selectOption(filter.name, '')" class="px-5 py-2.5 hover:bg-bgsoft cursor-pointer text-[10px] text-textsecondary uppercase font-black tracking-widest">{{ filter.label }}</div>
                                <div v-for="item in filter.list" :key="item[filter.key]" @click="selectOption(filter.name, item[filter.key])"
                                     class="px-5 py-2.5 hover:bg-bgsoft cursor-pointer text-[11px] font-bold transition-colors border-l-4 border-transparent"
                                     :class="{'text-secondary bg-secondary/5 border-secondary': form[filter.name] == item[filter.key]}">
                                    {{ item[filter.display] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- Panel Pengaturan Chart (Local Settings) -->
            <div class="border-t border-gray-100 pt-6 mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-primary/40 uppercase tracking-[0.2em] mb-1 ml-1">Jumlah Tema Ditampilkan</label>
                    <select v-model="temaLimit" class="w-full border border-gray-400 px-4 py-3 text-xs font-bold text-primary bg-white rounded-xl focus:border-secondary focus:outline-none transition-all">
                        <option :value="4">Top 4 Tema</option>
                        <option :value="6">Top 6 Tema</option>
                        <option :value="8">Top 8 Tema</option>
                        <option :value="12">Top 12 Tema</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-primary/40 uppercase tracking-[0.2em] mb-1 ml-1">Jendela Tren Bulanan</label>
                    <select v-model="trendWindow" class="w-full border border-gray-400 px-4 py-3 text-xs font-bold text-primary bg-white rounded-xl focus:border-secondary focus:outline-none transition-all">
                        <option value="3">3 bulan terakhir</option>
                        <option value="6">6 bulan terakhir</option>
                        <option value="12">12 bulan terakhir</option>
                        <option value="all">Semua data</option>
                    </select>
                </div>
            </div>

            <!-- Chips Filter Terpasang -->
            <div class="mt-6 flex flex-wrap gap-2 items-center text-xs">
                <span class="text-[10px] font-black text-textsecondary uppercase tracking-[0.2em] ml-1">Kondisi Aktif:</span>
                
                <span class="ui-chip bg-slate-100 px-3 py-1.5 text-textsecondary rounded-lg font-bold border border-slate-200">
                    Tema: {{ temaFocusLabel }}
                </span>
                
                <span v-if="form.urusan" class="ui-chip bg-slate-100 px-3 py-1.5 text-textsecondary rounded-lg font-bold border border-slate-200">
                    Urusan: {{ getSelectedName(listUrusan, form.urusan, 'id_urusan', 'nama_urusan', '') }}
                </span>

                <span v-if="form.bidang" class="ui-chip bg-slate-100 px-3 py-1.5 text-textsecondary rounded-lg font-bold border border-slate-200">
                    Bidang: {{ getSelectedName(listBidang, form.bidang, 'id_bidang', 'nama_bidang', '') }}
                </span>

                <span v-if="form.frekuensi" class="ui-chip bg-slate-100 px-3 py-1.5 text-textsecondary rounded-lg font-bold border border-slate-200">
                    Frekuensi: {{ getSelectedName(listFrekuensi, form.frekuensi, 'id_frekuensi', 'nama_frekuensi', '') }}
                </span>

                <span class="ui-chip bg-slate-100 px-3 py-1.5 text-textsecondary rounded-lg font-bold border border-slate-200">
                    Limit Tema: Top {{ temaLimit }}
                </span>

                <span class="ui-chip bg-slate-100 px-3 py-1.5 text-textsecondary rounded-lg font-bold border border-slate-200">
                    Tren: {{ trendWindow === 'all' ? 'Semua Data' : `${trendWindow} Bulan` }}
                </span>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2">
                <BarChartIndikator
                    :key="JSON.stringify(temaBarData)"
                    title="Distribusi Dataset per Tema"
                    :chartData="temaBarData"
                    xAxisLabel="Tema Pembangunan"
                    yAxisLabel="Jumlah Dataset"
                />
            </div>

            <div class="ui-panel p-7 border border-gray-400 bg-white" style="border-radius: var(--radius-panel);">
                <h2 class="ui-title-sm mb-5">Peringkat Tema</h2>
                <div class="space-y-3">
                    <div
                        v-for="(tema, index) in topTema"
                        :key="tema.label"
                        class="ui-surface p-3"
                        style="border-radius: var(--radius-soft);"
                    >
                        <div class="flex items-center justify-between text-[0.82rem] font-black uppercase">
                            <span class="tracking-[0.08em] text-primary">{{ index + 1 }}. {{ tema.label }}</span>
                            <span class="tracking-[0.08em] text-textsecondary">{{ tema.value }} ({{ tema.pct }}%)</span>
                        </div>
                        <div class="mt-2 h-2 w-full rounded-full bg-slate-200 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-cyan-600 to-blue-800" :style="{ width: `${tema.pct}%` }"></div>
                        </div>
                    </div>
                    <p v-if="topTema.length === 0" class="ui-body">Belum ada data tema.</p>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="ui-panel p-7 lg:col-span-2 border border-gray-400 bg-white" style="border-radius: var(--radius-panel);">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="ui-title-sm">Tren Dataset Tahunan</h2>
                    <span class="ui-eyebrow">
                        Total Tahun Ini: {{ totalTrenTahunIni }} | Window: {{ trendWindow }}
                    </span>
                </div>
                <div class="h-[330px]">
                    <LineChartTren
                        :key="JSON.stringify(trenLineData)"
                        :chartData="trenLineData"
                        xAxisLabel="Periode Bulanan"
                        yAxisLabel="Jumlah Dataset Baru"
                    />
                </div>
            </div>

            <ValidationDoughnut
                :key="JSON.stringify(bidangDoughnutData)"
                :doughnutData="bidangDoughnutData"
                :validationData="{ total: stats.total_dataset || 0 }"
                categoryLabel="Bidang"
                valueLabel="Jumlah Dataset"
            />
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="ui-panel p-7 border border-gray-400 bg-white" style="border-radius: var(--radius-panel);">
                <h2 class="ui-title-sm mb-5">Frekuensi Pembaruan</h2>
                <div class="space-y-3">
                    <div
                        v-for="row in frekuensiRows"
                        :key="row.label"
                        class="ui-surface p-4"
                        style="border-radius: var(--radius-soft);"
                    >
                        <div class="flex items-center justify-between text-[0.82rem] font-black uppercase">
                            <span class="tracking-[0.08em] text-primary">{{ row.label }}</span>
                            <span class="tracking-[0.08em] text-textsecondary">{{ row.value }}</span>
                        </div>
                        <div class="mt-2 h-2.5 w-full rounded-full bg-slate-200 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-cyan-600" :style="{ width: `${row.pct}%` }"></div>
                        </div>
                    </div>
                    <p v-if="!frekuensiRows.length" class="ui-body">Belum ada data frekuensi.</p>
                </div>
            </div>

            <div class="ui-panel p-7 border border-gray-400 bg-white" style="border-radius: var(--radius-panel);">
                <h2 class="ui-title-sm mb-5">Dataset Pilihan</h2>
                <div class="space-y-5">
                    <div>
                        <p class="ui-eyebrow mb-3">Populer</p>
                        <div class="space-y-2">
                            <div
                                v-for="item in topPopularDatasets"
                                :key="`popular-${item.id}`"
                                class="ui-surface px-3 py-3"
                                style="border-radius: var(--radius-soft);"
                            >
                                <p class="text-[0.92rem] font-black leading-snug text-primary line-clamp-1">{{ item.title }}</p>
                                <div class="mt-1 flex items-center justify-between gap-3">
                                    <p class="text-[0.82rem] font-bold text-textsecondary">{{ item.org }}</p>
                                    <span class="ui-chip border-transparent bg-cyan-100 px-2 py-1 text-cyan-800">
                                        {{ item.pin_count || 0 }} Pin
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="ui-eyebrow mb-3">Terbaru</p>
                        <div class="space-y-2">
                            <div
                                v-for="item in topLatestDatasets"
                                :key="`latest-${item.id}`"
                                class="ui-surface px-3 py-3"
                                style="border-radius: var(--radius-soft);"
                            >
                                <p class="text-[0.92rem] font-black leading-snug text-primary line-clamp-1">{{ item.title }}</p>
                                <p class="mt-1 text-[0.82rem] font-bold text-textsecondary">{{ item.org }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="ui-panel p-7 border border-gray-400 bg-white" style="border-radius: var(--radius-panel);">
            <h2 class="ui-title-sm mb-5">Topik Cepat</h2>
            <div class="flex flex-wrap gap-3">
                <span
                    v-for="topic in topics"
                    :key="topic.name"
                    class="ui-chip border-cyan-200 bg-cyan-50 px-4 py-2 text-cyan-800"
                    style="border-radius: var(--radius-soft);"
                >
                    {{ topic.name }}
                </span>
                <p v-if="!topics.length" class="ui-body">Belum ada topik.</p>
            </div>
        </section>
    </div>
</template>
