<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout }); // Persisten layout

const props = defineProps({
    dataIndikator: Array // Berisi data + relasi tema, urusan, bidang
});

const deleteData = (id) => {
    if (confirm('Menghapus master data akan menghapus semua riwayat upload terkait. Lanjutkan?')) {
        router.delete(`/data/${id}`);
    }
};
</script>

<template>
    <Head title="Master Data Indikator" />

    <div class="bg-white rounded-[2.5rem] shadow-sm p-10 border border-gray-100">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Master Data Indikator</h2>
                <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mt-1">Kelola daftar indikator pembangunan daerah</p>
            </div>
            <Link href="/data/create" class="bg-[#4A6CF7] text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Tambah Indikator
            </Link>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="py-4 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Indikator</th>
                        <th class="py-4 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kategori</th>
                        <th class="py-4 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="py-4 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="item in dataIndikator" :key="item.id_data" class="hover:bg-gray-50 transition-colors group">
                        <td class="py-6 px-2">
                            <p class="font-bold text-gray-900 text-sm italic">{{ item.nama_indikator }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">{{ item.sumber }}</p>
                        </td>
                        <td class="py-6 px-2">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded w-fit uppercase">{{ item.tema?.nama_tema }}</span>
                                <span class="text-[10px] font-medium text-gray-400 italic">{{ item.urusan?.nama_urusan }}</span>
                            </div>
                        </td>
                        <td class="py-6 px-2">
                            <span :class="item.status === 'aktif' ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50'" class="text-[10px] font-black uppercase px-3 py-1 rounded-full">
                                {{ item.status }}
                            </span>
                        </td>
                        <td class="py-6 px-2 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <Link :href="`/data/${item.id_data}/edit`" class="p-2 bg-white text-gray-400 rounded-lg hover:text-blue-600 border border-transparent hover:border-gray-100 shadow-sm transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </Link>
                                <button @click="deleteData(item.id_data)" class="p-2 bg-white text-gray-400 rounded-lg hover:text-red-600 border border-transparent hover:border-gray-100 shadow-sm transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>