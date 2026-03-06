<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    item: Object,
    isOpen: Boolean, 
    activeUrl: String
});

defineEmits(['toggle']);
</script>

<template>
    <div v-if="item.children" class="mb-2">
        <button @click="$emit('toggle')"
            class="w-full flex items-center justify-between px-5 py-4 text-[12px] font-black rounded-xl transition-all duration-500 group uppercase tracking-wider"
            :class="isOpen 
                ? 'text-secondary bg-secondary/5' 
                : 'text-textsecondary hover:text-primary hover:bg-bgsoft'">
            
            <div class="flex items-center gap-4">
                <div class="p-2 rounded-lg transition-colors duration-500"
                    :class="isOpen ? 'bg-secondary/10' : 'bg-transparent group-hover:bg-primary/5'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="item.icon" />
                    </svg>
                </div>
                <span class="tracking-tight">{{ item.name }}</span>
            </div>

            <svg class="w-4 h-4 transition-transform duration-500" 
                :class="isOpen ? 'rotate-90 text-secondary' : 'text-textsecondary'" 
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
            </svg>
        </button>
        
        <div v-if="isOpen" class="ml-10 mt-2 border-l-2 border-bgsoft space-y-1 animate-in slide-in-from-top-2 duration-300">
            <Link v-for="child in item.children" :key="child.name" :href="child.path"
                class="block px-6 py-3 text-[11px] font-black uppercase tracking-widest transition-all duration-300 relative group"
                :class="activeUrl.startsWith(child.path) 
                    ? 'text-secondary' 
                    : 'text-textsecondary/60 hover:text-primary'">
                
                <div v-if="activeUrl.startsWith(child.path)" 
                    class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-1.5 bg-secondary rounded-full -ml-[9px] shadow-sm shadow-secondary/50">
                </div>
                
                {{ child.name }}
            </Link>
        </div>
    </div>

    <Link v-else :href="item.path"
        class="flex items-center gap-4 px-4 py-4 text-[12px] font-black rounded-xl transition-all duration-500 group uppercase tracking-wider"
        :class="activeUrl.startsWith(item.path) 
            ? 'text-white bg-primary shadow-lg shadow-primary/20 translate-x-1' 
            : 'text-textsecondary hover:text-primary hover:bg-bgsoft hover:translate-x-1'">
        
        <div class="p-2 rounded-lg transition-colors duration-500"
            :class="activeUrl.startsWith(item.path) ? 'bg-white/20' : 'bg-transparent group-hover:bg-primary/5'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="item.icon" />
            </svg>
        </div>
        <span class="tracking-tight">{{ item.name }}</span>
    </Link>
</template>