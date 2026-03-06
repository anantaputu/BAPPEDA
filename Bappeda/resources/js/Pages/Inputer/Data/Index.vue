<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteModal from '@/Components/Layout/DeleteModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import debounce from 'lodash/debounce';

defineOptions({ layout: AppLayout });

const props = defineProps({
    recentUploads: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
    groupedData: { type: Object, default: () => ({}) },
    timeColumns: { type: Array, default: () => [] },
    metadata: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) }
});

// ==========================================
// 1. LOGIKA CRUD (DI PERTAHANKAN)
// ==========================================
const showDeleteModal = ref(false);
const dataToDelete = ref(null);

const getStatusClass = (status) => {
    const map = {
        'valid': 'bg-inovasi/10 text-inovasi border-inovasi/20',
        'processing': 'bg-profesional/10 text-profesional border-profesional/20',
        'pending': 'bg-profesional/10 text-profesional border-profesional/20',
        'rejected': 'bg-integritas/10 text-integritas border-integritas/20',
    };
    return map[status?.toLowerCase()] || 'bg-bgsoft text-textsecondary border-gray-200';
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
// 2. LOGIKA SPREADSHEET (DI PERTAHANKAN)
// ==========================================
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
    group_by: props.filters.group_by || 'tema',
    periode: props.filters.periode || '', 
    tahun_terbit: props.filters.tahun_terbit || ''
});

const updateView = debounce(() => {
    router.get('/inputer/data', form.value, { 
        preserveState: true, preserveScroll: true,
        only: ['groupedData', 'timeColumns', 'filters', 'recentUploads'] 
    });
}, 500);

watch(form, () => { updateView(); }, { deep: true });

const formatTimeHeader = (timeString) => {
    if (timeString === null || timeString === undefined) return '-';
    try {
        let str = String(timeString).trim(); 
        if (/^\d{4}-\d{2}-\d{2}$/.test(str)) return new Date(str).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        if (/^\d{4}-\d{2}$/.test(str)) {
            const [year, month] = str.split('-');
            return new Date(year, month - 1).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }); 
        }
        return str.replace(/\b\w/g, char => char.toUpperCase());
    } catch (e) { return String(timeString).toUpperCase(); }
};

const filteredTimeColumns = computed(() => {
    if (!props.timeColumns) return [];
    const selectedFreq = props.metadata.frekuensi?.find(f => f.id_frekuensi === form.value.frekuensi);
    const freqName = selectedFreq ? selectedFreq.nama_frekuensi.toLowerCase() : '';
    const searchPeriode = form.value.periode.toLowerCase().trim();

    return props.timeColumns.filter(col => {
        const rawCol = String(col).trim().toLowerCase();
        const formattedCol = formatTimeHeader(col).toLowerCase(); 
        if (searchPeriode && !rawCol.includes(searchPeriode) && !formattedCol.includes(searchPeriode)) return false;
        const isTahunAngka = /^\d{4}$/.test(rawCol); 
        if (freqName.includes('tahun')) return isTahunAngka;
        else if (freqName.includes('bulan') || freqName.includes('minggu') || freqName.includes('hari')) return !isTahunAngka;
        return true; 
    });
});

const getValue = (values, timeKey) => {
    if (!values) return '-';
    const found = values.find(v => String(v.tahun).trim() === String(timeKey).trim()); 
    return found ? found.nilai : '-';
};

const getGroupLabel = () => {
    const labels = { urusan: '🏛️ Urusan', bidang: '🏢 Bidang', frekuensi: '⏰ Frekuensi' };
    return labels[form.value.group_by] || 'Tema';
};

const isSpreadsheetView = ref(true);
</script>

