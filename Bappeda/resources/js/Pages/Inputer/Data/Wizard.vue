<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';
import axios from 'axios';

defineOptions({ layout: AppLayout });

const props = defineProps({
    tema: Array, urusan: Array, bidang: Array, frekuensi: Array,
});

// STATE
const step = ref(1); 
const isLoading = ref(false);
const excelData = reactive({
    headers: {}, 
    preview: []
});

// FORM GLOBAL
const form = useForm({
    nama_indikator: '', deskripsi: '', id_tema: '', id_urusan: '', 
    id_bidang: '', id_frekuensi: '', satuan: '', sumber: '', 
    kata_kunci: '', periode: new Date().getFullYear().toString(),
    
    file_path: '',
    mapping: {}, 
    new_fields: {}
});

// STEP 1: UPLOAD & ANALYZE
const handleFileUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    isLoading.value = true;
    const formData = new FormData();
    formData.append('file', file);

    try {
        const response = await axios.post('/inputer/wizard/analyze', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (response.data.status === 'success') {
            const res = response.data;
            
            // 1. Simpan Data Tampilan
            excelData.headers = res.headers;
            excelData.preview = res.preview;

            // 2. Isi Form langsung dari Logika Backend (Tanpa Loop di JS)
            form.file_path = res.temp_path;
            form.mapping = res.default_mapping;      // <--- Terima jadi
            form.new_fields = res.default_new_fields; // <--- Terima jadi
            form.nama_indikator = res.suggested_name;

            step.value = 2;
        }
    } catch (error) {
        alert('Gagal membaca file: ' + (error.response?.data?.message || error.message));
    } finally {
        isLoading.value = false;
    }
};

// HANDLER DROPDOWN (UI Helper Kecil)
const handleMappingChange = (colKey) => {
    if (form.mapping[colKey] === '__new__') {
        // Jika config hilang (case rare), restore default dari header
        if (!form.new_fields[colKey]) {
            form.new_fields[colKey] = {
                nama_field: excelData.headers[colKey],
                tipe_field: 'text'
            };
        }
    }
};

