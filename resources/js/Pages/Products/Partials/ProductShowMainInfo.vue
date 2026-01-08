<script setup>
import IconLoading from '@/svg/mdi/IconLoading.vue';
import { useProduct } from '@/Composables/useProduct.js';

const {
    isLoading,
    qtyCount,
    message,
    product,
    cartItem,
    formattedProductPrice,
    instantCheckout,
    updateCount,
    upsertToCart,
} = useProduct();
</script>

<template>
    <div
        class="flex flex-col items-start pt-2 px-4 w-full
        sm:px-0"
    >
        <div
            class="flex flex-col mb-2
            sm:flex-col-reverse"
        >
            <div class="flex flex-col gap-y-1.5">
                <h1 class="font-semibold text-3xl">{{ product.name }}</h1>

                <div class="divide-x divide-stone-600/80 flex flex-row text-lg">
                    <span class="pr-2">4.9 (3 reviews)</span>

                    <span class="pl-2">2 sold</span>
                </div>
            </div>
        </div>

        <div
            class="font-semibold pb-2 text-3xl text-blue-600"
        >{{ formattedProductPrice }}</div>

        <!-- Product stock & CTAs -->
        <div
            v-if="product.stock_quantity < 1"
            class="bg-red-600 border border-red-600 flex flex-col gap-y-3 p-2 rounded-lg text-center text-white w-full
            md:w-auto"
        >
            We are sorry, but no stock available at this moment
        </div>
        <div
            v-else
            class="flex flex-col items-start w-full"
        >
            <div class="pb-2">
                Available stock: {{ product.stock_quantity }}
            </div>

            <div
                v-if="cartItem"
                class="border border-yellow-600 flex-grow-0 flex-shrink-0 mb-4 mt-2 p-2 rounded-lg text-yellow-800"
            >
                You have {{ cartItem.quantity }} in your cart
            </div>

            <!-- CTA section -->
            <div class="flex flex-col gap-4 items-start w-full">
                <div class="flex flex-row gap-4 items-center w-full">
                    <button
                        @click="updateCount(qtyCount - 1)"
                        :disabled="isLoading"
                        type="button"
                        class="bg-white border border-stone-300 flex-grow-0 flex-shrink-0 font-bold px-3 py-1 rounded-lg text-2xl"
                    >-</button>

                    <input
                        v-model.number="qtyCount"
                        :disabled="isLoading"
                        :max="product.stock_quantity"
                        :min="1"
                        type="number"
                        name="quantity"
                        id="quantity"
                        class="border border-stone-300 flex-grow px-4 py-1.5 rounded-lg text-center text-lg
                        md:flex-grow-0 md:min-w-32"
                    />

                    <button
                        @click="updateCount(qtyCount + 1)"
                        :disabled="isLoading"
                        type="button"
                        class="bg-white border border-stone-300 flex-grow-0 flex-shrink-0 font-bold px-3 py-1 rounded-lg text-2xl"
                    >+</button>
                </div>

                <div
                    class="flex flex-row gap-4 items-center w-full
                    md:items-start"
                >
                    <button
                        @click="instantCheckout"
                        :disabled="isLoading"
                        class="bg-blue-600 border border-blue-600 font-semibold px-4 py-2 text-center text-white rounded-lg w-full
                        md:min-w-32 md:w-auto"
                    >
                        <IconLoading
                            v-if="isLoading"
                            :size="24"
                            color="#fff"
                            class="animate-spin mx-auto"
                        />
                        <template v-else>Buy now</template>
                    </button>

                    <button
                        @click="upsertToCart"
                        :disabled="isLoading"
                        class="border border-blue-600 font-semibold px-4 py-2 text-blue-600 text-center rounded-lg w-full
                        md:min-w-32 md:w-auto"
                    >
                        <IconLoading
                            v-if="isLoading"
                            :size="24"
                            color="#2563eb"
                            class="animate-spin mx-auto"
                        />
                        <template v-else-if="cartItem">Update cart</template>
                        <template v-else>Add to cart</template>
                    </button>
                </div>
            </div>
        </div>

        <div
            v-if="message.show"
            :class="{
                'bg-green-100 border-green-600 text-green-600': message.type === 'success',
                'bg-red-100 border-red-600 text-red-600': message.type === 'error',
            }"
            class="border bottom-4 fixed font-semibold mt-2 p-2 rounded-lg text-center w-[calc(100%-28px)] z-10
            sm:w-[calc(100%-48px)]
            md:bg-transparent md:border-0 md:border-transparent md:bottom-auto md:p-0 md:static md:w-auto"
        >
            {{ message.text }}
        </div>
    </div>
</template>
