<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
  // Data sekarang berupa objek pagination dari Laravel
  data: Object,
})

// --- Logic Modal Delete ---
const showModal = ref(false)
const selectedId = ref(null)

const confirmDelete = (id) => {
  selectedId.value = id // Menggunakan .value sesuai standar ref Vue
  showModal.value = true
}

const deleteData = () => {
  if (selectedId.value) {
    router.delete(`/admin/data/${selectedId.value}`, {
      onSuccess: () => {
        showModal.value = false
        selectedId.value = null
      },
    })
  }
}
</script>

<template>
  <Head title="Data Indikator" />

  <div class="bg-white rounded-[2.5rem] p-10 shadow-2xl shadow-gray-100 border border-gray-100 min-h-[70vh]">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12">
      <div>
        <h1 class="text-4xl font-black text-gray-900 tracking-tight">
          Data <span class="text-[#4A6CF7]">Indikator</span>
        </h1>
        <p class="text-xs text-gray-400 font-bold uppercase tracking-[0.2em] mt-2 flex items-center gap-2">
            <span class="w-2 h-2 bg-[#4A6CF7] rounded-full animate-pulse"></span>
            Total Terdata: {{ data.total }} Indikator
        </p>
      </div>

      <Link
        href="/admin/data/create"
        class="bg-[#4A6CF7] text-white px-10 py-5 rounded-[1.5rem] font-black text-xs uppercase tracking-widest shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all hover:-translate-y-1 active:scale-95 flex items-center gap-2"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Indikator
      </Link>
    </div>

    <div class="overflow-hidden rounded-[2rem] border border-gray-100 bg-white">
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-50/50 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">
            <th class="p-8">Nama Indikator</th>
            <th>Sektoral / Tema</th>
            <th>Urusan</th>
            <th>Status</th>
            <th class="text-right p-8">Aksi</th>
          </tr>
        </thead>

        <tbody class="text-sm font-bold text-gray-600">
          <tr
            v-for="item in data.data"
            :key="item.id_data"
            class="border-b border-gray-50 last:border-0 hover:bg-blue-50/20 transition-all group"
          >
            <td class="p-8">
                <div class="text-gray-900 font-extrabold uppercase italic text-xs tracking-tight leading-relaxed max-w-xs">
                    {{ item.nama_indikator }}
                </div>
                <div class="text-[9px] text-gray-300 font-bold mt-1 uppercase">ID: #{{ item.id_data }}</div>
            </td>
            
            <td class="text-xs">
                <span class="bg-gray-100 px-3 py-1 rounded-full text-gray-500 text-[10px] font-black uppercase">
                    {{ item.tema?.nama_tema || '-' }}
                </span>
            </td>

            <td class="text-[10px] text-gray-400 uppercase leading-snug max-w-[150px]">
                {{ item.urusan?.nama_urusan || '-' }}
            </td>

            <td>
              <span
                :class="item.status === 'valid' || item.status === 'aktif'
                  ? 'bg-emerald-50 text-emerald-600 border-emerald-100'
                  : 'bg-amber-50 text-amber-600 border-amber-100'"
                class="text-[9px] px-4 py-1.5 rounded-xl font-black uppercase tracking-widest border inline-block"
              >
                {{ item.status }}
              </span>
            </td>

            <td class="text-right p-8 space-x-3">
              <Link
                :href="`/admin/data/${item.id_data}/edit`"
                class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-blue-50 text-[#4A6CF7] hover:bg-[#4A6CF7] hover:text-white transition-all shadow-sm group-hover:scale-110"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
              </Link>

              <button
                class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all shadow-sm group-hover:scale-110"
                @click="confirmDelete(item.id_data)"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </td>
          </tr>

          <tr v-if="data.data.length === 0">
            <td colspan="5" class="p-20 text-center">
                <div class="flex flex-col items-center opacity-30">
                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                    <p class="font-black uppercase tracking-widest text-xs">Belum ada data indikator yang tersedia</p>
                </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-12 flex flex-wrap justify-center items-center gap-3">
        <template v-for="(link, index) in data.links" :key="index">
            <div 
                v-if="link.url === null" 
                v-html="link.label" 
                class="px-4 py-2 text-gray-300 text-[10px] font-black uppercase tracking-widest"
            ></div>
            <Link 
                v-else 
                :href="link.url" 
                v-html="link.label" 
                class="px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-[0.15em] transition-all active:scale-90"
                :class="link.active 
                    ? 'bg-[#4A6CF7] text-white shadow-xl shadow-blue-100' 
                    : 'bg-white text-gray-400 border border-gray-100 hover:bg-gray-50 hover:text-gray-900'"
            />
        </template>
    </div>

    <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-md transition-opacity" @click="showModal = false"></div>
        
        <div class="relative bg-white w-full max-w-sm rounded-[3rem] p-12 shadow-2xl text-center transform transition-all animate-in fade-in zoom-in duration-300">
            <div class="w-24 h-24 bg-rose-50 text-rose-500 rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-inner">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            
            <h3 class="text-2xl font-black text-gray-900 mb-3">Hapus Data?</h3>
            <p class="text-sm text-gray-400 font-medium mb-10 leading-relaxed px-2">
                Konfirmasi penghapusan data. Indikator yang dihapus akan hilang dari sistem secara permanen.
            </p>
            
            <div class="flex flex-col gap-4">
                <button 
                    @click="deleteData"
                    class="w-full bg-rose-600 text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-rose-100 hover:bg-rose-700 transition-all active:scale-95"
                >
                    Ya, Hapus Sekarang
                </button>
                <button 
                    @click="showModal = false"
                    class="w-full bg-gray-50 text-gray-400 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-gray-100 transition-all active:scale-95"
                >
                    Kembali
                </button>
            </div>
        </div>
    </div>
  </div>
</template>