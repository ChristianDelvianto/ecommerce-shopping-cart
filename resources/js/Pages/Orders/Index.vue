<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import IconCheck from '@/svg/mdi/IconCheck.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const orders = page.props.orders?.data ?? [];
const currentPage = page.props.orders?.current_page ?? 1;
const lastPage = page.props.orders?.last_page ?? 1;

const computedOrders = computed(() => {
    if (orders.length === 0) return [];

    return orders.map(order => {
        // Format created_at string
        order.formatted_created_at = new Date(order.created_at).toGMTString();

        // Format subtotal_amount
        order.formatted_subtotal_amount = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
            }).format(order.subtotal_amount / 100);

        // Format order items
        order.formatted_items = order.items.map(orderItem => {
            orderItem.formatted_unit_price = new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                }).format(orderItem.unit_price / 100);

            orderItem.formatted_total_price = new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                }).format(orderItem.total_price / 100);

            return orderItem;
        });

        return order;
    });
});

const buyBack = () => {
    window.alert('The button working as expected');
};
</script>

<template>
    <Head title="My Order" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto px-4 py-8
        sm:px-0">
            <div
                v-if="orders.length"
                class="flex flex-col gap-8 w-full
                sm:px-6
                md:max-w-4xl
                lg:px-8"
            >
                <div
                    v-for="order in computedOrders"
                    :key="order.id"
                    class="flex flex-grow flex-row gap-4 justify-start w-full"
                >
                    <!-- Order status (Pending, paid, processing, shipped, rejected, completed) -->
                    <div class="bg-green-600 flex flex-grow-0 flex-shrink-0 items-center justify-center rounded-full size-12">
                        <IconCheck
                            :size="24"
                            color="#fff"
                        />
                    </div>

                    <div class="flex flex-col gap-2 justify-start w-full">
                        <div class="flex flex-col">
                            <span class="text-gray-600">{{ order.formatted_created_at }}</span>
                            <span>You ordered {{ order.formatted_items.length }} items</span>
                        </div>

                        <div class="flex flex-col gap-4">
                            <div
                                v-for="orderItem in order.formatted_items"
                                :key="orderItem.id"
                                class="flex flex-col gap-2 w-full"
                            >
                                <div class="flex flex-grow flex-row flex-shrink gap-2 justify-between w-full">
                                    <!-- Product image -->
                                    <div class="bg-stone-300 flex-grow-0 flex-shrink-0 relative rounded-lg size-20
                                    md:size-28">
                                        <span class="absolute bg-blue-600 -bottom-1.5 flex font-bold items-center justify-center -right-1.5 rounded-full text-sm size-8">{{ orderItem.quantity }}x</span>
                                    </div>

                                    <div class="flex flex-col gap-2 w-full">
                                        <div class="flex flex-row gap-2">
                                            {{ orderItem.product.name }}
                                        </div>

                                        <div>Unit price: {{  orderItem.formatted_unit_price }}</div>

                                        <div class="text-lg">
                                            In total:
                                            <span class="text-blue-600">{{ orderItem.formatted_total_price }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-grow flex-row flex-shrink items-center justify-between">
                            <div class="flex flex-col flex-grow-0 flex-shrink-0">
                                Subtotal:
                                <span class="text-blue-600 text-lg">{{ order.formatted_subtotal_amount }}</span>
                            </div>

                            <button
                                @click="buyBack"
                                type="buton"
                                class="bg-blue-600 flex-grow-0 flex-shrink-0 font-semibold px-3 py-1.5 rounded-full text-white
                                sm:min-w-32"
                            >Buy again</button>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col-reverse flex-grow flex-shrink gap-4 w-full
                sm:flex-row sm:items-center sm:justify-end">
                    <Link
                        v-if="currentPage > 1"
                        :href="route('orders.index', { page: currentPage - 1 })"
                        class="border border-stone-300 px-3 py-1.5 rounded-lg text-center
                        sm:flex-grow-0 sm:flex-shrink-0 sm:min-w-32"
                    >
                        Prev
                    </Link>

                    <Link
                        v-if="currentPage < lastPage"
                        :href="route('orders.index', { page: currentPage + 1 })"
                        class="border border-stone-300 px-3 py-1.5 rounded-lg text-center
                        sm:flex-grow-0 sm:flex-shrink-0 sm:min-w-32"
                    >
                        Next
                    </Link>
                </div>
            </div>
            <div
                v-else
                class="flex flex-col flex-grow flex-shrink gap-2 items-center"
            >
                You have not create any orders yet

                <Link
                    :href="route('products.index')"
                    class="text-blue-600"
                >Browse products</Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
