<script setup>
defineProps({
    type: {
        type: String,
        default: "button",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <button
        :type="type"
        :disabled="disabled"
        class="px-6 py-2 bg-primary-container text-on-primary-container app-text font-semibold rounded-lg uppercase hover:bg-orange-500 transition-all shadow-sm cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
    >
        <slot />
    </button>
</template>
