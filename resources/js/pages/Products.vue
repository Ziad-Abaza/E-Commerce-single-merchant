<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="container py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-4 dark:text-white">
                    All Products
                </h1>
                <p class="text-gray-600 dark:text-gray-300">
                    Discover our complete collection of products
                </p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Mobile Filter Button -->
                <div class="lg:hidden">
                    <button
                        @click="showFilters = !showFilters"
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm text-gray-700 font-medium flex items-center justify-center dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300"
                    >
                        <svg
                            class="w-5 h-5 mr-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
                            />
                        </svg>
                        Filters
                    </button>
                </div>

                <!-- Filters Sidebar (Desktop) / Modal (Mobile) -->
                <div
                    class="lg:w-64 lg:flex-shrink-0 transition-all duration-300"
                    :class="{
                        'fixed inset-0 z-50 bg-black bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75': !isDesktop,
                        hidden: !showFilters && !isDesktop,
                        block: isDesktop,
                    }"
                >
                    <div
                        class="bg-white rounded-lg shadow-lg border border-gray-200 p-6 w-full lg:w-auto lg:static lg:rounded-lg lg:shadow-sm dark:bg-gray-800 dark:border-gray-700"
                        :class="
                            !isDesktop
                                ? 'absolute top-0 right-0 bottom-0 w-80 overflow-y-auto'
                                : ''
                        "
                    >
                        <!-- Close button for mobile -->
                        <div class="lg:hidden flex justify-end mb-4">
                            <button
                                @click="showFilters = false"
                                class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <svg
                                    class="w-6 h-6 dark:text-gray-300"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>

                        <h3 class="text-lg font-semibold text-gray-900 mb-6 dark:text-white">
                            Filters
                        </h3>

                        <!-- Categories -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3 dark:text-white">
                                Categories
                            </h4>
                            <div
                                class="space-y-2 max-h-64 overflow-y-auto pr-2"
                            >
                                <label class="flex items-center">
                                    <input
                                        v-model="filters.category"
                                        type="radio"
                                        :value="null"
                                        @change="applyFilters"
                                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                                    />
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                        >All Categories</span
                                    >
                                </label>
                                <label
                                    v-for="category in productStore.categories"
                                    :key="category.id"
                                    class="flex items-center"
                                >
                                    <input
                                        v-model="filters.category"
                                        type="radio"
                                        :value="category.id"
                                        @change="applyFilters"
                                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                                    />
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{
                                        category.name
                                    }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3 dark:text-white">
                                Price Range
                            </h4>
                            <div class="space-y-3">
                                <div>
                                    <label
                                        class="block text-xs text-gray-500 mb-1 dark:text-gray-400"
                                        >Min Price</label
                                    >
                                    <input
                                        v-model.number="filters.minPrice"
                                        type="number"
                                        placeholder="0"
                                        @change="applyFilters"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="block text-xs text-gray-500 mb-1 dark:text-gray-400"
                                        >Max Price</label
                                    >
                                    <input
                                        v-model.number="filters.maxPrice"
                                        type="number"
                                        placeholder="1000"
                                        @change="applyFilters"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Sort -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3 dark:text-white">
                                Sort By
                            </h4>
                            <select
                                v-model="filters.sort"
                                @change="applyFilters"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                                <option value="created_at">Newest First</option>
                                <option value="name">Name A-Z</option>
                                <option value="price_asc">
                                    Price: Low to High
                                </option>
                                <option value="price_desc">
                                    Price: High to Low
                                </option>
                                <option value="rating">Highest Rated</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col space-y-3">
                            <button
                                @click="clearFilters"
                                class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                            >
                                Clear Filters
                            </button>

                            <!-- Apply button for mobile -->
                            <button
                                v-if="!isDesktop"
                                @click="showFilters = false"
                                class="w-full px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-primary-700 dark:hover:bg-primary-800"
                            >
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="flex-1">
                    <!-- Results Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Showing {{ productStore.pagination.total }} products
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500 dark:text-gray-400">View:</span>
                            <button
                                @click="viewMode = 'grid'"
                                :class="[
                                    'p-2 rounded-md',
                                    viewMode === 'grid'
                                        ? 'bg-primary-100 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400'
                                        : 'text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300',
                                ]"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                                    />
                                </svg>
                            </button>
                            <button
                                @click="viewMode = 'list'"
                                :class="[
                                    'p-2 rounded-md',
                                    viewMode === 'list'
                                        ? 'bg-primary-100 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400'
                                        : 'text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300',
                                ]"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div v-if="productStore.loading" class="product-grid">
                        <div v-for="n in 12" :key="n" class="animate-pulse">
                            <div
                                class="bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700"
                            >
                                <div
                                    class="h-48 bg-gray-200 rounded-t-lg dark:bg-gray-700"
                                ></div>
                                <div class="p-4">
                                    <div
                                        class="bg-gray-200 h-4 rounded w-3/4 mb-2 dark:bg-gray-600"
                                    ></div>
                                    <div
                                        class="bg-gray-200 h-4 rounded w-1/2 dark:bg-gray-600"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div v-else-if="viewMode === 'grid'" class="product-grid">
                        <ProductCard
                            v-for="product in productStore.products"
                            :key="product.id"
                            :product="product"
                        />
                    </div>

                    <!-- Products List -->
                    <div v-else class="space-y-4">
                        <ProductListItem
                            v-for="product in productStore.products"
                            :key="product.id"
                            :product="product"
                        />
                    </div>

                    <!-- Empty State -->
                    <div
                        v-if="
                            !productStore.loading &&
                            productStore.products.length === 0
                        "
                        class="text-center py-12"
                    >
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                            />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                            No products found
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Try adjusting your filters or search terms.
                        </p>
                        <div class="mt-6">
                            <button
                                @click="clearFilters"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-primary-700 dark:hover:bg-primary-800"
                            >
                                Clear Filters
                            </button>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="productStore.pagination.lastPage > 1"
                        class="mt-12 flex justify-center"
                    >
                        <nav class="flex items-center space-x-2">
                            <button
                                @click="
                                    goToPage(
                                        productStore.pagination.currentPage - 1,
                                    )
                                "
                                :disabled="
                                    productStore.pagination.currentPage === 1
                                "
                                class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700"
                            >
                                Previous
                            </button>

                            <button
                                v-for="page in visiblePages"
                                :key="page"
                                @click="goToPage(page)"
                                :class="[
                                    'px-3 py-2 text-sm font-medium rounded-md',
                                    page === productStore.pagination.currentPage
                                        ? 'bg-primary-600 text-white dark:bg-primary-700'
                                        : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700',
                                ]"
                            >
                                {{ page }}
                            </button>

                            <button
                                @click="
                                    goToPage(
                                        productStore.pagination.currentPage + 1,
                                    )
                                "
                                :disabled="
                                    productStore.pagination.currentPage ===
                                    productStore.pagination.lastPage
                                "
                                class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700"
                            >
                                Next
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useProductStore } from "../stores/products";
import ProductCard from "../components/common/ProductCard.vue";
import ProductListItem from "../components/common/ProductListItem.vue";

