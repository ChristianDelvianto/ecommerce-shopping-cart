<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CartListTable from './Partials/CartListTable.vue';
import CartEditModal from './Partials/CartEditModal.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useCart } from '@/Composables/useCart.js';

const {
    message,
    isEditing,
    isLoading,
    itemToEdit,
    formattedItems,
    subtotal,
    checkoutCart,
    closeEditModal,
    openEditModal,
    updateItem,
    removeItem,
} = useCart();
</script>

<template>
    <Head title="My Cart" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-8">
            <div
                class="flex flex-col gap-4 max-w-7xl mx-auto w-full
                sm:px-6
                lg:px-8"
            >
                <div
                    v-if="message.show"
                    :class="{
                        'bg-green-100 border-green-600 text-green-600': message.type === 'success',
                        'bg-red-100 border-red-600 text-red-600': message.type === 'error'
                    }"
                    class="border p-4 rounded-lg text-center"
                >{{ message.text }}</div>

                <CartListTable
                    v-if="formattedItems.length"
                    @checkout="checkoutCart"
                    @edit-item="openEditModal"
                    @remove-item="removeItem"
                    :items="formattedItems"
                    :loading="isLoading"
                    :subtotal="subtotal"
                />
                <div
                    v-else
                    class="flex flex-col flex-grow flex-shrink gap-2 items-center"
                >
                    You don't have any items in your cart

                    <Link
                        :href="route('products.index')"
                        class="text-blue-600"
                    >Browse products</Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <CartEditModal
        v-if="isEditing && itemToEdit"
        @close="closeEditModal"
        @success="updateItem"
        :item="itemToEdit"
    />
</template>
