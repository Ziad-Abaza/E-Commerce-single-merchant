<!-- resources/js/pages/dashboard/Reviews.vue -->
<template>
  <div class="p-2 sm:p-4 md:p-6 space-y-4 sm:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
            Reviews Management
          </h1>
          <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
            Manage customer reviews and ratings
          </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
          <!-- Bulk Actions -->
          <div v-if="reviewsStore.selectedReviews.length > 0" class="flex gap-2">
            <button
              @click="handleBulkActivate"
              class="px-3 sm:px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
              :disabled="reviewsStore.bulkDeleting"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              Activate ({{ reviewsStore.selectedReviews.length }})
            </button>
            <button
              @click="handleBulkDeactivate"
              class="px-3 sm:px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
              :disabled="reviewsStore.bulkDeleting"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
              </svg>
              Deactivate ({{ reviewsStore.selectedReviews.length }})
            </button>
            <button
              @click="handleBulkDelete"
              class="px-3 sm:px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
              :disabled="reviewsStore.bulkDeleting"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Delete ({{ reviewsStore.selectedReviews.length }})
            </button>
          </div>

          <button
            @click="fetchReviews"
            class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh
          </button>
        </div>
      </div>
    </div>

    <!-- Error Alert -->
    <div
      v-if="reviewsStore.error"
      class="p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg animate-in slide-in-from-top-2 duration-300"
    >
      <div class="flex items-center">
        <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="text-sm text-red-700 dark:text-red-300 flex-1">{{ reviewsStore.error }}</span>
        <button
          @click="reviewsStore.clearError"
          class="ml-2 text-red-500 hover:text-red-700 dark:hover:text-red-300 p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
      <!-- Total Reviews -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Total Reviews</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
              {{ reviewsStore.statistics?.total_reviews }}
            </p>
          </div>
          <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Inactive Reviews -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Inactive</p>
            <p class="text-lg font-bold text-yellow-600 dark:text-yellow-400 mt-1">
              {{ reviewsStore.statistics?.inactive_reviews }}
            </p>
          </div>
          <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Active Reviews -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Active</p>
            <p class="text-lg font-bold text-green-600 dark:text-green-400 mt-1">
              {{ reviewsStore.statistics?.active_reviews }}
            </p>
          </div>
          <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Average Rating -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Avg Rating</p>
            <p class="text-lg font-bold text-purple-600 dark:text-purple-400 mt-1">
              {{ parseFloat(reviewsStore.statistics?.average_rating || 0).toFixed(2) }}
            </p>
          </div>
          <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Rating Distribution Chart -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Distribution</p>
            <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-1">
              {{ getRatingDistributionPercentage(5) }}%
            </p>
          </div>
          <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Rating Distribution Bar Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
      <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Rating Distribution</h3>
      <div class="space-y-2">
        <div
          v-for="rating in [5, 4, 3, 2, 1]"
          :key="rating"
          class="flex items-center gap-3"
        >
          <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-8">{{ rating }}★</span>
          <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
            <div
              class="h-full bg-yellow-500 rounded-full"
              :style="{ width: `${getRatingDistributionPercentage(rating)}%` }"
            ></div>
          </div>
          <span class="text-sm text-gray-500 dark:text-gray-400 w-12 text-right">
            {{ getRatingCount(rating) }}
          </span>
        </div>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700">
      <div class="flex flex-col sm:flex-row gap-3 items-end">
        <!-- Search -->
        <div class="flex-1">
          <Search
            v-model="reviewsStore.filters.search"
            placeholder="Search by title or comment..."
            @submit="handleSearch"
          />
        </div>

        <!-- Status Filter -->
        <Select
          v-model="reviewsStore.filters.status"
          :options="statusOptions"
          placeholder="All Statuses"
          label="Status"
          @update:modelValue="handleStatusFilter"
        />

        <!-- Rating Filter -->
        <Select
          v-model="reviewsStore.filters.rating"
          :options="ratingOptions"
          placeholder="All Ratings"
          label="Rating"
          @update:modelValue="handleRatingFilter"
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

    <!-- Bulk Actions -->
    <div
      v-if="reviewsStore.selectedReviews.length > 0"
      class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3"
    >
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="text-sm text-blue-700 dark:text-blue-300">
          {{ reviewsStore.selectedReviews.length }} reviews selected
        </div>
        <div class="flex gap-2">
          <button
            @click="reviewsStore.clearSelection()"
            class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
          >
            Clear Selection
          </button>
          <button
            @click="handleBulkDelete"
            class="px-3 py-1.5 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors flex items-center"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Delete Selected
          </button>
        </div>
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="reviewsStore.loading && !reviewsStore.reviews.length" class="space-y-4">
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

    <!-- Reviews Table -->
    <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
      <!-- Empty State -->
      <div
        v-if="reviewsStore.reviews.length === 0"
        class="text-center py-12"
      >
        <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No reviews found</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-4">
          {{ hasActiveFilters ? 'Try adjusting your filters.' : 'Reviews will appear here once customers submit them.' }}
        </p>
      </div>

      <!-- Reviews Table -->
      <div v-else>
        <Table
          :headers="tableHeaders"
          :rows="tableRows"
        />
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="reviewsStore.reviews.length > 0" class="flex justify-center">
      <Pagination
        :total="reviewsStore.pagination.total"
        :current-page="reviewsStore.pagination.current_page"
        :per-page="reviewsStore.pagination.per_page"
        :last-page="reviewsStore.pagination.last_page"
        @page-change="handlePageChange"
        @update:perPage="handlePerPageChange"
      />
    </div>

    <!-- Modals -->
    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      :show="showDeleteConfirm"
      title="Delete Review"
      :message="`Are you sure you want to delete this review? This action cannot be undone.`"
      confirm-text="Delete"
      confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
      @confirm="handleDelete"
      @cancel="showDeleteConfirm = false"
      :loading="reviewsStore.deleting"
    />

    <!-- Bulk Delete Confirmation Modal -->
    <ConfirmModal
      :show="showBulkDeleteConfirm"
      title="Delete Selected Reviews"
      :message="`Are you sure you want to delete ${reviewsStore.selectedReviews.length} selected reviews? This action cannot be undone.`"
      confirm-text="Delete"
      confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
      @confirm="handleBulkDeleteConfirm"
      @cancel="showBulkDeleteConfirm = false"
      :loading="reviewsStore.bulkDeleting"
    />

    <!-- Bulk Activate Confirmation Modal -->
    <ConfirmModal
      :show="showBulkActivateConfirm"
      title="Activate Selected Reviews"
      :message="`Are you sure you want to activate ${reviewsStore.selectedReviews.length} selected reviews?`"
      confirm-text="Activate"
      confirm-class="bg-green-600 hover:bg-green-700 focus:ring-green-500"
      @confirm="handleBulkActivateConfirm"
      @cancel="showBulkActivateConfirm = false"
      :loading="reviewsStore.bulkDeleting"
    />

    <!-- Bulk Deactivate Confirmation Modal -->
    <ConfirmModal
      :show="showBulkDeactivateConfirm"
      title="Deactivate Selected Reviews"
      :message="`Are you sure you want to deactivate ${reviewsStore.selectedReviews.length} selected reviews?`"
      confirm-text="Deactivate"
      confirm-class="bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500"
      @confirm="handleBulkDeactivateConfirm"
      @cancel="showBulkDeactivateConfirm = false"
      :loading="reviewsStore.bulkDeleting"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useReviewsStore } from '../../stores/dashboard/reviews';
