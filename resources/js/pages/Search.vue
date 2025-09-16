<script setup>
import { useSearchStore } from '../stores/search'
import { onMounted } from 'vue'
import ProductCard from '../components/common/ProductCard.vue'

const searchStore = useSearchStore()

onMounted(() => {
  if (searchStore.query) {
    searchStore.performSearch()
  }
})
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Title -->
    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">
      Search Results
      <span v-if="searchStore.query" class="text-primary-600 dark:text-primary-500">
        for "{{ searchStore.query }}"
      </span>
    </h1>

    <!-- Loading -->
    <div v-if="searchStore.loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-4 border-primary-600 dark:border-primary-500 mx-auto mb-4"></div>
      <p class="text-gray-600 dark:text-gray-400">Searching for products...</p>
    </div>

    <!-- No Results -->
    <div v-else-if="!searchStore.results.length" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>
      <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No results found</h2>
      <p class="text-gray-500 dark:text-gray-400">
        We couldn't find any products matching your search.
        Try different keywords or check your spelling.
      </p>
    </div>

    <!-- Results Grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <ProductCard v-for="p in searchStore.results" :key="p.id" :product="p" />
    </div>
  </div>
</template>
