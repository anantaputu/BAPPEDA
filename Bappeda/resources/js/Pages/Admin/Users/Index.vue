<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    users: Array
});

// --- Logic Modal Konfirmasi ---
const showModal = ref(false);
const modalConfig = ref({
    type: 'status', // 'status' atau 'delete'
    title: '',
    description: '',
    confirmText: '',
    confirmClass: '',
    action: null
});

const openModal = (type, user) => {
    if (type === 'status') {
        const isActivating = !user.status_aktif;
        modalConfig.value = {
            type: 'status',
            title: isActivating ? 'Aktifkan User?' : 'Nonaktifkan User?',
            description: `Apakah Anda yakin ingin mengubah status akses untuk @${user.username}? Pengguna ini akan ${isActivating ? 'mendapatkan' : 'kehilangan'} akses ke panel sistem.`,
            confirmText: isActivating ? 'Ya, Aktifkan' : 'Ya, Nonaktifkan',
            confirmClass: 'bg-blue-600 hover:bg-blue-700 shadow-blue-500/20',
            action: () => router.patch(`/admin/users/${user.id}/status`)
        };
    } else {
        modalConfig.value = {
            type: 'delete',
            title: 'Hapus Permanen?',
            description: `Menghapus @${user.username} akan menghilangkan data akun dan riwayat aktivitasnya secara permanen dari sistem BAPPEDA.`,
            confirmText: 'Ya, Hapus Sekarang',
            confirmClass: 'bg-red-500 hover:bg-red-600 shadow-red-500/20',
            action: () => router.delete(`/admin/users/${user.id}`)
        };
    }
    showModal.value = true;
};

const executeAction = () => {
    if (modalConfig.value.action) {
        modalConfig.value.action();
        showModal.value = false;
    }
};
</script>

<template>
    <Head title="Kelola User" />

    <div class="bg-white rounded-[2.5rem] p-10 shadow-2xl shadow-gray-100 border border-gray-400 min-h-[70vh]">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12">
            <div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight">
                    Manajemen <span class="text-[#00139E]">User</span>
                </h1>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-[0.2em] mt-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-[#00139E] rounded-full animate-pulse"></span>
                    Total Terdaftar: {{ users.length }} Pengguna
                </p>
            </div>

            <Link
                href="/admin/users/create"
                class="bg-[#00139E] text-white px-8 py-4 rounded-2xl text-lg font-bold hover:bg-[#000B58] hover:-translate-y-1 transition-all duration-300 flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Tambah User Baru
            </Link>
        </div>

        <div class="overflow-hidden rounded-[2rem] border border-gray-400 bg-white">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-400">
                        <th class="p-8">Pengguna</th>
                        <th>Role</th>
                        <th>Status Akun</th>
                        <th class="text-right p-8">Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-sm font-bold text-gray-800">
                    <tr v-for="user in users" :key="user.id" 
                        class="border-b border-gray-400 last:border-0 hover:bg-blue-50/20 transition-all group">
                        
                        <td class="p-8">
                            <div>
                                <p class="text-md text-gray-700 font-bold mt-0.5">@{{ user.username }} • {{ user.email }}</p>
                            </div>
                        </td>

                        <td>
                            <span 
                                :class="user.role?.nama_role === 'Admin' 
                                    ? 'text-blue-600 bg-blue-50 border-blue-100' 
                                    : 'text-gray-400 bg-gray-50 border-gray-100'" 
                                class="text-[9px] font-black uppercase px-3 py-1.5 rounded-xl border inline-block tracking-widest"
                            >
                                {{ user.role?.nama_role || 'No Role' }}
                            </span>
                        </td>

                        <td>
                            <button @click="user.role?.nama_role !== 'Admin' ? openModal('status', user) : null" 
                                    :disabled="user.role?.nama_role === 'Admin'"
                                    :class="[
                                        user.status_aktif ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100',
                                        user.role?.nama_role === 'Admin' ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105 active:scale-95'
                                    ]"
                                    class="text-[9px] font-black uppercase px-4 py-2 rounded-xl border transition-all tracking-widest">
                                {{ user.status_aktif ? 'Aktif' : 'Non-Aktif' }}
                            </button>
                        </td>

                        <td class="p-8 text-right space-x-3">
                            <template v-if="user.role?.nama_role !== 'Admin'">
                                <Link :href="`/admin/users/${user.id}/edit`" 
                                      class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-blue-50 text-[#4A6CF7] hover:bg-[#4A6CF7] hover:text-white transition-all shadow-sm group-hover:scale-110">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </Link>
                                
                                <button @click="openModal('delete', user)" 
                                        class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all shadow-sm group-hover:scale-110">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </template>
                            <span v-else class="text-[10px] font-bold text-gray-300 uppercase italic tracking-widest">
                                Sistem Terkunci
                            </span>
                        </td>
                    </tr>

                    <tr v-if="users.length === 0">
                        <td colspan="4" class="p-20 text-center text-gray-300 uppercase italic tracking-widest text-xs">
                            Belum ada pengguna terdaftar
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Transition
            enter-active-class="duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6">
                <div class="absolute inset-0 bg-[#000B58]/40 backdrop-blur-sm" @click="showModal = false"></div>
                
                <div class="relative bg-white w-full max-w-md rounded-[2.5rem] p-10 shadow-2xl text-center transform transition-all border border-gray-100">
                    
                    <div :class="modalConfig.type === 'delete' ? 'bg-red-50 text-red-500' : 'bg-blue-50 text-blue-500'" 
                         class="mx-auto w-20 h-20 rounded-3xl flex items-center justify-center mb-6">
                        
                        <svg v-if="modalConfig.type === 'delete'" class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <svg v-else class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <h3 class="text-2xl font-black text-[#000B58] mb-2">{{ modalConfig.title }}</h3>
                    <p class="text-[#A2B5CB] text-sm leading-relaxed mb-10 px-4">
                        {{ modalConfig.description }}
                    </p>

                    <div class="flex flex-col gap-3">
                        <button 
                            @click="executeAction"
                            :class="modalConfig.confirmClass"
                            class="w-full text-white font-bold py-4 rounded-2xl transition-all shadow-lg active:scale-[0.98]"
                        >
                            {{ modalConfig.confirmText }}
                        </button>
                        
                        <button 
                            @click="showModal = false"
                            class="w-full bg-transparent hover:bg-gray-50 text-[#A2B5CB] font-bold py-4 rounded-2xl transition-colors"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
        
        <div class="h-12"></div>
    </div>
</template>