<template>
    <Head title="Kelola Data Indikator" />

    <div class="min-h-full py-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-8">
            <div>
                <h1 class="text-2xl font-black text-primary uppercase tracking-tight">
                    Manajemen <span class="text-secondary">Data</span>
                </h1>
                <p class="text-[10px] text-textsecondary font-black uppercase tracking-[0.2em] mt-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-secondary rounded-full animate-pulse shadow-sm shadow-secondary/50"></span>
                    Audit & Sinkronisasi Indikator Sektoral
                </p>
            </div>

            <div class="flex flex-wrap gap-3 w-full md:w-auto">
                <button @click="isSpreadsheetView = !isSpreadsheetView" 
                    class="bg-white text-primary border border-gray-400 px-5 py-3 rounded-xl font-black text-[10px] tracking-widest uppercase hover:bg-bgsoft transition-all shadow-sm flex items-center justify-center gap-2 active:scale-95">
                    <component :is="isSpreadsheetView ? 'svg' : 'svg'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="isSpreadsheetView" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </component>
                    {{ isSpreadsheetView ? 'Mode Riwayat' : 'Mode Spreadsheet' }}
                </button>

                <Link href="/inputer/data/input-single" 
                    class="bg-white text-secondary border-2 border-secondary px-6 py-3 rounded-xl font-black text-[10px] tracking-widest uppercase hover:bg-secondary/5 transition-all shadow-sm flex items-center justify-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                    Input Single
                </Link>

                <Link href="/inputer/data/input-multi" 
                    class="bg-primary text-white px-6 py-3 rounded-xl font-black text-[10px] tracking-widest uppercase hover:bg-secondary transition-all shadow-lg shadow-primary/10 flex items-center justify-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Bulk Upload
                </Link>
            </div>
        </div>

        <div v-if="isSpreadsheetView" class="space-y-6">
            <div class="bg-white p-8 rounded-xl shadow-2xl shadow-primary/5 border border-gray-400">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-6">
        
        <div class="xl:col-span-2 space-y-2">
            <label class="block text-[9px] font-black text-textsecondary uppercase tracking-[0.2em] ml-2">
                Cari Nama Data
            </label>
            <div class="relative">
                <input v-model="form.search" type="text" placeholder="Ketik kata kunci..." 
                    class="w-full bg-white border border-gray-400 rounded-xl px-4 py-3 text-xs font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300 placeholder:text-gray-300 shadow-sm">
            </div>
        </div>

        <div class="space-y-2">
            <label class="block text-[9px] font-black text-textsecondary uppercase tracking-[0.2em] ml-2">
                Kelompokkan
            </label>
            <div class="relative group">
                <select v-model="form.group_by" 
                    class="w-full bg-white border border-gray-400 rounded-xl px-4 py-3 text-xs font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300">
                    <option value="tema">Per Tema</option>
                    <option value="urusan">Per Urusan</option>
                    <option value="bidang">Per Bidang</option>
                    <option value="frekuensi">Per Frekuensi</option>
                </select>
            </div>
        </div>

        <div class="space-y-2">
            <label class="block text-[9px] font-black text-textsecondary uppercase tracking-[0.2em] ml-2">
                Urusan
            </label>
            <select v-model="form.urusan" 
                class="w-full bg-white border border-gray-400 rounded-xl px-4 py-3 text-xs font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300">
                <option value="">Semua Urusan</option>
                <option v-for="u in metadata.urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
            </select>
        </div>

        <div class="space-y-2">
            <label class="block text-[9px] font-black text-textsecondary uppercase tracking-[0.2em] ml-2">
                Bidang
            </label>
            <select v-model="form.bidang" 
                class="w-full bg-white border border-gray-400 rounded-xl px-4 py-3 text-xs font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300">
                <option value="">Semua Bidang</option>
                <option v-for="b in metadata.bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
            </select>
        </div>

        <div class="space-y-2">
            <label class="block text-[9px] font-black text-inovasi uppercase tracking-[0.2em] ml-2">
                Filter Field
            </label>
            <input v-model="form.periode" type="text" placeholder="Cth: 2020..." 
                class="w-full bg-white border border-inovasi/30 rounded-xl px-4 py-3 text-xs font-bold text-inovasi focus:outline-none focus:border-inovasi focus:ring-4 focus:ring-inovasi/5 transition-all duration-300 placeholder:text-inovasi/30 shadow-sm">
        </div>

        <div class="space-y-2">
            <label class="block text-[9px] font-black text-textsecondary uppercase tracking-[0.2em] ml-2">
                Tahun Terbit
            </label>
            <select v-model="form.tahun_terbit" 
                class="w-full bg-white border border-gray-400 rounded-xl px-4 py-3 text-xs font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300">
                <option value="">Semua</option>
                <option v-for="t in metadata.tahun_terbit" :key="t" :value="t">{{ t }}</option>
            </select>
        </div>

    </div>
