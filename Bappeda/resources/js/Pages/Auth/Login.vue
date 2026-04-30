<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout });

const form = useForm({
    username: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post('/login')
}
</script>

<template>
    <Head title="Login" />

    <div class="relative overflow-hidden w-full min-h-screen flex items-center justify-center bg-white">

        <div class="w-full max-w-xl px-6 relative z-10">
            <div class="bg-white border border-gray-400 p-10 rounded-xl shadow-2xl shadow-primary/5">
                
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-black text-primary leading-tight tracking-tight uppercase">
                        Login <span class="text-secondary">BAPPEDA</span>
                    </h1>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">
                            Username
                        </label>
                        <input
                            v-model="form.username"
                            type="text"
                            class="w-full bg-white border border-gray-400 rounded-xl px-6 py-3.5 font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300 placeholder:text-gray-300"
                            placeholder="Masukkan username"
                        />
                        <div v-if="form.errors.username" class="text-integritas text-[10px] mt-1 font-black uppercase tracking-tighter ml-2">
                            {{ form.errors.username }}
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
                    <button
                        type="submit"
                        class="bg-primary text-white py-4 px-4 rounded-xl text-base font-black hover:bg-secondary transition-all duration-300 shadow-primary/20 disabled:opacity-50 disabled:translate-y-0 uppercase tracking-widest"
                        :disabled="form.processing"
                    >
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
