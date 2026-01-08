<style scoped>
.image-placeholder {
    @apply flex-grow-0 flex-shrink-0 h-full relative w-1/5;
}

.image-placeholder::after {
    @apply absolute bg-green-300 pb-[100%] w-full;
    content: '';
}
</style>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const product = computed(() => page.props.product);
</script>

<template>
    <div
        class="flex flex-col product-card relative
        md:w-[75%]
        lg:w-2/3"
    >
        <div
            class="product-image-placeholder"
        ></div>
    </div>
</template>
