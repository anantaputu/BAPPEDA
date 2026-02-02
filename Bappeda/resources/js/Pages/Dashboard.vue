<script setup>
import { computed } from 'vue';
import { usePage, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

// Import semua varian dashboard
import AdminDashboard from '@/Pages/Admin/Dashboard.vue';
import InputerDashboard from '@/Pages/Inputer/Dashboard.vue';
import PublicDashboard from '@/Pages/Public/Dashboard.vue'; // File baru kamu

defineOptions({ layout: AppLayout });

const page = usePage();
const user = computed(() => page.props.auth.user);

const role = computed(() => {
    // Jika user null, maka dia adalah anonymous
    if (!user.value) return 'anonymous';
    
    const namaRole = user.value.role;
    if (namaRole === 'Admin Super') return 'admin';
    if (namaRole === 'Inputer') return 'inputer';
    
    return 'anonymous';
});
</script>

<template>
    <Head title="Dashboard Data Pembangunan" />

    <AdminDashboard v-if="role === 'admin'" />
    <InputerDashboard v-else-if="role === 'inputer'" />
    <PublicDashboard v-else /> </template>