import { useAuthStore } from '../../stores/auth';
import Search from './components/Search.vue';
import Select from './components/Select.vue';
import Table from './components/Table.vue';
import Pagination from './components/Pagination.vue';
import ConfirmModal from './components/ConfirmModal.vue';

const reviewsStore = useReviewsStore();
const authStore = useAuthStore();

const showDeleteConfirm = ref(false);
const showBulkDeleteConfirm = ref(false);
const showBulkActivateConfirm = ref(false);
const showBulkDeactivateConfirm = ref(false);
const reviewToDelete = ref(null);

// Check permissions
if (!authStore.hasPermission('manage_reviews')) {
  throw new Error('Access denied: You do not have permission to manage reviews');
}

// Filter options
const statusOptions = ref([
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
]);

const ratingOptions = ref([
  { value: '5', label: '5 Stars' },
  { value: '4', label: '4 Stars' },
  { value: '3', label: '3 Stars' },
  { value: '2', label: '2 Stars' },
  { value: '1', label: '1 Star' },
]);

// Table headers
const tableHeaders = ref([
  {
    key: 'selection',
    label: '',
    type: 'checkbox',
    get checked() {
      return reviewsStore.reviews.length > 0 && reviewsStore.selectedReviews.length === reviewsStore.reviews.length;
    },
    onChange: () => {
      if (reviewsStore.selectedReviews.length === reviewsStore.reviews.length) {
        reviewsStore.clearSelection();
      } else {
        reviewsStore.selectAllReviews();
      }
    }
  },
  { key: 'product', label: 'Product' },
  { key: 'user', label: 'User' },
  { key: 'rating', label: 'Rating' },
  { key: 'title', label: 'Title' },
  { key: 'status', label: 'Status' },
  { key: 'date', label: 'Date' },
  { key: 'actions', label: 'Actions' },
]);

// Computed properties
const hasActiveFilters = computed(() => {
  return reviewsStore.filters.product_id ||
         reviewsStore.filters.user_id ||
         reviewsStore.filters.rating ||
         reviewsStore.filters.status ||
         reviewsStore.filters.search;
});

