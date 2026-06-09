<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

const props = defineProps({
    roles: Array
})

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

    <div class="max-w-4xl relative">
        
        <div class="bg-white rounded-xl p-10 md:p-12 shadow-2xl shadow-primary/5 border border-gray-400 relative z-10">
            <div class="mb-12 text-center md:text-left">
                <h1 class="text-3xl font-black text-primary leading-tight tracking-tight uppercase">
                    Registrasi <span class="text-secondary">User</span>
                </h1>
                <p class="text-textsecondary font-medium mt-2">Daftarkan personel baru untuk akses panel data BAPPEDA.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">Nama Depan</label>
                        <input v-model="form.nama_depan" type="text" placeholder="Contoh: Ahmad" 
                            class="w-full bg-white border border-gray-400 rounded-xl px-6 py-4 font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300 placeholder:text-gray-300 shadow-sm"
                            :class="{ 'border-integritas': form.errors.nama_depan }" />
                        <p v-if="form.errors.nama_depan" class="text-integritas text-[10px] font-black uppercase ml-2 tracking-tighter">{{ form.errors.nama_depan }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">Nama Belakang</label>
                        <input v-model="form.nama_belakang" type="text" placeholder="Contoh: Fauzi" 
                            class="w-full bg-white border border-gray-400 rounded-xl px-6 py-4 font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300 placeholder:text-gray-300 shadow-sm" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">Username</label>
                        <div class="relative">
                            <span class="absolute left-6 top-1/2 -translate-y-1/2 text-textsecondary font-black opacity-40">@</span>
                            <input v-model="form.username" type="text" placeholder="ahmadf_ntb" 
                                class="w-full bg-white border border-gray-400 rounded-xl pl-12 pr-6 py-4 font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300 placeholder:text-gray-300 shadow-sm"
                                :class="{ 'border-integritas': form.errors.username }" />
                        </div>
                        <p v-if="form.errors.username" class="text-integritas text-[10px] font-black uppercase ml-2 tracking-tighter">{{ form.errors.username }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">Alamat Email</label>
                        <input v-model="form.email" type="email" placeholder="email@ntbprov.go.id" 
                            class="w-full bg-white border border-gray-400 rounded-xl px-6 py-4 font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300 placeholder:text-gray-300 shadow-sm"
                            :class="{ 'border-integritas': form.errors.email }" />
                        <p v-if="form.errors.email" class="text-integritas text-[10px] font-black uppercase ml-2 tracking-tighter">{{ form.errors.email }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">Hak Akses (Role)</label>
                        <div class="relative group">
                            <select v-model="form.role_id" 
                                class="w-full bg-white border border-gray-400 rounded-xl px-6 py-4 font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300 appearance-none cursor-pointer shadow-sm"
                                :class="{ 'border-integritas': form.errors.role_id }">
                                <option value="">Pilih Role...</option>
                                <option v-for="r in roles" :key="r.id_role" :value="r.id_role">
                                    {{ r.nama_role }}
                                </option>
                            </select>
                            <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-textsecondary group-hover:text-secondary transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>
                        <p v-if="form.errors.role_id" class="text-integritas text-[10px] font-black uppercase ml-2 tracking-tighter">{{ form.errors.role_id }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">Kata Sandi Sementara</label>
                        <input v-model="form.password" type="password" placeholder="••••••••" 
                            class="w-full bg-white border border-gray-400 rounded-xl px-6 py-4 font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300 placeholder:text-gray-300 shadow-sm"
                            :class="{ 'border-integritas': form.errors.password }" />
                        <p v-if="form.errors.password" class="text-integritas text-[10px] font-black uppercase ml-2 tracking-tighter">{{ form.errors.password }}</p>
                    </div>
                </div>

                <div class="pt-10 flex flex-col md:flex-row items-center justify-between gap-6 border-t border-bgsoft">
                    <p class="text-[9px] font-black text-textsecondary uppercase tracking-widest text-center md:text-left">
                        Pastikan data yang diinput <br class="hidden md:block"> sudah sesuai standar operasional.
                    </p>
                    <div class="flex items-center gap-6 w-full md:w-auto">
                        <Link href="/admin/users" class="text-xs font-black uppercase tracking-widest text-textsecondary hover:text-integritas transition-colors">
                            Batal
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="flex-1 md:flex-none bg-primary text-white px-10 py-4 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-secondary shadow-xl shadow-primary/20 transition-all duration-300 disabled:opacity-50 flex items-center justify-center gap-3 active:scale-95">
                            <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Memproses...' : 'Simpan User Baru' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
