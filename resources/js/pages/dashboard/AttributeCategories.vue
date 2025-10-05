<!-- resources/js/pages/dashboard/AttributeCategoriesManagement.vue -->
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
                        Attribute-Category Management
                    </h1>
                    <p
                        class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1"
                    >
                        Manage which attributes belong to which categories
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <button
                        @click="fetchData"
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
                    <!-- Add Attribute Button (only visible when a category is selected) -->
                    <button
                        v-if="selectedCategoryId"
                        @click="showAssignModal = true"
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
            v-if="attributeCategoriesStore.error"
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
                    attributeCategoriesStore.error
                }}</span>
                <button
                    @click="attributeCategoriesStore.clearError"
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

        <!-- Category Selector -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700"
        >
            <label
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >Select Category</label
            >
            <select
                v-model="selectedCategoryId"
                @change="handleCategoryChange"
                class="w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                :disabled="attributeCategoriesStore.loading"
            >
                <option value="">-- Choose a category --</option>
                <option
                    v-for="category in attributeCategoriesStore.categories"
                    :key="category.id"
                    :value="category.id"
                >
                    {{ category.name }}
                </option>
            </select>
        </div>

        <!-- Loading Skeleton -->
        <div
            v-if="
                attributeCategoriesStore.loading &&
                !attributeCategoriesStore.categoryAttributes.length
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
                        class="flex items-center justify-between"
                    >
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-8 h-8 bg-gray-300 dark:bg-gray-700 rounded animate-pulse"
                            ></div>
                            <div class="space-y-2">
                                <div
                                    class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-32 animate-pulse"
                                ></div>
                                <div
                                    class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-24 animate-pulse"
                                ></div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <div
                                class="w-20 h-8 bg-gray-300 dark:bg-gray-700 rounded animate-pulse"
                            ></div>
                            <div
                                class="w-8 h-8 bg-gray-300 dark:bg-gray-700 rounded-full animate-pulse"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attributes Table -->
        <div
            v-else-if="selectedCategoryId"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
            <!-- Empty State -->
            <div
                v-if="attributeCategoriesStore.categoryAttributes.length === 0"
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
                    No attributes assigned
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    Assign attributes to this category to get started.
                </p>
                <button
                    @click="showAssignModal = true"
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

        <!-- No Categories State -->
        <div
            v-else-if="
                !attributeCategoriesStore.loading &&
                attributeCategoriesStore.categories.length === 0
            "
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
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                No categories available
            </h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">
                Create a category first to assign attributes.
            </p>
            <router-link
                to="/dashboard/categories"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
            >
                Go to Categories
            </router-link>
        </div>

        <!-- Assign Attribute Modal -->
        <div
            v-if="showAssignModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            @click.self="showAssignModal = false"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            >
                <div class="p-6">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h3
                            class="text-xl font-bold text-gray-900 dark:text-white"
                        >
                            Assign Attribute to Category
                        </h3>
                        <button
                            @click="showAssignModal = false"
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

                    <!-- Modal Body -->
                    <div class="space-y-5">
                        <!-- Category Info (Read-only) -->
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3"
                        >
                            <p
                                class="text-sm font-medium text-blue-800 dark:text-blue-200"
                            >
                                Category:
                                <span class="font-bold">{{
                                    selectedCategoryName
                                }}</span>
                            </p>
                        </div>

                        <!-- Attribute Selection -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Select Attribute
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="assignForm.attribute_id"
                                @change="handleAttributeChange"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                                <option value="">
                                    -- Choose an attribute --
                                </option>
                                <option
                                    v-for="attr in availableAttributes"
                                    :key="attr.id"
                                    :value="attr.id"
                                >
                                    {{ attr.name }} ({{ attr.type }})
                                </option>
                            </select>
                        </div>

                        <!-- Attribute Preview (when selected) -->
                        <div
                            v-if="selectedAttribute"
                            class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600"
                        >
                            <h4
                                class="text-sm font-semibold text-gray-900 dark:text-white mb-2"
                            >
                                Attribute Preview
                            </h4>
                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm"
                            >
                                <div>
                                    <span
                                        class="text-gray-500 dark:text-gray-400"
                                        >Type:</span
                                    >
                                    <span
                                        class="ml-1 font-medium text-gray-900 dark:text-white"
                                        >{{ selectedAttribute.type }}</span
                                    >
                                </div>
                                <div>
                                    <span
                                        class="text-gray-500 dark:text-gray-400"
                                        >Required Globally:</span
                                    >
                                    <span
                                        class="ml-1 font-medium text-gray-900 dark:text-white"
                                        >{{
                                            selectedAttribute.is_required
                                                ? "Yes"
                                                : "No"
                                        }}</span
                                    >
                                </div>
                                <div
                                    v-if="
                                        selectedAttribute.options &&
                                        selectedAttribute.options.length
                                    "
                                >
                                    <span
                                        class="text-gray-500 dark:text-gray-400"
                                        >Options:</span
                                    >
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        <span
                                            v-for="(
                                                opt, idx
                                            ) in selectedAttribute.options"
                                            :key="idx"
                                            class="px-2 py-1 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 text-xs rounded"
                                        >
                                            {{ opt }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Configuration Fields -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <div class="flex items-center">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input
                                            type="checkbox"
                                            v-model="assignForm.is_required"
                                            :true-value="1"
                                            :false-value="0"
                                            class="sr-only peer"
                                        />
                                        <div class="relative w-16 h-8">
                                            <!-- Track -->
                                            <div class="absolute inset-0 rounded-full transition-colors duration-300"
                                                :class="{
                                                    'bg-blue-500': assignForm.is_required === 1,
                                                    'bg-gray-300 dark:bg-gray-600': assignForm.is_required !== 1
                                                }">
                                            </div>
                                            <!-- Toggle handle with sliding animation -->
                                            <div class="absolute left-1 top-1 w-6 h-6 bg-white rounded-full shadow-md transform transition-transform duration-300"
                                                :class="{
                                                    'translate-x-8': assignForm.is_required === 1,
                                                    'translate-x-0': assignForm.is_required !== 1
                                                }">
                                            </div>
                                            <!-- Labels inside the toggle -->
                                            <div class="absolute inset-0 flex items-center justify-between px-2 text-[10px] font-medium text-white select-none pointer-events-none">
                                                <span :class="{'opacity-0': assignForm.is_required === 1}">OFF</span>
                                                <span :class="{'opacity-0': assignForm.is_required !== 1}">ON</span>
                                            </div>
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Required for this category
                                            <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                {{ assignForm.is_required ? 'Attribute is required' : 'Attribute is optional' }}
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                >
                                    Sort Order
                                </label>
                                <input
                                    type="number"
                                    v-model.number="assignForm.sort_order"
                                    min="0"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-8 flex justify-end gap-3">
                        <button
                            @click="showAssignModal = false"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 rounded-md text-sm font-medium text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="handleAssignAttribute"
                            :disabled="
                                !assignForm.attribute_id ||
                                attributeCategoriesStore.loading
                            "
                            class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:bg-blue-300 disabled:cursor-not-allowed transition-colors"
                        >
                            {{
                                attributeCategoriesStore.loading
                                    ? "Assigning..."
                                    : "Assign Attribute"
                            }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useAttributeCategoriesStore } from "../../stores/dashboard/attributeCategories";
