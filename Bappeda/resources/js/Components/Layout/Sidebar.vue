<script setup>
import { Link } from '@inertiajs/vue3';
import SidebarItem from './SidebarItem.vue';

defineProps({
    role: String,
    menuGroups: Array,
    activeUrl: String,
    openMasterData: Boolean,
    logoPath: String
});

defineEmits(['toggleMasterData', 'logout']);
</script>

<template>
    <aside class="fixed left-0 top-0 z-40 w-90 bg-white rounded-[3rem] border border-gray-400 shadow-2xl shadow-[#000B58]/5 flex flex-col p-10 h-[calc(100vh-4rem)] m-8 transition-all duration-500">
        
        <div class="flex items-center gap-5 mb-12 border-b border-gray-100 pb-8">
            <div class="w-14 h-14 flex-shrink-0 bg-white rounded-2xl flex items-center justify-center border border-gray-300 shadow-sm overflow-hidden p-2 group hover:rotate-6 transition-transform duration-500">
                <img :src="logoPath" alt="Logo" class="w-10 h-10 object-contain">
            </div>
            <div class="flex flex-col">
                <span class="text-xl font-black text-[#000B58] leading-none tracking-tighter">
                    DATA<span class="text-[#00139E]">BAPPEDA</span>
                </span>
                <span class="text-[9px] font-black text-[#A2B5CB] uppercase tracking-[0.2em] mt-1">Sistem Terintegrasi</span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto space-y-10 no-scrollbar">
            <div v-for="group in menuGroups" :key="group.label">
                <p class="text-[10px] font-black text-[#A2B5CB] tracking-[0.2em] mb-6 ml-4 uppercase">
                    {{ group.label }}
                </p>
                <div class="space-y-2">
                    <SidebarItem 
                        v-for="item in group.items" :key="item.name"
                        :item="item"
                        :isOpen="item.name === 'Master Data' ? openMasterData : false"
                        :activeUrl="activeUrl"
                        @toggle="$emit('toggleMasterData')"
                    />
                </div>
            </div>
        </div>

        <div class="mt-auto pt-8 border-t border-gray-100">
            <button @click="$emit('logout')" 
                class="w-full flex items-center gap-4 text-[#A2B5CB] font-black text-[11px] uppercase tracking-widest hover:text-[#FF1414] transition-all duration-300 px-5 py-4 rounded-2xl hover:bg-[#FF1414]/5 group">
                <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar Sesi
            </button>
        </div>
    </aside>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

/* Pastikan SidebarItem juga menggunakan font-black dan radius besar di file komponennya */
</style>