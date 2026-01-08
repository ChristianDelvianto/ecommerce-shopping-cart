<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const product = computed(() => page.props.product);
</script>

<template>
    <div
        class="px-4 mt-4
        sm:px-0"
    >
        <div
            class="bg-white p-4 rounded-lg shadow shadow-stone-300/60"
        >
            <h2
                class="border-b-2 border-gray-300 font-semibold mb-4 pb-2 text-xl"
            >Product info</h2>

            <div>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda ipsum ipsam aut minus at laborum, pariatur maxime porro a minima alias dolor saepe aliquid, deleniti tempora, voluptas autem accusamus eveniet.
            </div>
        </div>
    </div>
</template>
