<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            last_update: '-',
            total_indikator: 0,
            opd_aktif: 0,
            data_valid: 0
        })
    }
});
</script>

<template>
    <Head title="Selamat Datang" />

    <section class="relative mt-20 py-28 overflow-hidden w-full"> 
        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[600px] h-[600px] bg-[#A2B5CB]/10 rounded-full blur-3xl -z-10"></div>
        
        <div class="max-w-[80%] mx-auto grid lg:grid-cols-2 gap-16 items-center">
            <div class="text-center lg:text-left">
                <span class="inline-block px-4 py-1.5 mb-6 text-sm font-bold text-[#00139E] bg-[#A2B5CB]/20 rounded-full border border-[#A2B5CB]/30 tracking-wide uppercase">
                    Sistem Data Bappeda
                </span>
                <h1 class="text-5xl lg:text-7xl font-black text-[#000B58] leading-[1.1] mb-6">
                    Satu Data <br />
                    <span class="text-[#00139E]">Pembangunan</span> <br />
                    Terintegrasi
                </h1>
                <p class="text-lg text-gray-500 mb-8 leading-relaxed max-w-xl mx-auto lg:mx-0 font-medium">
                    Platform digital BAPPEDA Provinsi Nusa Tenggara Barat untuk mempermudah penyetoran, validasi, dan monitoring data kinerja pembangunan daerah secara real-time.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <Link href="/login" class="bg-[#00139E] text-white px-8 py-4 rounded-2xl text-lg font-bold hover:bg-[#000B58] hover:-translate-y-1 transition-all duration-300">
                        Pelajari lebih lanjut
                    </Link>
                    <Link href="/cari" class="border border-gray-400 text-[#000B58] px-8 py-4 rounded-2xl text-lg font-bold hover:bg-[#A2B5CB]/10 transition-all duration-300">
                        Cari Data
                    </Link>
                </div>
            </div>

            <div class="hidden lg:block relative">
                <!-- <div class="absolute inset-0 bg-gradient-to-tr from-[#000B58] to-[#00139E] transform rotate-6 rounded-[3rem] opacity-5 blur-2xl"></div> -->
                <div class="relative bg-white border border-gray-400 p-10 rounded-[3rem]">
                    
                    <div class="flex items-center gap-5 mb-8 border-b border-gray-400 pb-6">
                        <div class="w-14 h-14 bg-[#000B58] rounded-2xl flex items-center justify-center text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-[#000B58] text-lg leading-none mb-1">Monitoring Data</h3>
                            <p class="text-[10px] text-[#A2B5CB] uppercase tracking-[0.2em] font-black">Update: {{ props.stats.last_update ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-10">
                        <div class="bg-gray-50/50 p-5 rounded-[2rem] border border-gray-400 text-center">
                            <div class="text-3xl font-black text-[#00139E] mb-1">{{ props.stats.total_indikator }}</div>
                            <div class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-tighter">Indikator</div>
                        </div>
                        <div class="bg-gray-50/50 p-5 rounded-[2rem] border border-gray-400 text-center">
                            <div class="text-3xl font-black text-[#000B58] mb-1">{{ props.stats.opd_aktif }}</div>
                            <div class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-tighter">OPD Aktif</div>
                        </div>
                        <!-- <div class="bg-gray-50/50 p-5 rounded-[2rem] border border-gray-400 text-center">
                            <div class="text-3xl font-black text-[#FF1414] mb-1">{{ props.stats.data_valid }}</div>
                            <div class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-tighter">Tervalidasi</div>
                        </div> -->
                    </div>

                    <!-- <div class="space-y-5 mb-10">
                        <div class="flex gap-3 items-center">
                            <div class="h-2.5 bg-[#00139E]/20 rounded-full w-1/3"></div>
                            <div class="h-2.5 bg-gray-100 rounded-full w-2/3"></div>
                        </div>
                        <div class="h-2.5 bg-gray-50 rounded-full w-full"></div>
                    </div> -->

                    <div class="pt-6 border-t border-gray-40 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                            <span class="text-sm font-black text-green-600 uppercase tracking-widest">Sistem Online</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-32">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-24">
                <h2 class="text-4xl md:text-5xl font-black text-[#000B58] mb-6">Layanan Unggulan</h2>
                <div class="w-24 h-2 bg-[#00139E] mx-auto mb-8 rounded-full"></div>
                <p class="text-gray-500 text-lg font-medium leading-relaxed">Dirancang untuk memudahkan koordinasi data antar instansi di lingkungan Pemerintah Kota Mataram.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-12">
                <div class="bg-white p-12 rounded-[3rem] border border-gray-400 hover:border-[#00139E]/30 hover:shadow-2xl hover:shadow-[#000B58]/5 transition-all duration-500 group">
                    <div class="w-20 h-20 bg-[#A2B5CB]/10 rounded-[1.5rem] flex items-center justify-center mb-10 group-hover:bg-[#000B58] group-hover:rotate-6 transition-all duration-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#000B58] group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-[#000B58] mb-4">Repository Data</h3>
                    <p class="text-gray-500 leading-relaxed font-medium">Pusat penyimpanan seluruh dokumen dan data pembangunan daerah yang terorganisir per tahun dan sektor.</p>
                </div>

                <div class="bg-white p-12 rounded-[3rem] border border-gray-400 hover:border-[#00139E]/30 hover:shadow-2xl hover:shadow-[#000B58]/5 transition-all duration-500 group">
                    <div class="w-20 h-20 bg-[#00139E]/5 rounded-[1.5rem] flex items-center justify-center mb-10 group-hover:bg-[#00139E] group-hover:rotate-6 transition-all duration-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#00139E] group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-[#000B58] mb-4">Validasi Cepat</h3>
                    <p class="text-gray-500 leading-relaxed font-medium">Proses verifikasi data oleh admin BAPPEDA menjadi lebih cepat dengan sistem feedback otomatis ke OPD.</p>
                </div>

                <div class="bg-white p-12 rounded-[3rem] border border-gray-400 hover:border-[#00139E]/30 hover:shadow-2xl hover:shadow-[#000B58]/5 transition-all duration-500 group">
                    <div class="w-20 h-20 bg-[#00139E]/5 rounded-[1.5rem] flex items-center justify-center mb-10 group-hover:bg-[#00139E] group-hover:rotate-6 transition-all duration-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#00139E] group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-[#000B58] mb-4">Visualisasi Real-time</h3>
                    <p class="text-gray-500 leading-relaxed font-medium">Ubah data mentah menjadi grafik informatif untuk memudahkan pengambilan keputusan pimpinan daerah.</p>
                </div>
            </div>
        </div>
    </section>
</template>