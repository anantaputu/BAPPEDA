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
    // Gunakan method PUT untuk update data ke route inputer
    form.put(`/inputer/data/${props.dataIndikator.id_data}`, {
        onSuccess: () => {
            // Biasanya redirect ditangani controller, tapi bisa ditambah alert di sini jika mau
        }
    });
};
</script>

<template>
    <Head title="Edit Master Data" />

    <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 mb-8">
            <Link href="/inputer/dashboard" class="p-2 bg-white rounded-xl shadow-sm hover:text-blue-600 transition border border-gray-100">
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="col-span-1 md:col-span-2 space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Nama Data / Indikator</label>
                            <input v-model="form.nama_indikator" type="text" 
                                class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition shadow-sm outline-none" />
                            <p v-if="form.errors.nama_indikator" class="text-red-500 text-xs mt-1 ml-1">{{ form.errors.nama_indikator }}</p>
                        </div>

                        <div class="col-span-1 md:col-span-2 space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Deskripsi</label>
                            <textarea v-model="form.deskripsi" rows="3"
                                class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition shadow-sm outline-none"></textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Tema</label>
                            <select v-model="form.id_tema" class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500 shadow-sm outline-none">
                                <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Urusan</label>
                            <select v-model="form.id_urusan" class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500 shadow-sm outline-none">
                                <option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Bidang</label>
                            <select v-model="form.id_bidang" class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500 shadow-sm outline-none">
                                <option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Frekuensi</label>
                            <select v-model="form.id_frekuensi" class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500 shadow-sm outline-none">
                                <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Satuan</label>
                            <input v-model="form.satuan" type="text" class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 shadow-sm outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Jiwa, Persen, Km" />
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Sumber Data</label>
                            <input v-model="form.sumber" type="text" class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 shadow-sm outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: BPS, Dinas Kesehatan" />
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Kata Kunci</label>
                            <input v-model="form.kata_kunci" type="text" class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 shadow-sm outline-none focus:ring-2 focus:ring-blue-500" placeholder="Pisahkan dengan koma" />
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Status Data</label>
                            <select v-model="form.status" class="w-full border-gray-100 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500 shadow-sm outline-none">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Non-Aktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-10 flex flex-col sm:flex-row gap-4">
                        <button @click="submit" :disabled="form.processing"
                            class="flex-1 bg-[#4A6CF7] text-white px-8 py-4 rounded-2xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-100 disabled:opacity-50 flex items-center justify-center gap-2">
                            <svg v-if="!form.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                            <svg v-else class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ form.processing ? 'Menyimpan...' : 'SIMPAN PERUBAHAN' }}
                        </button>
                        
                        <Link href="/inputer/dashboard" class="px-8 py-4 rounded-2xl font-bold text-gray-400 hover:bg-gray-50 transition text-center border border-gray-100">
                            BATAL
                        </Link>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-blue-50 p-8 rounded-[2rem] border border-blue-100 shadow-sm">
                    <h3 class="font-bold text-blue-800 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Riwayat Perubahan
                    </h3>
                    <p class="text-[10px] text-blue-600 font-medium leading-relaxed">
                        Terakhir diperbarui pada:<br />
                        <span class="font-black text-sm">{{ new Date(dataIndikator.updated_at).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</span><br/>
                        <span class="font-bold text-xs opacity-70">Pukul {{ new Date(dataIndikator.updated_at).toLocaleTimeString('id-ID') }}</span>
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Tips Inputer
                    </p>
                    <p class="text-xs text-gray-600 font-medium leading-relaxed mb-3">
                        Jika Anda mengubah **Satuan** (misal dari "Jiwa" ke "Ribuan Jiwa"), pastikan data angka yang sudah di-input sebelumnya masih relevan.
                    </p>
                    <p class="text-xs text-gray-600 font-medium leading-relaxed">
                        Perubahan pada **Tema** atau **Urusan** akan mempengaruhi di mana data ini muncul pada grafik Dashboard Publik.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>