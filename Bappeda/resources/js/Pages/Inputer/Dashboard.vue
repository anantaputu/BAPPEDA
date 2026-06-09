<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

import StatCard from '@/Components/Dashboard/StatCard.vue';
import ActivityLog from '@/Components/Dashboard/ActivityLog.vue';
import GrowthLineChart from '@/Components/Dashboard/GrowthLineChart.vue';

// Import ChartJS
import { Line } from 'vue-chartjs';
import { 
    Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, 
    Title, Tooltip, Legend, Filler 
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler);

defineOptions({ layout: AppLayout });

const props = defineProps({
    auth: Object,
    stats: Object,
    growthChart: {
        type: Object,
        default: () => ({ labels: [], values: [] })
    },
    myRecentActivities: Array,
    allData: {
        type: Array,
        default: () => []
    },
    pinnedData: {
        type: Array,
        default: () => [] 
    },
    temaDistribution: {
        type: Object,
        default: () => ({ labels: [], values: [] })
    },
    frekuensiDistribution: {
        type: Object,
        default: () => ({ labels: [], values: [] })
    },
    recentMyData: {
        type: Array,
        default: () => []
    },
});
const activeView = ref('Grafik'); 
const views = ['Grafik', 'Tabel Data'];

const colors = {
    navy: { bg: 'bg-primary/5', text: 'text-primary', bar: 'bg-secondary' },
};

const temaDistributionBars = computed(() => {
    const labels = props.temaDistribution?.labels || [];
    const values = props.temaDistribution?.values || [];
    const max = Math.max(...values, 1);
    return labels.map((label, idx) => ({
        label,
        value: values[idx] || 0,
        percent: Math.round(((values[idx] || 0) / max) * 100),
    })).slice(0, 4);
});

const frekuensiDistributionBars = computed(() => {
    const labels = props.frekuensiDistribution?.labels || [];
    const values = props.frekuensiDistribution?.values || [];
    const max = Math.max(...values, 1);
    return labels.map((label, idx) => ({
        label,
        value: values[idx] || 0,
        percent: Math.round(((values[idx] || 0) / max) * 100),
    })).slice(0, 4);
});

// ========================================================
// 1. LOGIKA FILTER SATUAN DINAMIS
// ========================================================
const getSatuanFromRow = (row) => {
    if (!row) return '-';
    const key = Object.keys(row).find(k => k.toLowerCase().trim() === 'satuan');
    return row[key] || '-';
};

const availableSatuans = computed(() => {
    if (!props.allData || props.allData.length === 0) return [];
    const units = props.allData.map(row => getSatuanFromRow(row).toUpperCase());
    return [...new Set(units)].filter(u => u !== '-');
});

const activeSatuan = ref(null);
const activeTahunTerbit = ref('Semua');

// Pastikan activeSatuan terisi saat data tersedia
watch(() => availableSatuans.value, (newSatuans) => {
    if (newSatuans.length > 0 && !activeSatuan.value) {
        activeSatuan.value = newSatuans[0];
    }
}, { immediate: true });

// [PERBAIKAN] Ambil daftar Tahun Terbit secara dinamis
const availableTahunTerbit = computed(() => {
    // Selalu kembalikan minimal ['Semua'] agar tombol minimal muncul satu
    const defaultOption = ['Semua'];
    if (!props.allData || props.allData.length === 0) return defaultOption;
    
    // Ambil tahun_terbit, bersihkan null/undefined
    const years = props.allData
        .map(row => row.tahun_terbit)
        .filter(y => y !== null && y !== undefined && y !== '');
    
    // Ambil yang unik saja
    const uniqueYears = [...new Set(years)];
    
    // Urutkan angka (Terbaru ke Terlama)
    uniqueYears.sort((a, b) => b - a);
    
    return [...defaultOption, ...uniqueYears];
});

