<script setup>
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({
    upload: Object,
    excelColumns: Object, 
    previewData: Array,   
    fields: Array,
    autoMap: Object // Pastikan autoMap diterima dari controller jika ada fitur auto-map
})

const form = useForm({
    // Gunakan autoMap jika ada, jika tidak kosongkan
    mapping: props.autoMap ? { ...props.autoMap } : {},
    new_fields: {}
})

const handleSelectChange = (colKey) => {
    if (form.mapping[colKey] === '__new__') {
        // Inisialisasi object untuk field baru
        form.new_fields[colKey] = {
            nama_field: props.excelColumns[colKey], // Default nama ambil dari header excel
            tipe_field: 'text'
        }
    } else {
        // Hapus data field baru jika user membatalkan pilihan __new__
        if (form.new_fields[colKey]) {
            delete form.new_fields[colKey]
        }
    }
}

const submit = () => {
    form.post(`/input-data/${props.upload.id_upload}/mapping`, {
        onSuccess: () => alert('Mapping berhasil disimpan!'),
        onError: (errors) => console.log(errors) // Cek console jika ada error validasi
    })
}
</script>

<template>
    <div class="max-w-7xl mx-auto p-6">
        <Head title="Mapping Data" />
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
                                class="w-full rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">-- Abaikan --</option>
                                <option v-for="f in fields" :key="f.id_field" :value="f.id_field">
                                    {{ f.nama_field }}
                                </option>
                                <option value="__new__" class="font-bold text-blue-600">+ Tambah Field Baru</option>
                            </select>

                            <div v-if="form.mapping[colKey] === '__new__' && form.new_fields[colKey]" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded animate-fade-in">
                                <label class="text-xs font-bold text-blue-800">Nama Field Baru:</label>
                                <input 
                                    v-model="form.new_fields[colKey].nama_field" 
                                    class="w-full text-sm mb-2 mt-1 border-gray-300 rounded" 
                                    placeholder="Nama Field" 
                                />
                                
                                <label class="text-xs font-bold text-blue-800">Tipe Data:</label>
                                <select 
                                    v-model="form.new_fields[colKey].tipe_field" 
                                    class="w-full text-sm mt-1 border-gray-300 rounded"
                                >
                                    <option value="text">Text</option>
                                    <option value="number">Number</option>
                                    <option value="date">Date</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <button 
                @click="submit" 
                :disabled="form.processing"
                class="bg-green-600 text-white px-10 py-3 rounded-xl font-bold hover:bg-green-700 shadow-lg transition disabled:opacity-50"
            >
                {{ form.processing ? 'Menyimpan...' : 'Simpan & Proses Data' }}
            </button>
        </div>
    </div>
</template>