<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

defineProps({
  bidang: Array
})

const hapus = (id) => {
  if (confirm('Yakin ingin menghapus bidang ini?')) {
    router.delete(`/admin/bidang/${id}`)
  }
}
</script>

<template>
  <Head title="Bidang" />

  <div class="bg-white p-8 rounded-2xl max-w-xl">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-xl font-bold">Bidang</h2>
      <Link
        href="/admin/bidang/create"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold"
      >
        + Tambah Bidang
      </Link>
    </div>

    <ul class="divide-y">
      <li
        v-for="b in bidang"
        :key="b.id_bidang"
        class="py-3 flex justify-between items-center"
      >
        <span>{{ b.nama_bidang }}</span>
        <div class="space-x-3">
          <Link
            :href="`/admin/bidang/${b.id_bidang}/edit`"
            class="text-blue-600 font-semibold"
          >
            Edit
          </Link>
          <button
            @click="hapus(b.id_bidang)"
            class="text-red-600 font-semibold"
          >
            Hapus
          </button>
        </div>
      </li>

      <li v-if="bidang.length === 0" class="py-6 text-center text-gray-400">
        Belum ada bidang
      </li>
    </ul>
  </div>
</template>
