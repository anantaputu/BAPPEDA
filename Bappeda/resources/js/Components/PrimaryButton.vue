<template>
    <button
        class="inline-flex items-center justify-center bg-primary px-5 py-3 text-white transition hover:bg-secondary disabled:cursor-not-allowed disabled:opacity-60 ui-chip"
        style="border-radius: var(--radius-soft);"
    >
        <slot />
    </button>
</template>
