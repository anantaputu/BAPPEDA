<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Line } from 'vue-chartjs';
import { 
    Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, 
    Title, Tooltip, Legend, Filler 
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler);

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

const getSatuanFromRow = (row) => {
    if (!row) return '-';
    const key = Object.keys(row).find(k => k.toLowerCase().includes('satuan'));
    return row[key] || props.customSatuan || props.dataset?.satuan || '-';
};

const dynamicSatuan = computed(() => {
    if (!props.allData || selectedIndices.value.length === 0) return '-';
    const units = selectedIndices.value.map(idx => getSatuanFromRow(props.allData[idx]));
    const uniqueUnits = [...new Set(units)];
    return uniqueUnits.length === 1 ? uniqueUnits[0] : 'Beragam';
});

const extraFieldKeys = computed(() => {
    if (!props.dataset?.informasi_tambahan) return [];
    let extras = props.dataset.informasi_tambahan;
    if (typeof extras === 'string') {
        try { extras = JSON.parse(extras); } catch(e) { return []; }
    }
    return Object.keys(extras).filter(k => 
        k.toLowerCase() !== 'nama data' && k.toLowerCase() !== 'nama indikator'
    );
});

const timeColumns = computed(() => {
    if (!props.allData || props.allData.length === 0) return [];
    const firstRow = props.allData[0];
    const excludeKeys = [
        'nama indikator', 'nama data', 'uraian', 'indikator', 'satuan', 'id_data',
        ...extraFieldKeys.value.map(k => k.toLowerCase())
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
    if (!props.allData || props.allData.length === 0) return null;
    const yearKeys = timeColumns.value; 
    if (selectedIndices.value.length === 0) return { labels: yearKeys, datasets: [] };
    const datasets = selectedIndices.value.map((rowIndex, colorIdx) => {
        const row = props.allData[rowIndex];
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
    });
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

// Fungsi baru untuk memecah JSON mentah menjadi Array Object yang rapi
const parseLogValue = (jsonValue) => {
    if (!jsonValue) return [];
    
    try {
        let parsed = typeof jsonValue === 'string' ? JSON.parse(jsonValue) : jsonValue;
        let results = [];

        // Skenario 1: Data dari Single Input / Edit 
        // Format: { values: [{tahun: '2024', nilai: '10'}] }
        if (parsed.values && Array.isArray(parsed.values)) {
            parsed.values.forEach(v => {
                results.push({ label: v.tahun, value: v.nilai });
            });
            return results;
        }

        // Skenario 2: Data dari Bulk Upload Excel (Sesuai gambar Anda)
        // Format: { years: ['2023', '2024'], nilai: {'2023': '10', '2024': '20'} }
        if (parsed.years && parsed.nilai) {
            parsed.years.forEach(year => {
                if (parsed.nilai[year] !== undefined && parsed.nilai[year] !== null) {
                    results.push({ label: year, value: parsed.nilai[year] });
                }
            });
            return results;
        }

        // Skenario 3: Data inisialisasi awal (Pesan teks)
        if (parsed.pesan) {
            return [{ label: 'pesan_sistem', value: parsed.pesan }];
        }

        // Skenario 4: Format lain (Fallback)
        if (parsed.dataset) {
            return [{ label: 'pesan_sistem', value: 'Data berhasil diekstrak dari dokumen Excel.' }];
        }

        return [];
    } catch(e) {
        return []; // Jika gagal parse JSON
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

const chartOptions = {
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
        y: { grid: { color: '#EEF2F5', borderDash: [5, 5] }, ticks: { font: { size: 10 }, color: '#4B5563' } },
        x: { grid: { display: false }, ticks: { font: { size: 11, weight: 'bold' }, color: '#1F3A63' } }
    }
};

const toggleBookmark = () => {
    const dataId = props.dataset?.id_data || props.dataset?.id; 
    if (!dataId) return;
    router.post(`/inputer/data/${dataId}/bookmark`, {}, { preserveScroll: true });
};
</script>

<template>
    <Head :title="dataset?.nama_data || 'Detail Data'" />

    <div class="min-h-screen bg-white">
        <section class="relative pt-20 pb-10 overflow-hidden w-full bg-white"> 
            <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[600px] h-[600px] bg-primary/5 rounded-full blur-3xl -z-10"></div>
            
            <div class="max-w-[80%] mx-auto">
                <div class="flex items-center gap-2 text-[10px] font-black text-textsecondary uppercase tracking-[0.2em] mb-10">
                    <span class="text-primary">Detail Data</span>

                    <div class="ml-auto flex items-center gap-3">
                        <button @click="toggleBookmark" 
                            class="px-5 py-2.5 rounded-xl text-xs font-black transition-all flex items-center gap-2 border shadow-sm"
                            :class="dataset?.is_pinned ? 'bg-primary text-white border-primary shadow-lg shadow-primary/20' : 'bg-white text-primary border-gray-300 hover:bg-bgsoft'">
                            <svg class="w-4 h-4" :fill="dataset?.is_pinned ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            {{ dataset?.is_pinned ? 'Tersimpan' : 'Pin Data' }}
                        </button>
                        <Link v-if="dataset?.id_data" :href="`/inputer/data/${dataset.id_data}/edit`" 
                            class="bg-profesional text-white px-5 py-2.5 rounded-xl text-xs font-black hover:opacity-90 transition-all flex items-center gap-2 shadow-sm shadow-profesional/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Data
                        </Link>
                    </div>
                </div>

                <div class="grid lg:grid-cols-3 gap-16 items-start">
                    <div class="lg:col-span-2">
                        <span class="inline-block px-4 py-1.5 mb-6 text-sm font-bold text-secondary bg-secondary/10 rounded-full border border-secondary/30 tracking-wide uppercase">
                            {{ dataset?.tema?.nama_tema || 'Data Pembangunan' }}
                        </span>
                        <h1 class="text-4xl lg:text-6xl font-black text-primary leading-[1.15] mb-8">
                            {{ dataset?.nama_data || 'Data Tidak Ditemukan' }}
                        </h1>
                    </div>

                    <div class="bg-white border border-gray-400 p-8 rounded-xl shadow-xl shadow-primary/5">
                        <div class="space-y-6">
                            <div class="flex items-center gap-5 border-b border-bgsoft pb-6">
                                <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center text-white shadow-lg shadow-primary/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-textsecondary uppercase tracking-[0.2em] font-black">Tahun Data</p>
                                    <p class="font-black text-primary text-xl">{{ dataset?.tahun || '-' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-5">
                                <div class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center text-white shadow-lg shadow-secondary/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-textsecondary uppercase tracking-[0.2em] font-black">Instansi Sumber</p>
                                    <p class="font-black text-primary text-sm leading-tight">{{ dataset?.urusan?.nama_urusan || dataset?.sumber || '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="max-w-[80%] mx-auto pb-24">
            <div class="flex flex-wrap gap-2 mb-12 bg-bgsoft p-1.5 rounded-2xl border border-gray-300 w-fit">
                <button v-for="tab in tabs" :key="tab" @click="activeTab = tab"
                    class="px-10 py-3.5 rounded-xl text-sm font-black transition-all duration-300"
                    :class="activeTab === tab ? 'bg-primary text-white shadow-lg shadow-primary/20 scale-105' : 'text-textsecondary hover:text-primary'">
                    {{ tab }}
                </button>
            </div>

            <div v-if="activeTab === 'Data'" class="animate-in fade-in duration-500">
                <div class="border border-gray-400 rounded-xl overflow-hidden shadow-2xl shadow-primary/5 bg-white">
                    <div class="bg-primary p-8 flex flex-wrap justify-between items-center gap-6">
                        <div class="flex items-center gap-5 text-white">
                            <div class="w-14 h-14 bg-white/10 rounded-xl flex items-center justify-center border border-white/20">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-black uppercase tracking-[0.2em] text-lg">Capaian Nilai Data</h3>
                                <p class="text-secondary/80 text-[11px] font-bold mt-1 uppercase tracking-widest">Menampilkan {{ tableRows.length }} Data</p>
                            </div>
                        </div>
                        <a v-if="dataset?.id_data" :href="`/export/data/${dataset.id_data}`" 
                            class="bg-secondary text-white px-8 py-4 rounded-xl text-sm font-black hover:bg-white hover:text-primary transition-all flex items-center gap-3 shadow-lg shadow-secondary/10 border border-secondary/20">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" /></svg>
                            EXPORT EXCEL
                        </a>
                    </div>

                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                         <thead>
                            <tr class="bg-bgsoft">
                                <th class="p-6 bg-gray-100 border-b-2 border-r border-gray-300 text-[11px] font-black text-primary uppercase tracking-[0.15em] sticky left-0 z-20 min-w-[300px] shadow-md">
                                    Nama Data
                                </th>
                                <th class="p-6 border-b-2 border-r border-gray-300 text-[10px] font-black text-textsecondary uppercase tracking-widest text-center w-24">
                                    Satuan
                                </th>
                                <th v-for="key in extraFieldKeys" :key="'th-'+key" class="p-6 border-b-2 border-r border-gray-300 text-[10px] font-black text-textsecondary uppercase tracking-widest text-center">
                                    {{ key }}
                                </th>
                                <th v-for="year in timeColumns" :key="year" class="p-4 border-b-2 border-r border-gray-300 min-w-[160px] bg-bgsoft/50">
                                    <div class="bg-white border-2 border-primary/10 rounded-lg px-4 py-2 text-center">
                                        <span class="text-xs font-black text-primary uppercase tracking-widest">{{ year }}</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="(row, idx) in tableRows" :key="idx" class="hover:bg-bgsoft transition-colors group">
                                <td class="p-6 bg-white border-r border-gray-200 font-black text-primary text-sm sticky left-0 z-10 shadow-md group-hover:bg-gray-50">
                                    <div class="line-clamp-2 w-[280px]">
                                        {{ (props.tableData?.from || 1) + idx }}. {{ row['Nama Indikator'] || row.nama_data || row['Nama Data'] || row['Uraian'] || 'Data Tidak Diketahui' }}
                                    </div>
                                </td>
                                <td class="p-6 border-r border-gray-200 text-xs font-bold text-textsecondary text-center group-hover:bg-gray-50">
                                    {{ getSatuanFromRow(row) }}
                                </td>
                                <td v-for="key in extraFieldKeys" :key="'td-'+key" class="p-6 border-r border-gray-200 text-xs font-bold text-textsecondary text-center group-hover:bg-gray-50">
                                    {{ row[key] || '-' }}
                                </td>
                                <td v-for="year in timeColumns" :key="year" class="p-4 border-r border-gray-200 group-hover:bg-gray-50">
                                    <div class="w-full bg-white border border-gray-300 text-primary rounded-lg px-4 py-3 text-sm font-black text-center group-hover:border-secondary transition-all">
                                        {{ row[year] !== undefined && row[year] !== null && row[year] !== '' ? row[year] : '-' }}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'Relasi & Atribut'" class="animate-in slide-in-from-bottom-4 duration-500">
                <div class="grid lg:grid-cols-3 gap-8">
                    <div class="bg-white border border-gray-400 p-10 rounded-xl shadow-xl shadow-primary/5">
                        <h4 class="text-xl font-black text-primary uppercase tracking-[0.2em] mb-8 flex items-center gap-3">
                            <div class="w-8 h-8 bg-secondary rounded flex items-center justify-center text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg></div>
                            Relasi Data
                        </h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center py-4 border-b border-bgsoft px-2">
                                <span class="text-xs text-textsecondary uppercase font-bold tracking-wider">Tema</span>
                                <span class="text-sm font-black text-primary text-right">{{ datasetMeta?.tema || dataset?.tema?.nama_tema || '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-4 border-b border-bgsoft px-2">
                                <span class="text-xs text-textsecondary uppercase font-bold tracking-wider">Urusan</span>
                                <span class="text-sm font-black text-primary text-right">{{ datasetMeta?.urusan || dataset?.urusan?.nama_urusan || '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-4 border-b border-bgsoft px-2">
                                <span class="text-xs text-textsecondary uppercase font-bold tracking-wider">Bidang</span>
                                <span class="text-sm font-black text-primary text-right">{{ datasetMeta?.bidang || dataset?.bidang?.nama_bidang || '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-4 border-b border-bgsoft px-2">
                                <span class="text-xs text-textsecondary uppercase font-bold tracking-wider">Frekuensi</span>
                                <span class="text-sm font-black text-primary text-right">{{ datasetMeta?.frekuensi || dataset?.frekuensi?.nama_frekuensi || '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-4 border-b border-bgsoft px-2">
                                <span class="text-xs text-textsecondary uppercase font-bold tracking-wider">Sumber</span>
                                <span class="text-sm font-black text-primary text-right">{{ datasetMeta?.sumber || dataset?.sumber || '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-4 px-2">
                                <span class="text-xs text-textsecondary uppercase font-bold tracking-wider">Satuan</span>
                                <span class="text-sm font-black text-primary text-right">{{ datasetMeta?.satuan || dataset?.satuan || '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-400 p-10 rounded-xl shadow-xl shadow-primary/5">
                        <h4 class="text-xl font-black text-primary uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                            <div class="w-8 h-8 bg-inovasi rounded flex items-center justify-center text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17a1 1 0 102 0v-5a1 1 0 10-2 0v5zM12 7a1.5 1.5 0 110 3 1.5 1.5 0 010-3z" /></svg></div>
                            Statistik Nilai
                        </h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center py-3 border-b border-bgsoft">
                                <span class="text-xs text-textsecondary uppercase font-bold tracking-wider">Jumlah Periode</span>
                                <span class="text-sm font-black text-primary">{{ valueStats?.jumlah_periode ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-bgsoft">
                                <span class="text-xs text-textsecondary uppercase font-bold tracking-wider">Nilai Terkini</span>
                                <span class="text-sm font-black text-primary">{{ valueStats?.nilai_terkini ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-bgsoft">
                                <span class="text-xs text-textsecondary uppercase font-bold tracking-wider">Periode Terkini</span>
                                <span class="text-sm font-black text-primary">{{ valueStats?.periode_terkini ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-bgsoft">
                                <span class="text-xs text-textsecondary uppercase font-bold tracking-wider">Nilai Minimum</span>
                                <span class="text-sm font-black text-primary">{{ valueStats?.nilai_terendah ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-bgsoft">
                                <span class="text-xs text-textsecondary uppercase font-bold tracking-wider">Nilai Maksimum</span>
                                <span class="text-sm font-black text-primary">{{ valueStats?.nilai_tertinggi ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3">
                                <span class="text-xs text-textsecondary uppercase font-bold tracking-wider">Rata-Rata</span>
                                <span class="text-sm font-black text-primary">{{ valueStats?.rata_rata ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-400 p-10 rounded-xl shadow-xl shadow-primary/5">
                        <h4 class="text-xl font-black text-primary uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                            <div class="w-8 h-8 bg-integritas rounded flex items-center justify-center text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg></div>
                            Atribut Lain
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-bgsoft">
                                <span class="text-xs text-textsecondary uppercase font-bold tracking-wider">Tahun Terbit</span>
                                <span class="text-sm font-black text-primary">{{ datasetMeta?.tahun_terbit || dataset?.tahun_terbit || '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-bgsoft">
                                <span class="text-xs text-textsecondary uppercase font-bold tracking-wider">Dibuat</span>
                                <span class="text-sm font-black text-primary">{{ datasetMeta?.created_at || '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-bgsoft">
                                <span class="text-xs text-textsecondary uppercase font-bold tracking-wider">Diperbarui</span>
                                <span class="text-sm font-black text-primary">{{ datasetMeta?.updated_at || '-' }}</span>
                            </div>
                            <div class="pt-2">
                                <p class="text-xs text-textsecondary uppercase font-bold tracking-wider mb-2">Kata Kunci</p>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="keyword in (dataset?.katakunci || [])"
                                        :key="keyword.id_katakunci"
                                        class="px-3 py-1.5 rounded-lg bg-secondary/10 text-secondary border border-secondary/20 text-[10px] font-black uppercase tracking-wider"
                                    >
                                        {{ keyword.nama_katakunci }}
                                    </span>
                                    <span v-if="!(dataset?.katakunci || []).length" class="text-xs text-textsecondary font-bold">Tidak ada kata kunci.</span>
                                </div>
                            </div>
                            <div class="pt-3">
                                <p class="text-xs text-textsecondary uppercase font-bold tracking-wider mb-2">Deskripsi</p>
                                <p class="text-textsecondary text-sm leading-relaxed text-justify font-medium">{{ datasetMeta?.deskripsi || dataset?.deskripsi || 'Tidak ada deskripsi yang tersedia.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'Infografis'" class="animate-in slide-in-from-bottom-4 duration-500">
                <div class="grid lg:grid-cols-4 gap-8">
                    <div class="lg:col-span-3 bg-white border border-gray-400 p-10 rounded-xl shadow-xl shadow-primary/5 relative h-[600px] flex flex-col">
                        <div class="flex-1 relative bg-bgsoft/30 rounded-xl border border-gray-100 p-6">
                            <Line v-if="chartConfig && chartConfig.datasets.length > 0" :data="chartConfig" :options="chartOptions" />
                            <div v-else class="flex flex-col items-center justify-center h-full text-textsecondary italic">Pilih satu atau lebih data di samping untuk memvisualisasikan nilai.</div>
                        </div>
                    </div>
                    <div class="lg:col-span-1">
                        <div class="bg-white border border-gray-400 p-8 rounded-xl shadow-xl shadow-primary/5 h-full flex flex-col min-h-[600px]">
                            <h4 class="text-lg font-black text-primary mb-6 uppercase tracking-widest border-b border-bgsoft pb-4">Pilih Data</h4>
                            <div v-if="props.allData && props.allData.length > 0" class="overflow-y-auto custom-scrollbar flex-1 pr-2">
                                <div v-for="(row, index) in props.allData" :key="index" @click="toggleSelection(index)"
                                    class="p-4 rounded-xl border-2 cursor-pointer mb-3 transition-all"
                                    :class="selectedIndices.includes(index) ? 'bg-secondary/5 border-secondary ring-2 ring-secondary/10' : 'bg-white border-gray-200 hover:border-secondary/30'">
                                    <p class="text-[11px] font-black leading-tight" :class="selectedIndices.includes(index) ? 'text-secondary' : 'text-primary'">
                                        {{ row.nama_data || row['Nama Indikator'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'Riwayat'" class="animate-in slide-in-from-bottom-4 duration-500">
                <div class="bg-white border border-gray-400 p-12 rounded-xl shadow-xl shadow-primary/5 max-w-4xl mx-auto">
                    <div class="mb-12 text-center">
                        <h4 class="text-3xl font-black text-primary uppercase tracking-[0.3em] mb-4">Log Histori</h4>
                        <div class="w-24 h-2 bg-secondary mx-auto rounded-full"></div>
                    </div>

                    <div v-if="dataset?.uploads && dataset.uploads.length > 0" class="relative border-l-4 border-bgsoft ml-6 space-y-12 pb-8">
                        <div v-for="(log, idx) in timelineHistory" :key="log.id_upload" class="relative pl-12 group">
                            <div class="absolute -left-[14px] top-1 w-6 h-6 rounded-full border-4 border-white shadow-md transition-all duration-300"
                                 :class="idx === 0 ? 'bg-integritas scale-125' : 'bg-primary group-hover:bg-secondary'"></div>
                            
                            <div class="bg-bgsoft/50 p-8 rounded-xl border border-gray-300 group-hover:bg-white group-hover:shadow-2xl transition-all group-hover:border-secondary/30">
                                <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
                                    <div>
                                        <span class="text-[10px] font-black uppercase tracking-widest text-white px-4 py-2 rounded-lg mb-4 inline-block shadow-md"
                                            :class="idx === 0 ? 'bg-integritas' : 'bg-primary'">
                                            {{ getActionName(log.file_path) }}
                                        </span>
                                        <h5 class="text-sm font-black text-primary">User: {{ log.id_user || 'Sistem BAPPEDA' }}</h5>
                                        <p class="text-[11px] text-textsecondary font-bold mt-2 uppercase tracking-tighter">{{ formatDate(log.created_at) }}</p>
                                    </div>
                                    <span class="text-[10px] font-black px-4 py-2 rounded-full uppercase border-2"
                                        :class="log.status === 'valid' || log.status === 'aktif' ? 'bg-green-50 text-inovasi border-inovasi/20' : 'bg-amber-50 text-profesional border-profesional/20'">
                                        {{ log.status }}
                                    </span>
                                </div>
                                <div v-if="log.value" class="mt-6 pt-6 border-t border-gray-200">
    <p class="text-[10px] font-black text-textsecondary uppercase tracking-[0.2em] mb-4">Nilai Capaian yang Tersimpan:</p>
    
                                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-inner">
                                        <p v-if="parseLogValue(log.value).length === 1 && parseLogValue(log.value)[0].label === 'pesan_sistem'"
                                        class="text-xs font-bold text-textsecondary italic leading-relaxed">
                                            {{ parseLogValue(log.value)[0].value }}
                                        </p>

                                        <div v-else-if="parseLogValue(log.value).length > 0" class="flex flex-wrap gap-3">
                                            <div v-for="(item, i) in parseLogValue(log.value)" :key="i"
                                                class="flex items-center bg-bgsoft/50 rounded-lg border border-gray-200 overflow-hidden hover:border-secondary/30 transition-colors">
                                                <span class="bg-gray-100/50 text-[10px] font-black text-textsecondary uppercase tracking-widest px-3 py-2 border-r border-gray-200">
                                                    {{ item.label }}
                                                </span>
                                                <span class="text-xs font-black text-primary px-4 py-2">
                                                    {{ item.value }}
                                                </span>
                                            </div>
                                        </div>

                                        <p v-else class="text-[10px] text-gray-400 font-medium italic break-words">
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
