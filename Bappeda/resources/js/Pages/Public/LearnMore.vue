<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const steps = [
    {
        title: 'Login & Akses Dashboard',
        description: 'OPD masuk menggunakan akun resmi yang telah terdaftar untuk mengakses panel input data khusus sektoral.',
        icon: 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1'
    },
    {
        title: 'Input Data Indikator',
        description: 'Pilih kategori data, kemudian masukkan nilai realisasi berdasarkan periode yang ditentukan (bulanan/tahunan).',
        icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'
    },
    {
        title: 'Verifikasi & Validasi',
        description: 'Data yang telah diinput akan masuk ke tahap verifikasi oleh Admin Bappeda untuk memastikan keakuratan informasi.',
        icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
    }
];

const contactForm = useForm({
    name: '',
    message: '',
});

const submitContact = () => {
    contactForm.post(route('public.contact.store'), {
        preserveScroll: true,
        onSuccess: () => contactForm.reset(),
    });
};
</script>

<template>
    <Head title="Panduan Penggunaan - Satu Data" />

    <section class="relative mt-20 py-24 overflow-hidden w-full bg-white">
        <div class="absolute top-0 left-0 -translate-y-1/2 -translate-x-1/4 w-[600px] h-[600px] bg-primary/5 rounded-full blur-3xl -z-10"></div>
        
        <div class="max-w-[80%] mx-auto text-center">
            <span class="inline-block px-4 py-1.5 mb-6 text-sm font-bold text-integritas bg-integritas/10 rounded-full border border-integritas/40 tracking-wide uppercase">
                Pusat Bantuan
            </span>
            <h1 class="text-5xl lg:text-7xl font-black text-primary leading-[1.1] mb-6">
                Cara Penggunaan <br /> 
                <span class="text-secondary">Sistem Satu Data</span>
            </h1>
            <p class="text-lg text-textsecondary max-w-2xl mx-auto font-medium leading-relaxed">
                Panduan lengkap bagi OPD dan Inputer untuk mengelola data kinerja pembangunan secara efisien dan akurat.
            </p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="max-w-[80%] mx-auto">
            <div class="grid lg:grid-cols-3 gap-12">
                <div v-for="(step, index) in steps" :key="index" class="relative group">
                    <div class="bg-white p-12 rounded-xl border border-gray-400 h-full hover:border-secondary transition-all duration-300 shadow-xl shadow-primary/5">
                        <div class="text-6xl font-black text-primary/5 absolute top-8 right-10 group-hover:text-secondary/10 transition-colors">
                            0{{ index + 1 }}
                        </div>
                        <div class="w-20 h-20 bg-primary rounded-xl flex items-center justify-center text-white mb-10 border border-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="step.icon" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-black text-primary mb-4">{{ step.title }}</h3>
                        <p class="text-textsecondary leading-relaxed font-medium">{{ step.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-32 bg-white">
        <div class="max-w-[80%] mx-auto">
            <div class="bg-primary rounded-xl p-12 lg:p-20 overflow-hidden relative border border-gray-400">
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-secondary/20 rounded-full blur-[120px] translate-x-1/2 translate-y-1/2"></div>

                <div class="grid lg:grid-cols-2 gap-16 items-center relative z-10">
                    <div>
                        <h2 class="text-4xl lg:text-5xl font-black text-white mb-8 leading-tight">
                            Kelola Data Anda <br/> Dalam Berbagai Format
                        </h2>
                        <p class="text-white/70 text-lg mb-10 leading-relaxed font-medium">
                            Setelah data divalidasi, Anda dapat menggunakan fitur ekspor untuk kebutuhan pelaporan internal maupun presentasi pimpinan hanya dengan satu klik.
                        </p>
                        
                        <div class="space-y-8">
                            <div class="flex items-start gap-6">
                                <div class="w-14 h-14 bg-white/10 rounded-xl flex items-center justify-center shrink-0 border border-white/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-white font-black text-xl mb-1">Export ke PDF</h4>
                                    <p class="text-white/50 font-medium">Cocok untuk laporan resmi yang siap cetak dengan tata letak yang rapi.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-6">
                                <div class="w-14 h-14 bg-white/10 rounded-xl flex items-center justify-center shrink-0 border border-white/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-white font-black text-xl mb-1">Export ke Excel (XLSX)</h4>
                                    <p class="text-white/50 font-medium">Memudahkan pengolahan data lebih lanjut menggunakan rumus atau pivot table.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hidden lg:block">
                        <div class="bg-white/5 border border-white/10 p-10 rounded-xl backdrop-blur-sm">
                             <div class="space-y-4">
                                <div v-for="i in 4" :key="i" class="h-14 bg-white/10 rounded-xl w-full flex items-center px-6 justify-between border border-white/5">
                                    <div class="h-2 bg-white/20 rounded-full w-1/3"></div>
                                    <div class="h-2 bg-white/20 rounded-full w-12"></div>
                                </div>
                                <div class="pt-8 flex justify-end gap-4">
                                    <div class="px-6 py-3 bg-white/10 text-white rounded-xl text-xs font-black uppercase tracking-widest border border-white/20">PDF</div>
                                    <div class="px-6 py-3 bg-secondary text-white rounded-xl text-xs font-black uppercase tracking-widest">Excel</div>
                                </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pb-32 bg-white">
        <div class="max-w-3xl mx-auto px-6">
            <div class="bg-white border border-gray-400 rounded-xl p-8 md:p-12 shadow-xl shadow-primary/5 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                
                <div class="text-center mb-12">
                    <h3 class="text-3xl font-black text-primary mb-4">Berikan Masukan</h3>
                    <p class="text-textsecondary font-medium">Kirimkan pesan kepada tim teknis BAPPEDA jika Anda mengalami kendala akses atau data.</p>
                </div>

                <form @submit.prevent="submitContact" class="space-y-8">
                    <div>
                        <label class="block text-xs font-black text-primary uppercase tracking-[0.2em] mb-3 ml-1">Nama Lengkap</label>
                        <input type="text" v-model="contactForm.name" placeholder="Masukkan nama..." 
                            class="w-full px-6 py-4 rounded-xl border border-gray-400 focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none font-medium text-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-black text-primary uppercase tracking-[0.2em] mb-3 ml-1">Pesan Anda</label>
                        <textarea v-model="contactForm.message" rows="4" placeholder="Apa kendala Anda?" 
                            class="w-full px-6 py-4 rounded-xl border border-gray-400 focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none font-medium resize-none text-primary"></textarea>
                    </div>

                    <div class="text-center pt-4">
                        <button type="submit" :disabled="contactForm.processing"
                            class="bg-primary text-white px-12 py-4 rounded-xl text-lg font-black hover:bg-secondary transition-all inline-flex items-center gap-3 disabled:opacity-50 shadow-lg shadow-primary/20">
                            <span>{{ contactForm.processing ? 'MENGIRIM...' : 'KIRIM MASUKKAN' }}</span>
                        </button>
                        
                        <div v-if="$page.props.flash.success" class="mt-6 p-4 bg-integritas/10 text-integritas border border-integritas/20 rounded-xl font-bold text-sm">
                            {{ $page.props.flash.success }}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</template>