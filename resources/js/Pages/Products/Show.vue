<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProductShowGallery from '@/Components/ProductShowGallery.vue';
import ProductShowInfoDetail from '@/Components/ProductShowInfoDetail.vue';
import ProductShowMainInfo from '@/Components/ProductShowMainInfo.vue';
import ProductShowUserReviews from '@/Components/ProductShowUserReviews.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const product = computed(() => page.props.product);
const recommendedProducts = computed(() => page.props.recommended ?? []);
</script>

<template>
    <Head :title="product.name" />

    <AuthenticatedLayout>
        <div
            class="pb-8
            sm:pt-8"
        >
            <div
                class="max-w-7xl mx-auto
                sm:px-6
                lg:px-8"
            >
                <div
                    class="flex flex-col w-full
                    md:flex-row md:gap-x-4 md:items-start"
                >
                    <ProductShowGallery />

                    <ProductShowMainInfo />
                </div>

                <ProductShowInfoDetail />

                <ProductShowUserReviews />

                <!-- Recommended products -->
                <div
                    v-if="recommendedProducts.length"
                    class="px-4 mt-4
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
                                v-for="recommendedProduct in recommendedProducts"
                                :key="recommendedProduct.id"
                                :product="recommendedProduct"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
