<template>
  <div class="relative">
    <div class="relative">
      <input
        v-model="searchQuery"
        @keyup.enter="handleSearch"
        @input="handleInput"
        type="text"
        placeholder="Search products..."
        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
      />
      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>
      
      <!-- Clear Button -->
      <button
        v-if="searchQuery"
        @click="clearSearch"
        class="absolute inset-y-0 right-0 pr-3 flex items-center"
      >
        <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Search Suggestions -->
    <div 
      v-if="showSuggestions && suggestions.length > 0"
      class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg z-50 max-h-64 overflow-y-auto"
    >
      <div class="py-2">
        <div 
          v-for="suggestion in suggestions" 
          :key="suggestion.id"
          @click="selectSuggestion(suggestion)"
          class="px-4 py-2 hover:bg-gray-50 cursor-pointer flex items-center"
        >
          <svg class="h-4 w-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <div>
            <div class="text-sm font-medium text-gray-900">{{ suggestion.name }}</div>
            <div class="text-xs text-gray-500">{{ suggestion.category?.name }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Searches -->
    <div 
      v-if="showSuggestions && suggestions.length === 0 && recentSearches.length > 0"
      class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg z-50"
    >
      <div class="py-2">
        <div class="px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wide">
          Recent Searches
        </div>
        <div 
          v-for="search in recentSearches" 
          :key="search"
          @click="selectRecentSearch(search)"
          class="px-4 py-2 hover:bg-gray-50 cursor-pointer flex items-center"
        >
          <svg class="h-4 w-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="text-sm text-gray-700">{{ search }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useProductStore } from '../../stores/products'
import { debounce } from 'lodash-es'

const router = useRouter()
const productStore = useProductStore()

const searchQuery = ref('')
const showSuggestions = ref(false)
const suggestions = ref([])
const recentSearches = ref([])

// Debounced search function
const debouncedSearch = debounce(async (query) => {
  if (query.length < 2) {
    suggestions.value = []
    return
  }

  try {
    const result = await productStore.searchProducts(query, { limit: 5 })
    if (result.success) {
      suggestions.value = result.data.data
    }
  } catch (error) {
    console.error('Search error:', error)
    suggestions.value = []
  }
}, 300)

const handleInput = () => {
  showSuggestions.value = true
  debouncedSearch(searchQuery.value)
}

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    // Add to recent searches
    addToRecentSearches(searchQuery.value.trim())
    
    // Navigate to search page
    router.push({
      name: 'search',
      query: { q: searchQuery.value.trim() }
    })
    
    showSuggestions.value = false
  }
}

const selectSuggestion = (product) => {
  searchQuery.value = product.name
  addToRecentSearches(product.name)
  router.push({
    name: 'product-detail',
    params: { id: product.id }
  })
  showSuggestions.value = false
}

const selectRecentSearch = (search) => {
  searchQuery.value = search
  router.push({
    name: 'search',
    query: { q: search }
  })
  showSuggestions.value = false
}

const clearSearch = () => {
  searchQuery.value = ''
  suggestions.value = []
  showSuggestions.value = false
}

const addToRecentSearches = (search) => {
  // Remove if already exists
  recentSearches.value = recentSearches.value.filter(s => s !== search)
  
  // Add to beginning
  recentSearches.value.unshift(search)
  
  // Keep only last 5 searches
  recentSearches.value = recentSearches.value.slice(0, 5)
  
  // Save to localStorage
  localStorage.setItem('recent_searches', JSON.stringify(recentSearches.value))
}

const loadRecentSearches = () => {
  const saved = localStorage.getItem('recent_searches')
  if (saved) {
    try {
      recentSearches.value = JSON.parse(saved)
    } catch (error) {
      console.error('Error loading recent searches:', error)
    }
  }
}

// Close suggestions when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    showSuggestions.value = false
  }
}

onMounted(() => {
  loadRecentSearches()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

// Watch for route changes to update search query
watch(() => router.currentRoute.value.query.q, (newQuery) => {
  if (newQuery) {
    searchQuery.value = newQuery
  }
}, { immediate: true })
</script>