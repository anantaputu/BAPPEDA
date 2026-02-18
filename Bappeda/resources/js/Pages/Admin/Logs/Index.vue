<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const props = defineProps({
    activities: Array,
    pagination: Object
});

const getStatusClass = (status) => {
    const map = {
        // Status valid disesuaikan ke Biru Royal agar searah dengan branding
        'valid': 'bg-blue-50 text-[#00139E] border-blue-100',
        'pending': 'bg-amber-50 text-amber-600 border-amber-100',
        'rejected': 'bg-rose-50 text-rose-600 border-rose-100',
    };
    return map[status] || 'bg-gray-50 text-gray-500 border-gray-100';
};
</script>

<template>
    <Head title="Audit Log Aktivitas" />

    <div class="mx-auto px-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div>
                <h2 class="text-3xl font-black text-[#000B58] uppercase tracking-tight">Audit Log Aktivitas</h2>
                <p class="text-gray-400 font-medium mt-1 italic text-sm">Monitoring riwayat pengelolaan data pembangunan daerah secara transparan.</p>
            </div>
            
            <button class="bg-[#000B58] text-white px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-[#00139E] transition-all shadow-xl shadow-blue-900/10 flex items-center gap-2 opacity-50 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export PDF (Coming Soon)
            </button>
        </div>

        <div class="bg-white rounded-[2.5rem] p-8 md:p-12 shadow-2xl shadow-gray-100 border border-gray-400">
            <div class="space-y-0">
                <div v-for="(activity, index) in activities" :key="activity.id" class="flex gap-8 relative group">
                    <div class="flex flex-col items-center">
                        <div class="w-5 h-5 rounded-full border-4 border-white bg-[#00139E] z-10 shadow-md transition-transform group-hover:scale-125"></div>
                        <div v-if="index !== activities.length - 1" class="w-0.5 h-full bg-gray-100 absolute top-5"></div>
                    </div>

                    <div class="flex-1 pb-12">
                        <div class="flex flex-wrap justify-between items-center mb-4 gap-2">
                            <div class="flex items-center gap-3">
                                <span class="font-black text-[#000B58] text-sm uppercase tracking-tight">{{ activity.user }}</span>
                                <span :class="['text-[9px] px-3 py-1 rounded-lg font-black uppercase tracking-widest border shadow-sm', getStatusClass(activity.status)]">
                                    {{ activity.status }}
                                </span>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-[11px] text-[#000B58] font-black uppercase tracking-widest">{{ activity.time }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">{{ activity.date_full }}</span>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50/50 rounded-2xl p-6 border border-blue-50 group-hover:border-blue-200 group-hover:bg-blue-50 transition-all duration-300">
                            <p class="text-[13px] text-gray-600 font-medium leading-relaxed">
                                {{ activity.action }} 
                                <span class="text-[#00139E] font-black italic">"{{ activity.target }}"</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div v-if="activities.length === 0" class="py-20 text-center">
                    <div class="text-gray-200 mb-6">
                        <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-xs font-black text-gray-300 uppercase tracking-[0.4em]">Belum ada aktivitas terekam dalam sistem</p>
                </div>
            </div>

            <div v-if="pagination.links.length > 3" class="mt-10 flex justify-center gap-2">
                <template v-for="(link, k) in pagination.links" :key="k">
                    <Link 
                        v-if="link.url" 
                        :href="link.url" 
                        v-html="link.label" 
                        class="px-5 py-2.5 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl transition-all border"
                        :class="link.active 
                            ? 'bg-[#000B58] text-white border-transparent shadow-lg shadow-blue-900/20 scale-110' 
                            : 'bg-white text-gray-400 border-gray-200 hover:border-[#00139E] hover:text-[#00139E]'"
                    />
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
.flex-1 {
    animation: slideUp 0.4s ease-out;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>