<script setup>
import IconClose from '@/svg/mdi/IconClose.vue';
import IconLoading from '@/svg/mdi/IconLoading.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const emit = defineEmits({
    close: () => true,
    success: (quantity) => true
});

const props = defineProps({
    item: { type: Object, required: true }
});

const errorMessage = ref('');
const isLoading = ref(false);
const qtyCount = ref(props.item.quantity ?? 1);

const close = () => {
    if (isLoading.value) return;

    emit('close');
};
const decrementCount = () => {
    if (isLoading.value
    || (qtyCount.value - 1) < 1)
    return;

    qtyCount.value = qtyCount.value - 1;
};
const incrementCount = () => {
    if (isLoading.value
    || (qtyCount.value + 1) > props.item.product.stock_quantity)
    return;

    qtyCount.value = qtyCount.value + 1;
}
const updateCartQuantity = () => {
    if (isLoading.value) return;

    isLoading.value = true;

    router.put(`/cart/products/${props.item.product.id}`, {
        count: qtyCount.value
    }, {
        onError: (err) => {
            console.log('Error updating cart item quantity:', err);
        },
        onSuccess: () => {
            emit('success', qtyCount.value);
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
};
</script>

<template>
    <div
        class="bg-black/60 fixed flex items-end inset-0
        sm:items-center sm:justify-center"
    >
        <div
            class="bg-white flex flex-col h-4/5 w-full z-10
            sm:h-auto sm:max-h-[80%] sm:max-w-lg sm:min-h-[33rem] sm:mx-auto sm:overflow-hidden sm:rounded-xl"
        >
            <div
                class="flex flex-grow-0 flex-row flex-shrink-0 h-14 items-center pl-4 pr-2"
            >
                <span
                    class="flex-grow flex-shrink font-semibold text-2xl"
                >Edit cart item</span>

                <button
                    @click="close"
                    :disabled="isLoading"
                    type="button"
                    class="flex flex-grow-0 flex-shrink-0 items-center justify-center size-10"
                >
                    <IconClose :size="24" />
                </button>
            </div>

            <div
                class="flex flex-col flex-grow gap-4 h-[calc(100%-114px)] overflow-y-auto p-4"
            >
                <div
                    v-if="errorMessage"
                    class="bg-red-100 border border-red-600 p-4 rounded-lg text-left text-red-600"
                >{{ errorMessage }}</div>

                <div
                    class="flex flex-col flex-grow flex-shrink"
                >
                    <div class="flex flex-row gap-4">
                        <div class="bg-stone-300 flex-grow-0 flex-shrink-0 rounded-lg size-24">
                            
                        </div>

                        <div class="flex flex-col gap-2 justify-start">
                            <span>{{ props.item.product.name }}</span>
                            <span class="font-semibold">Available stock: {{ props.item.product.stock_quantity }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col flex-grow flex-shrink justify-end">
                        <span class="pb-2">Update cart item quantity</span>

                        <div class="flex flex-grow-0 flex-shrink-0 flex-row gap-4 items-center">
                            <button
                                @click="decrementCount"
                                :disabled="isLoading"
                                type="button"
                                class="bg-white border border-stone-300 flex-grow-0 flex-shrink-0 font-bold px-3 py-1 rounded-lg text-2xl"
                            >-</button>

                            <input
                                v-model.number="qtyCount"
                                :disabled="isLoading"
                                :max="props.item.stock_quantity"
                                :min="1"
                                type="number"
                                name="quantity"
                                id="quantity"
                                class="border border-stone-300 flex-grow px-4 py-1.5 rounded-lg text-center text-lg"
                            />

                            <button
                            @click="incrementCount"
                            :disabled="isLoading"
                            type="button"
                            class="bg-white border border-stone-300 flex-grow-0 flex-shrink-0 font-bold px-3 py-1 rounded-lg text-2xl"
                        >+</button>
                        </div>
                    </div>
                </div>

                
            </div>

            <div
                class="flex flex-grow-0 flex-row flex-shrink-0 gap-4 items-center pb-4 px-4"
            >
                <button
                    @click="close"
                    :disabled="isLoading"
                    type="button"
                    class="bg-stone-100 border border-stone-300 flex-grow flex-shrink rounded-full text-center py-2"
                >Cancel</button>

                <button
                    @click="updateCartQuantity"
                    :disabled="isLoading"
                    type="button"
                    class="bg-blue-600 flex-grow flex-shrink rounded-full text-center text-white py-2"
                >
                    <IconLoading
                        v-if="isLoading"
                        :size="24"
                        color="#fff"
                        class="animate-spin mx-auto"
                    />
                    <template v-else>Update cart</template>
                </button>
            </div>
        </div>
    </div>
</template>
