<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AlertModal from '@/Components/Layout/AlertModal.vue';
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
const showAlertModal = ref(false);
const alertTitle = ref('Informasi');
const alertMessage = ref('');
const alertType = ref('info');

const openAlert = (title, message, type = 'info') => {
    alertTitle.value = title;
    alertMessage.value = message;
    alertType.value = type;
    showAlertModal.value = true;
};

// FORM GLOBAL
const form = useForm({
    nama_data: '', deskripsi: '', id_tema: '', id_urusan: '', 
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
        const response = await axios.post('/inputer/data/wizard/analyze', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (response.data.status === 'success') {
            const res = response.data;
            excelData.headers = res.headers;
            excelData.preview = res.preview;
            form.file_path = res.temp_path;
            form.mapping = res.default_mapping;
            form.new_fields = res.default_new_fields;
            
            if (!form.nama_data) {
                form.nama_data = res.suggested_name;
            }
            
            step.value = 2;
        }
    } catch (error) {
        openAlert('Gagal Membaca File', error.response?.data?.message || error.message, 'error');
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

    form.clearErrors();

    console.log("Data yang dikirim:", form.data());
    try {
        // Payload: ambil data mentah dari form useForm
        const payload = form.data();
        
        // Pastikan endpoint sesuai dengan Route::post Anda
        const response = await axios.post('/inputer/data/wizard/store-all', payload);
        
        if (response.data.status === 'success') {
            // Gunakan router.visit untuk redirect bersih via Inertia
            router.visit('/inputer/data', {
                method: 'get'
            }); 
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            // Mapping eror dari Laravel kembali ke objek form.errors
            const serverErrors = error.response.data.errors;
            
            // Set error ke useForm agar muncul di bawah input (teks merah)
            Object.keys(serverErrors).forEach(key => {
                form.setError(key, serverErrors[key][0]);
            });

            openAlert('Validasi Gagal', 'Mohon lengkapi isian bertanda bintang (*).', 'warning');
            
            // Scroll otomatis ke field yang bermasalah
            document.getElementById('form-metadata')?.scrollIntoView({ behavior: 'smooth' });
        } else {
            openAlert('Gagal Menyimpan', error.response?.data?.message || 'Terjadi kesalahan sistem', 'error');
        }
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <Head title="Wizard Input Data" />

    <div class="min-h-screen py-0 px-4 md:px-8 mx-auto">
        <div class="mx-auto space-y-8">

            <div id="form-metadata" class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-100 border border-gray-400 overflow-hidden animate-fade-in">
                <div class="bg-gray-50/50 px-10 py-6 border-b border-gray-400 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 tracking-tight flex items-center gap-3 uppercase">
                            <span class="w-2 h-8 bg-[#00139E] rounded-full"></span>
                            1. Metadata Indikator
                        </h3>
                    </div>
                    <span v-if="step === 1" class="text-[10px] font-black text-[#00139E] uppercase tracking-widest bg-blue-50 px-4 py-2 rounded-xl border border-blue-100 italic">
                        Konfigurasi Parameter Data
                    </span>
                </div>
                
                <div class="p-10 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="md:col-span-2 space-y-2">
                        <label class="form-label-premium">Nama Indikator <span class="text-red-500">*</span></label>
                        <input v-model="form.nama_data" type="text" class="form-input-premium font-bold text-gray-800" placeholder="Contoh: Jumlah Penduduk Miskin Kota Mataram">
                        <p class="text-red-500 text-[10px] font-bold mt-1 uppercase ml-4" v-if="form.errors.nama_data">{{ form.errors.nama_data }}</p>
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="form-label-premium">Deskripsi Indikator</label>
                        <textarea v-model="form.deskripsi" rows="2" class="form-input-premium resize-none" placeholder="Penjelasan singkat mengenai indikator..."></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="form-label-premium">Tema Sektoral <span class="text-red-500">*</span></label>
                        <select v-model="form.id_tema" class="form-select-premium font-bold">
                            <option value="" disabled>-- Pilih Tema --</option>
                            <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                        </select>
                        <p class="text-red-500 text-[10px] font-bold mt-1 uppercase ml-4" v-if="form.errors.id_tema">{{ form.errors.id_tema }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="form-label-premium">Urusan Pemerintahan <span class="text-red-500">*</span></label>
                        <select v-model="form.id_urusan" class="form-select-premium font-bold">
                            <option value="" disabled>-- Pilih Urusan --</option>
                            <option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="form-label-premium">Bidang / Instansi <span class="text-red-500">*</span></label>
                        <select v-model="form.id_bidang" class="form-select-premium font-bold">
                            <option value="" disabled>-- Pilih Bidang --</option>
                            <option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="form-label-premium">Frekuensi Pelaporan <span class="text-red-500">*</span></label>
                        <select v-model="form.id_frekuensi" class="form-select-premium font-bold">
                            <option value="" disabled>-- Pilih Frekuensi --</option>
                            <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="form-label-premium">Satuan</label>
                            <input v-model="form.satuan" type="text" class="form-input-premium font-bold" placeholder="Ex: Jiwa, %">
                        </div>
                        <div class="space-y-2">
                            <label class="form-label-premium">Tahun Data</label>
                            <input v-model="form.periode" type="number" class="form-input-premium font-black text-[#00139E]">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="form-label-premium">Sumber Data</label>
                            <input v-model="form.sumber" type="text" class="form-input-premium font-bold" placeholder="Ex: BPS">
                        </div>
                        <div class="space-y-2">
                            <label class="form-label-premium">Kata Kunci</label>
                            <input v-model="form.kata_kunci" type="text" class="form-input-premium" placeholder="Tag pencarian...">
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="step === 1" class="flex flex-col items-center justify-center bg-white rounded-[2.5rem] shadow-2xl shadow-gray-100 border-4 border-dashed border-gray-300 p-20 text-center animate-fade-in hover:border-[#00139E] hover:bg-blue-50/10 transition-all group">
                <div class="max-w-md w-full space-y-6">
                    <div class="w-24 h-24 bg-blue-50 text-[#00139E] rounded-[2rem] flex items-center justify-center mx-auto transition-transform group-hover:scale-110 shadow-inner">
                        <svg v-if="!isLoading" class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                        <svg v-else class="w-12 h-12 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight uppercase">Upload Dataset Excel</h2>
                        <p class="text-gray-400 font-medium mt-2">Format yang didukung: .xlsx, .xls, .csv</p>
                    </div>
                    
                    <div class="relative group/input">
                        <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                            accept=".xlsx, .xls, .csv" @change="handleFileUpload" :disabled="isLoading" />
                        <div class="bg-[#00139E] text-white px-10 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-blue-200 transition-all group-hover/input:bg-[#000B58]">
                            Pilih File Dari Komputer
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="step === 2" class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-100 border border-gray-400 overflow-hidden flex flex-col h-[750px] animate-slide-up">
                <div class="bg-gray-50 px-10 py-6 border-b border-gray-400 flex justify-between items-center">
                    <h3 class="text-xl font-black text-gray-900 tracking-tight flex items-center gap-3 uppercase">
                        <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
                        2. Mapping Struktur Kolom
                    </h3>
                    
                    <button @click="step = 1" class="text-[10px] font-black text-rose-500 hover:text-rose-700 flex items-center gap-2 transition uppercase tracking-widest bg-rose-50 px-4 py-2 rounded-xl border border-rose-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Ganti File Excel
                    </button>
                </div>

                <div class="overflow-auto flex-1 w-full bg-gray-50/30">
                    <table class="border-collapse w-max">
                        <thead class="bg-white sticky top-0 z-20 shadow-sm">
                            <tr>
                                <th class="p-4 border-b border-r w-14 bg-gray-100 text-center text-[10px] font-black text-gray-400 sticky left-0 z-30 border-gray-300 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] uppercase">#</th>
                                
                                <th v-for="(colName, colKey) in excelData.headers" :key="colKey" 
                                    class="p-6 border-b border-r border-gray-300 w-80 align-top bg-white transition-colors"
                                    :class="form.mapping[colKey] === '__new__' ? 'bg-indigo-50/30' : ''">
                                    
                                    <div class="flex flex-col gap-4">
                                        <div class="bg-gray-50 text-gray-700 rounded-2xl py-4 px-5 text-center border border-gray-200 shadow-inner">
                                            <span class="block text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">HEADER EXCEL</span>
                                            <span class="block text-xs font-black truncate text-[#00139E]" :title="colName">{{ colName }}</span>
                                        </div>

                                        <select v-model="form.mapping[colKey]" @change="handleMappingChange(colKey)"
                                            class="w-full text-[10px] font-black uppercase tracking-widest border-2 rounded-2xl py-4 px-4 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all cursor-pointer shadow-sm"
                                            :class="form.mapping[colKey] === '__new__' ? 'border-indigo-400 text-indigo-700 bg-white' : 'border-gray-200 text-gray-400 bg-gray-50/50'">
                                            <option value="__new__">✨ Simpan Sebagai Kolom Baru</option>
                                            <option :value="null">⛔ Abaikan Kolom Ini</option>
                                        </select>

                                        <div v-if="form.mapping[colKey] === '__new__'" class="bg-white p-5 rounded-[1.5rem] border-2 border-indigo-100 shadow-xl shadow-indigo-500/5 space-y-4 animate-fade-in ring-4 ring-indigo-50/50">
                                            <div class="space-y-1.5">
                                                <label class="text-[9px] font-black text-indigo-500 uppercase tracking-widest block ml-2">Identitas Kolom (Database)</label>
                                                <input type="text" v-model="form.new_fields[colKey].nama_field" 
                                                    class="w-full text-xs border-gray-200 rounded-xl px-4 py-3 font-bold text-gray-800 focus:border-indigo-500 focus:ring-0 bg-gray-50">
                                            </div>
                                            <div class="space-y-1.5">
                                                <label class="text-[9px] font-black text-indigo-500 uppercase tracking-widest block ml-2">Tipe Representasi Data</label>
                                                <select v-model="form.new_fields[colKey].tipe_field" 
                                                    class="w-full text-xs border-gray-200 rounded-xl px-4 py-3 text-gray-700 font-bold focus:border-indigo-500 focus:ring-0 bg-white">
                                                    <option value="text">Teks (Deskriptif)</option>
                                                    <option value="number">Angka / Statistik</option>
                                                    <option value="date">Format Tanggal</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(row, index) in excelData.preview" :key="index" class="hover:bg-blue-50/20 transition-all group">
                                <td class="p-4 border-r border-gray-200 text-center text-[10px] font-black text-gray-300 bg-gray-50 sticky left-0 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                    {{ index + 2 }}
                                </td>
                                <td v-for="(colName, colKey) in excelData.headers" :key="'c-'+colKey" 
                                    class="p-4 border-r border-gray-100 text-xs text-gray-600 font-bold whitespace-nowrap overflow-hidden truncate max-w-[20rem]"
                                    :class="form.mapping[colKey] === '__new__' ? 'bg-indigo-50/10 text-gray-900 border-r-indigo-100' : 'opacity-40 grayscale'">
                                    {{ row[colKey] }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-if="step === 2" class="sticky bottom-0 z-40 w-full bg-white/90 backdrop-blur-xl border-t border-gray-400 py-6 px-10 rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.05)] animate-slide-up">
                <div class="flex items-center justify-between">
                    <div class="hidden md:flex items-center gap-4">
                        <div class="bg-emerald-100 text-emerald-600 p-3 rounded-2xl shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-black text-gray-900 uppercase tracking-widest">Siap Disimpan</p>
                            <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">Pastikan seluruh kolom ✨ telah terkonfigurasi</p>
                        </div>
                    </div>
                    
                    <button @click="submitAll" :disabled="isLoading" 
                        class="w-full md:w-auto bg-[#00139E] text-white px-12 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-[#000B58] transition-all shadow-xl shadow-blue-500/20 disabled:opacity-70 flex justify-center items-center gap-3 active:scale-95">
                        <svg v-if="isLoading" class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        {{ isLoading ? 'Memproses Database...' : 'Finalisasi & Simpan Dataset' }}
                    </button>
                </div>
            </div>

            <div class="h-10"></div>
        </div>
    </div>

    <AlertModal
        :show="showAlertModal"
        :title="alertTitle"
        :description="alertMessage"
        :type="alertType"
        @close="showAlertModal = false"
    />
</template>

<style scoped>
.form-label-premium { @apply block text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4; }
.form-input-premium { @apply w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all text-sm shadow-sm placeholder:text-gray-300; }
.form-select-premium { @apply w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all text-sm shadow-sm appearance-none cursor-pointer; }

.animate-fade-in { animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
.animate-slide-up { animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1); }

@keyframes fadeIn { from { opacity: 0; transform: scale(0.98); } to { opacity: 1; transform: scale(1); } }
@keyframes slideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }

/* Custom Scrollbar for Excel Table */
.overflow-auto::-webkit-scrollbar { width: 10px; height: 10px; }
.overflow-auto::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
.overflow-auto::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; border: 3px solid #f8fafc; }
.overflow-auto::-webkit-scrollbar-thumb:hover { background: #00139E; }
</style>
