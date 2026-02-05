<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const props = defineProps({
    uploads: Array // Data dikirim dari Controller
});

// Helper untuk warna badge status
const getStatusClass = (status) => {
    if (status === 'valid') return 'bg-green-100 text-green-700 border border-green-200';
    if (status === 'processing') return 'bg-amber-100 text-amber-700 border border-amber-200';
    return 'bg-red-100 text-red-700 border border-red-200';
};
</script>

<template>
    <Head title="Riwayat Input Data" />

    <div class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
            
            <div class="p-10 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gray-50/30">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight">Riwayat Upload</h2>
                    <p class="text-gray-500 font-medium text-sm mt-1">Pantau status validasi dan mapping data Anda.</p>
                </div>
                
                <Link href="/inputer/wizard" class="bg-[#4A6CF7] text-white px-8 py-4 rounded-2xl font-black text-sm hover:bg-blue-700 transition shadow-lg shadow-blue-200 flex items-center gap-2 transform hover:-translate-y-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    INPUT DATA BARU
                </Link>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-gray-100">
                            <th class="py-6 px-8 text-[10px] font-black text-gray-400 uppercase tracking-widest w-1/3">Nama Data / Indikator</th>
                            <th class="py-6 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Periode</th>
                            <th class="py-6 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Tgl Upload</th>
                            <th class="py-6 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            <th class="py-6 px-8 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        
                        <tr v-for="u in uploads" :key="u.id_upload" class="hover:bg-blue-50/30 transition-colors group">
                            <td class="py-6 px-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-gray-900 text-sm mb-1">{{ u.data ? u.data.nama_indikator : 'Data Terhapus' }}</p>
                                        <p class="text-xs text-gray-400 font-medium">ID Upload: #{{ u.id_upload }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="py-6 px-4 text-center">
                                <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-lg text-xs font-black">{{ u.periode }}</span>
                            </td>

                            <td class="py-6 px-4 text-center">
                                <p class="text-xs font-bold text-gray-500">
                                    {{ new Date(u.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                                </p>
                                <p class="text-[10px] text-gray-400 mt-1">
                                    {{ new Date(u.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }}
                                </p>
                            </td>
                            
                            <td class="py-6 px-4 text-center">
                                <span :class="getStatusClass(u.status)" class="text-[10px] font-black uppercase px-4 py-2 rounded-xl shadow-sm">
                                    {{ u.status }}
                                </span>
                            </td>
                            
                            <td class="py-6 px-8 text-right">
    <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
        
        <Link v-if="u.status !== 'valid'" 
              :href="`/inputer/upload/${u.id_upload}/mapping`" 
              class="bg-[#4A6CF7] text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-blue-700 transition shadow-md flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            Lanjut Mapping
        </Link>

        <a v-if="u.status === 'valid'" 
           :href="`/inputer/export/${u.id_upload}`" 
           target="_blank"
           class="bg-green-100 border border-green-200 text-green-700 px-4 py-2 rounded-xl text-xs font-bold hover:bg-green-600 hover:text-white transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0l-4 4m4-4v12" /></svg>
            Download
        </a>

        <Link :href="`/inputer/edit/${u.id_upload}/edit`" 
              class="bg-white border border-gray-200 text-gray-500 px-4 py-2 rounded-xl text-xs font-bold hover:bg-gray-50 hover:text-gray-900 transition flex items-center gap-2">
            Edit
        </Link>

    </div>
</td>
                        </tr>

                        <tr v-if="uploads.length === 0">
                            <td colspan="5" class="py-20 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900">Belum ada data</h3>
                                    <p class="text-gray-500 text-sm mt-1 max-w-xs mx-auto">Mulai dengan menambahkan data indikator baru melalui tombol di atas.</p>
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>