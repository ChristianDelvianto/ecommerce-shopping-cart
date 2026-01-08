<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CartEditModal from '@/Components/CartEditModal.vue';
import IconLoading from '@/svg/mdi/IconLoading.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useCart } from '@/Composables/useCart.js';

const {
    message,
    isEditing,
    isLoading,
    itemToEdit,
    formattedItems,
    subtotalAmount,
    resetMessage,
    checkoutCart,
    closeEditModal,
    openEditModal,
    updateItem,
    removeItem
} = useCart();
</script>

<template>
    <Head title="My cart" />

    <AuthenticatedLayout>
        <div class="py-8">
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

                <div
                    v-if="formattedItems.length"
                    class="flex flex-col gap-4 min-w-[640px] overflow-x-auto w-full"
                >
                    <table class="bg-white w-full">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product Info</th>
                                <th>Available Stock</th>
                                <th>Price Per Unit</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="item in formattedItems"
                                :key="item.id"
                            >
                                <!-- Id -->
                                <td class="px-3 py-1.5 text-center">{{ item.id }}</td>

                                <!-- Product info -->
                                <td>
                                    <Link
                                        :href="route('products.show', item.product.id)"
                                        class="flex flex-row gap-x-3 items-center p-3 product-card"
                                    >
                                        <span class="flex-grow-0 flex-shrink-0 product-image-placeholder rounded-lg size-20"></span>

                                        <span class="text-blue-600">{{ item.product.name }}</span>
                                    </Link>
                                </td>

                                <!-- Product stock quantity -->
                                <td class="text-center">
                                    {{ item.product.stock_quantity }}
                                </td>

                                <!-- Product price per unit -->
                                <td class="text-center">
                                    {{ item.product.formatted_price_per_unit }}
                                </td>

                                <!-- Cart item quantity -->
                                <td class="text-center">
                                    {{ item.quantity }}
                                </td>

                                <!-- Subtotal amount -->
                                <td class="text-center">
                                    {{ item.formatted_sub_total }}
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="flex flex-col gap-y-3 items-center">
                                        <button
                                            @click="openEditModal(item)"
                                            :disabled="isLoading"
                                            type="button"
                                            class="bg-green-100 border border-green-600 px-3 py-1.5 rounded-lg text-green-600 text-sm"
                                        >Edit</button>

                                        <button
                                            @click="removeItem(item)"
                                            :disabled="isLoading"
                                            type="button"
                                            class="bg-red-100 border border-red-600 px-3 py-1.5 rounded-lg text-red-600 text-sm"
                                        >Delete</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="flex flex-col items-end w-full">
                        <div class="border-t-2 border-stone-300 flex flex-col items-end pt-2 w-full
                        sm:flex-row sm:items-center sm:justify-between">
                            Total
                            
                            <span class="font-semibold text-blue-600 text-2xl">{{ subtotalAmount }}</span>
                        </div>

                         <button
                            @click="checkoutCart"
                            :disabled="isLoading"
                            type="button"
                            class="bg-blue-600 flex-grow-0 flex-shrink-0 font-semibold px-4 py-2 rounded-lg text-white w-full
                            sm:min-w-32 sm:mt-4 sm:w-auto"
                        >
                            <IconLoading
                                v-if="isLoading"
                                :size="24"
                                color="#fff"
                                class="animate-spin mx-auto"
                            />
                            <template v-else>Checkout</template>
                        </button>
                    </div>
                </div>
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
