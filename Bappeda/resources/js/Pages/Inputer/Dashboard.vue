<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({ layout: AppLayout });

const page = usePage();
const user = computed(() => page.props.auth.user);

// Menerima data dari Controller (Pastikan Controller Anda mengirim variable ini)
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalTugas: 0,
            pendingMapping: 0,
            suksesUpload: 0
        })
    },
    recentUploads: {
        type: Array,
        default: () => []
    }
});

const statusColor = (status) => {
    const colors = {
        'processing': 'bg-amber-100 text-amber-700 border-amber-200',
        'valid': 'bg-green-100 text-green-700 border-green-200',
        'failed': 'bg-red-100 text-red-700 border-red-200'
    };
    return colors[status] || 'bg-gray-100 text-gray-600';
};
</script>

<template>
    <Head title="Inputer Dashboard" />

    <div class="space-y-10 py-6">
        <div class="bg-[#4A6CF7] p-10 rounded-[2.5rem] text-white relative overflow-hidden shadow-xl shadow-blue-200">
            <div class="relative z-10">
                <h1 class="text-3xl font-extrabold tracking-tight mb-2">
                    Selamat Datang, {{ user?.nama_depan }}! 👋
                </h1>
                <p class="text-blue-100 font-medium max-w-md">
                    Hari ini ada {{ stats.pendingMapping }} data yang perlu diselesaikan proses mapping-nya. Mari selesaikan sekarang.
                </p>
                
                <Link :href="route('inputer.index')" class="inline-block mt-6 bg-white text-[#4A6CF7] px-8 py-3 rounded-xl font-black text-sm hover:bg-blue-50 transition shadow-lg">
                    MULAI INPUT DATA
                </Link>
            </div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div v-for="(val, label) in { 
                'Total Indikator': stats.totalTugas, 
                'Perlu Mapping': stats.pendingMapping, 
                'Data Valid': stats.suksesUpload 
            }" :key="label" class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ label }}</p>
                <h2 class="text-4xl font-black text-gray-900">{{ val }}</h2>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <div class="flex justify-between items-center mb-8">
                <h3 class="font-extrabold text-gray-900 text-xl tracking-tight">Riwayat Upload Terakhir</h3>
                <Link :href="route('inputer.index')" class="text-xs font-black text-[#4A6CF7] uppercase tracking-widest hover:underline">Lihat Semua</Link>
            </div>

            <div class="space-y-4">
                <div v-for="upload in recentUploads" :key="upload.id_upload" class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl hover:bg-gray-100/50 transition border border-transparent hover:border-gray-200">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-[#4A6CF7]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm italic">{{ upload.data?.nama_indikator || 'Indikator Dihapus' }}</h4>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">Periode: {{ upload.periode }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-tighter border" :class="statusColor(upload.status)">
                            {{ upload.status }}
                        </span>
                        <Link v-if="upload.status === 'processing'" :href="route('inputer.mapping', upload.id_upload)" class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 font-bold shadow-md">
                            Lanjut
                        </Link>
                    </div>
                </div>

                <div v-if="recentUploads.length === 0" class="text-center py-6 text-gray-400 text-sm">
                    Belum ada data upload.
                </div>
            </div>
        </div>
    </div>
</template>