<script setup>
import { computed } from 'vue';
import IconifyIcon from '@/Components/Base/IconifyIcon.vue';

const props = defineProps({
    label: String,
    value: [String, Number],
    icon: String,
    color: String,
    progress: Number,
    colors: Object
});

const isLegacyPathIcon = computed(() => typeof props.icon === 'string' && /^[Mm][\d\s.,-]/.test(props.icon.trim()));
</script>

<template>
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-400 h-40">
        <div class="flex items-center gap-4 mb-2">
            <div :class="[
                'w-12 h-12 rounded-xl flex items-center justify-center', 
                colors[color].bg, 
                colors[color].text
            ]">
                <svg v-if="isLegacyPathIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="icon" />
                </svg>
                <IconifyIcon v-else :icon="icon" width="24" height="24" />
            </div>
            <h3 class="text-[11px] font-black text-textsecondary uppercase tracking-[0.15em] leading-tight">
                {{ label }}
            </h3>
        </div>

        <div class="flex-1 flex items-center justify-center">
            <p class="text-5xl font-black text-primary tracking-tighter">
                {{ value }}
            </p>
        </div>
    </div>
</template>
