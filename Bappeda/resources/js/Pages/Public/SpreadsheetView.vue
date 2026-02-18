<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

defineOptions({ layout: AppLayout });

const props = defineProps({
    groupedData: Object,
    timeColumns: Array,
    metadata: Object, // Berisi list Tema, Urusan, Bidang, Frekuensi
    filters: Object
});

// State untuk Filter
const form = ref({
    search: props.filters.search || '',
    tema: props.filters.tema || '',
    urusan: props.filters.urusan || '',
    bidang: props.filters.bidang || '',
    frekuensi: props.filters.frekuensi || '',
    group_by: props.filters.group_by || 'tema' // Default grouping
});

// Fungsi Reload Data saat filter berubah
const updateView = debounce(() => {
    router.get('/data-spreadsheet', form.value, { 
        preserveState: true, 
        preserveScroll: true,
        only: ['groupedData', 'timeColumns', 'filters'] 
    });
}, 500);

// Watch semua perubahan di form
watch(form, () => { updateView(); }, { deep: true });

// Helper ambil nilai
const getValue = (values, timeKey) => {
    const found = values.find(v => v.tahun == timeKey); // Sesuaikan 'tahun' dengan nama kolom DB Anda
    return found ? found.nilai : '-';
};

// Helper Label Grouping
const getGroupLabel = (key) => {
    if (form.value.group_by === 'urusan') return '🏛️ Urusan Pemerintahan';
    if (form.value.group_by === 'bidang') return '🏢 Bidang / Instansi';
    if (form.value.group_by === 'frekuensi') return '⏰ Frekuensi Data';
    return '📂 Tema Sektoral';
};
</script>

<template>
    <Head title="Spreadsheet Data Master" />

    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-[98%] mx-auto">
            
            <div class="mb-8">
                <h2 class="text-3xl font-black text-[#000B58] uppercase tracking-tight">Master Data View</h2>
                <p class="text-gray-500 text-sm font-medium mt-1">Lihat, filter, dan bandingkan seluruh indikator pembangunan dalam satu tampilan.</p>
            </div>

            <div class="bg-white p-6 rounded-[1.5rem] shadow-lg border border-gray-200 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    
                    <div class="lg:col-span-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Cari Indikator</label>
                        <input v-model="form.search" type="text" placeholder="Ketik nama data..." class="w-full rounded-xl border-gray-300 text-sm font-bold focus:ring-[#00139E] focus:border-[#00139E]">
                    </div>

                    <div class="bg-blue-50/50 p-2 rounded-xl border border-blue-100">
                        <label class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1 block">Kelompokkan Data</label>
                        <select v-model="form.group_by" class="w-full rounded-lg border-blue-200 text-sm font-black text-[#00139E] bg-white focus:ring-[#00139E]">
                            <option value="tema">Per Tema</option>
                            <option value="urusan">Per Urusan</option>
                            <option value="bidang">Per Bidang</option>
                            <option value="frekuensi">Per Frekuensi</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Filter Urusan</label>
                        <select v-model="form.urusan" class="w-full rounded-xl border-gray-300 text-xs font-bold text-gray-600">
                            <option value="">Semua Urusan</option>
                            <option v-for="u in metadata.urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Filter Bidang</label>
                        <select v-model="form.bidang" class="w-full rounded-xl border-gray-300 text-xs font-bold text-gray-600">
                            <option value="">Semua Bidang</option>
                            <option v-for="b in metadata.bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Filter Frekuensi</label>
                        <select v-model="form.frekuensi" class="w-full rounded-xl border-gray-300 text-xs font-bold text-gray-600">
                            <option value="">Semua Waktu</option>
                            <option v-for="f in metadata.frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                        </select>
                    </div>

                </div>
            </div>

            <div class="bg-white border border-gray-300 rounded-2xl overflow-hidden shadow-2xl shadow-blue-900/5 flex flex-col max-h-[70vh]">
                <div class="overflow-auto flex-1 custom-scrollbar">
                    <table class="w-full text-left border-collapse relative">
                        <thead class="bg-[#000B58] text-white sticky top-0 z-30">
                            <tr>
                                <th class="p-4 text-[10px] uppercase font-black tracking-widest w-[400px] border-r border-white/10 sticky left-0 bg-[#000B58] z-20 shadow-lg">Nama Indikator</th>
                                <th class="p-4 text-[10px] uppercase font-black tracking-widest w-[100px] text-center border-r border-white/10">Satuan</th>
                                <th class="p-4 text-[10px] uppercase font-black tracking-widest w-[100px] text-center border-r border-white/10">Frekuensi</th>
                                <th v-for="col in timeColumns" :key="col" class="p-4 text-[10px] uppercase font-black tracking-widest w-[100px] text-center border-r border-white/10 whitespace-nowrap">
                                    {{ col }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(groupItems, groupName) in groupedData" :key="groupName">
                                <tr class="bg-gray-100 sticky top-[49px] z-10">
                                    <td :colspan="timeColumns.length + 3" class="p-3 text-xs font-black text-[#00139E] uppercase tracking-widest border-b border-gray-300 shadow-sm">
                                        {{ getGroupLabel() }}: {{ groupName }}
                                    </td>
                                </tr>

                                <tr v-for="item in groupItems" :key="item.id_data" class="hover:bg-blue-50/50 transition-colors border-b border-gray-200 group">
                                    <td class="p-3 text-xs font-bold text-gray-700 border-r border-gray-100 sticky left-0 bg-white group-hover:bg-blue-50 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.05)]">
                                        <a :href="`/dataset/${item.id_data}`" class="hover:text-blue-600 hover:underline leading-relaxed block">
                                            {{ item.nama_indikator }}
                                        </a>
                                        <div class="flex gap-2 mt-1 opacity-60 text-[9px] uppercase font-black text-gray-400">
                                            <span v-if="form.group_by !== 'tema'">{{ item.tema?.nama_tema }}</span>
                                            <span v-if="form.group_by !== 'urusan'">• {{ item.urusan?.nama_urusan }}</span>
                                        </div>
                                    </td>
                                    
                                    <td class="p-3 text-[10px] font-bold text-gray-500 text-center border-r border-gray-100 bg-gray-50/30">
                                        {{ item.satuan }}
                                    </td>
                                    
                                    <td class="p-3 text-[10px] font-bold text-center border-r border-gray-100" 
                                        :class="item.frekuensi?.nama_frekuensi === 'Tahunan' ? 'text-green-600 bg-green-50' : 'text-amber-600 bg-amber-50'">
                                        {{ item.frekuensi?.nama_frekuensi || '-' }}
                                    </td>

                                    <td v-for="col in timeColumns" :key="col" class="p-3 text-xs font-bold text-gray-800 text-center border-r border-gray-100">
                                        {{ getValue(item.values, col) }}
                                    </td>
                                </tr>
                            </template>
                            
                            <tr v-if="Object.keys(groupedData).length === 0">
                                <td :colspan="timeColumns.length + 3" class="p-10 text-center text-gray-400 font-bold italic">
                                    Data tidak ditemukan dengan filter tersebut.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <p class="text-[10px] text-gray-400 mt-4 italic">* Gunakan scroll horizontal untuk melihat tahun lainnya. Gunakan filter 'Frekuensi' untuk memisahkan data Tahunan dan Bulanan.</p>

        </div>
    </div>
</template>

<style scoped>
/* Scrollbar Kustom agar elegan */
.custom-scrollbar::-webkit-scrollbar { height: 12px; width: 12px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; border: 3px solid #f1f5f9; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>