<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({ layout: AppLayout });

const page = usePage();
const user = computed(() => page.props.auth.user);

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalTugas: 12,
            pendingMapping: 3,
            suksesUpload: 45
        })
    },
    recentUploads: Array
});

// Mapping warna status sesuai tabel data_uploads
const statusColor = (status) => {
    const colors = {
        'processing': 'bg-amber-50 text-amber-600 border-amber-100',
        'completed': 'bg-green-50 text-green-600 border-green-100',
        'failed': 'bg-red-50 text-red-600 border-red-100'
    };
    return colors[status] || 'bg-gray-50 text-gray-600 border-gray-100';
};

// Stats untuk Inputer (Tanpa data User/Bidang global)
const inputerStats = computed(() => [
    { label: 'Total Indikator', value: props.stats.totalTugas, icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', color: 'text-blue-600' },
    { label: 'Perlu Mapping', value: props.stats.pendingMapping, icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', color: 'text-amber-500' },
    { label: 'Sukses Upload', value: props.stats.suksesUpload, icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', color: 'text-green-500' },
]);
</script>

<template>
    <Head title="Inputer Dashboard" />

    <div class="space-y-8">
        <div class="bg-[#4F46E5] p-10 rounded-[2.5rem] text-white relative overflow-hidden shadow-2xl shadow-indigo-200">
            <div class="relative z-10">
                <span class="bg-white/20 text-xs font-bold px-3 py-1 rounded-full backdrop-blur-md uppercase tracking-widest mb-4 inline-block">
                    Role: Inputer Data
                </span>
                <h1 class="text-4xl font-black tracking-tight mb-2">
                    Halo, {{ user?.nama_depan }}! 
                </h1>
                <p class="text-indigo-100 font-medium max-w-md leading-relaxed">
                    Sistem mendeteksi <span class="text-white font-bold">{{ stats.pendingMapping }} data</span> yang belum selesai dipetakan. Segera lengkapi untuk validasi BAPPEDA.
                </p>
                <Link href="/input-data" class="inline-flex items-center gap-2 mt-8 bg-white text-[#4F46E5] px-8 py-4 rounded-2xl font-black text-sm hover:scale-105 transition-all shadow-lg active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    MULAI INPUT DATA BARU
                </Link>
            </div>
            <div class="absolute top-0 right-0 w-80 h-80 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/2 w-40 h-40 bg-indigo-400/20 rounded-full blur-2xl"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div v-for="s in inputerStats" :key="s.label" 
                class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-6 group hover:border-indigo-200 transition-all">
                <div :class="s.color" class="p-4 bg-gray-50 rounded-2xl group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="s.icon" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ s.label }}</p>
                    <h2 class="text-3xl font-black text-gray-900">{{ s.value }}</h2>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 overflow-hidden">
            <div class="flex justify-between items-center mb-10">
                <div>
                    <h3 class="font-extrabold text-gray-900 text-xl tracking-tight">Riwayat Aktivitas</h3>
                    <p class="text-xs text-gray-400 font-medium">Data yang Anda kelola dalam 30 hari terakhir</p>
                </div>
                <Link href="/input-data/history" class="bg-gray-50 text-gray-900 px-5 py-2 rounded-xl text-xs font-bold border border-gray-200 hover:bg-gray-100 transition">
                    Lihat Semua
                </Link>
            </div>

            <div class="space-y-3">
                <div v-for="i in 5" :key="i" class="flex items-center justify-between p-5 bg-white rounded-3xl hover:bg-indigo-50/30 transition-all border border-transparent hover:border-indigo-100 group">
                    <div class="flex items-center gap-5">
                        <div class="w-12 h-12 bg-indigo-50 text-[#4F46E5] rounded-2xl flex items-center justify-center group-hover:bg-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">Update Capaian Indikator - Sektor Pendidikan</h4>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">Format: XLSX</span>
                                <span class="text-gray-200 text-xs">•</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">2 Jam yang lalu</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-6">
                        <span class="px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider border" :class="statusColor(i === 1 ? 'processing' : 'completed')">
                            {{ i === 1 ? 'Processing' : 'Completed' }}
                        </span>
                        <Link href="#" class="w-10 h-10 rounded-xl flex items-center justify-center bg-gray-50 text-gray-400 group-hover:bg-white group-hover:text-indigo-600 border border-transparent group-hover:border-indigo-100 transition-all shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>