// STEP 2: SIMPAN FINAL (Tetap Sama)
const submitAll = async () => {
    isLoading.value = true;
    try {
        const response = await axios.post('/inputer/wizard/store-all', form.data());
        if (response.data.status === 'success') {
            router.visit('/inputer/data'); 
        }
    } catch (error) {
        if (error.response?.data?.errors) {
            form.errors = error.response.data.errors; 
            alert('Periksa kembali isian form metadata.');
            window.scrollTo(0,0);
        } else {
            alert('Terjadi kesalahan: ' + (error.response?.data?.message || 'Server Error'));
        }
    } finally {
        isLoading.value = false;
    }
};
</script>
<template>
    <Head title="Input Data Baru" />

    <div class="max-w-7xl mx-auto py-10 px-4">
        
        <div v-if="step === 1" class="flex flex-col items-center justify-center min-h-[60vh] bg-white rounded-[2rem] shadow-xl border border-gray-100 p-12 text-center">
            <div class="max-w-xl w-full">
                <h1 class="text-4xl font-black text-gray-900 mb-2">Upload Data Baru</h1>
                <p class="text-gray-500 mb-10">Unggah file Excel, sistem akan otomatis membaca kolomnya.</p>
                
                <div class="relative group cursor-pointer border-2 border-dashed border-gray-300 rounded-3xl p-16 hover:bg-blue-50/50 hover:border-blue-400 transition-all duration-300">
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 bg-blue-100 text-[#4A6CF7] rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300 shadow-md">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                        </div>
                        <span class="text-xl font-bold text-gray-700 group-hover:text-blue-600 transition">
                            {{ isLoading ? 'Sedang Menganalisa...' : 'Klik untuk Pilih Excel' }}
                        </span>
                        <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".xlsx, .xls, .csv" @change="handleFileUpload" :disabled="isLoading">
                    </div>
                </div>
            </div>
        </div>

        <div v-if="step === 2" class="animate-fade-in space-y-8">
            
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-black text-gray-900">Konfigurasi Data</h2>
                <button @click="step = 1" class="text-gray-500 font-bold hover:text-red-500 text-sm">Batal & Upload Ulang</button>
            </div>

           <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="bg-blue-600 text-white w-8 h-8 rounded-lg flex items-center justify-center font-bold">1</div>
                    <h3 class="text-xl font-bold text-gray-800">Detail Indikator</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Indikator <span class="text-red-500">*</span></label>
                        <input v-model="form.nama_indikator" type="text" class="w-full border-gray-300 rounded-xl px-4 py-3 font-bold text-gray-800 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Jumlah Penduduk Miskin">
                        <p class="text-red-500 text-xs mt-1" v-if="form.errors.nama_indikator">{{ form.errors.nama_indikator }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Deskripsi</label>
                        <textarea v-model="form.deskripsi" rows="2" class="w-full border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Penjelasan singkat indikator..."></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tema <span class="text-red-500">*</span></label>
                        <select v-model="form.id_tema" class="w-full border-gray-300 rounded-xl px-4 py-3 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="" disabled>-- Pilih Tema --</option>
                            <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                        </select>
                        <p class="text-red-500 text-xs mt-1" v-if="form.errors.id_tema">{{ form.errors.id_tema }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Urusan <span class="text-red-500">*</span></label>
                        <select v-model="form.id_urusan" class="w-full border-gray-300 rounded-xl px-4 py-3 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="" disabled>-- Pilih Urusan --</option>
                            <option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bidang <span class="text-red-500">*</span></label>
                        <select v-model="form.id_bidang" class="w-full border-gray-300 rounded-xl px-4 py-3 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="" disabled>-- Pilih Bidang --</option>
                            <option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Frekuensi <span class="text-red-500">*</span></label>
                        <select v-model="form.id_frekuensi" class="w-full border-gray-300 rounded-xl px-4 py-3 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="" disabled>-- Pilih Frekuensi --</option>
                            <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Satuan <span class="text-red-500">*</span></label>
                            <input v-model="form.satuan" type="text" class="w-full border-gray-300 rounded-xl px-4 py-3" placeholder="Ex: Jiwa">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Periode (Tahun) <span class="text-red-500">*</span></label>
                            <input v-model="form.periode" type="number" class="w-full border-gray-300 rounded-xl px-4 py-3 font-bold text-blue-600">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sumber Data</label>
                            <input v-model="form.sumber" type="text" class="w-full border-gray-300 rounded-xl px-4 py-3" placeholder="Ex: BPS, Dinas Kesehatan">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kata Kunci</label>
                            <input v-model="form.kata_kunci" type="text" class="w-full border-gray-300 rounded-xl px-4 py-3" placeholder="Tag pencarian...">
                        </div>
                    </div>

                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-[600px]">
                <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">2. Mapping Kolom Excel</h3>
                    <p class="text-xs text-gray-500">Sesuaikan nama field dan tipe data di kotak biru.</p>
                </div>

                <div class="overflow-auto flex-1">
                    <table class="w-full border-collapse min-w-max">
                        <thead class="bg-gray-50 sticky top-0 z-10 shadow-sm">
                            
                            <tr>
                                <th class="p-3 border-b border-r w-10 bg-gray-100 text-center text-xs text-gray-400">#</th>
                                
                                <th v-for="(colName, colKey) in excelData.headers" :key="colKey" 
                                    class="p-2 border-b border-r min-w-[250px] align-top transition-colors"
                                    :class="form.mapping[colKey] === '__new__' ? 'bg-blue-50' : 'bg-gray-50'">
                                    
                                    <div class="space-y-2">
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                            Kolom Excel: {{ colName }}
                                        </div>

                                        <select v-model="form.mapping[colKey]" @change="handleMappingChange(colKey)"
                                            class="w-full text-xs font-bold border rounded-lg py-2 shadow-sm"
                                            :class="form.mapping[colKey] === '__new__' ? 'border-blue-400 text-blue-800' : 'border-gray-300 text-gray-400'">
                                            <option value="__new__">+ Buat Field Baru</option>
                                            <option :value="null">-- Abaikan Kolom Ini --</option>
                                        </select>

                                        <div v-if="form.mapping[colKey] === '__new__'" class="bg-blue-100 p-3 rounded-lg border border-blue-200 space-y-2">
                                            <div>
                                                <label class="block text-[10px] font-bold text-blue-600 mb-1">Nama Field</label>
                                                <input type="text" v-model="form.new_fields[colKey].nama_field" 
                                                    class="w-full text-xs border-blue-300 rounded px-2 py-1 font-bold text-gray-700">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-blue-600 mb-1">Tipe Data</label>
                                                <select v-model="form.new_fields[colKey].tipe_field" 
                                                    class="w-full text-xs border-blue-300 rounded px-2 py-1">
                                                    <option value="text">Text (Bebas)</option>
                                                    <option value="number">Angka / Nominal</option>
                                                    <option value="date">Tanggal</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                            </tr>

                            <tr class="bg-gray-100 text-gray-600">
                                <th class="p-2 border-b border-r text-center text-xs font-mono">1</th>
                                <th v-for="(colName, colKey) in excelData.headers" :key="'h-'+colKey" class="p-3 border-b border-r text-left text-xs font-extrabold uppercase text-gray-700 font-mono">
                                    {{ colName }}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white">
                            <tr v-for="(row, index) in excelData.preview" :key="index" class="hover:bg-blue-50/30">
                                <td class="p-2 border-b border-r text-center text-xs font-mono text-gray-400 bg-gray-50">{{ index + 2 }}</td>
                                <td v-for="(colName, colKey) in excelData.headers" :key="'c-'+colKey" 
                                    class="p-3 border-b border-r text-xs text-gray-600 font-mono whitespace-nowrap overflow-hidden max-w-xs truncate"
                                    :class="form.mapping[colKey] === '__new__' ? 'bg-blue-50/20 font-medium text-gray-900' : ''">
                                    {{ row[colKey] }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 p-6 flex justify-end gap-4 z-50 shadow-[0_-10px_40px_rgba(0,0,0,0.1)]">
                <div class="max-w-7xl mx-auto w-full flex justify-between items-center px-4">
                    <span class="text-sm text-gray-500 font-medium">Pastikan semua kolom berwarna biru sudah dikonfigurasi.</span>
                    <button @click="submitAll" :disabled="isLoading" 
                        class="bg-[#4A6CF7] text-white px-10 py-4 rounded-xl font-black text-lg hover:bg-blue-700 transition shadow-xl transform hover:-translate-y-1 flex items-center gap-3">
                        <svg v-if="isLoading" class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        {{ isLoading ? 'MENYIMPAN DATA...' : 'SIMPAN SEMUA DATA' }}
                    </button>
                </div>
            </div>
            
            <div class="h-24"></div>

        </div>

    </div>
</template>

<style scoped>
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* Scrollbar Custom */
.overflow-auto::-webkit-scrollbar { width: 8px; height: 8px; }
.overflow-auto::-webkit-scrollbar-track { background: #f1f1f1; }
.overflow-auto::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
.overflow-auto::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
</style>