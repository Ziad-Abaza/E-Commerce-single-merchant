<!-- resources/js/pages/dashboard/products/ProductDetails.vue -->
<template>
    <div class="p-2 sm:p-4 md:p-6 space-y-4 sm:space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col gap-4">
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
            >
                <div>
                    <h1
                        class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 dark:text-white"
                    >
                        {{ product?.name || "Product" }} - Details Management
                    </h1>
                    <p
                        class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1"
                    >
                        Manage product variants, inventory, and pricing details
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <button
                        @click="fetchProductDetails"
                        class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
                    >
                        <svg
                            class="w-4 h-4 mr-1"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                            />
                        </svg>
                        Refresh
                    </button>
                    <button
                        @click="openCreateForm"
                        class="px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
                    >
                        <svg
                            class="w-4 h-4 mr-1"
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
                        Add Detail
                    </button>

                    <div class="flex gap-2">
                        <button
                            @click="
                                viewMode = 'active';
                                fetchProductDetails();
                            "
                            :class="
                                viewMode === 'active'
                                    ? 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                                    : 'bg-blue-600 text-white'
                            "
                            class="px-3 py-2 rounded-lg text-sm bg-green-500 text-white hover:bg-green-600 transition-colors duration-200"
                        >
                            Active Details
                        </button>
                        <button
                            @click="
                                viewMode = 'trash';
                                fetchTrashedDetails();
                            "
                            :class="
                                viewMode === 'trash'
                                    ? 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                                    : 'bg-blue-600 text-white'
                            "
                            class="px-3 py-2 rounded-lg text-sm bg-red-500 text-white hover:bg-red-600 transition-colors duration-200"
                        >
                            Trash
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Alert -->
        <div
            v-if="productDetailsStore.error"
            class="p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg animate-in slide-in-from-top-2 duration-300"
        >
            <div class="flex items-center">
                <svg
                    class="w-5 h-5 text-red-500 mr-2 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
                <span class="text-sm text-red-700 dark:text-red-300 flex-1">{{
                    productDetailsStore.error
                }}</span>
                <button
                    @click="productDetailsStore.clearError"
                    class="ml-2 text-red-500 hover:text-red-700 dark:hover:text-red-300 p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
                >
                    <svg
                        class="w-4 h-4"
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
        </div>

        <!-- Product Info Card -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700"
        >
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-shrink-0">
                    <img
                        :src="
                            product?.image_url ||
                            product?.main_image_url ||
                            '/images/placeholder-product.png'
                        "
                        :alt="product?.name"
                        class="w-24 h-24 object-cover rounded-lg"
                    />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ product?.name }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ product?.brand }}
                    </p>
                    <p
                        class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                        v-html="product?.description"
                    ></p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span
                            v-for="category in product?.categories"
                            :key="category.id"
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300"
                        >
                            {{ category.name }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700"
        >
            <div class="flex flex-col sm:flex-row gap-3 items-end">
                <!-- Search -->
                <div class="flex-1">
                    <Search
                        v-model="productDetailsStore.filters.search"
                        placeholder="Search by color, size, SKU..."
                        @submit="handleSearch"
                    />
                </div>

                <!-- Status Filter -->
                <Select
                    v-model="productDetailsStore.filters.is_active"
                    :options="statusOptions"
                    placeholder="All Statuses"
                    label="Status"
                    @update:modelValue="handleStatusFilter"
                />

                <!-- Stock Filter -->
                <Select
                    v-model="productDetailsStore.filters.in_stock"
                    :options="stockOptions"
                    placeholder="All Stock"
                    label="Stock"
                    @update:modelValue="handleStockFilter"
                />

                <!-- Clear Filters -->
                <button
                    v-if="hasActiveFilters"
                    @click="clearAllFilters"
                    class="px-3 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm"
                >
                    Clear
                </button>
            </div>
        </div>

        <!-- Loading Skeleton -->
        <div
            v-if="
                productDetailsStore.loading &&
                !productDetailsStore.details.length
            "
            class="space-y-4"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <div class="p-4 space-y-3">
                    <div
                        v-for="n in 5"
                        :key="n"
                        class="flex items-center space-x-4"
                    >
                        <div
                            class="w-12 h-12 bg-gray-300 dark:bg-gray-700 rounded-lg animate-pulse"
                        ></div>
                        <div class="flex-1 space-y-2">
                            <div
                                class="h-4 bg-gray-300 dark:bg-gray-700 rounded animate-pulse"
                            ></div>
                            <div
                                class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-2/3 animate-pulse"
                            ></div>
                        </div>
                        <div
                            class="w-20 h-8 bg-gray-300 dark:bg-gray-700 rounded animate-pulse"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Table -->
        <div
            v-else
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
            <!-- Empty State -->
            <div
                v-if="productDetailsStore.details.length === 0"
                class="text-center py-12"
            >
                <svg
                    class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                    />
                </svg>
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                >
                    No product details found
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    {{
                        hasActiveFilters
                            ? "Try adjusting your filters."
                            : "Get started by adding your first product detail."
                    }}
                </p>
                <button
                    v-if="!hasActiveFilters"
                    @click="openCreateForm"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                >
                    Add Product Detail
                </button>
            </div>

            <!-- Details Table -->
            <div v-else>
                <Table
                    :headers="tableHeaders"
                    :rows="viewMode === 'active' ? tableRows : trashedRows"
                />
            </div>
        </div>

        <!-- Pagination -->
        <div
            v-if="productDetailsStore.details.length > 0"
            class="flex justify-center"
        >
            <Pagination
                :total="productDetailsStore.pagination.total"
                :current-page="productDetailsStore.pagination.current_page"
                :per-page="productDetailsStore.pagination.per_page"
                :last-page="productDetailsStore.pagination.last_page"
                :from="productDetailsStore.pagination.from"
                :to="productDetailsStore.pagination.to"
                @page-change="handlePageChange"
                @update:perPage="handlePerPageChange"
            />
        </div>

        <!-- Modals -->
        <!-- Create/Edit Form Modal -->
        <div
            v-if="showFormModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            @click.self="showFormModal = false"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3
                            class="text-xl font-bold text-gray-900 dark:text-white"
                        >
                            {{
                                isEditing
                                    ? "Edit Product Detail"
                                    : "Add New Product Detail"
                            }}
                        </h3>
                        <button
                            @click="showFormModal = false"
                            class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        >
                            <svg
                                class="w-6 h-6"
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

                    <Form
                        :model-fields="formFields"
                        :available-attributes="availableAttributesForCategory"
                        @submit="handleSubmitForm"
                    />
                </div>
            </div>
        </div>

        <!-- Details Modal -->
        <DetailsModal
            v-if="selectedDetail"
            :show="showDetailModal"
            :item="selectedDetail"
            :editable="false"
            title="Product Detail Information"
            subtitle="Complete details about this product variant"
            image-section-title="Product Variant Image"
            id-label="Detail ID"
            @close="showDetailModal = false"
            @preview="handleImagePreview"
        />

        <!-- Delete Confirmation Modal -->
        <ConfirmModal
            :show="showDeleteConfirm"
            title="Delete Product Detail"
            :message="`Are you sure you want to delete this product detail? This action cannot be undone.`"
            confirm-text="Delete"
            confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
            @confirm="handleDelete"
            @cancel="showDeleteConfirm = false"
            :loading="productDetailsStore.loading"
        />

        <!-- confirm delete if detail is linked to orders -->
        <ConfirmModal
            :show="showSecondConfirm"
            title="Delete Product Detail Linked to Orders"
            :message="`This product detail is linked to existing orders. Do you still want to delete it?`"
            confirm-text="Yes, Delete"
            confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
            @confirm="handleForceDeleteConfirm"
            @cancel="showSecondConfirm = false"
            :loading="productDetailsStore.loading"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useProductDetailsStore } from "../../stores/dashboard/productDetails";
