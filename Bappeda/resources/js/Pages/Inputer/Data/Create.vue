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
    // PERBAIKAN: Gunakan URL manual, bukan route()
    // POST ke /inputer/data (sesuai route resource di Laravel)
    form.post('/inputer/data', {
        onSuccess: () => {
            // form.reset(); 
        },
        onError: (errors) => {
            console.log('Validasi Gagal:', errors);
        }
    });
};
</script>

<template>
    <Head title="Input Data Indikator" />

    <div class="max-w-3xl mx-auto py-10">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-extrabold text-gray-800">Tambah Data Baru</h1>
            
            <Link href="/inputer/data" class="text-gray-500 font-bold hover:text-blue-600 transition">
                &larr; Kembali
            </Link>
        </div>

        <div class="bg-white shadow-xl rounded-[2rem] overflow-hidden border border-gray-100">
            <form @submit.prevent="submit">
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center border-b border-gray-100 p-6 hover:bg-gray-50 transition">
                    <label class="w-full sm:w-1/3 font-bold text-gray-700 mb-2 sm:mb-0">Nama Data <span class="text-red-500">*</span></label>
                    <div class="w-full sm:w-2/3">
                        <input v-model="form.nama_indikator" type="text" placeholder="Contoh: Jumlah Penduduk Miskin"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 font-medium" />
                        <p v-if="form.errors.nama_indikator" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.nama_indikator }}</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-start border-b border-gray-100 p-6 hover:bg-gray-50 transition">
                    <label class="w-full sm:w-1/3 font-bold text-gray-700 mb-2 sm:mb-0 pt-2">Deskripsi</label>
                    <div class="w-full sm:w-2/3">
                        <textarea v-model="form.deskripsi" rows="3" placeholder="Penjelasan singkat indikator..."
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 font-medium resize-none"></textarea>
                        <p v-if="form.errors.deskripsi" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.deskripsi }}</p>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-gray-100 bg-gray-50/30">
                    
                    <div>
                        <label class="block font-bold text-gray-700 mb-2 text-sm">Tema <span class="text-red-500">*</span></label>
                        <select v-model="form.id_tema" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 font-medium">
                            <option value="">-- Pilih Tema --</option>
                            <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                        </select>
                        <p v-if="form.errors.id_tema" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.id_tema }}</p>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-2 text-sm">Urusan <span class="text-red-500">*</span></label>
                        <select v-model="form.id_urusan" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 font-medium">
                            <option value="">-- Pilih Urusan --</option>
                            <option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                        </select>
                        <p v-if="form.errors.id_urusan" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.id_urusan }}</p>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-2 text-sm">Bidang <span class="text-red-500">*</span></label>
                        <select v-model="form.id_bidang" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 font-medium">
                            <option value="">-- Pilih Bidang --</option>
                            <option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                        </select>
                        <p v-if="form.errors.id_bidang" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.id_bidang }}</p>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-2 text-sm">Frekuensi <span class="text-red-500">*</span></label>
                        <select v-model="form.id_frekuensi" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 font-medium">
                            <option value="">-- Pilih Frekuensi --</option>
                            <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                        </select>
                        <p v-if="form.errors.id_frekuensi" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.id_frekuensi }}</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center border-b border-gray-100 p-6 hover:bg-gray-50 transition gap-4">
                    <div class="w-full sm:w-1/2">
                        <label class="block font-bold text-gray-700 mb-2 text-sm">Satuan <span class="text-red-500">*</span></label>
                        <input v-model="form.satuan" type="text" placeholder="Contoh: Jiwa, Persen, Unit"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 font-medium" />
                        <p v-if="form.errors.satuan" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.satuan }}</p>
                    </div>
                    <div class="w-full sm:w-1/2">
                        <label class="block font-bold text-gray-700 mb-2 text-sm">Sumber Data</label>
                        <input v-model="form.sumber" type="text" placeholder="Contoh: BPS, Dinas Kesehatan"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 font-medium" />
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center border-b border-gray-100 p-6 hover:bg-gray-50 transition">
                    <label class="w-full sm:w-1/3 font-bold text-gray-700 mb-2 sm:mb-0">Kata Kunci</label>
                    <div class="w-full sm:w-2/3">
                        <input v-model="form.kata_kunci" type="text" placeholder="Tag pencarian..."
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 font-medium" />
                    </div>
                </div>

                <button type="submit" :disabled="form.processing"
                    class="w-full bg-[#00AEEF] text-white py-5 font-black text-lg hover:bg-sky-600 transition tracking-widest uppercase flex justify-center items-center gap-2 disabled:opacity-70">
                    <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>{{ form.processing ? 'MENYIMPAN...' : 'SIMPAN DATA' }}</span>
                </button>
            </form>
        </div>
    </div>
</template>