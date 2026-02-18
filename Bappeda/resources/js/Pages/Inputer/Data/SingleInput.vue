<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps({ 
    tema: Array, 
    urusan: Array, 
    bidang: Array, 
    frekuensi: Array 
});

const isLoading = ref(false);

// Pastikan semua field yang divalidasi di Backend ada di sini
const form = useForm({
    nama_indikator: '',
    deskripsi: '',
    id_tema: '',
    id_urusan: '',
    id_bidang: '',
    id_frekuensi: 1, // Beri default 1 (biasanya Tahunan)
    satuan: '',
    tahun: new Date().getFullYear(),
    nilai: '',
    sumber: '', // Field ini wajib ada jika backend memintanya
    kata_kunci: '',
});

const submitData = () => {
    isLoading.value = true;
    
    // Gunakan form.post agar validasi errors dari Laravel otomatis terpetakan
    form.post('/inputer/data/store-single', {
        preserveScroll: true,
        onSuccess: () => {
            // Toast atau Notifikasi Sukses
            form.reset();
            router.visit('/inputer/dashboard');
        },
        onError: (errors) => {
            console.log(errors);
            alert('Gagal menyimpan. Pastikan semua kolom bertanda bintang (*) sudah terisi dengan benar.');
        },
        onFinish: () => isLoading.value = false
    });
};
</script>

<template>
    <Head title="Input Indikator Tunggal" />
    <div class="min-h-screen py-10 px-4 md:px-8 mx-auto max-w-5xl">
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-black text-[#000B58] uppercase tracking-tight">Input Indikator Manual</h2>
            <p class="text-gray-500 font-medium mt-2">Ketik data indikator baru secara langsung ke dalam sistem Smart City.</p>
        </div>

        <div class="space-y-8">
            <div class="bg-white rounded-[2rem] shadow-xl border p-10">
                <h3 class="text-lg font-black text-[#000B58] mb-6 uppercase border-b pb-4 flex items-center gap-2">
                    <span class="w-2 h-6 bg-blue-600 rounded-full"></span>
                    1. Informasi & Nilai Data
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black uppercase text-gray-500 ml-2">Nama Indikator *</label>
                        <input v-model="form.nama_indikator" type="text" placeholder="Contoh: Jumlah Penduduk Miskin" 
                            class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold mt-1 focus:ring-blue-500" 
                            :class="{'border-red-500': form.errors.nama_indikator}">
                        <div v-if="form.errors.nama_indikator" class="text-red-500 text-[10px] mt-1 ml-2">{{ form.errors.nama_indikator }}</div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-gray-500 ml-2">Nilai Data *</label>
                        <input v-model="form.nilai" type="text" placeholder="0.00"
                            class="w-full bg-emerald-50 border-emerald-200 rounded-xl px-4 py-3 text-sm font-bold mt-1" required>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-gray-500 ml-2">Tahun Data *</label>
                        <input v-model="form.tahun" type="number" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold mt-1" required>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-gray-500 ml-2">Satuan * (Contoh: %, Jiwa, Km)</label>
                        <input v-model="form.satuan" type="text" placeholder="%" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold mt-1">
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-gray-500 ml-2">Sumber Data *</label>
                        <input v-model="form.sumber" type="text" placeholder="Instansi/Dinas terkait" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold mt-1">
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black uppercase text-gray-500 ml-2">Deskripsi Singkat</label>
                        <textarea v-model="form.deskripsi" placeholder="Penjelasan mengenai indikator ini..." 
                            class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm mt-1 min-h-[80px]"></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-xl border p-10">
                <h3 class="text-lg font-black text-[#000B58] mb-6 uppercase border-b pb-4 flex items-center gap-2">
                    <span class="w-2 h-6 bg-orange-500 rounded-full"></span>
                    2. Klasifikasi Metadata
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black uppercase text-gray-500 ml-2">Tema Sektoral *</label>
                        <select v-model="form.id_tema" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold mt-1">
                            <option value="">Pilih Tema...</option>
                            <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-gray-500 ml-2">Urusan Pemerintahan *</label>
                        <select v-model="form.id_urusan" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold mt-1">
                            <option value="">Pilih Urusan...</option>
                            <option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-gray-500 ml-2">Bidang / Instansi *</label>
                        <select v-model="form.id_bidang" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold mt-1">
                            <option value="">Pilih Bidang...</option>
                            <option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-gray-500 ml-2">Frekuensi Pelaporan *</label>
                        <select v-model="form.id_frekuensi" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold mt-1">
                            <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 pb-20 gap-4">
                <button type="button" @click="router.visit('/inputer/dashboard')" class="px-8 py-4 text-gray-400 font-bold hover:text-gray-600 transition-colors uppercase tracking-widest text-xs">
                    Batal
                </button>
                <button @click="submitData" :disabled="form.processing" 
                    class="bg-[#000B58] text-white px-16 py-4 rounded-xl font-black text-sm uppercase tracking-widest hover:bg-blue-900 shadow-xl disabled:opacity-50 flex items-center gap-2">
                    <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Indikator' }}
                </button>
            </div>
        </div>
    </div>
</template>