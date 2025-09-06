<script setup>
import { useSearchStore } from '../stores/search'
import { onMounted } from 'vue'
import ProductCard from '../components/common/ProductCard.vue'
import ProductListItem from '../components/common/ProductListItem.vue'

const searchStore = useSearchStore()

onMounted(() => {
  if (searchStore.query) {
    searchStore.performSearch()
  }
})
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-4">Search Results</h1>

    <div v-if="searchStore.loading" class="text-center py-12">Loading...</div>

    <div v-else-if="!searchStore.results.length">
      <p class="text-gray-500">No results found</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <ProductCard v-for="p in searchStore.results" :key="p.id" :product="p" />
    </div>
  </div>
</template>
