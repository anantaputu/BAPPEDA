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
    form.post('/data');
};
</script>

<template>
    <Head title="Input Data Indikator" />

    <div class="max-w-2xl mx-auto py-10">
        <div class="bg-white shadow-sm overflow-hidden">
            <form @submit.prevent="submit">
                
                <div class="flex items-center border-b border-gray-300">
                    <label class="w-1/3 px-6 py-4 text-gray-800 font-medium">Nama Data</label>
                    <div class="w-2/3">
                        <input v-model="form.nama_indikator" type="text" 
                            class="w-full bg-gray-100 border-none focus:ring-0 px-4 py-4" />
                    </div>
                </div>

                <div class="flex border-b border-gray-300">
                    <label class="w-1/3 px-6 py-4 text-gray-800 font-medium">Deskripsi</label>
                    <div class="w-2/3">
                        <textarea v-model="form.deskripsi" rows="4"
                            class="w-full bg-gray-100 border-none focus:ring-0 px-4 py-4 resize-none"></textarea>
                    </div>
                </div>

                <div class="flex items-center border-b border-gray-300">
                    <label class="w-1/3 px-6 py-4 text-gray-800 font-medium">Tema</label>
                    <div class="w-2/3">
                        <select v-model="form.id_tema" class="w-full bg-gray-100 border-none focus:ring-0 px-4 py-4 appearance-none">
                            <option value="">Pilih Tema</option>
                            <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center border-b border-gray-300">
                    <label class="w-1/3 px-6 py-4 text-gray-800 font-medium">Urusan</label>
                    <div class="w-2/3">
                        <select v-model="form.id_urusan" class="w-full bg-gray-100 border-none focus:ring-0 px-4 py-4">
                            <option value="">Pilih Urusan</option>
                            <option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center border-b border-gray-100">
                    <label class="w-1/3 px-6 py-4 text-gray-800 font-medium">Bidang</label>
                    <div class="w-2/3">
                        <select v-model="form.id_bidang" class="w-full bg-gray-50 border-none focus:ring-0 px-4 py-4">
                            <option value="">Pilih Bidang</option>
                            <option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center border-b border-gray-300">
                    <label class="w-1/3 px-6 py-4 text-gray-800 font-medium">Kata Kunci</label>
                    <div class="w-2/3">
                        <input v-model="form.kata_kunci" type="text" 
                            class="w-full bg-gray-100 border-none focus:ring-0 px-4 py-4" />
                    </div>
                </div>

                <div class="flex items-center border-b border-gray-300">
                    <label class="w-1/3 px-6 py-4 text-gray-800 font-medium">Satuan</label>
                    <div class="w-2/3">
                        <input v-model="form.satuan" type="text" 
                            class="w-full bg-gray-100 border-none focus:ring-0 px-4 py-4" />
                    </div>
                </div>

                <div class="flex items-center border-b border-gray-300">
                    <label class="w-1/3 px-6 py-4 text-gray-800 font-medium">Frekuensi</label>
                    <div class="w-2/3">
                        <select v-model="form.id_frekuensi" class="w-full bg-gray-100 border-none focus:ring-0 px-4 py-4">
                            <option value="">Pilih Frekuensi</option>
                            <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center border-b border-gray-300 mb-6">
                    <label class="w-1/3 px-6 py-4 text-gray-800 font-medium">Sumber</label>
                    <div class="w-2/3">
                        <input v-model="form.sumber" type="text" 
                            class="w-full bg-gray-100 border-none focus:ring-0 px-4 py-4" />
                    </div>
                </div>

                <button type="submit" :disabled="form.processing"
                    class="w-full bg-[#00AEEF] text-white py-4 font-bold text-lg hover:bg-sky-600 transition tracking-widest uppercase">
                    {{ form.processing ? 'Memproses...' : 'SIMPAN' }}
                </button>
            </form>
        </div>
    </div>
</template>