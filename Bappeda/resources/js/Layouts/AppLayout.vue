<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const logoPath = '/images/logo.png';
const page = usePage();

// State untuk Menu Master Data
const openMasterData = ref(
    page.url.startsWith('/admin/data') ||
    page.url.startsWith('/admin/tema') ||
    page.url.startsWith('/admin/urusan') ||
    page.url.startsWith('/admin/bidang') ||
    page.url.startsWith('/admin/frekuensi')
);

// State untuk Modal Logout
const showLogoutModal = ref(false);

const confirmLogout = () => { showLogoutModal.value = true; };
const cancelLogout = () => { showLogoutModal.value = false; };

// Deteksi User & Role
const user = computed(() => page.props.auth.user);

const role = computed(() => {
    const userData = page.props.auth.user;
    if (!userData) return 'anonymous';
    const namaRole = userData.role; 
    if (namaRole === 'Admin Super' || namaRole === 'Admin') return 'admin';
    if (namaRole === 'Inputer') return 'inputer';
    return "guest";
});

// Konfigurasi Menu Berdasarkan Role [cite: 2026-02-02, 2026-02-03]
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
                { name: 'Kelola User', path: '/admin/users', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
            ]
        });
    }

    if (role.value === 'inputer' || role.value === 'admin') {
        groups.push({
            label: 'OPERASIONAL',
            items: [
                { name: 'Input Data', path: '/inputer/data', icon: 'M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
            ]
        });
    }
    return groups;
});
</script>

