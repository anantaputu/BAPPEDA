<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteModal from '@/Components/Layout/DeleteModal.vue'; // Komponen baru kita
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    users: Array
});

// 1. Kontrol untuk Modal Status (Internal)
const showStatusModal = ref(false);
const statusModalConfig = ref({
    title: '',
    description: '',
    confirmClass: '',
    action: null
});

// 2. Kontrol untuk DeleteModal (Persis cara LogoutModal)
const showDeleteModal = ref(false);
const userToDelete = ref(null);

// Fungsi pembuka modal
const openStatusModal = (user) => {
    const isActivating = !user.status_aktif;
    statusModalConfig.value = {
        title: isActivating ? 'Aktifkan Akses?' : 'Nonaktifkan Akses?',
        description: `Apakah Anda yakin ingin mengubah status akses untuk @${user.username}?`,
        confirmText: isActivating ? 'Ya, Aktifkan' : 'Ya, Nonaktifkan',
        confirmClass: 'bg-[#00139E] hover:bg-[#000B58] shadow-blue-900/20',
        action: () => router.patch(`/admin/users/${user.id}/status`)
    };
    showStatusModal.value = true;
};

const openDeleteModal = (user) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

// Fungsi Eksekusi
const executeStatusAction = () => {
    if (statusModalConfig.value.action) {
        statusModalConfig.value.action();
        showStatusModal.value = false;
    }
};

const executeDeleteAction = () => {
    if (userToDelete.value) {
        router.delete(`/admin/users/${userToDelete.value.id}`, {
            onSuccess: () => {
                showDeleteModal.value = false;
                userToDelete.value = null;
            }
        });
    }
};
</script>

<template>
    <Head title="Kelola User" />

    <div class="min-h-full">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-5">
            <div>
                <h1 class="text-2xl font-black text-primary uppercase tracking-tight">
                    Manajemen <span class="text-[#00139E]">User</span>
                </h1>
                <p class="text-sm text-textsecondary font-medium">
                    Total Terdaftar: {{ users.length }} Pengguna
                </p>
            </div>
            <Link
                href="/admin/users/create"
                
                class="flex-1 md:flex-none bg-primary text-white px-10 py-4 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-secondary transition-all duration-300 disabled:opacity-50 flex items-center justify-center gap-3 active:scale-95"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Tambah User
            </Link>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-400 bg-white shadow-sm">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-bgsoft text-left text-[10px] font-black text-textsecondary uppercase tracking-[0.2em] border-b border-gray-400">
                        <th class="p-8">Pengguna</th>
                        <th>Peran</th>
                        <!-- <th>Status Akses</th> -->
                        <th class="text-right p-8">Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-sm font-bold text-primary">
                    <tr v-for="user in users" :key="user.id" 
                        class="border-b border-gray-100 last:border-0 hover:bg-bgsoft/50 transition-all group">
                        
                        <td class="p-8">
                            <div class="flex flex-col">
                                <p class="text-md text-primary font-black uppercase tracking-tight">
                                    @{{ user.username }}
                                </p>
                                <p class="text-[10px] text-textsecondary font-bold lowercase tracking-widest mt-1 opacity-70">
                                    {{ user.email }}
                                </p>
                            </div>
                        </td>

                        <td>
                            <span :class="user.role?.nama_role === 'Admin' 
                                    ? 'text-secondary bg-secondary/10 border-secondary/20' 
                                    : 'text-textsecondary bg-bgsoft border-gray-200'" 
                                  class="text-[9px] font-black uppercase px-3 py-1.5 rounded-xl border inline-block tracking-widest shadow-sm">
                                {{ user.role?.nama_role || 'No Role' }}
                            </span>
                        </td>

                        <!-- <td>
                            <div
                                class="text-[9px] font-black uppercase px-4 py-2 rounded-xl border transition-all tracking-widest shadow-sm">
                                {{ user.status_aktif ? 'Aktif' : 'Non-Aktif' }}
                            </div>
                        </td> -->

                        <td class="p-8 text-right space-x-3">
                            <template v-if="user.role?.nama_role !== 'Admin'">
                                <Link :href="`/admin/users/${user.id}/edit`" 
                                      class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-bgsoft text-primary hover:bg-secondary hover:text-white transition-all shadow-sm border border-gray-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </Link>
                                
                                <button @click="openDeleteModal(user)" 
                                        class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-integritas/10 text-integritas hover:bg-integritas hover:text-white transition-all shadow-sm border border-integritas/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </template>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <DeleteModal 
            :show="showDeleteModal" 
            :title="'Konfirmasi Hapus'"
            :description="'Akun @' + userToDelete?.username + ' akan dihapus permanen dari sistem.'"
            @close="showDeleteModal = false"
            @confirm="executeDeleteAction"
        />

    </div>
</template>