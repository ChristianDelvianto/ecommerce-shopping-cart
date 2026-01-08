<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    product: { type: Object, required: true }
});

const productPrice = computed(() => (props.product.price / 100).toFixed(2));
</script>

<template>
    <Link
        :href="route('products.show', product.id)"
        class="border border-stone-300 flex flex-col overflow-hidden product-card rounded-lg w-full"
    >
        <span class="product-image-placeholder"></span>

        <span class="product-info">
            <span>{{ product.name }}</span>

            <span class="font-semibold text-blue-600 text-lg">${{ productPrice }}</span>
        </span>
    </Link>
</template>
