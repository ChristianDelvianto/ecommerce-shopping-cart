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
            d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2L9.19 8.62L2 9.24l5.45 4.73L5.82 21z"
        />
    </svg>
</template>