const tableRows = computed(() => {
  return reviewsStore.reviews.map(review => ({
    id: review.id,
    selection: {
      type: 'checkbox',
      checked: reviewsStore.selectedReviews.includes(review.id),
      onChange: () => reviewsStore.toggleReviewSelection(review.id)
    },
    product: review.product?.name || 'Unknown Product',
    user: review.user?.name || 'Unknown User',
    rating: `${review.rating}/5 ⭐`,
    title: review.title || 'No Title',
    status: {
      type: 'status',
      value: review.is_active ? 'Active' : 'Inactive',
      class: review.is_active
        ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'
    },
    date: formatDate(review.created_at),
    actions: [
      {
        label: review.active ? 'Deactivate' : 'Activate',
        icon: review.active ? 'eye-slash' : 'eye',
        class: review.active
          ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300'
          : 'bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-300',
        onClick: () => handleToggleActive(review.id)
      },
      {
        label: 'Delete',
        icon: 'trash',
        class: 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300',
        onClick: () => handleDeleteClick(review)
      }
    ]
  }));
});


// Helper functions
const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  try {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    }).format(date);
  } catch (e) {
    return dateString;
  }
};

const getRatingDistributionPercentage = (rating) => {
  const distribution = reviewsStore.statistics.rating_distribution || [];
  const ratingData = distribution.find(r => r.rating === rating);
  const totalCount = reviewsStore.statistics.total_reviews || 1;
  return ratingData ? Math.round((ratingData.count / totalCount) * 100) : 0;
};

const getRatingCount = (rating) => {
  const distribution = reviewsStore.statistics.rating_distribution || [];
  const ratingData = distribution.find(r => r.rating === rating);
  return ratingData ? ratingData.count : 0;
};

// Event handlers
const handleSearch = (searchTerm) => {
  reviewsStore.setFilter('search', searchTerm);
};

const handleStatusFilter = (value) => {
  reviewsStore.setFilter('status', value);
};

const handleRatingFilter = (value) => {
  reviewsStore.setFilter('rating', value);
};

const clearAllFilters = () => {
  reviewsStore.clearFilters();
};

const handlePageChange = (page) => {
  reviewsStore.fetchReviews(page, reviewsStore.pagination.per_page);
};

const handlePerPageChange = (perPage) => {
  const perPageNumber = parseInt(perPage);
  reviewsStore.pagination.per_page = perPageNumber;
  reviewsStore.pagination.current_page = 1;
  reviewsStore.fetchReviews(1, perPageNumber);
};

const fetchReviews = async () => {
  await reviewsStore.fetchReviews(
    reviewsStore.pagination.current_page,
    reviewsStore.pagination.per_page,
  );
};

const handleToggleActive = async (reviewId) => {
  try {
    await reviewsStore.toggleReviewActive(reviewId);
  } catch (error) {
    console.error('Toggle active error:', error);
  }
};

const handleBulkActivate = () => {
  if (reviewsStore.selectedReviews.length === 0) return;
  showBulkActivateConfirm.value = true;
};

const handleBulkActivateConfirm = async () => {
  if (reviewsStore.selectedReviews.length === 0) return;

  try {
    await reviewsStore.bulkActivateReviews(reviewsStore.selectedReviews);
    showBulkActivateConfirm.value = false;
  } catch (error) {
    console.error('Bulk activate error:', error);
  }
};

const handleBulkDeactivate = () => {
  if (reviewsStore.selectedReviews.length === 0) return;
  showBulkDeactivateConfirm.value = true;
};

const handleBulkDeactivateConfirm = async () => {
  if (reviewsStore.selectedReviews.length === 0) return;

  try {
    await reviewsStore.bulkDeactivateReviews(reviewsStore.selectedReviews);
    showBulkDeactivateConfirm.value = false;
  } catch (error) {
    console.error('Bulk deactivate error:', error);
  }
};

const handleDeleteClick = (review) => {
  reviewToDelete.value = review;
  showDeleteConfirm.value = true;
};

const handleDelete = async () => {
  if (!reviewToDelete.value) return;

  try {
    await reviewsStore.deleteReview(reviewToDelete.value.id);
    showDeleteConfirm.value = false;
    reviewToDelete.value = null;
  } catch (error) {
    console.error('Delete error:', error);
  }
};

const handleBulkDelete = () => {
  if (reviewsStore.selectedReviews.length === 0) return;
  showBulkDeleteConfirm.value = true;
};

const handleBulkDeleteConfirm = async () => {
  if (reviewsStore.selectedReviews.length === 0) return;

  try {
    await reviewsStore.bulkDeleteReviews(reviewsStore.selectedReviews);
    showBulkDeleteConfirm.value = false;
  } catch (error) {
    console.error('Bulk delete error:', error);
  }
};

// Lifecycle
onMounted(async () => {
  await fetchReviews();
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
