<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const props = defineProps({
    tema: Array,
    urusan: Array,
    bidang: Array,
    frekuensi: Array,
});

const form = useForm({
    nama_indikator: '',
    deskripsi: '',
    id_tema: '',
    id_urusan: '',
    id_bidang: '',
    id_frekuensi: '',
    kata_kunci: '',
    satuan: '',
    sumber: '',
    status: 'aktif'
});

const submit = () => {
    form.post('/inputer/data', {
        onSuccess: () => {
            // Berikan feedback sukses jika diperlukan
        },
        onError: (errors) => {
            console.log('Validasi Gagal:', errors);
        }
    });
};
</script>

<template>
    <Head title="Tambah Indikator" />

    <div class="max-w-4xl mx-auto py-12 px-4">
        <Link href="/inputer/data" class="flex items-center gap-2 text-[#A2B5CB] hover:text-[#00139E] transition-colors mb-6 group">
            <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="font-bold uppercase tracking-widest text-[10px]">Kembali ke Daftar Data</span>
        </Link>

        <div class="bg-white rounded-[2.5rem] p-12 shadow-2xl shadow-gray-100 border border-gray-400">
            <div class="mb-10">
                <h1 class="text-4xl font-black text-gray-900 tracking-tight">
                    Input <span class="text-[#00139E]">Indikator</span>
                </h1>
                <p class="text-gray-400 font-medium mt-2">Daftarkan parameter indikator baru untuk pelaporan data sektoral.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Nama Indikator <span class="text-red-500">*</span></label>
                        <input v-model="form.nama_indikator" type="text" placeholder="Contoh: Jumlah Penduduk Miskin Kota Mataram" 
                            class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all font-bold text-gray-700"
                            :class="{ 'border-red-500': form.errors.nama_indikator }" />
                        <p v-if="form.errors.nama_indikator" class="text-red-500 text-xs ml-4 font-bold">{{ form.errors.nama_indikator }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Deskripsi</label>
                        <textarea v-model="form.deskripsi" rows="3" placeholder="Jelaskan secara singkat mengenai data ini..." 
                            class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all font-medium text-gray-600 resize-none"></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Tema <span class="text-red-500">*</span></label>
                        <select v-model="form.id_tema" class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all appearance-none font-bold text-gray-700">
                            <option value="">Pilih Tema...</option>
                            <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Urusan <span class="text-red-500">*</span></label>
                        <select v-model="form.id_urusan" class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all appearance-none font-bold text-gray-700">
                            <option value="">Pilih Urusan...</option>
                            <option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Bidang <span class="text-red-500">*</span></label>
                        <select v-model="form.id_bidang" class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all appearance-none font-bold text-gray-700">
                            <option value="">Pilih Bidang...</option>
                            <option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Frekuensi Update <span class="text-red-500">*</span></label>
                        <select v-model="form.id_frekuensi" class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all appearance-none font-bold text-gray-700">
                            <option value="">Pilih Frekuensi...</option>
                            <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Satuan <span class="text-red-500">*</span></label>
                        <input v-model="form.satuan" type="text" placeholder="Contoh: Jiwa / Persen / Km" 
                            class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all font-bold text-gray-700" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Sumber Data</label>
                        <input v-model="form.sumber" type="text" placeholder="BPS / Dinas Terkait" 
                            class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all font-bold text-gray-700" />
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Kata Kunci (Tags)</label>
                    <input v-model="form.kata_kunci" type="text" placeholder="Pisahkan dengan koma (contoh: Ekonomi, Mataram)" 
                        class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all font-bold text-gray-700" />
                </div>

                <div class="pt-10 flex items-center justify-end gap-4 border-t border-gray-50">
                    <Link href="/inputer/data" class="px-8 py-4 text-gray-400 font-bold hover:text-gray-600 transition-colors uppercase tracking-widest text-[10px]">
                        Batal
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="bg-[#00139E] text-white px-12 py-5 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-[#000B58] shadow-xl shadow-blue-200 transition-all disabled:opacity-50 flex items-center gap-3 active:scale-95">
                        <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'MENYIMPAN...' : 'SIMPAN INDIKATOR' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>