<template>
    <div class="flex min-h-screen bg-[#F8FAFC] font-sans">
        
        <aside v-if="role !== 'anonymous'" 
               class="w-72 bg-white rounded-[2.5rem] border border-[#A2B5CB]/30 shadow-2xl shadow-[#000B58]/5 flex flex-col p-8 fixed h-[calc(100vh-3rem)] m-6 z-40">
            <div class="flex items-center gap-4 mb-10">
                <div class="w-12 h-12 flex-shrink-0 bg-white rounded-xl flex items-center justify-center border border-[#A2B5CB]/20 shadow-sm overflow-hidden">
                    <img :src="logoPath" alt="Logo" class="w-9 h-9 object-contain">
                </div>
                <span class="text-xl font-extrabold text-[#000B58] tracking-tight italic">
                    DATA<span class="text-[#00139E]">BAPPEDA</span>
                </span>
            </div>

            <div class="flex-1 overflow-y-auto space-y-8 no-scrollbar">
                <div v-for="group in menuGroups" :key="group.label">
                    <p class="text-[10px] font-black text-[#A2B5CB] tracking-widest mb-4 ml-2 uppercase">{{ group.label }}</p>
                    <div class="space-y-1">
                        <template v-for="item in group.items" :key="item.name">
                            <div v-if="item.children">
                                <button @click="openMasterData = !openMasterData"
                                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300"
                                    :class="openMasterData ? 'text-[#00139E] bg-[#00139E]/5' : 'text-[#A2B5CB] hover:text-[#000B58] hover:bg-gray-50'">
                                    <div class="flex items-center gap-4">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" /></svg>
                                        {{ item.name }}
                                    </div>
                                    <svg class="w-4 h-4 transition-transform duration-300" :class="openMasterData ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </button>
                                <div v-if="openMasterData" class="ml-9 mt-2 border-l-2 border-[#A2B5CB]/20 space-y-1">
                                    <Link v-for="child in item.children" :key="child.name" :href="child.path"
                                        class="block px-6 py-2 text-sm font-semibold transition-colors duration-200"
                                        :class="page.url.startsWith(child.path) ? 'text-[#00139E] font-bold' : 'text-[#A2B5CB] hover:text-[#000B58]'">
                                        {{ child.name }}
                                    </Link>
                                </div>
                            </div>
                            <Link v-else :href="item.path"
                                class="flex items-center gap-4 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300"
                                :class="page.url.startsWith(item.path) ? 'text-white bg-[#00139E] shadow-lg shadow-[#00139E]/20' : 'text-[#A2B5CB] hover:text-[#000B58] hover:bg-gray-50'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" /></svg>
                                {{ item.name }}
                            </Link>
                        </template>
                    </div>
                </div>
            </div>

            <div class="mt-auto pt-6 border-t border-[#A2B5CB]/20">
                <button @click="confirmLogout" class="w-full flex items-center gap-4 text-[#A2B5CB] font-bold text-sm hover:text-[#FF1414] transition-colors px-4 py-2 group">
                    <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Keluar Sesi
                </button>
            </div>
        </aside>

      <nav v-if="role === 'anonymous'" 
             class="fixed top-0 w-full bg-white/90 backdrop-blur-md border-b border-[#A2B5CB]/20 h-20 z-50">
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex justify-between items-center">
                
                <Link href="/" class="flex items-center gap-3 group">
                    <img :src="logoPath" alt="Logo" class="h-10 w-auto object-contain">
                    <span class="text-xl font-black text-[#000B58] tracking-tight">DATA<span class="text-[#00139E]">BAPPEDA</span></span>
                </Link>
                
                <div class="flex gap-8 items-center text-sm font-bold">
                    <Link href="/" class="text-[#A2B5CB] hover:text-[#00139E] transition">Beranda</Link>
                    <Link href="/dashboard" class="text-[#A2B5CB] hover:text-[#00139E] transition">Dashboard</Link>
                    <Link href="/cari" class="text-[#A2B5CB] hover:text-[#00139E] transition">Cari</Link>
                    <Link href="/login" class="bg-[#00139E] text-white px-8 py-2.5 rounded-xl hover:bg-[#000B58] transition shadow-lg shadow-[#00139E]/20">Log in</Link>
                </div>

            </div>
        </nav>

        <main :class="[role === 'anonymous' ? 'w-full pt-28' : 'ml-[22rem] p-12 w-full flex flex-col min-h-screen']">
            <header v-if="role !== 'anonymous'" class="flex justify-between items-center mb-10">
                <div>
                    <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight capitalize">{{ page.url.split('/')[2] || 'Dashboard' }}</h1>
                    <p class="text-gray-400 text-[10px] font-black mt-1 uppercase tracking-widest italic">Sistem Informasi Data Pembangunan</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="font-bold text-sm text-gray-900 leading-none">{{ user?.nama_depan }} {{ user?.nama_belakang }}</p>
                        <p class="text-[10px] text-[#4A6CF7] font-black uppercase tracking-widest mt-1">{{ role }} Access</p>
                    </div>
                    <div class="w-12 h-12 bg-white rounded-2xl border-2 border-gray-100 shadow-sm flex items-center justify-center text-[#4A6CF7]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                </div>
            </header>

            <div class="flex-1"><slot /></div>

            <footer v-if="role === 'anonymous'" class="w-full bottom-0 bg-white border-t border-[#A2B5CB]/20 py-16 mt-20">
                <div class="max-w-[80%] mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                    <div class="space-y-4">
                        <h2 class="text-2xl font-black text-[#000B58]">BAPPEDA Provinsi Nusa Tenggara Barat</h2>
                        <p class="text-[#A2B5CB] text-xs font-medium max-w-xl italic">Jl. Flamboyan No.2, Mataram Bar., Kec. Selaparang, Kota Mataram, NTB 83126</p>
                        <div class="flex items-center gap-6 text-sm font-bold text-gray-600">
                            <span>Contact Us:</span>
                            <div class="flex items-center gap-2"><svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg> bappedaprov</div>
                            <div class="flex items-center gap-2"><svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg> bappedaprov</div>
                        </div>
                    </div>
                    <img :src="logoPath" alt="Logo" class="h-24 w-auto grayscale opacity-50">
                </div>
            </footer>
        </main>

        <div v-if="showLogoutModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6">
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-md" @click="cancelLogout"></div>
            <div class="relative bg-white w-full max-w-sm rounded-[3rem] p-12 shadow-2xl text-center animate-in fade-in zoom-in duration-300">
                <div class="w-24 h-24 bg-red-50 text-red-500 rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-inner">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-3 tracking-tight">Akhiri Sesi?</h3>
                <p class="text-sm text-gray-400 font-medium mb-10 leading-relaxed px-2">Anda akan keluar dari sistem Dashboard Satu Data BAPPEDA. Pastikan pekerjaan Anda telah disimpan.</p>
               <div class="flex flex-col gap-4">
                <Link 
                    href="/logout" 
                    method="post" 
                    as="button" 
                    @click="showLogoutModal = false"
                    class="w-full bg-red-600 text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-red-100 hover:bg-red-700 transition-all active:scale-95"
                >
                    Ya, Keluar Sekarang
                </Link>

                <button 
                    @click="cancelLogout" 
                    class="w-full bg-gray-50 text-gray-400 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-gray-100 transition-all active:scale-95"
                >
                    Kembali ke Sistem
                </button>
            </div>
            </div>
        </div>
    </div>
</template>