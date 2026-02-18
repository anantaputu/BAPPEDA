<script setup>
defineProps({
    show: Boolean,
    title: {
        type: String,
        default: 'Hapus Data?'
    },
    description: {
        type: String,
        default: 'Tindakan ini akan menghapus data secara permanen dari sistem BAPPEDA.'
    }
});

defineEmits(['close', 'confirm']);
</script>

<template>
    <Transition
        enter-active-class="duration-300 ease-out"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="duration-200 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
    >
        <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-6">
            <div class="absolute inset-0 bg-[#000B58]/40 backdrop-blur-sm" @click="$emit('close')"></div>
            
            <div class="relative bg-white w-full max-w-md rounded-[3rem] p-10 shadow-2xl text-center border border-slate-100">
                
                <div class="mx-auto w-24 h-24 bg-rose-50 rounded-[2.5rem] flex items-center justify-center mb-8">
                    <svg class="w-12 h-12 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>

                <h3 class="text-2xl font-black text-[#000B58] mb-4 uppercase tracking-tight">{{ title }}</h3>
                <p class="text-slate-400 text-sm font-medium leading-relaxed mb-10 px-4">
                    {{ description }}
                </p>

                <div class="flex flex-col gap-3">
                    <button 
                        @click="$emit('confirm')"
                        class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-4 rounded-2xl transition-colors"
                    >
                        Ya, Hapus Permanen
                    </button>
                    
                    <button 
                        @click="$emit('close')"
                        class="w-full bg-transparent hover:bg-gray-50 text-[#A2B5CB] font-bold py-4 rounded-2xl transition-colors"
                    >
                        Batalkan Tindakan
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>