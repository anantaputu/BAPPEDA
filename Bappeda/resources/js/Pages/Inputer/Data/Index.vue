<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

// 1. PROPS DEFINITION
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({ total_upload: 0, valid: 0, pending: 0 })
    },
    recentUploads: {
        type: Array,
        default: () => []
    },
    isAdmin: {
        type: Boolean,
        default: false
    },
    resumeData: {
        type: Object,
        default: () => ({})
    }
});

// Helper Function untuk Warna Status yang konsisten dengan standar kita
const getStatusClass = (status) => {
    if (status === 'valid') return 'bg-emerald-50 text-emerald-600 border-emerald-100';
    if (status === 'processing' || status === 'pending') return 'bg-amber-50 text-amber-600 border-amber-100';
    return 'bg-rose-50 text-rose-600 border-rose-100';
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};
</script>

<template>
    <Head title="Dashboard Inputer" />

    <div class="py-8 px-4 sm:px-6 lg:px-8 space-y-8 max-w-[1600px] mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h2 class="text-4xl font-black text-gray-900 tracking-tight">Dashboard <span class="text-[#00139E]">Data</span></h2>
                <p class="text-gray-400 font-medium text-sm mt-1 uppercase tracking-widest text-[10px]">Ringkasan aktivitas input data sistem BAPPEDA</p>
            </div>
           <div class="flex flex-col sm:flex-row gap-4">
                <Link href="/inputer/data/input-single" class="bg-white text-[#00139E] border-2 border-[#00139E] px-6 py-3.5 rounded-2xl font-black text-xs tracking-widest uppercase hover:bg-blue-50 transition-all shadow-lg flex items-center justify-center gap-2 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Input Single Data
                </Link>

                <Link href="/inputer/data/input-multi" class="bg-[#00139E] text-white px-6 py-3.5 rounded-2xl font-black text-xs tracking-widest uppercase hover:bg-[#000B58] transition-all shadow-xl shadow-blue-500/20 flex items-center justify-center gap-2 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" /></svg>
                    Upload Multi (Excel)
                </Link>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-400 shadow-2xl shadow-gray-100/50 flex items-center gap-6 group hover:-translate-y-1 transition-all">
                <div class="w-16 h-16 rounded-3xl bg-blue-50 text-[#00139E] flex items-center justify-center transition-transform group-hover:scale-110">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Upload</p>
                    <p class="text-3xl font-black text-gray-900 leading-none">{{ stats.total_upload }} <span class="text-xs font-bold text-gray-300">Dataset</span></p>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-400 shadow-2xl shadow-gray-100/50 flex items-center gap-6 group hover:-translate-y-1 transition-all">
                <div class="w-16 h-16 rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center transition-transform group-hover:scale-110">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Data Valid</p>
                    <p class="text-3xl font-black text-gray-900 leading-none">{{ stats.valid }} <span class="text-xs font-bold text-gray-300">Dataset</span></p>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-400 shadow-2xl shadow-gray-100/50 flex items-center gap-6 group hover:-translate-y-1 transition-all">
                <div class="w-16 h-16 rounded-3xl bg-amber-50 text-amber-600 flex items-center justify-center transition-transform group-hover:scale-110">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Review / Proses</p>
                    <p class="text-3xl font-black text-gray-900 leading-none">{{ stats.pending }} <span class="text-xs font-bold text-gray-300">Dataset</span></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-100 border border-gray-400 overflow-hidden">
            <div class="p-8 border-b border-gray-400 bg-gray-50/50">
                <h3 class="text-xl font-black text-gray-900 tracking-tight uppercase">Riwayat Upload Terakhir</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-gray-400">
                            <th class="py-6 px-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] w-1/3">Nama Indikator Data</th>
                            <th v-if="isAdmin" class="py-6 px-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Oleh</th>
                            <th class="py-6 px-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Periode</th>
                            <th class="py-6 px-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Tanggal</th>
                            <th class="py-6 px-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Status</th>
                            <th class="py-6 px-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-400">
                        <tr v-for="u in recentUploads" :key="u.id_upload" class="hover:bg-blue-50/20 transition-all group">
                             <td class="py-4 pl-4 transition-colors">
                                <div v-if="u.data">
                                    <Link 
                                        :href="`/dataset/${u.data.id_data}`"
                                        class="font-bold text-gray-700 group-hover:text-blue-600 hover:underline block"
                                        title="Lihat Detail Dataset"
                                    >
                                        {{ u.data.nama_indikator }}
                                    </Link>
                                    <span class="text-[10px] text-gray-300">ID: {{ u.data.id_data }}</span>
                                </div>

                                <div v-else class="flex flex-col">
                                    <span class="text-red-500 italic text-xs font-bold">
                                        Relasi Data Putus (NULL)
                                    </span>
                                    <span class="text-[10px] text-gray-400">
                                        Upload ID: {{ u.id_upload }} | id_data: {{ u.id_data }}
                                    </span>
                                </div>
                            </td>           
                            
                            <td class="py-6 px-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#00139E] flex items-center justify-center text-[10px] font-black border border-blue-100">
                                        {{ u.user?.name?.charAt(0).toUpperCase() || '?' }}
                                    </div>
                                    <span class="text-[10px] font-bold text-gray-600 uppercase tracking-tight">
                                        {{ u.user?.name || 'Unknown' }}
                                    </span>
                                </div>
                            </td>

                            <td class="py-6 px-4 text-center">
                                <span class="bg-gray-100 text-gray-600 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border border-gray-200">
                                    {{ u.periode }}
                                </span>
                            </td>
                            <td class="py-6 px-4 text-center text-[10px] font-bold text-gray-400 uppercase">
                                {{ formatDate(u.created_at) }}
                            </td>
                            <td class="py-6 px-4 text-center">
                                <span :class="getStatusClass(u.status)" class="text-[9px] font-black uppercase px-4 py-1.5 rounded-xl border tracking-widest">
                                    {{ u.status }}
                                </span>
                            </td>
                            <td class="py-4 px-8 text-right">

                                <a 
                                    :href="`/inputer/export/${u.id_upload}`" 
                                    target="_blank" 
                                    class="text-blue-600 hover:underline text-xs font-bold flex items-center justify-end gap-1 transition-colors"
                                    title="Download Data"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Download
                                </a>

                                <Link 
                                    :href="`/inputer/data/${u.data?.id_data}/edit`" 
                                    class="text-amber-600 hover:underline text-xs font-bold flex items-center justify-end gap-1 transition-colors"
                                    title="Edit Metadata"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    Edit Data
                                </Link>

                            </td>
                        </tr>

                        <tr v-if="recentUploads.length === 0">
                            <td :colspan="isAdmin ? 6 : 5" class="py-24 text-center">
                                <div class="flex flex-col items-center opacity-20">
                                    <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                    <p class="font-black uppercase tracking-[0.2em] text-xs">Belum ada riwayat upload data</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="h-12"></div>
    </div>
</template>