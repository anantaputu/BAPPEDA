<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteModal from '@/Components/Layout/DeleteModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

// 1. PROPS DEFINITION
const props = defineProps({
    recentUploads: {
        type: Array,
        default: () => []
    },
    isAdmin: {
        type: Boolean,
        default: false
    }
});

// 2. MODAL STATE (Sesuai gaya Manajemen User)
const showDeleteModal = ref(false);
const dataToDelete = ref(null);

// Warna Status standar BAPPEDA NTB
const getStatusClass = (status) => {
    const map = {
        'valid': 'bg-blue-50 text-[#00139E] border-blue-100',
        'processing': 'bg-amber-50 text-amber-600 border-amber-100',
        'pending': 'bg-amber-50 text-amber-600 border-amber-100',
        'rejected': 'bg-rose-50 text-rose-600 border-rose-100',
    };
    return map[status] || 'bg-gray-50 text-gray-500 border-gray-100';
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', { 
        day: 'numeric', 
        month: 'long', 
        year: 'numeric' 
    });
};

// 3. LOGIKA AKSI
const openDeleteModal = (upload) => {
    dataToDelete.value = upload;
    showDeleteModal.value = true;
};

const executeDeleteAction = () => {
    if (dataToDelete.value) {
        // Asumsi route delete menggunakan id_upload atau id_data sesuai kebutuhan Anda
        router.delete(`/inputer/data/${dataToDelete.value.id_upload}`, {
            onSuccess: () => {
                showDeleteModal.value = false;
                dataToDelete.value = null;
            }
        });
    }
};
</script>

<template>
    <Head title="Kelola Data Indikator" />

    <div class="bg-white rounded-[2.5rem] p-10 shadow-2xl shadow-gray-100 border border-gray-400">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12">
            <div>
                <h1 class="text-4xl font-black text-[#000B58] tracking-tight uppercase">
                    Manajemen <span class="text-[#00139E]">Data</span>
                </h1>
                <p class="text-slate-400 font-bold text-[10px] mt-2 uppercase tracking-[0.2em] flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-[#00139E] rounded-full animate-pulse"></span>
                    Total Record: {{ recentUploads.length }} Entri Terbaru
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                <Link href="/inputer/data/input-single" 
                    class="bg-white text-[#00139E] border-2 border-[#00139E] px-8 py-4 rounded-2xl font-black text-[11px] tracking-widest uppercase hover:bg-blue-50 transition-all shadow-lg flex items-center justify-center gap-3 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Input Single
                </Link>

                <Link href="/inputer/data/input-multi" 
                    class="bg-[#00139E] text-white px-8 py-4 rounded-2xl font-black text-[11px] tracking-widest uppercase hover:bg-[#000B58] transition-all shadow-xl shadow-blue-900/10 flex items-center justify-center gap-3 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Bulk Upload (Excel)
                </Link>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-100 border border-gray-400 overflow-hidden">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-400">
                        <th class="p-8">Indikator Pembangunan</th>
                        <th v-if="isAdmin" class="text-center">Operator</th>
                        <th class="text-center">Periode</th>
                        <th class="text-center">Status</th>
                        <th class="text-right p-8">Manajemen</th>
                    </tr>
                </thead>

                <tbody class="text-sm font-bold text-gray-800">
                    <tr v-for="u in recentUploads" :key="u.id_upload" 
                        class="border-b border-gray-400 last:border-0 hover:bg-blue-50/20 transition-all group">
                        
                        <td class="p-8">
                            <div v-if="u.data">
                                <Link :href="`/dataset/${u.data.id_data}`" 
                                    class="text-md text-[#000B58] font-black uppercase tracking-tight group-hover:text-[#00139E] transition-colors">
                                    {{ u.data.nama_indikator }}
                                </Link>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">ID: #{{ u.data.id_data }}</span>
                                    <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ formatDate(u.created_at) }}</span>
                                </div>
                            </div>
                            <div v-else class="flex flex-col">
                                <span class="text-rose-500 italic text-[11px] font-black uppercase tracking-widest">Relasi Terputus</span>
                            </div>
                        </td>

                        <td v-if="isAdmin" class="text-center">
                            <div class="inline-flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100">
                                <span class="text-[10px] font-black text-[#000B58] uppercase tracking-tighter">
                                    {{ u.user?.name || 'Unknown' }}
                                </span>
                            </div>
                        </td>

                        <td class="text-center">
                            <span class="text-[#000B58] text-[10px] font-black uppercase tracking-widest bg-blue-50 px-3 py-1.5 rounded-xl border border-blue-100">
                                {{ u.periode }}
                            </span>
                        </td>

                        <td class="text-center">
                            <span :class="getStatusClass(u.status)" class="text-[9px] font-black uppercase px-4 py-2 rounded-xl border tracking-widest shadow-sm">
                                {{ u.status }}
                            </span>
                        </td>

                        <td class="p-8 text-right space-x-2">
                            <a :href="`/inputer/export/${u.id_upload}`" target="_blank" 
                                class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-blue-50 text-[#00139E] hover:bg-[#00139E] hover:text-white transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" stroke-width="2.5" /></svg>
                            </a>
                            
                            <Link :href="`/inputer/data/${u.data?.id_data}/edit`" 
                                class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-blue-50 text-[#00139E] hover:bg-[#000B58] hover:text-white transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" /></svg>
                            </Link>

                            <button @click="openDeleteModal(u)" 
                                class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" /></svg>
                            </button>
                        </td>
                    </tr>

                    <tr v-if="recentUploads.length === 0">
                        <td :colspan="isAdmin ? 5 : 4" class="p-20 text-center text-gray-300 uppercase italic tracking-[0.3em] text-xs">
                            Belum ada riwayat aktivitas input
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <DeleteModal 
            :show="showDeleteModal" 
            :title="'Hapus Record Data?'"
            :description="'Entri data indikator untuk periode ' + dataToDelete?.periode + ' akan dihapus dari riwayat upload. Pastikan Anda telah memiliki cadangan data.'"
            @close="showDeleteModal = false"
            @confirm="executeDeleteAction"
        />

    </div>
</template>