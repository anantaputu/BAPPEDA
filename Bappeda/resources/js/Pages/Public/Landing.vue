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

    <section class="relative mt-20 w-full overflow-hidden bg-white py-20"> 
        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[600px] h-[600px] bg-primary/5 rounded-full blur-3xl -z-10"></div>
        
        <div class="max-w-[80%] mx-auto grid lg:grid-cols-2 gap-16 items-center">
            <div class="text-center lg:text-left">
                <span class="ui-chip mb-6 border-integritas/40 bg-integritas/10 px-4 py-1.5 text-integritas">
                    Sistem Data Bappeda
                </span>
                <h1 class="mb-6 text-[3.25rem] font-black leading-[0.98] tracking-tight text-primary lg:text-[5.25rem]">
                    Satu Data <br />
                    <span class="text-secondary">Pembangunan</span> <br />
                    Terintegrasi
                </h1>
                <p class="ui-body-lg mx-auto mb-8 max-w-xl lg:mx-0">
                    Platform digital BAPPEDA Provinsi Nusa Tenggara Barat untuk mempermudah penyetoran, validasi, dan monitoring data kinerja pembangunan daerah secara real-time.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <Link href="/learnmore" class="inline-flex items-center justify-center bg-primary px-8 py-4 text-white transition-all duration-300 hover:bg-secondary ui-chip" style="border-radius: var(--radius-soft); font-size: var(--font-body);">
                        Pelajari lebih lanjut
                    </Link>
                </div>
            </div>

            <div class="hidden lg:block relative">
                <div class="ui-shell relative overflow-hidden border border-slate-200 shadow-2xl shadow-slate-200/50">
                    <img 
                        src="/images/profile.png" 
                        alt="Dashboard Sistem Bappeda"
                        class="w-full h-[500px] object-cover"
                    />

                    <!-- Optional overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/30 to-transparent"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="pt-32 pb-6 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <h2 class="ui-title-lg mb-6 md:text-[4.625rem]">Layanan Unggulan</h2>
                <p class="ui-body-lg">Dirancang untuk memudahkan koordinasi data antar instansi di lingkungan Pemerintah Provinsi NTB.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-12 mb-16">
                <div class="ui-panel p-12" style="border-radius: var(--radius-panel);">
                    <div class="mb-10 flex h-20 w-20 items-center justify-center border border-slate-300 bg-integritas/20" style="border-radius: var(--radius-soft);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-integritas" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="ui-title-sm mb-4">Repository Data</h3>
                    <p class="ui-body">Pusat penyimpanan seluruh dokumen dan data pembangunan daerah yang terorganisir per tahun dan sektor.</p>
                </div>

                <div class="ui-panel p-12" style="border-radius: var(--radius-panel);">
                    <div class="mb-10 flex h-20 w-20 items-center justify-center border border-slate-300 bg-inovasi/20" style="border-radius: var(--radius-soft);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-inovasi group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="ui-title-sm mb-4">Validasi Cepat</h3>
                    <p class="ui-body">Proses verifikasi data oleh admin BAPPEDA menjadi lebih cepat dengan sistem feedback otomatis ke OPD.</p>
                </div>

                <div class="ui-panel p-12" style="border-radius: var(--radius-panel);">
                    <div class="mb-10 flex h-20 w-20 items-center justify-center border border-slate-300 bg-profesional/20" style="border-radius: var(--radius-soft);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-profesional" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="ui-title-sm mb-4">Visualisasi Real-time</h3>
                    <p class="ui-body">Ubah data mentah menjadi grafik informatif untuk memudahkan pengambilan keputusan pimpinan daerah.</p>
                </div>
            </div>
        </div>
    </section>
</template>
