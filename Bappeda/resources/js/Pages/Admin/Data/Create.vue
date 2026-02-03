<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { reactive } from 'vue'

defineOptions({ layout: AppLayout })

/**
 * Props dari controller
 */
const props = defineProps({
  tema: Array,
  urusan: Array,
  bidang: Array,
  frekuensi: Array,
})

/**
 * Form state
 * NOTE:
 * - satuan & sumber WAJIB (NOT NULL di DB)
 */
const form = reactive({
  nama_indikator: '',
  deskripsi: '',
  id_tema: '',
  id_urusan: '',
  id_bidang: '',
  id_frekuensi: '',
  kata_kunci: '',
  satuan: '',
  sumber: '',
})

const submit = () => {
  router.post('/admin/data', form)
}
</script>

<template>
  <Head title="Tambah Data Indikator" />

  <div class="bg-white p-8 rounded-2xl max-w-2xl">
    <h2 class="text-2xl font-bold mb-6">Tambah Data Indikator</h2>

    <form @submit.prevent="submit" class="space-y-5">

      <!-- Nama Indikator -->
      <div>
        <label class="block text-sm font-semibold mb-1">
          Nama Indikator <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.nama_indikator"
          type="text"
          class="input w-full"
          required
        />
      </div>

      <!-- Deskripsi -->
      <div>
        <label class="block text-sm font-semibold mb-1">Deskripsi</label>
        <textarea
          v-model="form.deskripsi"
          class="input w-full"
          rows="3"
        />
      </div>

      <!-- Tema -->
      <div>
        <label class="block text-sm font-semibold mb-1">
          Tema <span class="text-red-500">*</span>
        </label>
        <select v-model="form.id_tema" class="input w-full" required>
          <option value="">Pilih Tema</option>
          <option
            v-for="t in tema"
            :key="t.id_tema"
            :value="t.id_tema"
          >
            {{ t.nama_tema }}
          </option>
        </select>
      </div>

      <!-- Urusan -->
      <div>
        <label class="block text-sm font-semibold mb-1">
          Urusan <span class="text-red-500">*</span>
        </label>
        <select v-model="form.id_urusan" class="input w-full" required>
          <option value="">Pilih Urusan</option>
          <option
            v-for="u in urusan"
            :key="u.id_urusan"
            :value="u.id_urusan"
          >
            {{ u.nama_urusan }}
          </option>
        </select>
      </div>

      <!-- Bidang -->
      <div>
        <label class="block text-sm font-semibold mb-1">
          Bidang <span class="text-red-500">*</span>
        </label>
        <select v-model="form.id_bidang" class="input w-full" required>
          <option value="">Pilih Bidang</option>
          <option
            v-for="b in bidang"
            :key="b.id_bidang"
            :value="b.id_bidang"
          >
            {{ b.nama_bidang }}
          </option>
        </select>
      </div>

      <!-- Frekuensi -->
      <div>
        <label class="block text-sm font-semibold mb-1">
          Frekuensi <span class="text-red-500">*</span>
        </label>
        <select v-model="form.id_frekuensi" class="input w-full" required>
          <option value="">Pilih Frekuensi</option>
          <option
            v-for="f in frekuensi"
            :key="f.id_frekuensi"
            :value="f.id_frekuensi"
          >
            {{ f.nama_frekuensi }}
          </option>
        </select>
      </div>

      <!-- Kata Kunci -->
      <div>
        <label class="block text-sm font-semibold mb-1">Kata Kunci</label>
        <input
          v-model="form.kata_kunci"
          type="text"
          class="input w-full"
        />
      </div>

      <!-- Satuan -->
      <div>
        <label class="block text-sm font-semibold mb-1">
          Satuan <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.satuan"
          type="text"
          class="input w-full"
          required
        />
      </div>

      <!-- Sumber -->
      <div>
        <label class="block text-sm font-semibold mb-1">
          Sumber <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.sumber"
          type="text"
          class="input w-full"
          required
        />
      </div>

      <!-- Action -->
      <div class="flex justify-end gap-3 pt-6">
        <button
          type="button"
          class="px-6 py-2 rounded-lg border"
          @click="router.visit('/admin/data')"
        >
          Batal
        </button>

        <button
          type="submit"
          class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold"
        >
          Simpan
        </button>
      </div>

    </form>
  </div>
</template>
