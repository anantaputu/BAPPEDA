<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AlertModal from '@/Components/Layout/AlertModal.vue';
import { Head, router, Link, usePage } from '@inertiajs/vue3';
import { computed, ref, watch, onMounted, onUnmounted, nextTick } from 'vue';
import axios from 'axios';

defineOptions({ layout: AppLayout });

const props = defineProps({
    tema: Array, 
    urusan: Array, 
    bidang: Array, 
    frekuensi: Array, 
});

const page = usePage();
const dashboardPath = computed(() => {
    const role = page.props.auth?.user?.role;
    const roleName = (typeof role === 'string' ? role : role?.nama_role || '').toLowerCase();
    return roleName.includes('admin') ? '/admin/dashboard' : '/inputer/dashboard';
});

const fileExcel = ref(null);
const isLoading = ref(false);
const isPreviewing = ref(false);

const previewData = ref([]);
const timeColumns = ref([]);  
const extraColumns = ref([]); 
const showAlertModal = ref(false);
const alertTitle = ref('Informasi');
const alertMessage = ref('');
const alertType = ref('info');
const afterAlertAction = ref(null);

const openAlert = (title, message, type = 'info', onClose = null) => {
    alertTitle.value = title;
    alertMessage.value = message;
    alertType.value = type;
    afterAlertAction.value = onClose;
    showAlertModal.value = true;
};

const closeAlert = () => {
    showAlertModal.value = false;
    const action = afterAlertAction.value;
    afterAlertAction.value = null;
    if (typeof action === 'function') action();
};

// Default frekuensi
const globalFrekuensi = ref(props.frekuensi?.length > 0 ? props.frekuensi[0].id_frekuensi : null);

// [BARU] Default Tahun Terbit untuk bulk set
const globalTahunTerbit = ref(new Date().getFullYear());

watch(globalFrekuensi, (newVal) => {
    previewData.value.forEach(row => {
        row.id_frekuensi = newVal;
    });
});

// [BARU] Watcher untuk mengubah semua tahun terbit sekaligus jika user menggunakan dropdown bulk
watch(globalTahunTerbit, (newVal) => {
    previewData.value.forEach(row => {
        row.tahun_terbit = newVal;
    });
});

const formatHeader = (label) => {
    if (label === null || label === undefined) return '-';
    try {
        let str = String(label).trim();
        if (/^\d{4}-\d{2}$/.test(str)) {
            const [y, m] = str.split('-');
            return new Date(y, m - 1).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
        }
        return str.replace(/\b\w/g, char => char.toUpperCase());
    } catch (e) {
        return String(label);
    }
};

const handleFileUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;
    
    fileExcel.value = file;
    isLoading.value = true;
 
    const formData = new FormData();
    formData.append('file', file);

    try {
        const response = await axios.post('/inputer/data/preview-excel', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        globalFrekuensi.value = props.frekuensi?.length > 0 ? props.frekuensi[0].id_frekuensi : null;

        previewData.value = response.data.rows.map(row => {
            let indikatorAsli = row.nama_data; 
            let namaDataBaru = indikatorAsli;
            const keyNamaData = Object.keys(row.extra_fields || {}).find(k => k.toUpperCase().includes('NAMA DATA'));
            if (keyNamaData && row.extra_fields[keyNamaData]) {
                namaDataBaru = row.extra_fields[keyNamaData]; 
            }

            // [BARU] Deteksi Tahun Terbit dari extra_fields (jika kolom itu ada di Excel)
            let parsedTahunTerbit = new Date().getFullYear(); // Default ke tahun ini
            const keyTahunTerbit = Object.keys(row.extra_fields || {}).find(k => k.toUpperCase().includes('TAHUN TERBIT'));
            if (keyTahunTerbit && row.extra_fields[keyTahunTerbit]) {
                parsedTahunTerbit = row.extra_fields[keyTahunTerbit];
                // Hapus dari extra_fields agar tidak masuk ke kolom atribut tambahan di database
                delete row.extra_fields[keyTahunTerbit]; 
            }

            return {
                ...row,
                nama_data: namaDataBaru, 
                kode_indikator: indikatorAsli, 
                id_frekuensi: globalFrekuensi.value,
                id_tema: '',
                id_urusan: '',
                id_bidang: '',
                // [BARU] Masukkan ke objek baris
                tahun_terbit: parsedTahunTerbit 
            }
        });

        timeColumns.value = response.data.years || []; 
        extraColumns.value = (response.data.extra_headers || []).filter(h => 
            !h.toUpperCase().includes('NAMA DATA') && !h.toUpperCase().includes('TAHUN TERBIT')
        ); 
        isPreviewing.value = true;
    } catch (error) {
        let msg = 'Gagal membaca file Excel.';
        if (error.response?.data?.error) msg = error.response.data.error;
        openAlert('Gagal Membaca File', msg, 'error');
    } finally {
        isLoading.value = false;
    }
};

