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
    <Transition>
        <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-6">
            <div class="absolute inset-0 bg-white/20 backdrop-blur-sm" @click="$emit('close')"></div>
            
            <div class="relative bg-white w-full max-w-sm rounded-xl p-8 border border-gray-400">
                
                <div class="mx-auto w-16 h-16 bg-integritas/10 rounded-xl flex items-center justify-center mb-6 border border-integritas/20">
                    <svg class="w-8 h-8 text-integritas" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>

                <div class="text-center mb-8">
                    <h3 class="text-xl font-black text-primary uppercase tracking-tight mb-2">{{ title }}</h3>
                    <p class="text-textsecondary text-xs font-bold leading-relaxed px-4 opacity-70">
                        {{ description }}
                    </p>
                </div>

                <div class="flex flex-col gap-2">
                    <button 
                        @click="$emit('confirm')"
                        class="w-full bg-integritas hover:bg-red-700 text-white font-black uppercase tracking-widest py-4 rounded-xl transition-all shadow-lg shadow-integritas/20 text-[10px]"
                    >
                        Ya, Hapus Permanen
                    </button>
                    
                    <button 
                        @click="$emit('close')"
                        class="w-full bg-white text-textsecondary font-black py-3 rounded-xl transition-colors uppercase tracking-widest text-[9px] hover:bg-bgsoft"
                    >
                        Batalkan Tindakan
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>