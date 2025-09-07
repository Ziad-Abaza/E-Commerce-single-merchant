<!-- components/ReviewList.vue -->
<template>
  <div class="bg-white rounded-lg shadow border border-gray-200">
    <div class="p-6 border-b border-gray-200">
      <h3 class="text-lg font-medium text-gray-900">Customer Reviews</h3>
      <div v-if="stats" class="mt-2 flex items-center space-x-4">
        <div class="flex items-center">
          <StarRating :model-value="stats.average_rating" :show-rating="true" />
          <span class="ml-2 text-sm text-gray-600">({{ stats.total_reviews }} reviews)</span>
        </div>
      </div>
    </div>

    <div v-if="loading" class="p-6 flex justify-center">
      <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary-600"></div>
    </div>

    <div v-else-if="reviews.length === 0" class="p-6 text-center text-gray-500">
      No reviews yet. Be the first to write a review!
    </div>

    <div v-else class="divide-y divide-gray-200">
      <div v-for="review in reviews" :key="review.id" class="p-6">
        <div class="flex items-start space-x-4">
          <!-- User Avatar -->
          <div class="flex-shrink-0">
            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
              <span class="text-sm font-medium text-gray-700">
                {{ review.user.name.charAt(0).toUpperCase() }}
              </span>
            </div>
          </div>

          <!-- Review Content -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <h4 class="text-sm font-medium text-gray-900">{{ review.user.name }}</h4>
              <span class="text-xs text-gray-500">{{ formatDate(review.created_at) }}</span>
            </div>

            <div class="flex items-center mt-1">
              <StarRating :model-value="review.rating" />
              <span v-if="review.is_verified_purchase" class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                Verified Purchase
              </span>
            </div>

            <h5 v-if="review.title" class="mt-2 text-sm font-medium text-gray-900">{{ review.title }}</h5>
            <p class="mt-1 text-sm text-gray-600">{{ review.comment }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.lastPage > 1" class="px-6 py-4 border-t border-gray-200">
      <nav class="flex items-center justify-between">
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            @click="changePage(pagination.currentPage - 1)"
            :disabled="pagination.currentPage === 1"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
          >
            Previous
          </button>
          <button
            @click="changePage(pagination.currentPage + 1)"
            :disabled="pagination.currentPage === pagination.lastPage"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
          >
            Next
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Showing
              <span class="font-medium">{{ (pagination.currentPage - 1) * pagination.perPage + 1 }}</span>
              to
              <span class="font-medium">{{ Math.min(pagination.currentPage * pagination.perPage, pagination.total) }}</span>
              of
              <span class="font-medium">{{ pagination.total }}</span>
              reviews
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button
                @click="changePage(pagination.currentPage - 1)"
                :disabled="pagination.currentPage === 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                :class="{ 'cursor-not-allowed': pagination.currentPage === 1 }"
              >
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </button>

              <button
                v-for="page in visiblePages"
                :key="page"
                @click="changePage(page)"
                :class="[
                  page === pagination.currentPage
                    ? 'z-10 bg-primary-50 border-primary-500 text-primary-600'
                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                ]"
              >
                {{ page }}
              </button>

              <button
                @click="changePage(pagination.currentPage + 1)"
                :disabled="pagination.currentPage === pagination.lastPage"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                :class="{ 'cursor-not-allowed': pagination.currentPage === pagination.lastPage }"
              >
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </button>
            </nav>
          </div>
        </div>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useReviewStore } from '@/stores/reviews'
import StarRating from './StarRating.vue'

const props = defineProps({
  productId: {
    type: Number,
    required: true,
  },
})

const reviewStore = useReviewStore()
const loading = ref(false)
const reviews = computed(() => reviewStore.reviews)
const pagination = computed(() => reviewStore.pagination)
const stats = ref(null)

// Calculate visible pages for pagination (show max 5 pages)
const visiblePages = computed(() => {
  const currentPage = pagination.value.currentPage
  const lastPage = pagination.value.lastPage
  const pages = []

  if (lastPage <= 5) {
    for (let i = 1; i <= lastPage; i++) {
      pages.push(i)
    }
  } else {
    pages.push(1)
    if (currentPage > 3) pages.push('...')

    const start = Math.max(2, currentPage - 1)
    const end = Math.min(lastPage - 1, currentPage + 1)

    for (let i = start; i <= end; i++) {
      pages.push(i)
    }

    if (currentPage < lastPage - 2) pages.push('...')
    pages.push(lastPage)
  }

  return pages
})

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const loadReviews = async (page = 1) => {
  loading.value = true
  try {
    await reviewStore.loadReviews(props.productId, page)
  } catch (error) {
    console.error('Failed to load reviews:', error)
  } finally {
    loading.value = false
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.lastPage) {
    loadReviews(page)
  }
}

// Load product rating stats
const loadStats = async () => {
  try {
    const result = await reviewStore.getProductRatingStats(props.productId)
    if (result.success) {
      stats.value = result.data
    }
  } catch (error) {
    console.error('Failed to load rating stats:', error)
  }
}

onMounted(async () => {
  await Promise.all([
    loadReviews(1),
    loadStats(),
  ])
})
</script>
