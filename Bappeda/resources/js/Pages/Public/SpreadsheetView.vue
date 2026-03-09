<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteModal from '@/Components/Layout/DeleteModal.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import debounce from 'lodash/debounce';

defineOptions({ layout: AppLayout });

const props = defineProps({
    groupedData: Object,
    timeColumns: Array,
    metadata: Object, 
    filters: Object,
    isAdmin: {
        type: Boolean,
        default: false
    }
});

const getTahunanId = () => {
    if (!props.metadata?.frekuensi) return '';
    const tahunan = props.metadata.frekuensi.find(f => f.nama_frekuensi.toLowerCase() === 'tahunan');
    return tahunan ? tahunan.id_frekuensi : '';
};

const form = ref({
    search: props.filters.search || '',
    tema: props.filters.tema || '',
    urusan: props.filters.urusan || '',
    bidang: props.filters.bidang || '',
    frekuensi: props.filters.frekuensi || getTahunanId(), 
    group_by: props.filters.group_by || 'tema' 
});

const updateView = debounce(() => {
    router.get('/data-spreadsheet', form.value, { 
        preserveState: true, 
        preserveScroll: true,
        only: ['groupedData', 'timeColumns', 'filters'] 
    });
}, 500);

watch(form, () => { updateView(); }, { deep: true });

const filteredTimeColumns = computed(() => {
    if (!props.timeColumns) return [];
    
    const selectedFreq = props.metadata.frekuensi.find(f => f.id_frekuensi === form.value.frekuensi);
    const freqName = selectedFreq ? selectedFreq.nama_frekuensi.toLowerCase() : '';

    return props.timeColumns.filter(col => {
        const strCol = String(col).trim();
        const isTahunAngka = /^\d{4}$/.test(strCol); 

        if (freqName.includes('tahun')) {
            return isTahunAngka;
        } else if (freqName.includes('bulan') || freqName.includes('minggu') || freqName.includes('hari')) {
            return !isTahunAngka;
        }
        return true; 
    });
});

const getValue = (values, timeKey) => {
    if (!values) return '-';
    const found = values.find(v => String(v.tahun).trim() === String(timeKey).trim()); 
    return found ? found.nilai : '-';
};

const getGroupLabel = () => {
    if (form.value.group_by === 'urusan') return 'Urusan Pemerintahan';
    if (form.value.group_by === 'bidang') return 'Bidang / Instansi';
    if (form.value.group_by === 'frekuensi') return 'Frekuensi Data';
    return 'Tema Sektoral';
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

const showDeleteModal = ref(false);
const dataToDelete = ref(null);

const openDeleteModal = (item) => {
    dataToDelete.value = item;
    showDeleteModal.value = true;
};

const executeDeleteAction = () => {
    if (dataToDelete.value) {
        router.delete(`/inputer/data/${dataToDelete.value.id_data}`, {
            onSuccess: () => {
                showDeleteModal.value = false;
                dataToDelete.value = null;
            },
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Spreadsheet Data Master" />

    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-[98%] mx-auto">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-8">
                <div>
                    <h2 class="text-3xl font-black text-[#000B58] uppercase tracking-tight">Master Data View</h2>
                    <p class="text-gray-500 text-sm font-medium mt-1">Lihat, filter, dan bandingkan seluruh indikator pembangunan dalam satu tampilan.</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
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
                        Bulk Upload (Excel)
                    </Link>
                </div>
            </div>

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
                            <option value="">Semua Waktu (Campur)</option>
                            <option v-for="f in metadata.frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
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

                                            <button @click="openDeleteModal(item)" title="Hapus Data"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            
                            <tr v-if="Object.keys(groupedData).length === 0">
                                <td :colspan="filteredTimeColumns.length + 4" class="p-10 text-center text-gray-400 font-bold italic">
                                    Data tidak ditemukan dengan filter tersebut.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <p class="text-[10px] text-gray-400 mt-4 italic">* Gunakan scroll horizontal untuk melihat tahun lainnya. Tabel otomatis memfilter kolom berdasarkan menu Frekuensi.</p>

        </div>

        <DeleteModal 
            :show="showDeleteModal" 
            :title="'Hapus Indikator Ini?'"
            :description="'Seluruh data untuk indikator \'' + (dataToDelete?.nama_data || 'ini') + '\' akan dihapus secara permanen. Pastikan Anda telah memiliki cadangannya.'"
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