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
    customSatuan: String  
});

const activeTab = ref('Data');
// [BARU] Menambahkan Tab 'Riwayat'
const tabs = ['Data', 'Metadata', 'Infografis', 'Riwayat']; 
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

// --- 2. AMBIL KUNCI (HEADER) UNTUK ATRIBUT TAMBAHAN ---
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

// --- 3. AMBIL KUNCI (HEADER) UNTUK WAKTU/NILAI DAN URUTKAN ---
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
        'jan': 1, 'feb': 2, 'mar': 3, 'apr': 4, 'jun': 6, 'jul': 7, 'agu': 8, 'sep': 9, 'okt': 10, 'nov': 11, 'des': 12,
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

// --- 4. CONFIG CHART ---
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
            borderWidth: 2,
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

// [BARU] Helper untuk Format Label Tipe Aksi Riwayat
// 1. UPDATE HELPER ACTION NAME
const getActionName = (filePath) => {
    if (!filePath) return 'Pembaruan Sistem';
    if (filePath === 'system_init') return 'Registrasi Data Awal'; // <--- [BARU]
    if (filePath === 'manual_input') return 'Input Data Awal (Baru)';
    if (filePath === 'edit_manual') return 'Perubahan Data (Edit)';
    if (filePath.includes('.xls') || filePath.includes('.csv')) return 'Upload via Excel (Bulk)';
    return 'Pembaruan Data';
};

// 2. UPDATE HELPER FORMAT SNAPSHOT
const formatSnapshotValue = (jsonValue) => {
    if (!jsonValue) return 'Tidak ada rekam nilai tersimpan.';
    try {
        let parsed = typeof jsonValue === 'string' ? JSON.parse(jsonValue) : jsonValue;
        
        // [BARU] Jika ini adalah log virtual buatan sistem
        if (parsed.pesan) return parsed.pesan;

        // Format dari Input Single / Edit
        if (parsed.values && Array.isArray(parsed.values)) {
            return parsed.values.map(v => `${v.tahun} = ${v.nilai}`).join('   |   ');
        }
        
        // Format dari Upload Bulk Excel
        if (parsed.dataset) {
            return 'Data diekstrak dari dokumen Excel massal.';
        }

        return JSON.stringify(parsed);
    } catch(e) {
        return 'Format data tidak terbaca.';
    }
};

// 3. [BARU] LOGIKA UNTUK MENGGABUNGKAN LOG ASLI DAN LOG VIRTUAL (AWAL)
const timelineHistory = computed(() => {
    if (!props.dataset) return [];

    // Salin array log dari database
    let logs = [...(props.dataset.uploads || [])];

    // Cek apakah di database sudah ada log yang menandakan input awal
    const hasInitialLog = logs.some(log => 
        log.file_path === 'manual_input' || 
        (log.file_path && (log.file_path.includes('.xls') || log.file_path.includes('.csv')))
    );

    // Jika tidak ada log awal (karena data lama / bulk upload), kita buat log "Virtual" di awal waktu
    if (!hasInitialLog) {
        logs.push({
            id_upload: 'virtual-init-' + props.dataset.id_data,
            id_user: props.dataset.id_user || 'Sistem',
            created_at: props.dataset.created_at, // Ambil dari waktu pembuatan master
            status: props.dataset.status === 'aktif' ? 'valid' : 'pending',
            file_path: 'system_init',
            value: '{"pesan": "Data pertama kali diregistrasikan ke dalam sistem."}'
        });
    }

    // Urutkan riwayat dari yang terbaru (atas) ke yang terlama (bawah)
    return logs.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});

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
const toggleBookmark = () => {
    // Ambil ID, bisa dari id_data atau id biasa
    const dataId = props.dataset?.id_data || props.dataset?.id; 

    // Jika ID tetap tidak ketemu, beri peringatan (alert) agar kita tahu errornya
    if (!dataId) {
        alert("Gagal: ID Dataset tidak ditemukan!");
        console.log("Isi dataset:", props.dataset);
        return;
    }
    
    // Kirim request ke backend
    router.post(`/inputer/data/${dataId}/bookmark`, {}, {
        preserveScroll: true,
    });
};


</script>

