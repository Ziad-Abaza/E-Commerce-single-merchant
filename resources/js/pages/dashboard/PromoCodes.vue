<!-- resources/js/pages/dashboard/PromoCodes.vue -->
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
                        Promo Codes Management
                    </h1>
                    <p
                        class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1"
                    >
                        Create and manage promotional discount codes
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <button
                        @click="fetchPromoCodes"
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
                        Add Promo Code
                    </button>
                </div>
            </div>
        </div>

        <!-- Error Alert -->
        <div
            v-if="promoCodesStore.error"
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
                    promoCodesStore.error
                }}</span>
                <button
                    @click="promoCodesStore.clearError"
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

        <!-- Filters and Search -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700"
        >
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <!-- Search -->
                <div>
                    <Search
                        v-model="promoCodesStore.filters.search"
                        placeholder="Search by code or name..."
                        @submit="handleSearch"
                    />
                </div>
                <!-- Status Filter -->
                <Select
                    v-model="promoCodesStore.filters.status"
                    :options="statusOptions"
                    placeholder="All Statuses"
                    label="Status"
                    @update:modelValue="handleStatusFilter"
                />
                <!-- Discount Type -->
                <Select
                    v-model="promoCodesStore.filters.discount_type"
                    :options="discountTypeOptions"
                    placeholder="All Types"
                    label="Discount Type"
                    @update:modelValue="handleDiscountTypeFilter"
                />
                <!-- Target Type -->
                <div class="flex gap-2">
                    <Select
                        v-model="promoCodesStore.filters.target_type"
                        :options="targetTypeOptions"
                        placeholder="All Targets"
                        label="Target"
                        @update:modelValue="handleTargetTypeFilter"
                    />
                    <button
                        v-if="hasActiveFilters"
                        @click="clearAllFilters"
                        class="px-3 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm"
                    >
                        Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading Skeleton -->
        <div
            v-if="promoCodesStore.loading && !promoCodesStore.promoCodes.length"
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
                            class="w-10 h-6 bg-gray-300 dark:bg-gray-700 rounded animate-pulse"
                        ></div>
                        <div class="flex-1 space-y-2">
                            <div
                                class="h-4 bg-gray-300 dark:bg-gray-700 rounded animate-pulse"
                            ></div>
                            <div
                                class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-1/2 animate-pulse"
                            ></div>
                        </div>
                        <div
                            class="w-20 h-8 bg-gray-300 dark:bg-gray-700 rounded animate-pulse"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Promo Codes Table -->
        <div
            v-else
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
            <div v-if="promoCodesStore.promoCodes.length === 0" class="text-center py-12">
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
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    No promo codes found
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    {{ hasActiveFilters ? "Try adjusting your filters." : "Get started by adding your first promo code." }}
                </p>
                <button
                    v-if="!hasActiveFilters"
                    @click="openCreateForm"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                >
                    Add Your First Promo Code
                </button>
            </div>
            <div v-else>
                <Table :headers="tableHeaders" :rows="tableRows" />
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="promoCodesStore.promoCodes.length > 0" class="flex justify-center">
            <Pagination
                :total="promoCodesStore.pagination.total"
                :current-page="promoCodesStore.pagination.current_page"
                :per-page="promoCodesStore.pagination.per_page"
                :last-page="promoCodesStore.pagination.last_page"
                @page-change="handlePageChange"
                @update:perPage="handlePerPageChange"
            />
        </div>

        <!-- Create/Edit Modal -->
        <div
            v-if="showFormModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            @click.self="showFormModal = false"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ isEditing ? "Edit Promo Code" : "Add New Promo Code" }}
                        </h3>
                        <button
                            @click="showFormModal = false"
                            class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                    <Form :model-fields="formFields" @submit="handleSubmitForm" />
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmModal
            :show="showDeleteConfirm"
            title="Delete Promo Code"
            :message="`Are you sure you want to delete the promo code '${promoCodeToDelete?.code || 'this code'}'? This action cannot be undone.`"
            confirm-text="Delete"
            confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
            @confirm="handleDelete"
            @cancel="showDeleteConfirm = false"
            :loading="promoCodesStore.deleting"
        />

        <!-- Promo Code Details Modal -->
        <div
            v-if="showDetailsModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            @click.self="showDetailsModal = false"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                            Promo Code Details
                        </h3>
                        <button
                            @click="showDetailsModal = false"
                            class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                    <div v-if="currentPromoCode" class="space-y-4">
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Code</label>
                                    <p class="text-lg font-mono font-bold text-gray-900 dark:text-white">{{ currentPromoCode.code }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Name</label>
                                    <p class="text-gray-900 dark:text-white">{{ currentPromoCode.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Discount</label>
                                    <p class="text-gray-900 dark:text-white">
                                        {{ currentPromoCode.discount_value }}
                                        {{ currentPromoCode.discount_type === 'percentage' ? '%' : siteStore.settings.currency }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Target</label>
                                    <p class="text-gray-900 dark:text-white capitalize">{{ currentPromoCode.target_type }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                                            currentPromoCode.is_active
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                                                : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                        ]"
                                    >
                                        {{ currentPromoCode.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Usage</label>
                                    <p class="text-gray-900 dark:text-white">
                                        {{ currentPromoCode.total_usage_count }} / {{ currentPromoCode.total_usage_limit || '∞' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Start Date</label>
                                    <p class="text-gray-900 dark:text-white">{{ formatDate(currentPromoCode.start_date) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">End Date</label>
                                    <p class="text-gray-900 dark:text-white">{{ formatDate(currentPromoCode.end_date) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Target Items -->
                        <div v-if="currentPromoCode.products && currentPromoCode.products.length > 0">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Applied to Products:</h4>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="product in currentPromoCode.products"
                                    :key="product.id"
                                    class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 rounded text-sm"
                                >
                                    {{ product.name }}
                                </span>
                            </div>
                        </div>
                        <div v-else-if="currentPromoCode.categories && currentPromoCode.categories.length > 0">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Applied to Categories:</h4>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="cat in currentPromoCode.categories"
                                    :key="cat.id"
                                    class="px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 rounded text-sm"
                                >
                                    {{ cat.name }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-3 pt-4">
                            <button
                                @click="handleEdit(currentPromoCode)"
                                class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm"
                            >
                                Edit
                            </button>
                            <button
                                @click="handleToggleStatus(currentPromoCode)"
                                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm"
                            >
                                {{ currentPromoCode.is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                            <button
                                @click="handleDeleteClick(currentPromoCode)"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { usePromoCodesStore } from "../../stores/dashboard/promoCodes";
import { useAuthStore } from "../../stores/auth";
import { useSiteStore } from "../../stores/site";
import Search from "./components/Search.vue";
import Select from "./components/Select.vue";
import Table from "./components/Table.vue";
import Pagination from "./components/Pagination.vue";
import Form from "./components/Form.vue";
import ConfirmModal from "./components/ConfirmModal.vue";

const promoCodesStore = usePromoCodesStore();
const authStore = useAuthStore();
const siteStore = useSiteStore();

// Permissions
if (!authStore.hasPermission("manage_promo_codes")) {
    throw new Error("Access denied: You do not have permission to manage promo codes");
}

// Modals
const showFormModal = ref(false);
const showDeleteConfirm = ref(false);
const showDetailsModal = ref(false);
const isEditing = ref(false);
const promoCodeToDelete = ref(null);
const currentPromoCode = ref(null);
const formFields = ref([]);

// Options
const statusOptions = ref([
    { value: "active", label: "Active" },
    { value: "inactive", label: "Inactive" },
]);

const discountTypeOptions = ref([
    { value: "percentage", label: "Percentage" },
    { value: "fixed", label: "Fixed Amount" },
]);

const targetTypeOptions = computed(() => {
    return promoCodesStore.availableFilters.target_types.map(type => ({
        value: type,
        label: type.charAt(0).toUpperCase() + type.slice(1)
    }));
});

// Table
const tableHeaders = ref([
    { key: "code", label: "Code" },
    { key: "name", label: "Name" },
    { key: "discount", label: "Discount" },
    { key: "target", label: "Target" },
    { key: "usage", label: "Usage" },
    { key: "status", label: "Status" },
    { key: "actions", label: "Actions" },
]);

const tableRows = computed(() => {
    return promoCodesStore.promoCodes.map(code => ({
        id: code.id,
        code: code.code,
        name: code.name,
        discount: {
            type: "text",
            value: `${code.discount_value}${code.discount_type === 'percentage' ? '%' : ` ${siteStore.settings.currency}`}`
        },
        target: {
            type: "text",
            value: code.target_type.charAt(0).toUpperCase() + code.target_type.slice(1)
        },
        usage: {
            type: "text",
            value: `${code.total_usage_count} / ${code.total_usage_limit || '∞'}`
        },
        status: {
            type: "status",
            value: code.is_active ? "Active" : "Inactive",
            class: code.is_active
                ? "px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300"
                : "px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300"
        },
        actions: [
            {
                label: "View",
                icon: "eye",
                class: "bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300",
                onClick: () => handleView(code)
            },
            {
                label: "Edit",
                icon: "edit",
                class: "bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300",
                onClick: () => handleEdit(code)
            },
            {
                label: code.is_active ? "Deactivate" : "Activate",
                icon: code.is_active ? "pause" : "play",
                class: code.is_active
                    ? "bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300"
                    : "bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-300",
                onClick: () => handleToggleStatus(code)
            },
            {
                label: "Delete",
                icon: "trash",
                class: "bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300",
                onClick: () => handleDeleteClick(code)
            }
        ]
    }));
});

// Computed
const hasActiveFilters = computed(() => {
    return (
        promoCodesStore.filters.search ||
        promoCodesStore.filters.status ||
        promoCodesStore.filters.discount_type ||
        promoCodesStore.filters.target_type
    );
});

// Handlers
const handleSearch = (searchTerm) => {
    promoCodesStore.setFilter("search", searchTerm);
};

const handleStatusFilter = (value) => {
    promoCodesStore.setFilter("status", value);
};

const handleDiscountTypeFilter = (value) => {
    promoCodesStore.setFilter("discount_type", value);
};

const handleTargetTypeFilter = (value) => {
    promoCodesStore.setFilter("target_type", value);
};

const clearAllFilters = () => {
    promoCodesStore.clearFilters();
};

const handlePageChange = (page) => {
    promoCodesStore.fetchPromoCodes(page, promoCodesStore.pagination.per_page);
};

const handlePerPageChange = (perPage) => {
    promoCodesStore.pagination.per_page = perPage;
    promoCodesStore.fetchPromoCodes(1, perPage);
};

const fetchPromoCodes = () => {
    promoCodesStore.fetchPromoCodes(
        promoCodesStore.pagination.current_page,
        promoCodesStore.pagination.per_page
    );
};

const openCreateForm = () => {
    isEditing.value = false;
    currentPromoCode.value = null;
    initializeFormFields(null);
    showFormModal.value = true;
};

const handleView = async (promoCode) => {
    try {
        const fullCode = await promoCodesStore.fetchPromoCode(promoCode.id);
        currentPromoCode.value = fullCode;
        showDetailsModal.value = true;
    } catch (error) {
        console.error("Error loading promo code details:", error);
    }
};

const handleEdit = async (promoCode) => {
    isEditing.value = true;
    promoCodeToDelete.value = promoCode;
    try {
        const fullCode = await promoCodesStore.fetchPromoCode(promoCode.id);
        currentPromoCode.value = fullCode;
        initializeFormFields(fullCode);
        showFormModal.value = true;
    } catch (error) {
        console.error("Error loading promo code for edit:", error);
    }
};

const initializeFormFields = (code) => {
    formFields.value = [
        {
            id: "code",
            label: "Promo Code",
            type: "text",
            value: code?.code || "",
            required: true,
            placeholder: "e.g. SUMMER25"
        },
        {
            id: "name",
            label: "Name",
            type: "text",
            value: code?.name || "",
            required: true,
            placeholder: "e.g. Summer Sale 2025"
        },
        {
            id: "discount_type",
            label: "Discount Type",
            type: "select",
            value: code?.discount_type || "percentage",
            required: true,
            options: [
                { value: "percentage", label: "Percentage" },
                { value: "fixed", label: "Fixed Amount" }
            ]
        },
        {
            id: "discount_value",
            label: "Discount Value",
            type: "number",
            value: code?.discount_value || "",
            required: true,
            placeholder: "e.g. 25 or 10.99"
        },
        {
            id: "target_type",
            label: "Target Type",
            type: "select",
            value: code?.target_type || "products",
            required: true,
            options: promoCodesStore.availableFilters.target_types.map(t => ({
                value: t,
                label: t.charAt(0).toUpperCase() + t.slice(1)
            }))
        },
        {
            id: "total_usage_limit",
            label: "Total Usage Limit (optional)",
            type: "number",
            value: code?.total_usage_limit || "",
            required: false,
            placeholder: "Leave blank for unlimited"
        },
        {
            id: "per_user_usage_limit",
            label: "Per User Limit (optional)",
            type: "number",
            value: code?.per_user_usage_limit || "",
            required: false,
            placeholder: "e.g. 1"
        },
        {
            id: "start_date",
            label: "Start Date (optional)",
            type: "date",
            value: code?.start_date ? new Date(code.start_date).toISOString().split('T')[0] : "",
            required: false
        },
        {
            id: "end_date",
            label: "End Date (optional)",
            type: "date",
            value: code?.end_date ? new Date(code.end_date).toISOString().split('T')[0] : "",
            required: false
        },
        {
            id: "is_active",
            label: "Active",
            type: "checkbox",
            value: code?.is_active || false,
            required: false
        }
    ];

    // Add dynamic fields based on target_type
    if (code?.target_type === "products" || (!code && formFields.value.find(f => f.id === "target_type")?.value === "products")) {
        // You'd normally fetch products list here, but for simplicity we'll skip dynamic product selector
        // In real app, you'd add a multi-select with product options
    }
};

const handleSubmitForm = async (data) => {
    try {
        if (isEditing.value && promoCodeToDelete.value) {
            await promoCodesStore.updatePromoCode(promoCodeToDelete.value.id, data);
        } else {
            await promoCodesStore.createPromoCode(data);
        }
        await fetchPromoCodes();
        showFormModal.value = false;
        formFields.value = [];
    } catch (error) {
        console.error("Form submission error:", error);
    }
};

const handleDeleteClick = (promoCode) => {
    promoCodeToDelete.value = promoCode;
    showDeleteConfirm.value = true;
};

const handleDelete = async () => {
    if (!promoCodeToDelete.value) return;
    try {
        await promoCodesStore.deletePromoCode(promoCodeToDelete.value.id);
        showDeleteConfirm.value = false;
        promoCodeToDelete.value = null;
    } catch (error) {
        console.error("Delete error:", error);
    }
};

const handleToggleStatus = async (promoCode) => {
    try {
        await promoCodesStore.toggleStatus(promoCode.id);
    } catch (error) {
        console.error("Toggle status error:", error);
    }
};

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    return new Date(dateString).toLocaleDateString();
};

// Lifecycle
onMounted(() => {
    fetchPromoCodes();
});
</script>

<style scoped>
/* Reuse existing animations */
@keyframes slide-in-from-top {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-in {
    animation: slide-in-from-top 0.3s ease-out forwards;
}
</style>