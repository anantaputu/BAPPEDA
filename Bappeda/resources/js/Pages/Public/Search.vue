<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, onMounted, onUnmounted } from 'vue';
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

// State untuk UI Dropdown Custom
const openDropdown = ref(null); // 'tema' atau 'urusan' atau null

const toggleDropdown = (name) => {
    openDropdown.value = openDropdown.value === name ? null : name;
};

// Fungsi memilih opsi dan otomatis menutup dropdown
const selectOption = (field, value) => {
    form.value[field] = value;
    openDropdown.value = null;
};

// Helper untuk menampilkan nama label yang terpilih
const getSelectedName = (list, id, fieldId, fieldName, defaultLabel) => {
    const found = list.find(item => item[fieldId] == id);
    return found ? found[fieldName] : defaultLabel;
};

// Eksekusi pencarian dengan debounce agar server tidak berat
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

// Logika menutup dropdown saat klik di luar elemen
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

    <div class="max-w-[85%] mx-auto py-12 min-h-screen mt-20">
        <h1 class="text-4xl font-black text-[#000B58] mb-10 tracking-tight">Pencarian Data Indikator</h1>

        <div class="bg-white p-4 rounded-[2rem] border border-gray-300 shadow-xl shadow-[#000B58]/5 mb-12">
            <div class="flex flex-col lg:flex-row gap-4">
                
                <div class="flex-1 relative group">
                    <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-[#A2B5CB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input 
                      v-model="form.search" 
                      type="text" 
                      placeholder="Cari indikator atau kata kunci..."
                      class="w-full pl-14 pr-6 py-4 bg-gray-50 border border-gray-300 rounded-2xl font-medium text-[#000B58] focus:outline-none focus:border-[#00139E] transition-all"
                    >
                </div>

                <div class="lg:w-64 relative custom-select-container">
                    <div @click="toggleDropdown('tema')"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-300 rounded-2xl font-bold text-[#000B58] flex justify-between items-center cursor-pointer hover:bg-white transition-all shadow-sm"
                        :class="{'ring-2 ring-[#00139E] border-transparent': openDropdown === 'tema'}"
                    >
                        <span class="truncate text-sm">{{ getSelectedName(temas, form.id_tema, 'id_tema', 'nama_tema', 'Semua Tema') }}</span>
                        <svg class="w-4 h-4 transition-transform duration-300" :class="{'rotate-180': openDropdown === 'tema'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    <div v-if="openDropdown === 'tema'" 
                         class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-2xl py-2 max-h-64 overflow-y-auto animate-in fade-in zoom-in duration-200">
                        <div @click="selectOption('id_tema', '')" class="px-5 py-3 hover:bg-[#00139E]/5 hover:text-[#00139E] cursor-pointer text-sm font-medium transition-colors">Semua Tema</div>
                        <div v-for="t in temas" :key="t.id_tema" 
                             @click="selectOption('id_tema', t.id_tema)"
                             class="px-5 py-3 hover:bg-[#00139E]/5 hover:text-[#00139E] cursor-pointer text-sm font-medium transition-colors"
                             :class="{'text-[#00139E] bg-[#00139E]/5': form.id_tema == t.id_tema}">
                            {{ t.nama_tema }}
                        </div>
                    </div>
                </div>

                <div class="lg:w-64 relative custom-select-container">
                    <div @click="toggleDropdown('urusan')"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-300 rounded-2xl font-bold text-[#000B58] flex justify-between items-center cursor-pointer hover:bg-white transition-all shadow-sm"
                        :class="{'ring-2 ring-[#00139E] border-transparent': openDropdown === 'urusan'}"
                    >
                        <span class="truncate text-sm">{{ getSelectedName(urusans, form.id_urusan, 'id_urusan', 'nama_urusan', 'Semua Urusan') }}</span>
                        <svg class="w-4 h-4 transition-transform duration-300" :class="{'rotate-180': openDropdown === 'urusan'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    <div v-if="openDropdown === 'urusan'" 
                         class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-2xl py-2 max-h-64 overflow-y-auto animate-in fade-in zoom-in duration-200">
                        <div @click="selectOption('id_urusan', '')" class="px-5 py-3 hover:bg-[#00139E]/5 hover:text-[#00139E] cursor-pointer text-sm font-medium transition-colors">Semua Urusan</div>
                        <div v-for="u in urusans" :key="u.id_urusan" 
                             @click="selectOption('id_urusan', u.id_urusan)"
                             class="px-5 py-3 hover:bg-[#00139E]/5 hover:text-[#00139E] cursor-pointer text-sm font-medium transition-colors"
                             :class="{'text-[#00139E] bg-[#00139E]/5': form.id_urusan == u.id_urusan}">
                            {{ u.nama_urusan }}
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="form.search || form.id_tema || form.id_urusan" 
                class="flex flex-wrap items-center gap-3 mt-6 ml-2 animate-in slide-in-from-left duration-300">
                
                <span v-if="form.id_tema" 
                    class="flex items-center gap-2 px-4 py-2 bg-[#00139E] text-white rounded-full text-[10px] font-black uppercase shadow-sm">
                    TEMA: {{ getSelectedName(temas, form.id_tema, 'id_tema', 'nama_tema', '') }}
                    <button @click="form.id_tema = ''" class="hover:scale-125 transition-transform">×</button>
                </span>

                <span v-if="form.id_urusan" 
                    class="flex items-center gap-2 px-4 py-2 bg-[#00139E] text-white rounded-full text-[10px] font-black uppercase shadow-sm">
                    URUSAN: {{ getSelectedName(urusans, form.id_urusan, 'id_urusan', 'nama_urusan', '') }}
                    <button @click="form.id_urusan = ''" class="hover:scale-125 transition-transform">×</button>
                </span>

                <button @click="clearFilters" 
                        class="text-xs font-black text-[#A2B5CB] hover:text-[#FF1414] uppercase ml-2 tracking-widest transition-colors">
                    Hapus Semua Filter
                </button>
            </div>
        </div>

        <div v-if="results.data.length > 0" class="space-y-6">
            <Link v-for="item in results.data" :key="item.id_data" 
                 :href="`/dataset/${item.id_data}`"
                 class="block relative bg-white p-6 lg:p-8 rounded-2xl border-l-4 border-l-[#00139E] border border-gray-200 shadow-sm hover:shadow-xl hover:translate-x-2 transition-all group">
                
                <div class="flex flex-col lg:flex-row justify-between items-start gap-6">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-[#000B58] leading-tight group-hover:text-[#00139E] transition-colors mb-2">
                            {{ item.nama_indikator }} - {{ item.tahun_data }}
                        </h3>
                        <p class="text-sm text-[#A2B5CB] font-medium leading-relaxed mb-6">
                            Data indikator {{ item.nama_indikator }} dengan satuan {{ item.satuan }} yang dikelola oleh urusan {{ item.urusan?.nama_urusan || 'Umum' }}.
                        </p>
                        
                        <div class="flex items-center gap-4">
                            <span class="px-3 py-1 bg-[#107C41] text-white text-[10px] font-black rounded uppercase tracking-wider">
                                XLSX
                            </span>
                            <span class="text-[10px] font-bold text-[#00139E] opacity-0 group-hover:opacity-100 transition-opacity uppercase tracking-widest">
                                Lihat Detail Dataset &rarr;
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-3 min-w-[220px]">
                        <div class="flex items-center gap-2">
                            <div class="px-3 py-1 bg-[#E8FAF0] text-[#10B981] text-[10px] font-black rounded-lg border border-[#10B981]/20">
                                TERBUKA
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-[#00139E]/60 text-right uppercase tracking-tighter max-w-[250px] leading-tight">
                            {{ item.urusan?.nama_urusan || 'Badan Perencanaan Pembangunan Daerah' }}
                        </span>
                    </div>
                </div>
            </Link>

            <div class="mt-16 flex justify-center gap-2">
                <Link v-for="link in results.links" :key="link.label" :href="link.url || '#'" v-html="link.label"
                    class="px-5 py-2.5 rounded-xl text-xs font-black transition-all"
                    :class="[
                        link.active ? 'bg-[#000B58] text-white shadow-lg scale-105' : 'bg-white text-[#A2B5CB] border border-gray-200 hover:border-[#00139E] hover:text-[#00139E]',
                        !link.url ? 'opacity-30 cursor-not-allowed' : ''
                    ]" />
            </div>
        </div>

        <div v-else class="text-center py-32 bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-300 animate-pulse">
            <p class="text-[#000B58] font-black text-xl">Data tidak ditemukan</p>
            <p class="text-[#A2B5CB] font-medium mt-2">Silakan gunakan kata kunci lain atau hapus filter.</p>
        </div>
    </div>
</template>