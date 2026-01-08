<script setup>
import IconLoading from '@/svg/mdi/IconLoading.vue';
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const page = usePage();
const product = computed(() => page.props.product);
const formattedProductPrice = computed(() => {
    const productPrice = page.props.product.price / 100;

    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(productPrice);
});
const cartItem = computed(() => product.value?.cart_items?.[0] ?? null);

const errorMessage = ref(page.flash.error ?? '');
const successMessage = ref(page.flash.success ?? '');
const isLoading = ref(false);
const qtyCount = ref(1);

watch(() => cartItem.value?.quantity, (val) => {
    qtyCount.value = val ?? 1;
}, { immediate: true });

const resetMessage = () => {
    successMessage.value = '';
    errorMessage.value = '';
};
const decrementCount = () => {
    resetMessage();

    if (isLoading.value
    || (qtyCount.value - 1) < 1)
    return;

    qtyCount.value = qtyCount.value - 1;
};
const incrementCount = () => {
    resetMessage();

    if (isLoading.value
    || (qtyCount.value + 1) > product.value.stock_quantity)
    return;

    qtyCount.value = qtyCount.value + 1;
}
const instantCheckout = () => {
    window.alert('Congratulation, the button is working');
};
const upsertProductToCart = () => {
    if (isLoading.value
    || qtyCount.value < 1
    || cartItem.value && qtyCount.value === cartItem.value.quantity
    || qtyCount.value > product.value.stock_quantity)
        return;

    isLoading.value = true;

    resetMessage();

    router.put(`/cart/products/${product.value.id}`, {
        count: qtyCount.value
    }, {
        preserveScroll: true,
        preserveState: true, // Reload product to reflect authoritative server state (e.g. stock or availability changes)
        onError: (err) => {
            if (err.count) {
                errorMessage.value = err.count;
            } else {
                errorMessage.value = err.message ?? 'Sorry, something went wrong, please try again later';
            }

            router.reload({
                only: ['product'] // Reload product, in case if it's deleted, stock change, etc
            });
        },
        onSuccess: () => {
            if (page.flash.success) {
                successMessage.value = page.flash.success;
            }
            
            router.reload({
                only: ['product'],
            });
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
};
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
            <div
                class="flex flex-col gap-y-1.5"
            >
                <h1
                    class="font-semibold text-3xl"
                >{{ product.name }}</h1>

                <div class="divide-x divide-stone-600/80 flex flex-row text-lg">
                    <!-- Product star and total reviews -->
                    <span class="pr-2">4.9 (3 reviews)</span>

                    <!-- Product total sold -->
                    <span class="pl-2">2 sold</span>
                </div>
            </div>
        </div>

        <!-- Product price -->
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
                        @click="decrementCount"
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
                        @click="incrementCount"
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
                        @click="upsertProductToCart"
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

        <!-- Success message -->
        <div
            v-if="successMessage"
            class="bg-green-100 border border-green-600 bottom-4 fixed font-semibold mt-2 p-2 rounded-lg text-center text-green-600 w-[calc(100%-28px)] z-10
            sm:w-[calc(100%-48px)]
            md:bg-transparent md:border-0 md:border-transparent md:bottom-auto md:p-0 md:static md:w-auto"
        >
            {{ successMessage }}
        </div>
        <div
            v-else-if="errorMessage"
            @click="errorMessage = ''"
            class="bg-red-100 border border-red-600 bottom-4 fixed font-semibold mt-2 p-2 rounded-lg text-center text-red-600 w-[calc(100%-28px)] z-10
            sm:w-[calc(100%-48px)]
            md:bg-transparent md:border-0 md:border-transparent md:bottom-auto md:p-0 md:static md:w-auto"
        >
            {{ errorMessage }}
        </div>
    </div>
</template>
