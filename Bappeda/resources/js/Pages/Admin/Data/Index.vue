<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

const props = defineProps({
  data: Array,
})

const deleteData = (id) => {
  if (confirm('Yakin ingin menghapus indikator ini?')) {
    router.delete(`/admin/data/${id}`)
  }
}
</script>

<template>
  <Head title="Data Indikator" />

  <div class="bg-white rounded-2xl p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="text-2xl font-bold">Data Indikator</h2>
        <p class="text-sm text-gray-400">
          Total: {{ data.length }} indikator
        </p>
      </div>

      <Link
        href="/admin/data/create"
        class="bg-blue-600 text-white px-5 py-2 rounded-lg font-semibold"
      >
        + Tambah Indikator
      </Link>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full border-collapse">
        <thead>
          <tr class="border-b text-left text-sm text-gray-500">
            <th class="py-3">Nama Indikator</th>
            <th>Tema</th>
            <th>Urusan</th>
            <th>Bidang</th>
            <th>Frekuensi</th>
            <th>Status</th>
            <th class="text-right">Aksi</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="item in data"
            :key="item.id_data"
            class="border-b hover:bg-gray-50"
          >
            <td class="py-3 font-semibold">
              {{ item.nama_indikator }}
            </td>
            <td>{{ item.tema?.nama_tema }}</td>
            <td>{{ item.urusan?.nama_urusan }}</td>
            <td>{{ item.bidang?.nama_bidang }}</td>
            <td>{{ item.frekuensi?.nama_frekuensi }}</td>
            <td>
              <span
                :class="item.status === 'aktif'
                  ? 'bg-green-100 text-green-700'
                  : 'bg-gray-200 text-gray-600'"
                class="text-xs px-3 py-1 rounded-full font-semibold"
              >
                {{ item.status }}
              </span>
            </td>
            <td class="text-right space-x-2">
              <Link
                :href="`/admin/data/${item.id_data}/edit`"
                class="text-blue-600 font-semibold"
              >
                Edit
              </Link>

              <button
                class="text-red-600 font-semibold"
                @click="deleteData(item.id_data)"
              >
                Hapus
              </button>
            </td>
          </tr>

          <tr v-if="data.length === 0">
            <td colspan="7" class="text-center py-10 text-gray-400">
              Belum ada data indikator
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
