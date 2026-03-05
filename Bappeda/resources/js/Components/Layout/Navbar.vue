<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({ logoPath: String });
</script>

<template>
    <nav class="fixed top-0 w-full bg-white/80 backdrop-blur-xl border-b border-bgsoft h-20 z-50">
        <div class="max-w-[80%] mx-auto h-full flex justify-between items-center">
            <Link href="/" class="flex items-center gap-3 group">
                <img :src="logoPath" alt="Logo" class="h-10 w-auto object-contain transition-transform group-hover:scale-105">
                <span class="text-xl font-black text-primary tracking-tight uppercase">
                    DATA<span class="text-secondary">BAPPEDA</span>
                </span>
            </Link>
            
            <div class="flex gap-10 items-center text-sm font-black uppercase tracking-widest">
                <Link href="/" 
                    class="text-textsecondary hover:text-secondary transition-colors"
                    :class="{ 'text-secondary': $page.url === '/' }">
                    Beranda
                </Link>
                <Link href="/public-dashboard" 
                    class="text-textsecondary hover:text-secondary transition-colors"
                    :class="{ 'text-secondary': $page.url === '/public-dashboard' }">
                    Dashboard
                </Link>
                <Link href="/search" 
                    class="text-textsecondary hover:text-secondary transition-colors"
                    :class="{ 'text-secondary': $page.url === '/search' }">
                    Cari Data
                </Link>
                
                <Link href="/login" 
                    class="bg-primary text-white px-8 py-3 rounded-xl hover:bg-secondary hover:-translate-y-0.5 transition-all duration-300 shadow-lg shadow-primary/10 active:scale-95">
                    Log in
                </Link>
            </div>
        </div>
    </nav>
</template>