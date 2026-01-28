<script setup>
import { Head, Link } from '@inertiajs/vue3'

defineProps({
    dataList: {
        type: Array,
        default: () => []
    }
})
</script>

<template>
    <Head title="Input Data" />

    <div class="max-w-6xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">
            Daftar Data untuk Input
        </h1>

        <div v-if="dataList.length === 0" class="text-gray-500">
            Belum ada data aktif.
        </div>

        <table v-else class="w-full border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-3 text-left">Nama Data</th>
                    <th class="border p-3 text-center">Satuan</th>
                    <th class="border p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in dataList" :key="item.id_data">
                    <td class="border p-3">
                        {{ item.nama_indikator }}
                    </td>
                    <td class="border p-3 text-center">
                        {{ item.satuan ?? '-' }}
                    </td>
                    <td class="border p-3 text-center">
                        <Link
                            :href="`/input-data/${item.id_data}`"
                            class="text-blue-600 font-semibold"
                        >
                            Input Data
                        </Link>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
