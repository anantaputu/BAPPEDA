<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Navbar from '@/Components/Layout/Navbar.vue';
import Sidebar from '@/Components/Layout/Sidebar.vue';
import LogoutModal from '@/Components/Layout/LogoutModal.vue';

const isSidebarOpen = ref(true);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const page = usePage();
const logoPath = '/images/logo.png';
const activeUrl = computed(() => page.url);
const showLogoutModal = ref(false);

watch(() => page.url, () => {
    showLogoutModal.value = false;
});

const role = computed(() => {
    const userData = page.props.auth?.user; 
    if (!userData) return 'anonymous';
    
    const namaRole = userData.role; 
    if (namaRole === 'Admin Super' || namaRole === 'Admin') return 'admin';
    if (namaRole === 'Inputer') return 'inputer';
    return "guest";
});

const openMasterData = ref(
    ['/admin/data', '/admin/tema', '/admin/urusan', '/admin/bidang', '/admin/frekuensi']
    .some(path => activeUrl.value.startsWith(path))
);

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
            label: 'DATA REFERENSI',
            items: [
                {
                    name: 'Input Data Baru', 
                    path: '/inputer/data', 
                    icon: 'M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' 
                },
                { 
                    name: 'Master Data',
                    icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                    children: [
                        { name: 'Tema', path: '/admin/tema' },
                        { name: 'Urusan', path: '/admin/urusan' },
                        { name: 'Bidang', path: '/admin/bidang' },
                        { name: 'Frekuensi', path: '/admin/frekuensi' },
                    ]
                },
            ]
        });
    }
    return groups;
});
</script>

<template>
    <div class="flex min-h-screen bg-white font-sans overflow-x-hidden">
        
        <Sidebar 
            v-if="role !== 'anonymous'"
            :isSidebarOpen="isSidebarOpen"
            :role="role"
            :menuGroups="menuGroups"
            :activeUrl="activeUrl"
            :openMasterData="openMasterData"
            :logoPath="logoPath"
            @toggleMasterData="openMasterData = !openMasterData"
            @logout="showLogoutModal = true"
            @toggleSidebar="isSidebarOpen = !isSidebarOpen" 
        />

        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 -translate-x-10"
            enter-to-class="opacity-100 translate-x-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-x-0"
            leave-to-class="opacity-0 -translate-x-10"
        >
            <button 
                v-if="role !== 'anonymous' && !isSidebarOpen"
                @click="isSidebarOpen = true"
                class="fixed top-1/2 left-6 z-[60] transform -translate-y-1/2 w-12 h-12 bg-primary text-white rounded-xl flex items-center justify-center shadow-xl shadow-primary/20 hover:bg-secondary transition-all active:scale-90"
            >
                <svg class="w-6 h-6 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        </Transition>

        <div 
            :class="[
                role === 'anonymous' ? 'w-full' : 'flex-1 transition-all duration-500 ease-in-out',
                (role !== 'anonymous' && isSidebarOpen) ? 'ml-[26rem]' : 'ml-0'
            ]"
            class="flex flex-col min-h-screen min-w-0"
        >
            <Navbar v-if="role == 'anonymous'" :logoPath="logoPath"/>

            <main class="p-8 flex-1 bg-white">
                <div class="max-w-full">
                    <slot />
                </div>
            </main>

            <footer v-if="role !== 'anonymous'" class="px-8 py-6 border-t border-bgsoft text-xs font-bold text-textsecondary uppercase tracking-widest">
                &copy; 2026 BAPPEDA Provinsi NTB — Satu Data Terintegrasi
            </footer>
        </div>

        <LogoutModal :show="showLogoutModal" @close="showLogoutModal = false" />
    </div>
</template>