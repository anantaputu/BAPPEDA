<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineOptions({ layout: AuthenticatedLayout });

const stats = [
    { label: 'Total Data', value: '150', trend: '2.4%', color: 'bg-[#4A6CF7] text-white', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' },
    { label: 'Bidang Ekonomi', value: '2250 K', trend: '3%', color: 'bg-white text-gray-900' },
    { label: 'Bidang P2M', value: '1000', trend: '0.5%', color: 'bg-white text-gray-900', down: true },
    { label: 'Bidang IK', value: '150', trend: '2.4%', color: 'bg-white text-gray-900' },
];
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="space-y-8">
        <div class="grid grid-cols-4 gap-6">
            <div v-for="s in stats" :key="s.label" 
                :class="s.color"
                class="p-6 rounded-[1.5rem] shadow-sm border border-transparent flex flex-col justify-between min-h-[140px]">
                <div class="flex items-center gap-3">
                    <div v-if="s.icon" class="p-1.5 bg-white/20 rounded-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path :d="s.icon" /></svg>
                    </div>
                    <span class="text-xs font-bold opacity-70 tracking-wide uppercase">{{ s.label }}</span>
                </div>
                <div class="flex justify-between items-end mt-4">
                    <h2 class="text-4xl font-extrabold tracking-tight">{{ s.value }}</h2>
                    <div class="flex items-center gap-1 text-[10px] font-black" :class="s.down ? 'text-red-500' : (s.color.includes('white') ? 'text-green-500' : 'text-white/80')">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path :d="s.down ? 'M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l2.293-2.293a1 1 0 011.414 0z' : 'M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z'" /></svg>
                        {{ s.trend }}
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2 bg-white p-8 rounded-[2rem] shadow-sm">
                <h3 class="font-extrabold text-gray-800 text-lg mb-8 tracking-tight">Indeks Modal Manusia (IMM)</h3>
                <div class="h-64 flex items-end justify-between px-4 border-b border-gray-100 pb-2 relative">
                    <div class="absolute -left-2 h-full flex flex-col justify-between text-[10px] font-bold text-gray-400">
                        <span>5K</span><span>3K</span><span>1K</span><span>0</span>
                    </div>
                    <div v-for="h in [45, 65, 60, 85, 80, 82, 75]" :key="h" 
                        :style="{ height: h + '%' }" 
                        class="w-8 bg-[#3DD522] rounded-t-sm transition-all hover:brightness-110 cursor-pointer"></div>
                </div>
                <div class="flex justify-between mt-4 text-[10px] font-black text-gray-400 px-4">
                    <span>1 Nov</span><span>5 Nov</span><span>10 Nov</span><span>15 Nov</span><span>20 Nov</span><span>25 Nov</span><span>30 Nov</span>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2rem] shadow-sm">
                <h3 class="font-extrabold text-gray-800 text-lg mb-8 tracking-tight">Statistik Data</h3>
                <div class="flex justify-center relative py-4">
                    <div class="w-36 h-36 rounded-full border-[20px] border-[#3B82F6] border-l-[#EAB308] border-b-[#D946EF]"></div>
                </div>
                <div class="mt-8 space-y-3">
                    <div class="flex items-center gap-3 text-[10px] font-black uppercase text-gray-500">
                        <span class="w-3 h-3 rounded-full bg-[#EAB308]"></span> Light Bulb Cover - 60%
                    </div>
                    <div class="flex items-center gap-3 text-[10px] font-black uppercase text-gray-500">
                        <span class="w-3 h-3 rounded-full bg-[#D946EF]"></span> Ashtray - 25%
                    </div>
                    <div class="flex items-center gap-3 text-[10px] font-black uppercase text-gray-500">
                        <span class="w-3 h-3 rounded-full bg-[#3B82F6]"></span> Bamboo Chair - 15%
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <div v-for="title in ['Urusan', 'Key Word']" :key="title" class="bg-white p-10 rounded-[2rem] shadow-sm">
                <h3 class="font-extrabold text-gray-900 text-xl mb-4 tracking-tight">{{ title }}</h3>
                <p class="text-gray-800 text-base font-bold leading-relaxed max-w-5xl">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                </p>
            </div>
        </div>
    </div>
</template>