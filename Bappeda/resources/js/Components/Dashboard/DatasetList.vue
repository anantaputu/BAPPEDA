<script setup>
defineProps({ title: String, items: Array, type: String });
</script>

<template>
    <div class="bg-white rounded-[1.5rem] p-8 shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-50">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <slot name="icon"></slot>
                {{ title }}
            </h3>
            <a href="#" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua</a>
        </div>
        <div class="space-y-6">
            <div v-for="(item, index) in items" :key="index" class="flex items-start gap-4 group">
                <div :class="['w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0', type === 'popular' ? 'bg-gray-100 text-gray-500' : 'bg-blue-50 text-blue-500']">
                    <svg v-if="type === 'popular'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition line-clamp-2 leading-snug mb-2">{{ item.title }}</h4>
                    <div class="flex gap-2 items-center">
                        <span v-for="tag in item.tags" :key="tag" :class="['text-[9px] font-bold uppercase px-2 py-0.5 rounded', type === 'popular' ? 'bg-gray-100 text-gray-500' : 'bg-blue-50 text-blue-500']">{{ tag }}</span>
                        <span class="text-[10px] text-gray-400">• {{ item.org }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>