</div>
            <div class="bg-white border border-gray-400 rounded-xl overflow-hidden shadow-sm flex flex-col max-h-[75vh]">
                <div class="overflow-auto flex-1 custom-scrollbar">
                    <table class="w-full text-left border-collapse relative">
                        <thead class="bg-primary text-white sticky top-0 z-40">
                            <tr class="divide-x divide-white/10">
                                <th class="p-5 text-[10px] uppercase font-black tracking-widest min-w-[400px] sticky left-0 bg-primary z-50 shadow-md">Nama Indikator</th>
                                <th class="p-5 text-[10px] uppercase font-black tracking-widest w-[120px] text-center">Satuan</th>
                                <th class="p-5 text-[10px] uppercase font-black tracking-widest w-[120px] text-center">Update</th>
                                <th v-for="col in filteredTimeColumns" :key="col" class="p-5 text-[10px] uppercase font-black tracking-widest min-w-[140px] text-center bg-secondary">
                                    {{ formatTimeHeader(col) }}
                                </th>
                                <th class="p-5 text-[10px] uppercase font-black tracking-widest w-[160px] text-center sticky right-0 bg-primary z-50 shadow-[-4px_0_10px_rgba(0,0,0,0.2)]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-bold text-primary">
                            <template v-for="(groupItems, groupName) in groupedData" :key="groupName">
                                <tr class="bg-bgsoft sticky top-[58px] z-30">
                                    <td :colspan="filteredTimeColumns.length + 4" class="p-4 text-[11px] font-black text-primary uppercase tracking-[0.2em] border-b border-gray-300">
                                        {{ getGroupLabel() }}: <span class="text-secondary">{{ groupName }}</span>
                                    </td>
                                </tr>

                                <tr v-for="item in groupItems" :key="item.id_data" class="hover:bg-bgsoft transition-colors border-b border-gray-100 group divide-x divide-gray-100">
                                    <td class="p-4 sticky left-0 bg-white group-hover:bg-bgsoft z-20 shadow-md transition-colors">
                                        <a :href="`/dataset/${item.id_data}`" class="hover:text-secondary transition-colors leading-relaxed block uppercase text-xs font-black tracking-tight">
                                            {{ item.nama_data }}
                                        </a>
                                        <div class="flex gap-2 mt-1.5 opacity-40 text-[9px] uppercase font-black text-textsecondary">
                                            <span v-if="form.group_by !== 'tema'">{{ item.tema?.nama_tema }}</span>
                                            <span v-if="form.group_by !== 'urusan'">• {{ item.urusan?.nama_urusan }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-[10px] font-black text-center text-textsecondary/60 uppercase">{{ item.satuan }}</td>
                                    <td class="p-4 text-[9px] font-black text-center uppercase tracking-tighter">
                                        <span :class="item.frekuensi?.nama_frekuensi === 'Tahunan' ? 'text-inovasi bg-inovasi/10 border-inovasi/20' : 'text-profesional bg-profesional/10 border-profesional/20'" class="px-2 py-1 rounded-lg border">
                                            {{ item.frekuensi?.nama_frekuensi || '-' }}
                                        </span>
                                    </td>
                                    <td v-for="col in filteredTimeColumns" :key="col" class="p-4 text-xs font-black text-center group-hover:bg-white/50">
                                        <span :class="getValue(item.values, col) === '-' ? 'text-gray-300 font-normal' : 'text-primary'">
                                            {{ getValue(item.values, col) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center sticky right-0 bg-white group-hover:bg-bgsoft z-20 shadow-[-4px_0_10px_rgba(0,0,0,0.03)] transition-colors">
                                        <div class="flex justify-center gap-2">
                                            <a :href="`/export/data/${item.id_data}`" class="w-8 h-8 rounded-xl bg-inovasi/10 text-inovasi hover:bg-inovasi hover:text-white transition-all flex items-center justify-center shadow-sm border border-inovasi/20">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" stroke-width="2.5" /></svg>
                                            </a>
                                            <Link :href="`/inputer/data/${item.id_data}/edit`" class="w-8 h-8 rounded-xl bg-secondary/10 text-secondary hover:bg-secondary hover:text-white transition-all flex items-center justify-center shadow-sm border border-secondary/20">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" /></svg>
                                            </Link>
                                            <button @click="openDeleteModal({ id_upload: item.id_data, nama_data: item.nama_data })" class="w-8 h-8 rounded-xl bg-integritas/10 text-integritas hover:bg-integritas hover:text-white transition-all flex items-center justify-center shadow-sm border border-integritas/20">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            <p class="text-[10px] text-textsecondary mt-4 font-bold uppercase tracking-widest">* Gunakan scroll horizontal untuk navigasi tahun.</p>
        </div>

        <div v-else class="overflow-hidden rounded-xl border border-gray-400 bg-white shadow-sm">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-bgsoft text-left text-[10px] font-black text-textsecondary uppercase tracking-[0.2em] border-b border-gray-400">
                        <th class="p-8">Indikator & Klasifikasi</th>
                        <th class="text-center">Urusan / Bidang</th>
                        <th class="text-center">Satuan</th>
                        <th class="text-center">Periode</th>
                        <th class="text-center">Status</th>
                        <th v-if="isAdmin" class="text-center">Operator</th>
                        <th class="text-right p-8">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-bold text-primary">
                    <tr v-for="u in recentUploads" :key="u.id_upload" class="border-b border-gray-100 last:border-0 hover:bg-bgsoft/50 transition-all group">
                        <td class="p-8 max-w-[350px]">
                            <div v-if="u.data">
                                <Link :href="`/dataset/${u.data.id_data}`" class="text-sm text-primary font-black uppercase tracking-tight group-hover:text-secondary transition-colors line-clamp-2">
                                    {{ u.data.nama_data }}
                                </Link>
                                <span class="text-[9px] font-black text-textsecondary/40 uppercase tracking-widest mt-2 block italic">ID: #{{ u.data.id_data }}</span>
                            </div>
                            <span v-else class="text-integritas italic text-[11px] font-black uppercase tracking-widest">Relasi Terputus</span>
                        </td>
                        <td class="text-center py-4">
                            <div class="flex flex-col items-center gap-1.5">
                                <span class="text-[9px] font-black text-primary uppercase px-3 py-1 bg-bgsoft rounded-lg border border-gray-200">
                                    {{ u.data?.urusan?.nama_urusan || 'N/A' }}
                                </span>
                                <span class="text-[9px] font-bold text-textsecondary uppercase opacity-50 tracking-tighter">
                                    {{ u.data?.bidang?.nama_bidang || 'N/A' }}
                                </span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="text-[10px] font-black text-textsecondary uppercase">{{ u.data?.satuan || '-' }}</span>
                        </td>
                        <td class="text-center">
                            <span class="text-primary text-[10px] font-black uppercase tracking-widest bg-secondary/5 px-3 py-1.5 rounded-xl border border-secondary/10">
                                {{ u.periode }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span :class="getStatusClass(u.status)" class="text-[9px] font-black uppercase px-4 py-2 rounded-xl border tracking-widest shadow-sm">
                                {{ u.status }}
                            </span>
                        </td>
                        <td v-if="isAdmin" class="text-center">
                            <span class="text-[10px] font-black text-primary uppercase tracking-tighter bg-bgsoft px-3 py-1.5 rounded-lg border border-gray-200">
                                {{ u.user?.name || 'Unknown' }}
                            </span>
                        </td>
                        <td class="p-8 text-right space-x-2">
                            <a :href="`/export/data/${u.id_upload}`" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-bgsoft text-primary hover:bg-secondary hover:text-white transition-all shadow-sm border border-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" stroke-width="2.5" /></svg>
                            </a>
                            <Link :href="`/inputer/data/${u.data?.id_data}/edit`" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-bgsoft text-primary hover:bg-secondary hover:text-white transition-all shadow-sm border border-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" /></svg>
                            </Link>
                            <button @click="openDeleteModal(u)" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-integritas/10 text-integritas hover:bg-integritas hover:text-white transition-all shadow-sm border border-integritas/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" /></svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <DeleteModal 
            :show="showDeleteModal" 
            :title="'Hapus Riwayat Data?'"
            :description="'Entri data \'' + (dataToDelete?.data?.nama_data || dataToDelete?.nama_data || 'ini') + '\' akan dihapus. Pastikan Anda telah memiliki cadangan data.'"
            @close="showDeleteModal = false"
            @confirm="executeDeleteAction"
        />
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { height: 8px; width: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 20px; border: 2px solid white; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #0284C7; }
</style>