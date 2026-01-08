import { router, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref, watch } from 'vue';

export const useProduct = () => {
    const page = usePage();

    // Data
    const isLoading = ref(false);
    const qtyCount = ref(1);
    const message = reactive({
        show: false,
        text: '',
        type: ''
    });

    // Computed
    const product = computed(() => page.props.product);
    const cartItem = computed(() => product.value.cart_items?.[0] ?? null);
    const recommended = computed(() => page.props.recommended ?? []);
    const formattedProductPrice = computed(() => {
        const productPrice = page.props.product.price / 100;

        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
        }).format(productPrice);
    });

    // Functions
    function resetMessage () {
        message.show = false;
        message.type = null;
        message.text = '';
    };
    function instantCheckout () {
        window.alert('Congratulation, the button is working');
    };
    function updateCount (count) {
        resetMessage();

        if (isLoading.value
        || count < 1
        || count > product.value.stock_quantity)
        return;

        qtyCount.value = count;
    };
    function upsertToCart () {
        resetMessage();
    
        if (isLoading.value) return;
    
        isLoading.value = true;
    
        router.put(route('cart.upsert', product.value.id), {
            count: qtyCount.value
        }, {
            preserveScroll: true,
            preserveState: true, // Reload product to reflect authoritative server state (e.g. stock or availability changes)
            onError: (err) => {
                console.error('Error upsert product to cart:', err);
    
                message.text = err.count ?? 'Sorry, something went wrong, please try again later';
                message.type = 'error';
                message.show = true;

                // Reload product, in case if it's deleted, stock change, etc
                router.reload({
                    only: ['product']
                });
            },
            onSuccess: () => {
                message.text = page.flash.success;
                message.type = 'success';
                message.show = true;
                
                router.reload({
                    only: ['product'],
                });
            },
            onFinish: () => {
                isLoading.value = false;
            }
        });
    };

    // Watch
    watch(() => cartItem.value?.quantity, (val) => {
        qtyCount.value = val ?? 1;
    }, { immediate: true });

    return {
        isLoading,
        qtyCount,
        message,
        product,
        cartItem,
        recommended,
        formattedProductPrice,
        instantCheckout,
        updateCount,
        upsertToCart,
    };
};
