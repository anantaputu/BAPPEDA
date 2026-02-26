<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
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
    customSatuan: String  
});

const activeTab = ref('Data');
const tabs = ['Data', 'Metadata', 'Infografis'];
const selectedIndices = ref([0]); 
const chartColors = ['#00139E', '#FF1414', '#00D2FC', '#F8B400', '#54D62C', '#9D4EDD', '#FF6B6B'];

// --- 1. DATA TABEL ---
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

// --- 2. LOGIKA KOLOM TABEL DINAMIS ---
const timeColumns = computed(() => {
    if (!props.allData || props.allData.length === 0) return [];
    
    let cols = new Set();
    const excludeKeys = ['nama indikator', 'nama data', 'uraian', 'indikator', 'satuan', 'id_data', 'created_at', 'updated_at'];

    props.allData.forEach(row => {
        Object.keys(row).forEach(key => {
            const cleanKey = key.toLowerCase().trim();
            if (!excludeKeys.includes(cleanKey)) {
                cols.add(key);
            }
        });
    });

    return Array.from(cols).sort((a, b) => {
        const numA = parseInt(a.match(/\d+/)?.[0] || 0);
        const numB = parseInt(b.match(/\d+/)?.[0] || 0);
        if (numA && numB && numA !== numB) return numA - numB;
        return a.localeCompare(b); 
    });
});

// --- 3. CONFIG CHART ---
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
            if (val === undefined || val === null || val === '-' || val === '') return null; 
            if (typeof val === 'string') {
                val = val.replace(',', '.').replace(/[^0-9.-]/g, ''); 
            }
            return parseFloat(val) || 0;
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
            day: 'numeric', month: 'long', year: 'numeric'
        });
    } catch(e) { return '-'; }
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    plugins: {
        legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: { size: 10, weight: 'bold' } } }, 
        tooltip: { 
            backgroundColor: '#000B58', 
            titleFont: { size: 13 }, bodyFont: { size: 12 }, padding: 10, cornerRadius: 8, 
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
        y: { grid: { color: '#f3f4f6', borderDash: [5, 5] }, ticks: { font: { size: 10 }, color: '#9ca3af' } },
        x: { grid: { display: false }, ticks: { font: { size: 11, weight: 'bold' }, color: '#000B58' } }
    }
};
</script>

