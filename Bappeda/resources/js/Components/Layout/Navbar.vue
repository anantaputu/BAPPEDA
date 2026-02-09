<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({ logoPath: String });
</script>

<template>
    <nav class="fixed top-0 w-full bg-white/20 backdrop-blur-xl border-b border-gray-400 h-20 z-50">
        <div class="max-w-[80%] mx-auto h-full flex justify-between items-center">
            <Link href="/" class="flex items-center gap-3 group">
                <img :src="logoPath" alt="Logo" class="h-10 w-auto object-contain">
                <span class="text-xl font-black text-[#000B58] tracking-tight">
                    DATA<span class="text-[#00139E]">BAPPEDA</span>
                </span>
            </Link>
            
            <div class="flex gap-8 items-center text-sm font-bold">
                <Link href="/" class="text-[#A2B5CB] hover:text-[#00139E] transition">Beranda</Link>
                <Link href="/public-dashboard" class="text-[#A2B5CB] hover:text-[#00139E] transition">Dashboard</Link>
                <Link href="/cari" class="text-[#A2B5CB] hover:text-[#00139E] transition">Cari</Link>
                <Link href="/login" class="bg-[#00139E] text-white px-8 py-2.5 rounded-xl hover:bg-[#000B58] transition">
                    Log in
                </Link>
            </div>
        </div>
    </nav>
</template>

