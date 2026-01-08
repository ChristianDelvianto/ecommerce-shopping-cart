<script setup>

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
            >Product reviews</h2>

            <div
                class="flex flex-col gap-2"
            >
                <div
                    v-for="n in 3"
                    :key="n"
                    class="flex flex-1 flex-row gap-4"
                >
                    <div
                        class="bg-gray-300 flex-grow-0 flex-shrink-0 rounded-full size-12"
                    ></div>

                    <div
                        class="flex flex-col items-start"
                    >
                        <span
                            class="font-semibold"
                        >User {{ n }}</span>

                        <div>Lorem ipsum dolor sit amet consectetur adipisicing elit. Placeat similique laborum veniam est magnam dicta nesciunt eligendi accusantium eaque deleniti aut commodi laboriosam, suscipit sint nam veritatis nemo ducimus dolore.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
