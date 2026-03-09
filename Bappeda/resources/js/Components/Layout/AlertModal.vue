<script setup>
import IconifyIcon from '@/Components/Base/IconifyIcon.vue';

const props = defineProps({
    show: Boolean,
    title: {
        type: String,
        default: 'Informasi'
    },
    description: {
        type: String,
        default: ''
    },
    type: {
        type: String,
        default: 'info'
    },
    buttonText: {
        type: String,
        default: 'Tutup'
    }
});

const styleMap = {
    success: {
        wrap: 'bg-inovasi/10 border-inovasi/20',
        text: 'text-inovasi',
        icon: 'solar:check-circle-bold'
    },
    error: {
        wrap: 'bg-integritas/10 border-integritas/20',
        text: 'text-integritas',
        icon: 'solar:danger-triangle-bold'
    },
    warning: {
        wrap: 'bg-profesional/10 border-profesional/20',
        text: 'text-profesional',
        icon: 'solar:shield-warning-bold'
    },
    info: {
        wrap: 'bg-primary/10 border-primary/20',
        text: 'text-primary',
        icon: 'solar:info-circle-bold'
    }
};

const currentStyle = styleMap[props.type] || styleMap.info;

defineEmits(['close']);
</script>

<template>
    <Transition>
        <div v-if="show" class="fixed inset-0 z-[110] flex items-center justify-center p-6">
            <div class="absolute inset-0 bg-[#000B58]/35 backdrop-blur-sm" @click="$emit('close')"></div>

            <div class="relative bg-white w-full max-w-sm rounded-xl p-8 border border-gray-400 shadow-2xl">
                <div class="mx-auto w-16 h-16 rounded-xl flex items-center justify-center mb-6 border"
                    :class="[currentStyle.wrap]">
                    <IconifyIcon :icon="currentStyle.icon" width="32" height="32" :class="currentStyle.text" />
                </div>

                <div class="text-center mb-8">
                    <h3 class="text-xl font-black text-primary uppercase tracking-tight mb-2">{{ title }}</h3>
                    <p class="text-textsecondary text-xs font-bold leading-relaxed px-4 opacity-80">
                        {{ description }}
                    </p>
                </div>

                <button
                    @click="$emit('close')"
                    class="w-full bg-primary hover:bg-secondary text-white font-black uppercase tracking-widest py-4 rounded-xl transition-all shadow-lg shadow-primary/20 text-[10px]"
                >
                    {{ buttonText }}
                </button>
            </div>
        </div>
    </Transition>
</template>
