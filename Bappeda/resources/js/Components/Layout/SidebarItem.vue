<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    item: Object,
    isOpen: Boolean, // State dropdown dikontrol dari induk
    activeUrl: String
});

defineEmits(['toggle']);
</script>

<template>
    <div v-if="item.children" class="mb-2">
        <button @click="$emit('toggle')"
            class="w-full flex items-center justify-between px-5 py-4 text-[13px] font-black rounded-2xl transition-all duration-500 group"
            :class="isOpen 
                ? 'text-[#00139E] bg-[#00139E]/5 shadow-sm' 
                : 'text-[#A2B5CB] hover:text-[#000B58] hover:bg-gray-50'">
            
            <div class="flex items-center gap-4">
                <div class="p-2 rounded-lg transition-colors duration-500"
                    :class="isOpen ? 'bg-[#00139E]/10' : 'bg-transparent group-hover:bg-gray-200/50'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="item.icon" />
                    </svg>
                </div>
                <span class="tracking-tight">{{ item.name }}</span>
            </div>

            <svg class="w-4 h-4 transition-transform duration-500" 
                :class="isOpen ? 'rotate-90 text-[#00139E]' : 'text-[#A2B5CB]'" 
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
            </svg>
        </button>
        
        <div v-if="isOpen" class="ml-10 mt-2 border-l-2 border-gray-100 space-y-1 animate-in slide-in-from-top-2 duration-300">
            <Link v-for="child in item.children" :key="child.name" :href="child.path"
                class="block px-6 py-3 text-[12px] font-black uppercase tracking-widest transition-all duration-300 relative group"
                :class="activeUrl.startsWith(child.path) 
                    ? 'text-[#00139E]' 
                    : 'text-[#A2B5CB] hover:text-[#000B58]'">
                
                <div v-if="activeUrl.startsWith(child.path)" 
                    class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-1.5 bg-[#00139E] rounded-full -ml-[9px] shadow-[0_0_8px_rgba(0,19,158,0.5)]">
                </div>
                
                {{ child.name }}
            </Link>
        </div>
    </div>

    <Link v-else :href="item.path"
        class="flex items-center gap-4 px-4 py-4 text-[13px] font-black rounded-2xl transition-all duration-500 group"
        :class="activeUrl.startsWith(item.path) 
            ? 'text-white bg-[#00139E] shadow-xl shadow-[#00139E]/20 translate-x-1' 
            : 'text-[#A2B5CB] hover:text-[#000B58] hover:bg-gray-50 hover:translate-x-1'">
        
        <div class="p-2 rounded-lg transition-colors duration-500"
            :class="activeUrl.startsWith(item.path) ? 'bg-white/20' : 'bg-transparent group-hover:bg-gray-200/50'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="item.icon" />
            </svg>
        </div>
        <span class="tracking-tight">{{ item.name }}</span>
    </Link>
</template>