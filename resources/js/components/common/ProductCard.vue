<template>
    <div
        v-if="product"
        class="product-card h-full flex flex-col bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-200 dark:bg-gray-800 dark:border-gray-700 dark:hover:shadow-gray-700/20"
    >
        <!-- Product Image -->
        <div
            class="relative w-full h-48 md:h-56 lg:h-60 bg-gray-100 overflow-hidden dark:bg-gray-700"
        >
            <router-link
                v-if="product.id"
                :to="`/products/${product.id}`"
                class="absolute inset-0 z-10"
                aria-label="View product details"
            ></router-link>
            <img
                v-if="productImage"
                :src="productImage"
                :alt="product?.name || 'Product Image'"
                class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                @error="handleImageError"
            />
            <!-- Wishlist Button -->
            <button
                v-if="authStore.isAuthenticated"
                @click.stop="toggleWishlist"
                class="absolute top-2 right-2 p-2 bg-white rounded-full shadow-md hover:shadow-lg transition z-20 dark:bg-gray-700 dark:hover:bg-gray-600"
                :class="{
                    'text-red-500': isInWishlist,
                    'text-gray-400 dark:text-gray-300': !isInWishlist,
                }"
            >
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
                    />
                </svg>
            </button>
            <!-- Sale Badge -->
            <div
                v-if="product?.discount_percentage > 0"
                class="absolute top-2 left-2 bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded"
            >
                -{{ product.discount_percentage }}%
            </div>

            <!-- Stock Status -->
            <div
                v-if="product && !product.in_stock"
                class="absolute top-2 left-2 bg-gray-800 text-white text-xs font-semibold px-2 py-1 rounded dark:bg-gray-600"
            >
                Out of Stock
            </div>
        </div>

        <!-- Product Info -->
        <div class="p-4 flex flex-col flex-1">
            <!-- Category -->
            <span
                v-if="firstCategoryName"
                class="text-xs text-gray-500 mb-1 dark:text-gray-400"
            >
                {{ firstCategoryName }}
            </span>

            <!-- Product Name -->
            <h3
                class="text-sm md:text-base font-medium text-gray-900 mb-1 line-clamp-2 dark:text-white"
            >
                <router-link
                    v-if="product.id"
                    :to="`/products/${product.id}`"
                    class="hover:text-primary-600 transition-colors dark:hover:text-primary-400"
                >
                    {{ product?.name || "No Name" }}
                </router-link>
            </h3>

            <!-- Description -->
            <p
                v-if="product?.description"
                class="text-xs md:text-sm text-gray-600 mb-2 line-clamp-2 dark:text-gray-300"
            >
                <router-link
                    v-if="product.id"
                    :to="`/products/${product.id}`"
                    class="hover:text-primary-600 transition-colors dark:hover:text-primary-400"
                >
                    {{ product.description }}
                </router-link>
            </p>

            <!-- Rating -->
            <div v-if="product?.rating > 0" class="flex items-center mb-2">
                <div class="flex items-center">
                    <svg
                        v-for="star in 5"
                        :key="star"
                        class="h-4 w-4 md:h-5 md:w-5"
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
                <span
                    class="ml-2 text-xs md:text-sm text-gray-600 dark:text-gray-400"
                    >({{ product.reviews_count || 0 }})</span
                >
            </div>

            <!-- Price -->
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2">
                    <span
                        class="text-sm md:text-base font-bold text-gray-900 dark:text-white"
                        >{{ product?.price || 0 }}
                        {{ siteStore.settings.currency }}</span
                    >
                    <span
                        v-if="product?.discount_percentage > 0"
                        class="text-xs md:text-sm text-gray-500 line-through dark:text-gray-400"
                        >{{ product?.original_price || 0 }}
                        {{ siteStore.settings.currency }}</span
                    >
                </div>
            </div>

            <!-- Add to Cart Button -->
            <button
                v-if="
                    siteStore.settings &&
                    !siteStore.settings.orders_via_whatsapp_only
                "
                @click.stop="addToCart"
                :disabled="cartStore.loading || !product?.in_stock"
                class="mt-auto w-full py-2 px-4 rounded-md transition-colors flex items-center justify-center text-sm md:text-base"
                :class="
                    product?.in_stock
                        ? 'bg-primary-600 text-white hover:bg-primary-700 dark:bg-primary-700 dark:hover:bg-primary-800'
                        : 'bg-gray-300 text-gray-500 cursor-not-allowed dark:bg-gray-600 dark:text-gray-300'
                "
            >
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
                <svg
                    v-else
                    class="h-4 w-4 mr-2"
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
                    cartStore.loading
                        ? "Adding..."
                        : product?.in_stock
                          ? "Add to Cart"
                          : "Out of Stock"
                }}
            </button>
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

const product = props.product;

// Compute product image safely
const productImage = computed(() => {
    if (product?.main_image_url) return product.main_image_url;
    if (product?.gallery_images?.length) return product.gallery_images[0];
    return "/images/placeholder-product.png";
});

// First category name safely
const firstCategoryName = computed(() => {
    return product?.categories?.[0]?.name || "";
});

// Handle image load error
const handleImageError = (event) => {
    event.target.src = productImage.value;
};

// Add product to cart
const addToCart = async () => {
    if (!product?.in_stock) {
        toast.warning("This product is currently out of stock");
        return;
    } else if (product.details && product.block_direct_addition) {
        toast.warning(
            "This product has more options. Please view the product details page to select options.",
        );
        return;
    }
    try {
        await cartStore.addToCart(product.details[0].id, 1);
    } catch (error) {
        toast.error("An unexpected error occurred");
    }
};

// Check if product is in wishlist
const isInWishlist = computed(() => {
    if (!authStore.isAuthenticated || !product) return false;
    return wishlistStore.isInWishlist(product.id);
});

// Toggle wishlist status
const toggleWishlist = async () => {
    if (!authStore.isAuthenticated) {
        toast.info("Please log in to use the wishlist feature.");
        return;
    }

    try {
        if (wishlistStore.categories.length === 0) {
            await wishlistStore.initializeWishlist();
        }

        if (isInWishlist.value) {
            const item = wishlistStore.getItemByProductId(product.id);
            if (item) {
                await wishlistStore.removeFromWishlist(item.id);
            }
        } else {
            await wishlistStore.addToWishlist(product.id);
        }
    } catch (error) {
        console.error("Wishlist toggle error:", error);
        toast.error(error.message || "Failed to update wishlist");
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

.product-card {
    min-height: 380px;
    display: flex;
    flex-direction: column;
}
</style>
