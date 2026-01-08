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
            d="M21 7L9 19l-5.5-5.5l1.41-1.41L9 16.17L19.59 5.59z"
        />
    </svg>
</template>