// [PERBAIKAN] Filter Data dengan String Comparison agar tipe data Int/String tidak masalah
const filteredData = computed(() => {
    if (!activeSatuan.value) return [];
    
    return props.allData.filter(row => {
        // 1. Filter Satuan
        const matchSatuan = getSatuanFromRow(row).toUpperCase() === activeSatuan.value.toUpperCase();
        
        // 2. Filter Tahun Terbit (Gunakan String untuk perbandingan aman)
        const matchTahun = activeTahunTerbit.value === 'Semua' || 
                           String(row.tahun_terbit) === String(activeTahunTerbit.value);
                           
        return matchSatuan && matchTahun;
    });
});
// ... kode timeColumns dan chartConfig tetap sama ...
// ========================================================
// 2. LOGIKA GRAFIK & TABEL 
// ========================================================
const selectedIndices = ref([0]); 
const chartColors = ['#00139E', '#FF1414', '#00D2FC', '#F8B400', '#54D62C', '#9D4EDD', '#FF6B6B'];

// [BARU] STATE UNTUK FILTER FREKUENSI GRAFIK
const activeChartFreq = ref('Tahunan'); 
const chartFreqOptions = ['Tahunan', 'Bulanan', 'Lainnya', 'Semua'];

watch(activeSatuan, () => {
    if (filteredData.value.length > 0) {
        selectedIndices.value = [0];
    } else {
        selectedIndices.value = [];
    }
});

const extraFieldKeys = computed(() => {
    if (!filteredData.value || filteredData.value.length === 0) return [];
    let keys = new Set();
    const excludeKeys = ['nama data', 'nama_data', 'nama indikator', 'nama_indikator', 'uraian', 'indikator'];
    filteredData.value.forEach(row => {
        if (row.informasi_tambahan) {
            let extras = row.informasi_tambahan;
            if (typeof extras === 'string') {
                try { extras = JSON.parse(extras); } catch(e) {}
            }
            if (typeof extras === 'object' && extras !== null) {
                Object.keys(extras).forEach(k => {
                    const cleanK = k.toLowerCase().trim();
                    if (!excludeKeys.includes(cleanK)) {
                        keys.add(k);
                    }
                });
            }
        }
    });
    return Array.from(keys);
});

// Helper Format Waktu (Agar Januari/Kondisi Awal tampil rapi)
const formatTimeHeader = (timeString) => {
    if (!timeString) return '-';
    let str = String(timeString).trim();
    if (/^\d{4}-\d{2}-\d{2}$/.test(str)) {
        return new Date(str).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }
    if (/^\d{4}-\d{2}$/.test(str)) {
        const [year, month] = str.split('-');
        return new Date(year, month - 1).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }); 
    }
    return str.replace(/\b\w/g, char => char.toUpperCase());
};

const timeColumns = computed(() => {
    if (!filteredData.value || filteredData.value.length === 0) return [];
    
    // Daftar field yang harus dibuang dari header kolom waktu
    const excludeKeys = [
        'nama_data', 'nama indikator', 'nama data', 'uraian', 'indikator', 
        'satuan', 'id_data', 'informasi_tambahan', 'tahun_terbit', 'id_user', 
        'id_tema', 'id_urusan', 'id_bidang', 'id_frekuensi', 'status', 'tahun'
    ];

    let cols = new Set();
    filteredData.value.forEach(row => {
        Object.keys(row).forEach(key => {
            const cleanKey = key.toLowerCase().trim();
            // Jangan masukkan extra_fields atau metadata ke kolom waktu
            if (!excludeKeys.includes(cleanKey) && !extraFieldKeys.value.includes(key)) {
                cols.add(key);
            }
        });
    });

    const monthOrder = {
        'januari': 1, 'februari': 2, 'maret': 3, 'april': 4, 'mei': 5, 'juni': 6,
        'juli': 7, 'agustus': 8, 'september': 9, 'oktober': 10, 'november': 11, 'desember': 12,
    };

    return Array.from(cols).sort((a, b) => {
        const strA = a.toLowerCase().trim();
        const strB = b.toLowerCase().trim();

        // Urutkan angka tahun murni (2024, 2025)
        if (!isNaN(strA) && !isNaN(strB)) return parseInt(strA) - parseInt(strB);

        // Urutkan Bulan (Januari -> Desember)
        let mIndexA = 99; let mIndexB = 99;
        for (let m in monthOrder) {
            if (strA.includes(m)) mIndexA = monthOrder[m];
            if (strB.includes(m)) mIndexB = monthOrder[m];
        }

        if (mIndexA !== mIndexB) return mIndexA - mIndexB;
        return strA.localeCompare(strB);
    });
});

