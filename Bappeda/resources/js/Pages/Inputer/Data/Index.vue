<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteModal from '@/Components/Layout/DeleteModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import debounce from 'lodash/debounce';

defineOptions({ layout: AppLayout });

// Menggabungkan props dari versi Spreadsheet dan versi CRUD asli
const props = defineProps({
    recentUploads: {
        type: Array,
        default: () => []
    },
    isAdmin: {
        type: Boolean,
        default: false
    },
    groupedData: {
        type: Object,
        default: () => ({})
    },
    timeColumns: {
        type: Array,
        default: () => []
    },
    metadata: {
        type: Object,
        default: () => ({})
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

// ==========================================
// 1. STATE & LOGIKA CRUD (HAPUS DATA & STATUS)
// ==========================================
const showDeleteModal = ref(false);
const dataToDelete = ref(null);

const getStatusClass = (status) => {
    const map = {
        'valid': 'bg-blue-50 text-[#00139E] border-blue-100',
        'processing': 'bg-amber-50 text-amber-600 border-amber-100',
        'pending': 'bg-amber-50 text-amber-600 border-amber-100',
        'rejected': 'bg-rose-50 text-rose-600 border-rose-100',
    };
    return map[status] || 'bg-gray-50 text-gray-500 border-gray-100';
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', { 
        day: 'numeric', 
        month: 'long', 
        year: 'numeric' 
    });
};

const openDeleteModal = (upload) => {
    dataToDelete.value = upload;
    showDeleteModal.value = true;
};

const executeDeleteAction = () => {
    if (dataToDelete.value) {
        router.delete(`/inputer/data/${dataToDelete.value.id_upload}`, {
            onSuccess: () => {
                showDeleteModal.value = false;
                dataToDelete.value = null;
            }
        });
    }
};

// ==========================================
// 2. STATE & LOGIKA SPREADSHEET (FILTER & GROUPING)
// ==========================================
const getTahunanId = () => {
    if (!props.metadata?.frekuensi) return '';
    const tahunan = props.metadata.frekuensi.find(f => f.nama_frekuensi.toLowerCase() === 'tahunan');
    return tahunan ? tahunan.id_frekuensi : '';
};

// ... kode sebelumnya ...

const form = ref({
    search: props.filters.search || '',
    tema: props.filters.tema || '',
    urusan: props.filters.urusan || '',
    bidang: props.filters.bidang || '',
    frekuensi: props.filters.frekuensi || getTahunanId(), 
    group_by: props.filters.group_by || 'tema',
    periode: props.filters.periode || '', 
    tahun_terbit: props.filters.tahun_terbit || ''
});

const updateView = debounce(() => {
    router.get('/inputer/data', form.value, { 
        preserveState: true, 
        preserveScroll: true,
        only: ['groupedData', 'timeColumns', 'filters', 'recentUploads'] 
    });
}, 500);

watch(form, () => { updateView(); }, { deep: true });

const filteredTimeColumns = computed(() => {
    if (!props.timeColumns) return [];
    
    const selectedFreq = props.metadata.frekuensi?.find(f => f.id_frekuensi === form.value.frekuensi);
    const freqName = selectedFreq ? selectedFreq.nama_frekuensi.toLowerCase() : '';
    
    // [BARU] Ambil kata kunci pencarian periode (misal: "2020", "Maret")
    const searchPeriode = form.value.periode.toLowerCase().trim();

    return props.timeColumns.filter(col => {
        const rawCol = String(col).trim().toLowerCase();
        
        // Kita juga cek hasil formatnya agar bisa mengenali format seperti "2020-03" yang diubah jadi "Maret 2020"
        const formattedCol = formatTimeHeader(col).toLowerCase(); 

        // [BARU] Logika filter pencarian waktu
        if (searchPeriode && !rawCol.includes(searchPeriode) && !formattedCol.includes(searchPeriode)) {
            return false; // Sembunyikan kolom jika tidak cocok dengan ketikan
        }

        // Logika Frekuensi asli (Tahunan / Bulanan)
        const isTahunAngka = /^\d{4}$/.test(rawCol); 

        if (freqName.includes('tahun')) {
            return isTahunAngka;
        } else if (freqName.includes('bulan') || freqName.includes('minggu') || freqName.includes('hari')) {
            return !isTahunAngka;
        }
        return true; 
    });
});

// ... kode setelahnya ...

const getValue = (values, timeKey) => {
    if (!values) return '-';
    const found = values.find(v => String(v.tahun).trim() === String(timeKey).trim()); 
    return found ? found.nilai : '-';
};

const getGroupLabel = () => {
    if (form.value.group_by === 'urusan') return '🏛️ Urusan Pemerintahan';
    if (form.value.group_by === 'bidang') return '🏢 Bidang / Instansi';
    if (form.value.group_by === 'frekuensi') return '⏰ Frekuensi Data';
    return '📂 Tema Sektoral';
};

const formatTimeHeader = (timeString) => {
    if (timeString === null || timeString === undefined) return '-';
    try {
        let str = String(timeString).trim(); 
        if (/^\d{4}-\d{2}-\d{2}$/.test(str)) {
            return new Date(str).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        }
        if (/^\d{4}-\d{2}$/.test(str)) {
            const [year, month] = str.split('-');
            return new Date(year, month - 1).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }); 
        }
        if (/^\d{4}-W\d{2}$/.test(str)) {
            const [year, week] = str.split('-W');
            return `Minggu ${parseInt(week)}, ${year}`; 
        }
        return str.replace(/\b\w/g, char => char.toUpperCase());
    } catch (e) {
        return String(timeString).toUpperCase(); 
    }
};

