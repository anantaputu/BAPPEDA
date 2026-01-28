<script setup>
import { Head, useForm } from '@inertiajs/vue3'
// Tambahkan props previewData
const props = defineProps({
    upload: Object,
    excelColumns: Object, // Sekarang ini berbentuk { "A": "Nama Kolom" }
    previewData: Array,   // Array of 5 rows
    fields: Array
})

const form = useForm({
    mapping: {},
    new_fields: {}
})

const handleSelectChange = (colKey) => {
    if (form.mapping[colKey] === '__new__') {
        form.new_fields[colKey] = {
            nama_field: props.excelColumns[colKey],
            tipe_field: 'text'
        }
    } else {
        delete form.new_fields[colKey]
    }
}

const submit = () => {
    form.post(`/input-data/${props.upload.id_upload}/mapping`)
}
</script>

<template>
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Mapping & Preview Data</h1>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm">
                        <th class="p-4 border">Kolom Excel</th>
                        <th class="p-4 border">Contoh Data (Preview)</th>
                        <th class="p-4 border w-1/3">Mapping Ke Field</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(colName, colKey) in excelColumns" :key="colKey">
                        <td class="p-4 border bg-gray-50">
                            <div class="font-bold text-gray-700">{{ colName }}</div>
                            <div class="text-xs text-gray-400">Kolom {{ colKey }}</div>
                        </td>
                        
                        <td class="p-4 border">
                            <ul class="text-xs text-gray-600 space-y-1">
                                <li v-for="(row, index) in previewData" :key="index" class="truncate max-w-[200px]">
                                    <span class="text-gray-300 mr-2">{{ index + 1 }}.</span> 
                                    {{ row[colKey] || '(kosong)' }}
                                </li>
                            </ul>
                        </td>

                        <td class="p-4 border">
                            <select 
                                v-model="form.mapping[colKey]" 
                                @change="handleSelectChange(colKey)"
                                class="w-full rounded-md border-gray-300"
                            >
                                <option value="">-- Abaikan --</option>
                                <option v-for="f in fields" :key="f.id_field" :value="f.id_field">
                                    {{ f.nama_field }}
                                </option>
                                <option value="__new__">+ Tambah Field Baru</option>
                            </select>

                            <div v-if="form.mapping[colKey] === '__new__'" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded">
                                <input v-model="form.new_fields[colKey].nama_field" class="w-full text-sm mb-2" placeholder="Nama Field" />
                                <select v-model="form.new_fields[colKey].tipe_field" class="w-full text-sm">
                                    <option value="text">Text</option>
                                    <option value="number">Number</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <button @click="submit" class="mt-6 bg-green-600 text-white px-10 py-3 rounded-xl font-bold hover:bg-green-700 shadow-lg transition">
            Simpan & Proses Data
        </button>
    </div>
</template>