<script setup>
const props = defineProps({
    icon: {
        type: String,
        required: true,
    },
    width: {
        type: [Number, String],
        default: 24,
    },
    height: {
        type: [Number, String],
        default: 24,
    },
});
</script>

<template>
    <span
        class="iconify inline-flex"
        :data-icon="icon"
        :data-width="String(width)"
        :data-height="String(height)"
        aria-hidden="true"
    ></span>
</template>