import { useProductsStore } from "../../stores/dashboard/products";
import { useRoute, useRouter } from "vue-router";
import Search from "./components/Search.vue";
import Select from "./components/Select.vue";
import Table from "./components/Table.vue";
import Pagination from "./components/Pagination.vue";
import Form from "./components/Form.vue";
import DetailsModal from "./components/DetailsModal.vue";
import ConfirmModal from "./components/ConfirmModal.vue";
import VariantDisplay from "./components/VariantDisplay.vue";
import { useSiteStore } from "@/stores/site";
import { useAttributeCategoriesStore } from "../../stores/dashboard/attributeCategories";

const attributeCategoriesStore = useAttributeCategoriesStore();
const siteStore = useSiteStore();
const productDetailsStore = useProductDetailsStore();
const productsStore = useProductsStore();
const route = useRoute();
const formFields = ref([]);
const isSubmitting = ref(false);
const isEditing = ref(false);
const showFormModal = ref(false);
const availableAttributesForCategory = ref([]);
const showSecondConfirm = ref(false);
const showDeleteConfirm = ref(false);
const showDetailModal = ref(false);
const viewMode = ref("active");
const detailToDelete = ref(null);
const selectedDetail = ref(null);

// Get product ID from route
const productId = computed(() => route.params.id);

