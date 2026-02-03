<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { reactive } from 'vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
  roles: Array
})

const form = reactive({
  nama_depan: '',
  nama_belakang: '',
  username: '',
  email: '',
  role_id: '',
  password: ''
})

const submit = () => {
  router.post('/admin/users', form)
}
</script>

<template>
  <Head title="Tambah User" />

  <div class="bg-white p-8 rounded-2xl max-w-xl">
    <h2 class="text-2xl font-bold mb-6">Tambah User</h2>

    <form @submit.prevent="submit" class="space-y-4">
      <input v-model="form.nama_depan" placeholder="Nama Depan" class="input" />
      <input v-model="form.nama_belakang" placeholder="Nama Belakang" class="input" />
      <input v-model="form.username" placeholder="Username" class="input" />
      <input v-model="form.email" type="email" placeholder="Email" class="input" />
      <input v-model="form.password" type="password" placeholder="Password" class="input" />

      <select v-model="form.role_id" class="input">
        <option value="">Pilih Role</option>
        <option v-for="r in roles" :key="r.id_role" :value="r.id_role">
          {{ r.nama_role }}
        </option>
      </select>

      <button class="bg-blue-600 text-white px-6 py-2 rounded-lg">
        Simpan
      </button>
    </form>
  </div>
</template>