// [BARU] FILTER LABEL UNTUK GRAFIK BERDASARKAN FREKUENSI
const filteredChartLabels = computed(() => {
    if (!timeColumns.value) return [];

    return timeColumns.value.filter(col => {
        const str = String(col).trim().toLowerCase();
        const isTahun = /^\d{4}$/.test(str); 
        
        const monthNames = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des'];
        const isBulan = monthNames.some(m => str.includes(m)) || /^\d{4}-\d{2}$/.test(str);

        if (activeChartFreq.value === 'Tahunan') return isTahun;
        if (activeChartFreq.value === 'Bulanan') return isBulan && !isTahun;
        if (activeChartFreq.value === 'Lainnya') return !isTahun && !isBulan;
        return true; 
    });
});

const chartConfig = computed(() => {
    if (!filteredData.value || filteredData.value.length === 0) return null;

    // Ganti yearKeys menjadi label yang sudah difilter
    const labels = filteredChartLabels.value; 
    if (selectedIndices.value.length === 0 || labels.length === 0) return { labels: [], datasets: [] };

    const datasets = selectedIndices.value.map((rowIndex, colorIdx) => {
        const row = filteredData.value[rowIndex];
        if(!row) return null;

        const nameKey = Object.keys(row).find(k => 
            ['nama_data', 'nama indikator', 'nama data', 'uraian', 'indikator'].includes(k.toLowerCase().trim())
        ) || Object.keys(row)[0];

        const labelName = row[nameKey] || `Data ${rowIndex + 1}`;
        const rowSatuan = getSatuanFromRow(row);

        const dataValues = labels.map(timeKey => {
            let val = row[timeKey];
            if (typeof val === 'string') val = val.replace(',', '.'); // Handle koma desimal
            return (val === undefined || val === null || val === '') ? null : parseFloat(val);
        });

        const color = chartColors[colorIdx % chartColors.length];

        return {
            label: labelName,
            data: dataValues,
            unit: rowSatuan,
            borderColor: color,
            backgroundColor: color,
            borderWidth: 3,
            pointBackgroundColor: '#fff',
            pointBorderColor: color,
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            tension: 0.4, 
            fill: false 
        };
    }).filter(d => d !== null);

    const formattedLabels = labels.map(l => formatTimeHeader(l));

    return { labels: formattedLabels, datasets: datasets };
});

const toggleSelection = (index) => {
    if (selectedIndices.value.includes(index)) {
        if (selectedIndices.value.length > 1) { 
            selectedIndices.value = selectedIndices.value.filter(i => i !== index);
        }
    } else {
        selectedIndices.value.push(index);
    }
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    plugins: {
        legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: { size: 10, weight: 'bold' } } }, 
        tooltip: { 
            backgroundColor: '#000B58', titleFont: { size: 13 }, bodyFont: { size: 12 }, padding: 10, cornerRadius: 8, 
            callbacks: {
                label: function(context) {
                    let label = context.dataset.label || '';
                    if (label) label += ': ';
                    if (context.parsed.y !== null) {
                        label += context.parsed.y + ' ' + (context.dataset.unit || '');
                    }
                    return label;
                }
            }
        }
    },
    scales: {
        y: {
            title: {
                display: true,
                text: 'Nilai Data',
                color: '#000B58',
                font: { size: 12, weight: 'bold' }
            },
            grid: { color: '#f3f4f6', borderDash: [5, 5] },
            ticks: { font: { size: 10 }, color: '#9ca3af' }
        },
        x: {
            title: {
                display: true,
                text: 'Periode Data',
                color: '#000B58',
                font: { size: 12, weight: 'bold' }
            },
            grid: { display: false },
            ticks: { font: { size: 11, weight: 'bold' }, color: '#000B58' }
        }
    }
};

// ========================================================
// 3. UPDATE STATS CARDS 
// ========================================================
const statsCards = computed(() => [
    { label: 'Total Indikator Saya', value: props.stats.total_indikator || 0, icon: 'solar:database-bold', color: 'navy' },
    { label: 'Total Upload Saya', value: props.stats.total_upload || 0, icon: 'solar:upload-square-bold', color: 'navy' },
    { label: 'Total Nilai Data', value: props.stats.total_nilai || 0, icon: 'solar:chart-square-bold', color: 'navy' },
    { label: 'Cakupan Tema', value: props.stats.cakupan_tema || 0, icon: 'solar:tag-bold', color: 'navy' },
]);
</script>

