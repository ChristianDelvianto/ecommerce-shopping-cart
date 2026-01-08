<script setup>
import ProductCard from '@/Components/ProductCard.vue';
import { useProduct } from '@/Composables/useProduct.js';

const {
    recommended,
} = useProduct();
</script>

<template>
    <div
        v-if="recommended.length"
        class="mt-4 px-4
        sm:px-0"
    >
        <div
            class="bg-white p-4 rounded-lg shadow shadow-stone-300/60"
        >
            <h2
                class="font-semibold mb-4 text-xl"
            >Recommended products</h2>

            <div
                class="grid grid-cols-2 gap-4 mt-4
                lg:grid-cols-3
                xl:grid-cols-5"
            >
                <ProductCard
                    v-for="recommendedProduct in recommended"
                    :key="recommendedProduct.id"
                    :product="recommendedProduct"
                />
            </div>
        </div>
    </div>
</template>