const productStore = useProductStore();
const viewMode = ref("grid");
const showFilters = ref(false);

// Detect if desktop
const isDesktop = ref(window.innerWidth >= 1024);

// Update isDesktop on resize
const handleResize = () => {
    isDesktop.value = window.innerWidth >= 1024;
    if (isDesktop.value) {
        showFilters.value = true;
    }
};

// Local filters (synced with store)
const filters = ref({
    category: null,
    minPrice: null,
    maxPrice: null,
    sort: "created_at",
});

// Computed property for pagination
const visiblePages = computed(() => {
    const current = productStore.pagination.currentPage;
    const last = productStore.pagination.lastPage;
    const pages = [];

    // Show up to 5 pages around current page
    const start = Math.max(1, current - 2);
    const end = Math.min(last, current + 2);

    for (let i = start; i <= end; i++) {
        pages.push(i);
    }

    return pages;
});

// Initialize filters from store
onMounted(async () => {
    if (productStore.categories.length === 0) {
        await productStore.loadCategories();
    }

    productStore.clearFilters();
    filters.value = { ...productStore.filters };
    productStore.resetPagination();
    await loadProducts();

    window.addEventListener("resize", handleResize);
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", handleResize);
});

// Watch for local filter changes and sync with store
const applyFilters = () => {
    productStore.setFilters(filters.value);
    productStore.resetPagination();
    loadProducts();
};

// Load products with current filters
const loadProducts = async () => {
    await productStore.loadProducts();
};

// Clear all filters
const clearFilters = () => {
    filters.value = {
        category: null,
        minPrice: null,
        maxPrice: null,
        sort: "created_at",
    };
    productStore.clearFilters();
    loadProducts();
};

// Go to specific page
const goToPage = (page) => {
    if (page >= 1 && page <= productStore.pagination.lastPage) {
        productStore.pagination.currentPage = page;
        loadProducts();
    }
};
</script>

<style scoped>
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

/* Hide scrollbar for Chrome, Safari and Opera */
.max-h-64::-webkit-scrollbar {
    display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.max-h-64 {
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
}
</style>
