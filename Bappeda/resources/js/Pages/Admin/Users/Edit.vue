<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { reactive } from 'vue'

defineOptions({ layout: AppLayout })

/**
 * Props dari controller:
 * - user  : data user yang diedit
 * - roles : daftar role
 */
const props = defineProps({
  user: Object,
  roles: Array,
})

/**
 * Form diisi dari data user (prefill)
 * Catatan:
 * - username dibuat readonly (identifier)
 * - password TIDAK diubah di sini
 */
const form = reactive({
  nama_depan: props.user.nama_depan ?? '',
  nama_belakang: props.user.nama_belakang ?? '',
  username: props.user.username ?? '',
  email: props.user.email ?? '',
  role_id: props.user.role_id ?? '',
})

const submit = () => {
  router.put(`/admin/users/${props.user.id}`, form)
}
</script>

<template>
  <Head title="Edit User" />

  <div class="bg-white p-8 rounded-2xl max-w-xl">
    <h2 class="text-2xl font-bold mb-6">Edit User</h2>

    <form @submit.prevent="submit" class="space-y-4">
      <!-- Nama Depan -->
      <div>
        <label class="block text-sm font-semibold mb-1">Nama Depan</label>
        <input
          v-model="form.nama_depan"
          type="text"
          class="input w-full"
          required
        />
      </div>

      <!-- Nama Belakang -->
      <div>
        <label class="block text-sm font-semibold mb-1">Nama Belakang</label>
        <input
          v-model="form.nama_belakang"
          type="text"
          class="input w-full"
        />
      </div>

      <!-- Username (readonly) -->
      <div>
        <label class="block text-sm font-semibold mb-1">Username</label>
        <input
          v-model="form.username"
          type="text"
          class="input w-full bg-gray-100 cursor-not-allowed"
          readonly
        />
      </div>

      <!-- Email -->
      <div>
        <label class="block text-sm font-semibold mb-1">Email</label>
        <input
          v-model="form.email"
          type="email"
          class="input w-full"
          required
        />
      </div>

      <!-- Role -->
      <div>
        <label class="block text-sm font-semibold mb-1">Role</label>
        <select
          v-model="form.role_id"
          class="input w-full"
          required
        >
          <option value="">Pilih Role</option>
          <option
            v-for="r in roles"
            :key="r.id_role"
            :value="r.id_role"
          >
            {{ r.nama_role }}
          </option>
        </select>
      </div>

      <!-- Action -->
      <div class="flex justify-end gap-3 pt-6">
        <button
          type="button"
          class="px-6 py-2 rounded-lg border"
          @click="router.visit('/admin/users')"
        >
          Batal
        </button>

        <button
          type="submit"
          class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold"
        >
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</template>