const isSpreadsheetView = ref(true); // Toggle antara tampilan Spreadsheet dan List Upload
</script>

<template>
    <Head title="Kelola Data Indikator" />

    <div class="bg-gray-50 py-8 px-4 sm:px-6 lg:px-8 min-h-screen">
        <div class="max-w-[98%] mx-auto">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-8">
                <div>
                    <h1 class="text-4xl font-black text-[#000B58] tracking-tight uppercase">
                        Manajemen <span class="text-[#00139E]">Data</span>
                    </h1>
                    <p class="text-slate-400 font-bold text-[10px] mt-2 uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-[#00139E] rounded-full animate-pulse"></span>
                        Lihat, filter, dan bandingkan seluruh indikator.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                    <button @click="isSpreadsheetView = !isSpreadsheetView" 
                        class="bg-white text-gray-600 border border-gray-300 px-6 py-3 rounded-2xl font-black text-[11px] tracking-widest uppercase hover:bg-gray-100 transition-all shadow-sm flex items-center justify-center gap-2">
                        <svg v-if="isSpreadsheetView" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                        {{ isSpreadsheetView ? 'Lihat Riwayat Upload' : 'Lihat Spreadsheet' }}
                    </button>

                    <Link href="/inputer/data/input-single" 
                        class="bg-white text-[#00139E] border-2 border-[#00139E] px-8 py-3 rounded-2xl font-black text-[11px] tracking-widest uppercase hover:bg-blue-50 transition-all shadow-lg flex items-center justify-center gap-3 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Input Single
                    </Link>

                    <Link href="/inputer/data/input-multi" 
                        class="bg-[#00139E] text-white px-8 py-3 rounded-2xl font-black text-[11px] tracking-widest uppercase hover:bg-[#000B58] transition-all shadow-xl shadow-blue-900/10 flex items-center justify-center gap-3 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Bulk Upload
                    </Link>
                </div>
            </div>

            <div v-if="isSpreadsheetView">
                <div class="bg-white p-6 rounded-[1.5rem] shadow-lg border border-gray-200 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                        
                        <div class="lg:col-span-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Cari Indikator</label>
                            <input v-model="form.search" type="text" placeholder="Ketik nama data..." class="w-full rounded-xl border-gray-300 text-sm font-bold focus:ring-[#00139E] focus:border-[#00139E]">
                        </div>

                        <div class="bg-blue-50/50 p-2 rounded-xl border border-blue-100">
                            <label class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1 block">Kelompokkan Data</label>
                            <select v-model="form.group_by" class="w-full rounded-lg border-blue-200 text-sm font-black text-[#00139E] bg-white focus:ring-[#00139E]">
                                <option value="tema">Per Tema</option>
                                <option value="urusan">Per Urusan</option>
                                <option value="bidang">Per Bidang</option>
                                <option value="frekuensi">Per Frekuensi</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Filter Urusan</label>
                            <select v-model="form.urusan" class="w-full rounded-xl border-gray-300 text-xs font-bold text-gray-600">
                                <option value="">Semua Urusan</option>
                                <option v-for="u in metadata.urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Filter Bidang</label>
                            <select v-model="form.bidang" class="w-full rounded-xl border-gray-300 text-xs font-bold text-gray-600">
                                <option value="">Semua Bidang</option>
                                <option v-for="b in metadata.bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Filter Frekuensi</label>
                            <select v-model="form.frekuensi" class="w-full rounded-xl border-gray-300 text-xs font-bold text-gray-600">
                                <option value="">Semua Waktu</option>
                                <option v-for="f in metadata.frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1 block">Cari Field</label>
                            <input v-model="form.periode" type="text" placeholder="Cth: 2020, Maret..." 
                                class="w-full rounded-xl border-emerald-200 bg-emerald-50/30 text-xs font-bold focus:ring-emerald-500 focus:border-emerald-500 placeholder-emerald-300">
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Tahun Terbit Data</label>
                            <select v-model="form.tahun_terbit" class="w-full rounded-xl border-gray-300 text-xs font-bold text-[#00139E]">
                                <option value="">Semua Tahun Terbit</option>
                                <option v-for="t in metadata.tahun_terbit" :key="t" :value="t">{{ t }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-300 rounded-2xl overflow-hidden shadow-2xl shadow-blue-900/5 flex flex-col max-h-[70vh]">
                    <div class="overflow-auto flex-1 custom-scrollbar">
                        <table class="w-full text-left border-collapse relative">
                            <thead class="bg-[#000B58] text-white sticky top-0 z-40">
                                <tr>
                                    <th class="p-4 text-[10px] uppercase font-black tracking-widest w-[400px] border-r border-white/10 sticky left-0 bg-[#000B58] z-30 shadow-lg">Nama Indikator</th>
                                    <th class="p-4 text-[10px] uppercase font-black tracking-widest w-[100px] text-center border-r border-white/10">Satuan</th>
                                    <th class="p-4 text-[10px] uppercase font-black tracking-widest w-[100px] text-center border-r border-white/10">Frekuensi</th>
                                    
                                    <th v-for="(col, index) in filteredTimeColumns" :key="'header-' + index" class="p-4 text-[10px] uppercase font-black tracking-widest min-w-[120px] text-center border-r border-white/10 whitespace-nowrap bg-[#00139E]">
                                        {{ formatTimeHeader(col) }}
                                    </th>
                                    
                                    <th class="p-4 text-[10px] uppercase font-black tracking-widest w-[160px] text-center sticky right-0 bg-[#000B58] z-30 shadow-[-4px_0_8px_rgba(0,0,0,0.1)]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(groupItems, groupName) in groupedData" :key="groupName">
                                    <tr class="bg-gray-100 sticky top-[49px] z-20">
                                        <td :colspan="filteredTimeColumns.length + 4" class="p-3 text-xs font-black text-[#00139E] uppercase tracking-widest border-b border-gray-300 shadow-sm">
                                            {{ getGroupLabel() }}: {{ groupName }}
                                        </td>
                                    </tr>

                                    <tr v-for="item in groupItems" :key="item.id_data" class="hover:bg-blue-50/50 transition-colors border-b border-gray-200 group">
                                        
                                        <td class="p-3 text-xs font-bold text-gray-700 border-r border-gray-100 sticky left-0 bg-white group-hover:bg-blue-50 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.05)]">
                                            <a :href="`/dataset/${item.id_data}`" class="hover:text-blue-600 hover:underline leading-relaxed block">
                                                {{ item.nama_data }}
                                            </a>
                                            <div class="flex gap-2 mt-1 opacity-60 text-[9px] uppercase font-black text-gray-400">
                                                <span v-if="form.group_by !== 'tema'">{{ item.tema?.nama_tema }}</span>
                                                <span v-if="form.group_by !== 'urusan'">• {{ item.urusan?.nama_urusan }}</span>
                                            </div>
                                        </td>
                                        
                                        <td class="p-3 text-[10px] font-bold text-gray-500 text-center border-r border-gray-100 bg-gray-50/30">
                                            {{ item.satuan }}
                                        </td>
                                        
                                        <td class="p-3 text-[10px] font-bold text-center border-r border-gray-100 uppercase" 
                                            :class="item.frekuensi?.nama_frekuensi === 'Tahunan' ? 'text-emerald-600 bg-emerald-50' : 'text-amber-600 bg-amber-50'">
                                            {{ item.frekuensi?.nama_frekuensi || '-' }}
                                        </td>

                                        <td v-for="(col, index) in filteredTimeColumns" :key="'data-' + index" class="p-3 text-xs font-black text-gray-800 text-center border-r border-gray-100">
                                            {{ getValue(item.values, col) }}
                                        </td>

                                        <td class="p-3 text-center sticky right-0 bg-white group-hover:bg-blue-50 border-l border-gray-200 z-10 shadow-[-2px_0_5px_rgba(0,0,0,0.05)]">
                                            <div class="flex justify-center gap-2">
                                                <a :href="`/export/data/${item.id_data}`" target="_blank" title="Download Excel"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" stroke-width="2.5" /></svg>
                                                </a>
                                                
                                                <Link :href="`/inputer/data/${item.id_data}/edit`" title="Edit Data"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-[#00139E] hover:bg-[#000B58] hover:text-white transition-all shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" /></svg>
                                                </Link>

                                                <button @click="openDeleteModal({ id_upload: item.id_data, nama_data: item.nama_data })" title="Hapus Data"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" /></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                
                                <tr v-if="!groupedData || Object.keys(groupedData).length === 0">
                                    <td :colspan="filteredTimeColumns.length + 4" class="p-10 text-center text-gray-400 font-bold italic">
                                        Data tidak ditemukan dengan filter tersebut. Pastikan Controller `DataInputController` mengirimkan data `groupedData`.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <p class="text-[10px] text-gray-400 mt-4 italic">* Gunakan scroll horizontal untuk melihat tahun lainnya.</p>
            </div>

            <div v-else class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-100 border border-gray-400 overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-left text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-400">
                            <th class="p-8">Indikator & Klasifikasi</th>
                            <th class="text-center">Urusan / Bidang</th>
                            <th class="text-center">Satuan</th>
                            <th class="text-center">Periode</th>
                            <th class="text-center">Status</th>
                            <th v-if="isAdmin" class="text-center">Operator</th>
                            <th class="text-right p-8">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-bold text-gray-800">
                        <tr v-for="u in recentUploads" :key="u.id_upload" 
                            class="border-b border-gray-400 last:border-0 hover:bg-blue-50/20 transition-all group">
                            
                            <td class="p-8 max-w-[300px]">
                                <div v-if="u.data">
                                    <Link :href="`/dataset/${u.data.id_data}`" 
                                        class="text-[13px] text-[#000B58] font-black uppercase tracking-tight group-hover:text-[#00139E] transition-colors line-clamp-2">
                                        {{ u.data.nama_data }}
                                    </Link>
                                    <div class="flex flex-col gap-1 mt-2">
                                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">ID: #{{ u.data.id_data }}</span>
                                    </div>
                                </div>
                                <div v-else>
                                    <span class="text-rose-500 italic text-[11px] font-black uppercase tracking-widest">Relasi Terputus</span>
                                </div>
                            </td>

                            <td class="text-center py-4">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-[10px] font-black text-[#000B58] uppercase px-3 py-1 bg-gray-50 rounded-lg border border-gray-100">
                                        {{ u.data?.urusan?.nama_urusan || 'N/A' }}
                                    </span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase italic">
                                        {{ u.data?.bidang?.nama_bidang || 'N/A' }}
                                    </span>
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="text-[10px] font-black text-slate-500 uppercase">
                                    {{ u.data?.satuan || '-' }}
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="text-[#000B58] text-[10px] font-black uppercase tracking-widest bg-blue-50 px-3 py-1.5 rounded-xl border border-blue-100">
                                    {{ u.periode }}
                                </span>
                            </td>

                            <td class="text-center">
                                <span :class="getStatusClass(u.status)" class="text-[9px] font-black uppercase px-4 py-2 rounded-xl border tracking-widest shadow-sm">
                                    {{ u.status }}
                                </span>
                            </td>

                            <td v-if="isAdmin" class="text-center">
                                <div class="inline-flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100">
                                    <span class="text-[10px] font-black text-[#000B58] uppercase tracking-tighter">
                                        {{ u.user?.name || 'Unknown' }}
                                    </span>
                                </div>
                            </td>

                            <td class="p-8 text-right">
                                <div class="flex justify-end gap-2">
                                    <a :href="`/export/data/${u.id_upload}`" target="_blank" title="Download Excel"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-blue-50 text-[#00139E] hover:bg-[#00139E] hover:text-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" stroke-width="2.5" /></svg>
                                    </a>
                                    
                                    <Link :href="`/inputer/data/${u.data?.id_data}/edit`" title="Edit Data"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-blue-50 text-[#00139E] hover:bg-[#000B58] hover:text-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" /></svg>
                                    </Link>

                                    <button @click="openDeleteModal(u)" title="Hapus Data"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="recentUploads.length === 0">
                            <td :colspan="isAdmin ? 7 : 6" class="p-20 text-center text-gray-300 uppercase italic tracking-[0.3em] text-xs">
                                Belum ada riwayat aktivitas input
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <DeleteModal 
            :show="showDeleteModal" 
            :title="'Hapus Data?'"
            :description="'Entri data \'' + (dataToDelete?.data?.nama_data || dataToDelete?.nama_data || 'ini') + '\' akan dihapus dari riwayat upload. Pastikan Anda telah memiliki cadangan data.'"
            @close="showDeleteModal = false"
            @confirm="executeDeleteAction"
        />

    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { height: 12px; width: 12px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; border: 3px solid #f1f5f9; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #00139E; }
</style>