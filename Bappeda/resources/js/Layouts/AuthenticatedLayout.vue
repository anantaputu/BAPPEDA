<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);

// Logika Role: anonymous, inputer, admin
// Di AppLayout.vue
const role = computed(() => {
    // Normalisasi nama role agar konsisten dengan v-if
    const namaRole = page.props.auth.user.role;
    if (namaRole === 'Admin') return 'admin';
    if (namaRole === 'Inputer') return 'inputer';
    
    return 'anonymous';
});

// Definisi Menu berdasarkan Role
const menuGroups = computed(() => {
    const groups = [];

    // Kelompok ADMIN (Hanya Super Admin)
    if (role.value === 'admin') {
        groups.push({
            label: 'ADMIN',
            items: [
                { name: 'Kelola Data', path: '/data/manage', icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10' },
                { name: 'Kelola User', path: '/users', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
            ]
        });
    }

    // Kelompok INPUTER (Inputer & Admin)
    if (role.value === 'inputer' || role.value === 'admin') {
        groups.push({
            label: 'INPUTER',
            items: [
                { name: 'Input Data', path: '/input-data', icon: 'M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
            ]
        });
    }

    return groups;
});
</script>

<template>
    <div class="flex min-h-screen bg-[#F8FAFC] font-sans">
        
        <aside v-if="role !== 'anonymous'" 
               class="w-72 bg-white rounded-[2.5rem] shadow-sm flex flex-col p-8 fixed h-[calc(100vh-3rem)] m-6">
            <div class="flex items-center gap-4 mb-10">
                <div class="w-10 h-10 border-2 border-gray-200 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                <span class="text-2xl font-extrabold text-gray-800">{{ role === 'admin' ? 'Admin' : 'Inputer' }}</span>
            </div>

            <div class="flex-1 overflow-y-auto space-y-8 no-scrollbar">
                <div v-for="group in menuGroups" :key="group.label">
                    <p class="text-[10px] font-black text-gray-400 tracking-widest mb-4 ml-2">{{ group.label }}</p>
                    <div class="space-y-1">
                        <Link v-for="item in group.items" :key="item.name" :href="item.path"
                            :class="[page.url === item.path ? 'text-[#4A6CF7]' : 'text-gray-800 hover:text-[#4A6CF7]']"
                            class="flex items-center gap-4 px-2 py-2 transition-colors font-bold text-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" /></svg>
                            {{ item.name }}
                        </Link>
                    </div>
                </div>
            </div>

            <div class="mt-auto pt-6 border-t border-gray-50">
                <Link href="/logout" method="post" as="button" class="flex items-center gap-4 text-gray-400 font-bold text-sm hover:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Log out
                </Link>
            </div>
        </aside>

        <nav v-if="role === 'anonymous'" 
             class="fixed top-0 w-full bg-white/80 backdrop-blur-md border-b border-gray-100 h-20 px-12 flex justify-between items-center z-50">
            <div class="flex items-center gap-2">
                <div class="bg-[#4A6CF7] text-white px-3 py-1 rounded-lg text-xs font-bold uppercase">logos</div>
                <span class="text-xl font-black text-[#1E3A8A] tracking-tight">DATA<span class="text-[#3B82F6]">BAPPEDA</span></span>
            </div>
            <div class="flex gap-8 items-center">
                <Link href="/dashboard" class="text-sm font-bold text-gray-600 hover:text-blue-600">Dashboard</Link>
                <Link href="/cari" class="text-sm font-bold text-gray-600 hover:text-blue-600">Cari</Link>
                <Link href="/login" class="bg-[#4A6CF7] text-white px-8 py-2.5 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">Log in</Link>
            </div>
        </nav>

        <main :class="[role === 'anonymous' ? 'pt-28 px-12 w-full' : 'ml-[22rem] p-12 w-full']">
            <header v-if="role !== 'anonymous'" class="flex justify-between items-center mb-10">
                <div>
                    <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Dashboard</h1>
                    <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-wider">Jum'at, 12 mei 2025</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="font-bold text-sm text-gray-900 leading-none">{{ user?.name }}</p>
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-1">{{ role }} Manager</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-200 rounded-full border-2 border-white shadow-sm overflow-hidden flex items-center justify-center">
                         <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" /></svg>
                    </div>
                </div>
            </header>

            <slot />
        </main>
    </div>
</template>