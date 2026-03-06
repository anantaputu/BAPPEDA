<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

const form = useForm({
    nama_frekuensi: ''
})

const submit = () => {
    form.post('/admin/frekuensi')
}
</script>

<template>
    <Head title="Tambah Frekuensi Update" />

    <div class="max-w-3xl relative">
        <div class="bg-white rounded-xl p-10 md:p-12 shadow-2xl shadow-primary/5 border border-gray-400 relative z-10">
            <div class="mb-12">
                <h1 class="text-3xl font-black text-primary leading-tight tracking-tight uppercase">
                    Tambah <span class="text-secondary">Frekuensi</span>
                </h1>
                <p class="text-textsecondary font-medium mt-2 leading-relaxed">Tentukan parameter rentang waktu pembaruan data indikator sektoral BAPPEDA.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">
                            Nama Frekuensi Update <span class="text-integritas">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                v-model="form.nama_frekuensi" 
                                type="text" 
                                placeholder="Contoh: Tahunan, Semesteran, Bulanan..." 
                                class="w-full bg-white border border-gray-400 rounded-xl px-6 py-4 font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300 placeholder:text-gray-300 shadow-sm"
                                :class="{ 'border-integritas': form.errors.nama_frekuensi }"
                                required 
                            />
                        </div>
                        <p v-if="form.errors.nama_frekuensi" class="text-integritas text-[10px] font-black uppercase ml-2 tracking-tighter">{{ form.errors.nama_frekuensi }}</p>
                    </div>

                    <div class="bg-bgsoft border border-gray-200 rounded-xl p-6 flex items-start gap-4 transition-all hover:border-secondary/30">
                        <div class="p-2 bg-secondary/10 rounded-lg text-secondary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-[11px] text-textsecondary leading-relaxed font-bold uppercase tracking-tight">
                            Frekuensi ini menginformasikan seberapa sering data indikator harus diperbarui oleh OPD atau Bidang terkait dalam satu tahun anggaran.
                        </p>
                    </div>
                </div>

                <div class="pt-10 flex flex-col md:flex-row items-center justify-between gap-6 border-t border-bgsoft">
                    <p class="text-[9px] font-black text-textsecondary uppercase tracking-widest text-center md:text-left opacity-50 leading-relaxed">
                        Data frekuensi akan muncul <br class="hidden md:block"> pada dropdown input data.
                    </p>
                    <div class="flex items-center gap-6 w-full md:w-auto">
                        <Link href="/admin/frekuensi" class="text-xs font-black uppercase tracking-widest text-textsecondary hover:text-integritas transition-colors">
                            Batal
                        </Link>
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="flex-1 md:flex-none bg-primary text-white px-10 py-4 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-secondary shadow-xl shadow-primary/20 transition-all duration-300 disabled:opacity-50 flex items-center justify-center gap-3 active:scale-95"
                        >
                            <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Memproses...' : 'Simpan Frekuensi' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>