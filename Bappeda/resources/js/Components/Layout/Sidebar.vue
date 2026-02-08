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
    <aside class="fixed left-0 top-0 z-40 w-72 bg-white rounded-[2.5rem] border border-[#A2B5CB]/30 shadow-2xl shadow-[#000B58]/5 flex flex-col p-8 h-[calc(100vh-3rem)] m-6">
        <div class="flex items-center gap-4 mb-10">
            <div class="w-12 h-12 flex-shrink-0 bg-white rounded-xl flex items-center justify-center border border-[#A2B5CB]/20 shadow-sm overflow-hidden">
                <img :src="logoPath" alt="Logo" class="w-9 h-9 object-contain">
            </div>
            <span class="text-xl font-extrabold text-[#000B58] tracking-tight italic">
                DATA<span class="text-[#00139E]">BAPPEDA</span>
            </span>
        </div>

        <div class="flex-1 overflow-y-auto space-y-8 no-scrollbar">
            <div v-for="group in menuGroups" :key="group.label">
                <p class="text-[10px] font-black text-[#A2B5CB] tracking-widest mb-4 ml-2 uppercase">
                    {{ group.label }}
                </p>
                <div class="space-y-1">
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

        <div class="mt-auto pt-6 border-t border-[#A2B5CB]/20">
            <button @click="$emit('logout')" 
                class="w-full flex items-center gap-4 text-[#A2B5CB] font-bold text-sm hover:text-[#FF1414] transition-colors px-4 py-2 group">
                <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar Sesi
            </button>
        </div>
    </aside>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>