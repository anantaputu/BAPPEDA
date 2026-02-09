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
            description: `Apakah Anda yakin ingin ${isActivating ? 'mengaktifkan' : 'menonaktifkan'} akses untuk ${user.nama_depan} ${user.nama_belakang}?`,
            confirmText: isActivating ? 'Ya, Aktifkan' : 'Ya, Nonaktifkan',
            confirmClass: isActivating ? 'bg-emerald-600 shadow-emerald-100 hover:bg-emerald-700' : 'bg-amber-600 shadow-amber-100 hover:bg-amber-700',
            action: () => router.patch(`/admin/users/${user.id}/status`)
        };
    } else {
        modalConfig.value = {
            type: 'delete',
            title: 'Hapus Pengguna?',
            description: `Menghapus ${user.nama_depan} akan menghilangkan semua riwayat aktivitasnya secara permanen. Lanjutkan?`,
            confirmText: 'Ya, Hapus Permanen',
            confirmClass: 'bg-rose-600 shadow-rose-100 hover:bg-rose-700',
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
                    Manajemen <span class="text-[#4A6CF7]">User</span>
                </h1>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-[0.2em] mt-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-[#4A6CF7] rounded-full animate-pulse"></span>
                    Total Terdaftar: {{ users.length }} Pengguna
                </p>
            </div>

            <Link
                href="/admin/users/create"
                class="bg-[#4A6CF7] text-white px-10 py-5 rounded-[1.5rem] font-black text-xs uppercase tracking-widest shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all hover:-translate-y-1 active:scale-95 flex items-center gap-2"
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

                <tbody class="text-sm font-bold text-gray-600">
                    <tr v-for="user in users" :key="user.id" 
                        class="border-b border-gray-400 last:border-0 hover:bg-blue-50/20 transition-all group">
                        
                        <td class="p-8">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-[#4A6CF7]/10 rounded-2xl flex items-center justify-center text-[#4A6CF7] font-black text-xs border border-[#4A6CF7]/20 shadow-sm transition-transform group-hover:scale-110">
                                    {{ user.nama_depan?.[0] ?? '' }}{{ user.nama_belakang?.[0] ?? '' }}
                                </div>
                                <div>
                                    <p class="text-gray-900 font-extrabold uppercase text-xs tracking-tight">{{ user.nama_depan }} {{ user.nama_belakang }}</p>
                                    <p class="text-[10px] text-gray-300 font-bold mt-0.5">@{{ user.username }} • {{ user.email }}</p>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span :class="user.role?.nama_role === 'Admin Super' ? 'text-blue-600 bg-blue-50 border-blue-100' : 'text-gray-400 bg-gray-50 border-gray-100'" 
                                  class="text-[9px] font-black uppercase px-3 py-1.5 rounded-xl border inline-block tracking-widest">
                                {{ user.role?.nama_role || 'No Role' }}
                            </span>
                        </td>

                        <td>
                            <button @click="openModal('status', user)" 
                                    :class="user.status_aktif ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100'"
                                    class="text-[9px] font-black uppercase px-4 py-2 rounded-xl border transition-all hover:scale-105 active:scale-95 tracking-widest">
                                {{ user.status_aktif ? 'Aktif' : 'Non-Aktif' }}
                            </button>
                        </td>

                        <td class="p-8 text-right space-x-3">
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
                        </td>
                    </tr>

                    <tr v-if="users.length === 0">
                        <td colspan="4" class="p-20 text-center">
                            <div class="flex flex-col items-center opacity-30">
                                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="font-black uppercase tracking-widest text-xs">Belum ada pengguna terdaftar</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6">
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-md transition-opacity" @click="showModal = false"></div>
            
            <div class="relative bg-white w-full max-w-sm rounded-[3rem] p-12 shadow-2xl text-center transform transition-all animate-in fade-in zoom-in duration-300">
                <div :class="modalConfig.type === 'delete' ? 'bg-rose-50 text-rose-500' : 'bg-blue-50 text-blue-500'" 
                     class="w-24 h-24 rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-inner">
                    <svg v-if="modalConfig.type === 'delete'" class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <svg v-else class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                
                <h3 class="text-2xl font-black text-gray-900 mb-3 uppercase tracking-tight">{{ modalConfig.title }}</h3>
                <p class="text-sm text-gray-400 font-medium mb-10 leading-relaxed px-2">
                    {{ modalConfig.description }}
                </p>
                
                <div class="flex flex-col gap-4">
                    <button 
                        @click="executeAction"
                        :class="modalConfig.confirmClass"
                        class="w-full text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl transition-all active:scale-95"
                    >
                        {{ modalConfig.confirmText }}
                    </button>
                    <button 
                        @click="showModal = false"
                        class="w-full bg-gray-50 text-gray-400 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-gray-100 transition-all active:scale-95"
                    >
                        Kembali
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>