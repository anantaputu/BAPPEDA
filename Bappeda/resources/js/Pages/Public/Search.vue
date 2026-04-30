<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, onMounted, onUnmounted } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    results: Object,
    filters: Object,
    temas: Array,
    urusans: Array,
    frekuensis: Array, 
    bidangs: Array,
    katakuncis: Array,
    list_satuan: Array
});

const isFilterExpanded = ref(false);
const openDropdown = ref(null);

const form = ref({
    search: props.filters.search || '',
    id_tema: props.filters.id_tema || '',
    id_urusan: props.filters.id_urusan || '',
    id_frekuensi: props.filters.id_frekuensi || '',
    id_bidang: props.filters.id_bidang || '',
    id_katakunci: props.filters.id_katakunci || '',
    satuan: props.filters.satuan || '',
});

defineOptions({ layout: AppLayout });

const toggleDropdown = (name) => {
    openDropdown.value = openDropdown.value === name ? null : name;
};

const selectOption = (field, value) => {
    form.value[field] = value;
    openDropdown.value = null;
};

const getSelectedName = (list, id, fieldId, fieldName, defaultLabel) => {
    if (!list) return defaultLabel;
    const found = list.find(item => item[fieldId] == id);
    return found ? found[fieldName] : defaultLabel;
};

const performSearch = debounce(() => {
    router.get('/search', form.value, {
        preserveState: true,
        replace: true
    });
}, 300);

watch(form, () => performSearch(), { deep: true });

const clearFilters = () => {
    form.value = { 
        search: '', 
        id_tema: '', 
        id_urusan: '', 
        id_frekuensi: '', 
        id_bidang: '',
        id_katakunci: '',
        satuan: '' 
    };
};

const closeOnOutsideClick = (e) => {
    if (!e.target.closest('.custom-select-container')) {
        openDropdown.value = null;
    }
};

onMounted(() => window.addEventListener('click', closeOnOutsideClick));
onUnmounted(() => window.removeEventListener('click', closeOnOutsideClick));
</script>

