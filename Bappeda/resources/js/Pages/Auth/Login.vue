<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout });

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post('/login')
}
</script>

<template>
    <Head title="Login" />

    <div class="relative overflow-hidden w-full flex items-center justify-center bg-white pt-20 mt-10">

        <div class="w-full max-w-xl px-6 relative z-10">
            <div class="bg-white border border-gray-400 p-10 rounded-xl shadow-2xl shadow-primary/5">
                
                <div class="text-center mb-10">
                    <span class="inline-block px-4 py-1.5 mb-6 text-sm font-bold text-integritas bg-integritas/10 rounded-full border border-integritas/40 tracking-wide uppercase">
                        Akses Sistem
                    </span>
                    <h1 class="text-3xl font-black text-primary leading-tight tracking-tight uppercase">
                        Login <span class="text-secondary">BAPPEDA</span>
                    </h1>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">
                            Alamat Email
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            class="w-full bg-white border border-gray-400 rounded-xl px-6 py-3.5 font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300 placeholder:text-gray-300"
                            placeholder="admin@bappeda.go.id"
                        />
                        <div v-if="form.errors.email" class="text-integritas text-[10px] mt-1 font-black uppercase tracking-tighter ml-2">
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">
                            Kata Sandi
                        </label>
                        <input
                            v-model="form.password"
                            type="password"
                            class="w-full bg-white border border-gray-400 rounded-xl px-6 py-3.5 font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300 placeholder:text-gray-300"
                            placeholder="••••••••"
                        />
                        <div v-if="form.errors.password" class="text-integritas text-[10px] mt-1 font-black uppercase tracking-tighter ml-2">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <div class="flex items-center justify-between px-2">
                        <div class="flex items-center gap-3 group cursor-pointer">
                            <input
                                id="remember"
                                type="checkbox"
                                class="w-4 h-4 rounded border-gray-400 text-secondary focus:ring-secondary/20 transition-all cursor-pointer"
                                v-model="form.remember"
                            />
                            <label for="remember" class="text-xs font-black text-primary cursor-pointer group-hover:text-secondary transition-colors uppercase tracking-wider">
                                Ingat saya
                            </label>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-primary text-white py-4 rounded-xl text-base font-black hover:bg-secondary transition-all duration-300 shadow-primary/20 disabled:opacity-50 disabled:translate-y-0 uppercase tracking-widest"
                        :disabled="form.processing"
                    >
                        Masuk Ke Dashboard
                    </button>
                </form>

                <div class="mt-10 pt-6 border-t border-bgsoft text-center">
                    <p class="text-[9px] font-black text-textsecondary uppercase tracking-widest leading-loose">
                        BAPPEDA <span class="text-primary">Provinsi Nusa Tenggara Barat</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>