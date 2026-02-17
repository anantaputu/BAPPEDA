<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ tema: Array, urusan: Array, bidang: Array, frekuensi: Array });
const isLoading = ref(false);

const form = useForm({
    nama_indikator: '', deskripsi: '', id_tema: '', id_urusan: '', id_bidang: '', id_frekuensi: '', satuan: '', tahun: new Date().getFullYear(), nilai: '', sumber: '',
});

const submitData = () => {
    isLoading.value = true;
    form.post('/inputer/data/store-single', {
        preserveScroll: true,
        onSuccess: () => {
            alert('Data indikator berhasil disimpan!');
            router.visit('/inputer/dashboard');
        },
        onError: () => alert('Mohon lengkapi semua kolom bertanda bintang (*).'),
        onFinish: () => isLoading.value = false
    });
};
</script>

<template>
    <Head title="Input Indikator Tunggal" />
    <div class="min-h-screen py-10 px-4 md:px-8 mx-auto max-w-5xl">
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-black text-[#000B58] uppercase">Input Indikator Manual</h2>
            <p class="text-gray-500 font-medium mt-2">Ketik data indikator baru secara langsung ke dalam sistem.</p>
        </div>

        <div class="space-y-8">
            <div class="bg-white rounded-[2rem] shadow-xl border p-10">
                <h3 class="text-lg font-black text-[#000B58] mb-6 uppercase border-b pb-4">1. Informasi & Nilai Data</h3>
                <div class="grid grid-cols-2 gap-6">
                    <div class="col-span-2"><label class="text-[10px] font-black uppercase text-gray-500">Nama Indikator *</label><input v-model="form.nama_indikator" type="text" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold mt-1" required></div>
                    <div><label class="text-[10px] font-black uppercase text-gray-500">Nilai Data *</label><input v-model="form.nilai" type="text" class="w-full bg-emerald-50 border-emerald-200 rounded-xl px-4 py-3 text-sm font-bold mt-1" required></div>
                    <div><label class="text-[10px] font-black uppercase text-gray-500">Tahun Data *</label><input v-model="form.tahun" type="number" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold mt-1" required></div>
                    <div class="col-span-2"><label class="text-[10px] font-black uppercase text-gray-500">Satuan (Contoh: %, Jiwa)</label><input v-model="form.satuan" type="text" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold mt-1"></div>
                    <div class="col-span-2"><label class="text-[10px] font-black uppercase text-gray-500">Deskripsi</label><textarea v-model="form.deskripsi" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm mt-1 min-h-[80px]"></textarea></div>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-xl border p-10">
                <h3 class="text-lg font-black text-[#000B58] mb-6 uppercase border-b pb-4">2. Klasifikasi Metadata</h3>
                <div class="grid grid-cols-2 gap-6">
                    <div><label class="text-[10px] font-black uppercase text-gray-500">Tema Sektoral *</label><select v-model="form.id_tema" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold mt-1"><option v-for="t in tema" :value="t.id_tema">{{ t.nama_tema }}</option></select></div>
                    <div><label class="text-[10px] font-black uppercase text-gray-500">Urusan Pemerintahan *</label><select v-model="form.id_urusan" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold mt-1"><option v-for="u in urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option></select></div>
                    <div><label class="text-[10px] font-black uppercase text-gray-500">Bidang / Instansi *</label><select v-model="form.id_bidang" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold mt-1"><option v-for="b in bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option></select></div>
                    <div><label class="text-[10px] font-black uppercase text-gray-500">Frekuensi Pelaporan</label><select v-model="form.id_frekuensi" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold mt-1"><option v-for="f in frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option></select></div>
                </div>
            </div>

            <div class="flex justify-end pt-4 pb-20">
                <button @click="submitData" :disabled="isLoading" class="bg-[#000B58] text-white px-16 py-4 rounded-xl font-black text-sm uppercase tracking-widest hover:bg-blue-900 shadow-xl disabled:opacity-50">{{ isLoading ? 'Menyimpan...' : 'Simpan Indikator' }}</button>
            </div>
        </div>
    </div>
</template>