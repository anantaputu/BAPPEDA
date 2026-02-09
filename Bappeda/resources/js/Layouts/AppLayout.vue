<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
// Import Komponen Baru
import Navbar from '@/Components/Layout/Navbar.vue';
import Sidebar from '@/Components/Layout/Sidebar.vue';
import UserHeader from '@/Components/Layout/UserHeader.vue';
import LogoutModal from '@/Components/Layout/LogoutModal.vue';

// 1. Inisialisasi Data Global
const page = usePage();
const logoPath = '/images/logo.png';
const activeUrl = computed(() => page.url);
const showLogoutModal = ref(false);

// 2. Logika Deteksi Role
const role = computed(() => {
    const userData = page.props.auth.user;
    if (!userData) return 'anonymous';
    const namaRole = userData.role; 
    if (namaRole === 'Admin Super' || namaRole === 'Admin') return 'admin';
    if (namaRole === 'Inputer') return 'inputer';
    return "guest";
});

// 3. Logika Dropdown Master Data (Auto-open jika di URL terkait)
const openMasterData = ref(
    ['/admin/data', '/admin/tema', '/admin/urusan', '/admin/bidang', '/admin/frekuensi']
    .some(path => activeUrl.value.startsWith(path))
);

// 4. Konfigurasi Menu Berdasarkan Role
const menuGroups = computed(() => {
    const groups = [];
    const dashboardPath = role.value === 'admin' 
        ? '/admin/dashboard' 
        : (role.value === 'inputer' ? '/inputer/dashboard' : '/dashboard');

    if (role.value !== 'anonymous') {
        groups.push({
            label: 'MENU UTAMA',
            items: [
                { name: 'Dashboard', path: dashboardPath, icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
            ]
        });
    }

    if (role.value === 'admin') {
        groups.push({
            label: 'ADMINISTRATOR',
            items: [
                { name: 'Kelola User', path: '/admin/users', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
            ]
        });
    }

  if (role.value === 'admin' || role.value === 'inputer') {
        groups.push({
            label: 'DATA REFERENSI', // Label baru agar cocok untuk keduanya
            items: [
                { 
                    name: 'Master Data',
                    icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                    children: [
                        { name: 'Data Indikator', path: '/admin/data' },
                        { name: 'Tema', path: '/admin/tema' },
                        { name: 'Urusan', path: '/admin/urusan' },
                        { name: 'Bidang', path: '/admin/bidang' },
                        { name: 'Frekuensi', path: '/admin/frekuensi' },
                    ]
                },
                {
                       name: 'Input Data Baru', 

                    path: '/inputer/data', // Route ini sekarang aman untuk admin
                    icon: 'M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' 
                }
            ]
        });
    }
    return groups;
});
</script>

<template>
    <div class="flex min-h-screen bg-white font-sans">
        
        <Navbar v-if="role === 'anonymous'" :logoPath="logoPath" />

        <Sidebar 
            v-if="role !== 'anonymous'"
            :role="role"
            :menuGroups="menuGroups"
            :activeUrl="activeUrl"
            :openMasterData="openMasterData"
            :logoPath="logoPath"
            @toggleMasterData="openMasterData = !openMasterData"
            @logout="showLogoutModal = true"
        />

        <main :class="[role === 'anonymous' ? 'w-full' : 'ml-[22rem] p-8 w-full flex flex-col min-h-screen']">
            <!-- <UserHeader v-if="role !== 'anonymous'" /> -->
            
            <div class="flex-1">
                <slot />
            </div>

            <footer v-if="role === 'anonymous'" class="w-full bg-white border border-gray-400 py-16 mt-20">
                <div class="max-w-[80%] mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
                    <div class="space-y-4">
                        <h2 class="text-2xl font-black text-[#000B58]">BAPPEDA Provinsi Nusa Tenggara Barat</h2>
                        <p class="text-[#A2B5CB] text-xs italic">Jl. Flamboyan No.2, Mataram Bar., Kec. Selaparang, Kota Mataram, NTB 83126</p>
                    </div>
                    <img :src="logoPath" alt="Logo" class="h-32 grayscale opacity-50">
                </div>
            </footer>
        </main>

        <LogoutModal :show="showLogoutModal" @close="showLogoutModal = false" />
    </div>
</template>