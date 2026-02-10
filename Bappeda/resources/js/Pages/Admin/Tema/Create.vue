<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

// Menggunakan useForm untuk konsistensi fitur loading & error handling
const form = useForm({
    nama_tema: ''
})

const submit = () => {
    form.post('/admin/tema')
}
</script>

<template>
    <Head title="Tambah Tema Baru" />

    <div class="max-w-3xl mx-auto">
        <Link href="/admin/tema" class="flex items-center gap-2 text-[#A2B5CB] hover:text-[#00139E] transition-colors mb-6 group">
            <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="font-bold uppercase tracking-widest text-[10px]">Kembali ke Daftar Tema</span>
        </Link>

        <div class="bg-white rounded-[2.5rem] p-12 shadow-2xl shadow-gray-100 border border-gray-400">
            <div class="mb-10">
                <h1 class="text-4xl font-black text-gray-900 tracking-tight">
                    Tambah <span class="text-[#00139E]">Tema Sektoral</span>
                </h1>
                <p class="text-gray-400 font-medium mt-2">Buat kategori klasifikasi baru untuk pengelompokan data indikator.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">
                            Nama Tema <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                v-model="form.nama_tema" 
                                type="text" 
                                placeholder="Contoh: Infrastruktur, Sosial Budaya, Ekonomi..." 
                                class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all"
                                :class="{ 'border-red-500': form.errors.nama_tema }"
                                required 
                            />
                        </div>
                        <p v-if="form.errors.nama_tema" class="text-red-500 text-xs ml-4">{{ form.errors.nama_tema }}</p>
                    </div>

                    <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-6 flex items-start gap-4">
                        <svg class="w-6 h-6 text-blue-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-[11px] text-blue-700 leading-relaxed font-medium">
                            Nama tema ini akan muncul sebagai opsi kategori utama saat pengguna menambahkan data indikator baru. Pastikan penamaan sesuai dengan klasifikasi pembangunan daerah.
                        </p>
                    </div>
                </div>

                <div class="pt-6 flex items-center justify-end gap-4 border-t border-gray-50">
                    <Link href="/admin/tema" class="px-8 py-4 text-gray-400 font-bold hover:text-gray-600 transition-colors uppercase tracking-widest text-[10px]">
                        Batal
                    </Link>
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="bg-[#00139E] text-white px-10 py-4 rounded-2xl font-bold hover:bg-[#000B58] shadow-xl shadow-blue-200 transition-all disabled:opacity-50 flex items-center gap-3 active:scale-95"
                    >
                        <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Tema Baru' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>