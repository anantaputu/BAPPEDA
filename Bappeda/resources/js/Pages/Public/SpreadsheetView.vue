<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    groupedData: Object, // Data yang sudah di-group di Laravel
    allYears: Array,     // Daftar tahun untuk header kolom
    listTema: Array,
    filters: Object
});

const selectedTema = ref(props.filters.tema || '');

const filterData = () => {
    router.get('/data-spreadsheet', { tema: selectedTema.value }, { preserveState: true });
};

// Helper untuk mencari nilai berdasarkan tahun di dalam array values
const getValue = (values, year) => {
    const found = values.find(v => v.tahun == year);
    return found ? found.nilai : '-';
};
</script>

<template>
    <Head title="Kumpulan Data Master" />

    <div class="min-h-screen bg-white py-12 px-6">
        <div class="max-w-[95%] mx-auto">
            
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-3xl font-black text-[#000B58] uppercase">Big Data Spreadsheet</h2>
                    <p class="text-gray-500 font-medium">Rekapitulasi seluruh indikator pembangunan per tahun.</p>
                </div>

                <div class="flex gap-4">
                    <select v-model="selectedTema" @change="filterData" class="rounded-xl border-gray-300 text-sm font-bold shadow-sm">
                        <option value="">Semua Kelompok Tema</option>
                        <option v-for="t in listTema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                    </select>
                </div>
            </div>

            <div class="border border-gray-300 rounded-2xl overflow-hidden shadow-2xl shadow-blue-900/5">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse table-fixed">
                        <thead>
                            <tr class="bg-[#000B58] text-white">
                                <th class="p-4 text-xs font-black uppercase tracking-widest w-[400px] border-r border-white/10 sticky left-0 bg-[#000B58] z-20">Indikator</th>
                                <th class="p-4 text-xs font-black uppercase tracking-widest w-[120px] text-center border-r border-white/10">Satuan</th>
                                <th v-for="year in allYears" :key="year" class="p-4 text-xs font-black uppercase tracking-widest w-[100px] text-center border-r border-white/10">
                                    {{ year }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(indicators, temaName) in groupedData" :key="temaName">
                                <tr class="bg-gray-100">
                                    <td :colspan="allYears.length + 2" class="p-4 text-sm font-black text-blue-700 uppercase tracking-widest border-b border-gray-300">
                                        📁 Kelompok: {{ temaName }}
                                    </td>
                                </tr>

                                <tr v-for="item in indicators" :key="item.id_data" class="hover:bg-blue-50/50 transition-colors border-b border-gray-200 group">
                                    <td class="p-4 text-sm font-bold text-gray-700 border-r border-gray-100 sticky left-0 bg-white group-hover:bg-blue-50 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.05)]">
                                        <Link :href="`/dataset/${item.id_data}`" class="hover:text-blue-600 uppercase text-[12px] leading-tight">
                                            {{ item.nama_indikator }}
                                        </Link>
                                    </td>
                                    <td class="p-4 text-xs font-medium text-gray-400 text-center border-r border-gray-100">
                                        {{ item.satuan }}
                                    </td>
                                    <td v-for="year in allYears" :key="year" class="p-4 text-sm font-black text-gray-800 text-center border-r border-gray-100">
                                        {{ getValue(item.values, year) }}
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Membuat Scrollbar lebih rapi ala Excel */
.overflow-x-auto::-webkit-scrollbar { height: 10px; }
.overflow-x-auto::-webkit-scrollbar-track { @apply bg-gray-100; }
.overflow-x-auto::-webkit-scrollbar-thumb { @apply bg-gray-300 rounded-full hover:bg-gray-400 transition-all; }
</style>