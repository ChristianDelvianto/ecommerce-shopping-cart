<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const newProducts = page.props.products?.data ?? [];
const currentPage = page.props.products?.current_page ?? 1;
const lastPage = page.props.products?.last_page ?? 1;
</script>

<template>
    <Head title="Products" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div
                class="mx-auto max-w-7xl
                sm:px-6
                lg:px-8"
            >
                <h2 class="font-semibold text-xl">New products</h2>

                <div
                    class="grid grid-cols-2 gap-4 mt-4
                    lg:grid-cols-3
                    xl:grid-cols-5"
                >
                    <ProductCard
                        v-for="product in newProducts"
                        :key="product.id"
                        :product="product"
                    />
                </div>

                <div
                    class="flex flex-col gap-4 mt-4
                    sm:flex-row sm:items-center sm:justify-end"
                >
                    <Link
                        v-if="currentPage > 1"
                        :href="route('products.index', { page: currentPage - 1 })"
                        class="border border-blue-500 px-3 py-1 rounded-lg text-blue-500"
                    >
                        Prev
                    </Link>
                    <Link
                        v-if="currentPage < lastPage"
                        :href="route('products.index', { page: currentPage + 1 })"
                        class="border border-blue-500 px-3 py-1 rounded-lg text-blue-500"
                    >
                        Next
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
