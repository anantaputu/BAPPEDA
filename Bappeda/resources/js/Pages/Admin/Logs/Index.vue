<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

defineOptions({ layout: AppLayout });

const props = defineProps({
    activities: Array,
    users: Array,
    filters: Object,
    pagination: Object
});

const form = ref({
    search: props.filters?.search || '',
    action: props.filters?.action || '',
    user_id: props.filters?.user_id || '',
});

const updateFilters = debounce(() => {
    router.get('/admin/logs', form.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 500);

watch(form, () => updateFilters(), { deep: true });

const clearFilters = () => {
    form.value = {
        search: '',
        action: '',
        user_id: '',
    };
};

// Fungsi pembantu untuk menentukan skema warna berdasarkan tipe aksi
const getActionTheme = (type) => {
    const themes = {
        'UPLOAD': { 
            dot: 'bg-inovasi', 
            text: 'text-inovasi', 
            bg: 'bg-inovasi/5', 
            border: 'border-inovasi/20' 
        },
        'EDIT': { 
            dot: 'bg-profesional', 
            text: 'text-profesional', 
            bg: 'bg-profesional/5', 
            border: 'border-profesional/20' 
        },
        'DELETE': { 
            dot: 'bg-integritas', 
            text: 'text-integritas', 
            bg: 'bg-integritas/5', 
            border: 'border-integritas/20' 
        }
    };
    return themes[type] || { dot: 'bg-secondary', text: 'text-secondary', bg: 'bg-bgsoft', border: 'border-gray-100' };
};
</script>

<template>
    <Head title="Audit Log Aktivitas" />

    <div class="mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-black text-primary uppercase tracking-tight">Audit Log <span class="text-secondary">Aktivitas</span></h2>
                <p class="text-textsecondary font-medium mt-1 text-sm">Monitoring riwayat pengelolaan data dan integritas sistem.</p>
            </div>
            
            <div class="mt-4 md:mt-0 flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <p class="text-[9px] font-black text-textsecondary uppercase tracking-[0.2em]">Total Rekaman</p>
                    <p class="text-lg font-black text-primary leading-none">{{ pagination.total }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-8 md:p-12 shadow-2xl shadow-primary/5 border border-gray-400">
            <!-- PANEL FILTER -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 pb-10 border-b border-gray-200">
                <div class="space-y-2">
                    <label class="block text-[9px] font-black text-textsecondary uppercase tracking-[0.2em] ml-2">Cari Aktivitas/Target</label>
                    <input v-model="form.search" type="text" placeholder="Masukkan kata kunci..."
                        class="w-full bg-white border border-gray-400 rounded-xl px-4 py-3.5 text-xs font-bold text-primary focus:outline-none focus:border-secondary transition-all shadow-sm">
                </div>
                
                <div class="space-y-2">
                    <label class="block text-[9px] font-black text-textsecondary uppercase tracking-[0.2em] ml-2">Tipe Aksi</label>
                    <select v-model="form.action"
                        class="w-full bg-white border border-gray-400 rounded-xl px-4 py-3.5 text-xs font-bold text-primary focus:outline-none focus:border-secondary transition-all shadow-sm cursor-pointer">
                        <option value="">Semua Aksi</option>
                        <option value="UPLOAD">UPLOAD (Tambah Data)</option>
                        <option value="EDIT">EDIT (Perbarui Data)</option>
                        <option value="DELETE">DELETE (Hapus Data)</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-[9px] font-black text-textsecondary uppercase tracking-[0.2em] ml-2">Pengguna (User)</label>
                    <select v-model="form.user_id"
                        class="w-full bg-white border border-gray-400 rounded-xl px-4 py-3.5 text-xs font-bold text-primary focus:outline-none focus:border-secondary transition-all shadow-sm cursor-pointer">
                        <option value="">Semua User</option>
                        <option v-for="u in users" :key="u.id" :value="u.id">
                            {{ u.name }} (@{{ u.username }})
                        </option>
                    </select>
                </div>
            </div>

            <div v-if="form.search || form.action || form.user_id" class="flex items-center justify-between mb-8 -mt-6">
                <span class="text-[9px] font-black text-textsecondary uppercase tracking-widest ml-2">Filter Terpasang</span>
                <button @click="clearFilters" class="text-[9px] font-black text-integritas hover:underline uppercase tracking-widest transition-colors mr-2">
                    Reset Filter
                </button>
            </div>

            <div class="relative">
                <div v-if="activities.length > 0" class="absolute left-[9px] top-2 bottom-0 w-0.5 bg-gray-100"></div>

                <div class="space-y-0">
                    <div v-for="(activity, index) in activities" :key="activity.id" class="flex gap-8 relative group">
                        <div class="flex flex-col items-center">
                            <div class="w-5 h-5 rounded-full border-4 border-white z-10 shadow-sm transition-all duration-300 group-hover:scale-125"
                                 :class="getActionTheme(activity.type).dot"></div>
                        </div>

                        <div class="flex-1 pb-12">
                            <div class="flex flex-wrap justify-between items-start mb-3 gap-2">
                                <div class="flex items-center gap-3">
                                    <span class="font-black text-primary text-sm uppercase tracking-tight">{{ activity.user }}</span>
                                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest bg-primary/5 text-textsecondary border border-gray-100">
                                        IP: {{ activity.ip || '0.0.0.0' }}
                                    </span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-[11px] text-primary font-black uppercase tracking-widest">{{ activity.time }}</span>
                                    <span class="text-[10px] text-textsecondary font-bold uppercase tracking-widest mt-0.5 opacity-60">{{ activity.date_full }}</span>
                                </div>
                            </div>
                            
                            <div class="rounded-xl p-6 border transition-all duration-300 group-hover:shadow-lg group-hover:-translate-y-1"
                                 :class="[getActionTheme(activity.type).bg, getActionTheme(activity.type).border]">
                                <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4">
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] whitespace-nowrap" :class="getActionTheme(activity.type).text">
                                        [{{ activity.type }}]
                                    </span>
                                    <p class="text-[13px] text-textsecondary font-medium leading-relaxed">
                                        {{ activity.action }} 
                                        <span class="text-primary font-black italic ml-1">"{{ activity.target }}"</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="activities.length === 0" class="py-24 text-center bg-bgsoft/50 rounded-2xl border-2 border-dashed border-gray-200">
                        <div class="text-gray-300 mb-6">
                            <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-xs font-black text-textsecondary/40 uppercase tracking-[0.4em]">Belum ada rekaman aktivitas audit</p>
                    </div>
                </div>
            </div>

            <div v-if="pagination.links.length > 3" class="mt-10 flex flex-col md:flex-row items-center justify-between gap-6 border-t border-gray-100 pt-10">
                <p class="text-[10px] font-black text-textsecondary uppercase tracking-widest">
                    Menampilkan {{ pagination.from || 0 }} - {{ pagination.to || 0 }} dari {{ pagination.total }} Aktivitas
                </p>
                <div class="flex gap-2">
                    <template v-for="(link, k) in pagination.links" :key="k">
                        <Link 
                            v-if="link.url" 
                            :href="link.url" 
                            v-html="link.label" 
                            class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all border"
                            :class="link.active 
                                ? 'bg-primary text-white border-transparent shadow-lg shadow-primary/20 scale-110' 
                                : 'bg-white text-textsecondary border-gray-200 hover:border-secondary hover:text-secondary hover:shadow-md'"
                        />
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.flex-1 {
    animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Custom design for pagination arrows from Laravel */
:deep(svg) {
    display: inline;
    width: 12px;
    height: 12px;
}
</style>