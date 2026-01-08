<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import OrderListItems from './Partials/OrderListItems.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const orders = page.props.orders?.data ?? [];
const currentPage = page.props.orders?.current_page ?? 1;
const lastPage = page.props.orders?.last_page ?? 1;

const formattedOrders = computed(() => {
    if (orders.length === 0) return [];

    return orders.map(order => {
        // Format created_at string
        order.formatted_created_at = new Date(order.created_at).toGMTString();

        // Format subtotal_amount
        const subtotalAmount = order.subtotal_amount / 100;
        order.formatted_subtotal_amount = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
            }).format(subtotalAmount);

        // Make new array of copied items
        const formattedOrderItems = order.items.map(orderItem => {
            // Add formatted price unit price
            const pricePerUnit = orderItem.unit_price / 100;
            orderItem.formatted_price_per_unit = new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                }).format(pricePerUnit);

            const totalPrice = orderItem.total_price / 100;
            orderItem.formatted_total_price = new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                }).format(totalPrice);

            return orderItem;
        });
        order.formatted_items = formattedOrderItems;

        return order;
    });
});

const buyBack = (orderId) => {
    window.alert('The button working as expected');
};
</script>

<template>
    <Head title="My Orders" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto px-4 py-8
        sm:px-0">
            <OrderListItems
                v-if="formattedOrders.length"
                @buy-back="buyBack"
                :current-page="currentPage"
                :last-page="lastPage"
                :items="formattedOrders"
            />
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