// Status and stock options
const statusOptions = ref([
    { value: "active", label: "Active" },
    { value: "inactive", label: "Inactive" },
]);

const stockOptions = ref([
    { value: "in_stock", label: "In Stock" },
    { value: "out_of_stock", label: "Out of Stock" },
]);

// Table headers
const tableHeaders = ref([
    { key: "variant", label: "Variant" },
    { key: "sku_variant", label: "SKU Variant" },
    { key: "price", label: "Price" },
    { key: "stock", label: "Stock" },
    { key: "status", label: "Status" },
    { key: "actions", label: "Actions" },
]);

// Computed properties
const hasActiveFilters = computed(() => {
    return (
        productDetailsStore.filters.search ||
        productDetailsStore.filters.is_active ||
        productDetailsStore.filters.in_stock
    );
});

const product = computed(() => productDetailsStore.product);
const tableRows = computed(() => {
    return productDetailsStore.details.map((detail) => ({
        id: detail.id,
        variant: {
            type: "variant",
            component: VariantDisplay,
            props: {
                variant: {
                    color: detail.color || "N/A",
                    size: detail.size || "N/A",
                    material: detail.material || "N/A",
                },
            },
        },
        sku_variant: detail.sku_variant || "N/A",
        price: `${parseFloat(detail.price).toFixed(2)} ${siteStore.settings.currency}`,
        stock: detail.stock,
        status: detail.is_active ? "Active" : "Inactive",
        actions: [
            {
                label: "View",
                icon: "eye",
                class: "bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300",
                onClick: () => handleViewDetail(detail),
            },
            {
                label: "Edit",
                icon: "edit",
                class: "bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300",
                onClick: () => handleEdit(detail),
            },
            {
                label: "Duplicate",
                icon: "duplicate",
                class: "bg-indigo-100 text-indigo-700 hover:bg-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300",
                onClick: () => handleDuplicate(detail),
            },
            {
                label: "Delete",
                icon: "trash",
                class: "bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300",
                onClick: () => handleDeleteClick(detail),
            },
        ],
    }));
});

const trashedRows = computed(() => {
    return productDetailsStore.trashedDetails.map((detail) => ({
        id: detail.id,
        variant: `${detail.color || "N/A"} / ${detail.size || "N/A"}`,
        sku_variant: detail.sku_variant || "N/A",
        price: `${parseFloat(detail.price).toFixed(2)} ${siteStore.settings.currency}`,
        stock: detail.stock,
        status: "Deleted",
        actions: [
            {
                label: "Restore",
                icon: "refresh",
                class: "bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-300",
                onClick: () => handleRestore(detail),
            },
            {
                label: "Delete",
                icon: "trash",
                class: "bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300",
                onClick: () => handleForceDelete(detail),
            },
        ],
    }));
});

// Event handlers
const handleSearch = (searchTerm) => {
    productDetailsStore.setFilter("search", searchTerm);
};

const handleStatusFilter = (value) => {
    productDetailsStore.setFilter("is_active", value);
};

const handleStockFilter = (value) => {
    productDetailsStore.setFilter("in_stock", value);
};

