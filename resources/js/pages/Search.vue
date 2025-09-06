<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Search Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Search Results</h1>
      <p v-if="searchQuery" class="mt-2 text-gray-600">
        Results for "{{ searchQuery }}"
      </p>
    </div>

    <!-- Search Filters -->
    <div class="mb-8">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Search Input -->
          <div class="md:col-span-2">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              id="search"
              v-model="localSearchQuery"
              @keyup.enter="performSearch"
              type="text"
              placeholder="Search products..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
            />
          </div>

          <!-- Category Filter -->
          <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select
              id="category"
              v-model="filters.category"
              @change="performSearch"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
            >
              <option value="">All Categories</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>

          <!-- Sort Filter -->
          <div>
            <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
            <select
              id="sort"
              v-model="filters.sort"
              @change="performSearch"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
            >
              <option value="created_at">Newest First</option>
              <option value="name">Name A-Z</option>
              <option value="price_asc">Price: Low to High</option>
              <option value="price_desc">Price: High to Low</option>
              <option value="rating">Highest Rated</option>
            </select>
          </div>
        </div>

        <!-- Price Range -->
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
          <div class="flex items-center space-x-4">
            <input
              v-model="filters.minPrice"
              @change="performSearch"
              type="number"
              placeholder="Min"
              class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
            />
            <span class="text-gray-500">to</span>
            <input
              v-model="filters.maxPrice"
              @change="performSearch"
              type="number"
              placeholder="Max"
              class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
            />
            <button
              @click="clearFilters"
              class="text-sm text-primary-600 hover:text-primary-500"
            >
              Clear Filters
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="productStore.loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
    </div>

    <!-- No Results -->
    <div v-else-if="searchResults.length === 0 && searchQuery" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">No results found</h3>
      <p class="mt-1 text-sm text-gray-500">Try adjusting your search terms or filters.</p>
    </div>

    <!-- Results -->
    <div v-else>
      <!-- Results Header -->
      <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <span class="text-sm text-gray-600">
            {{ searchResults.length }} results found
          </span>
          <div class="flex items-center space-x-2">
            <button
              @click="viewMode = 'grid'"
              :class="viewMode === 'grid' ? 'text-primary-600' : 'text-gray-400'"
              class="p-2 hover:text-primary-600 transition-colors"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
              </svg>
            </button>
            <button
              @click="viewMode = 'list'"
              :class="viewMode === 'list' ? 'text-primary-600' : 'text-gray-400'"
              class="p-2 hover:text-primary-600 transition-colors"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Grid View -->
      <div v-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <ProductCard v-for="product in searchResults" :key="product.id" :product="product" />
      </div>

      <!-- List View -->
      <div v-else class="space-y-4">
        <ProductListItem v-for="product in searchResults" :key="product.id" :product="product" />
      </div>

      <!-- Pagination -->
      <div v-if="productStore.pagination.lastPage > 1" class="mt-8 flex justify-center">
        <nav class="flex items-center space-x-2">
          <button
            @click="changePage(productStore.pagination.currentPage - 1)"
            :disabled="productStore.pagination.currentPage === 1"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Previous
          </button>
          
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="changePage(page)"
            :class="page === productStore.pagination.currentPage 
              ? 'bg-primary-600 text-white' 
              : 'text-gray-700 bg-white hover:bg-gray-50'"
            class="px-3 py-2 text-sm font-medium border border-gray-300 rounded-md"
          >
            {{ page }}
          </button>
          
          <button
            @click="changePage(productStore.pagination.currentPage + 1)"
            :disabled="productStore.pagination.currentPage === productStore.pagination.lastPage"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProductStore } from '../stores/products'
import ProductCard from '../components/common/ProductCard.vue'
import ProductListItem from '../components/common/ProductListItem.vue'

const route = useRoute()
const router = useRouter()
const productStore = useProductStore()

const localSearchQuery = ref('')
const viewMode = ref('grid')
const filters = ref({
  category: '',
  minPrice: '',
  maxPrice: '',
  sort: 'created_at'
})

const searchQuery = computed(() => route.query.q || '')
const searchResults = computed(() => productStore.searchResults)
const categories = computed(() => productStore.categories)

const visiblePages = computed(() => {
  const current = productStore.pagination.currentPage
  const last = productStore.pagination.lastPage
  const pages = []
  
  const start = Math.max(1, current - 2)
  const end = Math.min(last, current + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

const performSearch = async () => {
  const searchParams = {
    q: localSearchQuery.value || searchQuery.value,
    ...filters.value
  }
  
  // Remove empty values
  Object.keys(searchParams).forEach(key => {
    if (!searchParams[key]) {
      delete searchParams[key]
    }
  })
  
  // Update URL
  router.push({
    name: 'search',
    query: searchParams
  })
  
  // Perform search
  await productStore.searchProducts(searchParams.q, searchParams)
}

const clearFilters = () => {
  filters.value = {
    category: '',
    minPrice: '',
    maxPrice: '',
    sort: 'created_at'
  }
  performSearch()
}

const changePage = async (page) => {
  if (page < 1 || page > productStore.pagination.lastPage) return
  
  const searchParams = {
    q: localSearchQuery.value || searchQuery.value,
    ...filters.value,
    page
  }
  
  await productStore.searchProducts(searchParams.q, searchParams)
}

// Watch for route changes
watch(() => route.query.q, (newQuery) => {
  if (newQuery) {
    localSearchQuery.value = newQuery
    performSearch()
  }
}, { immediate: true })

onMounted(async () => {
  // Load categories
  await productStore.loadCategories()
  
  // Perform initial search if query exists
  if (searchQuery.value) {
    localSearchQuery.value = searchQuery.value
    await performSearch()
  }
})
</script>