<template>
    <Head title="Eksplorasi Data Indikator" />

    <div class="max-w-[82%] mx-auto py-20 min-h-screen">
        
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-10 mb-12">
            <div class="max-w-xl">
                <h1 class="text-4xl lg:text-6xl font-black text-primary tracking-tight leading-[1.1]">
                    Eksplorasi <span class="text-secondary">Data</span>
                </h1>
                <p class="text-textsecondary mt-3 font-medium text-lg">
                    Temukan indikator pembangunan Provinsi NTB secara spesifik melalui filter sektoral.
                </p>
            </div>

            <div class="flex-1 lg:max-w-2xl w-full">
                <div class="bg-white p-3 rounded-xl border border-gray-400">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative group">
                            <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-textsecondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input v-model="form.search" type="text" placeholder="Cari nama indikator..."
                                class="w-full pl-14 pr-6 py-4 bg-white border border-gray-400 rounded-xl font-bold text-primary focus:outline-none focus:border-secondary transition-all">
                        </div>
                        
                        <button @click="isFilterExpanded = !isFilterExpanded" 
                            class="px-8 py-4 rounded-xl font-black text-xs uppercase tracking-widest flex items-center justify-center gap-3 transition-all border border-gray-400 whitespace-nowrap"
                            :class="isFilterExpanded ? 'bg-secondary text-white border-secondary shadow-lg shadow-secondary/20' : 'bg-white text-primary hover:bg-bgsoft'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            {{ isFilterExpanded ? 'Tutup' : 'Filter' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative mb-12">
            <transition 
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="transform opacity-0 -translate-y-4"
                enter-to-class="transform opacity-100 translate-y-0"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="transform opacity-100 translate-y-0"
                leave-to-class="transform opacity-0 -translate-y-4">
                
                <div v-if="isFilterExpanded" class="bg-white p-8 rounded-xl border border-gray-400 shadow-xl shadow-primary/5">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                        <div v-for="filter in [
                            { name: 'urusan', list: urusans, key: 'id_urusan', label: 'Semua Urusan', display: 'nama_urusan', title: 'Urusan' },
                            { name: 'bidang', list: bidangs, key: 'id_bidang', label: 'Semua Bidang', display: 'nama_bidang', title: 'Bidang' },
                            { name: 'tema', list: temas, key: 'id_tema', label: 'Semua Tema', display: 'nama_tema', title: 'Tema' },
                            { name: 'frekuensi', list: frekuensis, key: 'id_frekuensi', label: 'Semua Frekuensi', display: 'nama_frekuensi', title: 'Frekuensi' },
                            { name: 'katakunci', list: katakuncis, key: 'id_katakunci', label: 'Semua Label', display: 'nama_katakunci', title: 'Kata Kunci' }
                        ]" :key="filter.name" class="relative custom-select-container">
                            
                            <label class="block text-[10px] font-black text-primary/40 uppercase tracking-[0.2em] mb-3 ml-1">{{ filter.title }}</label>
                            
                            <div @click="toggleDropdown(filter.name)"
                                class="w-full px-5 py-3.5 bg-white border border-gray-400 rounded-xl font-bold text-primary flex justify-between items-center cursor-pointer hover:border-secondary transition-all"
                                :class="{'ring-2 ring-secondary border-transparent shadow-lg': openDropdown === filter.name}">
                                <span class="truncate text-[11px] uppercase tracking-tight">{{ getSelectedName(filter.list, form[filter.key], filter.key, filter.display, filter.label) }}</span>
                                <svg class="w-4 h-4 text-secondary transition-transform duration-300" :class="{'rotate-180': openDropdown === filter.name}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            <div v-if="openDropdown === filter.name" class="absolute z-50 w-full mt-2 bg-white border border-gray-400 rounded-xl py-2 max-h-64 overflow-y-auto shadow-2xl">
                                <div @click="selectOption(filter.key, '')" class="px-5 py-3 hover:bg-bgsoft cursor-pointer text-[10px] text-textsecondary uppercase font-black tracking-widest">{{ filter.label }}</div>
                                <div v-for="item in filter.list" :key="item[filter.key]" @click="selectOption(filter.key, item[filter.key])"
                                     class="px-5 py-3 hover:bg-bgsoft cursor-pointer text-[11px] font-bold transition-colors border-l-4 border-transparent"
                                     :class="{'text-secondary bg-secondary/5 border-secondary': form[filter.key] == item[filter.key]}">
                                    {{ item[filter.display] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>

            <div v-if="form.id_tema || form.id_urusan || form.id_frekuensi || form.id_bidang || form.id_katakunci" class="flex flex-wrap items-center gap-3 mt-6">
                <span class="text-[10px] font-black text-textsecondary uppercase tracking-[0.2em] ml-2">Filter Terpasang:</span>
                
                <button v-if="form.id_urusan" @click="form.id_urusan = ''" class="px-3 py-1.5 bg-primary text-white rounded-lg text-[9px] font-black uppercase flex items-center gap-2 hover:bg-secondary transition-all">
                    {{ getSelectedName(urusans, form.id_urusan, 'id_urusan', 'nama_urusan', '') }}
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <button v-if="form.id_bidang" @click="form.id_bidang = ''" class="px-3 py-1.5 bg-secondary text-white rounded-lg text-[9px] font-black uppercase flex items-center gap-2 hover:bg-primary transition-all">
                    {{ getSelectedName(bidangs, form.id_bidang, 'id_bidang', 'nama_bidang', '') }}
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <button @click="clearFilters" class="text-[9px] font-black text-integritas hover:underline uppercase ml-2 tracking-widest transition-colors">
                    Reset Filter
                </button>
            </div>
        </div>

        <div v-if="results.data.length > 0" class="bg-white rounded-xl border border-gray-400 shadow-xl shadow-primary/5 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="bg-bgsoft border-b border-gray-400">
                            <th class="px-8 py-5 text-[10px] font-black text-textsecondary uppercase tracking-[0.15em]">Nama Indikator</th>
                            <th class="px-6 py-5 text-[10px] font-black text-textsecondary uppercase tracking-[0.15em] text-center">Frekuensi</th>
                            <th class="px-6 py-5 text-[10px] font-black text-textsecondary uppercase tracking-[0.15em] text-center">Satuan</th>
                            <th class="px-6 py-5 text-[10px] font-black text-textsecondary uppercase tracking-[0.15em]">Urusan</th>
                            <th class="px-8 py-5 text-[10px] font-black text-textsecondary uppercase tracking-[0.15em] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="item in results.data" :key="item.id_data" class="group hover:bg-bgsoft transition-all duration-300">
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-primary group-hover:text-secondary transition-colors leading-snug">
                                        {{ item.nama_data }}
                                    </span>
                                    <div class="flex gap-2 mt-2">
                                        <span class="text-[9px] font-black bg-primary/5 text-primary px-2 py-0.5 rounded border border-primary/10 uppercase tracking-tighter">
                                            {{ item.tema?.nama_tema || 'Umum' }}
                                        </span>
                                        <span v-if="item.bidang" class="text-[9px] font-black bg-secondary/5 text-secondary px-2 py-0.5 rounded border border-secondary/10 uppercase tracking-tighter">
                                            {{ item.bidang.nama_bidang }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span v-if="item.frekuensi" class="inline-block px-3 py-1 bg-white border border-gray-400 rounded-lg text-[9px] font-black text-primary uppercase">
                                    {{ item.frekuensi.nama_frekuensi }}
                                </span>
                            </td>
                            <td class="px-6 py-6 text-center font-bold text-textsecondary italic text-xs">{{ item.satuan }}</td>
                            <td class="px-6 py-6">
                                <span class="text-[10px] font-black text-primary/70 uppercase leading-tight">
                                    {{ item.urusan?.nama_urusan || 'BAPPEDA PROVINSI NTB' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <Link :href="`/dataset/${item.id_data}`" class="inline-flex items-center gap-2 px-6 py-3 bg-primary hover:bg-secondary text-white text-[10px] font-black rounded-xl transition-all shadow-md active:scale-95">
                                    DETAIL
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-8 py-6 bg-bgsoft border-t border-gray-400 flex justify-center gap-2">
                <Link v-for="link in results.links" :key="link.label" :href="link.url || '#'" v-html="link.label"
                    class="px-5 py-2.5 rounded-xl text-[10px] font-black transition-all"
                    :class="[
                        link.active ? 'bg-primary text-white shadow-lg' : 'bg-white text-textsecondary border border-gray-400 hover:bg-bgsoft',
                        !link.url ? 'opacity-30 cursor-not-allowed' : ''
                    ]" />
            </div>
        </div>

        <div v-else class="text-center py-32 bg-white rounded-xl border border-gray-400 shadow-xl shadow-primary/5">
            <div class="w-20 h-20 bg-primary/5 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-primary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-primary font-black text-xl">Data Tidak Ditemukan</p>
            <p class="text-textsecondary text-sm mt-2 font-medium">Coba gunakan filter lain atau pastikan penulisan sudah benar.</p>
        </div>
    </div>
</template>