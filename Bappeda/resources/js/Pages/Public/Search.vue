<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Pastikan props didefinisikan dengan benar
const props = defineProps({
    results: Object, // Laravel Paginate mengembalikan Object
    filters: Object,
    temas: Array
});

defineOptions({ layout: AppLayout });
</script>

<template>
    <Head title="Cari Indikator" />

    <div class="max-w-[90%] mx-auto py-12">
        <h1 class="text-3xl font-black text-gray-900 mb-8">Pencarian Data Indikator</h1>

        <div v-if="results.data && results.data.length > 0">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="item in results.data" :key="item.id_data" 
                     class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="text-[10px] font-black text-blue-600 uppercase mb-2">
                        {{ item.tema?.nama_tema || 'Tanpa Tema' }}
                    </div>
                    <h3 class="font-bold text-gray-900 mb-3">{{ item.nama_indikator }}</h3>
                    <div class="flex justify-between items-center text-sm text-gray-500">
                        <span>Satuan: {{ item.satuan }}</span>
                        <span class="font-bold text-gray-900">{{ item.tahun_data }}</span>
                    </div>
                </div>
            </div>
            
            <div class="mt-10 flex justify-center gap-2">
                <Link v-for="link in results.links" 
                      :key="link.label"
                      :href="link.url"
                      v-html="link.label"
                      class="px-4 py-2 rounded-lg text-sm"
                      :class="link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50'"
                />
            </div>
        </div>

        <div v-else class="text-center py-20 bg-gray-50 rounded-[3rem] border border-dashed border-gray-200">
            <p class="text-gray-400 font-medium">Data tidak ditemukan atau belum tersedia.</p>
        </div>
    </div>
</template>