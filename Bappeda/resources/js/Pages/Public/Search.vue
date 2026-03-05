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
    katakuncis: Array,
    list_satuan: Array
});

const form = ref({
    search: props.filters.search || '',
    id_tema: props.filters.id_tema || '',
    id_urusan: props.filters.id_urusan || '',
    id_frekuensi: props.filters.id_frekuensi || '',
    id_katakunci: props.filters.id_katakunci || '',
    satuan: props.filters.satuan || '',
});

defineOptions({ layout: AppLayout });

const openDropdown = ref(null);
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
    <Head title="Cari Indikator" />

    <div class="max-w-[80%] mx-auto py-20 min-h-screen">
        <h1 class="text-4xl font-black text-primary mb-10 tracking-tight">Pencarian Data</h1>

        <div class="bg-white p-6 rounded-xl border border-gray-400 mb-12">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1 relative group">
                    <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-textsecondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input v-model="form.search" type="text" placeholder="Cari indikator atau kata kunci..."
                        class="w-full pl-14 pr-6 py-4 bg-white border border-gray-400 rounded-xl font-medium text-primary focus:outline-none focus:border-secondary transition-all">
                </div>

                <div v-for="filter in [
                    { name: 'tema', list: temas, key: 'id_tema', label: 'Semua Tema', display: 'nama_tema' },
                    { name: 'urusan', list: urusans, key: 'id_urusan', label: 'Semua Urusan', display: 'nama_urusan' },
                    { name: 'katakunci', list: katakuncis, key: 'id_katakunci', label: 'Semua Kata Kunci', display: 'nama_katakunci' }
                ]" :key="filter.name" class="lg:w-64 relative custom-select-container">
                    
                    <div @click="toggleDropdown(filter.name)"
                        class="w-full px-6 py-4 bg-white border border-gray-400 rounded-xl font-bold text-primary flex justify-between items-center cursor-pointer hover:bg-white transition-all"
                        :class="{'ring-2 ring-secondary border-transparent': openDropdown === filter.name}">
                        <span class="truncate text-sm">{{ getSelectedName(filter.list, form[filter.key], filter.key, filter.display, filter.label) }}</span>
                        <svg class="w-4 h-4 transition-transform duration-300" :class="{'rotate-180': openDropdown === filter.name}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    <div v-if="openDropdown === filter.name" class="absolute z-50 w-full mt-2 bg-white border border-gray-400 rounded-xl py-2 max-h-64 overflow-y-auto">
                        <div @click="selectOption(filter.key, '')" class="px-5 py-3 hover:bg-bgsoft cursor-pointer text-sm text-textsecondary uppercase font-bold">{{ filter.label }}</div>
                        <div v-for="item in filter.list" :key="item[filter.key]" @click="selectOption(filter.key, item[filter.key])"
                             class="px-5 py-3 hover:bg-bgsoft cursor-pointer text-sm font-bold transition-colors"
                             :class="{'text-secondary': form[filter.key] == item[filter.key]}">
                            {{ item[filter.display] }}
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="form.search || form.id_tema || form.id_urusan || form.id_frekuensi || form.id_katakunci" class="flex flex-wrap items-center gap-3 mt-6 ml-2">
                <span v-if="form.id_katakunci" class="px-4 py-2 bg-primary text-white rounded-full text-[10px] font-black uppercase flex items-center gap-2">
                    TAG: {{ getSelectedName(katakuncis, form.id_katakunci, 'id_katakunci', 'nama_katakunci', '') }}
                    <button @click="form.id_katakunci = ''" class="hover:text-integritas transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </span>

                <button @click="clearFilters" class="text-xs font-black text-textsecondary hover:text-integritas uppercase ml-2 tracking-widest transition-colors">
                    Hapus Semua Filter
                </button>
            </div>
        </div>

        <div v-if="results.data.length > 0" class="bg-white rounded-xl border border-gray-400 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="bg-bgsoft border-b border-gray-400">
                            <th class="px-8 py-5 text-[10px] font-black text-textsecondary uppercase tracking-[0.15em]">Nama Indikator</th>
                            <th class="px-6 py-5 text-[10px] font-black text-textsecondary uppercase tracking-[0.15em] text-center">Frekuensi</th>
                            <th class="px-6 py-5 text-[10px] font-black text-textsecondary uppercase tracking-[0.15em] text-center">Satuan</th>
                            <th class="px-6 py-5 text-[10px] font-black text-textsecondary uppercase tracking-[0.15em]">Instansi</th>
                            <th class="px-8 py-5 text-[10px] font-black text-textsecondary uppercase tracking-[0.15em] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="item in results.data" :key="item.id_data" class="group hover:bg-bgsoft transition-all duration-300">
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-[#000B58] group-hover:text-[#00139E] transition-colors leading-snug">
                                        {{ item.nama_data }}
                                    </span>
                                    <span class="text-[10px] font-bold text-textsecondary mt-1 uppercase tracking-wider">
                                        Tema: {{ item.tema?.nama_tema || 'Umum' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <div class="flex justify-center">
                                    <span v-if="item.frekuensi" 
                                        class="inline-block px-4 py-1.5 bg-secondary/10 text-secondary rounded-xl text-[10px] font-black uppercase tracking-wider border border-secondary/20">
                                        {{ item.frekuensi.nama_frekuensi }}
                                    </span>
                                    <span v-else class="text-[10px] text-textsecondary/50 font-bold italic">-</span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-xs font-bold text-textsecondary italic">{{ item.satuan }}</span>
                            </td>
                            <td class="px-6 py-6">
                                <span class="text-[11px] font-bold text-primary/70 uppercase leading-tight block max-w-[200px]">
                                    {{ item.urusan?.nama_urusan || 'BAPPEDA PROVINSI NTB' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <Link :href="`/dataset/${item.id_data}`" 
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-secondary text-white text-[10px] font-black rounded-xl transition-all hover:shadow-lg active:scale-95">
                                    DETAIL
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
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

        <div v-else class="text-center py-32 bg-white rounded-xl border-2 border-dashed border-gray-400">
            <svg class="w-16 h-16 text-textsecondary/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-primary font-black text-xl">Data tidak ditemukan</p>
            <p class="text-textsecondary text-sm mt-2">Coba gunakan kata kunci lain atau hapus filter.</p>
        </div>
    </div>
</template>