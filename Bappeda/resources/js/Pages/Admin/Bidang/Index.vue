<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

defineOptions({ layout: AppLayout })

const page = usePage()
const user = computed(() => page.props.auth.user)
const cantManage = computed(() => user.value?.role === 'Admin')

const props = defineProps({
    bidang: Array
})

// --- Logic Modal Delete (DI PERTAHANKAN SESUAI ASLINYA) ---
const showModal = ref(false)
const selectedId = ref(null)
const selectedTitle = ref('')

const confirmDelete = (item) => {
    selectedId.value = item.id_bidang
    selectedTitle.value = item.nama_bidang
    showModal.value = true
}

const deleteData = () => {
    if (selectedId.value) {
        router.delete(`/admin/bidang/${selectedId.value}`, {
            onSuccess: () => {
                showModal.value = false
                selectedId.value = null
            },
        })
    }
}
</script>

<template>
    <Head title="Manajemen Bidang" />

    <div class="min-h-full">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-8">
            <div>
                <h1 class="text-2xl font-black text-primary uppercase tracking-tight">
                    Manajemen <span class="text-secondary">Bidang</span>
                </h1>
                <p class="text-sm text-textsecondary font-medium mt-1">
                    Total Klasifikasi: <span class="text-primary font-black">{{ bidang.length }}</span> Bidang Kerja / Instansi
                </p>
            </div>

            <Link
                href="/admin/bidang/create"
                class="bg-primary text-white px-8 py-4 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-secondary transition-all duration-300 flex items-center gap-3 shadow-lg shadow-primary/10 active:scale-95"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Bidang
            </Link>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-400 bg-white shadow-sm">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-bgsoft text-left text-[10px] font-black text-textsecondary uppercase tracking-[0.2em] border-b border-gray-400">
                        <th class="p-8">Nama Bidang / Instansi</th>
                        <th v-if="cantManage" class="text-right p-8">Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-sm font-bold text-primary">
                    <tr v-for="b in bidang" :key="b.id_bidang" 
                        class="border-b border-gray-100 last:border-0 hover:bg-bgsoft/50 transition-all group">
                        
                        <td class="p-8">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-secondary/10 rounded-xl flex items-center justify-center text-secondary group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <span class="font-black uppercase text-xs tracking-tight text-primary leading-relaxed max-w-lg">{{ b.nama_bidang }}</span>
                            </div>
                        </td>

                        <td v-if="cantManage" class="p-8 text-right space-x-3">
                            <Link :href="`/admin/bidang/${b.id_bidang}/edit`" 
                                  class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-bgsoft text-primary hover:bg-secondary hover:text-white transition-all shadow-sm border border-gray-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </Link>
                            
                            <button @click="confirmDelete(b)" 
                                    class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-integritas/10 text-integritas hover:bg-integritas hover:text-white transition-all shadow-sm border border-integritas/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>

                    <tr v-if="bidang.length === 0">
                        <td colspan="2" class="p-24 text-center">
                             <p class="text-[10px] font-black text-textsecondary/40 uppercase tracking-[0.3em]">Belum ada data bidang yang tersedia</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Transition
            enter-active-class="duration-300 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="duration-200 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6">
                <div class="absolute inset-0 bg-primary/40 backdrop-blur-sm" @click="showModal = false"></div>
                
                <div class="relative bg-white w-full max-w-sm rounded-xl p-8 shadow-2xl border border-gray-200 text-center">
                    <div class="mx-auto w-16 h-16 bg-integritas/10 rounded-xl flex items-center justify-center mb-6 border border-integritas/20">
                        <svg class="w-8 h-8 text-integritas" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <h3 class="text-xl font-black text-primary uppercase tracking-tight mb-2">Hapus Bidang?</h3>
                    <p class="text-textsecondary text-xs font-bold leading-relaxed px-4 opacity-70 mb-8">
                        Data bidang <span class="text-primary font-black">"{{ selectedTitle }}"</span> akan dihapus permanen dari sistem BAPPEDA.
                    </p>

                    <div class="flex flex-col gap-2">
                        <button @click="deleteData"
                                class="w-full bg-integritas hover:bg-red-700 text-white font-black uppercase tracking-widest py-4 rounded-xl transition-all shadow-lg shadow-integritas/20 text-[10px]">
                            Ya, Hapus Sekarang
                        </button>
                        
                        <button @click="showModal = false"
                                class="w-full bg-white text-textsecondary font-black py-3 rounded-xl transition-colors uppercase tracking-widest text-[9px] hover:bg-bgsoft">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>