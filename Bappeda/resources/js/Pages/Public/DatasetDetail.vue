<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Line } from 'vue-chartjs';
import { 
    Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, 
    Title, Tooltip, Legend, Filler 
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler);

const page = usePage();
const user = computed(() => page.props.auth?.user);
const canEdit = computed(() => {
    if (!user.value) return false;
    const roleName = user.value.role;
    const isAdmin = roleName === 'Admin' || roleName === 'Admin Super';
    return isAdmin || props.dataset?.id_user === user.value.id;
});

defineOptions({ layout: AppLayout });

const props = defineProps({
    dataset: Object,      
    tableData: Object,    
    allData: Array,       
    customSatuan: String,
    valueStats: Object,
    datasetMeta: Object,
});

const activeTab = ref('Data');
const tabs = ['Data', 'Relasi & Atribut', 'Infografis', 'Riwayat']; 
const selectedIndices = ref([0]); 
const chartColors = ['#0284C7', '#15803D', '#D97706', '#C53030', '#1F3A63', '#9D4EDD', '#FF6B6B'];

const tableRows = computed(() => {
    if (props.tableData && props.tableData.data) return props.tableData.data;
    if (Array.isArray(props.tableData)) return props.tableData;
    return props.allData || []; 
});

// Fallback to tableRows if allData is empty
const chartDataSrc = computed(() => {
    if (props.allData && props.allData.length > 0) return props.allData;
    return tableRows.value;
});

// Watch chartDataSrc to keep selectedIndices in bounds and auto-select the first row
watch(chartDataSrc, (newVal) => {
    if (newVal && newVal.length > 0) {
        const maxIdx = newVal.length - 1;
        const validSelections = selectedIndices.value.filter(idx => idx <= maxIdx);
        if (validSelections.length === 0) {
            selectedIndices.value = [0];
        } else {
            selectedIndices.value = validSelections;
        }
    }
}, { immediate: true });

const getSatuanFromRow = (row) => {
    if (!row) return '-';
    const key = Object.keys(row).find(k => k.toLowerCase().includes('satuan'));
    return row[key] || props.customSatuan || props.dataset?.satuan || '-';
};

const dynamicSatuan = computed(() => {
    const dataSrc = chartDataSrc.value;
    if (!dataSrc || dataSrc.length === 0 || selectedIndices.value.length === 0) return '-';
    const units = selectedIndices.value.map(idx => {
        const row = dataSrc[idx];
        return row ? getSatuanFromRow(row) : '-';
    });
    const uniqueUnits = [...new Set(units)];
    return uniqueUnits.length === 1 ? uniqueUnits[0] : 'Beragam';
});

const extraFieldKeys = computed(() => {
    if (!props.dataset?.informasi_tambahan) return [];
    let extras = props.dataset.informasi_tambahan;
    if (typeof extras === 'string') {
        try { extras = JSON.parse(extras); } catch(e) { return []; }
    }
    const excludeKeys = ['nama data', 'nama_data', 'nama indikator', 'nama_indikator', 'uraian', 'indikator'];
    return Object.keys(extras).filter(k => {
        const cleanK = k.toLowerCase().trim();
        return !excludeKeys.includes(cleanK);
    });
});

const timeColumns = computed(() => {
    const dataSrc = chartDataSrc.value;
    if (!dataSrc || dataSrc.length === 0) return [];
    const firstRow = dataSrc[0];
    const excludeKeys = [
        'nama indikator', 'nama_indikator', 'nama data', 'nama_data', 'uraian', 'indikator', 'satuan', 'id_data',
        ...extraFieldKeys.value.map(k => k.toLowerCase().trim())
    ];
    let cols = [];
    Object.keys(firstRow).forEach(key => {
        const cleanKey = key.toLowerCase().trim();
        if (!excludeKeys.includes(cleanKey)) cols.push(key);
    });
    const monthOrder = {
        'januari': 1, 'februari': 2, 'maret': 3, 'april': 4, 'mei': 5, 'juni': 6,
        'juli': 7, 'agustus': 8, 'september': 9, 'oktober': 10, 'november': 11, 'desember': 12,
    };
    return cols.sort((a, b) => {
        const strA = a.toLowerCase().trim();
        const strB = b.toLowerCase().trim();
        if (!isNaN(strA) && !isNaN(strB)) return parseInt(strA) - parseInt(strB);
        let monthA = 99; let monthB = 99;
        for (let m in monthOrder) {
            if (strA.includes(m)) monthA = monthOrder[m];
            if (strB.includes(m)) monthB = monthOrder[m];
        }
        if (monthA !== monthB) return monthA - monthB;
        return a.localeCompare(b);
    });
});