<template>
    <Head :title="dataset?.nama_data || 'Detail Data'" />

    <div class="min-h-screen bg-white font-sans mt-20">
        <section class="relative pt-20 overflow-hidden w-full"> 
            <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[600px] h-[600px] bg-[#A2B5CB]/10 rounded-full blur-3xl -z-10"></div>
            
            <div class="max-w-[80%] mx-auto">
               <div class="flex items-center gap-2 text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] mb-8">
                    <Link href="/" class="hover:text-[#00139E] transition-colors">Beranda</Link> 
                    <span class="text-gray-300">/</span>
                    <Link href="/cari" class="hover:text-[#00139E] transition-colors">Cari</Link>
                    <span class="text-gray-300">/</span>
                    <span class="text-[#000B58]">Detail Indikator</span>

                    <div class="ml-auto flex items-center gap-3">
                        
                        <button @click="toggleBookmark" 
                            class="px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 border"
                            :class="dataset?.is_pinned 
                                ? 'bg-[#00139E] text-white border-[#00139E] shadow-lg' 
                                : 'bg-white text-[#000B58] border-gray-300 hover:bg-gray-50'">
                            
                            <svg class="w-4 h-4" :fill="dataset?.is_pinned ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            {{ dataset?.is_pinned ? 'Tersimpan di Dashboard' : 'Pin Data' }}
                        </button>

                        <Link v-if="dataset?.id_data" :href="`/inputer/data/${dataset.id_data}/edit`" 
                            class="bg-amber-400 text-[#000B58] px-4 py-2 rounded-xl text-xs font-black hover:bg-amber-500 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Data
                        </Link>

                    </div>
                  
                </div>
                <div class="grid lg:grid-cols-3 gap-16 items-start">
                    <div class="lg:col-span-2">
                        <span class="inline-block px-4 py-1.5 mb-6 text-sm font-bold text-[#00139E] bg-[#A2B5CB]/20 rounded-full border border-[#A2B5CB]/30 tracking-wide uppercase">
                            {{ dataset?.tema?.nama_tema || 'Indikator Pembangunan' }}
                        </span>
                        <h1 class="text-4xl lg:text-6xl font-black text-[#000B58] leading-[1.2] mb-8">
                            {{ dataset?.nama_data || 'Data Tidak Ditemukan' }}
                        </h1>
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

        <section class="max-w-[80%] mx-auto pb-20">
            <div class="flex flex-wrap gap-4 mb-10 bg-gray-50 p-2 rounded-[2rem] border border-gray-400 w-fit">
                <button v-for="tab in tabs" :key="tab" @click="activeTab = tab"
                    class="px-8 py-3 rounded-[1.5rem] text-sm font-black transition-all duration-300"
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
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-white font-black uppercase tracking-widest text-lg">Preview Capaian Nilai</h3>
                                    <p class="text-blue-200 text-xs font-medium mt-1 opacity-80">
                                      Geser ke samping untuk melihat seluruh periode waktu. Menampilkan {{ tableRows.length }} baris.
                                    </p>
                                </div>
                            </div>
                            
                            <a v-if="dataset?.id_data" :href="`/export/data/${dataset.id_data}`" 
                                class="bg-[#00139E] text-white px-6 py-3 rounded-xl text-sm font-bold hover:bg-[#000B58] transition-all flex items-center gap-2 shadow-lg border border-blue-800/50">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" /></svg>
                                Export to Excel
                            </a>
                        </div>
                    </div>

                    <div class="border-x border-b border-gray-200 rounded-b-[2rem] overflow-hidden bg-white relative">
                        <div class="overflow-x-auto custom-scrollbar" v-if="tableRows.length > 0">
                            <table class="w-full text-left border-collapse whitespace-nowrap">
                             <thead>
                                <tr class="bg-gray-50/80">
                                    <th class="p-5 bg-gray-100/90 border-b-2 border-r border-gray-200 text-[11px] font-black text-[#A2B5CB] uppercase tracking-[0.15em] sticky left-0 z-20 min-w-[250px] shadow-[4px_0_8px_-4px_rgba(0,0,0,0.1)] backdrop-blur-sm">
                                        Nama Indikator
                                    </th>
                                    <th class="p-5 border-b-2 border-r border-gray-200 text-[10px] font-black text-[#A2B5CB] uppercase tracking-widest text-center w-24 bg-gray-50/50">
                                        Satuan
                                    </th>
                                    <th v-for="key in extraFieldKeys" :key="'th-'+key" class="p-5 border-b-2 border-r border-gray-200 text-[10px] font-black text-[#A2B5CB] uppercase tracking-widest text-center bg-gray-50/50">
                                        {{ key }}
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
                                    <td class="p-5 bg-white border-r border-gray-200 font-bold text-[#000B58] text-sm sticky left-0 z-10 shadow-[4px_0_8px_-4px_rgba(0,0,0,0.1)] group-hover:bg-[#f8fafc]">
                                        <div class="line-clamp-2 w-[280px]" :title="row['Nama Indikator'] || row.nama_data || row['Nama Data'] || 'Data'">
                                            {{ (props.tableData?.from || 1) + idx }}. {{ row['Nama Indikator'] || row.nama_data || row['Nama Data'] || row['Uraian'] || 'Indikator Tidak Diketahui' }}
                                        </div>
                                    </td>
                                    <td class="p-5 border-r border-gray-100 text-xs font-bold text-gray-500 text-center bg-white group-hover:bg-[#f8fafc]">
                                        {{ getSatuanFromRow(row) }}
                                    </td>
                                    <td v-for="key in extraFieldKeys" :key="'td-'+key" class="p-5 border-r border-gray-100 text-xs font-bold text-gray-500 text-center bg-white group-hover:bg-[#f8fafc]">
                                        {{ row[key] || '-' }}
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
                            </div>
                        </div>
                    </div>
                    <div class="space-y-8">
                        <div class="bg-white border border-gray-400 p-10 rounded-[2.5rem] shadow-sm h-full">
                            <div class="mb-10">
                                <h4 class="text-xl font-black text-[#000B58] uppercase tracking-[0.2em] mb-4 flex items-center gap-2"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg> Deskripsi Lengkap</h4>
                                <p class="text-gray-800 text-s leading-loose text-justify">{{ dataset?.deskripsi || 'Tidak ada deskripsi yang tersedia.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'Infografis'" class="animate-in slide-in-from-bottom-4 duration-500">
                <div class="grid lg:grid-cols-4 gap-8">
                    <div class="lg:col-span-3 bg-white border border-gray-400 p-10 rounded-[2.5rem] shadow-sm relative overflow-hidden flex flex-col h-[600px]">
                        <div class="relative w-full h-[500px] bg-gray-50/30 rounded-[2rem] border border-gray-100 p-4">
                            <Line v-if="chartConfig && chartConfig.datasets.length > 0" :data="chartConfig" :options="chartOptions" />
                            <div v-else class="flex flex-col items-center justify-center h-full text-gray-400 bg-gray-50/50">Data Belum Dipilih</div>
                        </div>
                    </div>
                    <div class="lg:col-span-1">
                        <div class="bg-white border border-gray-400 p-8 rounded-[2.5rem] shadow-sm h-full flex flex-col min-h-[600px]">
                            <h4 class="text-lg font-black text-[#000B58] mb-6">Pilih Data</h4>
                            <div v-if="props.allData && props.allData.length > 0" class="overflow-y-auto custom-scrollbar flex-1">
                                <div v-for="(row, index) in props.allData" :key="index" @click="toggleSelection(index)"
                                    class="p-4 rounded-[1.5rem] border cursor-pointer mb-2"
                                    :class="selectedIndices.includes(index) ? 'bg-blue-50 border-blue-200' : 'bg-white'">
                                    <p class="text-xs font-black text-gray-700">{{ row.nama_data || row['Nama Indikator'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'Riwayat'" class="animate-in slide-in-from-bottom-4 duration-500">
                <div class="bg-white border border-gray-400 p-10 rounded-[2.5rem] shadow-sm max-w-4xl mx-auto">
                    <div class="mb-10 text-center">
                        <h4 class="text-2xl font-black text-[#000B58] uppercase tracking-[0.2em] mb-2 flex items-center justify-center gap-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Histori Perubahan Data
                        </h4>
                        <p class="text-sm text-gray-500 font-medium">Melacak rekam jejak kapan data ini ditambahkan atau direvisi dari waktu ke waktu.</p>
                    </div>

                    <div v-if="dataset?.uploads && dataset.uploads.length > 0" class="relative border-l-2 border-[#00139E]/20 ml-4 space-y-10 pb-6">
                        
                        <div v-for="(log, idx) in timelineHistory" :key="log.id_upload" class="relative pl-10 group">
                            
                            <div class="absolute -left-[11px] top-1 w-5 h-5 rounded-full border-4 border-white shadow-sm transition-all duration-300"
                                 :class="idx === 0 ? 'bg-[#FF1414] scale-125' : 'bg-[#00139E] group-hover:scale-110 group-hover:bg-[#FF1414]'"></div>
                            
                            <div class="bg-gray-50/80 p-6 rounded-[2rem] border border-gray-200 transition-all hover:bg-white hover:shadow-xl hover:border-blue-200">
                                <div class="flex flex-wrap justify-between items-start gap-4 mb-4">
                                    <div>
                                        <span class="text-[10px] font-black uppercase tracking-widest text-white px-3 py-1.5 rounded-lg mb-3 inline-block shadow-sm"
                                            :class="idx === 0 ? 'bg-[#FF1414]' : 'bg-[#00139E]'">
                                            {{ getActionName(log.file_path) }}
                                        </span>
                                        <h5 class="text-sm font-black text-[#000B58]">
                                            Terekam oleh User ID: {{ log.id_user || 'Sistem' }}
                                        </h5>
                                        <p class="text-xs text-gray-500 font-bold mt-1 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ formatDate(log.created_at) }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[10px] font-black px-4 py-2 rounded-full uppercase tracking-wider border"
                                            :class="log.status === 'valid' || log.status === 'aktif' ? 'bg-green-50 text-green-600 border-green-200' : 'bg-amber-50 text-amber-600 border-amber-200'">
                                            {{ log.status }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div v-if="log.value" class="mt-6 pt-5 border-t border-gray-200/80">
                                    <p class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] mb-3">Snapshot Capaian Nilai:</p>
                                    <div class="bg-white p-4 rounded-2xl border border-gray-200 text-sm font-bold text-gray-700">
                                        {{ formatSnapshotValue(log.value) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div v-else class="text-center py-16 bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-200">
                        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-sm mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <p class="text-sm font-black text-gray-400 uppercase tracking-widest">Belum Ada Riwayat Perubahan</p>
                        <p class="text-xs text-gray-400 font-medium mt-1">Data ini belum pernah mengalami pembaruan sejak dibuat.</p>
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