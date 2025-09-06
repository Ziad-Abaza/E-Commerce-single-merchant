<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">My Wishlist</h1>
      <p class="mt-2 text-gray-600">Save items you love for later</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="wishlistItems.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">Your wishlist is empty</h3>
      <p class="mt-1 text-sm text-gray-500">Start adding items you love to your wishlist.</p>
      <div class="mt-6">
        <router-link to="/products" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
          Start Shopping
        </router-link>
      </div>
    </div>

    <!-- Wishlist Items -->
    <div v-else>
      <!-- Actions Bar -->
      <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <span class="text-sm text-gray-600">{{ wishlistItems.length }} items</span>
          <button
            @click="selectAll"
            class="text-sm text-primary-600 hover:text-primary-500"
          >
            {{ allSelected ? 'Deselect All' : 'Select All' }}
          </button>
        </div>
        
        <div v-if="selectedItems.length > 0" class="flex items-center space-x-3">
          <button
            @click="addSelectedToCart"
            :disabled="cartStore.loading"
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
            </svg>
            Add to Cart ({{ selectedItems.length }})
          </button>
          <button
            @click="removeSelected"
            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Remove ({{ selectedItems.length }})
          </button>
        </div>
      </div>

      <!-- Grid Layout -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div v-for="item in wishlistItems" :key="item.id" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
          <!-- Selection Checkbox -->
          <div class="p-3 border-b border-gray-200">
            <label class="flex items-center">
              <input
                v-model="selectedItems"
                :value="item.id"
                type="checkbox"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
              />
              <span class="ml-2 text-sm text-gray-700">Select</span>
            </label>
          </div>

          <!-- Product Image -->
          <div class="relative aspect-w-1 aspect-h-1 bg-gray-200">
            <router-link :to="`/products/${item.product.id}`" class="block">
              <img
                :src="item.product.media?.[0]?.url || '/images/placeholder-product.jpg'"
                :alt="item.product.name"
                class="w-full h-48 object-cover hover:scale-105 transition-transform duration-200"
                @error="handleImageError"
              />
            </router-link>
            
            <!-- Remove from Wishlist Button -->
            <button
              @click="removeFromWishlist(item.id)"
              class="absolute top-2 right-2 p-2 bg-white rounded-full shadow-md hover:shadow-lg transition-shadow text-red-500 hover:text-red-600"
            >
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
              </svg>
            </button>
          </div>

          <!-- Product Info -->
          <div class="p-4">
            <!-- Category -->
            <div v-if="item.product.categories && item.product.categories.length > 0" class="mb-2">
              <span class="text-xs text-gray-500">{{ item.product.categories[0].name }}</span>
            </div>
            
            <!-- Product Name -->
            <h3 class="text-lg font-medium text-gray-900 mb-2 line-clamp-2">
              <router-link :to="`/products/${item.product.id}`" class="hover:text-primary-600 transition-colors">
                {{ item.product.name }}
              </router-link>
            </h3>
            
            <!-- Price -->
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center space-x-2">
                <span class="text-lg font-bold text-gray-900">${{ displayPrice(item.product) }}</span>
                <span v-if="item.product.sale_price" class="text-sm text-gray-500 line-through">
                  ${{ item.product.price }}
                </span>
              </div>
            </div>
            
            <!-- Add to Cart Button -->
            <button
              @click="addToCart(item.product)"
              :disabled="cartStore.loading"
              class="w-full bg-primary-600 text-white py-2 px-4 rounded-md hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center"
            >
              <svg v-if="cartStore.loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
              </svg>
              {{ cartStore.loading ? 'Adding...' : 'Add to Cart' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCartStore } from '../stores/cart'
import { useToast } from 'vue-toastification'

const cartStore = useCartStore()
const toast = useToast()

const loading = ref(false)
const wishlistItems = ref([])
const selectedItems = ref([])

const allSelected = computed(() => {
  return wishlistItems.value.length > 0 && selectedItems.value.length === wishlistItems.value.length
})

const displayPrice = (product) => {
  return product.sale_price || product.price
}

const handleImageError = (event) => {
  event.target.src = '/images/placeholder-product.jpg'
}

const selectAll = () => {
  if (allSelected.value) {
    selectedItems.value = []
  } else {
    selectedItems.value = wishlistItems.value.map(item => item.id)
  }
}

const addToCart = async (product) => {
  try {
    const result = await cartStore.addToCart(product.id, 1, {
      price: displayPrice(product)
    })
    
    if (result.success) {
      toast.success('Product added to cart!')
    } else {
      toast.error(result.error)
    }
  } catch (error) {
    toast.error('Failed to add product to cart')
  }
}

const addSelectedToCart = async () => {
  const selectedProducts = wishlistItems.value.filter(item => 
    selectedItems.value.includes(item.id)
  )
  
  try {
    for (const item of selectedProducts) {
      await cartStore.addToCart(item.product.id, 1, {
        price: displayPrice(item.product)
      })
    }
    
    toast.success(`${selectedProducts.length} items added to cart!`)
    selectedItems.value = []
  } catch (error) {
    toast.error('Failed to add items to cart')
  }
}

const removeFromWishlist = async (itemId) => {
  try {
    // This would be an actual API call
    wishlistItems.value = wishlistItems.value.filter(item => item.id !== itemId)
    selectedItems.value = selectedItems.value.filter(id => id !== itemId)
    toast.success('Item removed from wishlist')
  } catch (error) {
    toast.error('Failed to remove item from wishlist')
  }
}

const removeSelected = async () => {
  try {
    // This would be an actual API call
    wishlistItems.value = wishlistItems.value.filter(item => 
      !selectedItems.value.includes(item.id)
    )
    selectedItems.value = []
    toast.success('Selected items removed from wishlist')
  } catch (error) {
    toast.error('Failed to remove items from wishlist')
  }
}

const loadWishlist = async () => {
  loading.value = true
  
  try {
    // This would be an actual API call
    // For now, we'll use mock data
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    wishlistItems.value = [
      {
        id: 1,
        product: {
          id: 1,
          name: 'Sample Product 1',
          price: 49.99,
          sale_price: 39.99,
          categories: [{ name: 'Electronics' }],
          media: [{ url: '/images/placeholder-product.jpg' }]
        }
      },
      {
        id: 2,
        product: {
          id: 2,
          name: 'Sample Product 2',
          price: 29.99,
          categories: [{ name: 'Clothing' }],
          media: [{ url: '/images/placeholder-product.jpg' }]
        }
      },
      {
        id: 3,
        product: {
          id: 3,
          name: 'Sample Product 3',
          price: 79.99,
          sale_price: 59.99,
          categories: [{ name: 'Home & Garden' }],
          media: [{ url: '/images/placeholder-product.jpg' }]
        }
      }
    ]
  } catch (error) {
    toast.error('Failed to load wishlist')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadWishlist()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
