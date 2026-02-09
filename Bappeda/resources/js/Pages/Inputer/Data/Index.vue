<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

// 1. SESUAIKAN PROPS
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({ total_upload: 0, valid: 0, pending: 0 })
    },
    recentUploads: {
        type: Array,
        default: () => []
    },
    // Ganti jadi camelCase biar cocok sama Controller ($isAdmin)
    isAdmin: {
        type: Boolean,
        default: false
    }
});

// Helper Function
const getStatusClass = (status) => {
    if (status === 'valid') return 'bg-green-100 text-green-700 border border-green-200';
    if (status === 'processing' || status === 'pending') return 'bg-amber-100 text-amber-700 border border-amber-200';
    return 'bg-red-100 text-red-700 border border-red-200';
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};
</script>

<template>
    <Head title="Dashboard Inputer" />

    <div class="py-8 px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
            <div>
                <h2 class="text-3xl font-black text-gray-900 tracking-tight">Dashboard Data</h2>
                <p class="text-gray-500 font-medium text-sm mt-1">Ringkasan aktivitas input data Anda.</p>
            </div>
            <Link href="/inputer/wizard" class="bg-[#4A6CF7] text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-blue-700 transition shadow-lg shadow-blue-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                INPUT DATA BARU
            </Link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Upload</p>
                    <p class="text-2xl font-black text-gray-900">{{ stats.total_upload }} <span class="text-xs font-medium text-gray-400">Dataset</span></p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Data Valid</p>
                    <p class="text-2xl font-black text-gray-900">{{ stats.valid }} <span class="text-xs font-medium text-gray-400">Dataset</span></p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Menunggu / Proses</p>
                    <p class="text-2xl font-black text-gray-900">{{ stats.pending }} <span class="text-xs font-medium text-gray-400">Dataset</span></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-100 bg-gray-50/30">
                <h3 class="text-lg font-bold text-gray-800">Riwayat Upload Terakhir</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-gray-100">
                            <th class="py-4 px-8 text-[10px] font-black text-gray-400 uppercase tracking-widest w-1/3">Nama Data</th>
                            
                            <th v-if="isAdmin" class="py-4 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                Oleh
                            </th>

                            <th class="py-4 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Periode</th>
                            <th class="py-4 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Tanggal</th>
                            <th class="py-4 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            <th class="py-4 px-8 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        
                        <tr v-for="u in recentUploads" :key="u.id_upload" class="hover:bg-blue-50/30 transition-colors">
                            <td class="py-4 px-8">
                                <div class="font-bold text-gray-800 text-sm">{{ u.data ? u.data.nama_indikator : 'Indikator Dihapus' }}</div>
                                <div class="text-xs text-gray-400">#{{ u.id_upload }}</div>
                            </td>
                            
                            <td v-if="isAdmin" class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center gap-2" :title="u.user?.name">
                                    <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-[10px] font-bold ring-2 ring-white shadow-sm">
                                        {{ u.user?.name?.charAt(0).toUpperCase() || '?' }}
                                    </div>
                                    <span class="text-xs font-medium text-gray-600 truncate max-w-[100px]">
                                        {{ u.user?.name || 'Unknown' }}
                                    </span>
                                </div>
                            </td>

                            <td class="py-4 px-4 text-center">
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold">{{ u.periode }}</span>
                            </td>
                            <td class="py-4 px-4 text-center text-xs font-medium text-gray-500">
                                {{ formatDate(u.created_at) }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span :class="getStatusClass(u.status)" class="text-[10px] font-bold uppercase px-2 py-1 rounded-lg">
                                    {{ u.status }}
                                </span>
                            </td>
                            <td class="py-4 px-8 text-right">
                                <a v-if="u.status === 'valid'" :href="`/inputer/export/${u.id_upload}`" target="_blank" class="text-blue-600 hover:underline text-xs font-bold">Download</a>
                                <Link v-else :href="`/inputer/upload/${u.id_upload}/mapping`" class="text-amber-600 hover:underline text-xs font-bold">Lanjut Mapping</Link>
                            </td>
                        </tr>

                        <tr v-if="recentUploads.length === 0">
                            <td :colspan="isAdmin ? 6 : 5" class="py-12 text-center text-gray-400 text-sm">
                                Belum ada riwayat upload data.
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>