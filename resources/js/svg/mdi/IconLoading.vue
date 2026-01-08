<script setup>
defineProps({
    color: {
        type: String,
        default: 'rgb(82, 82, 82)'
    },
    size: {
        type: Number,
        default: 24
    }
});
</script>

<template>
    <svg
        :height="size"
        :width="size"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            :fill="color"
            d="M12 4V2A10 10 0 0 0 2 12h2a8 8 0 0 1 8-8"
        />
    </svg>
</template>
