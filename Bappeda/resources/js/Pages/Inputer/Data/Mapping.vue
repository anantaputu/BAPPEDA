<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const props = defineProps({
    upload: Object,
    excelColumns: Object, 
    previewData: Array,   
    fields: Array,
    autoMap: Object 
});

const form = useForm({
    mapping: props.autoMap ? { ...props.autoMap } : {},
    new_fields: {}
});

const handleSelectChange = (colKey) => {
    if (form.mapping[colKey] === '__new__') {
        form.new_fields[colKey] = {
            nama_field: props.excelColumns[colKey],
            tipe_field: 'text'
        };
    } else if (form.new_fields[colKey]) {
        delete form.new_fields[colKey];
    }
};

const submit = () => {
    form.post(`/input-data/${props.upload.id_upload}/mapping`);
};
</script>

<template>
    <Head title="Mapping Data" />

    <div class="max-w-7xl mx-auto space-y-8">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Mapping & Preview</h1>
            <p class="text-gray-400 text-xs font-bold mt-1 uppercase">Sinkronisasi kolom Excel dengan struktur database</p>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest w-1/4">Kolom Excel</th>
                        <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest w-1/3">Preview Data</th>
                        <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Mapping Field</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="(colName, colKey) in excelColumns" :key="colKey" class="group">
                        <td class="p-6 align-top">
                            <div class="font-extrabold text-gray-900 text-sm">{{ colName }}</div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">KODE: {{ colKey }}</div>
                        </td>
                        
                        <td class="p-6 align-top">
                            <div class="space-y-1">
                                <div v-for="(row, index) in previewData.slice(0, 3)" :key="index" class="text-xs text-gray-500 font-medium flex gap-2">
                                    <span class="text-blue-300">{{ index + 1 }}.</span>
                                    <span class="truncate">{{ row[colKey] || '(kosong)' }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="p-6 align-top">
                            <select 
                                v-model="form.mapping[colKey]" 
                                @change="handleSelectChange(colKey)"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500 transition"
                            >
                                <option value="">-- Abaikan Kolom Ini --</option>
                                <option v-for="f in fields" :key="f.id_field" :value="f.id_field">
                                    {{ f.nama_field }}
                                </option>
                                <option value="__new__" class="font-black text-blue-600">+ TAMBAH FIELD BARU</option>
                            </select>

                            <div v-if="form.mapping[colKey] === '__new__'" class="mt-4 p-5 bg-blue-50/50 rounded-2xl border border-blue-100 space-y-4">
                                <div>
                                    <label class="text-[10px] font-black text-blue-800 uppercase">Nama Field Baru</label>
                                    <input v-model="form.new_fields[colKey].nama_field" class="w-full mt-1 border-blue-100 rounded-lg text-sm" />
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-blue-800 uppercase">Tipe Data</label>
                                    <select v-model="form.new_fields[colKey].tipe_field" class="w-full mt-1 border-blue-100 rounded-lg text-sm font-bold">
                                        <option value="text">Text</option>
                                        <option value="number">Number</option>
                                        <option value="date">Date</option>
                                    </select>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-end">
            <button 
                @click="submit" 
                :disabled="form.processing"
                class="bg-green-600 text-white px-12 py-5 rounded-2xl font-black tracking-widest hover:bg-green-700 shadow-xl shadow-green-100 transition disabled:opacity-50"
            >
                {{ form.processing ? 'SEDANG MEMPROSES...' : 'SIMPAN & PROSES DATA' }}
            </button>
        </div>
    </div>
</template>