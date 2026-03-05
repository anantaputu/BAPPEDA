<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

defineOptions({ layout: AppLayout });

const props = defineProps({
    indicators: Object,
    filters: Object,
    listTema: Array,
    listUrusan: Array,
    listBidang: Array
});

// State Filter
const form = ref({
    q: props.filters.q || '',
    tema: props.filters.tema || '',
    urusan: props.filters.urusan || '',
    bidang: props.filters.bidang || ''
});

// Auto-submit saat filter berubah
watch(form, debounce(() => {
    router.get('/katalog', form.value, { preserveState: true, replace: true });
}, 500), { deep: true });

const resetFilter = () => {
    form.value = { q: '', tema: '', urusan: '', bidang: '' };
};
</script>

<template>
    <Head title="Katalog Indikator Pembangunan" />

    <div class="min-h-screen bg-gray-50 pb-20 pt-10">
        <div class="max-w-[90%] mx-auto grid lg:grid-cols-4 gap-8">
            
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-200">
                    <h3 class="font-black text-[#000B58] uppercase text-xs tracking-widest mb-6">Filter Kelompok</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block">Cari Kata Kunci</label>
                            <input v-model="form.q" type="text" class="w-full rounded-xl border-gray-200 text-sm" placeholder="Contoh: Kemiskinan...">
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block">Berdasarkan Tema</label>
                            <select v-model="form.tema" class="w-full rounded-xl border-gray-200 text-sm font-bold">
                                <option value="">Semua Tema</option>
                                <option v-for="t in listTema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block">Berdasarkan Urusan</label>
                            <select v-model="form.urusan" class="w-full rounded-xl border-gray-200 text-sm font-bold">
                                <option value="">Semua Urusan</option>
                                <option v-for="u in listUrusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                            </select>
                        </div>

                        <button @click="resetFilter" class="w-full py-3 text-xs font-black text-red-500 hover:bg-red-50 rounded-xl transition-all uppercase">Reset Filter</button>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3 space-y-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-black text-[#000B58]">Menampilkan {{ indicators.total }} Indikator</h2>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div v-for="item in indicators.data" :key="item.id_data" 
                        class="bg-white p-8 rounded-[2.5rem] border border-gray-100 hover:border-blue-300 transition-all group shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black uppercase">
                                {{ item.tema?.nama_tema }}
                            </span>
                            <span class="text-[10px] font-bold text-gray-300 italic">{{ item.tahun }}</span>
                        </div>
                        <h4 class="text-lg font-black text-gray-800 leading-tight mb-4 group-hover:text-blue-600 transition-colors">
                            {{ item.nama_data }}
                        </h4>
                        <p class="text-xs text-gray-400 mb-6 line-clamp-2">{{ item.deskripsi || 'Tidak ada deskripsi detail.' }}</p>
                        
                        <div class="flex justify-between items-center pt-6 border-t border-gray-50">
                            <div class="text-[10px] text-gray-400 uppercase font-bold">
                                Satuan: <span class="text-gray-900">{{ item.satuan }}</span>
                            </div>
                            <Link :href="`/dataset/${item.id_data}`" class="text-xs font-black text-blue-600 flex items-center gap-2">
                                LIHAT DATA <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center gap-2 mt-10">
                    <Link v-for="link in indicators.links" :key="link.label" :href="link.url || '#'" 
                        v-html="link.label" class="px-4 py-2 rounded-xl text-xs font-bold transition-all"
                        :class="link.active ? 'bg-[#000B58] text-white' : 'bg-white text-gray-400 hover:bg-gray-100'"/>
                </div>
            </div>
        </div>
    </div>
</template>