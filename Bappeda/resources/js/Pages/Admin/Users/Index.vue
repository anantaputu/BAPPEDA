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

    <div class="bg-white rounded-[2.5rem] p-10 shadow-2xl shadow-gray-100 border border-gray-400 min-h-[70vh]">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12">
            <div>
                <h1 class="text-4xl font-black text-[#000B58] tracking-tight uppercase">
                    Manajemen <span class="text-[#00139E]">User</span>
                </h1>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-[0.2em] mt-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-[#00139E] rounded-full animate-pulse"></span>
                    Total Terdaftar: {{ users.length }} Pengguna
                </p>
            </div>

            <Link
                href="/admin/users/create"
                class="bg-[#00139E] text-white px-8 py-4 rounded-2xl text-lg font-bold hover:bg-[#000B58] hover:-translate-y-1 transition-all duration-300 flex items-center gap-2 shadow-xl shadow-blue-900/10"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Tambah User
            </Link>
        </div>

        <div class="overflow-hidden rounded-[2rem] border border-gray-400 bg-white">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-400">
                        <th class="p-8">Pengguna</th>
                        <th>Peran</th>
                        <th>Status Akses</th>
                        <th class="text-right p-8">Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-sm font-bold text-gray-800">
                    <tr v-for="user in users" :key="user.id" class="border-b border-gray-400 last:border-0 hover:bg-blue-50/20 transition-all group">
                        <td class="p-8">
                            <p class="text-md text-[#000B58] font-black uppercase tracking-tight">@{{ user.username }}</p>
                            <p class="text-xs text-slate-400 font-medium lowercase tracking-widest mt-1">{{ user.email }}</p>
                        </td>
                        <td>
                            <span :class="user.role?.nama_role === 'Admin' ? 'text-[#00139E] bg-blue-50 border-blue-100' : 'text-slate-400 bg-slate-50 border-slate-100'" 
                                  class="text-[9px] font-black uppercase px-3 py-1.5 rounded-xl border inline-block tracking-widest shadow-sm">
                                {{ user.role?.nama_role || 'No Role' }}
                            </span>
                        </td>
                        <td>
                            <button @click="user.role?.nama_role !== 'Admin' ? openStatusModal(user) : null" 
                                    :disabled="user.role?.nama_role === 'Admin'"
                                    :class="[
                                        user.status_aktif ? 'bg-blue-50 text-[#00139E] border-blue-100' : 'bg-amber-50 text-amber-600 border-amber-100',
                                        user.role?.nama_role === 'Admin' ? 'opacity-30 cursor-not-allowed' : 'hover:scale-105 active:scale-95'
                                    ]"
                                    class="text-[9px] font-black uppercase px-4 py-2 rounded-xl border transition-all tracking-widest shadow-sm">
                                {{ user.status_aktif ? 'Aktif' : 'Non-Aktif' }}
                            </button>
                        </td>
                        <td class="p-8 text-right space-x-3">
                            <template v-if="user.role?.nama_role !== 'Admin'">
                                <Link :href="`/admin/users/${user.id}/edit`" class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-blue-50 text-[#00139E] hover:bg-[#00139E] hover:text-white transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </Link>
                                <button @click="openDeleteModal(user)" class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </template>
                            <span v-else class="text-[10px] font-black text-slate-300 uppercase italic tracking-widest">Sistem Terkunci</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Transition
            enter-active-class="duration-300 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="duration-200 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div v-if="showStatusModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-[#000B58]/60 backdrop-blur-md" @click="showStatusModal = false"></div>
                <div class="relative bg-white w-full max-w-md rounded-[3rem] p-10 shadow-2xl text-center border border-gray-100">
                    <div class="mx-auto w-24 h-24 rounded-[2rem] bg-blue-50 text-[#00139E] flex items-center justify-center mb-8">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-2xl font-black text-[#000B58] mb-4 uppercase tracking-tight">{{ statusModalConfig.title }}</h3>
                    <p class="text-slate-500 text-sm font-medium leading-relaxed mb-10 px-2">{{ statusModalConfig.description }}</p>
                    <div class="flex flex-col gap-3">
                        <button @click="executeStatusAction" :class="statusModalConfig.confirmClass" class="w-full text-white font-black uppercase tracking-[0.2em] py-5 rounded-2xl transition-all shadow-lg text-xs">{{ statusModalConfig.confirmText }}</button>
                        <button @click="showStatusModal = false" class="w-full bg-slate-50 text-slate-400 font-bold py-4 rounded-2xl transition-colors uppercase tracking-widest text-[10px]">Batalkan</button>
                    </div>
                </div>
            </div>
        </Transition>

        <DeleteModal 
            :show="showDeleteModal" 
            :title="'Hapus Akun Permanen?'"
            :description="'Menghapus @' + userToDelete?.username + ' akan menghilangkan data secara permanen dari sistem BAPPEDA. Tindakan ini tidak dapat dibatalkan.'"
            @close="showDeleteModal = false"
            @confirm="executeDeleteAction"
        />

    </div>
</template>