import { router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, reactive, ref } from 'vue';

export const useCart = () => {
    const page = usePage();

    // Data
    const isEditing = ref(false);
    const items = ref([]);
    const itemToEdit = ref(null);
    const message = reactive({
        show: false,
        text: '',
        type: ''
    });

    // Computed
    const nonDeletedItems = computed(() => {
        if (items.value.length === 0) return [];

        return items.value.filter(item => !item.deleted);
    });
    const formattedItems = computed(() => {
        if (nonDeletedItems.value.length === 0) return [];

        return nonDeletedItems.value.map(item => {
            const pricePerUnit = item.product.price / 100;
            item.product.formatted_price_per_unit = new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                }).format(pricePerUnit);

            const subtotal = (item.quantity * item.product.price) / 100;
            item.formatted_sub_total = new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                }).format(subtotal);

            return item;
        });
    });
    const subtotal = computed(() => {
        if (nonDeletedItems.value.length === 0) return '$0';
        
        const total = nonDeletedItems.value.reduce((acc, val) => acc + (val.product.price * val.quantity), 0) / 100;
        
        return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
            }).format(total);
    });

    // Functions
    function resetMessage () {
        message.show = false;
        message.type = null;
        message.text = '';
    };
    function checkoutCart () {
        resetMessage();

        router.post(route('cart.checkout'), null, {
            onError: (err) => {
                console.error('Error checking out cart:', err);

                message.text = err.message ?? 'We are sorry, something went wrong, please try again';
                message.type = 'error';
                message.show = true;
            },
            onSuccess: () => {
                items.value = [];

                message.text = 'Your order has been created';
                message.type = 'success';
                message.show = true;
            }
        });
    };
    function closeEditModal () {
        isEditing.value = false;
        itemToEdit.value = null;
    };
    function openEditModal (item) {
        resetMessage();

        itemToEdit.value = item;
        isEditing.value = true;
    };
    function updateItem (quantity) {
        const item = items.value.find(cartItem => cartItem.id === itemToEdit.value.id);

        if (item && !item.deleted) {
            item.quantity = quantity;

            message.text = 'Cart item quantity updated';
            message.type = 'success';
            message.show = true;
        }

        closeEditModal();
    };
    function removeItem (item) {
        resetMessage();

        item.deleted = true;

        message.type = 'success';
        message.text = 'Cart item deleted';
        message.show = true;

        router.delete(route('cart.destroy', item.id), {
            onError: (err) => {
                console.error('Error occured when removing a cart item:', err);

                item.deleted = false;

                message.text = err.message ?? 'Failed to delete cart item, please try again';
                message.type = 'error';
                message.show = true;
            },
            onSuccess: () => {
                // Permanently remove from ref array
                items.value = items.value.filter(cartItem => cartItem.value !== item.id);
            }
        });
    };
    function pushItems () {
        if (page.props.items?.length === 0) return;

        page.props.items.forEach(item => {
            // Add key for filter
            item.deleted = false;

            items.value.push(item);
        });
    };

    // Lifecycle
    onMounted(() => {
        pushItems();
    });

    return {
        message,
        isEditing,
        itemToEdit,
        formattedItems,
        subtotal,
        checkoutCart,
        closeEditModal,
        openEditModal,
        updateItem,
        removeItem,
    };
};