const submitFinalData = async () => {
    const isAllValid = previewData.value.every(row => row.id_tema && row.id_urusan && row.id_bidang && row.id_frekuensi);
    if (!isAllValid) {
        openAlert('Validasi Gagal', 'Mohon lengkapi Tema, Urusan, dan Bidang untuk setiap baris data.', 'warning');
        return;
    }

    isLoading.value = true;
    try {
        await axios.post('/inputer/data/store-bulk', {
            dataset: previewData.value, // Data ini sekarang sudah memuat row.tahun_terbit
            years: timeColumns.value 
        });
        openAlert(
            'Berhasil',
            'Data bulk berhasil disimpan.',
            'success',
            () => router.visit(dashboardPath.value)
        );
    } catch (error) {
        isLoading.value = false; 
        const message = error.response?.data?.error || 'Terjadi kesalahan saat menyimpan data.';
        openAlert('Gagal Menyimpan', message, 'error');
    }
};

// ==========================================
// SCROLL SLIDER LOGIC
// ==========================================
const tableContainer = ref(null);
const scrollLeftVal = ref(0);
const scrollMaxVal = ref(0);

const updateScrollMetrics = () => {
    if (tableContainer.value) {
        const el = tableContainer.value;
        scrollLeftVal.value = el.scrollLeft;
        scrollMaxVal.value = el.scrollWidth - el.clientWidth;
    }
};

const handleTableScroll = () => {
    if (tableContainer.value) {
        scrollLeftVal.value = tableContainer.value.scrollLeft;
    }
};

const handleSliderInput = (e) => {
    if (tableContainer.value) {
        tableContainer.value.scrollLeft = parseFloat(e.target.value);
    }
};

const scrollTable = (direction) => {
    if (tableContainer.value) {
        const offset = direction === 'left' ? -200 : 200;
        tableContainer.value.scrollBy({ left: offset, behavior: 'smooth' });
    }
};

watch(isPreviewing, (newVal) => {
    if (newVal) {
        nextTick(() => {
            updateScrollMetrics();
        });
    }
});

onMounted(() => {
    window.addEventListener('resize', updateScrollMetrics);
});

onUnmounted(() => {
    window.removeEventListener('resize', updateScrollMetrics);
});
</script>

