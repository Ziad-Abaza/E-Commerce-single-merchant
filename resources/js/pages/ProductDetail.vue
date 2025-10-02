<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="container py-8">
            <!-- Loading State -->
            <div v-if="productStore.loading" class="flex justify-center py-12">
                <div
                    class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 dark:border-primary-500"
                ></div>
            </div>

            <!-- Not Found -->
            <div
                v-else-if="!productStore.currentProduct"
                class="text-center py-12"
            >
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Product not found
                </h1>
                <p class="text-gray-600 mt-2 dark:text-gray-300">
                    The product you're looking for doesn't exist.
                </p>
                <router-link
                    to="/products"
                    class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 dark:bg-primary-700 dark:hover:bg-primary-800"
                >
                    View All Products
                </router-link>
            </div>

            <!-- Product Detail -->
            <div
                v-else
                class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden dark:bg-gray-800 dark:border-gray-700"
            >
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-6 md:p-8">
                    <!-- Product Images -->
                    <div class="space-y-4">
                        <!-- Main Image -->
                        <div
                            class="aspect-w-4 aspect-h-3 bg-gray-100 rounded-lg overflow-hidden dark:bg-gray-700"
                        >
                            <img
                                :src="selectedImage || mainImageUrl"
                                :alt="
                                    productStore.currentProduct?.name ??
                                    'Product Image'
                                "
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
                                class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded-md overflow-hidden border-2 hover:border-primary-500 transition-colors dark:bg-gray-600 dark:border-gray-500"
                                :class="{
                                    'border-primary-500 dark:border-primary-400':
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
                            class="flex flex-wrap items-center text-sm text-gray-500 dark:text-gray-400"
                            aria-label="Breadcrumb"
                        >
                            <router-link
                                to="/"
                                class="hover:text-gray-700 dark:hover:text-gray-300"
                                >Home</router-link
                            >
                            <span class="mx-2">/</span>
                            <router-link
                                to="/products"
                                class="hover:text-gray-700 dark:hover:text-gray-300"
                                >Products</router-link
                            >
                            <span class="mx-2">/</span>
                            <span class="text-gray-900 dark:text-white">
                                {{
                                    productStore.currentProduct?.name ??
                                    "Unknown Product"
                                }}
                            </span>
                        </nav>

                        <!-- Brand & Name -->
                        <div>
                            <p
                                class="text-sm text-gray-500 uppercase tracking-wide dark:text-gray-400"
                            >
                                {{
                                    productStore.currentProduct?.brand ??
                                    "Unknown Brand"
                                }}
                            </p>
                            <h1
                                class="text-2xl md:text-3xl font-bold text-gray-900 mt-1 dark:text-white"
                            >
                                {{
                                    productStore.currentProduct?.name ??
                                    "Unknown Product"
                                }}
                            </h1>
                        </div>

                        <!-- Short Description -->
                        <p
                            class="text-gray-600 leading-relaxed dark:text-gray-300"
                        >
                            {{
                                productStore.currentProduct
                                    ?.short_description ??
                                "No description available."
                            }}
                        </p>

                        <!-- Price Section -->
                        <div
                            class="flex flex-col sm:flex-row sm:items-center sm:space-x-4"
                        >
                            <div
                                class="flex items-baseline space-x-2"
                                v-if="finalPrice"
                            >
                                <span
                                    class="text-3xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ formatPrice(finalPrice) }}
                                    {{ siteStore.settings?.currency ?? "EGP" }}
                                </span>
                                <span
                                    v-if="discountPercentage > 0"
                                    class="text-xl text-gray-500 line-through dark:text-gray-400"
                                >
                                    {{ formatPrice(originalPrice) }}
                                    {{ siteStore.settings?.currency ?? "EGP" }}
                                </span>
                            </div>
                            <span
                                v-if="discountPercentage > 0"
                                class="mt-2 sm:mt-0 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300"
                            >
                                Save {{ discountPercentage }}%
                            </span>
                        </div>

                        <!-- Product Attributes -->
                        <div
                            v-if="selectedDetail?.attributes?.length"
                            class="space-y-4 pt-2"
                        >
                            <div
                                v-for="attribute in selectedDetail.attributes"
                                :key="attribute.id"
                                class="border-b border-gray-100 pb-4 last:border-0 last:pb-0 dark:border-gray-700"
                            >
                                <div class="flex flex-wrap items-start">
                                    <dt
                                        class="w-1/3 text-sm font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        {{ attribute.name }}:
                                    </dt>
                                    <dd
                                        class="w-2/3 text-sm text-gray-900 dark:text-gray-200"
                                    >
                                        {{ attribute.value }}
                                    </dd>
                                </div>
                            </div>
                        </div>

                        <!-- Product Details Selection -->
                        <div v-if="productDetails.length > 1" class="pt-4">
                            <h4
                                class="text-sm font-medium text-gray-500 mb-2 dark:text-gray-400"
                            >
                                Select Option
                            </h4>
                            <div class="flex flex-wrap gap-3">
                                <label
                                    v-for="detail in productDetails"
                                    :key="detail.id"
                                    class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer transition-colors dark:border-gray-600"
                                    :class="{
                                        'bg-primary-50 border-primary-500 dark:bg-primary-900/20 dark:border-primary-500':
                                            selectedDetail &&
                                            selectedDetail.id === detail.id,
                                        'bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700':
                                            !selectedDetail ||
                                            selectedDetail.id !== detail.id,
                                    }"
                                >
                                    <input
                                        type="radio"
                                        :value="detail"
                                        v-model="selectedDetail"
                                        name="product-detail"
                                        class="form-radio h-4 w-4 text-primary-600 border-gray-300 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-offset-gray-800"
                                    />
                                    <span
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    >
                                        <template v-if="detail.size">{{
                                            detail.size
                                        }}</template>
                                        <template
                                            v-if="detail.size && detail.color"
                                        >
                                            -
                                        </template>
                                        <template v-if="detail.color">{{
                                            detail.color
                                        }}</template>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Stock Status -->
                        <div class="flex items-center space-x-2">
                            <span
                                v-if="stockQuantity > 10"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300"
                            >
                                In Stock
                            </span>
                            <span
                                v-else-if="stockQuantity > 0"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300"
                            >
                                Only {{ stockQuantity }} left
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300"
                            >
                                Out of Stock
                            </span>
                            <span
                                class="text-sm text-gray-500 ml-2 dark:text-gray-400"
                            >
                                SKU: {{ sku ?? "N/A" }}
                            </span>
                        </div>

                        <!-- Quantity Selector -->
                        <div
                            class="flex items-center space-x-4"
                            v-if="
                                siteStore.settings &&
                                !siteStore.settings.orders_via_whatsapp_only
                            "
                        >
                            <label
                                class="text-sm font-medium text-gray-700 whitespace-nowrap dark:text-gray-300"
                                >Quantity:</label
                            >
                            <div class="flex items-center space-x-2">
                                <button
                                    @click="decreaseQuantity"
                                    :disabled="quantity <= 1"
                                    class="p-1.5 rounded-md border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors dark:border-gray-600 dark:hover:bg-gray-700 dark:bg-gray-700"
                                >
                                    <svg
                                        class="h-4 w-4 dark:text-gray-300"
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
                                    class="w-12 text-center text-sm font-medium text-gray-900 dark:text-white"
                                    >{{ quantity }}
                                </span>
                                <button
                                    @click="increaseQuantity"
                                    :disabled="quantity >= maxQuantity"
                                    class="p-1.5 rounded-md border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors dark:border-gray-600 dark:hover:bg-gray-700 dark:bg-gray-700"
                                >
                                    <svg
                                        class="h-4 w-4 dark:text-gray-300"
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
                                v-if="
                                    siteStore.settings &&
                                    !siteStore.settings.orders_via_whatsapp_only
                                "
                                @click="addToCart"
                                :disabled="
                                    isAddingToCart ||
                                    !productStore.currentProduct ||
                                    stockQuantity === 0
                                "
                                class="flex-1 bg-primary-600 text-white py-3 px-6 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center dark:bg-primary-700 dark:hover:bg-primary-800"
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
                                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors flex items-center justify-center dark:border-gray-600 dark:text-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600"
                                :class="{
                                    'text-red-600 border-red-300 bg-red-50 hover:bg-red-100 dark:text-red-400 dark:border-red-600 dark:bg-red-900/30 dark:hover:bg-red-900/50':
                                        isInWishlist,
                                    'text-gray-700 border-gray-300 bg-white hover:bg-gray-50 dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600':
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

                        <!-- WhatsApp Order Button -->
                        <div class="mt-4">
                            <button
                                v-if="productStore.currentProduct"
                                @click="openWhatsApp"
                                class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors flex items-center justify-center dark:bg-green-700 dark:hover:bg-green-800"
                            >
                                <svg
                                    class="h-5 w-5 mr-2"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"
                                    />
                                </svg>
                                Order via WhatsApp
                            </button>
                        </div>

                        <!-- Categories -->
                        <div
                            v-if="
                                productStore.currentProduct?.categories?.length
                            "
                            class="pt-4 border-t border-gray-200 dark:border-gray-700"
                        >
                            <h4
                                class="text-sm font-medium text-gray-500 mb-2 dark:text-gray-400"
                            >
                                Categories
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="category in productStore
                                        .currentProduct.categories"
                                    :key="category.id"
                                    class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full dark:bg-gray-700 dark:text-gray-300"
                                >
                                    {{ category.name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Full Description -->
                <div
                    v-if="productStore.currentProduct?.description"
                    class="border-t border-gray-200 p-6 md:p-8 dark:border-gray-700"
                >
                    <h3
                        class="text-xl font-semibold text-gray-900 mb-4 dark:text-white"
                    >
                        Description
                    </h3>
                    <div
                        class="prose prose-gray max-w-none dark:prose-invert"
                        v-html="productStore.currentProduct.description"
                    ></div>
                </div>

                <!-- Reviews Section -->
                <div
                    class="border-t border-gray-200 p-6 md:p-8 dark:border-gray-700"
                >
                    <!-- Review List -->
                    <ReviewList
                        v-if="productStore.currentProduct?.id"
                        :product-id="productStore.currentProduct.id"
                    />

                    <!-- Write Review Button -->
                    <div class="mt-6">
                        <button
                            @click="openReviewForm"
                            class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:bg-primary-700 dark:hover:bg-primary-800"
                        >
                            Write a Review
                        </button>
                    </div>

                    <!-- Review Form (Toggled) -->
                    <div v-if="showReviewForm" class="mt-6">
                        <ReviewForm
                            v-if="productStore.currentProduct?.id"
                            :product-id="productStore.currentProduct.id"
                            @submitted="handleReviewSubmitted"
                            @cancel="closeReviewForm"
                        />
                    </div>
                </div>

                <!-- Related Products Slider -->
                <div
                    v-if="productStore.currentProduct?.related_products?.length"
                    class="border-t border-gray-200 p-6 md:p-8 dark:border-gray-700"
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
import { useRoute, onBeforeRouteUpdate } from "vue-router";
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
const selectedDetail = ref(null);
const isAddingToCart = ref(false);
const isInWishlist = ref(false);
const showReviewForm = ref(false);

// Re-fetch product when route changes
onBeforeRouteUpdate(async (to, from) => {
    if (to.params.id !== from.params.id) {
        await fetchProduct(to.params.id);
    }
});

const productDetails = computed(
    () => productStore.currentProduct?.details || [],
);

const handleImageError = (event) => {
    const placeholder = "/images/placeholder-product.jpg";
    if (event.target.src !== window.location.origin + placeholder) {
        event.target.src = placeholder;
    }
};

const mainImageUrl = computed(() => {
    return (
        selectedDetail.value?.main_image ||
        productStore.currentProduct?.main_image_url ||
        "/images/placeholder-product.jpg"
    );
});

const galleryImages = computed(() => {
    const detailImages = selectedDetail.value?.images_url || [];
    if (detailImages.length > 0) return detailImages;

    const productImages = productStore.currentProduct?.gallery_images || [];
    if (productImages.length > 0) return productImages;

    const mainImage = productStore.currentProduct?.main_image_url;
    return mainImage ? [mainImage] : ["/images/placeholder-product.jpg"];
});

const finalPrice = computed(
    () =>
        selectedDetail.value?.final_price ??
        productStore.currentProduct?.final_price,
);
const originalPrice = computed(
    () => selectedDetail.value?.price ?? productStore.currentProduct?.price,
);
const discountPercentage = computed(
    () =>
        selectedDetail.value?.discount_percentage ??
        productStore.currentProduct?.discount_percentage,
);
const stockQuantity = computed(
    () =>
        selectedDetail.value?.stock_quantity ??
        productStore.currentProduct?.stock_quantity,
);
const sku = computed(
    () => selectedDetail.value?.sku_variant ?? productStore.currentProduct?.sku,
);
const weight = computed(
    () => selectedDetail.value?.weight ?? productStore.currentProduct?.weight,
);
const dimensions = computed(
    () =>
        selectedDetail.value?.dimensions ??
        productStore.currentProduct?.dimensions,
);
const material = computed(
    () =>
        selectedDetail.value?.material ?? productStore.currentProduct?.material,
);
const volume = computed(
    () => selectedDetail.value?.volume ?? productStore.currentProduct?.volume,
);
const origin = computed(
    () => selectedDetail.value?.origin ?? productStore.currentProduct?.origin,
);
const quality = computed(
    () => selectedDetail.value?.quality ?? productStore.currentProduct?.quality,
);
const packaging = computed(
    () =>
        selectedDetail.value?.packaging ??
        productStore.currentProduct?.packaging,
);
const barcode = computed(
    () => selectedDetail.value?.barcode ?? productStore.currentProduct?.barcode,
);

const maxQuantity = computed(() => stockQuantity.value || 0);

const formatPrice = (price) => parseFloat(price || 0).toFixed(2);

const selectImage = (imageUrl) => {
    selectedImage.value = imageUrl;
};

const thumbFallback = (index) => {
    const placeholder = "/images/placeholder-product.jpg";
    return index < galleryImages.value.length
        ? galleryImages.value[index]
        : placeholder;
};

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

const addToCart = async () => {
    if (
        isAddingToCart.value ||
        !productStore.currentProduct ||
        stockQuantity.value === 0
    )
        return;

    isAddingToCart.value = true;
    try {
        const detailId = selectedDetail.value
            ? selectedDetail.value.id
            : productDetails.value[0]?.id;
        if (!detailId) {
            toast.error("Please select a product option.");
            return;
        }

        await cartStore.addToCart(detailId, quantity.value);
        toast.success("Product added to cart!");
    } catch (error) {
        console.error("Add to cart error:", error);
        toast.error(
            error.response?.data?.message || "Failed to add product to cart",
        );
    } finally {
        isAddingToCart.value = false;
    }
};

const toggleWishlist = async () => {
    if (!productStore.currentProduct) return;

    try {
        if (wishlistStore.isAuthenticated && !wishlistStore.defaultCategory) {
            await wishlistStore.createDefaultCategory();
        }

        const productId = productStore.currentProduct.id;
        if (isInWishlist.value) {
            const item = wishlistStore.getItemByProductId(productId);
            if (item) {
                await wishlistStore.removeFromWishlist(item.id);
                toast.success("Removed from wishlist");
            }
        } else {
            await wishlistStore.addToWishlist(productId);
            toast.success("Added to wishlist");
        }
        isInWishlist.value = !isInWishlist.value;
    } catch (error) {
        console.error("Wishlist toggle error:", error);
        toast.error("Failed to update wishlist");
    }
};

const openWhatsApp = () => {
    if (!productStore.currentProduct) return;

    const phone = siteStore.settings?.whatsapp_number || "";
    const defaultMessage =
        siteStore.settings?.whatsapp_message ||
        "I would like to order the product:";
    const productName = selectedDetail.value
        ? `${productStore.currentProduct.name} (${selectedDetail.value.size || selectedDetail.value.color})`
        : productStore.currentProduct.name;

    const message = `${defaultMessage} ${productName}\n\n${window.location.href}`;
    window.open(
        `https://wa.me/${phone}?text=${encodeURIComponent(message)}`,
        "_blank",
    );
};

const openReviewForm = () => {
    if (!productStore.currentProduct) {
        toast.error("Cannot write review for this product");
        return;
    }
    showReviewForm.value = true;
};

const closeReviewForm = () => {
    showReviewForm.value = false;
};

const handleReviewSubmitted = (newReview) => {
    closeReviewForm();
    if (productStore.currentProduct?.reviews) {
        productStore.currentProduct.reviews.push(newReview);
    }
};

const fetchProduct = async (id) => {
    if (!id) return;
    try {
        await productStore.getProduct(id);

          if (productDetails.value.length > 0) {
            selectedDetail.value = productDetails.value[0];
        }

        selectedImage.value =
            galleryImages.value[0] || "/images/placeholder-product.jpg";

        if (wishlistStore.isAuthenticated && productStore.currentProduct) {
            isInWishlist.value = wishlistStore.isInWishlist(
                productStore.currentProduct.id,
            );

            if (!wishlistStore.defaultCategory) {
                await wishlistStore.createDefaultCategory();
            }
        } else {
            isInWishlist.value = false;
        }
    } catch (error) {
        console.error("Failed to fetch product:", error);
    }
};

onMounted(async () => {
    await fetchProduct(route.params.id);
});

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
    { immediate: true },
);

watch(selectedDetail, (newDetail) => {
    if (newDetail) {
        quantity.value = 1; // Reset quantity
        selectedImage.value =
            newDetail.main_image ||
            galleryImages.value[0] ||
            "/images/placeholder-product.jpg";
    }
});
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
