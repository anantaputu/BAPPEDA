<script setup>
import { Head, Link } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
</script>

<template>
    <Head title="Landing Page" />


        <div class="min-h-screen bg-white selection:bg-blue-100">
            <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
                <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">B</div>
                        <h1 class="text-xl font-extrabold tracking-tight text-gray-800">
                            DATA<span class="text-blue-600">BAPPEDA</span>
                        </h1>
                    </div>

                    <div class="hidden md:flex items-center space-x-8">
                        <Link href="/login" class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition">Login</Link>
                        <Link href="/register" class="bg-blue-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95">
                            Daftar Sekarang
                        </Link>
                    </div>
                </div>
            </header>

            <main>
                <section class="relative pt-20 pb-32 overflow-hidden">
                    <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[500px] h-[500px] bg-blue-50 rounded-full blur-3xl opacity-70 -z-10"></div>
                    <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-[400px] h-[400px] bg-indigo-50 rounded-full blur-3xl opacity-70 -z-10"></div>

                    <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
                        <div class="text-center lg:text-left">
                            <span class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold tracking-wide text-blue-700 uppercase bg-blue-100 rounded-full">
                                E-Governance & Transparansi
                            </span>
                            <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 leading-[1.15] mb-6">
                                Digitalisasi <br />
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Penyetoran Data</span> <br />
                                Pembangunan
                            </h2>

                            <p class="text-lg text-gray-600 mb-10 leading-relaxed max-w-xl mx-auto lg:mx-0">
                                Platform resmi BAPPEDA untuk pengelolaan, validasi,
                                dan monitoring data pembangunan daerah secara terpusat dan akuntabel.
                            </p>

                            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                                <Link href="/login" class="group bg-blue-600 text-white px-8 py-4 rounded-2xl text-lg font-bold hover:bg-blue-700 transition-all shadow-xl shadow-blue-200 flex items-center justify-center">
                                    Masuk Sistem
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </Link>

                                <Link href="/welcome" class="border-2 border-gray-200 text-gray-700 px-8 py-4 rounded-2xl text-lg font-bold hover:bg-gray-50 hover:border-blue-200 transition-all text-center">
                                    Pelajari Fitur
                                </Link>
                            </div>

                            <div class="mt-12 pt-12 border-t border-gray-100 grid grid-cols-3 gap-4">
                                <div>
                                    <div class="text-2xl font-bold text-gray-900">100%</div>
                                    <div class="text-sm text-gray-500">Data Terintegrasi</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-gray-900">Real-time</div>
                                    <div class="text-sm text-gray-500">Monitoring</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-gray-900">Aman</div>
                                    <div class="text-sm text-gray-500">Cloud Storage</div>
                                </div>
                            </div>
                        </div>

                        <div class="relative hidden lg:block">
                            <div class="relative z-10 bg-white p-4 rounded-[2rem] shadow-2xl border border-gray-100 rotate-2 hover:rotate-0 transition-transform duration-500">
                                <div class="bg-gray-50 rounded-xl p-6 border border-dashed border-gray-200">
                                    <div class="flex items-center justify-between mb-8">
                                        <div class="h-8 w-32 bg-gray-200 rounded-lg animate-pulse"></div>
                                        <div class="flex gap-2">
                                            <div class="h-8 w-8 bg-blue-100 rounded-full"></div>
                                            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
                                        </div>
                                    </div>
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="h-24 bg-blue-50 rounded-xl border border-blue-100 p-4">
                                                <div class="h-3 w-12 bg-blue-200 rounded mb-2"></div>
                                                <div class="h-6 w-20 bg-blue-600 rounded"></div>
                                            </div>
                                            <div class="h-24 bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                                                <div class="h-3 w-12 bg-gray-200 rounded mb-2"></div>
                                                <div class="h-6 w-20 bg-gray-400 rounded"></div>
                                            </div>
                                        </div>
                                        <div class="h-40 bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                                             <div class="flex items-end gap-2 h-full">
                                                 <div class="w-full bg-blue-100 h-[40%] rounded-t"></div>
                                                 <div class="w-full bg-blue-200 h-[60%] rounded-t"></div>
                                                 <div class="w-full bg-blue-400 h-[90%] rounded-t"></div>
                                                 <div class="w-full bg-blue-600 h-[70%] rounded-t"></div>
                                             </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="absolute -inset-4 bg-gradient-to-tr from-blue-100 to-indigo-100 rounded-[3rem] -z-10 blur-2xl opacity-50"></div>
                        </div>
                    </div>
                </section>

                <section class="py-24 bg-gray-50">
                    <div class="max-w-7xl mx-auto px-6 text-center">
                        <h3 class="text-3xl font-bold text-gray-900 mb-12">Kenapa Memilih Sistem Kami?</h3>
                        <div class="grid md:grid-cols-3 gap-8">
                            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mx-auto mb-6 text-2xl">🔒</div>
                                <h4 class="text-xl font-bold mb-3">Keamanan Tinggi</h4>
                                <p class="text-gray-600">Data Anda dilindungi dengan enkripsi standar industri dan backup berkala.</p>
                            </div>
                            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mx-auto mb-6 text-2xl">⚡</div>
                                <h4 class="text-xl font-bold mb-3">Penyetoran Cepat</h4>
                                <p class="text-gray-600">Alur kerja yang ringkas memudahkan OPD mengirim data dalam hitungan menit.</p>
                            </div>
                            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mx-auto mb-6 text-2xl">📊</div>
                                <h4 class="text-xl font-bold mb-3">Analisis Data</h4>
                                <p class="text-gray-600">Dashboard visual memudahkan pemantauan progres pembangunan secara instan.</p>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

                        <footer class="bg-white border-t border-gray-100 py-12">
                <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="text-gray-400 text-sm">
                        © {{ new Date().getFullYear() }}
                        <span class="font-bold text-gray-600 uppercase">BAPPEDA</span>
                        — Sistem Penyetoran Data.
                    </div>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-blue-600 transition text-sm">Kebijakan Privasi</a>
                        <a href="#" class="text-gray-400 hover:text-blue-600 transition text-sm">Bantuan</a>
                    </div>
                </div>
            </footer>
        </div>
</template>
