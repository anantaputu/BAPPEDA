<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    frekuensi: Array
})

// --- Logic Modal Delete (Standardized) ---
const showModal = ref(false)
const selectedId = ref(null)
const selectedTitle = ref('')

const confirmDelete = (item) => {
    selectedId.value = item.id_frekuensi
    selectedTitle.value = item.nama_frekuensi
    showModal.value = true
}

const deleteData = () => {
    if (selectedId.value) {
        router.delete(`/admin/frekuensi/${selectedId.value}`, {
            onSuccess: () => {
                showModal.value = false
                selectedId.value = null
            },
        })
    }
}
</script>

<template>
    <Head title="Manajemen Frekuensi" />

    <div class="bg-white rounded-[2.5rem] p-10 shadow-2xl shadow-gray-100 border border-gray-400 min-h-[70vh]">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12">
            <div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight">
                    Manajemen <span class="text-[#00139E]">Frekuensi</span>
                </h1>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-[0.2em] mt-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-[#00139E] rounded-full animate-pulse"></span>
                    Total Parameter: {{ frekuensi.length }} Periode Update
                </p>
            </div>

            <Link
                href="/admin/frekuensi/create"
                class="bg-[#00139E] text-white px-8 py-4 rounded-2xl text-lg font-bold hover:bg-[#000B58] hover:-translate-y-1 transition-all duration-300 flex items-center gap-2 shadow-xl shadow-blue-500/20"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Frekuensi
            </Link>
        </div>

        <div class="overflow-hidden rounded-[2rem] border border-gray-400 bg-white">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-400">
                        <th class="p-8">Rentang Waktu / Frekuensi Update</th>
                        <th class="text-right p-8">Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-sm font-bold text-gray-800">
                    <tr v-for="f in frekuensi" :key="f.id_frekuensi" 
                        class="border-b border-gray-400 last:border-0 hover:bg-blue-50/20 transition-all group">
                        
                        <td class="p-8">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-[#00139E]/5 rounded-xl flex items-center justify-center text-[#00139E]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="font-black uppercase text-xs tracking-tight text-gray-900">{{ f.nama_frekuensi }}</span>
                            </div>
                        </td>

                        <td class="p-8 text-right space-x-3">
                            <Link :href="`/admin/frekuensi/${f.id_frekuensi}/edit`" 
                                  class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-blue-50 text-[#00139E] hover:bg-[#00139E] hover:text-white transition-all shadow-sm group-hover:scale-110">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </Link>
                            
                            <button @click="confirmDelete(f)" 
                                    class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all shadow-sm group-hover:scale-110">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>

                    <tr v-if="frekuensi.length === 0">
                        <td colspan="2" class="p-20 text-center text-gray-300 uppercase italic tracking-widest text-xs">
                            Belum ada data frekuensi yang tersedia
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Transition
            enter-active-class="duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6">
                <div class="absolute inset-0 bg-[#000B58]/40 backdrop-blur-sm" @click="showModal = false"></div>
                
                <div class="relative bg-white w-full max-w-md rounded-[2.5rem] p-10 shadow-2xl text-center transform transition-all border border-gray-100">
                    
                    <div class="mx-auto w-20 h-20 bg-rose-50 rounded-3xl flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <h3 class="text-2xl font-black text-[#000B58] mb-2">Hapus Frekuensi?</h3>
                    <p class="text-[#A2B5CB] text-sm leading-relaxed mb-10 px-4">
                        Data frekuensi <span class="text-gray-900 font-bold">"{{ selectedTitle }}"</span> akan dihapus. Hal ini dapat menyebabkan data indikator yang menggunakan frekuensi ini kehilangan metadata waktu.
                    </p>

                    <div class="flex flex-col gap-3">
                        <button 
                            @click="deleteData"
                            class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-red-500/20 active:scale-[0.98]"
                        >
                            Ya, Hapus Sekarang
                        </button>
                        
                        <button 
                            @click="showModal = false"
                            class="w-full bg-transparent hover:bg-gray-50 text-[#A2B5CB] font-bold py-4 rounded-2xl transition-colors"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
        
        <div class="h-12"></div>
    </div>
</template>