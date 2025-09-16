<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Category Header -->
    <div v-if="category" class="mb-8">
      <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-4">
          <li>
            <router-link to="/" class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-300">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
            </router-link>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
              <router-link to="/products" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                Products
              </router-link>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
              <span class="ml-4 text-sm font-medium text-gray-500 dark:text-gray-400">{{ category.name }}</span>
            </div>
          </li>
        </ol>
      </nav>

      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ category.name }}</h1>
      <p v-if="category.description" class="mt-2 text-gray-600 dark:text-gray-300">{{ category.description }}</p>
    </div>

    <!-- Loading State -->
    <div v-if="productStore.loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 dark:border-primary-500"></div>
    </div>

    <!-- No Products -->
    <div v-else-if="products.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No products found</h3>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This category doesn't have any products yet.</p>
      <div class="mt-6">
        <router-link to="/products" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 dark:bg-primary-700 dark:hover:bg-primary-800">
          Browse All Products
        </router-link>
      </div>
    </div>

    <!-- Products -->
    <div v-else>
      <!-- Filters and Sort -->
      <div class="mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 dark:bg-gray-800 dark:border-gray-700">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <!-- Sort Options -->
            <div class="flex items-center space-x-4">
              <label for="sort" class="text-sm font-medium text-gray-700 dark:text-gray-300">Sort by:</label>
              <select
                id="sort"
                v-model="sortBy"
                @change="loadProducts"
                class="px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="created_at">Newest First</option>
                <option value="name">Name A-Z</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
                <option value="rating">Highest Rated</option>
              </select>
            </div>

            <!-- View Mode Toggle -->
            <div class="flex items-center space-x-2">
              <button
                @click="viewMode = 'grid'"
                :class="viewMode === 'grid' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 dark:text-gray-500'"
                class="p-2 hover:text-primary-600 transition-colors dark:hover:text-primary-400"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
              </button>
              <button
                @click="viewMode = 'list'"
                :class="viewMode === 'list' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 dark:text-gray-500'"
                class="p-2 hover:text-primary-600 transition-colors dark:hover:text-primary-400"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Results Header -->
      <div class="mb-6">
        <p class="text-sm text-gray-600 dark:text-gray-400">
          {{ products.length }} products in {{ category?.name }}
        </p>
      </div>

      <!-- Grid View -->
      <div v-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <ProductCard v-for="product in products" :key="product.id" :product="product" />
      </div>

      <!-- List View -->
      <div v-else class="space-y-4">
        <ProductListItem v-for="product in products" :key="product.id" :product="product" />
      </div>

      <!-- Pagination -->
      <div v-if="productStore.pagination.lastPage > 1" class="mt-8 flex justify-center">
        <nav class="flex items-center space-x-2">
          <button
            @click="changePage(productStore.pagination.currentPage - 1)"
            :disabled="productStore.pagination.currentPage === 1"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
          >
            Previous
          </button>

          <button
            v-for="page in visiblePages"
            :key="page"
            @click="changePage(page)"
            :class="page === productStore.pagination.currentPage
              ? 'bg-primary-600 text-white dark:bg-primary-700'
              : 'text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'"
            class="px-3 py-2 text-sm font-medium border border-gray-300 rounded-md dark:border-gray-600"
          >
            {{ page }}
          </button>

          <button
            @click="changePage(productStore.pagination.currentPage + 1)"
            :disabled="productStore.pagination.currentPage === productStore.pagination.lastPage"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
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
import { useRoute } from 'vue-router'
import { useProductStore } from '../stores/products'
import ProductCard from '../components/common/ProductCard.vue'
import ProductListItem from '../components/common/ProductListItem.vue'

const route = useRoute()
const productStore = useProductStore()

const viewMode = ref('grid')
const sortBy = ref('created_at')

const categoryId = computed(() => route.params.id)
const category = computed(() => productStore.getCategoryById(categoryId.value))
const products = computed(() => productStore.products)

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

const loadProducts = async () => {
  if (!categoryId.value) return

  const params = {
    sort: sortBy.value,
    page: productStore.pagination.currentPage
  }

  await productStore.getProductsByCategory(categoryId.value, params)
}

const changePage = async (page) => {
  if (page < 1 || page > productStore.pagination.lastPage) return

  const params = {
    sort: sortBy.value,
    page
  }

  await productStore.getProductsByCategory(categoryId.value, params)
}

// Watch for route changes
watch(() => route.params.id, () => {
  loadProducts()
}, { immediate: true })

onMounted(async () => {
  // Load categories first
  await productStore.loadCategories()

  // Load products for the category
  await loadProducts()
})
</script>
