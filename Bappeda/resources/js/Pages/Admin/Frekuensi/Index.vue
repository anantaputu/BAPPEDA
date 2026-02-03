<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

defineProps({
  frekuensi: Array
})

const hapus = (id) => {
  if (confirm('Yakin ingin menghapus frekuensi ini?')) {
    router.delete(`/admin/frekuensi/${id}`)
  }
}
</script>

<template>
  <Head title="Frekuensi" />

  <div class="bg-white p-8 rounded-2xl max-w-xl">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-xl font-bold">Frekuensi</h2>
      <Link
        href="/admin/frekuensi/create"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold"
      >
        + Tambah Frekuensi
      </Link>
    </div>

    <ul class="divide-y">
      <li
        v-for="f in frekuensi"
        :key="f.id_frekuensi"
        class="py-3 flex justify-between items-center"
      >
        <span>{{ f.nama_frekuensi }}</span>
        <div class="space-x-3">
          <Link
            :href="`/admin/frekuensi/${f.id_frekuensi}/edit`"
            class="text-blue-600 font-semibold"
          >
            Edit
          </Link>
          <button
            @click="hapus(f.id_frekuensi)"
            class="text-red-600 font-semibold"
          >
            Hapus
          </button>
        </div>
      </li>

      <li v-if="frekuensi.length === 0" class="py-6 text-center text-gray-400">
        Belum ada frekuensi
      </li>
    </ul>
  </div>
</template>
