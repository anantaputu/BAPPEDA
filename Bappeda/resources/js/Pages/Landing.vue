<script setup>
import { Head, Link } from '@inertiajs/vue3'
// KITA TIDAK PAKAI IMPORT LAYOUT AGAR TIDAK KEJEPIT
</script>

<template>
    <Head title="Landing Page" />

    <div class="w-full min-h-screen bg-white">
        
        <nav class="w-full border-b border-gray-100 bg-white/80 backdrop-blur-md sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
                <div class="text-2xl font-extrabold flex items-center text-gray-800">
                    <img src="/public/images/logo.png" alt="Logo BAPPEDA" class="h-10 w-10 object-contain" />
                    DATA<span class="text-blue-600">BAPPEDA</span>
                </div>
                
                <div class="flex gap-4">
                    <Link href="/dashboard" class="text-gray-600 font-semibold hover:text-blue-600 px-4 py-2">
                        Dashboard
                    </Link>
                    <Link href="/cari" class="text-gray-600 font-semibold hover:text-blue-600 px-4 py-2">
                        Cari
                    </Link>
                    <Link href="/login" class="bg-blue-600 text-white px-6 py-2 rounded-full font-bold hover:bg-blue-700 transition">
                        Login
                    </Link>
                </div>
            </div>
        </nav>

        <section class="relative pt-20 pb-32 overflow-hidden w-full">
            <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[500px] h-[500px] bg-blue-100 rounded-full blur-3xl opacity-50 -z-10"></div>

            <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
                
                <div class="text-center lg:text-left">
                    <span class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold text-blue-700 bg-blue-50 rounded-full border border-blue-100">
                        Sistem Pemerintahan Daerah
                    </span>
                    <h1 class="text-5xl lg:text-6xl font-black text-gray-900 leading-tight mb-6">
                        Satu Data <br />
                        <span class="text-blue-600">Pembangunan</span> <br />
                        Terintegrasi
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        Platform digital untuk mempermudah penyetoran, validasi, dan monitoring data kinerja pembangunan daerah secara real-time.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <Link href="/login" class="bg-blue-600 text-white px-8 py-4 rounded-xl text-lg font-bold shadow-lg hover:bg-blue-700 transition">
                            Mulai Sekarang
                        </Link>
                        <Link href="#" class="border border-gray-300 text-gray-700 px-8 py-4 rounded-xl text-lg font-bold hover:bg-gray-50 transition">
                            Pelajari Alur
                        </Link>
                    </div>
                </div>

                <div class="hidden lg:block relative">
                    <div class="absolute inset-0 bg-gradient-to-tr from-blue-600 to-purple-600 transform rotate-6 rounded-3xl opacity-20 blur-lg"></div>
                    <div class="relative bg-white border border-gray-200 p-8 rounded-3xl shadow-2xl">
                        <div class="flex items-center gap-4 mb-6 border-b border-gray-100 pb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">1</div>
                            <div>
                                <h3 class="font-bold text-gray-900">Upload Data Excel</h3>
                                <p class="text-xs text-gray-500">Format .xlsx didukung</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="h-4 bg-gray-100 rounded w-3/4"></div>
                            <div class="h-4 bg-gray-100 rounded w-full"></div>
                            <div class="h-4 bg-gray-100 rounded w-5/6"></div>
                        </div>
                        <div class="mt-6 pt-4 border-t border-gray-100 flex justify-between items-center">
                            <span class="text-sm font-bold text-green-600">Status: Aktif</span>
                            <button class="bg-gray-900 text-white px-4 py-2 rounded-lg text-xs">Lihat Detail</button>
                        </div>
                    </div>
                </div>

            </div>
        </section>

    </div>
</template>