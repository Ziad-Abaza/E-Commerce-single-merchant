<script setup>
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useSearchStore } from '../../stores/search'

const searchStore = useSearchStore()
const router = useRouter()

searchStore.loadRecentSearches()
const isFocused = ref(false)

// Watch query to fetch suggestions
watch(() => searchStore.query, (newVal) => {
  if (!newVal.trim()) {
    searchStore.suggestions = []
  } else {
    searchStore.fetchSuggestions(newVal)
  }
})

// Handle search submit
const handleSearchSubmit = () => {
  if (!searchStore.query.trim()) return
  searchStore.performSearch()
}
</script>

<template>
  <div class="relative">
    <input
      v-model="searchStore.query"
      type="text"
      placeholder="Search products..."
      class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400"
      @focus="isFocused = true"
      @blur="isFocused = false"
      @keyup.enter="handleSearchSubmit"
    />

    <!-- Clear button -->
    <button
      v-if="searchStore.query"
      @click="searchStore.clearSearch"
      class="absolute inset-y-0 right-0 pr-3 flex items-center"
    >
      ✕
    </button>

    <!-- Suggestions -->
    <div
      v-if="isFocused && searchStore.query && searchStore.suggestions.length"
      class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-50"
    >
      <div
        v-for="s in searchStore.suggestions"
        :key="s.id"
        @mousedown.prevent="() => { searchStore.query = s.name; searchStore.performSearch() }"
        class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer text-gray-800 dark:text-gray-200"
      >
        {{ s.name }}
      </div>
    </div>

    <!-- Recent Searches -->
    <div
      v-else-if="isFocused && !searchStore.query && searchStore.recentSearches.length"
      class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-50"
    >
      <div class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
        Recent Searches
      </div>
      <div
        v-for="r in searchStore.recentSearches"
        :key="r"
        @mousedown.prevent="() => { searchStore.query = r; searchStore.performSearch() }"
        class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer text-gray-800 dark:text-gray-200"
      >
        {{ r }}
      </div>
    </div>
  </div>
</template>
