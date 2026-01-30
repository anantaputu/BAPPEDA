<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <div class="min-h-screen w-full bg-white">
        <!-- Navbar -->
        <header class="w-full border-b bg-white">
            <div class="w-full px-6 lg:px-24 py-4 flex justify-between items-center">
                <h1 class="text-lg font-bold text-blue-600">
                    Sistem Penyetoran Data BAPPEDA
                </h1>
                <div class="space-x-4">
                    <Link href="/login" class="text-sm font-semibold text-gray-600 hover:text-blue-600">
                        Login
                    </Link>
                    <Link
                        href="/register"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700"
                    >
                        Daftar
                    </Link>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="w-full">
            <slot />
        </main>
    </div>
</template>
