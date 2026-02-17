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
    dataset: Object,      // Metadata Indikator Utama
    tableData: Object,    // Paginasi Data (Berasal dari JSON data_uploads)
    allData: Array,       // Full Data untuk Chart (Berasal dari JSON data_uploads)
    customSatuan: String  // Satuan
});

const activeTab = ref('Data');
const tabs = ['Data', 'Metadata', 'Infografis'];
const selectedIndices = ref([0]); 
const chartColors = ['#00139E', '#FF1414', '#00D2FC', '#F8B400', '#54D62C', '#9D4EDD', '#FF6B6B'];

// --- 1. JARING PENGAMAN TABEL (Mengatasi jika tableData null/undefined) ---
const tableRows = computed(() => {
    if (props.tableData && props.tableData.data) return props.tableData.data;
    if (Array.isArray(props.tableData)) return props.tableData;
    return props.allData || []; // Fallback ke allData
});

// --- 2. LOGIKA SATUAN DINAMIS ---
const getSatuanFromRow = (row) => {
    if (!row) return '-';
    // Cari key yang mengandung kata 'satuan'
    const key = Object.keys(row).find(k => k.toLowerCase().includes('satuan'));
    return row[key] || props.customSatuan || props.dataset?.satuan || '-';
};

const dynamicSatuan = computed(() => {
    if (!props.allData || selectedIndices.value.length === 0) return '-';
    
    const units = selectedIndices.value.map(idx => {
        return getSatuanFromRow(props.allData[idx]);
    });

    const uniqueUnits = [...new Set(units)];
    return uniqueUnits.length === 1 ? uniqueUnits[0] : 'Beragam';
});

