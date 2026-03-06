<script setup>
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

defineProps({ 
    activities: {
        type: Array,
        default: () => []
    }
});

const page = usePage()
const user = computed(() => page.props.auth.user)
// Logika akses: Admin Super atau Admin bisa melihat semua log
const canManage = computed(() => {
    const role = user.value?.role;
    return role === 'Admin Super' || role === 'Admin';
});

const getStatusClass = (status) => {
    const map = {
        'valid': 'bg-inovasi/10 text-inovasi border-inovasi/20', // Hijau Inovasi
        'pending': 'bg-profesional/10 text-profesional border-profesional/20', // Oranye Profesional
        'rejected': 'bg-integritas/10 text-integritas border-integritas/20', // Merah Integritas
    };
    return map[status?.toLowerCase()] || 'bg-bgsoft text-textsecondary border-gray-200';
};
</script>

<template>
    <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-400">
        <div class="flex items-center justify-between mb-10">
            <div class="flex items-center gap-3">
                <h3 class="text-sm font-black text-primary uppercase tracking-[0.2em] border-l-4 border-secondary pl-4">
                    Log Aktivitas Terbaru
                </h3>
            </div>
            <Link 
                v-if="canManage"
                href="/admin/logs"
                class="text-[10px] font-black text-secondary uppercase tracking-[0.2em] hover:bg-bgsoft px-4 py-2 rounded-xl transition-all border border-transparent hover:border-gray-200"
            >
                Lihat Semua
            </Link>
        </div>

        <div class="space-y-2">
            <div v-for="(activity, index) in activities" :key="activity.id || index" class="flex gap-6 relative group">
                <div class="flex flex-col items-center">
                    <div class="w-4 h-4 rounded-full border-4 border-white bg-secondary z-10 shadow-sm transition-transform group-hover:scale-125 duration-300"></div>
                    <div v-if="index !== activities.length - 1" class="w-0.5 h-full bg-bgsoft absolute top-4"></div>
                </div>

                <div class="flex-1 pb-10">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-black text-primary text-[13px] uppercase tracking-tight">{{ activity.user }}</span>
                        <span class="text-[10px] text-textsecondary font-bold uppercase tracking-widest">{{ activity.time }}</span>
                    </div>
                    
                    <p class="text-[12px] text-textsecondary font-medium leading-relaxed mb-4">
                        {{ activity.action }} 
                        <span class="text-secondary font-black">"{{ activity.target }}"</span>
                    </p>
                </div>
            </div>

            <div v-if="activities.length === 0" class="py-20 text-center bg-bgsoft/50 rounded-xl border-2 border-dashed border-gray-200">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-[10px] font-black text-textsecondary/40 uppercase tracking-[0.3em]">Belum ada aktivitas terekam</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.flex-1 {
    animation: slideIn 0.4s ease-out forwards;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(12px); }
    to { opacity: 1; transform: translateX(0); }
}
</style>