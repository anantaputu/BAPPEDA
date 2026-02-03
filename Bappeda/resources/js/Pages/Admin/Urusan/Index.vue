<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

defineProps({
  urusan: Array
})

const hapus = (id) => {
  if (confirm('Yakin ingin menghapus urusan ini?')) {
    router.delete(`/admin/urusan/${id}`)
  }
}
</script>

<template>
  <Head title="Urusan" />

  <div class="bg-white p-8 rounded-2xl max-w-xl">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-xl font-bold">Urusan</h2>
      <Link
        href="/admin/urusan/create"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold"
      >
        + Tambah Urusan
      </Link>
    </div>

    <ul class="divide-y">
      <li
        v-for="u in urusan"
        :key="u.id_urusan"
        class="py-3 flex justify-between items-center"
      >
        <span>{{ u.nama_urusan }}</span>
        <div class="space-x-3">
          <Link
            :href="`/admin/urusan/${u.id_urusan}/edit`"
            class="text-blue-600 font-semibold"
          >
            Edit
          </Link>
          <button
            @click="hapus(u.id_urusan)"
            class="text-red-600 font-semibold"
          >
            Hapus
          </button>
        </div>
      </li>

      <li v-if="urusan.length === 0" class="py-6 text-center text-gray-400">
        Belum ada urusan
      </li>
    </ul>
  </div>
</template>
