<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Dashboard Control Panel
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">1. Master Data</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">
                            Buat format data baru (Indikator) sebelum melakukan pengisian.
                        </p>
                        <Link 
                            href="/data/create" 
                            class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-blue-700"
                        >
                            + Tambah Master Data
                        </Link>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">2. Setor Data</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">
                            Upload file Excel ke dalam sistem berdasarkan master data.
                        </p>
                        <Link 
                            href="/input-data" 
                            class="inline-block bg-green-600 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-green-700"
                        >
                            Mulai Upload
                        </Link>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>