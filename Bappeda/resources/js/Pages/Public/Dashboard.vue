<script setup>
import { Link } from '@inertiajs/vue3';
// 1. Import layout secara mandiri
import AppLayout from '@/Layouts/AppLayout.vue'; 

// Jika Anda ingin menggunakan persistent layout (opsional)
defineOptions({ layout: AppLayout })
</script>

<template>
        <div class="space-y-10">
            <div class="text-center py-10">
                <h1 class="text-4xl font-black text-gray-900 mb-4">Informasi Capaian Pembangunan</h1>
                <p class="text-gray-500 max-w-2xl mx-auto font-medium">
                    Akses terbuka bagi masyarakat untuk memantau data indikator pembangunan daerah secara transparan dan akuntabel.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div v-for="item in ['Total Indikator', 'Data Tervalidasi', 'Update Terakhir']" :key="item" 
                    class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm text-center">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ item }}</p>
                    <p class="text-3xl font-black text-blue-600">85%</p>
                </div>
            </div>

            <div class="bg-blue-600 p-10 rounded-[2.5rem] text-white flex flex-col md:flex-row justify-between items-center gap-6 shadow-xl shadow-blue-100">
                <div>
                    <h3 class="text-2xl font-black mb-2">Ingin mencari data spesifik?</h3>
                    <p class="text-blue-100 font-medium">Gunakan fitur pencarian untuk menemukan indikator berdasarkan tema atau urusan.</p>
                </div>
                <Link href="/cari" class="bg-white text-blue-600 px-8 py-4 rounded-2xl font-black hover:bg-blue-50 transition whitespace-nowrap">
                    EKSPLORASI DATA
                </Link>
            </div>
        </div>
</template>