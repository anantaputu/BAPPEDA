<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

const props = defineProps({
    roles: Array
})

// Menggunakan useForm agar handling error validation lebih mudah
const form = useForm({
    nama_depan: '',
    nama_belakang: '',
    username: '',
    email: '',
    role_id: '',
    password: ''
})

const submit = () => {
    form.post('/admin/users', {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Tambah User Baru" />

    <div class="max-w-4xl mx-auto">
        <Link href="/admin/users" class="flex items-center gap-2 text-[#A2B5CB] hover:text-[#00139E] transition-colors mb-6 group">
            <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="font-bold uppercase tracking-widest text-[10px]">Kembali ke Manajemen User</span>
        </Link>

        <div class="bg-white rounded-[2.5rem] p-12 shadow-2xl shadow-gray-100 border border-gray-400">
            <div class="mb-10">
                <h1 class="text-4xl font-black text-gray-900 tracking-tight">
                    Registrasi <span class="text-[#00139E]">User</span>
                </h1>
                <p class="text-gray-400 font-medium mt-2">Daftarkan personel baru untuk akses panel data BAPPEDA.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Nama Depan</label>
                        <input v-model="form.nama_depan" type="text" placeholder="Contoh: Ahmad" 
                            class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all"
                            :class="{ 'border-red-500': form.errors.nama_depan }" />
                        <p v-if="form.errors.nama_depan" class="text-red-500 text-xs ml-4">{{ form.errors.nama_depan }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Nama Belakang</label>
                        <input v-model="form.nama_belakang" type="text" placeholder="Contoh: Fauzi" 
                            class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Username</label>
                        <div class="relative">
                            <span class="absolute left-6 top-4 text-gray-400">@</span>
                            <input v-model="form.username" type="text" placeholder="ahmadf_ntb" 
                                class="w-full bg-gray-50 border-gray-200 rounded-2xl pl-12 pr-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all"
                                :class="{ 'border-red-500': form.errors.username }" />
                        </div>
                        <p v-if="form.errors.username" class="text-red-500 text-xs ml-4">{{ form.errors.username }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Alamat Email</label>
                        <input v-model="form.email" type="email" placeholder="email@ntbprov.go.id" 
                            class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all"
                            :class="{ 'border-red-500': form.errors.email }" />
                        <p v-if="form.errors.email" class="text-red-500 text-xs ml-4">{{ form.errors.email }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Hak Akses (Role)</label>
                        <select v-model="form.role_id" 
                            class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all appearance-none"
                            :class="{ 'border-red-500': form.errors.role_id }">
                            <option value="">Pilih Role...</option>
                            <option v-for="r in roles" :key="r.id_role" :value="r.id_role">
                                {{ r.nama_role }}
                            </option>
                        </select>
                        <p v-if="form.errors.role_id" class="text-red-500 text-xs ml-4">{{ form.errors.role_id }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#00139E] ml-4">Kata Sandi</label>
                        <input v-model="form.password" type="password" placeholder="••••••••" 
                            class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all"
                            :class="{ 'border-red-500': form.errors.password }" />
                        <p v-if="form.errors.password" class="text-red-500 text-xs ml-4">{{ form.errors.password }}</p>
                    </div>
                </div>

                <div class="pt-6 flex items-center justify-end gap-4">
                    <Link href="/admin/users" class="px-8 py-4 text-gray-400 font-bold hover:text-gray-600 transition-colors">
                        Batal
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="bg-[#00139E] text-white px-10 py-4 rounded-2xl font-bold hover:bg-[#000B58] shadow-xl shadow-blue-200 transition-all disabled:opacity-50 flex items-center gap-3">
                        <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'Menyimpan...' : 'Simpan User Baru' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>