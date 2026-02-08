<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({ show: Boolean });
defineEmits(['close']);
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-6">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-md" @click="$emit('close')"></div>
        <div class="relative bg-white w-full max-w-sm rounded-[3rem] p-12 shadow-2xl text-center">
            <Link href="/logout" method="post" as="button" class="bg-red-600 ...">Ya, Keluar</Link>
            <button @click="$emit('close')">Batal</button>
        </div>
    </div>
</template>