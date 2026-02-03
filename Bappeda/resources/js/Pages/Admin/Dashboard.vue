<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({ layout: AppLayout });

const page = usePage();
// Mengambil data user yang sedang login
const user = computed(() => page.props.auth.user);

// Mapping data statistik berdasarkan tabel 'data' dan 'bidang' yang kamu miliki
const stats = [
    { label: 'Total Indikator', value: '612.917', trend: '+2,08%', color: 'bg-[#4F46E5] text-white', icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { label: 'Total Upload', value: '34.760', trend: '+12,4%', color: 'bg-white text-gray-900', icon: 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12' },
    { label: 'User Aktif', value: '14.987', trend: '-2,08%', color: 'bg-white text-gray-900', down: true, icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
    { label: 'Data Tervalidasi', value: '12.987', trend: '+12,1%', color: 'bg-white text-gray-900', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
];

// Data bar chart untuk tren data per bulan (sesuai gambar)
const monthlyTrends = [
    { month: 'Jan', val1: 40, val2: 25 }, { month: 'Feb', val1: 45, val2: 30 },
    { month: 'Mar', val1: 55, val2: 40 }, { month: 'Apr', val1: 65, val2: 50 },
    { month: 'May', val1: 40, val2: 30 }, { month: 'Jun', val1: 45, val2: 35 },
    { month: 'Jul', val1: 50, val2: 40 }
];

// Data untuk Radial Chart sebelah kanan (Bidang di BAPPEDA)
const bidangStats = [
    { name: 'Ekonomi', value: '2.487', trend: '+1,8%', color: 'text-[#4F46E5]', bg: 'bg-[#4F46E5]' },
    { name: 'P2M', value: '1.828', trend: '+2,3%', color: 'text-[#F59E0B]', bg: 'bg-[#F59E0B]' },
    { name: 'Infrastruktur', value: '1.463', trend: '-1,04%', color: 'text-[#10B981]', bg: 'bg-[#10B981]', down: true },
];
</script>

<template>
    <Head title="BAPPEDA Dashboard" />

    <div class="space-y-6">
        <div class="flex justify-between items-center mb-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Laporan</h1>
                <p class="text-sm text-gray-500">Selasa, 3 Februari 2026</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div v-for="s in stats" :key="s.label" 
                :class="s.color"
                class="p-5 rounded-3xl shadow-sm border border-gray-100 flex flex-col relative overflow-hidden group">
                <div class="flex justify-between items-start mb-4">
                    <div :class="s.color.includes('white') ? 'bg-gray-50 text-gray-600' : 'bg-white/20 text-white'" class="p-3 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="s.icon" /></svg>
                    </div>
                    <span :class="s.down ? 'text-red-500 bg-red-50' : 'text-green-500 bg-green-50'" class="px-2 py-1 rounded-full text-[10px] font-bold">
                        {{ s.trend }}
                    </span>
                </div>
                <p :class="s.color.includes('white') ? 'text-gray-400' : 'text-blue-100'" class="text-xs font-semibold mb-1">{{ s.label }}</p>
                <h2 class="text-2xl font-black tracking-tight">${{ s.value }}</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-50">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Tren Penyetoran Data</h3>
                        <p class="text-xs text-gray-400">Pantau aktivitas upload per periode</p>
                    </div>
                    <select class="text-xs border-gray-200 rounded-xl px-3 py-2 bg-gray-50 font-bold text-gray-600">
                        <option>Tahun Ini</option>
                    </select>
                </div>

                <div class="h-64 flex items-end justify-between px-2 gap-4">
                    <div v-for="d in monthlyTrends" :key="d.month" class="flex-1 flex flex-col items-center group">
                        <div class="w-full flex justify-center gap-1 items-end h-full">
                            <div :style="{ height: d.val1 + '%' }" class="w-3 bg-[#E0E7FF] rounded-full group-hover:bg-[#4F46E5] transition-colors relative">
                                <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[8px] p-1 rounded opacity-0 group-hover:opacity-100 transition">{{ d.val1 }}k</div>
                            </div>
                            <div :style="{ height: d.val2 + '%' }" class="w-3 bg-[#4F46E5] rounded-full"></div>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 mt-4 uppercase">{{ d.month }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-50 flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-gray-900">Statistik Bidang</h3>
                    <select class="text-[10px] font-bold text-gray-400 border-none bg-transparent focus:ring-0 uppercase tracking-widest">
                        <option>Hari Ini</option>
                    </select>
                </div>

                <div class="relative flex justify-center items-center py-6">
                    <svg viewBox="0 0 36 36" class="w-48 h-48 transform -rotate-90">
                        <circle cx="18" cy="18" r="16" fill="none" stroke="#F3F4F6" stroke-width="2.5"></circle>
                        <circle cx="18" cy="18" r="16" fill="none" stroke="#4F46E5" stroke-width="2.5" stroke-dasharray="75, 100" stroke-linecap="round"></circle>
                        <circle cx="18" cy="18" r="12" fill="none" stroke="#F59E0B" stroke-width="2.5" stroke-dasharray="60, 100" stroke-linecap="round"></circle>
                        <circle cx="18" cy="18" r="8" fill="none" stroke="#10B981" stroke-width="2.5" stroke-dasharray="45, 100" stroke-linecap="round"></circle>
                    </svg>
                    <div class="absolute flex flex-col items-center">
                        <span class="text-2xl font-black text-gray-900 leading-none">9.829</span>
                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest mt-1">Total Entri</span>
                    </div>
                </div>

                <div class="mt-8 space-y-4">
                    <div v-for="b in bidangStats" :key="b.name" class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div :class="b.bg" class="w-2 h-2 rounded-full"></div>
                            <span class="text-xs font-bold text-gray-500">{{ b.name }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-xs font-black text-gray-900">{{ b.value }}</span>
                            <span :class="b.down ? 'text-red-500' : 'text-green-500'" class="text-[10px] font-bold">{{ b.trend }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>