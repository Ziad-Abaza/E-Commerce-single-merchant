<!-- resources/js/pages/dashboard/Attribute.vue -->
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
                        Attribute Management
                    </h1>
                    <p
                        class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1"
                    >
                        Manage product attributes like color, size, material,
                        etc.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <button
                        @click="fetchAttributes"
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
                        Add Attribute
                    </button>
                </div>
            </div>
        </div>

        <!-- Error Alert -->
        <div
            v-if="attributesStore.error"
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
                    attributesStore.error
                }}</span>
                <button
                    @click="attributesStore.clearError"
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

        <!-- Filters -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700"
        >
            <div class="flex flex-col sm:flex-row gap-3 items-end">
                <!-- Search -->
                <div class="flex-1">
                    <Search
                        v-model="attributesStore.filters.search"
                        placeholder="Search by name or slug..."
                        @submit="handleSearch"
                    />
                </div>
                <!-- Type Filter -->
                <Select
                    v-model="attributesStore.filters.type"
                    :options="typeOptions"
                    placeholder="All Types"
                    label="Type"
                    @update:modelValue="handleTypeFilter"
                />
                <!-- Filterable Filter -->
                <Select
                    v-model="attributesStore.filters.is_filterable"
                    :options="booleanOptions"
                    placeholder="Filterable"
                    label="Filterable"
                    @update:modelValue="handleFilterableFilter"
                />
                <!-- Variant Filter -->
                <Select
                    v-model="attributesStore.filters.is_variant"
                    :options="booleanOptions"
                    placeholder="Variant"
                    label="Variant"
                    @update:modelValue="handleVariantFilter"
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
        <div v-if="attributesStore.loading" class="space-y-4">
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

        <!-- Attributes Table -->
        <div
            v-else
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
            <!-- Empty State -->
            <div
                v-if="attributesStore.attributes.length === 0"
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
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M9 11a2 2 0 114 0m-4 0V9a2 2 0 012-2m-2 0V5a2 2 0 012-2"
                    />
                </svg>
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                >
                    No attributes found
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    {{
                        hasActiveFilters
                            ? "Try adjusting your filters."
                            : "Get started by adding your first attribute."
                    }}
                </p>
                <button
                    v-if="!hasActiveFilters"
                    @click="openCreateForm"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                >
                    Add Attribute
                </button>
            </div>

            <!-- Table -->
            <div v-else>
                <Table :headers="tableHeaders" :rows="tableRows" />
            </div>
        </div>

        <!-- Pagination -->
        <div
            v-if="attributesStore.attributes.length > 0"
            class="flex justify-center"
        >
            <Pagination
                :total="attributesStore.pagination.total"
                :current-page="attributesStore.pagination.current_page"
                :per-page="attributesStore.pagination.per_page"
                :last-page="attributesStore.pagination.last_page"
                :from="attributesStore.pagination.from"
                :to="attributesStore.pagination.to"
                @page-change="handlePageChange"
                @update:perPage="handlePerPageChange"
            />
        </div>

        <!-- Create/Edit Form Modal -->
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
                        <h3
                            class="text-xl font-bold text-gray-900 dark:text-white"
                        >
                            {{
                                isEditing
                                    ? "Edit Attribute"
                                    : "Add New Attribute"
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
                        @submit="handleSubmitForm"
                    />
                </div>
            </div>
        </div>

        <!-- Details Modal -->
        <DetailsModal
            v-if="selectedAttribute"
            :show="showDetailModal"
            :item="selectedAttribute"
            :editable="false"
            title="Attribute Information"
            subtitle="Complete details about this attribute"
            image-section-title=""
            id-label="Attribute ID"
            @close="showDetailModal = false"
        />

        <!-- Delete Confirmation Modal -->
        <ConfirmModal
            :show="showDeleteConfirm"
            title="Delete Attribute"
            message="Are you sure you want to delete this attribute? This action cannot be undone."
            confirm-text="Delete"
            confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
            @confirm="handleDelete"
            @cancel="showDeleteConfirm = false"
            :loading="attributesStore.loading"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useAttributesStore } from "../../stores/dashboard/attributes";
import Search from "./components/Search.vue";
import Select from "./components/Select.vue";
import Table from "./components/Table.vue";
import Pagination from "./components/Pagination.vue";
import Form from "./components/Form.vue";
import DetailsModal from "./components/DetailsModal.vue";
import ConfirmModal from "./components/ConfirmModal.vue";

const attributesStore = useAttributesStore();

const showFormModal = ref(false);
const isEditing = ref(false);
const showDeleteConfirm = ref(false);
const showDetailModal = ref(false);
const attributeToDelete = ref(null);
const selectedAttribute = ref(null);
const formFields = ref([]);

// Filter options
const typeOptions = ref([
    { value: "text", label: "Text" },
    { value: "number", label: "Number" },
    { value: "select", label: "Select" },
    { value: "checkbox", label: "Checkbox" },
    { value: "radio", label: "Radio" },
    { value: "textarea", label: "Textarea" },
]);

const booleanOptions = ref([
    { value: "true", label: "Yes" },
    { value: "false", label: "No" },
]);

// Table headers
const tableHeaders = ref([
    { key: "name", label: "Name" },
    { key: "slug", label: "Slug" },
    { key: "type", label: "Type" },
    { key: "is_required", label: "Required" },
    { key: "is_filterable", label: "Filterable" },
    { key: "is_variant", label: "Variant" },
    { key: "sort_order", label: "Sort Order" },
    { key: "actions", label: "Actions" },
]);

// Computed properties
const hasActiveFilters = computed(() => {
    return (
        attributesStore.filters.search ||
        attributesStore.filters.type ||
        attributesStore.filters.is_filterable ||
        attributesStore.filters.is_variant
    );
});

const tableRows = computed(() => {
    return attributesStore.attributes.map((attr) => ({
        id: attr.id,
        name: attr.name,
        slug: attr.slug,
        type: attr.type,
        is_required: attr.is_required ? "Yes" : "No",
        is_filterable: attr.is_filterable ? "Yes" : "No",
        is_variant: attr.is_variant ? "Yes" : "No",
        sort_order: attr.sort_order,
        actions: [
            {
                label: "View",
                icon: "eye",
                class: "bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300",
                onClick: () => handleView(attr),
            },
            {
                label: "Edit",
                icon: "edit",
                class: "bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300",
                onClick: () => handleEdit(attr),
            },
            {
                label: "Delete",
                icon: "trash",
                class: "bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300",
                onClick: () => handleDeleteClick(attr),
            },
        ],
    }));
});

// Event handlers
const handleSearch = (searchTerm) => {
    attributesStore.setFilter("search", searchTerm);
};

const handleTypeFilter = (value) => {
    attributesStore.setFilter("type", value);
};

const handleFilterableFilter = (value) => {
    attributesStore.setFilter("is_filterable", value);
};

const handleVariantFilter = (value) => {
    attributesStore.setFilter("is_variant", value);
};

const clearAllFilters = () => {
    attributesStore.clearFilters();
};

const handlePageChange = (page) => {
    attributesStore.pagination.current_page = page;
    fetchAttributes(page, attributesStore.pagination.per_page);
};

const handlePerPageChange = (perPage) => {
    const perPageNumber = parseInt(perPage);
    attributesStore.pagination.per_page = perPageNumber;
    attributesStore.pagination.current_page = 1;
    fetchAttributes(1, perPageNumber);
};

const fetchAttributes = async (page = 1, perPage = 15) => {
    await attributesStore.fetchAttributes(page, perPage);
};

const openCreateForm = () => {
    isEditing.value = false;
    initializeFormFields(null);
    showFormModal.value = true;
};

const handleView = async (attr) => {
    try {
        const fullAttribute = await attributesStore.fetchAttribute(attr.id);
        selectedAttribute.value = fullAttribute;
        showDetailModal.value = true;
    } catch (error) {
        console.error("Error loading attribute details:", error);
    }
};

const handleEdit = async (attr) => {
    isEditing.value = true;
    attributeToDelete.value = attr;
    try {
        await attributesStore.fetchAttribute(attr.id);
        initializeFormFields(attributesStore.currentAttribute);
        showFormModal.value = true;
    } catch (error) {
        console.error("Error loading attribute for edit:", error);
    }
};

const initializeFormFields = (attr) => {
    formFields.value = [
        {
            id: "name",
            label: "Name",
            type: "text",
            value: attr?.name || "",
            required: true,
            placeholder: "Enter attribute name",
        },
        {
            id: "slug",
            label: "Slug",
            type: "text",
            value: attr?.slug || "",
            required: true,
            placeholder: "Enter a unique, URL-friendly slug (e.g., color-red)",
        },
        {
            id: "type",
            label: "Type",
            type: "select",
            value: attr?.type || "text",
            options: typeOptions.value,
            required: true,
        },
        {
            id: "options",
            label: "Options (for select/checkbox)",
            type: "tags",
            value: attr?.options || [],
            required: false,
            placeholder: "Enter options separated by commas",
        },
        {
            id: "is_required",
            label: "Required",
            type: "checkbox",
            value: attr?.is_required || false,
            required: false,
        },
        {
            id: "is_filterable",
            label: "Filterable",
            type: "checkbox",
            value: attr?.is_filterable || false,
            required: false,
        },
        {
            id: "is_variant",
            label: "Variant",
            type: "checkbox",
            value: attr?.is_variant || false,
            required: false,
        },
        {
            id: "is_visible_on_frontend",
            label: "Visible on Frontend",
            type: "checkbox",
            value: attr?.is_visible_on_frontend || true,
            required: false,
        },
        {
            id: "sort_order",
            label: "Sort Order",
            type: "number",
            value: attr?.sort_order || 0,
            required: false,
            min: 0,
        },
    ];
};

const handleSubmitForm = async (data) => {
    try {
        // Process options field
        if (data.options && Array.isArray(data.options)) {
            data.options = data.options.filter((opt) => opt.trim() !== "");
        } else if (typeof data.options === "string") {
            data.options = data.options
                .split(",")
                .map((opt) => opt.trim())
                .filter((opt) => opt !== "");
        }

        if (isEditing.value && attributeToDelete.value) {
            await attributesStore.updateAttribute(
                attributeToDelete.value.id,
                data,
            );
        } else {
            await attributesStore.createAttribute(data);
        }

        await fetchAttributes(
            attributesStore.pagination.current_page,
            attributesStore.pagination.per_page,
        );
        showFormModal.value = false;
        formFields.value = [];
        attributeToDelete.value = null;
    } catch (error) {
        console.error("Form submission error:", error);
    }
};

const handleDeleteClick = (attr) => {
    attributeToDelete.value = attr;
    showDeleteConfirm.value = true;
};

const handleDelete = async () => {
    if (!attributeToDelete.value) return;
    try {
        await attributesStore.deleteAttribute(attributeToDelete.value.id);
        showDeleteConfirm.value = false;
        attributeToDelete.value = null;
    } catch (error) {
        console.error("Delete error:", error);
    }
};

// Lifecycle
onMounted(() => {
    fetchAttributes(1, 15);
});
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
</style>
