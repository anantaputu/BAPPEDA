<script setup>
import { Link } from '@inertiajs/vue3';
import IconifyIcon from '@/Components/Base/IconifyIcon.vue';

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
            class="group flex w-full items-center justify-between px-4 py-4 text-[0.82rem] font-black uppercase tracking-[0.14em] transition-all duration-500"
            style="border-radius: var(--radius-soft);"
            :class="isOpen 
                ? 'text-secondary bg-secondary/5' 
                : 'text-textsecondary hover:text-primary hover:bg-bgsoft'">
            
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center transition-colors duration-500"
                    style="border-radius: 0.7rem;"
                    :class="isOpen ? 'bg-secondary/10' : 'bg-transparent group-hover:bg-primary/5'">
                    <IconifyIcon :icon="item.icon" width="20" height="20" />
                </div>
                <span class="tracking-tight">{{ item.name }}</span>
            </div>

            <IconifyIcon
                icon="solar:alt-arrow-right-bold"
                width="16"
                height="16"
                class="transition-transform duration-500"
                :class="isOpen ? 'rotate-90 text-secondary' : 'text-textsecondary'" 
            />
        </button>
        
        <div v-if="isOpen" class="ml-4 mt-2 space-y-1 border-l-2 border-bgsoft pl-4 animate-in slide-in-from-top-2 duration-300">
            <Link v-for="child in item.children" :key="child.name" :href="child.path"
                class="group relative flex items-center gap-3 px-3 py-3 text-[0.76rem] font-black uppercase tracking-[0.14em] transition-all duration-300"
                style="border-radius: 0.7rem;"
                :class="activeUrl.startsWith(child.path) 
                    ? 'bg-secondary/5 text-secondary' 
                    : 'text-textsecondary/70 hover:bg-bgsoft hover:text-primary'">
                
                <div v-if="activeUrl.startsWith(child.path)" 
                    class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-1.5 bg-secondary rounded-full -ml-[9px] shadow-sm shadow-secondary/50">
                </div>

                <div v-if="child.icon" class="flex h-8 w-8 items-center justify-center bg-white/70" style="border-radius: 0.6rem;">
                    <IconifyIcon :icon="child.icon" width="16" height="16" />
                </div>
                {{ child.name }}
            </Link>
        </div>
    </div>

    <Link v-else :href="item.path"
        class="group flex items-center gap-4 px-4 py-4 text-[0.82rem] font-black uppercase tracking-[0.14em] transition-all duration-500"
        style="border-radius: var(--radius-soft);"
        :class="activeUrl.startsWith(item.path) 
            ? 'text-white bg-primary shadow-lg shadow-primary/20 translate-x-1' 
            : 'text-textsecondary hover:text-primary hover:bg-bgsoft hover:translate-x-1'">
        
        <div class="flex h-10 w-10 items-center justify-center transition-colors duration-500"
            style="border-radius: 0.7rem;"
            :class="activeUrl.startsWith(item.path) ? 'bg-white/20' : 'bg-transparent group-hover:bg-primary/5'">
            <IconifyIcon :icon="item.icon" width="20" height="20" />
        </div>
        <span class="tracking-tight">{{ item.name }}</span>
    </Link>
</template>
