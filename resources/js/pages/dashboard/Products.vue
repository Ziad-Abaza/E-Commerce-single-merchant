<template>
    <div class="p-2 sm:p-4 md:p-6 space-y-4 sm:space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                        Product Management
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Manage your product catalog, inventory, and pricing.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <button @click="
                        productsStore.fetchProducts(
                            productsStore.pagination.current_page,
                            productsStore.pagination.per_page,
                        )
                        "
                        class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh
                    </button>
                    <button @click="handleOpenCreateForm"
                        class="px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Product
                    </button>

                    <div class="flex gap-2">
                        <button @click="showTrashed = false" :class="showTrashed ? 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' : 'bg-blue-600 text-white'
                            "
                            class="px-3 py-2 rounded-lg text-sm bg-green-500 text-white hover:bg-green-600 transition-colors duration-200">
                            All Products
                        </button>
                        <button @click="
                            showTrashed = true;
                        fetchTrashed();
                        " :class="showTrashed ? 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' : 'bg-blue-600 text-white'
                            "
                            class="px-3 py-2 rounded-lg text-sm bg-red-500 text-white hover:bg-red-600 transition-colors duration-200">
                            Trash
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Alert -->
        <div v-if="productsStore.error"
            class="p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg animate-in slide-in-from-top-2 duration-300">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm text-red-700 dark:text-red-300 flex-1">{{
                    productsStore.error
                    }}</span>
                <button @click="productsStore.clearError"
                    class="ml-2 text-red-500 hover:text-red-700 dark:hover:text-red-300 p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <!-- Total Products -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">
                            Total Products
                        </p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                            {{ productsStore.statistics?.total_products }}
                        </p>
                    </div>
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Products -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">
                            Active
                        </p>
                        <p class="text-lg font-bold text-green-600 dark:text-green-400 mt-1">
                            {{ productsStore.statistics?.active_products }}
                        </p>
                    </div>
                    <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Inactive Products -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">
                            Inactive
                        </p>
                        <p class="text-lg font-bold text-red-600 dark:text-red-400 mt-1">
                            {{ productsStore.statistics?.inactive_products }}
                        </p>
                    </div>
                    <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Low Stock -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">
                            Low Stock
                        </p>
                        <p class="text-lg font-bold text-yellow-600 dark:text-yellow-400 mt-1">
                            {{ productsStore.statistics?.low_stock }}
                        </p>
                    </div>
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row gap-3 items-end">
                <!-- Search -->
                <div class="flex-1">
                    <Search v-model="productsStore.filters.search"
                        placeholder="Search products by name, SKU, or brand..." @submit="handleSearch" />
                </div>

                <!-- Category Filter -->
                <Select v-model="productsStore.filters.category" :options="categoryOptions" placeholder="All Categories"
                    label="Category" @update:modelValue="handleCategoryFilter" />

                <!-- Status Filter -->
                <Select v-model="productsStore.filters.status" :options="statusOptions" placeholder="All Statuses"
                    label="Status" @update:modelValue="handleStatusFilter" />

                <!-- Clear Filters -->
                <button v-if="hasActiveFilters" @click="clearAllFilters"
                    class="px-3 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm">
                    Clear
                </button>
            </div>
        </div>

        <!-- Loading Skeleton -->
        <div v-if="productsStore.loading" class="space-y-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-4 space-y-3">
                    <div v-for="n in 5" :key="n" class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gray-300 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded animate-pulse"></div>
                            <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-2/3 animate-pulse"></div>
                        </div>
                        <div class="w-20 h-8 bg-gray-300 dark:bg-gray-700 rounded animate-pulse"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div v-else
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Empty State -->
            <div v-if="productsStore.products.length === 0" class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    No products found
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    {{
                        hasActiveFilters
                            ? "Try adjusting your filters or search terms."
                            : "Get started by adding your first product."
                    }}
                </p>
                <button v-if="!hasActiveFilters" @click="productsStore.openCreateModal()"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Add Your First Product
                </button>
            </div>

            <!-- Products Table -->
            <div v-else-if="showTrashed">
                <!-- Trashed Products Table -->
                <div v-if="productsStore.trashedProducts.length === 0" class="text-center py-12">
                    <p class="text-gray-500 dark:text-gray-400">
                        No trashed products found.
                    </p>
                </div>
                <div v-else>
                    <Table :headers="trashedTableHeaders" :rows="trashedTableRows" />
                </div>
            </div>
            <div v-else>
                <Table :headers="tableHeaders" :rows="tableRows" />
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="productsStore.products.length > 0" class="flex justify-center">
            <!-- Pagination -->
            <div v-if="productsStore.products.length > 0" class="flex justify-center">
                <Pagination :total="productsStore.pagination.total"
                    :current-page="productsStore.pagination.current_page" :per-page="productsStore.pagination.per_page"
                    :last-page="productsStore.pagination.last_page" :from="productsStore.pagination.from"
                    :to="productsStore.pagination.to" @page-change="handlePageChange"
                    @update:perPage="handlePerPageChange" />
            </div>
        </div>

        <!-- Form Modal -->
        <div v-if="showFormModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            @click.self="showFormModal = false">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <Form :model-fields="formFields" @submit="handleSubmitForm" />
                </div>
            </div>
        </div>

        <div v-if="showPromoCodeFormModal"
            class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center p-4 z-[60]"
            @click.self="showPromoCodeFormModal = false">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg" @click.stop>
                <div class="p-5 border-b dark:border-gray-700">
                    <h3 class="text-lg font-bold">{{ isEditingPromoCode ? 'Edit Promo Code' : 'Add New Promo Code' }}
                    </h3>
                </div>
                <form @submit.prevent="handleSubmitPromoCode" class="p-5 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Code</label>
                            <input v-model="currentPromoCode.code" type="text" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                            <select v-model="currentPromoCode.type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600">
                                <option value="percentage">Percentage</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Value</label>
                            <input v-model.number="currentPromoCode.value" type="number" step="0.01" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Usage Limit
                                (optional)</label>
                            <input v-model.number="currentPromoCode.usage_limit" type="number"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date
                                (optional)</label>
                            <input v-model="currentPromoCode.start_date" type="date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date
                                (optional)</label>
                            <input v-model="currentPromoCode.end_date" type="date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600">
                        </div>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input v-model="currentPromoCode.is_active" type="checkbox"
                                class="rounded border-gray-300 text-blue-600 shadow-sm">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                        </label>
                    </div>

                    <div v-if="promoCodesStore.error" class="text-sm text-red-500">{{ promoCodesStore.error }}</div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" @click="showPromoCodeFormModal = false"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 rounded-md text-sm">Cancel</button>
                        <button type="submit" :disabled="promoCodesStore.loading"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 disabled:bg-blue-300">
                            {{ promoCodesStore.loading ? 'Saving...' : (isEditingPromoCode ? 'Update Code' : 'Create Code') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <DetailsModal v-if="productsStore.currentProduct" :show="productsStore.showDetailsModal"
            :item="productsStore.currentProduct" :editable="true" title="Product Information"
            subtitle="Complete information about this product" image-section-title="Product Image" id-label="Product ID"
            @close="productsStore.showDetailsModal = false" @edit="handleEditFromDetails"
            @preview="handleImagePreview" />

        <ConfirmModal :show="showDeleteConfirm" title="Delete Product"
            :message="`Are you sure you want to delete this product? This action cannot be undone.`"
            confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500" @confirm="handleDelete"
            @cancel="showDeleteConfirm = false" :loading="productsStore.deleting" />

        <ConfirmModal :show="showSecondConfirm" title="Delete Product Linked to Orders"
            :message="`This product is linked to existing orders. Do you still want to delete it?`"
            confirm-text="Yes, Delete" confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
            @confirm="handleSecondDelete" @cancel="showSecondConfirm = false" :loading="productsStore.deleting" />

        <!-- Loading Overlay -->
        <LoadingOverlay v-if="productsStore.loading && productsStore.products.length > 0" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useProductsStore } from "../../stores/dashboard/products";
import { usePromoCodesStore } from "../../stores/dashboard/promoCodes";
import { useAuthStore } from "../../stores/auth";
import { useRouter } from "vue-router";
import Search from "./components/Search.vue";
import Select from "./components/Select.vue";
import Table from "./components/Table.vue";
import Pagination from "./components/Pagination.vue";
import Form from "./components/Form.vue";
import DetailsModal from "./components/DetailsModal.vue";
import ConfirmModal from "./components/ConfirmModal.vue";
import LoadingOverlay from "./components/LoadingOverlay.vue";
import { useSiteStore } from "@/stores/site";

const siteStore = useSiteStore();
const productsStore = useProductsStore();
const promoCodesStore = usePromoCodesStore();
const authStore = useAuthStore();
const router = useRouter();

const showDeleteConfirm = ref(false);
const showSecondConfirm = ref(false);
const showTrashed = ref(false);

const productToDelete = ref(null);
const showFormModal = ref(false);
const isEditing = ref(false);

const showPromoCodeFormModal = ref(false);
const isEditingPromoCode = ref(false);
const currentPromoCode = ref({});

const formFields = ref([]);
const formData = ref({});
const currentProductId = ref(null);



// Check permissions
if (!authStore.hasPermission("manage_products")) {
    throw new Error(
        "Access denied: You do not have permission to manage products",
    );
}

// Filter options
const categoryOptions = computed(() => {
    return [
        ...productsStore.categories.map((cat) => ({
            value: cat.id.toString(),
            label: cat.name,
        })),
    ];
});

const statusOptions = ref([
    { value: "active", label: "Active" },
    { value: "is_active", label: "Inactive" },
    { value: "in_stock", label: "In Stock" },
]);

// Table configuration
const tableHeaders = ref([
    { key: "image", label: "Image" },
    { key: "name", label: "Product Name" },
    { key: "sku", label: "SKU" },
    { key: "brand", label: "Brand" },
    { key: "price", label: "Price" },
    { key: "stock_quantity", label: "Stock" },
    { key: "status", label: "Status" },
    { key: "actions", label: "Actions" },
]);

// Computed properties
const hasActiveFilters = computed(() => {
    return (
        productsStore.filters.search ||
        productsStore.filters.category ||
        productsStore.filters.status
    );
});

const tableRows = computed(() => {
    return productsStore.products.map((product) => ({
        id: product.id,
        image: {
            type: "image",
            src:
                product.main_image_url ||
                product.images?.[0]?.url ||
                "/images/placeholder-product.png",
            full:
                product.main_image_url ||
                product.images?.[0]?.url ||
                "/images/placeholder-product.png",
        },
        name: product.name || "Unnamed Product",
        sku: product.sku || "N/A",
        brand: product.brand || "No Brand",
        price: product.price
            ? `${parseFloat(product.price).toFixed(2)} ${siteStore.settings.currency}`
            : "N/A",
        stock_quantity:
            product.stock_quantity !== undefined ? product.stock_quantity : 0,
        status:
            product.is_active === 1 || product.is_active === true
                ? "Active"
                : "Inactive",
        actions: [
            {
                label: "View",
                icon: "eye",
                class: "bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300",
                onClick: () => handleView(product),
            },
            {
                label: "Details",
                icon: "list",
                class: "bg-purple-100 text-purple-700 hover:bg-purple-200 dark:bg-purple-900/30 dark:text-purple-300",
                onClick: () => handleDetails(product),
            },
            {
                label: "Edit",
                icon: "edit",
                class: "bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300",
                onClick: () => handleEdit(product),
            },

            {
                label: "Delete",
                icon: "trash",
                class: "bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300",
                onClick: () => handleDeleteClick(product),
            },
        ],
    }));
});


const trashedTableHeaders = ref([
    { key: "name", label: "Product Name" },
    { key: "sku", label: "SKU" },
    { key: "deleted_at", label: "Deleted At" },
    { key: "actions", label: "Actions" },
]);

const trashedTableRows = computed(() => {
    return productsStore.trashedProducts.map((product) => ({
        id: product.id,
        name: product.name,
        sku: product.sku,
        deleted_at: new Date(product.deleted_at).toLocaleString(),
        actions: [
            {
                label: "Restore",
                class: "flex items-center gap-1 px-3 py-1 rounded-lg bg-green-600 text-white hover:bg-green-800 dark:bg-green-900/30 dark:text-white transition-colors duration-200",
                onClick: () => handleRestore(product),
            },
            {
                label: "Delete Permanently",
                class: "flex items-center gap-1 px-3 py-1 rounded-lg bg-red-600 text-white hover:bg-red-800 dark:bg-red-900/30 dark:text-white transition-colors duration-200",
                onClick: () => handleForceDelete(product),
            },
        ],
    }));
});

// Event handlers
const handleSearch = (searchTerm) => {
    productsStore.setFilter("search", searchTerm);
};

const handleCategoryFilter = (value) => {
    productsStore.setFilter("category", value);
};

const handleStatusFilter = (value) => {
    productsStore.setFilter("status", value);
};

const clearAllFilters = () => {
    productsStore.clearFilters();
    productsStore.fetchProducts();
};

const handleImagePreview = (imageUrl) => {
    console.log("Preview image:", imageUrl);
};

const handlePageChange = (page) => {
    productsStore.fetchProducts(page, productsStore.pagination.per_page);
};

const handlePerPageChange = (perPage) => {
    productsStore.pagination.per_page = perPage;
    productsStore.fetchProducts(1, perPage);
};

const handleView = async (product) => {
    try {
        const fullProduct = await productsStore.fetchProduct(product.id);
        productsStore.showDetailsModal = true;
    } catch (error) {
        console.error("Error loading product details:", error);
    }
};


const handleOpenCreateForm = () => {
    isEditing.value = false;
    currentProductId.value = null;
    initializeFormFields(null);
    showFormModal.value = true;
};

const handleEditFromDetails = (product) => {
    handleEdit(product);
};

const handleEdit = async (product) => {
    isEditing.value = true;
    currentProductId.value = product.id;

    try {
        const fullProduct = await productsStore.fetchProduct(product.id);

        initializeFormFields(fullProduct);
        showFormModal.value = true;
    } catch (error) {
        console.error("Error loading product for edit:", error);
        productsStore.error = "Failed to load product for editing";
    }
};

const initializeFormFields = (product) => {
    formFields.value = [
        {
            id: "name",
            label: "Product Name",
            type: "text",
            value: product?.name || "",
            required: true,
            placeholder: "Enter product name",
        },
        {
            id: "brand",
            label: "Brand",
            type: "text",
            value: product?.brand || "",
            required: false,
            placeholder: "Enter brand name",
        },
        {
            id: "sku",
            label: "SKU",
            type: "text",
            value: product?.sku || "",
            required: false,
            placeholder: "Enter SKU",
        },
        {
            id: "is_active",
            label: "Active Status",
            type: "checkbox",
            value: product?.is_active || false,
            required: false,
        },
        {
            id: "short_description",
            label: "Short Description",
            type: "textarea",
            value: product?.short_description || "",
            required: false,
            placeholder: "Enter short description",
        },
        {
            id: "description",
            label: "Full Description",
            type: "richtext",
            value: product?.description || "",
            required: false,
            placeholder: "Enter full description",
        },
        {
            id: "category_ids",
            type: "multipleselect",
            value: product?.categories?.map((c) => c.id.toString()) || [],
            options: productsStore.categories.map((cat) => ({
                value: cat.id.toString(),
                label: cat.name,
            })),
            required: false,
        },
    ];
};

const handleDeleteClick = (product) => {
    productToDelete.value = product;
    showDeleteConfirm.value = true;
};

const handleSubmitForm = async (data) => {
    try {
        if (isEditing.value && currentProductId.value) {
            const updatedData = {
                ...data,
                category_ids: data.category_ids
                    ? data.category_ids.map((id) => parseInt(id))
                    : [],
            };

            await productsStore.updateProduct(
                currentProductId.value,
                updatedData,
            );
        } else {
            const createData = {
                ...data,
                category_ids: data.category_ids
                    ? data.category_ids.map((id) => parseInt(id))
                    : [],
            };

            await productsStore.createProduct(createData);
        }

        await productsStore.fetchProducts(
            productsStore.pagination.current_page,
            productsStore.pagination.per_page,
        );

        showFormModal.value = false;
        formFields.value = [];
        formData.value = {};
        currentProductId.value = null;
    } catch (error) {
        console.error("Form submission error:", error);
    }
};
const handleDelete = async () => {
    if (!productToDelete.value) return;
    try {
        await productsStore.deleteProduct(productToDelete.value.id);
        showDeleteConfirm.value = false;
        productToDelete.value = null;
    } catch (error) {
        if (error.requiresConfirmation) {
            showDeleteConfirm.value = false;
            showSecondConfirm.value = true;
        } else {
            console.error("Delete error:", error);
        }
    }
};

const handleSecondDelete = async () => {
    if (!productToDelete.value) return;
    try {
        await productsStore.deleteProduct(productToDelete.value.id, true);
        showSecondConfirm.value = false;
        productToDelete.value = null;
    } catch (error) {
        console.error("Force delete error:", error);
    }
};


const fetchTrashed = async () => {
    await productsStore.fetchTrashedProducts(
        productsStore.trashedPagination.current_page,
        productsStore.trashedPagination.per_page
    );
};

const handleRestore = async (product) => {
    try {
        await productsStore.restoreProduct(product.id);
    } catch (error) {
        console.error("Restore error:", error);
    }
};

const handleForceDelete = async (product) => {
    try {
        await productsStore.forceDeleteProduct(product.id);
    } catch (error) {
        console.error("Force delete error:", error);
    }
};


const handleDetails = (product) => {
    router.push(`/dashboard/products/${product.id}/details`);
};
// Lifecycle
onMounted(() => {
    productsStore.fetchProducts(1, productsStore.pagination.per_page);
});

// Watch for filter changes
watch(
    () => [
        productsStore.filters.search,
        productsStore.filters.category,
        productsStore.filters.status,
    ],
    () => {
        productsStore.fetchProducts(1, productsStore.pagination.per_page);
    },
    { deep: true },
);
const handleOpenAddPromoCode = () => {
    isEditingPromoCode.value = false;
    currentPromoCode.value = {
        code: '',
        type: 'percentage',
        value: 10,
        usage_limit: null,
        start_date: null,
        end_date: null,
        is_active: true,
    };
    showPromoCodeFormModal.value = true;
};

const handleOpenEditPromoCode = (promoCode) => {
    isEditingPromoCode.value = true;
    // Create a copy to avoid reactive issues
    currentPromoCode.value = { ...promoCode };
    showPromoCodeFormModal.value = true;
};

const handleSubmitPromoCode = async () => {
    try {
        if (isEditingPromoCode.value) {
            await promoCodesStore.updatePromoCode(currentPromoCode.value.id, {
                ...currentPromoCode.value,
                product_id: currentProductId.value // ensure product_id is sent
            });
        } else {
            await promoCodesStore.createPromoCode({
                ...currentPromoCode.value,
                product_id: currentProductId.value,
            });
        }
        showPromoCodeFormModal.value = false;
    } catch (error) {
        // Error is handled in the store, but you can add component-specific logic here
        console.error("Failed to save promo code.");
    }
};

const handleDeletePromoCode = async (promoCodeId) => {
    if (confirm('Are you sure you want to delete this promo code?')) {
        await promoCodesStore.deletePromoCode(promoCodeId, currentProductId.value);
    }
};

</script>

<style scoped>
/* Add smooth animations */
@keyframes slide-in-from-top {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-in {
    animation: slide-in-from-top 0.3s ease-out forwards;
}

/* Improve card hover effects */
.group:hover .text-gray-900 {
    color: #1f2937;
}

.group:hover .dark\:text-white {
    color: #f9fafb;
}

/* Mobile responsive adjustments */
@media (max-width: 640px) {
    .grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .p-3 {
        padding: 0.75rem;
    }

    .text-sm {
        font-size: 0.875rem;
    }

    .text-xs {
        font-size: 0.75rem;
    }
}

@media (min-width: 640px) {
    .sm\:grid-cols-4 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .sm\:p-4 {
        padding: 1rem;
    }

    .sm\:text-2xl {
        font-size: 1.5rem;
    }
}

@media (min-width: 768px) {
    .md\:p-6 {
        padding: 1.5rem;
    }

    .md\:text-3xl {
        font-size: 1.875rem;
    }
}
</style>
