<template>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="text-center mb-10">
            <div
                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/30 mb-4"
            >
                <svg
                    class="w-8 h-8 text-primary-600 dark:text-primary-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                    />
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">
                Ready to Checkout?
            </h1>
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
                Click below to send your order via WhatsApp
            </p>
        </div>

        <!-- Empty Cart State -->
        <div
            v-if="cartStore.items.length === 0"
            class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl shadow-lg"
        >
            <svg
                class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"
                />
            </svg>
            <h3 class="mt-4 text-xl font-medium text-gray-900 dark:text-white">
                Your cart is empty
            </h3>
            <p class="mt-2 text-gray-500 dark:text-gray-400">
                Add some items to proceed with checkout.
            </p>
            <div class="mt-6">
                <router-link
                    to="/products"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-primary-600 hover:bg-primary-700 shadow-sm transition-all duration-200 transform hover:scale-105 dark:bg-primary-700 dark:hover:bg-primary-800"
                >
                    Continue Shopping
                </router-link>
            </div>
        </div>

        <!-- Loading State -->
        <div v-else-if="cartLoading" class="flex justify-center py-20">
            <div
                class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"
            ></div>
        </div>

        <!-- Main Checkout UI -->
        <div v-else class="space-y-8">
            <!-- Order Summary Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden"
            >
                <div
                    class="bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-900/10 px-6 py-4"
                >
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        🛒 Your Order Summary
                    </h2>
                </div>
                <div class="p-6">
                    <!-- Items List -->
                    <div class="space-y-4 mb-6 max-h-96 overflow-y-auto pr-2">
                        <div
                            v-for="item in cartStore.items"
                            :key="item.id"
                            class="flex items-start space-x-4 py-3 border-b border-gray-100 last:border-b-0 dark:border-gray-700"
                        >
                            <img
                                :src="
                                    item.product_detail.main_image
                                "
                                :alt="item.name"
                                class="w-16 h-16 object-cover rounded-lg flex-shrink-0 border border-gray-200 dark:border-gray-600"
                            />
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="font-medium text-gray-900 dark:text-white line-clamp-2"
                                >
                                    {{ item.name }}
                                </h3>
                                <div
                                    class="mt-1 text-sm text-gray-500 dark:text-gray-400 space-y-1"
                                >
                                    <p v-if="item.color">
                                        Color: {{ item.color }}
                                    </p>
                                    <p v-if="item.size">
                                        Size: {{ item.size }}
                                    </p>
                                    <p>Qty: {{ item.quantity }}</p>
                                </div>
                            </div>
                            <div
                                class="text-right font-semibold text-gray-900 dark:text-white whitespace-nowrap"
                            >
                                {{
                                    formatPrice(
                                        item.product_detail.final_price *
                                            item.quantity,
                                    )
                                }}
                            </div>
                        </div>
                    </div>

                    <!-- Totals -->
                    <div
                        class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 space-y-3"
                    >
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-300"
                                >Subtotal</span
                            >
                            <span
                                class="font-medium text-gray-900 dark:text-white"
                                >{{
                                    formatPrice(cartStore.summary.subtotal)
                                }}</span
                            >
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-300"
                                >Shipping</span
                            >
                            <span
                                class="font-medium text-gray-900 dark:text-white"
                                >{{ formatPrice(shippingCost) }}</span
                            >
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-300"
                                >Tax ({{
                                    siteStore.settings.tax_rate ?? 0
                                }}%)</span
                            >
                            <span
                                class="font-medium text-gray-900 dark:text-white"
                                >{{ formatPrice(taxAmount) }}</span
                            >
                        </div>
                                     <div class="flex justify-between text-sm">
                            <span class="text-green-600 dark:text-green-300"
                                >discount</span
                            >
                            <span
                                class="font-medium text-green-600 dark:text-green-300"
                                >-{{ formatPrice(cartStore.discount) }} {{ siteStore.settings.currency }}</span
                            >
                        </div>
                        <div
                            class="border-t border-gray-200 dark:border-gray-600 pt-3 flex justify-between text-lg font-bold"
                        >
                            <span class="text-gray-900 dark:text-white"
                                >Total</span
                            >
                            <span
                                class="text-primary-600 dark:text-primary-400"
                                >{{ formatPrice(grandTotal) }}</span
                            >
                        </div>
                        <div
                            v-if="
                                siteStore.settings
                                    ?.show_checkout_alert_message ?? false
                            "
                            class="flex items-start space-x-3 bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 dark:border-yellow-500 rounded-xl p-4 text-sm shadow-sm"
                        >
                            <svg
                                class="w-6 h-6 text-yellow-500 flex-shrink-0 mt-0.5"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.518 11.594c.75 1.336-.213 3.007-1.743 3.007H3.482c-1.53 0-2.493-1.67-1.743-3.007L8.257 3.1zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-2a.75.75 0 01-.75-.75V8a.75.75 0 011.5 0v3.25A.75.75 0 0110 12z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <p class="text-gray-700 dark:text-gray-200">
                                <span
                                    class="font-bold text-yellow-600 dark:text-yellow-400"
                                    >Note:
                                </span>
                                {{
                                    siteStore.settings
                                        .whatsapp_warning_message ||
                                    "Prices may vary depending on customization, delivery, and other factors. Please contact us for final confirmation."
                                }}
                            </p>
                        </div>
                        <div
                            v-if="
                                siteStore.settings
                                    ?.show_checkout_disclaimer_message ?? false
                            "
                            class="flex items-start space-x-3 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-400 dark:border-red-500 rounded-xl p-4 text-sm shadow-sm"
                        >
                            <svg
                                class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M18 10c0 4.418-3.582 8-8 8s-8-3.582-8-8 3.582-8 8-8 8 3.582 8 8zm-8-4a1 1 0 100 2 1 1 0 000-2zm1 4a1 1 0 10-2 0v4a1 1 0 102 0v-4z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <p class="text-gray-700 dark:text-gray-200">
                                <span
                                    class="font-bold text-red-600 dark:text-red-400"
                                    >Disclaimer:
                                </span>
                                {{
                                    siteStore.settings.price_warning_message ||
                                    "Prices shown may not always be updated in real-time. The store is not responsible for any discrepancies; please confirm with the merchant before purchase."
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- WhatsApp CTA Button -->
            <div class="text-center">
                <div
                    class="bg-gradient-to-r from-green-500 to-emerald-600 p-1 rounded-2xl shadow-xl inline-block transform hover:scale-105 transition-transform duration-300"
                >
                    <button
                        @click="sendOrderViaWhatsApp"
                        :disabled="loading"
                        class="flex items-center space-x-3 px-8 py-4 bg-white dark:bg-gray-800 rounded-2xl text-green-700 dark:text-green-400 font-bold text-lg shadow-lg hover:shadow-xl transition-all duration-300"
                    >
                        <svg
                            class="w-6 h-6"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                        >
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"
                            />
                        </svg>
                        <span>Send Order via WhatsApp</span>
                        <span v-if="loading" class="ml-2">
                            <svg
                                class="animate-spin h-5 w-5 text-green-600"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                        </span>
                    </button>
                </div>

                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    You'll be redirected to WhatsApp to confirm your order with
                    the merchant.
                </p>
            </div>

            <!-- Back to Cart -->
            <div class="text-center">
                <router-link
                    to="/cart"
                    class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300"
                >
                    ← Back to Cart
                </router-link>
            </div>
        </div>

        <!-- Order Success Modal (Optional - if you want to keep it) -->
        <div
            v-if="orderCompleted"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-8 max-w-md w-full text-center shadow-2xl"
            >
                <div
                    class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900/30 mb-4"
                >
                    <svg
                        class="h-8 w-8 text-green-600 dark:text-green-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>
                </div>
                <h3
                    class="text-xl font-bold text-gray-900 dark:text-white mb-2"
                >
                    Order Sent!
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    Your order has been sent to the merchant via WhatsApp. They
                    will contact you shortly.
                </p>
                <button
                    @click="handleOrderSuccess"
                    class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors dark:bg-primary-700 dark:hover:bg-primary-800"
                >
                    View Order Details
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useCartStore } from "../stores/cart";
import { useOrderStore } from "../stores/orders";
import { useToast } from "vue-toastification";
import { useSiteStore } from "../stores/site";

const siteStore = useSiteStore();
const router = useRouter();
const cartStore = useCartStore();
const orderStore = useOrderStore();
const toast = useToast();

const loading = ref(false);
const cartLoading = ref(true);
const orderCompleted = ref(false);

// Load cart
onMounted(async () => {
    try {
        await cartStore.loadCart();
    } catch (error) {
        toast.error("Failed to load cart. Please try again.");
    } finally {
        cartLoading.value = false;
    }
});

// Computed Totals
const shippingCost = computed(() => {
    return cartStore.cartTotal > 50 ? 0 : 9.99;
});

const taxAmount = computed(() => {
    return (cartStore.cartTotal * (siteStore.settings.tax_rate || 0)) / 100;
});

const grandTotal = computed(() => {
    return cartStore.cartTotal + shippingCost.value + taxAmount.value;
});

// Format price
const formatPrice = (price) => {
    return `${parseFloat(price || 0).toFixed(2)} ${siteStore.settings.currency || "EGP"}`;
};

// WhatsApp Order Function
const sendOrderViaWhatsApp = async () => {
    if (loading.value) return;
    loading.value = true;

    try {
        const orderItems = cartStore.items.map((item) => {
            const orderItem = {
                product_detail_id: item.product_detail_id,
                quantity: item.quantity,
                price: item.product_detail?.final_price || 0,
                name: item.name,
                sku: item.sku,
            };
            return orderItem;
        });

        const orderData = {
            phone: "",
            shipping_address: "",
            shipping_phone: "",
            items: orderItems,
            notes: "Order placed via WhatsApp checkout",
            subtotal: cartStore.cartTotal,
            shipping_fee: shippingCost.value,
            tax: taxAmount.value,
            total: grandTotal.value,
            currency: siteStore.settings?.currency || "EGP",
        };

        const { success, data, error } = await orderStore.sendOrderViaWhatsApp(
            orderData,
            {
                whatsapp_number: siteStore.settings?.whatsapp_number,
                whatsapp_order_message:
                    siteStore.settings?.whatsapp_order_message,
                currency: siteStore.settings?.currency,
            },
        );
        if (!success) {
            console.error('[Checkout] Order processing failed:', error);
            throw new Error(error || "Failed to process order");
        }

        orderCompleted.value = true;

        // Open WhatsApp in a new tab after a short delay
        setTimeout(() => {
            if (data?.whatsappUrl) {
                window.open(data.whatsappUrl, "_blank");
            } else {
                console.warn('[Checkout] No WhatsApp URL provided in response');
            }
        }, 1000);
    } catch (error) {
        console.error('[Checkout] Order submission error:', {
            message: error.message,
            stack: error.stack,
            response: error.response?.data
        });
        
        const errorMessage = error.response?.data?.message || error.message || "Something went wrong. Please try again later.";
        console.error('[Checkout] Displaying error to user:', errorMessage);
        toast.error(errorMessage);
    } finally {
        loading.value = false;
    }
};

// Handle order success (after WhatsApp)
const handleOrderSuccess = async () => {
    await cartStore.clearCart();
    orderCompleted.value = false;
    router.push("/orders");
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>