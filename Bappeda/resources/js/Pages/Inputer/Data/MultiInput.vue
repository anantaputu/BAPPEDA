<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
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

// State untuk menampung data dari Excel yang siap diedit di layar
const previewData = ref([]);
const yearColumns = ref([]); // Menampung header tahun (contoh: ['2024', '2025', '2026'])

// 1. FUNGSI UPLOAD & PREVIEW (BACA EXCEL)
const handleFileUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;
    
    fileExcel.value = file;
    isLoading.value = true;

    const formData = new FormData();
    formData.append('file', file);

    try {
        // Kirim ke backend HANYA untuk dibaca, belum disimpan ke DB
        const response = await axios.post('/inputer/data/preview-excel', formData);
        
        previewData.value = response.data.rows; // Data per baris
        yearColumns.value = response.data.years; // Header tahun
        isPreviewing.value = true;
    } catch (error) {
        alert('Gagal membaca file Excel. Pastikan format tabel benar.');
        console.error(error);
    } finally {
        isLoading.value = false;
    }
};

// 2. FUNGSI SIMPAN FINAL KE DATABASE
const submitFinalData = async () => {
    // Validasi: Pastikan semua baris sudah dipilih Tema, Urusan, dan Bidangnya
    const isAllValid = previewData.value.every(row => row.id_tema && row.id_urusan && row.id_bidang);
    
    if (!isAllValid) {
        alert("Mohon lengkapi pilihan Tema, Urusan, dan Bidang untuk SETIAP BARIS data sebelum menyimpan.");
        return;
    }

    isLoading.value = true;

    try {
        // Kirim array data yang sudah diedit ke backend
        await axios.post('/inputer/data/store-bulk', {
            dataset: previewData.value,
            years: yearColumns.value
        });

        alert('Semua dataset berhasil disimpan ke database!');
        router.visit('/inputer/dashboard'); // Redirect ke dashboard
    } catch (error) {
        alert('Terjadi kesalahan saat menyimpan data.');
        console.error(error);
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <Head title="Upload Multi Data" />

    <div class="min-h-screen py-10 px-6 mx-auto max-w-[95%]">
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-black text-[#000B58] tracking-tight uppercase">Upload & Konfigurasi Multi Data</h2>
            <p class="text-gray-500 font-medium mt-2">Unggah file Excel, lalu sesuaikan metadata untuk setiap baris indikator.</p>
        </div>

        <div v-if="!isPreviewing" class="flex flex-col items-center justify-center bg-white border-2 border-dashed border-gray-300 rounded-[2rem] p-16 text-center max-w-3xl mx-auto">
            <svg class="w-16 h-16 text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
            <h2 class="text-xl font-black uppercase text-gray-700 mb-2">Pilih File Excel Anda</h2>
            <p class="text-sm text-gray-400 mb-6">Pastikan file memiliki kolom Nama Indikator dan Tahun.</p>
            
            <div class="relative">
                <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept=".xlsx, .xls, .csv" @change="handleFileUpload" />
                <button :disabled="isLoading" class="bg-[#00139E] text-white px-10 py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-900 transition-all shadow-xl">
                    {{ isLoading ? 'Membaca File...' : 'Cari File (.xlsx)' }}
                </button>
            </div>
        </div>

        <div v-if="isPreviewing" class="bg-white rounded-[2rem] shadow-xl border border-gray-200 overflow-hidden animate-fade-in">
            <div class="bg-gray-50 px-8 py-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-black text-[#000B58] uppercase">Preview Data & Konfigurasi</h3>
                <span class="bg-emerald-100 text-emerald-700 px-4 py-1.5 rounded-full text-xs font-black uppercase">{{ previewData.length }} Baris Ditemukan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left table-auto">
                    <thead>
                        <tr class="bg-[#000B58] text-white">
                            <th class="p-4 text-[10px] uppercase font-black tracking-widest whitespace-nowrap">Indikator</th>
                            <th class="p-4 text-[10px] uppercase font-black tracking-widest w-48">Tema *</th>
                            <th class="p-4 text-[10px] uppercase font-black tracking-widest w-48">Urusan *</th>
                            <th class="p-4 text-[10px] uppercase font-black tracking-widest w-48">Bidang *</th>
                            <th class="p-4 text-[10px] uppercase font-black tracking-widest text-center w-24">Satuan</th>
                            <th v-for="year in yearColumns" :key="year" class="p-4 text-[10px] uppercase font-black tracking-widest text-center bg-blue-900">
                                {{ year }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="(row, index) in previewData" :key="index" class="hover:bg-blue-50/30 transition-colors">
                            <td class="p-4 text-sm font-bold text-gray-800">{{ row.nama_indikator }}</td>
                            
                            <td class="p-2">
                                <select v-model="row.id_tema" class="w-full bg-gray-50 border-gray-200 rounded-lg text-xs font-bold focus:ring-[#00139E]">
                                    <option :value="null" disabled>Pilih Tema</option>
                                    <option v-for="t in tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                                </select>
                            </td>
                            <td class="p-2">
                                <select v-model="row.id_urusan" class="w-full bg-gray-50 border-gray-200 rounded-lg text-xs font-bold focus:ring-[#00139E]">
                                    <option :value="null" disabled>Pilih Urusan</option>
                                    <option v-for="u in urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                                </select>
                            </td>
                            <td class="p-2">
                                <select v-model="row.id_bidang" class="w-full bg-gray-50 border-gray-200 rounded-lg text-xs font-bold focus:ring-[#00139E]">
                                    <option :value="null" disabled>Pilih Bidang</option>
                                    <option v-for="b in bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                                </select>
                            </td>

                            <td class="p-4 text-xs font-medium text-center text-gray-500">{{ row.satuan }}</td>
                            
                            <td v-for="year in yearColumns" :key="year" class="p-4 text-sm font-black text-center text-[#00139E]">
                                {{ row.values[year] !== undefined ? row.values[year] : '-' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-8 bg-gray-50 border-t border-gray-200 flex justify-end gap-4">
                <button @click="isPreviewing = false" class="px-8 py-4 text-xs font-black text-gray-500 uppercase hover:bg-gray-200 rounded-xl transition-all">Batal</button>
                <button @click="submitFinalData" :disabled="isLoading" class="bg-emerald-600 text-white px-10 py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-xl disabled:opacity-50 flex items-center gap-2">
                    <svg v-if="isLoading" class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    {{ isLoading ? 'Menyimpan Semua Data...' : 'Simpan Semua Indikator' }}
                </button>
            </div>
        </div>

    </div>
</template>