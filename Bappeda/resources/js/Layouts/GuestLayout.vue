<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <div class="mx-auto flex min-h-screen max-w-7xl items-center justify-center px-4 py-10">
            <div class="w-full max-w-md">
                <div class="mb-8 text-center">
                    <Link href="/" class="inline-flex items-center gap-3 text-primary">
                        <img src="/images/logo.png" alt="Bappeda" class="h-14 w-14 rounded-xl object-contain" />
                        <div class="text-left">
                            <p class="text-xs font-black uppercase tracking-[0.3em] text-textsecondary">Portal Data</p>
                            <h1 class="text-2xl font-black uppercase tracking-tight">Bappeda</h1>
                        </div>
                    </Link>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-xl shadow-slate-200/40">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