<template>
    <Head title="Dashboard Inputer" />

    <div class="space-y-6">
        <div class="mb-5 flex flex-col justify-between md:flex-row md:items-center">
            <div>
                <h1 class="ui-title-md">Dashboard Inputer</h1>
                <p class="ui-body mt-2">Monitoring performa, tren, dan rekap indikator Anda dengan tampilan yang lebih ringkas.</p>
            </div>
        </div>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard 
                v-for="(stat, index) in statsCards" 
                :key="index" 
                v-bind="stat" 
                :colors="colors"
                class="!rounded-xl border border-gray-400 bg-white"
            />
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="ui-panel p-6 xl:col-span-2 border border-gray-400 bg-white" style="border-radius: var(--radius-panel);">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <h3 class="ui-eyebrow border-l-4 border-secondary pl-4">Ringkasan Sebaran Data Saya</h3>
                    <span class="ui-chip border-slate-200 bg-slate-50 px-3 py-2 text-textsecondary" style="border-radius: var(--radius-soft);">
                        {{ props.stats.variasi_frekuensi || 0 }} Frekuensi
                    </span>
                </div>
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <p class="ui-eyebrow mb-3">Tema Utama</p>
                        <div class="space-y-3">
                            <div v-for="(item, idx) in temaDistributionBars" :key="`tema-${idx}`">
                                <div class="mb-1 flex items-center justify-between text-[0.76rem] font-black uppercase tracking-[0.14em] text-textsecondary">
                                    <span class="truncate">{{ item.label }}</span>
                                    <span class="text-primary">{{ item.value }}</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-bgsoft">
                                    <div class="h-full rounded-full bg-secondary" :style="{ width: `${item.percent}%` }"></div>
                                </div>
                            </div>
                            <p v-if="temaDistributionBars.length === 0" class="ui-body">Belum ada data tema.</p>
                        </div>
                    </div>
                    <div>
                        <p class="ui-eyebrow mb-3">Frekuensi Teratas</p>
                        <div class="space-y-3">
                            <div v-for="(item, idx) in frekuensiDistributionBars" :key="`freq-${idx}`">
                                <div class="mb-1 flex items-center justify-between text-[0.76rem] font-black uppercase tracking-[0.14em] text-textsecondary">
                                    <span class="truncate">{{ item.label }}</span>
                                    <span class="text-primary">{{ item.value }}</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-bgsoft">
                                    <div class="h-full rounded-full bg-primary" :style="{ width: `${item.percent}%` }"></div>
                                </div>
                            </div>
                            <p v-if="frekuensiDistributionBars.length === 0" class="ui-body">Belum ada data frekuensi.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ui-panel p-6 border border-gray-400 bg-white" style="border-radius: var(--radius-panel);">
                <h3 class="ui-eyebrow border-l-4 border-secondary pl-4">Ringkasan Bulan Ini</h3>
                <div class="mt-6 space-y-4">
                    <div class="ui-surface p-4" style="border-radius: var(--radius-soft);">
                        <p class="ui-eyebrow">Indikator Baru</p>
                        <p class="mt-2 text-[2rem] font-black leading-none text-primary">{{ props.stats.input_bulan_ini || 0 }}</p>
                    </div>
                    <div class="ui-surface p-4" style="border-radius: var(--radius-soft);">
                        <p class="ui-eyebrow">Upload Data</p>
                        <p class="mt-2 text-[2rem] font-black leading-none text-primary">{{ props.stats.upload_bulan_ini || 0 }}</p>
                    </div>
                    <div class="ui-surface p-4" style="border-radius: var(--radius-soft);">
                        <p class="ui-eyebrow">Sumber Data Saya</p>
                        <p class="mt-2 text-[2rem] font-black leading-none text-primary">{{ props.stats.sumber_data || 0 }}</p>
                    </div>
                </div>
            </div>
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
<section class="ui-panel mb-2 flex flex-col items-center justify-between gap-6 p-6 md:flex-row border border-gray-400 bg-white" style="border-radius: var(--radius-panel);">
    <div class="flex w-full flex-wrap gap-6 md:w-auto">
        <div>
            <p class="ui-eyebrow mb-3 ml-2">Satuan</p>
            <div class="flex flex-wrap gap-2">
                <button v-for="satuan in availableSatuans" :key="satuan" 
                    @click="activeSatuan = satuan"
                    class="ui-chip px-5 py-2.5 transition-all"
                    style="border-radius: var(--radius-soft);"
                    :class="activeSatuan === satuan ? 'bg-primary text-white shadow-lg' : 'bg-bgsoft text-textsecondary border border-gray-200'">
                    {{ satuan }}
                </button>
            </div>
        </div>

            <div>
    <p class="ui-eyebrow mb-3 ml-2">Tahun Terbit</p>
    <div class="flex flex-wrap gap-2">
        <button v-for="tahun in availableTahunTerbit" :key="tahun" 
            @click="activeTahunTerbit = tahun"
            class="ui-chip px-5 py-2.5 transition-all"
            style="border-radius: var(--radius-soft);"
            :class="activeTahunTerbit === tahun 
                ? 'bg-secondary text-white shadow-lg shadow-secondary/20' 
                : 'bg-bgsoft text-textsecondary border border-gray-200 hover:bg-secondary/10'">
            {{ tahun }}
        </button>
    </div>
