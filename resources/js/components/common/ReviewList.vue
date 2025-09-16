<!-- components/ReviewList.vue -->
<template>
  <div class="bg-white rounded-lg shadow border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    <!-- Header with Stats -->
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
      <h3 class="text-lg font-medium text-gray-900 dark:text-white">Customer Reviews</h3>

      <div v-if="stats" class="mt-4 space-y-4">
        <!-- Average Rating & Total -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div class="flex items-center">
            <StarRating :model-value="stats.average_rating" :size="24" :show-rating="true" class="text-yellow-500" />
            <span class="ml-2 text-2xl font-bold text-gray-900 dark:text-white">{{ stats.average_rating }}</span>
            <span class="mx-1 text-gray-500 dark:text-gray-400">•</span>
            <span class="text-sm text-gray-600 dark:text-gray-300">({{ stats.total_reviews }} reviews)</span>
          </div>
        </div>

        <!-- Rating Distribution Bars -->
        <div class="space-y-3">
          <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Rating Breakdown</h4>
          <div class="space-y-2">
            <div v-for="star in [5, 4, 3, 2, 1]" :key="star" class="flex items-center space-x-3">
              <!-- Star Icon + Number (SVG inline) -->
              <div class="flex items-center w-8">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 text-yellow-500">
                  <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium text-gray-700 ml-1 dark:text-gray-300">{{ star }}</span>
              </div>

              <!-- Progress Bar -->
              <div class="flex-1 bg-gray-200 rounded-full h-2.5 dark:bg-gray-600">
                <div
                  class="h-2.5 rounded-full bg-yellow-500 transition-all duration-500 ease-out"
                  :style="{ width: getBarWidth(star) + '%' }"
                ></div>
              </div>

              <!-- Count & Percentage -->
              <div class="text-right w-16">
                <span class="text-xs text-gray-600 dark:text-gray-400">
                  {{ getRatingCount(star) }} ({{ getRatingPercentage(star) }}%)
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading && reviews.length === 0" class="p-6 flex justify-center">
      <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary-600 dark:border-primary-500"></div>
    </div>

    <!-- No Reviews -->
    <div v-else-if="reviews.length === 0" class="p-6 text-center text-gray-500 dark:text-gray-400">
      No reviews yet. Be the first to write a review!
    </div>

    <!-- Reviews List -->
    <div v-else class="divide-y divide-gray-200 dark:divide-gray-700">
      <div v-for="review in displayedReviews" :key="review.id" class="p-6">
        <div class="flex items-start space-x-4">
          <!-- User Avatar -->
          <div class="flex-shrink-0">
            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center dark:bg-gray-600">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ review.user.name.charAt(0).toUpperCase() }}
              </span>
            </div>
          </div>

          <!-- Review Content -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ review.user.name }}</h4>
              <span class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(review.created_at) }}</span>
            </div>

            <div class="flex items-center mt-1">
              <StarRating :model-value="review.rating" />
              <span v-if="review.is_verified_purchase" class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                Verified Purchase
              </span>
            </div>

            <h5 v-if="review.title" class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ review.title }}</h5>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ review.comment }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Load More Button -->
    <div v-if="hasMorePages" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 text-center">
      <button
        @click="loadMore"
        :disabled="loadingMore"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 dark:bg-primary-700 dark:hover:bg-primary-800"
      >
        <span v-if="!loadingMore">Load More</span>
        <div v-else class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></div>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useReviewStore } from '../../stores/reviews'
import StarRating from './StarRating.vue'

const props = defineProps({
  productId: {
    type: Number,
    required: true,
  },
})

const reviewStore = useReviewStore()
const loading = ref(false)
const loadingMore = ref(false)
const currentPage = ref(1)
const allReviews = ref([])

const reviews = computed(() => allReviews.value)
const displayedReviews = computed(() => {
  return reviews.value.slice(0, 4 + (currentPage.value - 1) * 15)
})

const hasMorePages = computed(() => {
  const totalLoaded = displayedReviews.value.length
  return totalLoaded < reviewStore.pagination.total && !loading.value
})

const stats = ref(null)

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

// Get rating count for specific star
const getRatingCount = (star) => {
  if (!stats.value || !stats.value.rating_distribution) return 0
  const rating = stats.value.rating_distribution.find(r => r.rating === star)
  return rating ? rating.count : 0
}

// Get percentage for specific star
const getRatingPercentage = (star) => {
  if (!stats.value || !stats.value.rating_distribution || stats.value.total_reviews === 0) return 0
  const count = getRatingCount(star)
  return Math.round((count / stats.value.total_reviews) * 100)
}

// Get bar width percentage for progress bar
const getBarWidth = (star) => {
  return getRatingPercentage(star)
}

const loadReviews = async (page = 1) => {
  const isFirstLoad = page === 1
  if (isFirstLoad) {
    loading.value = true
  } else {
    loadingMore.value = true
  }

  try {
    const result = await reviewStore.loadReviews(props.productId, page)
    if (result.success) {
      if (page === 1) {
        allReviews.value = result.data.data || []
      } else {
        allReviews.value = [...allReviews.value, ...(result.data.data || [])]
      }
      currentPage.value = page
    }
  } catch (error) {
    console.error('Failed to load reviews:', error)
  } finally {
    if (isFirstLoad) {
      loading.value = false
    } else {
      loadingMore.value = false
    }
  }
}

const loadMore = () => {
  if (hasMorePages.value) {
    loadReviews(currentPage.value + 1)
  }
}

// Load product rating stats
const loadStats = async () => {
  try {
    const result = await reviewStore.getProductRatingStats(props.productId)
    if (result.success) {
      stats.value = result.data.data
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

<style scoped>
/* Smooth transition for progress bars */
.bg-yellow-500 {
  transition: width 0.5s ease-out;
}
</style>
