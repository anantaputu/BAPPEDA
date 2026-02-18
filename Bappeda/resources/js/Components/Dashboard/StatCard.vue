<script setup>
defineProps({
    label: String,
    value: [String, Number],
    icon: String,
    color: String,
    progress: Number,
    colors: Object
});
</script>

<template>
    <div class="bg-white rounded-[3rem] p-6 shadow-sm border border-gray-400 hover:shadow-md transition flex flex-col h-48">
        <div class="flex items-center gap-4 mb-2">
            <div :class="['w-10 h-10 rounded-xl flex items-center justify-center shrink-0', colors[color].bg, colors[color].text]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icon" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800 tracking-tight">{{ label }}</h3>
        </div>

        <div class="flex-1 flex items-center justify-center">
            <p class="text-5xl font-black text-gray-800 tracking-tight">
                {{ value }}
            </p>
        </div>

        <div class="h-2"></div>
    </div>
</template>