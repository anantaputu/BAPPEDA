<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    dataset: Object,
    tableData: Object 
});

const activeTab = ref('Data');
const tabs = ['Data', 'Metadata'];

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric'
    });
};
</script>

<template>
    <Head :title="dataset.nama_indikator || 'Detail Data'" />

    <div class="min-h-screen bg-white font-sans pb-32 mt-20">
        <section class="relative py-20 overflow-hidden w-full"> 
            <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[600px] h-[600px] bg-[#A2B5CB]/10 rounded-full blur-3xl -z-10"></div>
            
            <div class="max-w-[80%] mx-auto">
                <div class="flex items-center gap-2 text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] mb-8">
                    <Link href="/" class="hover:text-[#00139E] transition-colors">Beranda</Link> 
                    <span class="text-gray-300">/</span>
                    <Link href="/cari" class="hover:text-[#00139E] transition-colors">Katalog Data</Link>
                    <span class="text-gray-300">/</span>
                    <span class="text-[#000B58]">Detail Indikator</span>
                </div>

                <div class="grid lg:grid-cols-3 gap-16 items-start">
                    <div class="lg:col-span-2">
                        <span class="inline-block px-4 py-1.5 mb-6 text-sm font-bold text-[#00139E] bg-[#A2B5CB]/20 rounded-full border border-[#A2B5CB]/30 tracking-wide uppercase">
                            {{ dataset.tema?.nama_tema || 'Indikator Pembangunan' }}
                        </span>
                        <h1 class="text-4xl lg:text-6xl font-black text-[#000B58] leading-[1.2] mb-8">
                            {{ dataset.nama_indikator }}
                        </h1>
                        <p class="text-lg text-gray-500 leading-relaxed font-medium max-w-2xl">
                            {{ dataset.deskripsi || 'Tidak ada deskripsi rinci untuk dataset ini.' }}
                        </p>
                    </div>

                    <div class="bg-white border border-gray-400 p-8 rounded-[2.5rem] shadow-2xl shadow-[#000B58]/5">
                        <div class="space-y-6">
                            <div class="flex items-center gap-4 border-b border-gray-100 pb-4">
                                <div class="w-10 h-10 bg-[#00139E]/10 rounded-xl flex items-center justify-center text-[#00139E]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-[#A2B5CB] uppercase tracking-widest font-black">Tahun Data</p>
                                    <p class="font-black text-[#000B58]">{{ dataset.tahun }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-[#FF1414]/10 rounded-xl flex items-center justify-center text-[#FF1414]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-[#A2B5CB] uppercase tracking-widest font-black">Instansi Sumber</p>
                                    <p class="font-black text-[#000B58] text-sm leading-tight">{{ dataset.urusan?.nama_urusan || dataset.sumber }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="max-w-[80%] mx-auto">
            <div class="flex gap-4 mb-10 bg-gray-50 p-2 rounded-[2rem] border border-gray-400 w-fit">
                <button v-for="tab in tabs" :key="tab" 
                    @click="activeTab = tab"
                    class="px-10 py-3 rounded-[1.5rem] text-sm font-black transition-all duration-300"
                    :class="activeTab === tab ? 'bg-[#000B58] text-white shadow-lg' : 'text-[#A2B5CB] hover:text-[#000B58]'">
                    {{ tab }}
                </button>
            </div>

            <div v-if="activeTab === 'Data'" class="animate-in fade-in duration-500">
                <div class="bg-white border border-gray-400 rounded-[3rem] overflow-hidden">
                    <div class="p-10 border-b border-gray-400 flex flex-col md:flex-row justify-between items-center gap-6">
                        <div>
                            <h3 class="text-2xl font-black text-[#000B58]">Preview Dataset</h3>
                            <p class="text-sm font-medium text-gray-400">Menampilkan {{ tableData.data?.length || 0 }} baris data terbaru</p>
                        </div>
                        <a v-if="tableData.total > 0" :href="`/export/data/${dataset.id_data}`" 
                           class="bg-[#00139E] text-white px-8 py-4 rounded-2xl text-sm font-black hover:bg-[#000B58] hover:-translate-y-1 transition-all duration-300 flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Export to Excel (.xlsx)
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-8 py-6 text-[10px] font-black text-[#A2B5CB] uppercase tracking-widest border-b border-gray-400 text-center w-20">#</th>
                                    <th v-for="(val, key) in tableData.data[0]" :key="key" 
                                        class="px-8 py-6 text-[10px] font-black text-[#A2B5CB] uppercase tracking-widest border-b border-gray-400">
                                        {{ key }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="(row, idx) in tableData.data" :key="idx" class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-5 font-black text-[#00139E] text-center text-sm italic">{{ (tableData.from || 1) + idx }}</td>
                                    <td v-for="(val, key) in row" :key="key" class="px-8 py-5 text-sm font-bold text-[#000B58]">
                                        {{ val }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-8 border-t border-gray-400 bg-gray-50/30 flex justify-between items-center">
                        <p class="text-xs font-black text-[#A2B5CB] uppercase tracking-widest">
                            Halaman {{ tableData.current_page }} dari {{ tableData.last_page }}
                        </p>
                        <div class="flex gap-3">
                            <Link v-if="tableData.prev_page_url" :href="tableData.prev_page_url" class="p-3 border border-gray-400 rounded-xl hover:bg-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#000B58]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                            </Link>
                            <Link v-if="tableData.next_page_url" :href="tableData.next_page_url" class="p-3 border border-gray-400 rounded-xl hover:bg-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#000B58]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'Metadata'" class="animate-in slide-in-from-bottom-4 duration-500">
                <div class="grid lg:grid-cols-2 gap-8">
                    
                    <div class="space-y-8">
                        <div class="bg-white border border-gray-200 p-10 rounded-[2.5rem] shadow-sm">
                            <h4 class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                Klasifikasi Data
                            </h4>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 hover:bg-gray-50/50 transition px-2 rounded-lg">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Urusan</span>
                                    <span class="text-sm font-black text-[#000B58] text-right">{{ dataset.urusan?.nama_urusan || '-' }}</span>
                                </div>

                                <div class="flex justify-between items-center py-3 border-b border-gray-50 hover:bg-gray-50/50 transition px-2 rounded-lg">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Bidang</span>
                                    <span class="text-sm font-black text-[#000B58] text-right">{{ dataset.bidang?.nama_bidang || '-' }}</span>
                                </div>

                                <div class="flex justify-between items-center py-3 border-b border-gray-50 hover:bg-gray-50/50 transition px-2 rounded-lg">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tema</span>
                                    <span class="text-sm font-black text-[#000B58] text-right">{{ dataset.tema?.nama_tema || '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 p-10 rounded-[2.5rem] shadow-sm">
                            <h4 class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                Atribut Teknis
                            </h4>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Satuan</span>
                                    <span class="text-sm font-black text-[#000B58] bg-blue-50 px-3 py-1 rounded-lg">{{ dataset.satuan || '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Frekuensi</span>
                                    <span class="text-sm font-black text-[#000B58]">{{ dataset.frekuensi?.nama_frekuensi || 'Tahunan' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tahun Data</span>
                                    <span class="text-sm font-black text-[#000B58]">{{ dataset.tahun || '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-50 px-2">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status Data</span>
                                    <span :class="dataset.status === 'aktif' ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50'" class="px-3 py-1 rounded-full text-[10px] font-black uppercase">
                                        {{ dataset.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="bg-white border border-gray-200 p-10 rounded-[2.5rem] shadow-sm h-full">
                            
                            <div class="mb-10">
                                <h4 class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
                                    Deskripsi Lengkap
                                </h4>
                                <p class="text-gray-600 text-sm leading-loose font-medium text-justify">
                                    {{ dataset.deskripsi || 'Tidak ada deskripsi yang tersedia untuk dataset ini.' }}
                                </p>
                            </div>

                            <div class="mb-10">
                                <h4 class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    Sumber Data
                                </h4>
                                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm text-blue-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                                    </div>
                                    <span class="text-gray-900 font-bold text-sm">{{ dataset.sumber || 'Pemerintah Provinsi' }}</span>
                                </div>
                            </div>

                            <div class="mb-10">
                                <h4 class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                                    Kata Kunci
                                </h4>
                                <div class="flex flex-wrap gap-2">
                                    <template v-if="dataset.kata_kunci">
                                        <span v-for="(tag, i) in dataset.kata_kunci.split(',')" :key="i" 
                                            class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold border border-blue-100 hover:bg-blue-100 transition cursor-default">
                                            #{{ tag.trim() }}
                                        </span>
                                    </template>
                                    <span v-else class="text-gray-400 text-xs italic">Tidak ada kata kunci.</span>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Timeline Sistem
                                </h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-3 rounded-xl">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">Dibuat</p>
                                        <p class="text-xs font-black text-gray-700 mt-1">{{ formatDate(dataset.created_at) }}</p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-xl">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">Update Terakhir</p>
                                        <p class="text-xs font-black text-gray-700 mt-1">{{ formatDate(dataset.updated_at) }}</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>