<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import IconifyIcon from '@/Components/Base/IconifyIcon.vue';

defineProps({ logoPath: String });

const page = usePage();
const user = computed(() => page.props.auth?.user);

const dashboardRoute = computed(() => {
    if (!user.value) return '/login';
    return user.value.role === 'Admin' 
        ? '/admin/dashboard' 
        : '/inputer/dashboard';
});
</script>

<template>
    <nav class="fixed top-0 w-full bg-white/80 backdrop-blur-xl border-b border-gray-400 h-20 z-50">
        <div class="max-w-[80%] mx-auto h-full flex justify-between items-center">
            <Link href="/" class="flex items-center gap-3 group">
                <img :src="logoPath" alt="Logo" class="h-10 w-auto object-contain transition-transform group-hover:scale-105">
                <span class="text-xl font-black text-primary tracking-tight uppercase">
                    DATA<span class="text-secondary">BAPPEDA</span>
                </span>
            </Link>
            
            <div class="flex gap-10 items-center text-sm font-black uppercase tracking-widest text-primary">
                <Link href="/" 
                    class="text-textsecondary hover:text-secondary transition-colors"
                    :class="{ 'text-secondary': $page.url === '/' }">
                    Beranda
                </Link>
                <Link href="/visualisasi" 
                    class="text-textsecondary hover:text-secondary transition-colors"
                    :class="{ 'text-secondary': $page.url === '/visualisasi' || $page.url === '/public-dashboard' }">
                    Visualisasi
                </Link>
                <Link href="/search" 
                    class="text-textsecondary hover:text-secondary transition-colors"
                    :class="{ 'text-secondary': $page.url === '/search' }">
                    Cari Data
                </Link>
                
                <Link :href="dashboardRoute" 
                    class="group relative flex items-center gap-3 px-8 py-3 rounded-xl transition-all duration-300 active:scale-95 overflow-hidden shadow-lg"
                    :class="user ? 'bg-secondary text-white shadow-secondary/20' : 'bg-primary text-white shadow-primary/20 hover:bg-secondary'">
                    
                    <IconifyIcon v-if="user" icon="solar:login-2-bold" width="16" height="16" />
                    <IconifyIcon v-else icon="solar:user-circle-bold" width="16" height="16" />

                    <span class="relative z-10">
                        {{ user ? 'Ke Dashboard' : 'Log in' }}
                    </span>
                </Link>
            </div>
        </div>
    </nav>
</template>
