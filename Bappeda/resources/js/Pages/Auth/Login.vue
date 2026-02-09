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

    <div class="relative overflow-hidden w-full min-h-screen flex items-center justify-center bg-white py-12">
        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[600px] h-[600px] bg-[#A2B5CB]/10 rounded-full blur-3xl -z-10"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-[500px] h-[500px] bg-[#00139E]/5 rounded-full blur-3xl -z-10"></div>

        <div class="w-full max-w-xl px-6 relative z-10">
            <div class="bg-white border border-gray-400 p-10 rounded-[3rem] shadow-2xl shadow-[#000B58]/5">
                
                <div class="text-center mb-8">
                    <span class="inline-block px-4 py-1.5 mb-4 text-[10px] font-black text-[#00139E] bg-[#A2B5CB]/20 rounded-full border border-[#A2B5CB]/30 tracking-widest uppercase">
                        Akses Sistem
                    </span>
                    <h1 class="text-3xl font-black text-[#000B58] leading-tight tracking-tight">
                        Login <span class="text-[#00139E]">BAPPEDA</span>
                    </h1>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-[#A2B5CB] ml-2">
                            Alamat Email
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            class="w-full bg-gray-50 border border-gray-400 rounded-2xl px-6 py-3.5 font-bold text-[#000B58] focus:outline-none focus:border-[#00139E] focus:ring-4 focus:ring-[#00139E]/5 transition-all duration-300 placeholder:text-gray-300"
                            placeholder="admin@bappeda.go.id"
                        />
                        <div v-if="form.errors.email" class="text-[#FF1414] text-[10px] mt-1 font-black uppercase tracking-tighter ml-2">
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-[#A2B5CB] ml-2">
                            Kata Sandi
                        </label>
                        <input
                            v-model="form.password"
                            type="password"
                            class="w-full bg-gray-50 border border-gray-400 rounded-2xl px-6 py-3.5 font-bold text-[#000B58] focus:outline-none focus:border-[#00139E] focus:ring-4 focus:ring-[#00139E]/5 transition-all duration-300 placeholder:text-gray-300"
                            placeholder="••••••••"
                        />
                        <div v-if="form.errors.password" class="text-[#FF1414] text-[10px] mt-1 font-black uppercase tracking-tighter ml-2">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <div class="flex items-center justify-between px-2">
                        <div class="flex items-center gap-3 group cursor-pointer">
                            <input
                                id="remember"
                                type="checkbox"
                                class="w-4 h-4 rounded-lg border-gray-400 text-[#00139E] focus:ring-[#00139E]/20 transition-all cursor-pointer"
                                v-model="form.remember"
                            />
                            <label for="remember" class="text-xs font-black text-[#000B58] cursor-pointer group-hover:text-[#00139E] transition-colors">
                                Ingat saya
                            </label>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-[#00139E] text-white py-4 rounded-2xl text-base font-black hover:bg-[#000B58] hover:-translate-y-1 transition-all duration-300 shadow-xl shadow-[#00139E]/20 disabled:opacity-50 disabled:translate-y-0"
                        :disabled="form.processing"
                    >
                        Masuk Ke Dashboard
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <p class="text-[9px] font-bold text-[#A2B5CB] uppercase tracking-widest leading-loose">
                        BAPPEDA <span class="text-[#000B58]">Provinsi Nusa Tenggara Barat</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>