import { useAttributesStore } from "../../stores/dashboard/attributes";
import Table from "./components/Table.vue";
import { useRouter } from "vue-router";

const attributeCategoriesStore = useAttributeCategoriesStore();
const attributesStore = useAttributesStore();
const router = useRouter();

const selectedCategoryId = ref(null);
const showAssignModal = ref(false);
const assignForm = ref({
    attribute_id: null,
    is_required: false, // Ensure this is properly initialized
    sort_order: 0,
});

// ... existing imports ...

// Add this after `assignForm`
const selectedAttribute = ref(null);

// Computed property for selected category name
const selectedCategoryName = computed(() => {
    const category = attributeCategoriesStore.categories.find(
        (cat) => cat.id === selectedCategoryId.value,
    );
    return category ? category.name : "Unknown";
});

// Watch for attribute selection change
const handleAttributeChange = () => {
    if (assignForm.value.attribute_id) {
        const attr = attributesStore.attributes.find(
            (a) => a.id === assignForm.value.attribute_id,
        );
        selectedAttribute.value = attr || null;
    } else {
        selectedAttribute.value = null;
    }
};

const tableHeaders = ref([
    { key: "name", label: "Attribute Name" },
    { key: "type", label: "Type" },
    { key: "is_required", label: "Required" },
    { key: "sort_order", label: "Sort Order" },
    { key: "actions", label: "Actions" },
]);

const tableRows = computed(() => {
    return attributeCategoriesStore.categoryAttributes.map((attr) => ({
        id: attr.id,
        name: attr.name,
        type: attr.type,
        is_required: attr.is_required_in_category ? "Yes" : "No",
        sort_order: attr.sort_order_in_category,
        actions: [
            {
                label: "Remove",
                icon: "trash",
                class: "bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300",
                onClick: () => handleDetach(attr.id),
            },
        ],
    }));
});

// Get attributes not already assigned to the selected category
const availableAttributes = computed(() => {
    const assignedIds = new Set(
        attributeCategoriesStore.categoryAttributes.map((a) => a.id),
    );
    return attributesStore.attributes.filter(
        (attr) => !assignedIds.has(attr.id),
    );
});

const fetchData = async () => {
    await attributeCategoriesStore.fetchAllCategories();
    await attributesStore.fetchAttributes(1, 100); // fetch all attributes
};

const handleCategoryChange = async () => {
    if (selectedCategoryId.value) {
        await attributeCategoriesStore.fetchAttributesForCategory(
            selectedCategoryId.value,
        );
    }
};

const handleAssignAttribute = async () => {
    if (!selectedCategoryId.value || !assignForm.value.attribute_id) return;

    try {
        await attributeCategoriesStore.assignAttributeToCategory({
            attribute_id: assignForm.value.attribute_id,
            category_id: selectedCategoryId.value,
            is_required: assignForm.value.is_required,
            sort_order: assignForm.value.sort_order,
        });
        showAssignModal.value = false;
        assignForm.value = {
            attribute_id: null,
            is_required: 0, // Reset to 0 (false) when closing the modal
            sort_order: 0,
        };
    } catch (error) {
        console.error("Failed to assign attribute:", error);
    }
};

const handleDetach = async (attributeId) => {
    if (!selectedCategoryId.value) return;
    if (
        !confirm(
            "Are you sure you want to remove this attribute from the category?",
        )
    )
        return;

    try {
        await attributeCategoriesStore.detachAttributeFromCategory(
            attributeId,
            selectedCategoryId.value,
        );
    } catch (error) {
        console.error("Failed to detach attribute:", error);
    }
};

onMounted(async () => {
    await fetchData();
});
</script>