const clearAllFilters = () => {
    productDetailsStore.clearFilters();
};

const handlePageChange = (page) => {
    productDetailsStore.pagination.current_page = page;
    fetchProductDetails(page, productDetailsStore.pagination.per_page);
};

const handlePerPageChange = (perPage) => {
    const perPageNumber = parseInt(perPage);
    productDetailsStore.pagination.per_page = perPageNumber;
    productDetailsStore.pagination.current_page = 1;
    fetchProductDetails(1, perPageNumber);
};

const fetchProductDetails = async (page = 1, perPage = 10) => {
    if (!productId.value) return;
    await productDetailsStore.fetchProductDetails(
        productId.value,
        page,
        perPage,
    );
};

const openCreateForm = async () => {
    isEditing.value = false;
    await initializeFormFields(null);
    showFormModal.value = true;
};

const handleViewDetail = async (detail) => {
    try {
        const fullDetail = await productDetailsStore.fetchProductDetail(
            productId.value,
            detail.id,
        );
        selectedDetail.value = fullDetail;
        showDetailModal.value = true;
    } catch (error) {
        console.error("Error loading product detail:", error);
    }
};

const handleEdit = async (detail) => {
    isEditing.value = true;
    detailToDelete.value = detail;

    try {
        await productDetailsStore.fetchProductDetail(
            productId.value,
            detail.id,
        );
        await initializeFormFields(productDetailsStore.currentDetail);
        showFormModal.value = true;
    } catch (error) {
        console.error("Error loading product detail for edit:", error);
    }
};

const handleDuplicate = async (detail) => {
    isEditing.value = false;
    await initializeFormFields({ ...detail });
    showFormModal.value = true;
};

const initializeFormFields = async (detail) => {
    if (detail && !isEditing.value) {
        delete detail.id;
        delete detail.images;
    }
    // Format attributes for the AttributeManager
    const formatAttributes = (attributes) => {
        if (!attributes || !Array.isArray(attributes)) return [];

        return attributes.map((attr) => ({
            id: attr.id || Date.now() + Math.random().toString(36).substr(2, 9),
            name: attr.name || "",
            code: attr.code || "",
            type: attr.type || "select",
            values: attr.values
                ? attr.values.map((val) => ({
                      id:
                          val.id ||
                          Date.now() + Math.random().toString(36).substr(2, 9),
                      value: val.value || "",
                      is_visible: val.is_visible !== false,
                      is_variant: val.is_variant || 0,
                      is_filterable: val.is_filterable !== false,
                  }))
                : [],
        }));
    };
    const categoryId = product.value?.categories?.[0]?.id;
    
    if (categoryId) {
        try {
            const categoryAttrs = await attributeCategoriesStore.fetchAttributesForCategory(categoryId);
            availableAttributesForCategory.value = categoryAttrs.map(attr => ({
                id: attr.id,
                name: attr.name,
                code: attr.slug,
                type: attr.type || 'select' // Ensure type has a default value
            }));
        } catch (error) {
            console.error("Failed to load category attributes:", error);
            availableAttributesForCategory.value = [];
        }
    } else {
        availableAttributesForCategory.value = [];
    }

    formFields.value = [
        // Attributes Section
        {
            id: "attributes",
            label: "Product Attributes",
            type: "attribute-selector",
            value: formatAttributes(detail?.attributes) || [],
            availableAttributes: availableAttributesForCategory.value,
            required: false,
        },
        // Color field (kept for backward compatibility)
        {
            id: "color",
            label: "Color",
            type: "text",
            value: detail?.color || "",
            required: false,
            placeholder: "Enter color (e.g., Black, Red)",
        },
        {
            id: "price",
            label: "Price",
            type: "number",
            value: detail?.price || 0,
            required: true,
            placeholder: "Enter price",
        },
        {
            id: "discount",
            label: "Discount",
            type: "number",
            value: detail?.discount || 0,
            required: false,
            placeholder: "Enter discount amount",
        },
        {
            id: "stock",
            label: "Stock Quantity",
            type: "number",
            value: detail?.stock || 0,
            required: true,
            placeholder: "Enter stock quantity",
        },
        {
            id: "min_stock_alert",
            label: "Minimum Stock Alert",
            type: "number",
            value: detail?.min_stock_alert || 0,
            required: false,
            placeholder: "Enter minimum stock alert",
        },
        {
            id: "sku_variant",
            label: "SKU Variant",
            type: "text",
            value: detail?.sku_variant || "",
            required: false,
            placeholder: "Enter SKU variant",
        },
        {
            id: "images",
            label: "Product Images",
            type: "files",
            value: detail?.images || [],
            required: false,
            acceptedTypes:
                "image/jpeg,image/png,image/jpg,image/webp,image/gif",
            placeholder: "Upload product images",
        },
        {
            id: "is_active",
            label: "Active Status",
            type: "checkbox",
            value: detail?.is_active || false,
            required: false,
        },
    ];
};

