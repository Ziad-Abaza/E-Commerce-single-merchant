<!-- resources/js/pages/dashboard/Categories.vue -->
<template>
  <div class="p-2 sm:p-4 md:p-6 space-y-4 sm:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
            Categories Management
          </h1>
          <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
            Manage your product categories and organize your catalog
          </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
          <button
            @click="fetchCategories"
            class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh
          </button>
          <button
            @click="openCreateForm"
            class="px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add Category
          </button>
        </div>
      </div>
    </div>

    <!-- Error Alert -->
    <div
      v-if="categoriesStore.error"
      class="p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg animate-in slide-in-from-top-2 duration-300"
    >
      <div class="flex items-center">
        <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="text-sm text-red-700 dark:text-red-300 flex-1">{{ categoriesStore.error }}</span>
        <button
          @click="categoriesStore.clearError"
          class="ml-2 text-red-500 hover:text-red-700 dark:hover:text-red-300 p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
      <!-- Total Categories -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Total Categories</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
              {{ categoriesStore.statistics?.total }}
            </p>
          </div>
          <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Active Categories -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Active</p>
            <p class="text-lg font-bold text-green-600 dark:text-green-400 mt-1">
              {{ categoriesStore.statistics?.active }}
            </p>
          </div>
          <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Inactive Categories -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Inactive</p>
            <p class="text-lg font-bold text-red-600 dark:text-red-400 mt-1">
              {{ categoriesStore.statistics?.inactive }}
            </p>
          </div>
          <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Root Categories -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Root Categories</p>
            <p class="text-lg font-bold text-purple-600 dark:text-purple-400 mt-1">
              {{ categoriesStore.statistics?.root }}
            </p>
          </div>
          <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
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
          <Search
            v-model="categoriesStore.filters.search"
            placeholder="Search categories by name or description..."
            @submit="handleSearch"
          />
        </div>

        <!-- Parent Category Filter -->
        <Select
          v-model="categoriesStore.filters.parent_id"
          :options="parentCategoryOptions"
          placeholder="All Categories"
          label="Parent Category"
          @update:modelValue="handleParentFilter"
        />

        <!-- Status Filter -->
        <Select
          v-model="categoriesStore.filters.status"
          :options="statusOptions"
          placeholder="All Statuses"
          label="Status"
          @update:modelValue="handleStatusFilter"
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
    <div v-if="categoriesStore.loading" class="space-y-4">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
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

    <!-- Categories Table -->
    <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
      <!-- Empty State -->
      <div
        v-if="categoriesStore.categories.length === 0"
        class="text-center py-12"
      >
        <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No categories found</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-4">
          {{ hasActiveFilters ? 'Try adjusting your filters.' : 'Get started by adding your first category.' }}
        </p>
        <button
          v-if="!hasActiveFilters"
          @click="openCreateForm"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
        >
          Add Your First Category
        </button>
      </div>

      <!-- Categories Table -->
      <div v-else>
        <Table
          :headers="tableHeaders"
          :rows="tableRows"
        />
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="categoriesStore.categories.length > 0" class="flex justify-center">
      <Pagination
        :total="categoriesStore.pagination.total"
        :current-page="categoriesStore.pagination.current_page"
        :per-page="categoriesStore.pagination.per_page"
        :last-page="categoriesStore.pagination.last_page"
        :from="categoriesStore.pagination.from"
        :to="categoriesStore.pagination.to"
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
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
              {{ isEditing ? 'Edit Category' : 'Add New Category' }}
            </h3>
            <button
              @click="showFormModal = false"
              class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      :show="showDeleteConfirm"
      title="Delete Category"
      :message="`Are you sure you want to delete '${categoryToDelete?.name || 'this category'}'? This action cannot be undone.`"
      confirm-text="Delete"
      confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
      @confirm="handleDelete"
      @cancel="showDeleteConfirm = false"
      :loading="categoriesStore.deleting"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useCategoriesStore } from '../../stores/dashboard/categories';
import { useAuthStore } from '../../stores/auth';
import Search from './components/Search.vue';
import Select from './components/Select.vue';
import Table from './components/Table.vue';
import Pagination from './components/Pagination.vue';
import Form from './components/Form.vue';
import ConfirmModal from './components/ConfirmModal.vue';

const categoriesStore = useCategoriesStore();
const authStore = useAuthStore();

const showFormModal = ref(false);
const isEditing = ref(false);
const showDeleteConfirm = ref(false);
const categoryToDelete = ref(null);
const formFields = ref([]);

// Check permissions
if (!authStore.hasPermission('manage_categories')) {
  throw new Error('Access denied: You do not have permission to manage categories');
}

// Filter options
const statusOptions = ref([
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
]);

const parentCategoryOptions = computed(() => {
  return [
    { value: '', label: 'Root Category' },
    ...categoriesStore.categories
      .filter(cat => !cat.parent_id)
      .map(cat => ({
        value: cat.id.toString(),
        label: cat.name
      }))
  ];
});

// Table headers
const tableHeaders = ref([
  { key: 'name', label: 'Category Name' },
  { key: 'parent', label: 'Parent Category' },
  { key: 'products_count', label: 'Products' },
  { key: 'status', label: 'Status' },
  { key: 'actions', label: 'Actions' },
]);

// Computed properties
const hasActiveFilters = computed(() => {
  return categoriesStore.filters.search ||
         categoriesStore.filters.parent_id ||
         categoriesStore.filters.status;
});

