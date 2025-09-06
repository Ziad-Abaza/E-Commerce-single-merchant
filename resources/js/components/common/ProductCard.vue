<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
    <!-- Product Image -->
    <div class="relative aspect-w-1 aspect-h-1 bg-gray-200">
      <router-link :to="`/products/${product.id}`" class="block">
        <img
          :src="productImage"
          :alt="product.name"
          class="w-full h-48 object-cover hover:scale-105 transition-transform duration-200"
          @error="handleImageError"
        />
      </router-link>

      <!-- Wishlist Button -->
      <button
        v-if="authStore.isAuthenticated"
        @click="toggleWishlist"
        class="absolute top-2 right-2 p-2 bg-white rounded-full shadow-md hover:shadow-lg transition-shadow"
        :class="{ 'text-red-500': isInWishlist, 'text-gray-400': !isInWishlist }"
      >
        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
        </svg>
      </button>

      <!-- Sale Badge -->
      <div v-if="product.sale_price" class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">
        Sale
      </div>
    </div>

    <!-- Product Info -->
    <div class="p-4">
      <!-- Category -->
      <div v-if="product.categories && product.categories.length > 0" class="mb-2">
        <span class="text-xs text-gray-500">{{ product.categories[0].name }}</span>
      </div>

      <!-- Product Name -->
      <h3 class="text-lg font-medium text-gray-900 mb-2 line-clamp-2">
        <router-link :to="`/products/${product.id}`" class="hover:text-primary-600 transition-colors">
          {{ product.name }}
        </router-link>
      </h3>

      <!-- Product Description -->
      <p v-if="product.description" class="text-sm text-gray-600 mb-3 line-clamp-2">
        {{ product.description }}
      </p>

      <!-- Rating -->
      <div v-if="product.average_rating" class="flex items-center mb-3">
        <div class="flex items-center">
          <svg
            v-for="star in 5"
            :key="star"
            class="h-4 w-4"
            :class="star <= Math.round(product.average_rating) ? 'text-yellow-400' : 'text-gray-300'"
            fill="currentColor"
            viewBox="0 0 24 24"
          >
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
        </div>
        <span class="ml-2 text-sm text-gray-600">({{ product.reviews_count || 0 }})</span>
      </div>

      <!-- Price -->
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-2">
          <span class="text-lg font-bold text-gray-900">${{ displayPrice }}</span>
          <span v-if="product.sale_price" class="text-sm text-gray-500 line-through">
            ${{ product.price }}
          </span>
        </div>
      </div>

      <!-- Add to Cart Button -->
      <button
        @click="addToCart"
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
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useCartStore } from '../../stores/cart'
import { useToast } from 'vue-toastification'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const authStore = useAuthStore()
const cartStore = useCartStore()
const toast = useToast()

// Compute dynamic placeholder
const productImage = computed(() => {
  if (props.product.media?.length) return props.product.media[0].url
  // Base64 placeholder to avoid missing image error
  return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIwIiBoZWlnaHQ9IjMyMCIgdmlld0JveD0iMCAwIDMyMCAzMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjMyMCIgaGVpZ2h0PSIzMjAiIGZpbGw9IiNlZWUiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZG9taW5hbnQtYmFzZWxpbmU9ImNlbnRyYWwiIGRvbWluYW50LWJhc2VsaW5lPSJjZW50cmFsIiBmb250LXNpemU9IjIwIiBmaWxsPSIjY2NjIj5ObyBJbWFnZTwvdGV4dD48L3N2Zz4='
})

const handleImageError = (event) => {
  event.target.src = productImage.value
}


const displayPrice = computed(() => {
  return props.product.sale_price || props.product.price
})

const isInWishlist = computed(() => {
  // This would be connected to a wishlist store when available
  return false
})

const addToCart = async () => {
  try {
    const result = await cartStore.addToCart(props.product.id, 1, {
      price: displayPrice.value
    })

    if (!result.success) {
      toast.error(result.error)
    }
  } catch (error) {
    toast.error('Failed to add product to cart')
  }
}

const toggleWishlist = () => {
  // This would be connected to a wishlist store when available
  toast.info('Wishlist functionality coming soon!')
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
