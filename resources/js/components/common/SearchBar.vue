<script setup>
import { ref, watch } from 'vue'
import { useSearchStore } from '../../stores/search'

const searchStore = useSearchStore()
searchStore.loadRecentSearches()

const isFocused = ref(false)

watch(() => searchStore.query, (newVal) => {
  if (!newVal.trim()) {
    searchStore.suggestions = []
  } else {
    searchStore.fetchSuggestions(newVal)
  }
})
</script>

<template>
  <div class="relative">
    <input
      v-model="searchStore.query"
      type="text"
      placeholder="Search products..."
      class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
      @focus="isFocused = true"
      @blur="isFocused = false"
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
      class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg z-50"
    >
      <div
        v-for="s in searchStore.suggestions"
        :key="s.id"
        @mousedown.prevent="() => { searchStore.query = s.name; searchStore.performSearch() }"
        class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
      >
        {{ s.name }}
      </div>
    </div>

    <!-- Recent Searches -->
    <div
      v-else-if="isFocused && !searchStore.query && searchStore.recentSearches.length"
      class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg z-50"
    >
      <div class="px-4 py-2 text-xs text-gray-500">Recent Searches</div>
      <div
        v-for="r in searchStore.recentSearches"
        :key="r"
        @mousedown.prevent="() => { searchStore.query = r; searchStore.performSearch() }"
        class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
      >
        {{ r }}
      </div>
    </div>
  </div>
</template>
