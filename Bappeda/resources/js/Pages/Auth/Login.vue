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

    <div class="py-32 flex flex-col items-center justify-center bg-white min-h-[60vh]">
        
        <div class="w-full max-w-md bg-white p-10 rounded-3xl shadow-2xl shadow-gray-100 border border-gray-100">
            <h1 class="text-2xl font-black text-center mb-8 text-gray-900 tracking-tight">
                Login Sistem BAPPEDA
            </h1>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">
                        Email
                    </label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="w-full border-gray-200 rounded-xl px-4 py-3 focus:ring-[#4A6CF7] focus:border-[#4A6CF7]"
                        placeholder="admin@bappeda.go.id"
                    />
                    <div v-if="form.errors.email" class="text-red-600 text-xs mt-1 font-bold">
                        {{ form.errors.email }}
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">
                        Password
                    </label>
                    <input
                        v-model="form.password"
                        type="password"
                        class="w-full border-gray-200 rounded-xl px-4 py-3 focus:ring-[#4A6CF7] focus:border-[#4A6CF7]"
                        placeholder="••••••••"
                    />
                    <div v-if="form.errors.password" class="text-red-600 text-xs mt-1 font-bold">
                        {{ form.errors.password }}
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input
                        id="remember"
                        type="checkbox"
                        class="rounded border-gray-300 text-[#4A6CF7] focus:ring-[#4A6CF7]"
                        v-model="form.remember"
                    />
                    <label for="remember" class="text-sm font-semibold text-gray-600">
                        Ingat saya
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full bg-[#4A6CF7] text-white py-4 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 disabled:opacity-50"
                    :disabled="form.processing"
                >
                    Login
                </button>
            </form>
        </div>
    </div>
</template>