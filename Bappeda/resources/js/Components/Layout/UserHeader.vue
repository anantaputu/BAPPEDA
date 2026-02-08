<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);
const role = computed(() => user.value?.role || 'Guest');
const currentPath = computed(() => page.url.split('/')[2] || 'Dashboard');
</script>

<template>
    <header class="flex justify-between items-center mb-10">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight capitalize">{{ currentPath }}</h1>
            <p class="text-gray-400 text-[10px] font-black mt-1 uppercase tracking-widest italic">
                Sistem Informasi Data Pembangunan
            </p>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="font-bold text-sm text-gray-900 leading-none">
                    {{ user?.nama_depan }} {{ user?.nama_belakang }}
                </p>
                <p class="text-[10px] text-[#4A6CF7] font-black uppercase tracking-widest mt-1">
                    {{ role }} Access
                </p>
            </div>
            <div class="w-12 h-12 bg-white rounded-2xl border-2 border-gray-100 shadow-sm flex items-center justify-center text-[#4A6CF7]">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        </div>
    </header>
</template>