<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

// Props dari Controller (Data Asli dari Database)
const props = defineProps({
    dataset: Object, // Objek data metadata
    tableData: Array // Data baris excel
});

// State untuk Tab (Analisis sudah dihapus)
const activeTab = ref('Data');
const tabs = ['Data', 'Metadata']; 

// Helper format tanggal
const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric'
    });
};
</script>

<template>
    <Head :title="dataset.nama_indikator" />

    <div class="min-h-screen bg-gray-50 font-sans pb-20">
        
        <div class="bg-[#000B58] text-white py-12 px-4 md:px-8 shadow-xl relative overflow-hidden">
            <div class="max-w-7xl mx-auto relative z-10">
                <div class="flex items-center gap-2 text-xs font-bold text-blue-300 uppercase tracking-wider mb-6">
                    <Link href="/" class="hover:text-white">Beranda</Link> / 
                    <span class="text-white">Detail Data</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold mb-4">{{ dataset.nama_indikator }}</h1>
                
                <div class="flex items-center gap-6 text-sm text-blue-100 font-medium">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-white/10 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                        {{ dataset.sumber || 'Pemerintah Provinsi' }}
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-white/10 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        {{ dataset.tahun_data || new Date().getFullYear() }}
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
                            <h3 class="font-bold text-gray-700">Preview Data</h3>
                            <button class="bg-[#000B58] text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-900 transition">Download Excel</button>
                        </div>
                        
                        <div v-if="tableData.length === 0" class="p-10 text-center text-gray-400 italic">
                            Belum ada data excel yang diupload untuk indikator ini.
                        </div>
                        <div v-else class="overflow-auto max-h-[500px]">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th v-for="(val, key) in tableData[0]" :key="key" class="p-3 font-bold uppercase text-gray-500 border-b">{{ key }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, idx) in tableData" :key="idx" class="hover:bg-blue-50 border-b">
                                        <td v-for="(val, key) in row" :key="key" class="p-3 text-gray-700">{{ val }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="activeTab === 'Metadata'" class="bg-white rounded-b-2xl rounded-tr-2xl shadow-sm border border-gray-100 p-8 animate-in fade-in slide-in-from-bottom-2 duration-300">
                        
                        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Informasi Rinci Metadata
                        </h2>

                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            <table class="w-full text-sm text-left">
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="bg-gray-50/50">
                                        <td class="px-6 py-4 font-bold text-gray-500 w-1/3">Nama Indikator</td>
                                        <td class="px-6 py-4 font-bold text-gray-900">{{ dataset.nama_indikator }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 font-bold text-gray-500 align-top">Deskripsi</td>
                                        <td class="px-6 py-4 text-gray-700 leading-relaxed">{{ dataset.deskripsi || '-' }}</td>
                                    </tr>
                                    
                                    <tr class="bg-gray-50/50">
                                        <td class="px-6 py-4 font-bold text-gray-500">Tema</td>
                                        <td class="px-6 py-4 text-gray-900">
                                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">
                                                {{ dataset.tema?.nama_tema || '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 font-bold text-gray-500">Urusan Pemerintahan</td>
                                        <td class="px-6 py-4 text-gray-900">{{ dataset.urusan?.nama_urusan || '-' }}</td>
                                    </tr>
                                    <tr class="bg-gray-50/50">
                                        <td class="px-6 py-4 font-bold text-gray-500">Bidang</td>
                                        <td class="px-6 py-4 text-gray-900">{{ dataset.bidang?.nama_bidang || '-' }}</td>
                                    </tr>

                                    <tr>
                                        <td class="px-6 py-4 font-bold text-gray-500">Frekuensi Data</td>
                                        <td class="px-6 py-4 text-gray-900">{{ dataset.frekuensi?.nama_frekuensi || '-' }}</td>
                                    </tr>
                                    <tr class="bg-gray-50/50">
                                        <td class="px-6 py-4 font-bold text-gray-500">Satuan</td>
                                        <td class="px-6 py-4 text-gray-900 font-mono">{{ dataset.satuan || '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 font-bold text-gray-500">Sumber Data</td>
                                        <td class="px-6 py-4 text-gray-900 font-bold">{{ dataset.sumber || '-' }}</td>
                                    </tr>

                                    <tr class="bg-gray-50/50">
                                        <td class="px-6 py-4 font-bold text-gray-500">Periode Data</td>
                                        <td class="px-6 py-4 text-gray-900">{{ dataset.tahun_data }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 font-bold text-gray-500">Terakhir Diupdate</td>
                                        <td class="px-6 py-4 text-gray-900">{{ formatDate(dataset.updated_at) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <div class="lg:w-1/4 space-y-6 mt-14">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Dataset ID</h3>
                        <p class="font-mono text-xs bg-gray-100 p-2 rounded text-gray-600 break-all">ID-{{ dataset.id_data }}</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>