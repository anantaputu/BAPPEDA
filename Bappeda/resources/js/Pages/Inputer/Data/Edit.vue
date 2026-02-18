<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Head, Link } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const props = defineProps({
    dataIndikator: Object,
    tema: Array,
    urusan: Array,
    bidang: Array,
    frekuensi: Array,
});

const form = useForm({
    nama_indikator: props.dataIndikator.nama_indikator,
    deskripsi: props.dataIndikator.deskripsi,
    id_tema: props.dataIndikator.id_tema,
    id_urusan: props.dataIndikator.id_urusan,
    id_bidang: props.dataIndikator.id_bidang,
    id_frekuensi: props.dataIndikator.id_frekuensi,
    satuan: props.dataIndikator.satuan,
    sumber: props.dataIndikator.sumber,
    status: props.dataIndikator.status
});

const submit = () => {
    form.put(`/inputer/data/${props.dataIndikator.id_data}`);
};
</script>

<template>
    <Head title="Edit Master Data" />

    <div class="min-h-screen mx-auto max-w-5xl">
        <div class="bg-white rounded-[3rem] border border-gray-400 p-12 md:p-16">
            
            <div class="mb-12">
                <h1 class="text-4xl font-black text-[#000B58] tracking-tight">
                    Edit <span class="text-[#00139E]">Master Data</span>
                </h1>
                <p class="text-gray-400 font-medium mt-2">Perbarui informasi indikator dan klasifikasi metadata sistem.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="md:col-span-2 space-y-3">
                        <label class="text-[11px] font-black uppercase tracking-[0.2em] text-[#00139E] ml-1">Nama Indikator / Data</label>
                        <input v-model="form.nama_indikator" type="text" 
                            class="w-full bg-gray-50 border-transparent rounded-2xl px-6 py-5 focus:ring-4 focus:ring-blue-100 focus:bg-white focus:border-[#00139E] transition-all font-bold text-[#000B58]"
                            :class="{ 'border-red-500': form.errors.nama_indikator }" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[11px] font-black uppercase tracking-[0.2em] text-[#00139E] ml-1">Satuan Data</label>
                        <input v-model="form.satuan" type="text" placeholder="Contoh: % atau Jiwa"
                            class="w-full bg-gray-50 border-transparent rounded-2xl px-6 py-5 focus:ring-4 focus:ring-blue-100 focus:bg-white focus:border-[#00139E] transition-all font-bold text-[#000B58]" />
                    </div>
                    <div class="space-y-3">
                        <label class="text-[11px] font-black uppercase tracking-[0.2em] text-[#00139E] ml-1">Sumber Data</label>
                        <input v-model="form.sumber" type="text" placeholder="Contoh: BPS NTB"
                            class="w-full bg-gray-50 border-transparent rounded-2xl px-6 py-5 focus:ring-4 focus:ring-blue-100 focus:bg-white focus:border-[#00139E] transition-all font-bold text-[#000B58]" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-8">
                        <div class="space-y-3">
                            <label class="text-[11px] font-black uppercase tracking-[0.2em] text-[#00139E] ml-1">Tema Sektoral</label>
                            <div class="relative">
                                <select v-model="form.id_tema" class="w-full bg-gray-50 border-transparent rounded-2xl px-6 py-5 focus:ring-4 focus:ring-blue-100 focus:bg-white focus:border-[#00139E] transition-all font-bold text-[#000B58] appearance-none cursor-pointer">
                                    <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                                </select>
                                <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[11px] font-black uppercase tracking-[0.2em] text-[#00139E] ml-1">Status Master</label>
                            <div class="relative">
                                <select v-model="form.status" class="w-full bg-gray-50 border-transparent rounded-2xl px-6 py-5 focus:ring-4 focus:ring-blue-100 focus:bg-white focus:border-[#00139E] transition-all font-bold text-[#000B58] appearance-none cursor-pointer">
                                    <option value="aktif">AKTIF</option>
                                    <option value="nonaktif">NON-AKTIF</option>
                                </select>
                                <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-end">
                        <div class="w-full bg-blue-50/50 border border-blue-100 rounded-3xl p-8 flex items-start gap-5 shadow-sm">
                            <div class="mt-1">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-[12px] text-blue-700 leading-relaxed font-medium">
                                Perubahan pada master data indikator ini akan berdampak langsung pada visualisasi grafik dan tabel di seluruh halaman publik NTB Satu Data.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex items-center justify-end gap-4 border-t border-gray-50">
                    <Link href="/inputer/dashboard" class="px-8 py-4 text-gray-400 font-bold hover:text-gray-600 transition-colors">
                        Batal
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="bg-[#000B58] text-white px-12 py-5 rounded-[1.5rem] text-sm font-black hover:bg-[#00139E] transition-all shadow-xl shadow-blue-900/20 active:scale-95 disabled:opacity-50">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan Data' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>