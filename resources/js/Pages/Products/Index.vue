<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
const page = usePage();
const { current_page, data, last_page } = page.props.products;
</script>

<template>
    <Head title="Products" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl">New products</h2>

                <div
                    class="grid grid-cols-2 gap-4 mt-4
                    lg:grid-cols-3
                    xl:grid-cols-5"
                >
                    <ProductCard
                        v-for="product in data"
                        :key="product.id"
                        :product="product"
                    />
                </div>

                <div class="flex flex-col gap-4 mt-4
                sm:flex-row sm:items-center sm:justify-end">
                    <Link
                        v-if="current_page > 1"
                        :href="route('products.index', { page: current_page - 1 })"
                        class="border border-blue-500 px-4 py-2 text-blue-500 rounded-lg"
                    >
                        Prev
                    </Link>
                    <Link
                        v-if="current_page < last_page"
                        :href="route('products.index', { page: current_page + 1 })"
                        class="border border-blue-500 px-4 py-2 text-blue-500 rounded-lg"
                    >
                        Next
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
