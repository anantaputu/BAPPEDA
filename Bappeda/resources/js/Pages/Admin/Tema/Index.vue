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

  <!-- CONTAINER UTAMA (SAMA DENGAN HALAMAN ADMIN LAIN) -->
  <div class="mx-auto px-4 sm:px-6 lg:px-8">

    <div class="bg-white border border-gray-400 rounded-2xl shadow-sm p-6">

      <!-- HEADER -->
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold text-gray-800">
          Manajemen Tema
        </h2>

        <Link
          href="/admin/tema/create"
          class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition"
        >
          + Tambah Tema
        </Link>
      </div>

      <!-- LIST CONTAINER -->
      <div class="border border-gray-400 rounded-xl overflow-hidden">
        <ul class="divide-y divide-gray-200">
          <li
            v-for="t in tema"
            :key="t.id_tema"
            class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition"
          >
            <span class="text-sm font-medium text-gray-700">
              {{ t.nama_tema }}
            </span>

            <div class="flex items-center gap-3">
              <Link
                :href="`/admin/tema/${t.id_tema}/edit`"
                class="px-3 py-1.5 text-sm font-semibold text-blue-600 border border-blue-300 rounded-lg hover:bg-blue-50 transition"
              >
                Edit
              </Link>

              <button
                @click="hapus(t.id_tema)"
                class="px-3 py-1.5 text-sm font-semibold text-red-600 border border-red-300 rounded-lg hover:bg-red-50 transition"
              >
                Hapus
              </button>
            </div>
          </li>

          <li
            v-if="tema.length === 0"
            class="px-6 py-12 text-center text-sm text-gray-400"
          >
            Belum ada data tema
          </li>
        </ul>
      </div>

    </div>
  </div>
</template>
