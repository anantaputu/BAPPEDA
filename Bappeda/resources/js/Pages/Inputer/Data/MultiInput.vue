<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
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

// 1. STATE GLOBAL FREKUENSI
// Diambil dari id_frekuensi pertama (biasanya Tahunan), atau null
const globalFrekuensi = ref(props.frekuensi?.length > 0 ? props.frekuensi[0].id_frekuensi : null);

// Watcher: Jika pengguna mengubah globalFrekuensi di atas tabel,
// otomatis semua baris di bawahnya akan berubah
watch(globalFrekuensi, (newVal) => {
    previewData.value.forEach(row => {
        row.id_frekuensi = newVal;
    });
});

// FORMATTER UNTUK HEADER WAKTU (BULAN/HARI/MINGGU)
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
        
        // Atur nilai global frekuensi saat file pertama kali dibaca
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
                id_frekuensi: globalFrekuensi.value // Ambil dari state global
            }
        });

        timeColumns.value = response.data.years || []; 
        extraColumns.value = (response.data.extra_headers || []).filter(h => !h.toUpperCase().includes('NAMA DATA')); 
        
        isPreviewing.value = true;
    } catch (error) {
        console.error("Error Detail:", error);
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
        alert("Mohon lengkapi Tema, Urusan, dan Bidang untuk SETIAP BARIS data, serta pastikan Frekuensi Global telah dipilih.");
        return;
    }

    isLoading.value = true;
    try {
        await axios.post('/inputer/data/store-bulk', {
            dataset: previewData.value,
            years: timeColumns.value 
        });
        alert('Sukses! Data berhasil disimpan.');
        router.visit('/inputer/dashboard');
    } catch (error) {
        isLoading.value = false; 
        if (error.response) {
            const serverMessage = error.response.data.message || error.response.data.error;
            alert(`Gagal menyimpan data: ${serverMessage ? serverMessage : 'Terjadi kesalahan pada server (Error 500)'}`);
        } else {
            alert('Terjadi kesalahan pada aplikasi: ' + error.message);
        }
    }
};
</script>

