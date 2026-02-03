<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const props = defineProps({
    uploads: Array // Data dari tabel data_uploads
});

const getStatusClass = (status) => {
    if (status === 'valid') return 'bg-green-50 text-green-600';
    if (status === 'processing') return 'bg-amber-50 text-amber-600';
    return 'bg-red-50 text-red-600';
};
</script>

<template>
    <Head title="Riwayat Input Data" />

    <div class="bg-white rounded-[2rem] shadow-sm p-10 border border-gray-100">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Riwayat Input Data</h2>
                <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mt-1">Kelola dan pantau setoran data anda</p>
            </div>
            <Link href="/input-data" class="bg-[#4A6CF7] text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Tambah Data Baru
            </Link>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="py-4 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Indikator / Periode</th>
                        <th class="py-4 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Tgl Upload</th>
                        <th class="py-4 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                        <th class="py-4 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="u in uploads" :key="u.id_upload" class="hover:bg-gray-50 transition-colors group">
                        <td class="py-6 px-2">
                            <p class="font-bold text-gray-900 text-sm italic">{{ u.data?.nama_indikator }}</p>
                            <p class="text-[10px] text-blue-600 font-black uppercase mt-1">Periode: {{ u.periode }}</p>
                        </td>
                        <td class="py-6 px-2 text-center text-xs text-gray-500 font-bold">
                            {{ new Date(u.created_at).toLocaleDateString('id-ID') }}
                        </td>
                        <td class="py-6 px-2 text-center">
                            <span :class="getStatusClass(u.status)" class="text-[10px] font-black uppercase px-3 py-1 rounded-full">
                                {{ u.status }}
                            </span>
                        </td>
                        <td class="py-6 px-2 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <Link :href="`/input-data/${u.id_upload}/edit`" class="inline-flex items-center gap-2 bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-xs font-bold hover:bg-[#4A6CF7] hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    Edit
                                </Link>
                                <Link :href="`/input-data/${u.id_upload}/mapping`" class="p-2 bg-gray-100 text-gray-400 rounded-lg hover:text-blue-600 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                </Link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>