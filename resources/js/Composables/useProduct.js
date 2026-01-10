import { router, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref, watch } from 'vue';

export const useProduct = () => {
    const page = usePage();

    // Data
    const qtyCount = ref(1);
    const message = reactive({
        show: false,
        text: '',
        type: ''
    });

    // Computed
    const product = computed(() => page.props.product);
    const productCartItem = computed(() => page.props.product.cart_items?.[0] ?? null);
    const recommended = computed(() => page.props.recommended ?? []);

    // Functions
    function resetMessage () {
        message.show = false;
        message.type = null;
        message.text = '';
    };
    function instantCheckout () {
        window.alert('The button is working, but not in development scope');
    };
    function upsertToCart () {
        resetMessage();
    
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
            }
        });
    };

    watch(() => productCartItem.value?.quantity, (val) => {
        qtyCount.value = val ?? 1;
    }, { immediate: true });

    return {
        qtyCount,
        message,
        product,
        productCartItem,
        recommended,
        instantCheckout,
        upsertToCart,
    };
};
