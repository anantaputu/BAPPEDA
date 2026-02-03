<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

// Gunakan layout secara persisten
defineOptions({ layout: AppLayout });

const props = defineProps({
    dataIndikator: Array,
    tema: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const selectedTema = ref(props.filters.tema || '');

// Fungsi untuk melakukan filter secara real-time tanpa refresh
const performSearch = debounce(() => {
    router.get('/cari', { search: search.value, tema: selectedTema.value }, {
        preserveState: true,
        replace: true
    });
}, 300);

watch([search, selectedTema], () => performSearch());
</script>

<template>
    <Head title="Cari Data Indikator" />

    <div class="max-w-7xl mx-auto space-y-12">
        <div class="text-center space-y-4">
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">Eksplorasi Data Pembangunan</h1>
            <p class="text-gray-500 max-w-2xl mx-auto">
                Temukan berbagai indikator capaian pembangunan daerah mulai dari SDGs, RPJMD, hingga data sektoral lainnya.
            </p>
        </div>

        <div class="bg-white p-4 rounded-[2rem] shadow-xl shadow-blue-100/50 border border-gray-100 flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input v-model="search" type="text" placeholder="Cari nama indikator atau kata kunci..."
                    class="block w-full pl-11 pr-4 py-4 bg-gray-50 border-transparent rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition" />
            </div>
            
            <div class="md:w-64">
                <select v-model="selectedTema" class="w-full py-4 bg-gray-50 border-transparent rounded-2xl focus:ring-2 focus:ring-blue-500 transition font-bold text-gray-600">
                    <option value="">Semua Tema</option>
                    <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                </select>
            </div>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="item in dataIndikator" :key="item.id_data" 
                class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition group">
                <div class="flex justify-between items-start mb-6">
                    <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-black uppercase rounded-lg tracking-widest">
                        {{ item.tema?.nama_tema }}
                    </span>
                    <span class="text-gray-300 group-hover:text-blue-200 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    </span>
                </div>
                
                <h3 class="text-xl font-extrabold text-gray-900 mb-2 leading-tight min-h-[3.5rem]">
                    {{ item.nama_indikator }}
                </h3>
                
                <div class="space-y-3 mb-8">
                    <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        {{ item.urusan?.nama_urusan || 'Sektoral' }}
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                        {{ item.satuan || '-' }}
                    </div>
                </div>

                <Link :href="`/data/${item.id_data}/detail`" 
                    class="block w-full text-center py-4 bg-gray-50 text-gray-900 font-bold rounded-2xl group-hover:bg-blue-600 group-hover:text-white transition shadow-sm">
                    Lihat Tren Data
                </Link>
            </div>

            <div v-if="dataIndikator.length === 0" class="col-span-full py-20 text-center">
                <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Data Tidak Ditemukan</h3>
                <p class="text-gray-400">Coba gunakan kata kunci lain atau pilih tema yang berbeda.</p>
            </div>
        </div>
    </div>
</template>