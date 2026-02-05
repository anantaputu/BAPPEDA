<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    results: Object,
    filters: Object,
    temas: Array,
    urusans: Array
});

defineOptions({ layout: AppLayout });

// State untuk Form Pencarian
const form = ref({
    search: props.filters.search || '',
    id_tema: props.filters.id_tema || '',
    id_urusan: props.filters.id_urusan || '',
    tahun: props.filters.tahun || '',
});

// Fungsi untuk eksekusi pencarian
const performSearch = debounce(() => {
    router.get('/cari', form.value, {
        preserveState: true,
        replace: true
    });
}, 300);

// Watch setiap perubahan di form
watch(form, () => performSearch(), { deep: true });

const clearFilters = () => {
    form.value = { search: '', id_tema: '', id_urusan: '', tahun: '' };
};
</script>

<template>
    <Head title="Cari Indikator" />

    <div class="max-w-[80%] mx-auto py-12 min-h-screen">
        <h1 class="text-4xl font-black text-[#000B58] mb-10 tracking-tight">Pencarian Data Indikator</h1>

        <div class="bg-white p-4 rounded-[2.5rem] border border-[#A2B5CB]/30 shadow-2xl shadow-[#000B58]/5 mb-12">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1 relative group">
                    <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-[#A2B5CB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input v-model="form.search" type="text" placeholder="Cari indikator atau kata kunci..."
                        class="w-full pl-14 pr-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#00139E] font-medium text-[#000B58]">
                </div>

                <div class="lg:w-64 relative">
                    <select v-model="form.id_tema" class="w-full pl-6 pr-10 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#00139E] font-bold text-[#000B58] appearance-none">
                        <option value="">Semua Tema</option>
                        <option v-for="t in temas" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                    </select>
                </div>

                <div class="lg:w-64 relative">
                    <select v-model="form.id_urusan" class="w-full pl-6 pr-10 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#00139E] font-bold text-[#000B58] appearance-none">
                        <option value="">Semua Urusan</option>
                        <option v-for="u in urusans" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                    </select>
                </div>
            </div>

            <div v-if="form.search || form.id_tema || form.id_urusan" 
                class="flex flex-wrap items-center gap-3 mt-6 ml-2">
                
                <span v-if="form.id_tema" 
                    class="flex items-center gap-2 px-4 py-2 bg-[#00139E]/5 text-[#00139E] rounded-full text-xs font-black uppercase">
                    Tema Terpilih 
                    <button @click="form.id_tema = ''" class="hover:text-[#FF1414] transition-colors">×</button>
                </span>

                <span v-if="form.id_urusan" 
                    class="flex items-center gap-2 px-4 py-2 bg-[#00139E]/5 text-[#00139E] rounded-full text-xs font-black uppercase">
                    Urusan Terpilih 
                    <button @click="form.id_urusan = ''" class="hover:text-[#FF1414] transition-colors">×</button>
                </span>

                <button @click="clearFilters" 
                        class="text-xs font-black text-[#A2B5CB] hover:text-[#FF1414] uppercase ml-2 tracking-widest transition-colors">
                    Hapus Semua Filter
                </button>
            </div>
        </div>

        <div v-if="results.data.length > 0">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div v-for="item in results.data" :key="item.id_data" 
                     class="bg-white p-8 rounded-[2.5rem] border border-[#A2B5CB]/20 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex justify-between items-start mb-4">
                        <div class="px-3 py-1 bg-[#00139E]/5 text-[#00139E] text-[9px] font-black rounded-lg uppercase tracking-widest">
                            {{ item.tema?.nama_tema || 'Global' }}
                        </div>
                        <span class="text-xl font-black text-[#000B58] opacity-20 group-hover:opacity-100 transition-opacity">#{{ item.id_data }}</span>
                    </div>
                    
                    <h3 class="font-black text-[#000B58] text-lg mb-6 leading-tight min-h-[3rem]">{{ item.nama_indikator }}</h3>
                    
                    <div class="pt-6 border-t border-gray-50 flex justify-between items-center text-sm">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-tighter">Satuan</span>
                            <span class="font-bold text-[#000B58]">{{ item.satuan }}</span>
                        </div>
                        <div class="text-right flex flex-col">
                            <span class="text-[10px] font-black text-[#A2B5CB] uppercase tracking-tighter">Tahun</span>
                            <span class="font-black text-[#00139E] text-lg">{{ item.tahun_data }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-16 flex justify-center gap-3">
                <Link v-for="link in results.links" :key="link.label" :href="link.url || '#'" v-html="link.label"
                    class="px-6 py-3 rounded-2xl text-xs font-black transition-all"
                    :class="[
                        link.active ? 'bg-[#000B58] text-white shadow-lg' : 'bg-white text-[#A2B5CB] hover:text-[#00139E] border border-[#A2B5CB]/20',
                        !link.url ? 'opacity-30 cursor-not-allowed' : ''
                    ]" />
            </div>
        </div>

        <div v-else class="text-center py-32 bg-gray-50 rounded-[4rem] border-2 border-dashed border-[#A2B5CB]/30">
            <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-10 h-10 text-[#A2B5CB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <p class="text-[#000B58] font-black text-xl mb-2">Tidak ada data indikator</p>
            <p class="text-[#A2B5CB] font-medium">Coba sesuaikan kata kunci atau filter pencarian Anda.</p>
        </div>
    </div>
</template>