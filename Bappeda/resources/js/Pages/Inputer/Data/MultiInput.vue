<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import axios from 'axios';

defineOptions({ layout: AppLayout });

const props = defineProps({
    tema: Array, 
    urusan: Array, 
    bidang: Array, 
    frekuensi: Array, 
});

const fileExcel = ref(null);
const isLoading = ref(false);
const isPreviewing = ref(false);

const previewData = ref([]);
const timeColumns = ref([]);  
const extraColumns = ref([]); 

const globalFrekuensi = ref(props.frekuensi?.length > 0 ? props.frekuensi[0].id_frekuensi : null);

watch(globalFrekuensi, (newVal) => {
    previewData.value.forEach(row => {
        row.id_frekuensi = newVal;
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
            let indikatorAsli = row.nama_indikator; 
            let namaDataBaru = indikatorAsli;
            const keyNamaData = Object.keys(row.extra_fields || {}).find(k => k.toUpperCase().includes('NAMA DATA'));
            if (keyNamaData && row.extra_fields[keyNamaData]) {
                namaDataBaru = row.extra_fields[keyNamaData]; 
            }

            return {
                ...row,
                nama_indikator: namaDataBaru, 
                kode_indikator: indikatorAsli, 
                id_frekuensi: globalFrekuensi.value,
                id_tema: '',
                id_urusan: '',
                id_bidang: ''
            }
        });

        timeColumns.value = response.data.years || []; 
        extraColumns.value = (response.data.extra_headers || []).filter(h => !h.toUpperCase().includes('NAMA DATA')); 
        isPreviewing.value = true;
    } catch (error) {
        let msg = 'Gagal membaca file Excel.';
        if (error.response?.data?.error) msg = error.response.data.error;
        alert(msg);
    } finally {
        isLoading.value = false;
    }
};

const submitFinalData = async () => {
    const isAllValid = previewData.value.every(row => row.id_tema && row.id_urusan && row.id_bidang && row.id_frekuensi);
    if (!isAllValid) {
        alert("Mohon lengkapi Tema, Urusan, dan Bidang untuk setiap baris data.");
        return;
    }

    isLoading.value = true;
    try {
        await axios.post('/inputer/data/store-bulk', {
            dataset: previewData.value,
            years: timeColumns.value 
        });
        router.visit('/inputer/dashboard');
    } catch (error) {
        isLoading.value = false; 
        alert('Terjadi kesalahan saat menyimpan data.');
    }
};
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
            <div class="bg-white rounded-[2.5rem] border-2 border-dashed border-gray-400 p-16 flex flex-col items-center text-center shadow-2xl shadow-gray-100">
                <div class="w-20 h-20 bg-blue-50 rounded-[1.5rem] flex items-center justify-center mb-6 text-[#00139E]">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2 uppercase tracking-tight">Impor Berkas Excel</h3>
                <p class="text-gray-400 text-xs font-bold mb-8 uppercase tracking-widest leading-loose">Pilih berkas untuk memulai pratinjau data.</p>
                
                <div class="relative">
                    <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept=".xlsx, .xls" @change="handleFileUpload" />
                    <button :disabled="isLoading" class="bg-[#00139E] text-white px-10 py-4 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] shadow-lg active:scale-95 transition-all">
                        {{ isLoading ? 'Memproses Berkas...' : 'Cari Berkas Excel' }}
                    </button>
                </div>
            </div>
        </div>

        <div v-if="isPreviewing" class="animate-fade-in space-y-6">
            
            <div class="bg-white p-6 rounded-[2rem] border border-gray-400 shadow-xl flex flex-wrap justify-between items-center gap-4">
                <div class="flex items-center gap-4 ml-2">
                    <span class="bg-[#00139E] text-white px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-widest">
                        {{ previewData.length }} Baris Terdeteksi
                    </span>
                </div>
                
                <div class="flex items-center gap-4 px-4 py-2 bg-gray-50 rounded-2xl border border-gray-200">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Set Frekuensi Untuk Semua:</label>
                    <select v-model="globalFrekuensi" class="bg-transparent border-none text-[#00139E] text-[11px] font-black focus:ring-0 cursor-pointer uppercase">
                        <option :value="null">Pilih...</option>
                        <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                    </select>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] border border-gray-400 shadow-2xl overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-900 text-white border-b border-gray-800">
                            <th class="p-5 text-[9px] uppercase font-black tracking-widest whitespace-nowrap">Indikator</th>
                            <th class="p-5 text-[9px] uppercase font-black tracking-widest w-[180px]">Tema</th>
                            <th class="p-5 text-[9px] uppercase font-black tracking-widest w-[180px]">Urusan</th>
                            <th class="p-5 text-[9px] uppercase font-black tracking-widest w-[180px]">Bidang</th>
                            <th class="p-5 text-[9px] uppercase font-black tracking-widest text-center">Satuan</th>
                            
                            <th v-for="t in timeColumns" :key="t" class="p-5 text-[9px] uppercase font-black tracking-widest text-center bg-blue-900 min-w-[100px]">
                                {{ formatHeader(t) }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="(row, index) in previewData" :key="index" class="hover:bg-gray-50 transition-colors">
                            <td class="p-5 border-r border-gray-100">
                                <span class="text-[11px] font-black text-gray-900 uppercase block">{{ row.nama_indikator }}</span>
                                <span class="text-[9px] text-gray-400 font-bold uppercase mt-1 block">Ref: {{ row.kode_indikator }}</span>
                            </td>
                            
                            <td class="p-2 border-r border-gray-100">
                                <select v-model="row.id_tema" class="w-full bg-gray-50 border-gray-200 rounded-lg text-[10px] font-bold py-2 focus:ring-[#00139E] uppercase tracking-tighter">
                                    <option value="">Pilih...</option>
                                    <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                                </select>
                            </td>
                            <td class="p-2 border-r border-gray-100">
                                <select v-model="row.id_urusan" class="w-full bg-gray-50 border-gray-200 rounded-lg text-[10px] font-bold py-2 focus:ring-[#00139E] uppercase tracking-tighter">
                                    <option value="">Pilih...</option>
                                    <option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                                </select>
                            </td>
                            <td class="p-2 border-r border-gray-100">
                                <select v-model="row.id_bidang" class="w-full bg-gray-50 border-gray-200 rounded-lg text-[10px] font-bold py-2 focus:ring-[#00139E] uppercase tracking-tighter">
                                    <option value="">Pilih...</option>
                                    <option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                                </select>
                            </td>
                            
                            <td class="p-5 text-[10px] text-center font-bold text-gray-500 uppercase border-r border-gray-100">{{ row.satuan }}</td>
                            
                            <td v-for="t in timeColumns" :key="t" class="p-5 text-xs text-center font-black text-[#00139E] bg-blue-50/30">
                                {{ row.values[t] || '-' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between items-center bg-white p-6 rounded-[2rem] border border-gray-400 shadow-xl">
                <button @click="isPreviewing = false" class="px-8 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-red-500 transition-colors">
                    Batal & Reset
                </button>
                <button @click="submitFinalData" :disabled="isLoading" 
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
</template>

<style scoped>
/* Scrollbar horizontal tetap manis tapi tidak mengganggu layout */
.custom-scrollbar::-webkit-scrollbar { height: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

.animate-fade-in {
    animation: fadeIn 0.4s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>