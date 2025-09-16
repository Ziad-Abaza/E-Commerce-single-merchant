<template>
    <div class="min-h-screen bg-gray-50">
        <div class="container py-8">
            <!-- Loading State -->
            <div v-if="productStore.loading" class="flex justify-center py-12">
                <div
                    class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"
                ></div>
            </div>
            <!-- Not Found -->
            <div
                v-else-if="!productStore.currentProduct"
                class="text-center py-12"
            >
                <h1 class="text-2xl font-bold text-gray-900">
                    Product not found
                </h1>
                <p class="text-gray-600 mt-2">
                    The product you're looking for doesn't exist.
                </p>
                <router-link
                    to="/products"
                    class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
                >
                    View All Products
                </router-link>
            </div>
            <!-- Product Detail -->
            <div
                v-else
                class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
            >
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-6 md:p-8">
                    <!-- Product Images -->
                    <div class="space-y-4">
                        <!-- Main Image -->
                        <div
                            class="aspect-w-4 aspect-h-3 bg-gray-100 rounded-lg overflow-hidden"
                        >
                            <img
                                :src="selectedImage || mainImageUrl"
                                :alt="productStore.currentProduct.name"
                                class="w-full h-full object-contain"
                                @error="handleImageError"
                            />
                        </div>
                        <!-- Gallery Thumbnails -->
                        <div
                            v-if="galleryImages.length > 1"
                            class="flex space-x-2 overflow-x-auto py-2 hide-scrollbar"
                        >
                            <button
                                v-for="(thumb, index) in galleryImages"
                                :key="index"
                                @click="selectImage(thumb)"
                                class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded-md overflow-hidden border-2 hover:border-primary-500 transition-colors"
                                :class="{
                                    'border-primary-500':
                                        selectedImage === thumb,
                                }"
                            >
                                <img
                                    :src="thumb"
                                    :alt="`Thumbnail ${index + 1}`"
                                    class="w-full h-full object-cover"
                                    @error="thumbFallback(index)"
                                />
                            </button>
                        </div>
                    </div>
                    <!-- Product Info -->
                    <div class="space-y-6">
                        <!-- Breadcrumb -->
                        <nav
                            class="flex flex-wrap items-center text-sm text-gray-500"
                            aria-label="Breadcrumb"
                        >
                            <router-link to="/" class="hover:text-gray-700"
                                >Home</router-link
                            >
                            <span class="mx-2">/</span>
                            <router-link
                                to="/products"
                                class="hover:text-gray-700"
                                >Products</router-link
                            >
                            <span class="mx-2">/</span>
                            <span class="text-gray-900">{{
                                productStore.currentProduct.name
                            }}</span>
                        </nav>
                        <!-- Brand & Name -->
                        <div>
                            <p
                                class="text-sm text-gray-500 uppercase tracking-wide"
                            >
                                {{ productStore.currentProduct.brand }}
                            </p>
                            <h1
                                class="text-2xl md:text-3xl font-bold text-gray-900 mt-1"
                            >
                                {{ productStore.currentProduct.name }}
                            </h1>
                        </div>
                        <!-- Short Description -->
                        <p class="text-gray-600 leading-relaxed">
                            {{ productStore.currentProduct.short_description }}
                        </p>
                        <!-- Price Section -->
                        <div
                            class="flex flex-col sm:flex-row sm:items-center sm:space-x-4"
                        >
                            <div class="flex items-baseline space-x-2">
                                <span class="text-3xl font-bold text-gray-900">
                                    {{
                                        formatPrice(
                                            productStore.currentProduct
                                                .final_price,
                                        )
                                    }} {{ siteStore.settings.currency }}
                                </span>
                                <span
                                    v-if="
                                        productStore.currentProduct
                                            .discount_percentage > 0
                                    "
                                    class="text-xl text-gray-500 line-through"
                                >
                                    {{
                                        formatPrice(
                                            productStore.currentProduct.price,
                                        )
                                    }} {{ siteStore.settings.currency }}
                                </span>
                            </div>
                            <span
                                v-if="
                                    productStore.currentProduct
                                        .discount_percentage > 0
                                "
                                class="mt-2 sm:mt-0 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                            >
                                Save
                                {{
                                    productStore.currentProduct
                                        .discount_percentage
                                }}%
                            </span>
                        </div>
                        <!-- Stock Status -->
                        <div class="flex items-center space-x-2">
                            <span
                                v-if="
                                    productStore.currentProduct.stock_quantity >
                                    10
                                "
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                            >
                                In Stock
                            </span>
                            <span
                                v-else-if="
                                    productStore.currentProduct.stock_quantity >
                                    0
                                "
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                            >
                                Only
                                {{
                                    productStore.currentProduct.stock_quantity
                                }}
                                left
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"
                            >
                                Out of Stock
                            </span>
                            <span class="text-sm text-gray-500 ml-2">
                                SKU: {{ productStore.currentProduct.sku }}
                            </span>
                        </div>
                        <!-- Quantity Selector -->
                        <div class="flex items-center space-x-4">
                            <label
                                class="text-sm font-medium text-gray-700 whitespace-nowrap"
                                >Quantity:</label
                            >
                            <div class="flex items-center space-x-2">
                                <button
                                    @click="decreaseQuantity"
                                    :disabled="quantity <= 1"
                                    class="p-1.5 rounded-md border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M20 12H4"
                                        />
                                    </svg>
                                </button>
                                <span
                                    class="w-12 text-center text-sm font-medium text-gray-900"
                                    >{{ quantity }}</span
                                >
                                <button
                                    @click="increaseQuantity"
                                    :disabled="quantity >= maxQuantity"
                                    class="p-1.5 rounded-md border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <!-- Action Buttons -->
                        <div
                            class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4"
                        >
                            <button
                                @click="addToCart"
                                :disabled="
                                    isAddingToCart ||
                                    productStore.currentProduct
                                        .stock_quantity === 0
                                "
                                class="flex-1 bg-primary-600 text-white py-3 px-6 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center"
                            >
                                <span
                                    v-if="isAddingToCart"
                                    class="flex items-center"
                                >
                                    <svg
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
                                    Adding...
                                </span>
                                <span v-else>Add to Cart</span>
                            </button>
                            <button
                                @click="toggleWishlist"
                                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors flex items-center justify-center"
                                :class="{
                                    'text-red-600 border-red-300 bg-red-50 hover:bg-red-100':
                                        isInWishlist,
                                    'text-gray-700 border-gray-300 bg-white hover:bg-gray-50':
                                        !isInWishlist,
                                }"
                            >
                                <svg
                                    class="h-5 w-5 mr-1"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        v-if="!isInWishlist"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                                    />
                                    <path
                                        v-else
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
                                    />
                                </svg>
                                {{
                                    isInWishlist
                                        ? "Remove from Wishlist"
                                        : "Add to Wishlist"
                                }}
                            </button>
                        </div>
                        <!-- Categories -->
                        <div
                            v-if="
                                productStore.currentProduct.categories?.length
                            "
                            class="pt-4 border-t border-gray-200"
                        >
                            <h4 class="text-sm font-medium text-gray-500 mb-2">
                                Categories
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="category in productStore
                                        .currentProduct.categories"
                                    :key="category.id"
                                    class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full"
                                >
                                    {{ category.name }}
                                </span>
                            </div>
                        </div>
                        <!-- Specifications -->
                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">
                                Specifications
                            </h4>
                            <dl
                                class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm"
                            >
                                <div v-if="productStore.currentProduct.weight">
                                    <dt class="font-medium text-gray-700">
                                        Weight
                                    </dt>
                                    <dd class="text-gray-600">
                                        {{ productStore.currentProduct.weight }}
                                    </dd>
                                </div>
                                <div
                                    v-if="
                                        productStore.currentProduct.dimensions
                                    "
                                >
                                    <dt class="font-medium text-gray-700">
                                        Dimensions
                                    </dt>
                                    <dd class="text-gray-600">
                                        {{
                                            productStore.currentProduct
                                                .dimensions
                                        }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                <!-- Full Description -->
                <div
                    v-if="productStore.currentProduct.description"
                    class="border-t border-gray-200 p-6 md:p-8"
                >
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">
                        Description
                    </h3>
                    <div
                        class="prose prose-gray max-w-none"
                        v-html="productStore.currentProduct.description"
                    ></div>
                </div>

                <!-- Reviews Section -->
                <div class="border-t border-gray-200 p-6 md:p-8">
                    <!-- Review List -->
                    <ReviewList :product-id="productStore.currentProduct.id" />

                    <!-- Write Review Button -->
                    <div class="mt-6">
                        <button
                            @click="openReviewForm"
                            class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                        >
                            Write a Review
                        </button>
                    </div>

                    <!-- Review Form (Toggled) -->
                    <div v-if="showReviewForm" class="mt-6">
                        <ReviewForm
                            :product-id="productStore.currentProduct.id"
                            @submitted="handleReviewSubmitted"
                            @cancel="closeReviewForm"
                        />
                    </div>
                </div>

                <!-- Related Products Slider -->
                <div
                    v-if="productStore.currentProduct.related_products?.length"
                    class="border-t border-gray-200 p-6 md:p-8"
                >
                    <ProductSlider
                        :products="productStore.currentProduct.related_products"
                        title="You May Also Like"
                        :itemsDesktop="4"
                        :itemsTablet="3"
                        :itemsMobile="1.2"
                        :autoplay="false"
                        :showDots="true"
                        :showNavigation="true"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
import { useProductStore } from "../stores/products";
import { useCartStore } from "../stores/cart";
import { useWishlistStore } from "../stores/wishlist";
import { useToast } from "vue-toastification";
import ProductSlider from "../components/common/ProductSlider.vue";
import { useSiteStore } from "../stores/site";

import ReviewList from "../components/common/ReviewList.vue";
import ReviewForm from "../components/common/ReviewForm.vue";

const siteStore = useSiteStore();
const route = useRoute();
const productStore = useProductStore();
const cartStore = useCartStore();
const wishlistStore = useWishlistStore();
const toast = useToast();

const quantity = ref(1);
const selectedImage = ref("");
const isAddingToCart = ref(false);
const isInWishlist = ref(false);

const showReviewForm = ref(false);

// Computed: Max quantity based on stock
const maxQuantity = computed(() => {
    return productStore.currentProduct?.stock_quantity || 0;
});

const handleImageError = (event) => {
    const placeholder = "/public/images/placeholder-product.jpg";
    if (event.target.src !== window.location.origin + placeholder) {
        event.target.src = placeholder;
    }
};

// Computed: Main image URL (fallback if null)
const mainImageUrl = computed(() => {
    return (
        productStore.currentProduct?.main_image_url ||
        "/public/images/placeholder-product.jpg"
    );
});

// Computed: Gallery images (fallback to main image if empty)
const galleryImages = computed(() => {
    const images = productStore.currentProduct?.gallery_images || [];
    if (images.length > 0) return images;
    return productStore.currentProduct?.main_image_url
        ? [productStore.currentProduct.main_image_url]
        : ["/public/images/placeholder-product.jpg"];
});

// Format price to 2 decimals
const formatPrice = (price) => {
    return parseFloat(price || 0).toFixed(2);
};

// Select image from gallery
const selectImage = (imageUrl) => {
    selectedImage.value = imageUrl;
};

const thumbFallback = (index) => {
    // You can set a placeholder for thumbnail too if needed
};

// Quantity controls
const increaseQuantity = () => {
    if (quantity.value < maxQuantity.value) {
        quantity.value++;
    }
};

const decreaseQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
    }
};

