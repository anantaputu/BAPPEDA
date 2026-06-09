<script setup>
import { computed } from 'vue';
import IconifyIcon from '@/Components/Base/IconifyIcon.vue';

const props = defineProps({
    label: String,
    value: [String, Number],
    icon: String,
    color: String,
    progress: Number,
    colors: Object
});

const isLegacyPathIcon = computed(() => typeof props.icon === 'string' && /^[Mm][\d\s.,-]/.test(props.icon.trim()));
</script>

<template>
    <div class="ui-panel h-40 p-6" style="border-radius: var(--radius-panel);">
        <div class="mb-3 flex items-center gap-4">
            <div :class="[
                'flex h-12 w-12 items-center justify-center', 
                colors[color].bg, 
                colors[color].text
            ]" style="border-radius: var(--radius-soft);">
                <svg v-if="isLegacyPathIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="icon" />
                </svg>
                <IconifyIcon v-else :icon="icon" width="24" height="24" />
            </div>
            <h3 class="ui-eyebrow leading-tight">
                {{ label }}
            </h3>
        </div>

        <div class="flex-1 flex items-center justify-center">
            <p class="ui-stat-value">
                {{ value }}
            </p>
        </div>
    </div>
</template>
