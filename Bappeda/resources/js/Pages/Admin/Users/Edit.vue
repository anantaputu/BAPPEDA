<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'

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

// Menggunakan useForm untuk handling error dan data prefill
const form = useForm({
    nama_depan: props.user.nama_depan ?? '',
    nama_belakang: props.user.nama_belakang ?? '',
    username: props.user.username ?? '',
    email: props.user.email ?? '',
    role_id: props.user.role_id ?? '',
})

const submit = () => {
    // Menggunakan metode PUT sesuai dengan route Laravel resource/manual
    form.put(`/admin/users/${props.user.id}`)
}
</script>

<template>
    <Head title="Edit Pengguna" />

    <div class="max-w-4xl mx-auto">
        <!-- <Link href="/admin/users" class="flex items-center gap-2 text-[#A2B5CB] hover:text-[#00139E] transition-colors mb-6 group">
            <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="font-bold uppercase tracking-widest text-[10px]">Kembali ke Manajemen User</span>
        </Link> -->

        <div class="bg-white rounded-[2.5rem] p-12 shadow-2xl shadow-gray-100 border border-gray-400">
            <div class="mb-10">
                <h1 class="text-4xl font-black text-gray-900 tracking-tight">
                    Edit <span class="text-[#00139E]">Profil User</span>
                </h1>
                <p class="text-gray-400 font-medium mt-2">Perbarui informasi akun dan hak akses pengguna sistem.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Nama Depan</label>
                        <input v-model="form.nama_depan" type="text"
                            class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all"
                            :class="{ 'border-red-500': form.errors.nama_depan }" />
                        <p v-if="form.errors.nama_depan" class="text-red-500 text-xs ml-4">{{ form.errors.nama_depan }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Nama Belakang</label>
                        <input v-model="form.nama_belakang" type="text"
                            class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#A2B5CB] ml-4">Username (Identitas Tetap)</label>
                        <div class="relative">
                            <span class="absolute left-6 top-4 text-gray-400">@</span>
                            <input v-model="form.username" type="text" readonly
                                class="w-full bg-gray-100 border-gray-200 text-gray-400 rounded-2xl pl-12 pr-6 py-4 cursor-not-allowed italic" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Alamat Email</label>
                        <input v-model="form.email" type="email"
                            class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all"
                            :class="{ 'border-red-500': form.errors.email }" />
                        <p v-if="form.errors.email" class="text-red-500 text-xs ml-4">{{ form.errors.email }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Hak Akses (Role)</label>
                        <div class="relative">
                            <select v-model="form.role_id" 
                                class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all appearance-none"
                                :class="{ 'border-red-500': form.errors.role_id }">
                                <option value="">Pilih Role...</option>
                                <option v-for="r in roles" :key="r.id_role" :value="r.id_role">
                                    {{ r.nama_role }}
                                </option>
                            </select>
                            <div class="absolute right-6 top-5 pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        <p v-if="form.errors.role_id" class="text-red-500 text-xs ml-4">{{ form.errors.role_id }}</p>
                    </div>

                    <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-6 flex items-start gap-4">
                        <svg class="w-6 h-6 text-blue-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-[11px] text-blue-700 leading-relaxed font-medium">
                            Perubahan role akan langsung berdampak pada menu dan hak akses pengguna ini saat mereka masuk ke sesi berikutnya.
                        </p>
                    </div>
                </div>

                <div class="pt-6 flex items-center justify-end gap-4 border-t border-gray-50">
                    <Link href="/admin/users" class="px-8 py-4 text-gray-400 font-bold hover:text-gray-600 transition-colors">
                        Batal
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="bg-[#00139E] text-white px-10 py-4 rounded-2xl font-bold hover:bg-[#000B58] shadow-xl shadow-blue-200 transition-all disabled:opacity-50 flex items-center gap-3 active:scale-95">
                        <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'Memproses...' : 'Simpan Perubahan Profil' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>