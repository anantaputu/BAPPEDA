<script setup>
defineProps({
    for: {
        type: String,
        default: '',
    },
    value: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <label :for="for" class="block text-sm font-bold text-slate-700">
        <slot>{{ value }}</slot>
    </label>
</template>
