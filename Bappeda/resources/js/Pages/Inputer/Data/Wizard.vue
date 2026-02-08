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
const excelData = reactive({ headers: {}, preview: [] });

// FORM GLOBAL
const form = useForm({
    nama_indikator: '', deskripsi: '', id_tema: '', id_urusan: '', 
    id_bidang: '', id_frekuensi: '', satuan: '', sumber: '', 
    kata_kunci: '', periode: new Date().getFullYear().toString(),
    file_path: '', mapping: {}, new_fields: {}
});

// HANDLERS
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
            excelData.headers = res.headers;
            excelData.preview = res.preview;
            form.file_path = res.temp_path;
            
            // Auto-fill form jika mapping tersedia
            form.mapping = res.default_mapping;
            form.new_fields = res.default_new_fields;
            
            // Jika nama indikator belum diisi user, pakai saran dari nama file
            if (!form.nama_indikator) {
                form.nama_indikator = res.suggested_name;
            }
            
            step.value = 2; // Pindah ke mode mapping (Upload box hilang, Tabel muncul)
        }
    } catch (error) {
        alert('Gagal membaca file: ' + (error.response?.data?.message || error.message));
    } finally {
        isLoading.value = false;
    }
};

const handleMappingChange = (colKey) => {
    if (form.mapping[colKey] === '__new__' && !form.new_fields[colKey]) {
        form.new_fields[colKey] = {
            nama_field: excelData.headers[colKey],
            tipe_field: 'text'
        };
    }
};

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
            alert('Mohon periksa kembali isian form yang berwarna merah.');
            document.getElementById('form-metadata')?.scrollIntoView({ behavior: 'smooth' });
        } else {
            alert('Terjadi kesalahan: ' + (error.response?.data?.message || 'Server Error'));
        }
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <Head title="Wizard Input Data" />

    <div class="min-h-screen bg-slate-50/50 py-10 px-4 md:px-8">
        <div class="max-w-5xl mx-auto space-y-8">

            <div id="form-metadata" class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden animate-fade-in">
                <div class="bg-slate-50 px-8 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-blue-600 rounded-full"></span>
                        1. Metadata Indikator
                    </h3>
                    <span v-if="step === 1" class="text-xs text-slate-400 font-medium">Silakan isi detail data sebelum atau sesudah upload.</span>
                </div>
                
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="form-label">Nama Indikator <span class="text-red-500">*</span></label>
                        <input v-model="form.nama_indikator" type="text" class="form-input font-semibold text-slate-800" placeholder="Contoh: Jumlah Penduduk Miskin">
                        <p class="form-error" v-if="form.errors.nama_indikator">{{ form.errors.nama_indikator }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="form-label">Deskripsi</label>
                        <textarea v-model="form.deskripsi" rows="2" class="form-input" placeholder="Penjelasan singkat..."></textarea>
                    </div>

                    <div>
                        <label class="form-label">Tema <span class="text-red-500">*</span></label>
                        <select v-model="form.id_tema" class="form-select">
                            <option value="" disabled>-- Pilih Tema --</option>
                            <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                        </select>
                        <p class="form-error" v-if="form.errors.id_tema">{{ form.errors.id_tema }}</p>
                    </div>

                    <div>
                        <label class="form-label">Urusan <span class="text-red-500">*</span></label>
                        <select v-model="form.id_urusan" class="form-select">
                            <option value="" disabled>-- Pilih Urusan --</option>
                            <option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Bidang <span class="text-red-500">*</span></label>
                        <select v-model="form.id_bidang" class="form-select">
                            <option value="" disabled>-- Pilih Bidang --</option>
                            <option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Frekuensi <span class="text-red-500">*</span></label>
                        <select v-model="form.id_frekuensi" class="form-select">
                            <option value="" disabled>-- Pilih Frekuensi --</option>
                            <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Satuan</label>
                            <input v-model="form.satuan" type="text" class="form-input" placeholder="Ex: Jiwa, %">
                        </div>
                        <div>
                            <label class="form-label">Tahun Data</label>
                            <input v-model="form.periode" type="number" class="form-input font-bold text-blue-600">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Sumber Data</label>
                            <input v-model="form.sumber" type="text" class="form-input" placeholder="Ex: BPS">
                        </div>
                        <div>
                            <label class="form-label">Kata Kunci</label>
                            <input v-model="form.kata_kunci" type="text" class="form-input" placeholder="Tag pencarian...">
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="step === 1" class="flex flex-col items-center justify-center bg-white rounded-3xl shadow-sm border-2 border-dashed border-slate-300 p-10 text-center animate-fade-in hover:border-blue-400 hover:bg-blue-50/10 transition-all">
                <div class="max-w-md w-full space-y-4">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto">
                        <svg v-if="!isLoading" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                        <svg v-else class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Upload File Excel</h2>
                        <p class="text-slate-500 text-sm mt-1">Lengkapi form di atas, lalu upload file (.xlsx, .csv)</p>
                    </div>
                    
                    <label class="block">
                        <span class="sr-only">Choose file</span>
                        <input type="file" class="block w-full text-sm text-slate-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-50 file:text-blue-700
                            hover:file:bg-blue-100 cursor-pointer"
                            accept=".xlsx, .xls, .csv" @change="handleFileUpload" :disabled="isLoading"
                        />
                    </label>
                </div>
            </div>

            <div v-if="step === 2" class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-[700px] animate-slide-up">
                <div class="bg-slate-50 px-8 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-indigo-600 rounded-full"></span>
                        2. Mapping Kolom Excel
                    </h3>
                    
                    <button @click="step = 1" class="text-xs font-bold text-slate-500 hover:text-red-600 flex items-center gap-1 transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Upload Ulang File
                    </button>
                </div>

                <div class="overflow-auto flex-1 w-full bg-slate-50/50">
                    <table class="border-collapse w-max">
                        <thead class="bg-white sticky top-0 z-20 shadow-sm">
                            <tr>
                                <th class="p-4 border-b border-r w-14 bg-slate-50 text-center text-xs font-bold text-slate-400 sticky left-0 z-30 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">#</th>
                                
                                <th v-for="(colName, colKey) in excelData.headers" :key="colKey" 
                                    class="p-4 border-b border-r w-64 align-top bg-white transition-colors"
                                    :class="form.mapping[colKey] === '__new__' ? 'bg-indigo-50/30' : ''">
                                    
                                    <div class="flex flex-col gap-3">
                                        <div class="bg-slate-100 text-slate-600 rounded-lg py-2 px-3 text-center border border-slate-200">
                                            <span class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">HEADER EXCEL</span>
                                            <span class="block text-xs font-bold truncate" :title="colName">{{ colName }}</span>
                                        </div>

                                        <select v-model="form.mapping[colKey]" @change="handleMappingChange(colKey)"
                                            class="w-full text-xs font-semibold border rounded-lg py-2.5 px-3 focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer"
                                            :class="form.mapping[colKey] === '__new__' ? 'border-indigo-300 text-indigo-700 bg-white' : 'border-slate-300 text-slate-500'">
                                            <option value="__new__">✨ Simpan Kolom Ini</option>
                                            <option :value="null">⛔ Abaikan</option>
                                        </select>

                                        <div v-if="form.mapping[colKey] === '__new__'" class="bg-white p-3 rounded-xl border border-indigo-100 shadow-sm space-y-3 animate-fade-in">
                                            <div>
                                                <label class="text-[10px] font-bold text-indigo-500 uppercase tracking-wide block mb-1">Nama Field DB</label>
                                                <input type="text" v-model="form.new_fields[colKey].nama_field" 
                                                    class="w-full text-xs border-slate-200 rounded-md px-2 py-2 font-semibold text-slate-700 focus:border-indigo-500 focus:ring-0 bg-slate-50">
                                            </div>
                                            <div>
                                                <label class="text-[10px] font-bold text-indigo-500 uppercase tracking-wide block mb-1">Tipe Data</label>
                                                <select v-model="form.new_fields[colKey].tipe_field" 
                                                    class="w-full text-xs border-slate-200 rounded-md px-2 py-2 text-slate-600 focus:border-indigo-500 focus:ring-0 bg-white">
                                                    <option value="text">Teks (Umum)</option>
                                                    <option value="number">Angka / Nominal</option>
                                                    <option value="date">Tanggal</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            <tr v-for="(row, index) in excelData.preview" :key="index" class="hover:bg-slate-50 transition-colors">
                                <td class="p-3 border-r text-center text-xs font-mono text-slate-400 bg-slate-50 sticky left-0 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                    {{ index + 2 }}
                                </td>
                                <td v-for="(colName, colKey) in excelData.headers" :key="'c-'+colKey" 
                                    class="p-3 border-r text-xs text-slate-600 font-mono whitespace-nowrap overflow-hidden truncate max-w-[16rem]"
                                    :class="form.mapping[colKey] === '__new__' ? 'bg-indigo-50/10 font-medium text-slate-800' : 'opacity-60'">
                                    {{ row[colKey] }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-if="step === 2" class="sticky bottom-0 z-40 w-full -mx-4 md:-mx-8 px-4 md:px-8 pb-4 pt-4 bg-white/80 backdrop-blur-md border-t border-slate-200 mt-auto animate-slide-up">
                <div class="max-w-5xl mx-auto flex items-center justify-between">
                    <div class="hidden md:flex items-center gap-3">
                        <div class="bg-green-100 text-green-600 p-2 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div class="text-xs">
                            <p class="font-bold text-slate-700">Konfirmasi Simpan</p>
                            <p class="text-slate-500">Pastikan kolom yang ingin disimpan sudah dipilih.</p>
                        </div>
                    </div>
                    
                    <button @click="submitAll" :disabled="isLoading" 
                        class="w-full md:w-auto bg-blue-600 text-white px-8 py-3 rounded-xl font-bold text-sm hover:bg-blue-700 transition shadow-lg shadow-blue-200 disabled:opacity-70 flex justify-center items-center gap-2">
                        <svg v-if="isLoading" class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        {{ isLoading ? 'Menyimpan Data...' : 'Simpan Semua Data' }}
                    </button>
                </div>
            </div>

            <div class="h-8"></div>
        </div>
    </div>
</template>

<style scoped>
.form-label { @apply block text-sm font-medium text-slate-700 mb-1.5; }
.form-input { @apply w-full border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-sm placeholder:text-slate-400; }
.form-select { @apply w-full border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-sm bg-white text-slate-700; }
.form-error { @apply text-red-500 text-xs mt-1 font-medium; }

.animate-fade-in { animation: fadeIn 0.5s ease-out; }
.animate-slide-up { animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

/* Custom Scrollbar */
.overflow-auto::-webkit-scrollbar { width: 8px; height: 8px; }
.overflow-auto::-webkit-scrollbar-track { background: transparent; }
.overflow-auto::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.overflow-auto::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>