<script setup>
import { Head, Link } from '@inertiajs/vue3'; // Link dibutuhkan untuk navigasi paginasi
import { ref } from 'vue';

// Props dari Controller
const props = defineProps({
    dataset: Object,
    // tableData sekarang adalah OBJECT Paginator (bukan Array biasa)
    // Isinya: { data: [...], current_page: 1, next_page_url: '...', total: 100, ... }
    tableData: Object 
});

const activeTab = ref('Data');
const tabs = ['Data', 'Metadata'];

// Helper Format Tanggal
const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric'
    });
};
</script>

<template>
    <Head :title="dataset.nama_indikator || 'Detail Data'" />

    <div class="min-h-screen bg-gray-50 font-sans pb-20">
        
        <div class="bg-[#000B58] text-white py-12 px-4 md:px-8 shadow-xl relative overflow-hidden">
            <div class="max-w-7xl mx-auto relative z-10">
                <div class="flex items-center gap-2 text-xs font-bold text-blue-300 uppercase tracking-wider mb-6">
                    <Link href="/" class="hover:text-white">Beranda</Link> / 
                    <span class="text-white">Detail Data</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold mb-4 leading-tight">
                    {{ dataset.nama_indikator }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-6 text-sm text-blue-100 font-medium">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-white/10 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                        {{ dataset.sumber || 'Sumber Tidak Diketahui' }}
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-white/10 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        {{ dataset.tahun_data || '-' }}
                    </div>
                </div>
            </div>
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-[#00139E]/50 rounded-full blur-3xl -mr-20 -mt-40 pointer-events-none"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 md:px-8 -mt-8 relative z-20">
            <div class="flex flex-col lg:flex-row gap-8">
                
                <div class="lg:w-3/4 space-y-8">
                    
                    <div class="flex gap-1 border-b border-gray-200 bg-white px-6 pt-4 rounded-t-2xl shadow-sm">
                        <button v-for="tab in tabs" :key="tab" 
                            @click="activeTab = tab"
                            class="px-6 py-3 text-sm font-bold border-b-2 transition-colors"
                            :class="activeTab === tab ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-800'">
                            {{ tab }}
                        </button>
                    </div>

                    <div v-if="activeTab === 'Data'" class="bg-white rounded-b-2xl rounded-tr-2xl shadow-sm border border-gray-100 overflow-hidden min-h-[400px]">
                        
                        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <h3 class="font-bold text-gray-700">
                                Preview Data 
                                <span v-if="tableData.total" class="ml-2 text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full">
                                    Total {{ tableData.total }} Baris
                                </span>
                            </h3>
                            <a 
    v-if="tableData.total > 0"  
    :href="`/export/data/${dataset.id_data}`"  
    target="_blank"
    class="bg-[#000B58] text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-900 transition flex items-center gap-2 shadow-lg shadow-blue-900/20 cursor-pointer"
>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12"/>
    </svg>
    Download Excel
</a>
                        </div>

                        <div class="px-8 py-6 bg-blue-50/30 border-b border-blue-100">
                            <h4 class="text-xs font-bold text-blue-800 uppercase tracking-widest mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Deskripsi Data
                            </h4>
                            <p class="text-sm text-gray-700 leading-relaxed">
                                {{ dataset.deskripsi || 'Tidak ada deskripsi rinci untuk dataset ini.' }}
                            </p>
                        </div>
                        
                        <div v-if="!tableData.data || tableData.data.length === 0" class="flex flex-col items-center justify-center h-64 text-gray-400">
                            <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-sm font-medium">Belum ada data valid untuk indikator ini.</p>
                        </div>

                        <div v-else class="overflow-auto max-h-[800px]">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="bg-gray-100 sticky top-0 shadow-sm z-10">
                                    <tr>
                                        <th class="p-4 font-black uppercase text-gray-500 border-b border-gray-200 w-12 text-center bg-gray-100">#</th>
                                        <th v-for="(val, key) in tableData.data[0]" :key="key" 
                                            class="p-4 font-black uppercase text-gray-600 border-b border-gray-200 whitespace-nowrap bg-gray-100">
                                            {{ key }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr v-for="(row, idx) in tableData.data" :key="idx" class="hover:bg-blue-50/50 transition">
                                        <td class="p-4 font-bold text-gray-400 text-center border-r border-gray-50">
                                            {{ (tableData.from || 1) + idx }}
                                        </td>
                                        <td v-for="(val, key) in row" :key="key" class="p-4 text-gray-700 whitespace-nowrap border-r border-gray-50 last:border-0">
                                            {{ val }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div v-if="tableData.total > 0" class="p-4 bg-gray-50 border-t border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
                            
                            <span class="text-xs text-gray-500 font-medium">
                                Menampilkan <span class="font-bold text-gray-800">{{ tableData.from }} - {{ tableData.to }}</span> dari <span class="font-bold text-gray-800">{{ tableData.total }}</span> data
                            </span>

                            <div class="flex items-center gap-2">
                                <Link 
                                    v-if="tableData.prev_page_url" 
                                    :href="tableData.prev_page_url" 
                                    preserve-scroll
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border bg-white hover:bg-gray-100 transition text-gray-600 font-bold">
                                    &laquo;
                                </Link>
                                <button v-else disabled class="w-8 h-8 flex items-center justify-center rounded-lg border bg-gray-50 text-gray-300 cursor-not-allowed">
                                    &laquo;
                                </button>
                                
                                <span class="text-xs font-bold text-blue-800 bg-blue-100 px-3 py-1.5 rounded-lg border border-blue-200">
                                    Halaman {{ tableData.current_page }}
                                </span>

                                <Link 
                                    v-if="tableData.next_page_url" 
                                    :href="tableData.next_page_url" 
                                    preserve-scroll
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border bg-white hover:bg-gray-100 transition text-gray-600 font-bold">
                                    &raquo;
                                </Link>
                                <button v-else disabled class="w-8 h-8 flex items-center justify-center rounded-lg border bg-gray-50 text-gray-300 cursor-not-allowed">
                                    &raquo;
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeTab === 'Metadata'" class="bg-white rounded-b-2xl rounded-tr-2xl shadow-sm border border-gray-100 p-8 animate-in fade-in slide-in-from-bottom-2 duration-300">
                        <h2 class="text-lg font-bold text-gray-900 mb-8 flex items-center gap-3 pb-4 border-b border-gray-100">
                            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            Informasi Rinci Metadata
                        </h2>

                        <div class="border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                            <table class="w-full text-sm text-left">
                                <tbody class="divide-y divide-gray-100">
                                    <tr class="bg-gray-50/50 hover:bg-white transition"><td class="px-6 py-5 font-bold text-gray-500 w-1/3 align-top uppercase text-xs tracking-wider">Nama Indikator</td><td class="px-6 py-5 font-bold text-gray-900 text-base leading-relaxed">{{ dataset.nama_indikator }}</td></tr>
                                    <tr class="hover:bg-gray-50 transition"><td class="px-6 py-5 font-bold text-gray-500 align-top uppercase text-xs tracking-wider">Deskripsi</td><td class="px-6 py-5 text-gray-700 leading-relaxed">{{ dataset.deskripsi || '-' }}</td></tr>
                                    <tr class="bg-gray-50/50 hover:bg-white transition"><td class="px-6 py-5 font-bold text-gray-500 align-top uppercase text-xs tracking-wider">Tema</td><td class="px-6 py-5 text-gray-900"><span class="bg-blue-100 text-blue-700 px-4 py-1.5 rounded-full text-xs font-bold inline-block shadow-sm">{{ dataset.tema?.nama_tema || '-' }}</span></td></tr>
                                    <tr class="hover:bg-gray-50 transition"><td class="px-6 py-5 font-bold text-gray-500 align-top uppercase text-xs tracking-wider">Urusan</td><td class="px-6 py-5 text-gray-900 font-medium">{{ dataset.urusan?.nama_urusan || '-' }}</td></tr>
                                    <tr class="bg-gray-50/50 hover:bg-white transition"><td class="px-6 py-5 font-bold text-gray-500 align-top uppercase text-xs tracking-wider">Bidang</td><td class="px-6 py-5 text-gray-900 font-medium">{{ dataset.bidang?.nama_bidang || '-' }}</td></tr>
                                    <tr class="hover:bg-gray-50 transition"><td class="px-6 py-5 font-bold text-gray-500 align-top uppercase text-xs tracking-wider">Frekuensi</td><td class="px-6 py-5 text-gray-900 font-medium">{{ dataset.frekuensi?.nama_frekuensi || '-' }}</td></tr>
                                    <tr class="bg-gray-50/50 hover:bg-white transition"><td class="px-6 py-5 font-bold text-gray-500 align-top uppercase text-xs tracking-wider">Satuan</td><td class="px-6 py-5 text-gray-900 font-mono font-bold text-blue-600">{{ dataset.satuan || '-' }}</td></tr>
                                    <tr class="hover:bg-gray-50 transition"><td class="px-6 py-5 font-bold text-gray-500 align-top uppercase text-xs tracking-wider">Sumber Data</td><td class="px-6 py-5 text-gray-900 font-bold">{{ dataset.sumber || '-' }}</td></tr>
                                    <tr class="bg-gray-50/50 hover:bg-white transition"><td class="px-6 py-5 font-bold text-gray-500 align-top uppercase text-xs tracking-wider">Periode Data</td><td class="px-6 py-5 text-gray-900 font-bold">{{ dataset.tahun_data }}</td></tr>
                                    <tr class="hover:bg-gray-50 transition"><td class="px-6 py-5 font-bold text-gray-500 align-top uppercase text-xs tracking-wider">Update Terakhir</td><td class="px-6 py-5 text-gray-900 font-medium">{{ formatDate(dataset.updated_at) }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <div class="lg:w-1/4 space-y-6 mt-14">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Dataset ID</h3>
                        <p class="font-mono text-xs bg-gray-100 p-3 rounded-lg text-gray-600 break-all border border-gray-200">ID-{{ dataset.id_data }}</p>
                        
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Update</h3>
                            <p class="text-sm font-bold text-gray-800">{{ formatDate(dataset.updated_at) }}</p>
                        </div>
                        
                        <div class="mt-4">
                            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Upload</h3>
                            <p class="text-sm font-bold text-gray-800">{{ formatDate(dataset.created_at) }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>