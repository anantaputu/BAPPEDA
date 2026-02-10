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
    <div class="bg-white rounded-[1.5rem] p-6 shadow-sm border border-gray-400 hover:shadow-md transition flex flex-col justify-between h-48">
        <div>
            <div class="flex items-center gap-4 mb-4">
                <div :class="['w-12 h-12 rounded-xl flex items-center justify-center', colors[color].bg, colors[color].text]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icon" />
                    </svg>
                </div>
                <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-wider">{{ label }}</h3>
            </div>
            <p class="text-4xl font-black text-gray-800">{{ value }}</p>
        </div>
        <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden mt-4">
            <div :class="['h-full rounded-full', colors[color].bar]" :style="{ width: progress + '%' }"></div>
        </div>
    </div>
</template>