const tableRows = computed(() => {
  return categoriesStore.categories.map(category => ({
    id: category.id,
    name: category.name,
    parent: category.parent ? category.parent.name : 'Root',
    products_count: category.products_count || 0,
    status: category.is_active ? 'Active' : 'Inactive',
    actions: [
      {
        label: 'Edit',
        icon: 'edit',
        class: 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300',
        onClick: () => handleEdit(category)
      },
      {
        label: 'Delete',
        icon: 'trash',
        class: 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300',
        onClick: () => handleDeleteClick(category)
      }
    ]
  }));
});

// Event handlers
const handleSearch = (searchTerm) => {
  categoriesStore.setFilter('search', searchTerm);
  categoriesStore.fetchCategories(1, categoriesStore.pagination.per_page);
};


const handleParentFilter = (value) => {
  categoriesStore.setFilter('parent_id', value);
  categoriesStore.fetchCategories(1, categoriesStore.pagination.per_page);
};

const handleStatusFilter = (value) => {
  categoriesStore.setFilter('status', value);
  categoriesStore.fetchCategories(1, categoriesStore.pagination.per_page);
};


const clearAllFilters = () => {
  categoriesStore.clearFilters();
};

const handlePageChange = (page) => {
  categoriesStore.fetchCategories(page, categoriesStore.pagination.per_page);
};

const handlePerPageChange = (perPage) => {
  const perPageNumber = parseInt(perPage);
  categoriesStore.pagination.per_page = perPageNumber;
  categoriesStore.pagination.current_page = 1;
  categoriesStore.fetchCategories(1, perPageNumber);
};

const fetchCategories = async () => {
  await categoriesStore.fetchCategories(
    categoriesStore.pagination.current_page,
    categoriesStore.pagination.per_page
  );
};

const openCreateForm = () => {
  isEditing.value = false;
  categoryToDelete.value = null;
  initializeFormFields(null);
  showFormModal.value = true;
};

const handleEdit = async (category) => {
  isEditing.value = true;
  categoryToDelete.value = category;

  try {
    await categoriesStore.fetchCategory(category.id);
    initializeFormFields(categoriesStore.currentCategory);
    showFormModal.value = true;
  } catch (error) {
    console.error("Error loading category for edit:", error);
  }
};

const initializeFormFields = (category) => {
  // Get parent categories for select options
  const parentOptions = [
    { value: '', label: 'None (Root Category)' },
    ...categoriesStore.categories
      .filter(cat => cat.id !== category?.id) // Exclude current category
      .map(cat => ({
        value: cat.id.toString(),
        label: cat.name
      }))
  ];

  formFields.value = [
    {
      id: 'name',
      label: 'Category Name',
      type: 'text',
      value: category?.name || '',
      required: true,
      placeholder: 'Enter category name'
    },
    {
      id: 'slug',
      label: 'Slug (URL)',
      type: 'text',
      value: category?.slug || '',
      required: false,
      placeholder: 'Enter slug (auto-generated if empty)'
    },
    {
      id: 'parent_id',
      label: 'Parent Category',
      type: 'select',
      value: category?.parent_id ? category.parent_id.toString() : '',
      options: parentOptions,
      required: false,
      placeholder: 'Select parent category'
    },
    {
      id: 'description',
      label: 'Description',
      type: 'textarea',
      value: category?.description || '',
      required: false,
      placeholder: 'Enter category description'
    },
    {
      id: 'sort_order',
      label: 'Sort Order',
      type: 'number',
      value: category?.sort_order || 0,
      required: false,
      placeholder: 'Enter sort order'
    },
    {
      id: 'is_active',
      label: 'Active Status',
      type: 'checkbox',
      value: category?.is_active || false,
      required: false
    },
    {
      id: 'thumbnail',
      label: 'Thumbnail image',
      type: 'file',
      value: category?.thumbnail_url || '',
      required: false,
      placeholder: 'Enter thumbnail URL'
    },
    {
      id: 'icon',
      label: 'Icon image',
      type: 'file',
      value: category?.icon_url || '',
      required: false,
      placeholder: 'Enter icon URL'
    }
  ];
};

const handleDeleteClick = (category) => {
  categoryToDelete.value = category;
  showDeleteConfirm.value = true;
};

const handleSubmitForm = async (data) => {
  try {
    if (isEditing.value && categoryToDelete.value) {
      // Update existing category
      const updatedData = {
        ...data,
        parent_id: data.parent_id ? parseInt(data.parent_id) : null
      };

      await categoriesStore.updateCategory(categoryToDelete.value.id, updatedData);
    } else {
      // Create new category
      const createData = {
        ...data,
        parent_id: data.parent_id ? parseInt(data.parent_id) : null
      };

      await categoriesStore.createCategory(createData);
    }

    // Refresh the list
    await fetchCategories();

    // Close the form
    showFormModal.value = false;
    formFields.value = [];
    categoryToDelete.value = null;

  } catch (error) {
    console.error('Form submission error:', error);
  }
};

const handleDelete = async () => {
  if (!categoryToDelete.value) return;

  try {
    await categoriesStore.deleteCategory(categoryToDelete.value.id);
    showDeleteConfirm.value = false;
    categoryToDelete.value = null;
  } catch (error) {
    console.error('Delete error:', error);
  }
};

// Lifecycle
onMounted(async () => {
  await fetchCategories();
});

// Watch for route changes or other updates
watch(
  () => [categoriesStore.filters.search, categoriesStore.filters.parent_id, categoriesStore.filters.status],
  () => {
    categoriesStore.fetchCategories(1, categoriesStore.pagination.per_page);
  },
  { deep: true }
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
