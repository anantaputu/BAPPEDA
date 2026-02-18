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
    list_tahun: Array,
    list_satuan: Array
});

defineOptions({ layout: AppLayout });

const form = ref({
    search: props.filters.search || '',
    id_tema: props.filters.id_tema || '',
    id_urusan: props.filters.id_urusan || '',
    tahun: props.filters.tahun || '',
    satuan: props.filters.satuan || '',
});

const openDropdown = ref(null);
const toggleDropdown = (name) => {
    openDropdown.value = openDropdown.value === name ? null : name;
};

const selectOption = (field, value) => {
    form.value[field] = value;
    openDropdown.value = null;
};

const getSelectedName = (list, id, fieldId, fieldName, defaultLabel) => {
    const found = list.find(item => item[fieldId] == id);
    return found ? found[fieldName] : defaultLabel;
};

const performSearch = debounce(() => {
    router.get('/cari', form.value, {
        preserveState: true,
        replace: true
    });
}, 300);

watch(form, () => performSearch(), { deep: true });

const clearFilters = () => {
    form.value = { search: '', id_tema: '', id_urusan: '', tahun: '' };
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

    <div class="max-w-[80%] mx-auto py-12 min-h-screen mt-20">
        <h1 class="text-4xl font-black text-[#000B58] mb-10 tracking-tight">Pencarian Data Indikator</h1>

        <div class="bg-white p-4 rounded-[2rem] border border-gray-400 shadow-xl shadow-[#000B58]/5 mb-12">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1 relative group">
                    <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-[#A2B5CB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input v-model="form.search" type="text" placeholder="Cari indikator atau kata kunci..."
                        class="w-full pl-14 pr-6 py-4 bg-gray-50 border border-gray-400 rounded-2xl font-medium text-[#000B58] focus:outline-none focus:border-[#00139E] transition-all">
                </div>

                <div class="lg:w-64 relative custom-select-container">
                    <div @click="toggleDropdown('tema')"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-400 rounded-2xl font-bold text-[#000B58] flex justify-between items-center cursor-pointer hover:bg-white transition-all shadow-sm"
                        :class="{'ring-2 ring-[#00139E] border-transparent': openDropdown === 'tema'}">
                        <span class="truncate text-sm">{{ getSelectedName(temas, form.id_tema, 'id_tema', 'nama_tema', 'Semua Tema') }}</span>
                        <svg class="w-4 h-4 transition-transform duration-300" :class="{'rotate-180': openDropdown === 'tema'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    <div v-if="openDropdown === 'tema'" class="absolute z-50 w-full mt-2 bg-white border border-gray-400 rounded-xl shadow-2xl py-2 max-h-64 overflow-y-auto animate-in fade-in zoom-in duration-200">
                        <div @click="selectOption('id_tema', '')" class="px-5 py-3 hover:bg-[#00139E]/5 hover:text-[#00139E] cursor-pointer text-sm font-medium transition-colors">Semua Tema</div>
                        <div v-for="t in temas" :key="t.id_tema" @click="selectOption('id_tema', t.id_tema)"
                             class="px-5 py-3 hover:bg-[#00139E]/5 hover:text-[#00139E] cursor-pointer text-sm font-medium transition-colors"
                             :class="{'text-[#00139E] bg-[#00139E]/5': form.id_tema == t.id_tema}">
                            {{ t.nama_tema }}
                        </div>
                    </div>
                </div>

                <div class="lg:w-64 relative custom-select-container">
                    <div @click="toggleDropdown('urusan')"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-400 rounded-2xl font-bold text-[#000B58] flex justify-between items-center cursor-pointer hover:bg-white transition-all shadow-sm"
                        :class="{'ring-2 ring-[#00139E] border-transparent': openDropdown === 'urusan'}">
                        <span class="truncate text-sm">{{ getSelectedName(urusans, form.id_urusan, 'id_urusan', 'nama_urusan', 'Semua Urusan') }}</span>
                        <svg class="w-4 h-4 transition-transform duration-300" :class="{'rotate-180': openDropdown === 'urusan'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    <div v-if="openDropdown === 'urusan'" class="absolute z-50 w-full mt-2 bg-white border border-gray-400 rounded-xl shadow-2xl py-2 max-h-64 overflow-y-auto animate-in fade-in zoom-in duration-200">
                        <div @click="selectOption('id_urusan', '')" class="px-5 py-3 hover:bg-[#00139E]/5 hover:text-[#00139E] cursor-pointer text-sm font-medium transition-colors">Semua Urusan</div>
                        <div v-for="u in urusans" :key="u.id_urusan" @click="selectOption('id_urusan', u.id_urusan)"
                             class="px-5 py-3 hover:bg-[#00139E]/5 hover:text-[#00139E] cursor-pointer text-sm font-medium transition-colors"
                             :class="{'text-[#00139E] bg-[#00139E]/5': form.id_urusan == u.id_urusan}">
                            {{ u.nama_urusan }}
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="form.search || form.id_tema || form.id_urusan" class="flex flex-wrap items-center gap-3 mt-6 ml-2 animate-in slide-in-from-left duration-300">
                <span v-if="form.id_tema" class="flex items-center gap-2 px-4 py-2 bg-[#00139E] text-white rounded-full text-[10px] font-black uppercase shadow-sm">
                    TEMA: {{ getSelectedName(temas, form.id_tema, 'id_tema', 'nama_tema', '') }}
                    <button @click="form.id_tema = ''" class="hover:scale-125 transition-transform">×</button>
                </span>
                <span v-if="form.id_urusan" class="flex items-center gap-2 px-4 py-2 bg-[#00139E] text-white rounded-full text-[10px] font-black uppercase shadow-sm">
                    URUSAN: {{ getSelectedName(urusans, form.id_urusan, 'id_urusan', 'nama_urusan', '') }}
                    <button @click="form.id_urusan = ''" class="hover:scale-125 transition-transform">×</button>
                </span>
                <button @click="clearFilters" class="text-xs font-black text-[#A2B5CB] hover:text-[#FF1414] uppercase ml-2 tracking-widest transition-colors">
                    Hapus Semua Filter
                </button>
            </div>
        </div>

        <div v-if="results.data.length > 0" class="bg-white rounded-[2rem] border border-gray-400 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-400">
                            <th class="px-8 py-5 text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.15em]">Nama Indikator</th>
                            <th class="px-6 py-5 text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.15em] text-center">Tahun</th>
                            <th class="px-6 py-5 text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.15em] text-center">Satuan</th>
                            <th class="px-6 py-5 text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.15em]">Urusan / Instansi</th>
                            <th class="px-8 py-5 text-[10px] font-black text-[#A2B5CB] uppercase tracking-[0.15em] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="item in results.data" :key="item.id_data" class="group hover:bg-[#00139E]/5 transition-all duration-300">
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-[#000B58] group-hover:text-[#00139E] transition-colors leading-snug">
                                        {{ item.nama_indikator }}
                                    </span>
                                    <span class="text-[10px] font-bold text-[#A2B5CB] mt-1 uppercase tracking-wider">
                                        Tema: {{ item.tema?.nama_tema || 'Umum' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="inline-block px-3 py-1 bg-[#00139E]/10 text-[#00139E] rounded-lg text-[11px] font-black">
                                    {{ item.tahun }}
                                </span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-xs font-bold text-gray-500 italic">
                                    {{ item.satuan }}
                                </span>
                            </td>
                            <td class="px-6 py-6">
                                <span class="text-[11px] font-bold text-[#000B58]/70 uppercase leading-tight block max-w-[200px]">
                                    {{ item.urusan?.nama_urusan || 'BAPPEDA PROVINSI NTB' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <Link :href="`/dataset/${item.id_data}`" 
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#00139E] hover:bg-[#000B58] text-white text-[10px] font-black rounded-xl transition-all hover:shadow-lg hover:shadow-[#00139E]/30 active:scale-95">
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

            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-400 flex justify-center gap-2">
                <Link v-for="link in results.links" :key="link.label" :href="link.url || '#'" v-html="link.label"
                    class="px-5 py-2.5 rounded-xl text-[10px] font-black transition-all"
                    :class="[
                        link.active ? 'bg-[#000B58] text-white shadow-lg scale-105' : 'bg-white text-[#A2B5CB] border border-gray-400 hover:border-[#00139E] hover:text-[#00139E]',
                        !link.url ? 'opacity-30 cursor-not-allowed' : ''
                    ]" />
            </div>
        </div>

        <div v-else class="text-center py-32 bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-400">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-400">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <p class="text-[#000B58] font-black text-xl">Data tidak ditemukan</p>
            <p class="text-[#A2B5CB] font-medium mt-2">Silakan gunakan kata kunci lain atau hapus filter.</p>
        </div>
    </div>
</template>