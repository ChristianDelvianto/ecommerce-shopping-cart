<script setup>
import IconLoading from '@/svg/mdi/IconLoading.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    items: { type: Array, default: [] },
    loading: { type: Boolean, default: false },
    subtotal: { type: String, default: '$0' }
});

defineEmits({
    checkout: () => true,
    editItem: (cartItem) => true,
    removeItem: (cartItem) => true,
});
</script>

<template>
    <div
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
                    v-for="(cartItem, index) in items"
                    :key="cartItem.id"
                >
                    <td class="px-3 py-1.5 text-center">
                        {{ index + 1 }}
                    </td>

                    <!-- Product info -->
                    <td>
                        <Link
                            :href="route('products.show', cartItem.product.id)"
                            class="flex flex-row gap-x-3 items-center p-3 product-card"
                        >
                            <span class="flex-grow-0 flex-shrink-0 product-image-placeholder rounded-lg size-20"></span>

                            <span class="text-blue-600">{{ cartItem.product.name }}</span>
                        </Link>
                    </td>

                    <td class="text-center">
                        {{ cartItem.product.stock_quantity }}
                    </td>

                    <td class="text-center">
                        {{ cartItem.product.formatted_price_per_unit }}
                    </td>

                    <td class="text-center">
                        {{ cartItem.quantity }}
                    </td>

                    <td class="text-center">
                        {{ cartItem.formatted_sub_total }}
                    </td>

                    <!-- Actions -->
                    <td>
                        <div class="flex flex-col gap-y-3 items-center">
                            <button
                                @click="$emit('editItem', cartItem)"
                                :disabled="loading"
                                type="button"
                                class="bg-green-100 border border-green-600 px-3 py-1.5 rounded-lg text-green-600 text-sm"
                            >Edit</button>

                            <button
                                @click="$emit('removeItem', cartItem)"
                                :disabled="loading"
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
                
                <span class="font-semibold text-blue-600 text-2xl">{{ subtotal }}</span>
            </div>

            <button
                @click="$emit('checkout')"
                :disabled="loading"
                type="button"
                class="bg-blue-600 flex-grow-0 flex-shrink-0 font-semibold px-4 py-2 rounded-lg text-white w-full
                sm:min-w-32 sm:mt-4 sm:w-auto"
            >
                <IconLoading
                    v-if="loading"
                    :size="24"
                    color="#fff"
                    class="animate-spin mx-auto"
                />
                <template v-else>Checkout</template>
            </button>
        </div>
    </div>
</template>
