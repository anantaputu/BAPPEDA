<script setup>
import { computed, useAttrs, ref } from 'vue';

defineOptions({
    inheritAttrs: false,
});

const model = defineModel({
    type: [String, Number],
    default: '',
});

const props = defineProps({
    id: {
        type: String,
        default: null,
    },
    type: {
        type: String,
        default: 'text',
    },
    class: {
        type: String,
        default: '',
    },
});

const attrs = useAttrs();
const input = ref(null);

const classes = computed(() => (
    'w-full border border-gray-300 px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/10 ui-body ' + props.class
));

defineExpose({
    focus: () => input.value?.focus(),
});
</script>

<template>
    <input
        ref="input"
        :id="id"
        v-model="model"
        :type="type"
        :class="classes"
        style="border-radius: var(--radius-soft);"
        v-bind="attrs"
    />
</template>
