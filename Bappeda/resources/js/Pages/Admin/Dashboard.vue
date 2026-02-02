<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Set Layout secara persisten agar Sidebar tetap melayang
defineOptions({ layout: AppLayout });

const page = usePage();
const user = computed(() => page.props.auth.user);

// Data Statistik untuk Cards
const stats = [
    { label: 'Total Data', value: '150', trend: '2.4%', color: 'bg-[#4A6CF7] text-white', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' },
    { label: 'Bidang Ekonomi', value: '2.250', trend: '3%', color: 'bg-white text-gray-900' },
    { label: 'Bidang P2M', value: '1.000', trend: '0.5%', color: 'bg-white text-gray-900', down: true },
    { label: 'Bidang IK', value: '150', trend: '2.4%', color: 'bg-white text-gray-900' },
];

// Data Grafik IMM (Indeks Modal Manusia)
const immData = [45, 65, 60, 85, 80, 82, 75];
const months = ['1 Nov', '5 Nov', '10 Nov', '15 Nov', '20 Nov', '25 Nov', '30 Nov'];
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div v-for="s in stats" :key="s.label" 
                :class="s.color"
                class="p-6 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col justify-between min-h-[160px] transition-transform hover:scale-[1.02]">
                <div class="flex items-center gap-3">
                    <div v-if="s.icon" class="p-2 bg-white/20 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="s.icon" /></svg>
                    </div>
                    <span class="text-[10px] font-black opacity-70 tracking-widest uppercase">{{ s.label }}</span>
                </div>
                <div class="flex justify-between items-end mt-4">
                    <h2 class="text-4xl font-black tracking-tight">{{ s.value }}</h2>
                    <div class="flex items-center gap-1 text-[10px] font-black px-2 py-1 rounded-lg" 
                        :class="s.down ? 'bg-red-50 text-red-500' : (s.color.includes('white') ? 'bg-green-50 text-green-500' : 'bg-white/20 text-white')">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path :d="s.down ? 'M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l2.293-2.293a1 1 0 011.414 0z' : 'M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z'" />
                        </svg>
                        {{ s.trend }}
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-50">
                <div class="flex justify-between items-start mb-10">
                    <div>
                        <h3 class="font-extrabold text-gray-900 text-xl tracking-tight">Indeks Modal Manusia (IMM)</h3>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Tren Capaian November 2025</p>
                    </div>
                </div>
                
                <div class="h-64 flex items-end justify-between px-6 border-b border-gray-100 pb-2 relative">
                    <div class="absolute -left-2 h-full flex flex-col justify-between text-[10px] font-bold text-gray-300">
                        <span>100%</span><span>75%</span><span>50%</span><span>25%</span><span>0%</span>
                    </div>
                    <div v-for="(h, idx) in immData" :key="idx" 
                        :style="{ height: h + '%' }" 
                        class="w-10 bg-[#3DD522] rounded-t-lg transition-all hover:bg-green-400 cursor-pointer group relative">
                        <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition shadow-xl font-bold">
                            {{ h }}%
                        </div>
                    </div>
                </div>
                <div class="flex justify-between mt-4 text-[10px] font-black text-gray-400 px-6">
                    <span v-for="m in months" :key="m">{{ m }}</span>
                </div>
            </div>

            <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-50 flex flex-col items-center">
                <div class="w-full mb-8">
                    <h3 class="font-extrabold text-gray-900 text-xl tracking-tight">Statistik Data</h3>
                </div>
                
                <div class="relative flex items-center justify-center py-6">
                    <div class="w-40 h-40 rounded-full border-[24px] border-[#3B82F6] border-l-[#EAB308] border-b-[#D946EF] shadow-inner"></div>
                    <div class="absolute flex flex-col items-center">
                        <span class="text-2xl font-black text-gray-900 leading-none">85%</span>
                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Valid</span>
                    </div>
                </div>

                <div class="mt-10 w-full space-y-4">
                    <div v-for="l in [
                        { label: 'Ekonomi', val: '60%', color: 'bg-[#EAB308]' },
                        { label: 'P2M', val: '25%', color: 'bg-[#D946EF]' },
                        { label: 'IK', val: '15%', color: 'bg-[#3B82F6]' }
                    ]" :key="l.label" class="flex items-center justify-between group cursor-default">
                        <div class="flex items-center gap-3">
                            <span :class="l.color" class="w-2.5 h-2.5 rounded-full shadow-sm"></span>
                            <span class="text-[10px] font-black uppercase text-gray-500 tracking-wider group-hover:text-gray-900 transition">{{ l.label }}</span>
                        </div>
                        <span class="text-xs font-black text-gray-900">{{ l.val }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div v-for="title in ['Urusan Terpopuler', 'Kata Kunci Pencarian']" :key="title" 
                class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-50 transition-all hover:shadow-md">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-1.5 h-8 bg-[#4A6CF7] rounded-full"></div>
                    <h3 class="font-extrabold text-gray-900 text-xl tracking-tight">{{ title }}</h3>
                </div>
                <p class="text-gray-500 text-sm font-medium leading-relaxed">
                    Data kinerja pembangunan yang paling sering diakses dan diperbarui dalam 30 hari terakhir mencakup sektor pendidikan, kesehatan, dan infrastruktur strategis daerah.
                </p>
                <div class="mt-6 flex flex-wrap gap-2">
                    <span v-for="tag in ['SDGs', 'Infrastruktur', 'IK', 'Ekonomi']" :key="tag" 
                        class="px-3 py-1 bg-gray-50 text-gray-400 text-[9px] font-black uppercase rounded-lg border border-gray-100">
                        #{{ tag }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>