const chartConfig = computed(() => {
    const dataSrc = chartDataSrc.value;
    if (!dataSrc || dataSrc.length === 0) return null;
    const yearKeys = timeColumns.value; 
    if (selectedIndices.value.length === 0) return { labels: yearKeys, datasets: [] };
    const datasets = selectedIndices.value.map((rowIndex, colorIdx) => {
        const row = dataSrc[rowIndex];
        if (!row) return null;
        const nameKey = Object.keys(row).find(k => 
            ['nama data', 'nama indikator', 'uraian', 'indikator'].includes(k.toLowerCase().trim())
        ) || Object.keys(row)[0];
        const labelName = row[nameKey] || `Data ${rowIndex + 1}`;
        const rowSatuan = getSatuanFromRow(row);
        const dataValues = yearKeys.map(year => {
            let val = row[year];
            if (val === undefined || val === null || val === '') return null; 
            return val; 
        });
        const color = chartColors[colorIdx % chartColors.length];
        return {
            label: labelName,
            data: dataValues,
            unit: rowSatuan,
            borderColor: color,
            backgroundColor: color,
            borderWidth: 3,
            tension: 0.3, fill: false 
        };
    }).filter(Boolean);
    return { labels: yearKeys, datasets: datasets };
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

const formatDate = (dateString) => {
    if (!dateString) return '-';
    try {
        return new Date(dateString).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute:'2-digit'
        });
    } catch(e) { return '-'; }
};

const getActionName = (filePath) => {
    if (!filePath) return 'Pembaruan Sistem';
    if (filePath === 'system_init') return 'Registrasi Data Awal';
    if (filePath === 'manual_input') return 'Input Data Awal (Baru)';
    if (filePath === 'edit_manual') return 'Perubahan Data (Edit)';
    if (filePath.includes('.xls') || filePath.includes('.csv')) return 'Upload via Excel (Bulk)';
    return 'Pembaruan Data';
};

const parseLogValue = (jsonValue) => {
    if (!jsonValue) return [];
    
    try {
        let parsed = typeof jsonValue === 'string' ? JSON.parse(jsonValue) : jsonValue;
        let results = [];

        if (parsed.values && Array.isArray(parsed.values)) {
            parsed.values.forEach(v => {
                results.push({ label: v.tahun, value: v.nilai });
            });
            return results;
        }

        if (parsed.years && parsed.nilai) {
            parsed.years.forEach(year => {
                if (parsed.nilai[year] !== undefined && parsed.nilai[year] !== null) {
                    results.push({ label: year, value: parsed.nilai[year] });
                }
            });
            return results;
        }

        if (parsed.pesan) {
            return [{ label: 'pesan_sistem', value: parsed.pesan }];
        }

        if (parsed.dataset) {
            return [{ label: 'pesan_sistem', value: 'Data berhasil diekstrak dari dokumen Excel.' }];
        }

        return [];
    } catch(e) {
        return [];
    }
};

const timelineHistory = computed(() => {
    if (!props.dataset) return [];
    let logs = [...(props.dataset.uploads || [])];
    const hasInitialLog = logs.some(log => 
        log.file_path === 'manual_input' || 
        (log.file_path && (log.file_path.includes('.xls') || log.file_path.includes('.csv')))
    );
    if (!hasInitialLog) {
        logs.push({
            id_upload: 'virtual-init-' + props.dataset.id_data,
            id_user: props.dataset.id_user || 'Sistem',
            created_at: props.dataset.created_at, 
            status: props.dataset.status === 'aktif' ? 'valid' : 'pending',
            file_path: 'system_init',
            value: '{"pesan": "Data pertama kali diregistrasikan ke dalam sistem."}'
        });
    }
    return logs.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    plugins: {
        legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11, weight: 'bold' }, color: '#1F3A63' } }, 
        tooltip: { 
            backgroundColor: '#1F3A63', titleFont: { size: 13 }, bodyFont: { size: 12 }, padding: 12, cornerRadius: 10, 
            callbacks: {
                label: function(context) {
                    let label = context.dataset.label || '';
                    if (label) label += ': ';
                    if (context.parsed.y !== null) {
                        const unit = context.dataset.unit || '';
                        label += context.parsed.y + ' ' + unit;
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
                text: `Nilai Data (${dynamicSatuan.value})`,
                color: '#1F3A63',
                font: { size: 12, weight: 'bold' }
            },
            grid: { color: '#EEF2F5', borderDash: [5, 5] },
            ticks: { font: { size: 10 }, color: '#4B5563' }
        },
        x: {
            title: {
                display: true,
                text: 'Periode Pengamatan',
                color: '#1F3A63',
                font: { size: 12, weight: 'bold' }
            },
            grid: { display: false },
            ticks: { font: { size: 11, weight: 'bold' }, color: '#1F3A63' }
        }
    }
}));