<template>
    <Head title="Upload Multi Data" />

    <div class="max-w-[96%] mx-auto py-10">
        <div class="mb-10 text-center">
            <h1 class="text-4xl font-black text-gray-900 tracking-tight uppercase">
                Upload <span class="text-[#00139E]">Multi Data</span>
            </h1>
            <p class="text-gray-400 font-bold mt-2 italic tracking-wide">Kelola data massal secara efisien.</p>
        </div>

        <div v-if="!isPreviewing" class="max-w-3xl mx-auto">
            <div class="bg-white rounded-2xl border-2 border-dashed border-gray-400 p-16 flex flex-col items-center text-center shadow-sm">
                <div class="w-20 h-20 bg-primary/10 rounded-xl flex items-center justify-center mb-6 text-primary">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                </div>
                <h3 class="text-xl font-black text-primary mb-2 uppercase tracking-tight">Impor Berkas Excel</h3>
                <p class="text-textsecondary text-xs font-bold mb-8 uppercase tracking-widest leading-loose">Pilih berkas untuk memulai pratinjau data.</p>
                
                <div class="relative">
                    <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept=".xlsx, .xls" @change="handleFileUpload" />
                    <button type="button" :disabled="isLoading" class="bg-primary text-white px-10 py-4 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] shadow-sm hover:bg-secondary transition-all">
                        {{ isLoading ? 'Memproses Berkas...' : 'Cari Berkas Excel' }}
                    </button>
                </div>
            </div>
        </div>

        <div v-if="isPreviewing" class="animate-fade-in space-y-6">
            
            <div class="bg-white p-6 rounded-2xl border border-gray-400 shadow-sm flex flex-wrap justify-between items-center gap-4">
                <div class="flex items-center gap-4 ml-2">
                    <span class="bg-primary text-white px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-widest">
                        {{ previewData.length }} Baris Terdeteksi
                    </span>
                </div>
                
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center gap-4 px-4 py-2 bg-slate-50 rounded-xl border border-gray-400">
                        <label class="text-[10px] font-black text-textsecondary uppercase tracking-widest">Set Tahun Terbit Semua:</label>
                        <input v-model="globalTahunTerbit" type="number" class="bg-transparent border-none text-primary text-[11px] font-black focus:ring-0 w-20 p-0 text-center">
                    </div>

                    <div class="flex items-center gap-4 px-4 py-2 bg-slate-50 rounded-xl border border-gray-400">
                        <label class="text-[10px] font-black text-textsecondary uppercase tracking-widest">Set Frekuensi Semua:</label>
                        <select v-model="globalFrekuensi" class="bg-transparent border-none text-primary text-[11px] font-black focus:ring-0 cursor-pointer uppercase">
                            <option :value="null">Pilih...</option>
                            <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="border border-gray-400 rounded-2xl overflow-hidden shadow-sm bg-white">
                <div ref="tableContainer" @scroll="handleTableScroll" class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="bg-slate-50 border-b border-gray-400">
                                <th class="p-4 border-r border-gray-400 text-xs font-bold text-textsecondary uppercase tracking-wider">Indikator</th>
                                <th class="p-4 border-r border-gray-400 text-xs font-bold text-textsecondary uppercase tracking-wider w-[160px]">Tema</th>
                                <th class="p-4 border-r border-gray-400 text-xs font-bold text-textsecondary uppercase tracking-wider w-[160px]">Urusan</th>
                                <th class="p-4 border-r border-gray-400 text-xs font-bold text-textsecondary uppercase tracking-wider w-[160px]">Bidang</th>
                                <th class="p-4 border-r border-gray-400 text-xs font-bold text-textsecondary uppercase tracking-wider text-center">Satuan</th>
                                <th class="p-4 border-r border-gray-400 text-xs font-bold text-textsecondary uppercase tracking-wider text-center">Thn Terbit</th>
                                <th v-for="t in timeColumns" :key="t" class="p-4 border-r border-gray-400 min-w-[120px] text-center text-xs font-bold text-primary uppercase tracking-wider">
                                    {{ formatHeader(t) }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-400">
                            <tr v-for="(row, index) in previewData" :key="index" class="hover:bg-slate-50/50 transition-colors group">
                                <td class="p-4 border-r border-gray-400 group-hover:bg-slate-50/30">
                                    <span class="text-xs font-bold text-primary uppercase block leading-tight">{{ row.nama_data }}</span>
                                    <span class="text-[9px] text-textsecondary font-semibold uppercase mt-1 block tracking-wider">Ref: {{ row.kode_indikator }}</span>
                                </td>
                                
                                <td class="p-2 border-r border-gray-400 group-hover:bg-slate-50/30">
                                    <select v-model="row.id_tema" class="w-full bg-slate-50/80 border border-gray-400 rounded-xl text-[10px] font-bold py-2 focus:ring-secondary focus:border-secondary uppercase tracking-tighter">
                                        <option value="">Pilih...</option>
                                        <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                                    </select>
                                </td>
                                <td class="p-2 border-r border-gray-400 group-hover:bg-slate-50/30">
                                    <select v-model="row.id_urusan" class="w-full bg-slate-50/80 border border-gray-400 rounded-xl text-[10px] font-bold py-2 focus:ring-secondary focus:border-secondary uppercase tracking-tighter">
                                        <option value="">Pilih...</option>
                                        <option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                                    </select>
                                </td>
                                <td class="p-2 border-r border-gray-400 group-hover:bg-slate-50/30">
                                    <select v-model="row.id_bidang" class="w-full bg-slate-50/80 border border-gray-400 rounded-xl text-[10px] font-bold py-2 focus:ring-secondary focus:border-secondary uppercase tracking-tighter">
                                        <option value="">Pilih...</option>
                                        <option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                                    </select>
                                </td>
                                
                                <td class="p-4 text-xs text-center font-semibold text-textsecondary uppercase border-r border-gray-400 group-hover:bg-slate-50/30">
                                    {{ row.satuan }}
                                </td>
                                
                                <td class="p-2 border-r border-gray-400 group-hover:bg-slate-50/30">
                                    <input v-model="row.tahun_terbit" type="number" class="w-full bg-white border border-gray-400 rounded-xl text-[10px] font-bold py-2 focus:ring-secondary focus:border-secondary text-center text-primary">
                                </td>

                                <td v-for="t in timeColumns" :key="t" class="p-4 text-sm text-center font-bold text-primary border-r border-gray-400 group-hover:bg-slate-50/30">
                                    <span class="group-hover:text-secondary transition-colors">
                                        {{ row.values[t] !== undefined && row.values[t] !== null && row.values[t] !== '' ? row.values[t] : '-' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Custom Scroll Slider Indicator -->
                <div v-if="scrollMaxVal > 0" class="flex items-center justify-center gap-3 py-3 border-t border-gray-400 bg-slate-50/50">
                    <span class="text-[9px] font-black text-textsecondary uppercase tracking-widest">Scroll Horizontal</span>
                    <div class="flex items-center gap-2">
                        <button @click="scrollTable('left')" class="p-1 rounded-lg hover:bg-slate-200 text-textsecondary hover:text-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <input type="range" min="0" :max="scrollMaxVal" :value="scrollLeftVal" @input="handleSliderInput" 
                            class="w-48 accent-primary cursor-pointer h-1 bg-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0">
                        <button @click="scrollTable('right')" class="p-1 rounded-lg hover:bg-slate-200 text-textsecondary hover:text-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-400 shadow-sm">
                <button type="button" @click.prevent="isPreviewing = false" class="px-8 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-red-500 transition-colors">
                    Batal & Reset
                </button>
                <button type="button" @click.prevent="submitFinalData" :disabled="isLoading" 
                    class="bg-emerald-600 text-white px-12 py-4 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] shadow-lg hover:bg-emerald-700 transition-all flex items-center gap-3">
                    <svg v-if="isLoading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Simpan Permanen
                </button>
            </div>
        </div>
    </div>

    <AlertModal
        :show="showAlertModal"
        :title="alertTitle"
        :description="alertMessage"
        :type="alertType"
        @close="closeAlert"
    />
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { height: 10px; width: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #EEF2F5; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; border: 2px solid #EEF2F5; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #1F3A63; }

.animate-fade-in {
    animation: fadeIn 0.4s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
