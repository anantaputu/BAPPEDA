<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

import StatCard from '@/Components/Dashboard/StatCard.vue';
import ActivityLog from '@/Components/Dashboard/ActivityLog.vue';

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
    myRecentActivities: Array,
    allData: {
        type: Array,
        default: () => []
    }
});

const activeView = ref('Grafik'); // Tab pemilih antara Grafik dan Tabel
const views = ['Grafik', 'Tabel Data'];

const colors = {
    emerald: { bg: 'bg-emerald-50', text: 'text-emerald-600', bar: 'bg-emerald-600' },
    amber: { bg: 'bg-amber-50', text: 'text-amber-600', bar: 'bg-amber-600' },
    rose: { bg: 'bg-rose-50', text: 'text-rose-600', bar: 'bg-rose-600' },
    blue: { bg: 'bg-blue-50', text: 'text-blue-600', bar: 'bg-blue-600' },
};

// ========================================================
// 1. LOGIKA FILTER SATUAN DINAMIS
// ========================================================
const getSatuanFromRow = (row) => {
    if (!row) return '-';
    // Cari kunci yang mengandung kata satuan (berjaga-jaga jika huruf besar/kecil)
    const key = Object.keys(row).find(k => k.toLowerCase().trim() === 'satuan');
    return row[key] || '-';
};

// Dapatkan daftar satuan yang unik dari seluruh data
const availableSatuans = computed(() => {
    if (!props.allData || props.allData.length === 0) return [];
    const units = props.allData.map(row => getSatuanFromRow(row).toUpperCase());
    return [...new Set(units)].filter(u => u !== '-');
});

// State untuk menyimpan satuan yang sedang aktif dipilih
const activeSatuan = ref(availableSatuans.value.length > 0 ? availableSatuans.value[0] : null);

// Data yang sudah difilter berdasarkan satuan yang dipilih
const filteredData = computed(() => {
    if (!activeSatuan.value) return [];
    return props.allData.filter(row => getSatuanFromRow(row).toUpperCase() === activeSatuan.value);
});

// ========================================================
// 2. LOGIKA GRAFIK & TABEL
// ========================================================
const selectedIndices = ref([0]); 
const chartColors = ['#00139E', '#FF1414', '#00D2FC', '#F8B400', '#54D62C', '#9D4EDD', '#FF6B6B'];

// Reset pilihan grafik jika pindah tab satuan
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
    filteredData.value.forEach(row => {
        if (row.informasi_tambahan) {
            let extras = row.informasi_tambahan;
            if (typeof extras === 'string') {
                try { extras = JSON.parse(extras); } catch(e) {}
            }
            if (typeof extras === 'object' && extras !== null) {
                Object.keys(extras).forEach(k => {
                    if (k.toLowerCase() !== 'nama data' && k.toLowerCase() !== 'nama indikator') keys.add(k);
                });
            }
        }
    });
    return Array.from(keys);
});

