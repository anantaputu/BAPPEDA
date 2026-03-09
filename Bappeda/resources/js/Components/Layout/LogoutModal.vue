<script setup>
import { Link } from '@inertiajs/vue3';
import IconifyIcon from '@/Components/Base/IconifyIcon.vue';
defineProps({ show: Boolean });
defineEmits(['close']);
</script>

<template>
    <Transition
        enter-active-class="duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-6">
            <div class="absolute inset-0 bg-[#000B58]/40 backdrop-blur-sm" @click="$emit('close')"></div>
            
            <div class="relative bg-white w-full max-w-md rounded-[2.5rem] p-10 shadow-2xl text-center transform transition-all border border-gray-100">
                
                <div class="mx-auto w-20 h-20 bg-red-50 rounded-3xl flex items-center justify-center mb-6">
                    <IconifyIcon icon="solar:logout-3-bold" width="40" height="40" class="text-red-500" />
                </div>

                <h3 class="text-2xl font-black text-[#000B58] mb-2">Keluar Sesi?</h3>
                <p class="text-[#A2B5CB] text-sm leading-relaxed mb-10 px-4">
                    Anda akan keluar dari panel sistem DATA BAPPEDA. Pastikan semua perubahan data telah tersimpan.
                </p>

                <div class="flex flex-col gap-3">
                    <Link 
                        href="/logout" 
                        method="post" 
                        as="button" 
                        @click="$emit('close')" 
                        class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-4 rounded-2xl transition-colors"
                    >
                        Ya, Keluar Sekarang
                    </Link>
                    
                    <button 
                        @click="$emit('close')"
                        class="w-full bg-transparent hover:bg-gray-50 text-[#A2B5CB] font-bold py-4 rounded-2xl transition-colors"
                    >
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>
