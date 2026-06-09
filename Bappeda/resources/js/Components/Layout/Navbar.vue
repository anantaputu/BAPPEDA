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
    <nav class="fixed top-0 z-50 h-20 w-full border-b border-slate-200 bg-white/82 backdrop-blur-xl">
        <div class="max-w-[80%] mx-auto h-full flex justify-between items-center">
            <Link href="/" class="flex items-center gap-3 group">
                <img :src="logoPath" alt="Logo" class="h-10 w-auto object-contain transition-transform group-hover:scale-105">
                <span class="text-[1.375rem] font-black tracking-tight text-primary">
                    DATA<span class="text-secondary">BAPPEDA</span>
                </span>
            </Link>
            
            <div class="flex items-center gap-10 text-primary">
                <Link href="/" 
                    class="ui-eyebrow text-textsecondary transition-colors hover:text-secondary"
                    :class="{ 'text-secondary': $page.url === '/' }">
                    Beranda
                </Link>
                <Link href="/visualisasi" 
                    class="ui-eyebrow text-textsecondary transition-colors hover:text-secondary"
                    :class="{ 'text-secondary': $page.url === '/visualisasi' || $page.url === '/public-dashboard' }">
                    Visualisasi
                </Link>
                <Link href="/search" 
                    class="ui-eyebrow text-textsecondary transition-colors hover:text-secondary"
                    :class="{ 'text-secondary': $page.url === '/search' }">
                    Cari Data
                </Link>
                
                <Link :href="dashboardRoute" 
                    class="ui-chip group relative flex items-center gap-3 overflow-hidden px-7 py-3 text-white transition-all duration-300 active:scale-95"
                    style="border-radius: var(--radius-soft);"
                    :class="user ? 'bg-secondary text-white shadow-secondary/20' : 'bg-primary text-white shadow-primary/20 hover:bg-secondary'">
                    
                    <IconifyIcon v-if="user" icon="solar:login-2-bold" width="24" height="24" />
                    <IconifyIcon v-else icon="solar:user-circle-bold" width="24" height="24" />

                    <span class="relative z-10">
                        {{ user ? 'Ke Dashboard' : 'Log in' }}
                    </span>
                </Link>
            </div>
        </div>
    </nav>
</template>
