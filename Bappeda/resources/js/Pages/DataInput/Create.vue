<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    data: Object
})

const form = useForm({
    periode: '',
    file: null
})

const submit = () => {
    form.post(`/input-data/${props.data.id_data}`, {
        forceFormData: true
    })
}
</script>

<template>
    <Head title="Upload Excel/CSV" />

    <div class="max-w-xl mx-auto p-6">
        <h1 class="text-xl font-bold mb-4">
            Upload Data:
            <span class="text-blue-600">{{ data.nama_indikator }}</span>
        </h1>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block font-bold mb-1">Periode</label>
                <input v-model="form.periode" class="border w-full p-2 rounded" placeholder="Contoh: 2024" />
            </div>

            <div>
                <label class="block font-bold mb-1">File Excel atau CSV</label>
                
                <input 
                    type="file" 
                    @change="e => form.file = e.target.files[0]" 
                    accept=".xlsx, .xls, .csv"
                    class="border w-full p-2 rounded bg-white" 
                />
                <p class="text-xs text-gray-500 mt-1">Format didukung: .xlsx, .xls, .csv</p>
            </div>

            <button class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                Upload
            </button>

            <Link href="/input-data" class="block mt-4 text-gray-600 hover:text-gray-900">
                Kembali
            </Link>
        </form>
    </div>
</template>