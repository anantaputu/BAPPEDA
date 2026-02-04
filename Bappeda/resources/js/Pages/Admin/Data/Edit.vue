<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

/**
 * Props dari controller
 */
const props = defineProps({
  data: Object,
  tema: Array,
  urusan: Array,
  bidang: Array,
  frekuensi: Array,
})

/**
 * Prefill form menggunakan useForm untuk konsistensi style login
 */
const form = useForm({
  nama_indikator: props.data.nama_indikator ?? '',
  deskripsi: props.data.deskripsi ?? '',
  id_tema: props.data.id_tema ?? '',
  id_urusan: props.data.id_urusan ?? '',
  id_bidang: props.data.id_bidang ?? '',
  id_frekuensi: props.data.id_frekuensi ?? '',
  kata_kunci: props.data.kata_kunci ?? '',
  satuan: props.data.satuan ?? '',
  sumber: props.data.sumber ?? '',
  status: props.data.status ?? 'valid', // Default mengikuti status validasi
})

const submit = () => {
  // Menggunakan method PUT sesuai dengan standar RESTful Laravel untuk update
  form.put(`/admin/data/${props.data.id_data}`)
}
</script>

<template>
  <Head title="Edit Data Indikator" />

  <div class="py-12 flex flex-col items-center justify-center bg-white min-h-[80vh]">
    
    <div class="w-full max-w-2xl bg-white p-10 rounded-[2.5rem] shadow-2xl shadow-gray-100 border border-gray-100">
      
      <div class="mb-10">
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">
          Edit Data <span class="text-[#4A6CF7]">Indikator</span>
        </h1>
        <p class="text-sm text-gray-400 font-medium mt-1 uppercase tracking-widest italic">
          ID Data: #{{ props.data.id_data }}
        </p>
      </div>

      <form @submit.prevent="submit" class="space-y-6">

        <div>
          <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">
            Nama Indikator <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.nama_indikator"
            type="text"
            class="w-full border-gray-200 rounded-xl px-4 py-3 focus:ring-[#4A6CF7] focus:border-[#4A6CF7] transition-all"
            required
          />
          <div v-if="form.errors.nama_indikator" class="text-red-600 text-xs mt-1 font-bold">{{ form.errors.nama_indikator }}</div>
        </div>

        <div>
          <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Deskripsi</label>
          <textarea
            v-model="form.deskripsi"
            rows="3"
            class="w-full border-gray-200 rounded-xl px-4 py-3 focus:ring-[#4A6CF7] focus:border-[#4A6CF7] transition-all"
          />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Tema <span class="text-red-500">*</span></label>
            <select v-model="form.id_tema" class="w-full border-gray-200 rounded-xl px-4 py-3 focus:ring-[#4A6CF7] focus:border-[#4A6CF7] transition-all" required>
              <option value="">Pilih Tema</option>
              <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Urusan <span class="text-red-500">*</span></label>
            <select v-model="form.id_urusan" class="w-full border-gray-200 rounded-xl px-4 py-3 focus:ring-[#4A6CF7] focus:border-[#4A6CF7] transition-all" required>
              <option value="">Pilih Urusan</option>
              <option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Bidang <span class="text-red-500">*</span></label>
            <select v-model="form.id_bidang" class="w-full border-gray-200 rounded-xl px-4 py-3 focus:ring-[#4A6CF7] focus:border-[#4A6CF7] transition-all" required>
              <option value="">Pilih Bidang</option>
              <option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Frekuensi <span class="text-red-500">*</span></label>
            <select v-model="form.id_frekuensi" class="w-full border-gray-200 rounded-xl px-4 py-3 focus:ring-[#4A6CF7] focus:border-[#4A6CF7] transition-all" required>
              <option value="">Pilih Frekuensi</option>
              <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Kata Kunci</label>
            <input v-model="form.kata_kunci" type="text" class="w-full border-gray-200 rounded-xl px-4 py-3 focus:ring-[#4A6CF7] focus:border-[#4A6CF7]" />
          </div>
          <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Status Validasi</label>
            <select v-model="form.status" class="w-full border-gray-200 rounded-xl px-4 py-3 focus:ring-[#4A6CF7] focus:border-[#4A6CF7] transition-all">
              <option value="valid">Valid (Publik)</option>
              <option value="pending">Pending (Review)</option>
              <option value="invalid">Invalid (Tolak)</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Satuan <span class="text-red-500">*</span></label>
            <input v-model="form.satuan" type="text" class="w-full border-gray-200 rounded-xl px-4 py-3 focus:ring-[#4A6CF7] focus:border-[#4A6CF7]" required />
          </div>
          <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Sumber <span class="text-red-500">*</span></label>
            <input v-model="form.sumber" type="text" class="w-full border-gray-200 rounded-xl px-4 py-3 focus:ring-[#4A6CF7] focus:border-[#4A6CF7]" required />
          </div>
        </div>

        <div class="flex justify-end gap-4 pt-8">
          <button
            type="button"
            class="px-8 py-4 rounded-xl text-sm font-bold text-gray-400 hover:text-gray-600 transition-all uppercase tracking-widest"
            @click="router.visit('/admin/data')"
          >
            Batal
          </button>

          <button
            type="submit"
            class="bg-[#4A6CF7] text-white px-10 py-4 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 disabled:opacity-50 uppercase tracking-widest text-xs"
            :disabled="form.processing"
          >
            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </button>
        </div>

      </form>
    </div>
  </div>
</template>