const toggleBookmark = () => {
    const dataId = props.dataset?.id_data || props.dataset?.id; 
    if (!dataId) return;
    router.post(`/inputer/data/${dataId}/bookmark`, {}, { preserveScroll: true });
};
</script>

<template>
    <Head :title="dataset?.nama_data || 'Detail Data'" />

    <div class="min-h-screen bg-white">
        <section class="relative pt-28 pb-6 overflow-hidden w-full bg-white"> 
            <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[600px] h-[600px] bg-primary/5 rounded-full blur-3xl -z-10"></div>
            
            <div class="max-w-[80%] mx-auto">
                <div class="flex justify-end gap-3 mb-6">
                    <button @click="toggleBookmark" 
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 border shadow-sm"
                        :class="dataset?.is_pinned ? 'bg-primary text-white border-primary shadow-sm shadow-primary/10' : 'bg-white text-primary border-gray-400 hover:bg-bgsoft'">
                        <svg class="w-4 h-4" :fill="dataset?.is_pinned ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                        {{ dataset?.is_pinned ? 'Tersimpan' : 'Pin Data' }}
                    </button>
                    <Link v-if="dataset?.id_data && canEdit" :href="`/inputer/data/${dataset.id_data}/edit`" 
                        class="bg-profesional text-white px-4 py-2 rounded-xl text-xs font-bold hover:opacity-90 transition-all flex items-center gap-2 shadow-sm shadow-profesional/10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Data
                    </Link>
                </div>

                <div class="grid lg:grid-cols-3 gap-8 items-start mb-10">
                    <div class="lg:col-span-2">
                        <span class="inline-flex items-center px-3 py-1 mb-4 text-xs font-semibold text-secondary bg-secondary/10 rounded-full border border-secondary/20 tracking-wider uppercase">
                            {{ dataset?.tema?.nama_tema || 'Data Pembangunan' }}
                        </span>
                        <h1 class="text-3xl lg:text-4xl font-extrabold text-primary leading-tight tracking-tight">
                            {{ dataset?.nama_data || 'Data Tidak Ditemukan' }}
                        </h1>
                    </div>

                    <div class="bg-white border border-gray-400 p-5 rounded-2xl space-y-4">
                        <div class="flex items-center gap-4 border-b border-gray-400 pb-4">
                            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                            <div>
                                <p class="text-[9px] text-textsecondary uppercase tracking-wider font-bold">Tahun Data</p>
                                <p class="font-extrabold text-primary text-lg leading-tight">{{ dataset?.tahun || '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-secondary/10 rounded-xl flex items-center justify-center text-secondary shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            </div>
                            <div>
                                <p class="text-[9px] text-textsecondary uppercase tracking-wider font-bold">Instansi Sumber</p>
                                <p class="font-extrabold text-primary text-sm leading-tight">{{ dataset?.urusan?.nama_urusan || dataset?.sumber || '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="max-w-[80%] mx-auto pb-24">
            <div class="flex border-b border-gray-400 gap-6 mb-8 overflow-x-auto scrollbar-none">
                <button v-for="tab in tabs" :key="tab" @click="activeTab = tab"
                    class="pb-3.5 text-sm font-bold transition-all relative whitespace-nowrap px-1"
                    :class="activeTab === tab ? 'text-primary font-extrabold' : 'text-textsecondary hover:text-primary'">
                    {{ tab }}
                    <span v-if="activeTab === tab" class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary rounded-full transition-all duration-300"></span>
                </button>
            </div>

            <div v-if="activeTab === 'Data'" class="animate-in fade-in duration-500">
                <div class="border border-gray-400 rounded-2xl overflow-hidden shadow-sm bg-white">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50 border-b border-gray-400">
                                    <th class="p-4 border-r border-gray-400 text-xs font-bold text-textsecondary uppercase tracking-wider text-center w-24">
                                        Satuan
                                    </th>
                                    <th v-for="key in extraFieldKeys" :key="'th-'+key" class="p-4 border-r border-gray-400 text-xs font-bold text-textsecondary uppercase tracking-wider text-center">
                                        {{ key }}
                                    </th>
                                    <th v-for="year in timeColumns" :key="year" class="p-4 border-r border-gray-400 min-w-[120px] text-center text-xs font-bold text-primary uppercase tracking-wider">
                                        {{ year }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-400">
                                <tr v-for="(row, idx) in tableRows" :key="idx" class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="p-4 border-r border-gray-400 text-xs font-semibold text-textsecondary text-center group-hover:bg-slate-50/30">
                                        {{ getSatuanFromRow(row) }}
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
                </div>
            </div>

            <div v-if="activeTab === 'Relasi & Atribut'" class="animate-in slide-in-from-bottom-4 duration-500">
                <div class="grid lg:grid-cols-3 gap-6">
                    <div class="bg-white border border-gray-400 p-5 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                        <h4 class="text-sm font-extrabold text-primary uppercase tracking-wider mb-5 flex items-center gap-2.5">
                            <div class="w-7 h-7 bg-secondary/10 rounded-lg flex items-center justify-center text-secondary shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            </div>
                            Relasi Data
                        </h4>
                        <div class="divide-y divide-gray-400">
                            <div class="flex justify-between items-center py-3 px-1">
                                <span class="text-xs text-textsecondary uppercase font-semibold tracking-wider">Tema</span>
                                <span class="text-sm font-bold text-primary text-right">{{ datasetMeta?.tema || dataset?.tema?.nama_tema || '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 px-1">
                                <span class="text-xs text-textsecondary uppercase font-semibold tracking-wider">Urusan</span>
                                <span class="text-sm font-bold text-primary text-right">{{ datasetMeta?.urusan || dataset?.urusan?.nama_urusan || '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 px-1">
                                <span class="text-xs text-textsecondary uppercase font-semibold tracking-wider">Bidang</span>
                                <span class="text-sm font-bold text-primary text-right">{{ datasetMeta?.bidang || dataset?.bidang?.nama_bidang || '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 px-1">
                                <span class="text-xs text-textsecondary uppercase font-semibold tracking-wider">Frekuensi</span>
                                <span class="text-sm font-bold text-primary text-right">{{ datasetMeta?.frekuensi || dataset?.frekuensi?.nama_frekuensi || '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 px-1">
                                <span class="text-xs text-textsecondary uppercase font-semibold tracking-wider">Sumber</span>
                                <span class="text-sm font-bold text-primary text-right">{{ datasetMeta?.sumber || dataset?.sumber || '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 px-1">
                                <span class="text-xs text-textsecondary uppercase font-semibold tracking-wider">Satuan</span>
                                <span class="text-sm font-bold text-primary text-right">{{ datasetMeta?.satuan || dataset?.satuan || '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-400 p-5 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                        <h4 class="text-sm font-extrabold text-primary uppercase tracking-wider mb-5 flex items-center gap-2.5">
                            <div class="w-7 h-7 bg-inovasi/10 rounded-lg flex items-center justify-center text-inovasi shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17a1 1 0 102 0v-5a1 1 0 10-2 0v5zM12 7a1.5 1.5 0 110 3 1.5 1.5 0 010-3z" /></svg>
                            </div>
                            Statistik Nilai
                        </h4>
                        <div class="divide-y divide-gray-400">
                            <div class="flex justify-between items-center py-3 px-1">
                                <span class="text-xs text-textsecondary uppercase font-semibold tracking-wider">Jumlah Periode</span>
                                <span class="text-sm font-bold text-primary">{{ valueStats?.jumlah_periode ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 px-1">
                                <span class="text-xs text-textsecondary uppercase font-semibold tracking-wider">Nilai Terkini</span>
                                <span class="text-sm font-bold text-primary">{{ valueStats?.nilai_terkini ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 px-1">
                                <span class="text-xs text-textsecondary uppercase font-semibold tracking-wider">Periode Terkini</span>
                                <span class="text-sm font-bold text-primary">{{ valueStats?.periode_terkini ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 px-1">
                                <span class="text-xs text-textsecondary uppercase font-semibold tracking-wider">Nilai Minimum</span>
                                <span class="text-sm font-bold text-primary">{{ valueStats?.nilai_terendah ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 px-1">
                                <span class="text-xs text-textsecondary uppercase font-semibold tracking-wider">Nilai Maksimum</span>
                                <span class="text-sm font-bold text-primary">{{ valueStats?.nilai_tertinggi ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 px-1">
                                <span class="text-xs text-textsecondary uppercase font-semibold tracking-wider">Rata-Rata</span>
                                <span class="text-sm font-bold text-primary">{{ valueStats?.rata_rata ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-400 p-5 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                        <h4 class="text-sm font-extrabold text-primary uppercase tracking-wider mb-5 flex items-center gap-2.5">
                            <div class="w-7 h-7 bg-integritas/10 rounded-lg flex items-center justify-center text-integritas shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
                            </div>
                            Atribut Lain
                        </h4>
                        <div class="space-y-4">
                            <div class="divide-y divide-gray-400">
                                <div class="flex justify-between items-center py-2.5 px-1">
                                    <span class="text-xs text-textsecondary uppercase font-semibold tracking-wider">Tahun Terbit</span>
                                    <span class="text-sm font-bold text-primary">{{ datasetMeta?.tahun_terbit || dataset?.tahun_terbit || '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2.5 px-1">
                                    <span class="text-xs text-textsecondary uppercase font-semibold tracking-wider">Dibuat</span>
                                    <span class="text-sm font-bold text-primary">{{ datasetMeta?.created_at || '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2.5 px-1">
                                    <span class="text-xs text-textsecondary uppercase font-semibold tracking-wider">Diperbarui</span>
                                    <span class="text-sm font-bold text-primary">{{ datasetMeta?.updated_at || '-' }}</span>
                                </div>
                            </div>
                            
                            <div class="px-1 border-t border-gray-400 pt-3">
                                <p class="text-xs text-textsecondary uppercase font-semibold tracking-wider mb-2">Kata Kunci</p>
                                <div class="flex flex-wrap gap-1.5">
                                    <span
                                        v-for="keyword in (dataset?.katakunci || [])"
                                        :key="keyword.id_katakunci"
                                        class="px-2 py-1 rounded bg-secondary/5 text-secondary border border-secondary/10 text-[9px] font-bold uppercase tracking-wider"
                                    >
                                        {{ keyword.nama_katakunci }}
                                    </span>
                                    <span v-if="!(dataset?.katakunci || []).length" class="text-xs text-textsecondary font-medium italic">Tidak ada kata kunci.</span>
                                </div>
                            </div>
                            
                            <div class="px-1 border-t border-gray-400 pt-3">
                                <p class="text-xs text-textsecondary uppercase font-semibold tracking-wider mb-1">Deskripsi</p>
                                <p class="text-textsecondary text-xs leading-relaxed text-justify font-medium">{{ datasetMeta?.deskripsi || dataset?.deskripsi || 'Tidak ada deskripsi yang tersedia.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'Infografis'" class="animate-in slide-in-from-bottom-4 duration-500">
                <div class="grid lg:grid-cols-4 gap-6">
                    <div class="lg:col-span-3 bg-white border border-gray-400 p-5 rounded-2xl shadow-sm relative h-[500px] flex flex-col">
                        <div class="flex-1 relative bg-slate-50/30 rounded-xl border border-gray-400 p-4">
                            <Line v-if="chartConfig && chartConfig.datasets.length > 0" :data="chartConfig" :options="chartOptions" />
                            <div v-else class="flex flex-col items-center justify-center h-full text-textsecondary text-sm italic">Pilih satu atau lebih data di samping untuk memvisualisasikan nilai.</div>
                        </div>
                    </div>
                    <div class="lg:col-span-1">
                        <div class="bg-white border border-gray-400 p-5 rounded-2xl shadow-sm h-full flex flex-col min-h-[500px] max-h-[500px]">
                            <h4 class="text-xs font-extrabold text-primary mb-4 uppercase tracking-wider border-b border-gray-400 pb-3">Pilih Data</h4>
                            <div v-if="chartDataSrc && chartDataSrc.length > 0" class="overflow-y-auto custom-scrollbar flex-1 pr-1 space-y-2">
                                <div v-for="(row, index) in chartDataSrc" :key="index" @click="toggleSelection(index)"
                                    class="p-3 rounded-xl border border-gray-400 cursor-pointer transition-all hover:bg-slate-50/50"
                                    :class="selectedIndices.includes(index) ? 'bg-secondary/5 border-secondary/30 ring-1 ring-secondary/10' : 'bg-white hover:border-secondary/20'">
                                    <p class="text-[11px] font-bold leading-tight" :class="selectedIndices.includes(index) ? 'text-secondary' : 'text-primary'">
                                        {{ row.nama_data || row['Nama Indikator'] || row['Nama Data'] || row['Uraian'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'Riwayat'" class="animate-in slide-in-from-bottom-4 duration-500">
                <div class="bg-white border border-gray-400 p-8 rounded-2xl shadow-sm max-w-3xl mx-auto">
                    <div class="mb-8 text-center">
                        <h4 class="text-xl font-extrabold text-primary uppercase tracking-wider mb-2">Log Histori</h4>
                        <div class="w-12 h-1 bg-secondary mx-auto rounded-full"></div>
                    </div>

                    <div v-if="dataset?.uploads && dataset.uploads.length > 0" class="relative border-l-2 border-gray-400 ml-4 space-y-8 pb-4">
                        <div v-for="(log, idx) in timelineHistory" :key="log.id_upload" class="relative pl-8 group">
                            <div class="absolute -left-[7px] top-1.5 w-3.5 h-3.5 rounded-full border-2 border-white shadow-sm transition-all duration-300"
                                 :class="idx === 0 ? 'bg-inovasi scale-110' : 'bg-primary group-hover:bg-secondary'"></div>
                            
                            <div class="bg-slate-50/50 p-5 rounded-xl border border-gray-400 group-hover:bg-white group-hover:shadow-md transition-all group-hover:border-secondary/20">
                                <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
                                    <div>
                                        <span class="text-[9px] font-extrabold uppercase tracking-wider text-white px-2.5 py-1 rounded mb-2 inline-block"
                                            :class="idx === 0 ? 'bg-inovasi' : 'bg-primary'">
                                            {{ getActionName(log.file_path) }}
                                        </span>
                                        <h5 class="text-xs font-extrabold text-primary">User: {{ log.id_user || 'Sistem BAPPEDA' }}</h5>
                                        <p class="text-[10px] text-textsecondary font-bold mt-1 uppercase tracking-wider">{{ formatDate(log.created_at) }}</p>
                                    </div>
                                    <span class="text-[9px] font-extrabold px-2.5 py-1 rounded-full uppercase border"
                                        :class="log.status === 'valid' || log.status === 'aktif' ? 'bg-green-50 text-inovasi border-inovasi/20' : 'bg-amber-50 text-profesional border-profesional/20'">
                                        {{ log.status }}
                                    </span>
                                </div>
                                <div v-if="log.value" class="mt-4 pt-4 border-t border-gray-400">
                                    <p class="text-[9px] font-bold text-textsecondary uppercase tracking-wider mb-2">Nilai Capaian yang Tersimpan:</p>
                                    
                                    <div class="bg-white p-4 rounded-xl border border-gray-400 shadow-inner">
                                        <p v-if="parseLogValue(log.value).length === 1 && parseLogValue(log.value)[0].label === 'pesan_sistem'"
                                        class="text-xs font-semibold text-textsecondary italic leading-relaxed">
                                            {{ parseLogValue(log.value)[0].value }}
                                        </p>

                                        <div v-else-if="parseLogValue(log.value).length > 0" class="flex flex-wrap gap-2">
                                            <div v-for="(item, i) in parseLogValue(log.value)" :key="i"
                                                class="flex items-center bg-slate-50/80 rounded-lg border border-gray-400 overflow-hidden hover:border-secondary/20 transition-colors">
                                                <span class="bg-slate-100 text-[9px] font-extrabold text-textsecondary uppercase tracking-wider px-2 py-1.5 border-r border-gray-400">
                                                    {{ item.label }}
                                                </span>
                                                <span class="text-xs font-bold text-primary px-3 py-1.5">
                                                    {{ item.value }}
                                                </span>
                                            </div>
                                        </div>

                                        <p v-else class="text-[10px] text-slate-400 font-medium italic break-words">
                                            Data mentah: {{ log.value }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
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