// --- 3. CONFIG CHART ---
// --- CONFIG CHART (DIPERBAIKI) ---
const chartConfig = computed(() => {
    if (!props.allData || props.allData.length === 0) return null;

    let allYears = new Set();
    props.allData.forEach(row => {
        Object.keys(row).forEach(k => {
            // REGEX BARU: Mencari angka 4 digit di dalam string manapun (misal: "2025" atau "Tahun 2025")
            const match = k.match(/\b(20\d{2})\b/);
            if (match) {
                allYears.add(k); // Simpan key aslinya (misal: "2025")
            }
        });
    });
    
    // Urutkan tahun berdasarkan angka di dalamnya
    const yearKeys = Array.from(allYears).sort((a, b) => {
        return a.match(/\d+/)[0] - b.match(/\d+/)[0];
    });

    if (selectedIndices.value.length === 0) return { labels: yearKeys, datasets: [] };

    const datasets = selectedIndices.value.map((rowIndex, colorIdx) => {
        const row = props.allData[rowIndex];
        
        // Deteksi Nama Indikator lebih cerdas
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

    return {
        labels: yearKeys,
        datasets: datasets
    };
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

    <div class="min-h-screen bg-white font-sans pb-32 mt-20">
        <section class="relative py-20 overflow-hidden w-full"> 
            <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[600px] h-[600px] bg-[#A2B5CB]/10 rounded-full blur-3xl -z-10"></div>
            
            <div class="max-w-[80%] mx-auto">
                <div class="flex items-center gap-2 text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] mb-8">
                    <Link href="/" class="hover:text-[#00139E] transition-colors">Beranda</Link> 
                    <span class="text-gray-300">/</span>
                    <Link href="/cari" class="hover:text-[#00139E] transition-colors">Katalog Data</Link>
                    <span class="text-gray-300">/</span>
                    <span class="text-[#000B58]">Detail Indikator</span>
                </div>

                <div class="grid lg:grid-cols-3 gap-16 items-start">
                    <div class="lg:col-span-2">
                        <span class="inline-block px-4 py-1.5 mb-6 text-sm font-bold text-[#00139E] bg-[#A2B5CB]/20 rounded-full border border-[#A2B5CB]/30 tracking-wide uppercase">
                            {{ dataset?.tema?.nama_tema || 'Indikator Pembangunan' }}
                        </span>
                        <h1 class="text-4xl lg:text-6xl font-black text-[#000B58] leading-[1.2] mb-8">
                            {{ dataset?.nama_indikator || 'Data Tidak Ditemukan' }}
                        </h1>
                        <p class="text-lg text-gray-500 leading-relaxed font-medium max-w-2xl">
                            {{ dataset?.deskripsi || 'Tidak ada deskripsi rinci untuk dataset ini.' }}
                        </p>
                    </div>

                    <div class="bg-white border border-gray-400 p-8 rounded-[2.5rem] shadow-2xl shadow-[#000B58]/5">
                        <div class="space-y-6">
                            <div class="flex items-center gap-4 border-b border-gray-100 pb-4">
                                <div class="w-10 h-10 bg-[#00139E]/10 rounded-xl flex items-center justify-center text-[#00139E]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-[#A2B5CB] uppercase tracking-widest font-black">Tahun Data</p>
                                    <p class="font-black text-[#000B58]">{{ dataset?.tahun || '-' }}</p>
                                </div>
                            </div>
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

        <section class="max-w-[80%] mx-auto">
            <div class="flex gap-4 mb-10 bg-gray-50 p-2 rounded-[2rem] border border-gray-400 w-fit">
                <button v-for="tab in tabs" :key="tab" @click="activeTab = tab"
                    class="px-10 py-3 rounded-[1.5rem] text-sm font-black transition-all duration-300"
                    :class="activeTab === tab ? 'bg-[#000B58] text-white shadow-lg' : 'text-[#A2B5CB] hover:text-[#000B58]'">
                    {{ tab }}
                </button>
            </div>

            <div v-if="activeTab === 'Data'" class="animate-in fade-in duration-500">
                <div class="bg-white border border-gray-400 rounded-[3rem] overflow-hidden">
                    <div class="p-10 border-b border-gray-400 flex flex-col md:flex-row justify-between items-center gap-6">
                        <div>
                            <h3 class="text-2xl font-black text-[#000B58]">Preview Dataset</h3>
                            <p class="text-sm font-medium text-gray-400">Menampilkan {{ tableRows.length }} baris indikator</p>
                        </div>
                        <a v-if="dataset?.id_data" :href="`/export/data/${dataset.id_data}`" 
                           class="bg-[#00139E] text-white px-8 py-4 rounded-2xl text-sm font-black hover:bg-[#000B58] transition-all flex items-center gap-3">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" /></svg>
                            Export to Excel
                        </a>
                    </div>

                    <div class="overflow-x-auto" v-if="tableRows.length > 0">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-6 py-6 text-[10px] font-black text-[#A2B5CB] uppercase tracking-widest border-b border-gray-400 text-center w-16">#</th>
                                    <th class="px-6 py-6 text-[10px] font-black text-[#A2B5CB] uppercase tracking-widest border-b border-gray-400">Nama Indikator</th>
                                    <th class="px-6 py-6 text-[10px] font-black text-[#A2B5CB] uppercase tracking-widest border-b border-gray-400 w-32 text-center">Satuan</th>
                                    
                                    <th v-for="year in (chartConfig?.labels || [])" :key="year" 
                                        class="px-6 py-6 text-[10px] font-black text-[#A2B5CB] uppercase tracking-widest border-b border-gray-400 text-center">
                                        {{ year }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="(row, idx) in tableRows" :key="idx" class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-5 font-black text-[#00139E] text-center text-sm italic">{{ (props.tableData?.from || 1) + idx }}</td>
                                    
                                    <td class="px-6 py-5 text-sm font-bold text-[#000B58]">
                                        {{ row.nama_indikator || row['Nama Data'] || row['Nama Indikator'] || row['Uraian'] || Object.values(row)[0] }}
                                    </td>

                                    <td class="px-6 py-5 text-sm font-medium text-gray-500 text-center">
                                        {{ getSatuanFromRow(row) }}
                                    </td>

                                    <td v-for="year in (chartConfig?.labels || [])" :key="year" 
                                        class="px-6 py-5 text-sm font-bold text-[#000B58] text-center">
                                        {{ row[year] !== undefined ? row[year] : '-' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else class="p-10 text-center text-gray-400 italic">
                        Tidak ada data indikator yang ditemukan.
                    </div>

                    <div v-if="props.tableData?.current_page" class="p-8 border-t border-gray-400 bg-gray-50/30 flex justify-between items-center">
                        <p class="text-xs font-black text-[#A2B5CB] uppercase tracking-widest">Halaman {{ props.tableData.current_page }} dari {{ props.tableData.last_page }}</p>
                        <div class="flex gap-3">
                            <Link v-if="props.tableData.prev_page_url" :href="props.tableData.prev_page_url" class="p-3 border border-gray-400 rounded-xl hover:bg-white transition-all"><svg class="h-4 w-4 text-[#000B58]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></Link>
                            <Link v-if="props.tableData.next_page_url" :href="props.tableData.next_page_url" class="p-3 border border-gray-400 rounded-xl hover:bg-white transition-all"><svg class="h-4 w-4 text-[#000B58]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></Link>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'Metadata'" class="animate-in slide-in-from-bottom-4 duration-500">
                <div class="grid lg:grid-cols-2 gap-8">
                    <div class="space-y-8">
                        <div class="bg-white border border-gray-200 p-10 rounded-[2.5rem] shadow-sm">
                            <h4 class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                Klasifikasi Data
                            </h4>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2"><span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Urusan</span><span class="text-sm font-black text-[#000B58] text-right">{{ dataset?.urusan?.nama_urusan || '-' }}</span></div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2"><span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Bidang</span><span class="text-sm font-black text-[#000B58] text-right">{{ dataset?.bidang?.nama_bidang || '-' }}</span></div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2"><span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tema</span><span class="text-sm font-black text-[#000B58] text-right">{{ dataset?.tema?.nama_tema || '-' }}</span></div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 p-10 rounded-[2.5rem] shadow-sm">
                            <h4 class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                Atribut Teknis
                            </h4>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Satuan Global</span>
                                    <span class="text-sm font-black text-[#000B58] bg-blue-50 px-3 py-1 rounded-lg">{{ dataset?.satuan || '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2"><span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Frekuensi</span><span class="text-sm font-black text-[#000B58]">{{ dataset?.frekuensi?.nama_frekuensi || 'Tahunan' }}</span></div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2"><span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tahun Data Utama</span><span class="text-sm font-black text-[#000B58]">{{ dataset?.tahun || '-' }}</span></div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2"><span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status Data</span><span :class="dataset?.status === 'aktif' ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50'" class="px-3 py-1 rounded-full text-[10px] font-black uppercase">{{ dataset?.status || '-' }}</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="bg-white border border-gray-200 p-10 rounded-[2.5rem] shadow-sm h-full">
                            <div class="mb-10">
                                <h4 class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] mb-4 flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg> Deskripsi Lengkap</h4>
                                <p class="text-gray-600 text-sm leading-loose font-medium text-justify">{{ dataset?.deskripsi || 'Tidak ada deskripsi yang tersedia untuk dataset ini.' }}</p>
                            </div>
                            
                            <div class="mb-10">
                                <h4 class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] mb-4 flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg> Timeline Sistem</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-3 rounded-xl"><p class="text-[10px] font-bold text-gray-400 uppercase">Dibuat</p><p class="text-xs font-black text-gray-700 mt-1">{{ formatDate(dataset?.created_at) }}</p></div>
                                    <div class="bg-gray-50 p-3 rounded-xl"><p class="text-[10px] font-bold text-gray-400 uppercase">Update Terakhir</p><p class="text-xs font-black text-gray-700 mt-1">{{ formatDate(dataset?.updated_at) }}</p></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'Infografis'" class="animate-in slide-in-from-bottom-4 duration-500">
                <div class="grid lg:grid-cols-4 gap-8">
                    <div class="lg:col-span-3 bg-white border border-gray-400 p-8 rounded-[3rem] shadow-xl shadow-blue-900/5 relative overflow-hidden flex flex-col">
                        <div class="relative z-10 flex justify-between items-end mb-6">
                            <div>
                                <h4 class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                    Tren Data
                                </h4>
                                <h3 class="text-2xl font-black text-[#000B58]">Perbandingan Data</h3>
                            </div>
                            
                            <div class="bg-[#00139E] text-white px-4 py-2 rounded-xl text-xs font-bold shadow-lg shadow-blue-900/20 transition-all duration-300">
                                Satuan: {{ dynamicSatuan }}
                            </div>
                        </div>
                        <div class="relative w-full h-[450px]">
                            <Line v-if="chartConfig && chartConfig.datasets.length > 0" :data="chartConfig" :options="chartOptions" />
                            <div v-else class="flex flex-col items-center justify-center h-full text-gray-400 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                                <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <p class="text-xs font-bold">Silakan centang indikator di samping untuk menampilkan grafik.</p>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-gray-50 border border-gray-200 p-6 rounded-[2.5rem] h-full max-h-[600px] flex flex-col">
                            <h4 class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                Pilih Indikator
                            </h4>

                            <div v-if="props.allData && props.allData.length > 0" class="overflow-y-auto pr-2 space-y-3 custom-scrollbar flex-1">
                                <div v-for="(row, index) in props.allData" :key="index" @click="toggleSelection(index)"
                                    class="p-3 rounded-xl border transition-all cursor-pointer group flex items-start gap-3"
                                    :class="selectedIndices.includes(index) ? 'bg-white border-blue-200 shadow-md' : 'bg-transparent border-transparent hover:bg-white hover:border-gray-200'">
                                    
                                    <div class="mt-0.5 w-5 h-5 rounded-md border flex items-center justify-center transition-colors"
                                         :class="selectedIndices.includes(index) ? 'bg-[#00139E] border-[#00139E]' : 'bg-white border-gray-300'">
                                        <svg v-if="selectedIndices.includes(index)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-700 leading-tight group-hover:text-[#00139E] transition-colors">
                                            {{ row.nama_indikator || row['Nama Data'] || row['Nama Indikator'] || row['Uraian'] || Object.values(row)[0] }}
                                        </p>
                                        <div v-if="selectedIndices.includes(index)" class="mt-1.5 h-1 w-8 rounded-full" :style="{ backgroundColor: chartColors[selectedIndices.indexOf(index) % chartColors.length] }"></div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center text-gray-400 text-xs py-10">Data untuk chart tidak tersedia.</div>
                            
                            <p class="text-[10px] text-gray-400 text-center mt-4 pt-4 border-t border-gray-200">
                                Klik item untuk menampilkan/menyembunyikan garis pada grafik.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>