// Add to cart
const addToCart = async () => {
    if (isAddingToCart.value || !productStore.currentProduct) return;
    isAddingToCart.value = true;
    try {
        await cartStore.addToCart(
            productStore.currentProduct.id,
            quantity.value,
        );
    } catch (error) {
        console.error("Add to cart error:", error);
    } finally {
        isAddingToCart.value = false;
    }
};

// Toggle wishlist
const toggleWishlist = async () => {
    if (!productStore.currentProduct) return;
    try {
        if (!wishlistStore.defaultCategory) {
            await wishlistStore.getDefaultCategory();
        }
        if (isInWishlist.value) {
            const item = wishlistStore.getItemByProductId(
                productStore.currentProduct.id,
            );
            if (item) {
                await wishlistStore.removeFromWishlist(item.id);
            }
        } else {
            await wishlistStore.addToWishlist(productStore.currentProduct.id);
        }
        isInWishlist.value = !isInWishlist.value;
    } catch (error) {
        toast.error("Failed to update wishlist");
    }
};

const openReviewForm = () => {
    showReviewForm.value = true;
};

const closeReviewForm = () => {
    showReviewForm.value = false;
};

const handleReviewSubmitted = (newReview) => {
    closeReviewForm();
    productStore.currentProduct.reviews.push(newReview);
    // refresh the reviews list
    productStore.currentProduct.reviews = [
        ...productStore.currentProduct.reviews,
    ];
};

// Initialize
onMounted(async () => {
    const productId = route.params.id;
    await productStore.getProduct(productId);
    selectedImage.value =
        galleryImages.value[0] || "/public/images/placeholder-product.jpg";
    await wishlistStore.initializeWishlist();
    if (wishlistStore.isAuthenticated && productStore.currentProduct) {
        isInWishlist.value = wishlistStore.isInWishlist(
            productStore.currentProduct.id,
        );
    }
    if (!wishlistStore.defaultCategory) {
        await wishlistStore.createDefaultCategory();
    }
});

// Watch for auth changes to update wishlist status
watch(
    () => wishlistStore.isAuthenticated,
    async (isAuth) => {
        if (isAuth && productStore.currentProduct) {
            isInWishlist.value = wishlistStore.isInWishlist(
                productStore.currentProduct.id,
            );
        } else {
            isInWishlist.value = false;
        }
    },
);
</script>
<style scoped>
.hide-scrollbar {
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
}
.hide-scrollbar::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}
</style>