<template>
    <Head title="Upload Multi Data" />

    <div class="min-h-screen py-10 px-4 md:px-8 mx-auto max-w-[98%]">
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-black text-[#000B58] tracking-tight uppercase">Upload Multi Data</h2>
            <p class="text-gray-500 font-medium mt-2">Upload Excel dan atur metadata (Tema, Urusan, Frekuensi) sekaligus.</p>
        </div>

        <div v-if="!isPreviewing" class="flex flex-col items-center justify-center bg-white border-2 border-dashed border-[#00139E]/30 rounded-[2.5rem] p-16 text-center max-w-3xl mx-auto hover:border-[#00139E] transition-colors">
            <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mb-6 text-[#00139E]">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
            </div>
            <h2 class="text-xl font-black uppercase text-[#000B58] mb-2">Pilih File Excel</h2>
            <div class="relative">
                <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept=".xlsx, .xls, .csv" @change="handleFileUpload" />
                <button :disabled="isLoading" class="bg-[#00139E] text-white px-12 py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#000B58] shadow-xl">
                    {{ isLoading ? 'Membaca...' : 'Cari File' }}
                </button>
            </div>
        </div>

        <div v-if="isPreviewing" class="bg-white rounded-[2rem] shadow-xl border border-gray-200 overflow-hidden flex flex-col max-h-[75vh] animate-fade-in">
            <div class="bg-gray-50 px-8 py-4 border-b border-gray-200 flex flex-wrap justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <h3 class="text-lg font-black text-[#000B58] uppercase">Konfigurasi Data</h3>
                    <span class="bg-blue-100 text-blue-800 px-4 py-1.5 rounded-full text-xs font-black">{{ previewData.length }} Baris</span>
                </div>
                
                <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-200">
                    <label class="text-xs font-black text-gray-500 uppercase tracking-widest">Frekuensi File:</label>
                    <select v-model="globalFrekuensi" class="bg-emerald-50 border-emerald-200 text-emerald-800 rounded-lg text-xs font-bold py-1.5 pl-3 pr-8 focus:ring-emerald-500 focus:border-emerald-500 outline-none cursor-pointer">
                        <option :value="null">Pilih...</option>
                        <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                    </select>
                </div>
            </div>

            <div class="overflow-auto flex-1 custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#000B58] text-white sticky top-0 z-30">
                        <tr>
                            <th class="p-4 text-[10px] uppercase font-black w-[300px] sticky left-0 bg-[#000B58] z-20 shadow-md border-r border-white/10">
                                Indikator
                            </th>
                            <th class="p-3 text-[10px] uppercase font-black w-[150px]">Tema *</th>
                            <th class="p-3 text-[10px] uppercase font-black w-[150px]">Urusan *</th>
                            <th class="p-3 text-[10px] uppercase font-black w-[150px]">Bidang *</th>
                            
                            <th class="p-3 text-[10px] uppercase font-black text-center w-[80px]">Satuan</th>
                            
                            <th v-for="(h, index) in extraColumns" :key="'extra-' + index" class="p-3 text-[10px] uppercase font-black text-center text-amber-300 bg-[#000840] border-l border-white/20">
                                {{ h }}
                            </th>
                            
                            <th v-for="(t, index) in timeColumns" :key="'time-' + index" class="p-3 text-[10px] uppercase font-black text-center bg-blue-900 border-l border-white/20 min-w-[100px]">
                                {{ formatHeader(t) }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="(row, index) in previewData" :key="index" class="hover:bg-blue-50 transition-colors">
                            <td class="p-4 text-xs font-bold text-gray-800 sticky left-0 bg-white shadow-sm border-r border-gray-200">
                                {{ row.nama_indikator }}
                            </td>
                            
                            <td class="p-2"><select v-model="row.id_tema" class="w-full bg-gray-50 border-gray-200 rounded-md text-[10px] font-bold py-2 focus:ring-[#00139E]"><option :value="null">Pilih Tema</option><option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option></select></td>
                            <td class="p-2"><select v-model="row.id_urusan" class="w-full bg-gray-50 border-gray-200 rounded-md text-[10px] font-bold py-2 focus:ring-[#00139E]"><option :value="null">Pilih Urusan</option><option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option></select></td>
                            <td class="p-2"><select v-model="row.id_bidang" class="w-full bg-gray-50 border-gray-200 rounded-md text-[10px] font-bold py-2 focus:ring-[#00139E]"><option :value="null">Pilih Bidang</option><option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option></select></td>
                            
                            <td class="p-3 text-[10px] text-center font-bold text-gray-500 uppercase">{{ row.satuan }}</td>
                            
                            <td v-for="(h, eIndex) in extraColumns" :key="'data-extra-' + eIndex" class="p-3 text-[10px] text-center font-bold text-amber-700 bg-amber-50/30 border-l border-gray-200">
                                {{ row.extra_fields[h] || '-' }}
                            </td>
                            
                            <td v-for="(t, tIndex) in timeColumns" :key="'data-time-' + tIndex" class="p-3 text-xs text-center font-black text-blue-900 bg-gray-50/50 border-l border-gray-200">
                                {{ row.values[t] || '-' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-white border-t border-gray-200 flex justify-end gap-3 z-40">
                <button @click="isPreviewing = false" class="px-6 py-3 text-xs font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Batal</button>
                <button @click="submitFinalData" :disabled="isLoading" class="bg-emerald-600 text-white px-8 py-3 rounded-xl font-black text-xs uppercase hover:bg-emerald-700 shadow-lg transition-colors">
                    {{ isLoading ? 'Menyimpan...' : 'Simpan Data' }}
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { height: 10px; width: 10px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; }
</style>