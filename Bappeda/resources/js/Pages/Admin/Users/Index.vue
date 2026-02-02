<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

// Set Layout secara persisten
defineOptions({ layout: AppLayout });

const props = defineProps({
    users: Array // Data user beserta relasi role-nya
});

const toggleStatus = (user) => {
    if (confirm(`Apakah Anda yakin ingin ${user.status_aktif ? 'menonaktifkan' : 'mengaktifkan'} user ini?`)) {
        router.patch(`/users/${user.id}/status`);
    }
};

const deleteUser = (id) => {
    if (confirm('Menghapus user akan menghilangkan semua riwayat uploadnya. Lanjutkan?')) {
        router.delete(`/users/${id}`);
    }
};
</script>

<template>
    <Head title="Kelola User" />

    <div class="bg-white rounded-[2rem] shadow-sm p-8 border border-gray-100">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen User</h2>
                <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mt-1">
                    Total: {{ users.length }} Pengguna Terdaftar
                </p>
            </div>
            <Link 
                href="/users/create" 
                class="bg-[#4A6CF7] text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200 flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                Tambah User Baru
            </Link>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="py-4 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pengguna</th>
                        <th class="py-4 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Role</th>
                        <th class="py-4 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status Akun</th>
                        <th class="py-4 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 transition-colors group">
                        <td class="py-5 px-2">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-[#4A6CF7] font-black text-xs border-2 border-white shadow-sm">
                                    {{ user.nama_depan[0] }}{{ user.nama_belakang ? user.nama_belakang[0] : '' }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">{{ user.nama_depan }} {{ user.nama_belakang }}</p>
                                    <p class="text-[10px] text-gray-400 font-medium">@{{ user.username }} • {{ user.email }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="py-5 px-2">
                            <span :class="user.role?.nama_role === 'Admin Super' ? 'text-blue-600 bg-blue-50' : 'text-gray-500 bg-gray-50'" 
                                  class="text-[10px] font-black uppercase px-3 py-1 rounded-lg">
                                {{ user.role?.nama_role || 'No Role' }}
                            </span>
                        </td>

                        <td class="py-5 px-2">
                            <button @click="toggleStatus(user)" 
                                    :class="user.status_aktif ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50'"
                                    class="text-[10px] font-black uppercase px-3 py-1 rounded-full transition-all hover:ring-2 hover:ring-offset-2"
                                    :title="user.status_aktif ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan'">
                                {{ user.status_aktif ? 'Aktif' : 'Non-Aktif' }}
                            </button>
                        </td>

                        <td class="py-5 px-2 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <Link :href="`/users/${user.id}/edit`" class="p-2 bg-white text-gray-400 rounded-lg hover:text-blue-600 hover:shadow-sm border border-transparent hover:border-gray-100 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </Link>
                                <button @click="deleteUser(user.id)" class="p-2 bg-white text-gray-400 rounded-lg hover:text-red-600 hover:shadow-sm border border-transparent hover:border-gray-100 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="users.length === 0" class="py-20 text-center">
                <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <p class="text-gray-400 font-bold">Belum ada user terdaftar.</p>
            </div>
        </div>
    </div>
</template>