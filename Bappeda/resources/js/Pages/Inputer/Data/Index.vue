<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteModal from '@/Components/Layout/DeleteModal.vue';
import AlertModal from '@/Components/Layout/AlertModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    recentUploads: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
});

const showDeleteModal = ref(false);
const dataToDelete = ref(null);
const showAlertModal = ref(false);
const alertTitle = ref('Informasi');
const alertMessage = ref('');
const alertType = ref('info');

const openAlert = (title, message, type = 'info') => {
    alertTitle.value = title;
    alertMessage.value = message;
    alertType.value = type;
    showAlertModal.value = true;
};

const openDeleteModal = (item, type = 'data') => {
    if (type === 'riwayat') {
        dataToDelete.value = {
            id_data: item.data?.id_data,
            nama_data: item.data?.nama_data
        };
    } else {
        dataToDelete.value = {
            id_data: item.id_data,
            nama_data: item.nama_data
        };
    }
    showDeleteModal.value = true;
};

const executeDeleteAction = () => {
    if (dataToDelete.value && dataToDelete.value.id_data) {
        router.delete(`/inputer/data/${dataToDelete.value.id_data}`, {
            preserveScroll: true,
            onSuccess: () => {
                showDeleteModal.value = false;
                dataToDelete.value = null;
            },
            onError: (err) => {
                console.error("Gagal Hapus:", err);
                openAlert('Gagal Menghapus', 'Pastikan Anda memiliki akses untuk menghapus data ini.', 'error');
            }
        });
    } else {
        openAlert('Data Tidak Ditemukan', 'ID data tidak ditemukan.', 'warning');
    }
};

const exportUrl = '/export-bulk?scope=internal';
</script>

<template>
    <Head title="Kelola Data Indikator" />

    <div class="min-h-full py-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-8">
            <div>
                <h1 class="text-2xl font-black text-primary uppercase tracking-tight">
                    Manajemen <span class="text-secondary">Data</span>
                </h1>
                <p class="text-[10px] text-textsecondary font-black uppercase tracking-[0.2em] mt-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-secondary rounded-full animate-pulse shadow-sm shadow-secondary/50"></span>
                    Audit & Sinkronisasi Indikator Sektoral
                </p>
            </div>

            <div class="flex flex-wrap gap-3 w-full md:w-auto">
                <Link href="/inputer/data/input-single" 
                    class="bg-white text-secondary border-2 border-secondary px-6 py-3 rounded-xl font-black text-[10px] tracking-widest uppercase hover:bg-secondary/5 transition-all shadow-sm flex items-center justify-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                    Input Single
                </Link>

                <Link href="/inputer/data/input-multi" 
                    class="bg-primary text-white px-6 py-3 rounded-xl font-black text-[10px] tracking-widest uppercase hover:bg-secondary transition-all shadow-lg shadow-primary/10 flex items-center justify-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Bulk Upload
                </Link>

                <a :href="exportUrl" target="_blank"
                    class="bg-inovasi text-white px-6 py-3 rounded-xl font-black text-[10px] tracking-widest uppercase hover:opacity-90 transition-all shadow-lg shadow-inovasi/20 flex items-center justify-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export Excel
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-400 bg-white shadow-sm">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-bgsoft text-left text-[10px] font-black text-textsecondary uppercase tracking-[0.2em] border-b border-gray-400">
                        <th class="p-8">Indikator & Klasifikasi</th>
                        <th class="text-center">Urusan / Bidang</th>
                        <th class="text-center">Satuan</th>
                        <th class="text-center">Periode</th>
                        <th v-if="isAdmin" class="text-center">Operator</th>
                        <th class="text-right p-8">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-bold text-primary">
                    <tr v-for="u in recentUploads" :key="u.id_upload" class="border-b border-gray-100 last:border-0 hover:bg-bgsoft/50 transition-all group">
                        <td class="p-8 max-w-[350px]">
                            <div v-if="u.data">
                                <Link :href="`/dataset/${u.data.id_data}`" class="text-sm text-primary font-black uppercase tracking-tight group-hover:text-secondary transition-colors line-clamp-2">
                                    {{ u.data.nama_data }}
                                </Link>
                                <span class="text-[9px] font-black text-textsecondary/40 uppercase tracking-widest mt-2 block italic">ID: #{{ u.data.id_data }}</span>
                            </div>
                            <span v-else class="text-integritas italic text-[11px] font-black uppercase tracking-widest">Relasi Terputus</span>
                        </td>
                        <td class="text-center py-4">
                            <div class="flex flex-col items-center gap-1.5">
                                <span class="text-[9px] font-black text-primary uppercase px-3 py-1 bg-bgsoft rounded-lg border border-gray-200">
                                    {{ u.data?.urusan?.nama_urusan || 'N/A' }}
                                </span>
                                <span class="text-[9px] font-bold text-textsecondary uppercase opacity-50 tracking-tighter">
                                    {{ u.data?.bidang?.nama_bidang || 'N/A' }}
                                </span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="text-[10px] font-black text-textsecondary uppercase">{{ u.data?.satuan || '-' }}</span>
                        </td>
                        <td class="text-center">
                            <span class="text-primary text-[10px] font-black uppercase tracking-widest bg-secondary/5 px-3 py-1.5 rounded-xl border border-secondary/10">
                                {{ u.periode }}
                            </span>
                        </td>
                        <td v-if="isAdmin" class="text-center">
                            <span class="text-[10px] font-black text-primary uppercase tracking-tighter bg-bgsoft px-3 py-1.5 rounded-lg border border-gray-200">
                                {{ u.user?.name || 'Unknown' }}
                            </span>
                        </td>
                        <td class="p-8 text-right space-x-2">
                            <a v-if="u.data?.id_data" :href="`/export/data/${u.data.id_data}`" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-bgsoft text-primary hover:bg-secondary hover:text-white transition-all shadow-sm border border-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" stroke-width="2.5" /></svg>
                            </a>
                            <Link :href="`/inputer/data/${u.data?.id_data}/edit`" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-bgsoft text-primary hover:bg-secondary hover:text-white transition-all shadow-sm border border-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" /></svg>
                            </Link>
                            <button @click="openDeleteModal(u, 'riwayat')" 
                                class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-integritas/10 text-integritas hover:bg-integritas hover:text-white transition-all shadow-sm border border-integritas/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" /></svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <DeleteModal 
            :show="showDeleteModal" 
            :title="'Hapus Riwayat Data?'"
            :description="'Entri data \'' + (dataToDelete?.nama_data || 'ini') + '\' akan dihapus. Pastikan Anda telah memiliki cadangan data.'"
            @close="showDeleteModal = false"
            @confirm="executeDeleteAction"
        />
        <AlertModal
            :show="showAlertModal"
            :title="alertTitle"
            :description="alertMessage"
            :type="alertType"
            @close="showAlertModal = false"
        />
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { height: 8px; width: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 20px; border: 2px solid white; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #0284C7; }
</style>
