<script setup>
import { Head, useForm } from '@inertiajs/vue3'

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

    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="w-full max-w-md bg-white p-8 rounded shadow">
            <h1 class="text-2xl font-bold text-center mb-6">
                Login Sistem BAPPEDA
            </h1>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">
                        Email
                    </label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="w-full border rounded px-3 py-2"
                        placeholder="admin@bappeda.go.id"
                    />
                    <div v-if="form.errors.email" class="text-red-600 text-sm">
                        {{ form.errors.email }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">
                        Password
                    </label>
                    <input
                        v-model="form.password"
                        type="password"
                        class="w-full border rounded px-3 py-2"
                        placeholder="••••••••"
                    />
                    <div v-if="form.errors.password" class="text-red-600 text-sm">
                        {{ form.errors.password }}
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input
                        id="remember"
                        type="checkbox"
                        v-model="form.remember"
                    />
                    <label for="remember" class="text-sm">
                        Ingat saya
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700"
                    :disabled="form.processing"
                >
                    Login
                </button>
            </form>
        </div>
    </div>
</template>
