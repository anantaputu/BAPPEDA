<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const props = defineProps({
    activities: Array,
    pagination: Object
});
</script>

<template>
    <Head title="Audit Log Aktivitas" />

    <div class="mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-5">
            <div>
                <h2 class="text-3xl font-black text-primary uppercase tracking-tight">Audit Log Aktivitas</h2>
                <p class="text-textsecondary font-medium mt-1 text-sm">Monitoring riwayat pengelolaan data</p>
            </div>
            
            <button class="bg-primary text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] transition-all shadow-xl shadow-primary/10 flex items-center gap-2 opacity-50 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export PDF (Coming Soon)
            </button>
        </div>

        <div class="bg-white rounded-xl p-8 md:p-12 shadow-sm border border-gray-400">
            <div class="space-y-0">
                <div v-for="(activity, index) in activities" :key="activity.id" class="flex gap-8 relative group">
                    <div class="flex flex-col items-center">
                        <div class="w-5 h-5 rounded-full border-4 border-white bg-secondary z-10 shadow-sm transition-transform group-hover:scale-125 duration-300"></div>
                        <div v-if="index !== activities.length - 1" class="w-0.5 h-full bg-bgsoft absolute top-5"></div>
                    </div>

                    <div class="flex-1 pb-12">
                        <div class="flex flex-wrap justify-between items-start mb-4 gap-2">
                            <div class="flex flex-col gap-2">
                                <span class="font-black text-primary text-sm uppercase tracking-tight">{{ activity.user }}</span>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-[11px] text-primary font-black uppercase tracking-widest">{{ activity.time }}</span>
                                <span class="text-[10px] text-textsecondary font-bold uppercase tracking-widest mt-0.5">{{ activity.date_full }}</span>
                            </div>
                        </div>
                        
                        <div class="bg-bgsoft rounded-xl p-6 border border-gray-100 group-hover:border-secondary/20 group-hover:bg-white transition-all duration-300 group-hover:shadow-md">
                            <p class="text-[13px] text-textsecondary font-medium leading-relaxed">
                                {{ activity.action }} 
                                <span class="text-secondary font-black italic">"{{ activity.target }}"</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div v-if="activities.length === 0" class="py-24 text-center bg-bgsoft/50 rounded-xl border-2 border-dashed border-gray-200">
                    <div class="text-gray-300 mb-6">
                        <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-xs font-black text-textsecondary/40 uppercase tracking-[0.4em]">Belum ada aktivitas terekam dalam sistem</p>
                </div>
            </div>

            <div v-if="pagination.links.length > 3" class="mt-10 flex justify-center gap-2 border-t border-bgsoft pt-10">
                <template v-for="(link, k) in pagination.links" :key="k">
                    <Link 
                        v-if="link.url" 
                        :href="link.url" 
                        v-html="link.label" 
                        class="px-5 py-2.5 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl transition-all border"
                        :class="link.active 
                            ? 'bg-primary text-white border-transparent shadow-lg shadow-primary/20 scale-105' 
                            : 'bg-white text-textsecondary border-gray-200 hover:border-secondary hover:text-secondary'"
                    />
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
.flex-1 {
    animation: slideUp 0.4s ease-out forwards;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>