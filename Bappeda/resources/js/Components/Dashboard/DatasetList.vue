<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({ title: String, items: Array, type: String });
</script>

<template>
    <div class="bg-white rounded-[3rem] p-8 shadow-sm border border-gray-400">
        <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-400">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <slot name="icon"></slot>
                {{ title }}
            </h3>
            <Link href="/cari" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua</Link>
        </div>
        
        <div class="space-y-6">
            <Link 
                v-for="(item, index) in items" 
                :key="index" 
                :href="`/dataset/${item.id || item.id_data}`" 
                class="flex items-start gap-4 group cursor-pointer"
            >
                <div :class="['w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors group-hover:bg-blue-100', type === 'popular' ? 'bg-gray-100 text-gray-500' : 'bg-blue-50 text-blue-500']">
                    <svg v-if="type === 'popular'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                
                <div>
                    <h4 class="text-sm font-bold text-gray-800 group-hover:text-[#00139E] transition-colors line-clamp-2 leading-snug mb-2">
                        {{ item.title || item.nama_indikator }}
                    </h4>
                    <div class="flex flex-wrap gap-2 items-center">
                        <span v-if="item.tags" v-for="tag in item.tags" :key="tag" :class="['text-[9px] font-bold uppercase px-2 py-0.5 rounded', type === 'popular' ? 'bg-gray-100 text-gray-500' : 'bg-blue-50 text-blue-500']">
                            {{ tag }}
                        </span>
                        <span class="text-[10px] text-gray-400 font-medium">
                            • {{ item.org || item.urusan?.nama_urusan || item.sumber }}
                        </span>
                        <span v-if="item.tahun" class="text-[10px] text-gray-400 font-medium">
                            • {{ item.tahun }}
                        </span>
                    </div>
                </div>
            </Link>
        </div>
    </div>
</template>