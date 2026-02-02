<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Head, Link } from '@inertiajs/vue3';

// Set Layout secara persisten
defineOptions({ layout: AppLayout });

const props = defineProps({
    dataIndikator: Object, // Data yang akan diedit
    tema: Array,
    urusan: Array,
    bidang: Array,
    frekuensi: Array,
});

// Inisialisasi form dengan data yang sudah ada
const form = useForm({
    nama_indikator: props.dataIndikator.nama_indikator,
    deskripsi: props.dataIndikator.deskripsi,
    id_tema: props.dataIndikator.id_tema,
    id_urusan: props.dataIndikator.id_urusan,
    id_bidang: props.dataIndikator.id_bidang,
    id_frekuensi: props.dataIndikator.id_frekuensi,
    kata_kunci: props.dataIndikator.kata_kunci,
    satuan: props.dataIndikator.satuan,
    sumber: props.dataIndikator.sumber,
    status: props.dataIndikator.status
});

const submit = () => {
    // Gunakan method PUT untuk update data
    form.put(`/data/${props.dataIndikator.id_data}`, {
        onSuccess: () => {
            // Logika tambahan jika diperlukan setelah sukses
        }
    });
};
</script>

<template>
    <Head title="Edit Master Data" />

    <div class="max-w-5xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <Link href="/data" class="p-2 bg-white rounded-xl shadow-sm hover:text-blue-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </Link>
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Edit Master Data</h1>
                <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mt-1">ID Data: #{{ dataIndikator.id_data }}</p>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-2 space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Nama Data / Indikator</label>
                            <input v-model="form.nama_indikator" type="text" 
                                class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition shadow-sm" />
                            <p v-if="form.errors.nama_indikator" class="text-red-500 text-xs mt-1">{{ form.errors.nama_indikator }}</p>
                        </div>

                        <div class="col-span-2 space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Deskripsi</label>
                            <textarea v-model="form.deskripsi" rows="3"
                                class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition shadow-sm"></textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Tema</label>
                            <select v-model="form.id_tema" class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500 shadow-sm">
                                <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Urusan</label>
                            <select v-model="form.id_urusan" class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500 shadow-sm">
                                <option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Bidang</label>
                            <select v-model="form.id_bidang" class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500 shadow-sm">
                                <option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Frekuensi</label>
                            <select v-model="form.id_frekuensi" class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500 shadow-sm">
                                <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Satuan</label>
                            <input v-model="form.satuan" type="text" class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 shadow-sm" />
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Status Data</label>
                            <select v-model="form.status" class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500 shadow-sm">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Non-Aktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-10 flex gap-4">
                        <button @click="submit" :disabled="form.processing"
                            class="flex-1 bg-[#4A6CF7] text-white px-8 py-4 rounded-2xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-100 disabled:opacity-50">
                            {{ form.processing ? 'Menyimpan Perubahan...' : 'UPDATE DATA' }}
                        </button>
                        <Link href="/data" class="px-8 py-4 rounded-2xl font-bold text-gray-400 hover:bg-gray-50 transition text-center border border-gray-100">
                            BATAL
                        </Link>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-blue-50 p-8 rounded-[2rem] border border-blue-100 shadow-sm">
                    <h3 class="font-bold text-blue-800 mb-2">Riwayat Perubahan</h3>
                    <p class="text-[10px] text-blue-600 font-medium leading-relaxed">
                        Terakhir diperbarui pada:<br />
                        <span class="font-black">{{ new Date(dataIndikator.updated_at).toLocaleString() }}</span>
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase mb-4">Tips</p>
                    <p class="text-xs text-gray-600 font-medium leading-relaxed">
                        Jika anda mengubah **Satuan**, pastikan data yang sudah di-input sebelumnya masih relevan atau lakukan penyesuaian pada kolom Excel.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>