<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { reactive } from 'vue'

defineOptions({ layout: AppLayout })

const form = reactive({
  nama_frekuensi: ''
})

const submit = () => {
  router.post('/admin/frekuensi', form)
}
</script>

<template>
  <Head title="Tambah Frekuensi" />

  <div class="bg-white p-8 rounded-2xl max-w-md">
    <h2 class="text-xl font-bold mb-6">Tambah Frekuensi</h2>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block text-sm font-semibold mb-1">
          Nama Frekuensi <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.nama_frekuensi"
          type="text"
          class="input w-full"
          required
        />
      </div>

      <div class="flex justify-end gap-3 pt-4">
        <button
          type="button"
          class="px-4 py-2 border rounded-lg"
          @click="router.visit('/admin/frekuensi')"
        >
          Batal
        </button>
        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold"
        >
          Simpan
        </button>
      </div>
    </form>
  </div>
</template>
