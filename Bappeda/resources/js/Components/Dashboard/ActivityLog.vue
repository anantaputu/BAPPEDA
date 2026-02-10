<script setup>
defineProps({ activities: Array });

const getStatusClass = (status) => {
    const map = {
        'valid': 'bg-green-100 text-green-700',
        'pending': 'bg-yellow-100 text-yellow-700',
        'rejected': 'bg-red-100 text-red-700',
    };
    return map[status] || 'bg-gray-100 text-gray-700';
};
</script>

<template>
    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-400">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-xl font-black text-[#000B58]">Log Aktivitas Terbaru</h3>
            <Link href="/admin/logs" class="text-blue-600 text-sm font-bold hover:underline">Lihat Semua</Link>
        </div>

        <div class="space-y-6">
            <div v-for="activity in activities" :key="activity.id" class="flex gap-4 relative">
                <div class="flex flex-col items-center">
                    <div class="w-3 h-3 rounded-full bg-blue-600 z-10"></div>
                    <div class="w-0.5 h-full bg-gray-100 absolute top-3"></div>
                </div>

                <div class="flex-1 pb-6">
                    <div class="flex justify-between items-start mb-1">
                        <span class="font-bold text-[#000B58] text-sm">{{ activity.user }}</span>
                        <span class="text-[10px] text-gray-400 font-medium">{{ activity.time }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">
                        {{ activity.action }} <span class="text-blue-600 font-medium">"{{ activity.target }}"</span>
                    </p>
                    <span :class="['text-[9px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider', getStatusClass(activity.status)]">
                        {{ activity.status }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>