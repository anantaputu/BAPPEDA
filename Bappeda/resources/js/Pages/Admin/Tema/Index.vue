<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

defineProps({
  tema: Array
})

const hapus = (id) => {
  if (confirm('Yakin ingin menghapus tema ini?')) {
    router.delete(`/admin/tema/${id}`)
  }
}
</script>

<template>
  <Head title="Tema" />

  <div class="bg-white p-8 rounded-2xl max-w-xl">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-xl font-bold">Tema</h2>
      <Link
        href="/admin/tema/create"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold"
      >
        + Tambah Tema
      </Link>
    </div>

    <ul class="divide-y">
      <li
        v-for="t in tema"
        :key="t.id_tema"
        class="py-3 flex justify-between items-center"
      >
        <span>{{ t.nama_tema }}</span>
        <div class="space-x-3">
          <Link
            :href="`/admin/tema/${t.id_tema}/edit`"
            class="text-blue-600 font-semibold"
          >
            Edit
          </Link>
          <button
            @click="hapus(t.id_tema)"
            class="text-red-600 font-semibold"
          >
            Hapus
          </button>
        </div>
      </li>

      <li v-if="tema.length === 0" class="py-6 text-center text-gray-400">
        Belum ada tema
      </li>
    </ul>
  </div>
</template>
