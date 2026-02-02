<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const props = defineProps({
    dataIndikator: Object, // Data existing
    tema: Array, urusan: Array, bidang: Array, frekuensi: Array
});

const form = useForm({
    nama_indikator: props.dataIndikator.nama_indikator,
    id_tema: props.dataIndikator.id_tema,
    id_urusan: props.dataIndikator.id_urusan,
    status: props.dataIndikator.status,
    // ... field lainnya
});

const submit = () => form.put(`/data/${props.dataIndikator.id_data}`);
</script>

<template>
    <Head title="Edit Indikator" />
    <div class="max-w-6xl mx-auto">
        <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Edit Indikator: <span class="text-blue-600">#{{ dataIndikator.id_data }}</span></h1>
            <div class="space-y-6">
                <button @click="submit" class="bg-[#4A6CF7] text-white px-10 py-5 rounded-2xl font-black shadow-xl">UPDATE DATA</button>
            </div>
        </div>
    </div>
</template>