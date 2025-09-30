<template>
    <div
        class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200 dark:bg-gray-800 dark:border-gray-700 dark:hover:shadow-gray-700/20"
    >
        <div class="flex space-x-4">
            <!-- Product Image -->
            <div class="flex-shrink-0">
                <router-link :to="`/products/${product.id}`">
                    <img
                        :src="productImage"
                        :alt="product.name"
                        class="w-24 h-24 object-cover rounded-md"
                        @error="handleImageError"
                    />
                </router-link>
            </div>

            <!-- Product Info -->
            <div class="flex-1 min-w-0 relative">
                <!-- Category -->
                <div
                    v-if="product.categories && product.categories.length > 0"
                    class="mb-1"
                >
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{
                        product.categories[0].name
                    }}</span>
                </div>

                <!-- Product Name -->
                <h3
                    class="text-lg font-medium text-gray-900 mb-2 dark:text-white"
                >
                    <router-link
                        :to="`/products/${product.id}`"
                        class="hover:text-primary-600 transition-colors dark:hover:text-primary-400"
                    >
                        {{ product.name }}
                    </router-link>
                </h3>

                <!-- Product Description -->
                <p
                    v-if="product.description"
                    class="text-sm text-gray-600 mb-3 line-clamp-2 dark:text-gray-300"
                >
                    {{ product.description }}
                </p>

                <!-- Rating -->
                <div v-if="product.rating" class="flex items-center mb-3">
                    <div class="flex items-center">
                        <svg
                            v-for="star in 5"
                            :key="star"
                            class="h-4 w-4"
                            :class="
                                star <= Math.round(product.rating)
                                    ? 'text-yellow-400'
                                    : 'text-gray-300 dark:text-gray-600'
                            "
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                            />
                        </svg>
                    </div>
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400"
                        >({{ product.reviews_count || 0 }})</span
                    >
                </div>

                <!-- Price and Actions -->
                <div class="flex items-center justify-between">
                    <!-- Price -->
                    <div class="flex items-center space-x-2">
                        <span
                            class="text-lg font-bold text-gray-900 dark:text-white"
                            >{{ displayPrice }}
                            {{ siteStore.settings.currency }}</span
                        >
                        <span
                            v-if="product.discount_percentage"
                            class="text-sm text-gray-500 line-through dark:text-gray-400"
                        >
                            {{ product.original_price }}
                            {{ siteStore.settings.currency }}
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-2 relative z-20">
                        <!-- Wishlist Button -->
                        <button
                            v-if="authStore.isAuthenticated"
                            @click.stop="toggleWishlist"
                            class="p-2 text-gray-400 hover:text-red-500 transition-colors dark:text-gray-300 dark:hover:text-red-400"
                            :class="{
                                'text-red-500 dark:text-red-400': isInWishlist,
                            }"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
                                />
                            </svg>
                        </button>

                        <!-- Add to Cart Button -->
                        <button
                            v-if="
                                siteStore.settings &&
                                !siteStore.settings.orders_via_whatsapp_only
                            "
                            @click.stop="addToCart"
                            :disabled="cartStore.loading"
                            class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center dark:bg-primary-700 dark:hover:bg-primary-800"
                        >
                            <!-- Loading Spinner -->
                            <svg
                                v-if="cartStore.loading"
                                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
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
                            <!-- Cart Icon -->
                            <svg
                                v-else
                                class="h-4 w-4 mr-1"
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
                            {{
                                cartStore.loading ? "Adding..." : "Add to Cart"
                            }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { useAuthStore } from "../../stores/auth";
import { useCartStore } from "../../stores/cart";
import { useWishlistStore } from "../../stores/wishlist";
import { useSiteStore } from "../../stores/site";
import { useToast } from "vue-toastification";

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
});

const authStore = useAuthStore();
const siteStore = useSiteStore();
const cartStore = useCartStore();
const wishlistStore = useWishlistStore();
const toast = useToast();

// Compute product image (use main image or fallback to gallery)
const productImage = computed(() => {
    return (
        props.product.main_image_url ||
        props.product.gallery_images?.[0] ||
        "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIwIiBoZWlnaHQ9IjMyMCIgdmlld0JveD0iMCAwIDMyMCAzMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjMyMCIgaGVpZ2h0PSIzMjAiIGZpbGw9IiNlZWUiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZG9taW5hbnQtYmFzZWxpbmU9ImNlbnRyYWwiIGZvbnQtc2l6ZT0iMjAiIGZpbGw9IiNjY2MiPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg=="
    );
});

// Handle image error by using fallback
const handleImageError = (event) => {
    event.target.src = productImage.value;
};

// Compute display price (discount or normal)
const displayPrice = computed(() => {
    return props.product.price || props.product.original_price;
});

// Check if product is in wishlist
const isInWishlist = computed(() => {
    if (!authStore.isAuthenticated) return false;
    return wishlistStore.isInWishlist(props.product.id);
});

// Add product to cart
const addToCart = async () => {
    if (!props.product.in_stock) {
        toast.warning("This product is currently out of stock");
        return;
    } else if (product.details && product.block_direct_addition) {
        toast.warning(
            "This product has more options. Please view the product details page to select options.",
        );
        return;
    }

    try {
        await cartStore.addToCart(props.product.id, 1);
        // toast.success('Added to cart')
    } catch (error) {
        // toast.error('An unexpected error occurred')
    }
};

// Toggle wishlist (add/remove)
const toggleWishlist = async () => {
    if (!authStore.isAuthenticated) {
        toast.info("Please log in to use the wishlist feature.");
        return;
    }

    try {
        if (isInWishlist.value) {
            const item = wishlistStore.getItemByProductId(props.product.id);
            if (item) {
                const result = await wishlistStore.removeFromWishlist(item.id);
                if (result.success) {
                    //   toast.success('Removed from wishlist')
                }
            }
        } else {
            const result = await wishlistStore.addToWishlist(props.product.id);
            if (result.success) {
                // toast.success('Added to wishlist')
            }
        }
    } catch (error) {
        // toast.error('Failed to update wishlist')
    }
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
