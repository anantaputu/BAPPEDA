<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue'; 

// 1. Terima Data dengan Nilai Default yang Aman
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({ total_upload: 0, valid: 0, pending: 0 })
    },
    recentUploads: {
        type: Array,
        default: () => []
    },
});

// 2. Computed Property untuk "Membersihkan" Data sebelum ditampilkan
// Ini menjamin recentUploads selalu berupa Array, tidak pernah Null/Undefined
const safeUploads = computed(() => {
    return Array.isArray(props.recentUploads) ? props.recentUploads : [];
});

const formatDate = (dateString) => {
    if (!dateString) return '-';
    try {
        return new Date(dateString).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'long', year: 'numeric'
        });
    } catch (e) {
        return '-';
    }
};
</script>

<template>
    <Head title="Dashboard Inputer" />

    <AppLayout>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            
            <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-blue-900/5 border border-blue-100/50 relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Upload</p>
                    <h3 class="text-4xl font-black text-gray-900">{{ stats?.total_upload || 0 }} <span class="text-lg text-gray-400 font-medium">Berkas</span></h3>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-green-900/5 border border-green-100/50 relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Data Valid</p>
                    <h3 class="text-4xl font-black text-gray-900">{{ stats?.valid || 0 }} <span class="text-lg text-gray-400 font-medium">Set</span></h3>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-orange-900/5 border border-orange-100/50 relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Menunggu</p>
                    <h3 class="text-4xl font-black text-gray-900">{{ stats?.pending || 0 }} <span class="text-lg text-gray-400 font-medium">Set</span></h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-blue-900/5 border border-blue-100/50">
            <h3 class="text-xl font-black text-gray-900 mb-8 flex items-center gap-3">
                <span class="w-2 h-8 bg-blue-600 rounded-full"></span>
                Aktivitas Upload Terkini
            </h3>
            
           <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 text-xs uppercase tracking-widest text-gray-400 font-bold">
                <th class="pb-4 pl-4">Nama Indikator</th>
                <th class="pb-4">Tahun</th>
                <th class="pb-4">Status</th>
                <th class="pb-4">Tanggal</th>
                <th class="pb-4 text-right pr-4">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-sm font-medium text-gray-600">
            <tr v-for="(upload, index) in safeUploads" :key="index" 
                class="group hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                
                <td class="py-4 pl-4 transition-colors">
                    <Link 
                        v-if="upload?.id_data" 
                        :href="`/dataset/${upload.id_data}`"
                        class="font-bold text-gray-700 group-hover:text-blue-600 hover:underline block"
                        title="Lihat Detail Dataset"
                    >
                        {{ upload.nama_indikator }}
                    </Link>

                    <span v-else class="text-gray-400 italic text-xs">
                        Indikator Tidak Ditemukan
                    </span>
                </td>
                
                <td class="py-4">
                    {{ upload?.tahun || '-' }}
                </td>
                
                <td class="py-4">
                    <span v-if="upload?.status === 'aktif' || upload?.status === 'valid'" class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-[10px] font-black uppercase">Valid</span>
                    <span v-else class="px-3 py-1 rounded-full bg-orange-100 text-orange-700 text-[10px] font-black uppercase">Proses</span>
                </td>
                
                <td class="py-4 font-mono text-xs text-gray-400">
                    {{ formatDate(upload?.created_at) }}
                </td>

                <td class="py-4 text-right pr-4">
                    <Link 
                        v-if="upload?.id_data" 
                        :href="`/dataset/${upload.id_data}`"
                        class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-blue-600 bg-blue-50 border border-transparent rounded-xl hover:bg-blue-600 hover:text-white transition-all duration-200"
                    >
                        Detail
                        <svg class="w-3 h-3 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </Link>
                </td>
            </tr>

            <tr v-if="safeUploads.length === 0">
                <td colspan="5" class="py-12 text-center text-gray-400 italic">
                    Belum ada data yang diupload.
                </td>
            </tr>
        </tbody>
    </table>
</div>
        </div>

    </AppLayout>
</template>