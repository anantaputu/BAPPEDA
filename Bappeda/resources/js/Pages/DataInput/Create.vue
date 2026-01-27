<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    data: Object
})

const form = useForm({
    periode: '',
    file: null
})

function submit() {
    form.post(route('input-data.store', props.data.id_data))
}
</script>

<template>
    <div class="p-6 max-w-xl">
        <h1 class="text-2xl font-bold mb-4">
            Upload Data: {{ data.nama_indikator }}
        </h1>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block font-semibold">Periode</label>
                <input
                    v-model="form.periode"
                    type="text"
                    class="w-full border p-2 rounded"
                    placeholder="Contoh: 2024 / 2024-TW1"
                />
                <div v-if="form.errors.periode" class="text-red-600 text-sm">
                    {{ form.errors.periode }}
                </div>
            </div>

            <div>
                <label class="block font-semibold">File Excel</label>
                <input
                    type="file"
                    @change="e => form.file = e.target.files[0]"
                    class="w-full"
                />
                <div v-if="form.errors.file" class="text-red-600 text-sm">
                    {{ form.errors.file }}
                </div>
            </div>

            <button
                type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded"
                :disabled="form.processing"
            >
                Upload
            </button>
        </form>
    </div>
</template>
