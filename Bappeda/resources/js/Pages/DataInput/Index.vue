<script setup>
defineProps({
    dataList: Array
})
</script>

<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-6">Pilih Data untuk Diinput</h1>

        <table class="w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Nama Data</th>
                    <th class="p-2 border">Satuan</th>
                    <th class="p-2 border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in dataList" :key="item.id_data">
                    <td class="p-2 border">{{ item.nama_indikator }}</td>
                    <td class="p-2 border">{{ item.satuan }}</td>
                    <td class="p-2 border">
                        <a
                            :href="route('input-data.create', item.id_data)"
                            class="text-blue-600 font-semibold"
                        >
                            Input Data
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