<template>
    <Head :title="dataset?.nama_indikator || 'Detail Data'" />

    <div class="min-h-screen bg-white font-sans mt-20">
        <section class="relative pt-20 overflow-hidden w-full"> 
            <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[600px] h-[600px] bg-[#A2B5CB]/10 rounded-full blur-3xl -z-10"></div>
            
            <div class="max-w-[80%] mx-auto">
                <div class="grid lg:grid-cols-3 gap-16 items-start">
                    <div class="lg:col-span-2">
                        <span class="inline-block px-4 py-1.5 mb-6 text-sm font-bold text-[#00139E] bg-[#A2B5CB]/20 rounded-full border border-[#A2B5CB]/30 tracking-wide uppercase">
                            {{ dataset?.tema?.nama_tema || 'Indikator Pembangunan' }}
                        </span>
                        <h1 class="text-4xl lg:text-6xl font-black text-[#000B58] leading-[1.2] mb-8">
                            {{ dataset?.nama_indikator || 'Data Tidak Ditemukan' }}
                        </h1>
                    </div>

                    <div class="bg-white border border-gray-400 p-8 rounded-[2.5rem] shadow-2xl shadow-[#000B58]/5">
                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-[#FF1414]/10 rounded-xl flex items-center justify-center text-[#FF1414]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-[#A2B5CB] uppercase tracking-widest font-black">Instansi Sumber</p>
                                    <p class="font-black text-[#000B58] text-sm leading-tight">{{ dataset?.urusan?.nama_urusan || dataset?.sumber || '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="max-w-[80%] mx-auto pb-20">
            <div class="flex gap-4 mb-10 bg-gray-50 p-2 rounded-[2rem] border border-gray-400 w-fit">
                <button v-for="tab in tabs" :key="tab" @click="activeTab = tab"
                    class="px-10 py-3 rounded-[1.5rem] text-sm font-black transition-all duration-300"
                    :class="activeTab === tab ? 'bg-[#000B58] text-white shadow-lg' : 'text-[#A2B5CB] hover:text-[#000B58]'">
                    {{ tab }}
                </button>
            </div>

            <div v-if="activeTab === 'Data'" class="animate-in fade-in duration-500">
                <div class="mt-8">
                    <div class="border border-gray-200 rounded-t-[2rem] overflow-hidden shadow-sm relative">
                        <div class="bg-[#000B58] p-6 flex flex-wrap justify-between items-center gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-white font-black uppercase tracking-widest text-lg">Preview Capaian Nilai</h3>
                                    <p class="text-blue-200 text-xs font-medium mt-1 opacity-80">
                                        Geser ke samping untuk melihat seluruh periode waktu. Menampilkan {{ tableRows.length }} baris.
                                    </p>
                                </div>
                            </div>
                            
                            <a v-if="dataset?.id_data" 
                               :href="'/export/data/' + dataset.id_data" 
                               class="bg-[#00139E] text-white px-6 py-3 rounded-xl text-sm font-bold hover:bg-[#000B58] transition-all flex items-center gap-2 shadow-lg border border-blue-800/50">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Export to Excel
                            </a>
                        </div>
                    </div>

                    <div class="border-x border-b border-gray-200 rounded-b-[2rem] overflow-hidden bg-white relative">
                        <div class="overflow-x-auto custom-scrollbar" v-if="tableRows.length > 0">
                            <table class="w-full text-left border-collapse whitespace-nowrap">
                                <thead>
                                    <tr class="bg-gray-50/80">
                                        <!-- <th class="p-5 bg-gray-100/90 border-b-2 border-r border-gray-200 text-[11px] font-black text-[#A2B5CB] uppercase tracking-[0.15em] sticky left-0 z-20 min-w-[250px] shadow-[4px_0_8px_-4px_rgba(0,0,0,0.1)] backdrop-blur-sm">
                                            Nama Indikator
                                        </th> -->
                                        <th class="p-5 border-b-2 border-r border-gray-200 text-[10px] font-black text-[#A2B5CB] uppercase tracking-widest text-center w-24 bg-gray-50/50">
                                            Satuan
                                        </th>
                                        <th v-for="year in timeColumns" :key="year" class="p-3 border-b-2 border-r border-gray-100 min-w-[180px] align-bottom bg-gray-50/30">
                                            <div class="bg-white border-2 border-blue-100/50 rounded-xl px-4 py-3 text-center shadow-sm relative overflow-hidden">
                                                <div class="absolute top-0 left-0 w-full h-1 bg-[#00139E]/20"></div>
                                                <span class="text-xs font-black text-[#000B58] uppercase tracking-wider">{{ year }}</span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                
                                <tbody class="divide-y divide-gray-100">
                                    <tr v-for="(row, idx) in tableRows" :key="idx" class="hover:bg-blue-50/20 transition-colors group">
                                        <!-- <td class="p-5 bg-white border-r border-gray-200 font-bold text-[#000B58] text-sm sticky left-0 z-10 shadow-[4px_0_8px_-4px_rgba(0,0,0,0.1)] group-hover:bg-[#f8fafc]">
                                           <div class="line-clamp-2 w-[280px]" :title="row.nama_indikator || row['Nama Data'] || row['Nama Indikator'] || row['Uraian']">
                                              {{ (props.tableData?.from || 1) + idx }}. {{ row.nama_indikator || row['Nama Data'] || row['Nama Indikator'] || row['Uraian'] || 'Data' }}
                                           </div>
                                        </td> -->
                                        <td class="p-5 border-r border-gray-100 text-xs font-bold text-gray-500 text-center bg-white group-hover:bg-[#f8fafc]">
                                            {{ getSatuanFromRow(row) }}
                                        </td>
                                        <td v-for="year in timeColumns" :key="year" class="p-3 border-r border-gray-100 min-w-[180px] bg-white group-hover:bg-[#f8fafc]">
                                            <div class="w-full bg-[#F5F7FA] border border-gray-200 text-[#000B58] rounded-xl px-5 py-4 text-sm font-black text-center shadow-inner transition-all hover:border-[#00139E]/30 hover:bg-white">
                                                {{ row[year] !== undefined && row[year] !== null && row[year] !== '' ? row[year] : '-' }}
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="p-10 text-center text-gray-400 italic">Tidak ada data indikator yang ditemukan.</div>

                        <div v-if="props.tableData?.current_page" class="p-6 bg-gray-50/80 border-t border-gray-200 flex justify-between items-center text-xs text-gray-500 font-medium">
                            <p class="text-xs font-black text-[#A2B5CB] uppercase tracking-widest">Halaman {{ props.tableData.current_page }} dari {{ props.tableData.last_page }}</p>
                            <div class="flex gap-3">
                                <Link v-if="props.tableData.prev_page_url" :href="props.tableData.prev_page_url" class="p-3 border border-gray-400 rounded-xl hover:bg-white transition-all"><svg class="h-4 w-4 text-[#000B58]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></Link>
                                <Link v-if="props.tableData.next_page_url" :href="props.tableData.next_page_url" class="p-3 border border-gray-400 rounded-xl hover:bg-white transition-all"><svg class="h-4 w-4 text-[#000B58]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'Metadata'" class="animate-in slide-in-from-bottom-4 duration-500">
                <div class="grid lg:grid-cols-2 gap-8">
                    <div class="space-y-8">
                        <div class="bg-white border border-gray-400 p-10 rounded-[2.5rem] shadow-sm">
                            <h4 class="text-xl font-black text-[#000B58] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                Klasifikasi Data
                            </h4>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2"><span class="text-s text-gray-800 uppercase tracking-wider">Urusan</span><span class="text-s font-black text-[#000B58] text-right">{{ dataset?.urusan?.nama_urusan || '-' }}</span></div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2"><span class="text-s text-gray-800 uppercase tracking-wider">Bidang</span><span class="text-s font-black text-[#000B58] text-right">{{ dataset?.bidang?.nama_bidang || '-' }}</span></div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2"><span class="text-s text-gray-800 uppercase tracking-wider">Tema</span><span class="text-s font-black text-[#000B58] text-right">{{ dataset?.tema?.nama_tema || '-' }}</span></div>
                                
                                <div class="pt-4 mt-2">
                                    <span class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] block mb-3">Kata Kunci / Tagging</span>
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="tag in dataset?.katakunci" :key="tag.id_katakunci" 
                                              class="px-3 py-1 bg-blue-50 text-[#00139E] text-[11px] font-bold rounded-lg border border-blue-100 uppercase tracking-wider hover:bg-[#00139E] hover:text-white transition-colors duration-200 cursor-default">
                                            #{{ tag.nama_katakunci }}
                                        </span>
                                        <span v-if="!dataset?.katakunci || dataset?.katakunci.length === 0" class="text-gray-400 italic text-xs">
                                            Tidak ada kata kunci yang disematkan.
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-400 p-10 rounded-[2.5rem] shadow-sm">
                            <h4 class="text-xl font-black text-[#000B58] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                Atribut Teknis
                            </h4>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2">
                                    <span class="text-s text-gray-800 uppercase tracking-wider">Satuan Global</span>
                                    <span class="text-s font-black text-[#000B58] bg-blue-50 px-3 py-1 rounded-lg">{{ dataset?.satuan || '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2"><span class="text-s text-gray-800 uppercase tracking-wider">Frekuensi</span><span class="text-s font-black text-[#000B58]">{{ dataset?.frekuensi?.nama_frekuensi || 'Tahunan' }}</span></div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2"><span class="text-s text-gray-800 uppercase tracking-wider">Tahun Data Utama</span><span class="text-s font-black text-[#000B58]">{{ dataset?.tahun || '-' }}</span></div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2"><span class="text-s text-gray-800 uppercase tracking-wider">Status Data</span><span :class="dataset?.status === 'aktif' ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50'" class="px-3 py-1 rounded-full text-[10px] font-black uppercase">{{ dataset?.status || '-' }}</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="bg-white border border-gray-400 p-10 rounded-[2.5rem] shadow-sm h-full">
                            <div class="mb-10">
                                <h4 class="text-xl font-black text-[#000B58] uppercase tracking-[0.2em] mb-4 flex items-center gap-2"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg> Deskripsi Lengkap</h4>
                                <p class="text-gray-800 text-s leading-loose text-justify">{{ dataset?.deskripsi || 'Tidak ada deskripsi yang tersedia untuk dataset ini.' }}</p>
                            </div>
                            
                            <div class="mb-10">
                                <h4 class="text-xl font-black text-[#000B58] uppercase tracking-[0.2em] mb-4 flex items-center gap-2"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg> Timeline Sistem</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-3 rounded-xl"><p class="text-[10px] font-bold text-gray-400 uppercase">Dibuat</p><p class="text-s font-black text-gray-700 mt-1">{{ formatDate(dataset?.created_at) }}</p></div>
                                    <div class="bg-gray-50 p-3 rounded-xl"><p class="text-[10px] font-bold text-gray-400 uppercase">Update Terakhir</p><p class="text-s font-black text-gray-700 mt-1">{{ formatDate(dataset?.updated_at) }}</p></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'Infografis'" class="animate-in slide-in-from-bottom-4 duration-500">
                <div class="grid lg:grid-cols-4 gap-8">
                    <div class="lg:col-span-3 bg-white border border-gray-400 p-10 rounded-[2.5rem] shadow-sm relative overflow-hidden flex flex-col">
                        <div class="relative z-10 flex justify-between items-end mb-8">
                            <div>
                                <h4 class="text-xl font-black text-[#000B58] uppercase tracking-[0.2em] mb-2 flex items-center gap-3">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    Tren Data Visual
                                </h4>
                                <p class="text-s text-gray-500 uppercase tracking-wider font-medium">Visualisasi Perbandingan Variabel Indikator</p>
                            </div>
                            
                            <div class="bg-[#00139E] text-white px-6 py-2 rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-900/20">
                                Satuan: {{ dynamicSatuan }}
                            </div>
                        </div>

                        <div class="relative w-full h-[500px] bg-gray-50/30 rounded-[2rem] border border-gray-100 p-4">
                            <Line v-if="chartConfig && chartConfig.datasets.length > 0" :data="chartConfig" :options="chartOptions" />
                            <div v-else class="flex flex-col items-center justify-center h-full text-gray-400 bg-gray-50/50 rounded-[2rem] border-2 border-dashed border-gray-200">
                                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-sm mb-4 border border-gray-200">
                                    <svg class="w-10 h-10 text-[#A2B5CB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-black text-[#000B58] uppercase tracking-widest">Data Belum Dipilih</p>
                                <p class="text-xs font-medium text-gray-400 mt-1">Silakan centang indikator pada panel kanan untuk memuat grafik.</p>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <div class="bg-white border border-gray-400 p-8 rounded-[2.5rem] shadow-sm h-full flex flex-col min-h-[600px]">
                            <h4 class="text-lg font-black text-[#000B58] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Pilih Data
                            </h4>

                            <div v-if="props.allData && props.allData.length > 0" class="overflow-y-auto pr-2 space-y-3 custom-scrollbar flex-1">
                                <div v-for="(row, index) in props.allData" :key="index" @click="toggleSelection(index)"
                                    class="p-4 rounded-[1.5rem] border transition-all cursor-pointer group flex items-start gap-3"
                                    :class="selectedIndices.includes(index) ? 'bg-blue-50/50 border-blue-200 shadow-sm' : 'bg-white border-gray-100 hover:border-gray-300 hover:bg-gray-50/50'">
                                    
                                    <div class="mt-0.5 w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all duration-300"
                                         :class="selectedIndices.includes(index) ? 'bg-[#00139E] border-[#00139E] scale-110' : 'bg-white border-gray-200'">
                                        <svg v-if="selectedIndices.includes(index)" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>

                                    <div class="flex-1">
                                        <p class="text-xs font-black text-gray-700 leading-tight group-hover:text-[#00139E] transition-colors uppercase tracking-wide">
                                            {{ row.nama_indikator || row['Nama Data'] || row['Nama Indikator'] || row['Uraian'] }}
                                        </p>
                                        <div v-if="selectedIndices.includes(index)" 
                                             class="mt-2 h-1.5 w-full rounded-full overflow-hidden bg-gray-100">
                                            <div class="h-full animate-in slide-in-from-left duration-500" 
                                                 :style="{ backgroundColor: chartColors[selectedIndices.indexOf(index) % chartColors.length], width: '40%' }">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="flex-1 flex flex-col items-center justify-center text-center p-6 bg-gray-50 rounded-[2rem] border border-dashed border-gray-200">
                                <p class="text-xs font-bold text-gray-400 italic">Data list tidak tersedia.</p>
                            </div>
                            
                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <p class="text-[9px] font-black text-[#A2B5CB] text-center uppercase tracking-[0.2em] leading-relaxed">
                                    Pilih item untuk membandingkan tren data secara real-time pada grafik.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { height: 12px; width: 12px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 0 0 2rem 2rem; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; border: 3px solid #f1f5f9; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    white-space: normal;
}
</style>