const timeColumns = computed(() => {
    if (!filteredData.value || filteredData.value.length === 0) return [];
    
    const excludeKeys = [
        'nama_indikator', 'nama indikator', 'nama data', 'uraian', 'indikator', 'satuan', 'id_data', 'informasi_tambahan',
        ...extraFieldKeys.value.map(k => k.toLowerCase())
    ];

    let cols = new Set();
    filteredData.value.forEach(row => {
        Object.keys(row).forEach(key => {
            const cleanKey = key.toLowerCase().trim();
            if (!excludeKeys.includes(cleanKey)) cols.add(key);
        });
    });

    const monthOrder = {
        'januari': 1, 'februari': 2, 'maret': 3, 'april': 4, 'mei': 5, 'juni': 6,
        'juli': 7, 'agustus': 8, 'september': 9, 'oktober': 10, 'november': 11, 'desember': 12,
    };

    return Array.from(cols).sort((a, b) => {
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
    if (!filteredData.value || filteredData.value.length === 0) return null;

    const yearKeys = timeColumns.value; 
    if (selectedIndices.value.length === 0) return { labels: yearKeys, datasets: [] };

    const datasets = selectedIndices.value.map((rowIndex, colorIdx) => {
        const row = filteredData.value[rowIndex];
        if(!row) return null;

        const nameKey = Object.keys(row).find(k => 
            ['nama_indikator', 'nama indikator', 'nama data', 'uraian', 'indikator'].includes(k.toLowerCase().trim())
        ) || Object.keys(row)[0];

        const labelName = row[nameKey] || `Data ${rowIndex + 1}`;
        const rowSatuan = getSatuanFromRow(row);

        const dataValues = yearKeys.map(year => {
            let val = row[year];
            return (val === undefined || val === null || val === '') ? null : parseFloat(val);
        });

        const color = chartColors[colorIdx % chartColors.length];

        return {
            label: labelName,
            data: dataValues,
            unit: rowSatuan,
            borderColor: color,
            backgroundColor: color,
            borderWidth: 2,
            tension: 0.3, 
            fill: false 
        };
    }).filter(d => d !== null);

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
        y: { grid: { color: '#f3f4f6', borderDash: [5, 5] }, ticks: { font: { size: 10 }, color: '#9ca3af' } },
        x: { grid: { display: false }, ticks: { font: { size: 11, weight: 'bold' }, color: '#000B58' } }
    }
};

const statsCards = computed(() => [
    { label: 'TOTAL DATA DIINPUT', value: props.stats.total_input || 0, icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', color: 'blue', progress: 100 },
    { label: 'DATA DISETUJUI', value: props.stats.data_approved || 0, icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', color: 'emerald', progress: (props.stats.total_input > 0) ? ((props.stats.data_approved / props.stats.total_input) * 100) : 0 },
    { label: 'MENUNGGU VALIDASI', value: props.stats.data_pending || 0, icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', color: 'amber', progress: (props.stats.total_input > 0) ? ((props.stats.data_pending / props.stats.total_input) * 100) : 0 },
    { label: 'DATA DITOLAK', value: props.stats.data_rejected || 0, icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z', color: 'rose', progress: (props.stats.total_input > 0) ? ((props.stats.data_rejected / props.stats.total_input) * 100) : 0 },
]);
</script>

<template>
    <Head title="Dashboard Inputer" />

    <div class="max-w-full overflow-hidden space-y-8 px-4 sm:px-6 lg:px-8 py-4">
        
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black text-[#000B58] uppercase tracking-tight">Ruang Kerja <span class="text-[#00139E]">Inputer</span></h1>
                <p class="text-sm text-slate-400 font-medium italic mt-1">Monitoring performa dan visualisasi data Anda secara menyeluruh.</p>
            </div>
        </div>

        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard v-for="(stat, index) in statsCards" :key="index" v-bind="stat" :colors="colors" />
        </section>

        <section class="mt-12 mb-4 bg-white p-6 rounded-[2rem] border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-6">
            
            <div class="w-full md:w-auto">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-2">Filter Berdasarkan Satuan:</p>
                <div class="flex flex-wrap gap-2">
                    <button v-for="satuan in availableSatuans" :key="satuan" 
                        @click="activeSatuan = satuan"
                        class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-300"
                        :class="activeSatuan === satuan ? 'bg-[#00139E] text-white shadow-lg shadow-blue-900/20' : 'bg-gray-50 text-gray-500 hover:bg-blue-50 hover:text-[#00139E] border border-gray-200'">
                        {{ satuan }}
                    </button>
                    <div v-if="availableSatuans.length === 0" class="text-xs text-rose-500 italic font-bold">Belum ada data indikator.</div>
                </div>
            </div>

            <div class="flex bg-gray-50 p-1.5 rounded-2xl border border-gray-200">
                <button v-for="view in views" :key="view" @click="activeView = view"
                    class="px-8 py-3 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all"
                    :class="activeView === view ? 'bg-white text-[#000B58] shadow-sm' : 'text-gray-400 hover:text-[#000B58]'">
                    {{ view }}
                </button>
            </div>
        </section>

        <section v-if="activeView === 'Grafik'" class="grid lg:grid-cols-4 gap-6 items-start animate-in fade-in duration-500">
            <div class="lg:col-span-3 bg-white border border-gray-200 p-8 rounded-[2rem] shadow-sm relative overflow-hidden flex flex-col h-full min-h-[550px]">
                <div class="flex justify-between items-end mb-6">
                    <div>
                        <h4 class="text-lg font-black text-[#000B58] uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-6 h-6 text-[#00139E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                            Tren Indikator
                        </h4>
                        <p class="text-xs text-gray-400 font-medium mt-1">Membandingkan {{ selectedIndices.length }} indikator bersatuan {{ activeSatuan }}</p>
                    </div>
                </div>

                <div class="relative w-full flex-1 bg-gray-50/50 rounded-2xl border border-gray-100 p-4">
                    <Line v-if="chartConfig && chartConfig.datasets.length > 0" :data="chartConfig" :options="chartOptions" />
                    
                    <div v-else class="flex flex-col items-center justify-center h-full text-gray-400">
                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-sm font-black text-gray-400 uppercase tracking-widest">Pilih Indikator di Samping</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 bg-white border border-gray-200 p-6 rounded-[2rem] shadow-sm flex flex-col h-[550px]">
                <h4 class="text-sm font-black text-[#000B58] uppercase tracking-widest mb-4 flex items-center gap-2 pb-4 border-b border-gray-100">
                    <svg class="w-5 h-5 text-[#00139E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                    Pin Indikator
                </h4>

                <div v-if="filteredData.length > 0" class="overflow-y-auto pr-2 space-y-3 custom-scrollbar flex-1">
                    <div v-for="(row, index) in filteredData" :key="index" @click="toggleSelection(index)"
                        class="p-4 rounded-[1.25rem] border transition-all cursor-pointer group flex items-start gap-3"
                        :class="selectedIndices.includes(index) ? 'bg-blue-50/50 border-blue-200 shadow-sm' : 'bg-white border-gray-100 hover:border-gray-300'">
                        
                        <div class="mt-0.5 w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all duration-300 shrink-0"
                            :class="selectedIndices.includes(index) ? 'bg-[#00139E] border-[#00139E]' : 'bg-white border-gray-200'">
                            <svg v-if="selectedIndices.includes(index)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] font-black text-gray-700 leading-tight group-hover:text-[#00139E] transition-colors uppercase tracking-wide line-clamp-2">
                                {{ row.nama_indikator || row['Nama Indikator'] || Object.values(row)[0] }}
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
            <div class="bg-white border border-gray-200 rounded-[2rem] overflow-hidden shadow-sm relative">
                
                <div class="bg-[#000B58] p-6 flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-black uppercase tracking-widest text-lg">Rekapitulasi Data ({{ activeSatuan }})</h3>
                        <p class="text-blue-200 text-xs font-medium mt-1">Geser ke samping untuk melihat nilai per tahun. Total: {{ filteredData.length }} Indikator.</p>
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar" v-if="filteredData.length > 0">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th class="p-5 bg-gray-100/90 border-b-2 border-r border-gray-200 text-[11px] font-black text-[#A2B5CB] uppercase tracking-[0.15em] sticky left-0 z-20 min-w-[280px] shadow-[4px_0_8px_-4px_rgba(0,0,0,0.1)]">
                                    Nama Indikator
                                </th>
                                
                                <th v-for="key in extraFieldKeys" :key="'th-'+key" class="p-5 border-b-2 border-r border-gray-200 text-[10px] font-black text-[#A2B5CB] uppercase tracking-widest text-center bg-gray-50/50">
                                    {{ key }}
                                </th>

                                <th v-for="year in timeColumns" :key="year" class="p-4 border-b-2 border-r border-gray-100 min-w-[150px] align-bottom bg-gray-50/30">
                                    <div class="bg-white border border-blue-100 rounded-xl px-4 py-3 text-center shadow-sm relative overflow-hidden">
                                        <div class="absolute top-0 left-0 w-full h-1 bg-[#00139E]/20"></div>
                                        <span class="text-xs font-black text-[#000B58] uppercase tracking-wider">{{ year }}</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="(row, idx) in filteredData" :key="idx" class="hover:bg-blue-50/20 transition-colors group">
                                <td class="p-5 bg-white border-r border-gray-200 font-bold text-[#000B58] text-sm sticky left-0 z-10 shadow-[4px_0_8px_-4px_rgba(0,0,0,0.1)] group-hover:bg-[#f8fafc]">
                                    <div class="line-clamp-2 w-[280px]">
                                        {{ idx + 1 }}. {{ row.nama_indikator || row['Nama Indikator'] || 'Data' }}
                                    </div>
                                </td>

                                <td v-for="key in extraFieldKeys" :key="'td-'+key" class="p-5 border-r border-gray-100 text-xs font-bold text-gray-500 text-center bg-white group-hover:bg-[#f8fafc]">
                                    {{ row[key] || '-' }}
                                </td>

                                <td v-for="year in timeColumns" :key="year" class="p-3 border-r border-gray-100 min-w-[150px] bg-white group-hover:bg-[#f8fafc]">
                                    <div class="w-full bg-[#F5F7FA] border border-gray-100 text-[#000B58] rounded-xl px-4 py-3 text-sm font-black text-center transition-all hover:border-[#00139E]/30 hover:bg-white">
                                        {{ row[year] !== undefined && row[year] !== null && row[year] !== '' ? row[year] : '-' }}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class="p-16 text-center bg-gray-50">
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">Tidak ada data untuk satuan ini.</p>
                </div>
            </div>
        </section>

        <div class="w-full mt-10">
            <ActivityLog title="Riwayat Aktivitas Saya" :activities="props.myRecentActivities" />
        </div>
        
    </div>
</template>

<style scoped>
div { transition: width 0.3s ease; }

.custom-scrollbar::-webkit-scrollbar { height: 10px; width: 10px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 0 0 2rem 2rem; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; border: 2px solid #f8fafc; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #00139E; }

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    white-space: normal;
}
</style>