</div>
</div>

    <div class="flex rounded-xl border border-gray-300 bg-bgsoft p-1.5" style="border-radius: var(--radius-soft);">
        <button v-for="view in views" :key="view" @click="activeView = view"
            class="px-8 py-3 text-[0.82rem] font-black uppercase tracking-[0.14em] transition-all"
            style="border-radius: 0.7rem;"
            :class="activeView === view ? 'bg-white text-primary shadow-sm' : 'text-textsecondary hover:text-primary'">
            {{ view }}
        </button>
    </div>
</section>
        <section v-if="activeView === 'Grafik'" class="grid lg:grid-cols-4 gap-6 items-start animate-in fade-in duration-500">
            <div class="lg:col-span-3 bg-white border border-gray-400 p-8 rounded-xl shadow-sm relative overflow-hidden flex flex-col h-full min-h-[550px]">
                
                <div class="flex flex-wrap justify-between items-end mb-6 gap-4">
                    <div>
                        <h4 class="text-lg font-black text-primary uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                            Tren Indikator
                        </h4>
                        <p class="text-xs text-textsecondary font-medium mt-1">Membandingkan {{ selectedIndices.length }} indikator bersatuan {{ activeSatuan }}</p>
                    </div>

                    <div class="flex bg-bgsoft p-1 rounded-xl border border-gray-300">
                        <button v-for="freq in chartFreqOptions" :key="freq" 
                            @click="activeChartFreq = freq"
                            class="px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all"
                            :class="activeChartFreq === freq ? 'bg-white text-primary shadow-sm border border-gray-100' : 'text-textsecondary hover:text-primary'">
                            {{ freq }}
                        </button>
                    </div>
                </div>

                <div class="relative w-full flex-1 bg-bgsoft/30 rounded-xl border border-gray-100 p-4">
                    <Line v-if="chartConfig && chartConfig.datasets.length > 0 && chartConfig.labels.length > 0" :data="chartConfig" :options="chartOptions" />
                    
                    <div v-else class="flex flex-col items-center justify-center h-full text-center">
                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-sm font-black text-gray-400 uppercase tracking-widest">Data Tidak Lengkap</p>
                        <p class="text-[10px] text-gray-400 mt-1">Ganti filter frekuensi waktu di pojok kanan atas, atau pilih indikator lain di samping.</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 bg-white border border-gray-400 p-6 rounded-xl shadow-sm flex flex-col h-[550px]">
                <h4 class="text-sm font-black text-primary uppercase tracking-widest mb-4 flex items-center gap-2 pb-4 border-b border-gray-100">
                    <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                    Pilih Indikator
                </h4>

                <div v-if="filteredData.length > 0" class="overflow-y-auto pr-2 space-y-3 custom-scrollbar flex-1">
                    <div v-for="(row, index) in filteredData" :key="index" @click="toggleSelection(index)"
                        class="p-4 rounded-[1.25rem] border transition-all cursor-pointer group flex items-start gap-3"
                        :class="selectedIndices.includes(index) ? 'bg-secondary/10 border-secondary/20 shadow-sm' : 'bg-white border-gray-100 hover:border-gray-300'">
                        
                        <div class="mt-0.5 w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all duration-300 shrink-0"
                            :class="selectedIndices.includes(index) ? 'bg-[#00139E] border-[#00139E]' : 'bg-white border-gray-200'">
                            <svg v-if="selectedIndices.includes(index)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] font-black text-gray-700 leading-tight group-hover:text-primary transition-colors uppercase tracking-wide line-clamp-2">
                                {{ row.nama_data || row['Nama Indikator'] || Object.values(row)[0] }}
                            </p>
                            <div v-if="selectedIndices.includes(index)" class="mt-2 h-1 w-full rounded-full overflow-hidden bg-gray-100">
                                <div class="h-full" :style="{ backgroundColor: chartColors[selectedIndices.indexOf(index) % chartColors.length], width: '40%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-else class="flex-1 flex flex-col items-center justify-center text-center p-4 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <p class="text-[10px] font-bold text-gray-400 italic">Data untuk satuan ini kosong.</p>
                </div>
            </div>
        </section>

        <section v-if="activeView === 'Tabel Data'" class="animate-in slide-in-from-bottom-4 duration-500">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-extrabold text-primary uppercase tracking-wider">Rekapitulasi Data ({{ activeSatuan }})</h4>
                    <p class="text-xs text-textsecondary font-medium mt-1">Geser ke samping untuk melihat nilai per tahun.</p>
                </div>
                <span class="px-3 py-1 rounded bg-secondary/10 text-secondary border border-secondary/20 text-[10px] font-bold uppercase tracking-wider">
                    Total: {{ filteredData.length }} Indikator
                </span>
            </div>
            
            <div class="border border-gray-400 rounded-2xl overflow-hidden shadow-sm bg-white">
                <div class="overflow-x-auto custom-scrollbar" v-if="filteredData.length > 0">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="bg-slate-50 border-b border-gray-400">
                                <th class="p-4 border-r border-gray-400 text-xs font-bold text-textsecondary uppercase tracking-wider sticky left-0 z-20 min-w-[280px] bg-slate-50 shadow-sm">
                                    Nama Indikator
                                </th>
                                
                                <th v-for="key in extraFieldKeys" :key="'th-'+key" class="p-4 border-r border-gray-400 text-xs font-bold text-textsecondary uppercase tracking-wider text-center bg-slate-50">
                                    {{ key }}
                                </th>

                                <th v-for="year in timeColumns" :key="year" class="p-4 border-r border-gray-400 min-w-[120px] text-center text-xs font-bold text-primary uppercase tracking-wider">
                                    {{ formatTimeHeader(year) }}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-400">
                            <tr v-for="(row, idx) in filteredData" :key="idx" class="hover:bg-slate-50/50 transition-colors group">
                                <td class="p-4 bg-white border-r border-gray-400 font-bold text-primary text-sm sticky left-0 z-10 shadow-sm group-hover:bg-slate-50/50">
                                    <div class="line-clamp-2 w-[280px]">
                                        {{ idx + 1 }}. {{ row.nama_data || row['Nama Indikator'] || 'Data' }}
                                    </div>
                                </td>

                                <td v-for="key in extraFieldKeys" :key="'td-'+key" class="p-4 border-r border-gray-400 text-xs font-semibold text-textsecondary text-center group-hover:bg-slate-50/30">
                                    {{ row[key] || '-' }}
                                </td>

                                <td v-for="year in timeColumns" :key="year" class="p-4 border-r border-gray-400 text-center group-hover:bg-slate-50/30">
                                    <span class="text-sm font-bold text-primary group-hover:text-secondary transition-colors">
                                        {{ row[year] !== undefined && row[year] !== null && row[year] !== '' ? row[year] : '-' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class="p-16 text-center bg-slate-50">
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">Tidak ada data untuk satuan ini.</p>
                </div>
            </div>
        </section>

        <section class="w-full">
            <GrowthLineChart
                :chartData="growthChart"
                xAxisLabel="Periode Bulanan"
                yAxisLabel="Jumlah Upload Baru"
            />
        </section>

        <div class="w-full mt-10">
            <ActivityLog title="Riwayat Aktivitas Saya" :activities="props.myRecentActivities" />
        </div>
        
    </div>
</template>
<style scoped>
div { transition: width 0.3s ease; }

.custom-scrollbar::-webkit-scrollbar { height: 10px; width: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #EEF2F5; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; border: 2px solid #EEF2F5; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #1F3A63; }

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    white-space: normal;
}
</style>
