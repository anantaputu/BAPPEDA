<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Gunakan layout secara persisten agar navigasi "mengalir"
defineOptions({ layout: AppLayout });

const props = defineProps({
  stats: Object
})

</script>

<template>
    <Head title="Selamat Datang" />

    <section class="relative py-16 overflow-hidden w-full bg-white">
        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[500px] h-[500px] bg-blue-100 rounded-full blur-3xl opacity-50 -z-10"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-[400px] h-[400px] bg-purple-50 rounded-full blur-3xl opacity-40 -z-10"></div>

        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
            
            <div class="text-center lg:text-left">
                <span class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold text-blue-700 bg-blue-50 rounded-full border border-blue-100">
                    Sistem Pemerintahan Kota Mataram
                </span>
                <h1 class="text-5xl lg:text-6xl font-black text-gray-900 leading-tight mb-6">
                    Satu Data <br />
                    <span class="text-blue-600">Pembangunan</span> <br />
                    Terintegrasi
                </h1>
                <p class="text-lg text-gray-600 mb-8 leading-relaxed max-w-xl mx-auto lg:mx-0">
                    Platform digital BAPPEDA Kota Mataram untuk mempermudah penyetoran, validasi, dan monitoring data kinerja pembangunan daerah secara real-time.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <Link href="/login" class="bg-blue-600 text-white px-8 py-4 rounded-xl text-lg font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 hover:-translate-y-1 transition-all duration-300">
                        Mulai Sekarang
                    </Link>
                    <Link href="/cari" class="border border-gray-300 text-gray-700 px-8 py-4 rounded-xl text-lg font-bold hover:bg-gray-50 hover:border-gray-400 transition-all duration-300">
                        Cari Data
                    </Link>
                </div>
            </div>

            <div class="hidden lg:block relative">
                <div class="absolute inset-0 bg-gradient-to-tr from-blue-600 to-purple-600 transform rotate-6 rounded-3xl opacity-10 blur-xl"></div>
                <div class="relative bg-white border border-gray-200 p-8 rounded-3xl shadow-2xl">
                    <div class="flex items-center gap-4 mb-8 border-b border-gray-100 pb-5">
                        <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Upload Data Capaian</h3>
                            <p class="text-xs text-gray-500">Otomatis sinkron ke database</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex gap-3">
                            <div class="h-4 bg-blue-100 rounded w-1/4"></div>
                            <div class="h-4 bg-gray-100 rounded w-3/4"></div>
                        </div>
                        <div class="h-4 bg-gray-50 rounded w-full"></div>
                        <div class="h-4 bg-gray-100 rounded w-5/6"></div>
                        <div class="pt-4 flex items-center justify-between">
                             <div class="flex -space-x-2">
                                <div class="w-8 h-8 rounded-full bg-blue-400 border-2 border-white"></div>
                                <div class="w-8 h-8 rounded-full bg-purple-400 border-2 border-white"></div>
                                <div class="w-8 h-8 rounded-full bg-gray-300 border-2 border-white"></div>
                             </div>
                             <div class="h-8 w-24 bg-blue-50 rounded-lg animate-pulse"></div>
                        </div>
                    </div>
                    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-ping"></span>
                            <span class="text-sm font-bold text-green-600">Sistem Online</span>
                        </div>
                        <button class="bg-gray-900 text-white px-5 py-2.5 rounded-xl text-xs font-semibold hover:bg-gray-800 transition">Dashboard Admin</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 py-12 px-10 bg-gray-900 rounded-[3rem]">
    
                <div class="text-center">
                    <div class="text-4xl font-black text-white mb-1">
                    {{ props.stats.total_indikator }}
                    </div>
                    <div class="text-blue-400 text-xs font-medium uppercase">Indikator</div>
                </div>

                <div class="text-center border-l border-gray-800">
                    <div class="text-4xl font-black text-white mb-1">
                    {{ props.stats.opd_aktif }}
                    </div>
                    <div class="text-blue-400 text-xs font-medium uppercase">OPD Aktif</div>
                </div>

                <div class="text-center border-l border-gray-800">
                    <div class="text-4xl font-black text-white mb-1">
                    {{ props.stats.data_valid }}
                    </div>
                    <div class="text-blue-400 text-xs font-medium uppercase">Data Tervalidasi</div>
                </div>

                <div class="text-center border-l border-gray-800">
                    <div class="text-sm text-white font-bold">
                    {{ props.stats.last_update ?? '-' }}
                    </div>
                    <div class="text-blue-400 text-xs font-medium uppercase">Update Terakhir</div>
                </div>

            </div>
        </div>
    </section>

    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-20">
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">Layanan Unggulan</h2>
                <p class="text-gray-500 text-lg leading-relaxed">Dirancang untuk memudahkan koordinasi data antar instansi di lingkungan Pemerintah Kota Mataram.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-10">
                <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 hover:shadow-2xl hover:shadow-blue-100 transition-all duration-500 group">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-blue-600 group-hover:rotate-6 transition-all duration-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Repository Data</h3>
                    <p class="text-gray-600 leading-relaxed">Pusat penyimpanan seluruh dokumen dan data pembangunan daerah yang terorganisir per tahun dan sektor.</p>
                </div>

                <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 hover:shadow-2xl hover:shadow-blue-100 transition-all duration-500 group">
                    <div class="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-purple-600 group-hover:rotate-6 transition-all duration-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Validasi Cepat</h3>
                    <p class="text-gray-600 leading-relaxed">Proses verifikasi data oleh admin BAPPEDA menjadi lebih cepat dengan sistem feedback otomatis ke OPD.</p>
                </div>

                <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 hover:shadow-2xl hover:shadow-blue-100 transition-all duration-500 group">
                    <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-green-600 group-hover:rotate-6 transition-all duration-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Visualisasi Real-time</h3>
                    <p class="text-gray-600 leading-relaxed">Ubah data mentah menjadi grafik informatif untuk memudahkan pengambilan keputusan pimpinan daerah.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-12 bg-white border-t border-gray-100 text-center">
        <p class="text-gray-400 text-sm">© 2026 BAPPEDA Kota Mataram. Dikembangkan untuk Satu Data Mataram.</p>
    </footer>
</template>