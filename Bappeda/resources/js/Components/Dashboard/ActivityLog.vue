<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({ 
    activities: {
        type: Array,
        default: () => []
    }
});

const getStatusClass = (status) => {
    const map = {
        // Status disesuaikan agar tetap profesional
        'valid': 'bg-blue-50 text-[#00139E] border-blue-100', // Gunakan Biru Royal untuk valid
        'pending': 'bg-amber-50 text-amber-600 border-amber-100',
        'rejected': 'bg-rose-50 text-rose-600 border-rose-100',
    };
    return map[status] || 'bg-gray-50 text-gray-500 border-gray-100';
};
</script>

<template>
    <div class="bg-white rounded-[2.5rem] p-10 shadow-2xl shadow-gray-100/50 border border-gray-400">
        <div class="flex items-center justify-between mb-10">
            <div class="flex items-center gap-3">
                <div class="w-2 h-6 bg-[#00139E] rounded-full"></div>
                <h3 class="text-xl font-black text-[#000B58] uppercase tracking-tight">Log Aktivitas Terbaru</h3>
            </div>
            <Link 
                href="/admin/logs" 
                class="text-[10px] font-black text-[#00139E] uppercase tracking-[0.2em] hover:bg-blue-50 px-4 py-2 rounded-xl transition-colors border border-transparent hover:border-blue-100"
            >
                Lihat Semua
            </Link>
        </div>

        <div class="space-y-2">
            <div v-for="(activity, index) in activities" :key="activity.id || index" class="flex gap-6 relative group">
                <div class="flex flex-col items-center">
                    <div class="w-4 h-4 rounded-full border-4 border-white bg-[#00139E] z-10 shadow-sm transition-transform group-hover:scale-125"></div>
                    <div v-if="index !== activities.length - 1" class="w-0.5 h-full bg-gray-100 absolute top-4"></div>
                </div>

                <div class="flex-1 pb-10">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-black text-[#000B58] text-[13px] uppercase tracking-tight">{{ activity.user }}</span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ activity.time }}</span>
                    </div>
                    
                    <p class="text-[12px] text-gray-500 font-medium leading-relaxed mb-3">
                        {{ activity.action }} 
                        <span class="text-[#00139E] font-black">"{{ activity.target }}"</span>
                    </p>

                    <span :class="['text-[9px] px-3 py-1 rounded-lg font-black uppercase tracking-widest border shadow-sm', getStatusClass(activity.status)]">
                        {{ activity.status }}
                    </span>
                </div>
            </div>

            <div v-if="activities.length === 0" class="py-12 text-center">
                <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.3em]">Belum ada aktivitas terekam</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.flex-1 {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateX(10px); }
    to { opacity: 1; transform: translateX(0); }
}
</style>