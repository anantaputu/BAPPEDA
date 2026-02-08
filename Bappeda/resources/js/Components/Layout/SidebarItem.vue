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
    <div v-if="item.children">
        <button @click="$emit('toggle')"
            class="w-full flex items-center justify-between px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300"
            :class="isOpen ? 'text-[#00139E] bg-[#00139E]/5' : 'text-[#A2B5CB] hover:text-[#000B58] hover:bg-gray-50'">
            <div class="flex items-center gap-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" /></svg>
                {{ item.name }}
            </div>
            <svg class="w-4 h-4 transition-transform duration-300" :class="isOpen ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        </button>
        
        <div v-if="isOpen" class="ml-9 mt-2 border-l-2 border-[#A2B5CB]/20 space-y-1">
            <Link v-for="child in item.children" :key="child.name" :href="child.path"
                class="block px-6 py-2 text-sm font-semibold transition-colors duration-200"
                :class="activeUrl.startsWith(child.path) ? 'text-[#00139E] font-bold' : 'text-[#A2B5CB] hover:text-[#000B58]'">
                {{ child.name }}
            </Link>
        </div>
    </div>

    <Link v-else :href="item.path"
        class="flex items-center gap-4 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300"
        :class="activeUrl.startsWith(item.path) ? 'text-white bg-[#00139E] shadow-lg shadow-[#00139E]/20' : 'text-[#A2B5CB] hover:text-[#000B58] hover:bg-gray-100'">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" /></svg>
        {{ item.name }}
    </Link>
</template>