const handleDeleteClick = (detail) => {
    detailToDelete.value = detail;
    showDeleteConfirm.value = true;
};

const handleSubmitForm = async (data) => {
    try {
        data.weight = parseFloat(data.weight) || 0;
        data.length = parseFloat(data.length) || 0;
        data.width = parseFloat(data.width) || 0;
        data.height = parseFloat(data.height) || 0;
        data.price = parseFloat(data.price) || 0;
        data.discount = parseFloat(data.discount) || 0;
        data.stock = parseInt(data.stock) || 0;
        data.min_stock_alert = parseInt(data.min_stock_alert) || 0;

        if (isEditing.value && detailToDelete.value) {
            await productDetailsStore.updateProductDetail(
                productId.value,
                detailToDelete.value.id,
                data,
            );
        } else {
            await productDetailsStore.createProductDetail(
                productId.value,
                data,
            );
        }

        await fetchProductDetails(
            productDetailsStore.pagination.current_page,
            productDetailsStore.pagination.per_page,
        );

        showFormModal.value = false;
        formFields.value = [];
        detailToDelete.value = null;
    } catch (error) {
        console.error("Form submission error:", error);
    }
};

const handleDelete = async () => {
    if (!detailToDelete.value) return;

    try {
        await productDetailsStore.deleteProductDetail(
            productId.value,
            detailToDelete.value.id,
        );
        showDeleteConfirm.value = false;
        detailToDelete.value = null;
    } catch (error) {
        if (error.requiresConfirmation) {
            showDeleteConfirm.value = false;
            showSecondConfirm.value = true;
        } else {
            console.error("Delete error:", error);
        }
    }
};
const handleForceDeleteConfirm = async () => {
    if (!detailToDelete.value) return;

    try {
        await productDetailsStore.deleteProductDetail(
            productId.value,
            detailToDelete.value.id,
            true,
        );
        showSecondConfirm.value = false;
        detailToDelete.value = null;
    } catch (error) {
        console.error("Force delete error:", error);
    }
};

const fetchTrashedDetails = async (page = 1, perPage = 10) => {
    if (!productId.value) return;
    await productDetailsStore.fetchTrashedDetails(
        productId.value,
        page,
        perPage,
    );
};

const handleRestore = async (detail) => {
    await productDetailsStore.restoreDetail(productId.value, detail.id);
};

const handleForceDelete = async (detail) => {
    await productDetailsStore.forceDeleteDetail(productId.value, detail.id);
};

const handleImagePreview = (imageUrl) => {
    window.open(imageUrl, "_blank");
};

// Lifecycle
onMounted(async () => {
    if (productId.value) {
        try {
            const productData = await productsStore.fetchProduct(
                productId.value,
            );
            productDetailsStore.setProduct(productData);
            await fetchProductDetails(1, 10);
        } catch (error) {
            console.error("Error loading product:", error);
        }
    }
});

watch(
    () => route.params.id,
    async (newId, oldId) => {
        if (newId && newId !== oldId) {
            try {
                const productData = await productsStore.fetchProduct(newId);
                productDetailsStore.setProduct(productData);
                await fetchProductDetails(1, 10);
            } catch (error) {
                console.error("Error loading product:", error);
            }
        }
    },
);
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
