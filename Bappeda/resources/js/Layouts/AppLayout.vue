<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Navbar from '@/Components/Layout/Navbar.vue';
import Sidebar from '@/Components/Layout/Sidebar.vue';
import LogoutModal from '@/Components/Layout/LogoutModal.vue';
import AlertModal from '@/Components/Layout/AlertModal.vue';
import IconifyIcon from '@/Components/Base/IconifyIcon.vue';

const page = usePage();
const logoPath = '/images/logo.png';
const activeUrl = computed(() => page.url);
const showLogoutModal = ref(false);
const showFlashModal = ref(false);
const flashTitle = ref('Informasi');
const flashMessage = ref('');
const flashType = ref('info');

const isInternalPage = computed(() => {
    return activeUrl.value.startsWith('/admin') || activeUrl.value.startsWith('/inputer');
});

const shouldShowSidebar = computed(() => {
    return isInternalPage.value && role.value !== 'anonymous';
});

const shouldShowNavbar = computed(() => {
    return !isInternalPage.value;
});

const isSidebarOpen = ref(true);
const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

watch(() => page.url, () => {
    showLogoutModal.value = false;
});

watch(
    () => page.props.flash,
    (flash) => {
        const successMessage = flash?.success || flash?.message;
        const errorMessage = flash?.error;

        if (errorMessage) {
            flashTitle.value = 'Terjadi Kesalahan';
            flashMessage.value = errorMessage;
            flashType.value = 'error';
            showFlashModal.value = true;
            return;
        }

        if (successMessage) {
            flashTitle.value = 'Berhasil';
            flashMessage.value = successMessage;
            flashType.value = 'success';
            showFlashModal.value = true;
        }
    },
    { deep: true, immediate: true }
);

const role = computed(() => {
    const userData = page.props.auth?.user; 
    if (!userData) return 'anonymous';
    
    const namaRole = userData.role; 
    if (namaRole === 'Admin Super' || namaRole === 'Admin') return 'admin';
    if (namaRole === 'Inputer') return 'inputer';
    return "guest";
});

const openMasterData = ref(
    ['/admin/tema', '/admin/urusan', '/admin/bidang', '/admin/frekuensi']
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
                { name: 'Dashboard', path: dashboardPath, icon: 'solar:home-2-bold' },
            ]
        });
    }

    if (role.value === 'admin') {
        groups.push({
            label: '',
            items: [
                { name: 'Kelola User', path: '/admin/users', icon: 'solar:users-group-rounded-bold' },
            ]
        });
    }

    if (role.value === 'admin' || role.value === 'inputer') {
        groups.push({
            label: 'DATA REFERENSI',
            items: [
                { name: 'Input Data Baru', path: '/inputer/data', icon: 'solar:add-folder-bold' },
                { 
                    name: 'Master Data',
                    icon: 'solar:database-bold',
                    children: [
                        { name: 'Tema', path: '/admin/tema' },
                        { name: 'Urusan', path: '/admin/urusan' },
                        { name: 'Bidang', path: '/admin/bidang' },
                        { name: 'Frekuensi', path: '/admin/frekuensi' },
                        { name: 'Kata Kunci', path: '/admin/katakunci'},
                    ]
                },
            ]
        });
    }

    if (role.value === 'admin') {
        groups.push({
            label: 'LOGS',
            items: [
                { name: 'Masukan Non-User', path: '/admin/contacts', icon: 'solar:chat-round-dots-bold' },
                { name: 'Log Activity', path: '/admin/logs', icon: 'solar:document-text-bold' },
            ]
        });
    }

    return groups;
});
</script>

<template>
    <div class="flex min-h-screen bg-white font-sans overflow-x-hidden">
        
        <Sidebar 
            v-if="shouldShowSidebar"
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

        <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 -translate-x-10" enter-to-class="opacity-100 translate-x-0" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100 translate-x-0" leave-to-class="opacity-0 -translate-x-10">
            <button 
                v-if="shouldShowSidebar && !isSidebarOpen"
                @click="isSidebarOpen = true"
                class="fixed top-1/2 left-6 z-[60] transform -translate-y-1/2 w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center hover:bg-secondary transition-all active:scale-90"
            >
                <IconifyIcon icon="solar:double-alt-arrow-left-bold" width="20" height="20" class="rotate-180" />
            </button>
        </Transition>

        <div 
            :class="[
                !shouldShowSidebar ? 'w-full' : 'flex-1 transition-all duration-500 ease-in-out',
                (shouldShowSidebar && isSidebarOpen) ? 'ml-[20rem]' : 'ml-0'
            ]"
            class="flex flex-col min-h-screen min-w-0"
        >
            <Navbar v-if="shouldShowNavbar" :logoPath="logoPath"/>

            <main :class="['p-8 flex-1 bg-white', shouldShowNavbar ? '' : '']">
                <div class="max-w-full">
                    <slot />
                </div>
            </main>
        </div>

        <LogoutModal :show="showLogoutModal" @close="showLogoutModal = false" />
        <AlertModal
            :show="showFlashModal"
            :title="flashTitle"
            :description="flashMessage"
            :type="flashType"
            @close="showFlashModal = false"
        />
    </div>
</template>
