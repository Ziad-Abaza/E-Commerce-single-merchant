<template>
  <div class="product-card h-full flex flex-col bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-200 dark:bg-gray-800 dark:border-gray-700 dark:hover:shadow-gray-700/20">
    <!-- Product Image -->
    <div class="relative w-full h-48 md:h-56 lg:h-60 bg-gray-100 overflow-hidden dark:bg-gray-700">
      <router-link :to="`/products/${product.id}`" class="absolute inset-0 z-10" :aria-label="t('app.view_details')"></router-link>
      <img
        :src="productImage"
        :alt="product.name"
        class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
        @error="handleImageError"
        loading="lazy"
      />
      <!-- Wishlist Button -->
      <button
        v-if="authStore.isAuthenticated"
        @click.stop="toggleWishlist"
        class="absolute top-2 right-2 p-2 bg-white rounded-full shadow-md hover:shadow-lg transition z-20 dark:bg-gray-700 dark:hover:bg-gray-600"
        :class="{ 'text-red-500': isInWishlist, 'text-gray-400 dark:text-gray-300': !isInWishlist }"
        :aria-label="isInWishlist ? t('app.remove_from_wishlist') : t('app.add_to_wishlist')"
        :disabled="isWishlistProcessing"
      >
        <svg v-if="isWishlistProcessing" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <svg v-else class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
        </svg>
      </button>

      <!-- Sale Badge -->
      <div v-if="product.discount_percentage > 0" class="absolute top-2 left-2 bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded">
        {{ t('app.discount_percentage', { percentage: product.discount_percentage }) }}
      </div>

      <!-- Stock Status -->
      <div v-if="!product.in_stock" class="absolute top-2 left-2 bg-gray-800 text-white text-xs font-semibold px-2 py-1 rounded dark:bg-gray-600">
        {{ t('app.out_of_stock') }}
      </div>
      <div v-else-if="product.quantity <= 3" class="absolute top-2 left-2 bg-yellow-500 text-white text-xs font-semibold px-2 py-1 rounded">
        {{ t('app.only_x_left', { qty: product.quantity }) }}
      </div>
      <div v-else class="absolute top-2 left-2 bg-green-500 text-white text-xs font-semibold px-2 py-1 rounded">
        {{ t('app.in_stock') }}
      </div>

      <!-- Free Shipping Badge -->
      <div v-if="product.free_shipping" class="absolute bottom-2 right-2 bg-blue-500 text-white text-xs font-semibold px-2 py-1 rounded">
        {{ t('app.free_shipping') }}
      </div>
    </div>

    <!-- Product Info -->
    <div class="p-4 flex flex-col flex-1">
      <!-- Category -->
      <span v-if="product.categories?.length" class="text-xs text-gray-500 mb-1 dark:text-gray-400">
        {{ product.categories[0].name }}
      </span>

      <!-- Product Name -->
      <h3 class="text-sm md:text-base font-medium text-gray-900 mb-1 line-clamp-2 dark:text-white">
        <router-link :to="`/products/${product.id}`" class="hover:text-primary-600 transition-colors dark:hover:text-primary-400">
          {{ product.name }}
        </router-link>
      </h3>

      <!-- Description -->
      <p v-if="product.description" class="text-xs md:text-sm text-gray-600 mb-2 line-clamp-2 dark:text-gray-300">
        <router-link :to="`/products/${product.id}`" class="hover:text-primary-600 transition-colors dark:hover:text-primary-400">
          {{ product.description }}
        </router-link>
      </p>

      <!-- Rating -->
      <div v-if="product.rating > 0" class="flex items-center mb-2">
        <div class="flex items-center">
          <svg
            v-for="star in 5"
            :key="star"
            class="h-4 w-4 md:h-5 md:w-5"
            :class="star <= Math.round(product.rating) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'"
            fill="currentColor"
            viewBox="0 0 24 24"
          >
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
        </div>
        <span class="ml-2 text-xs md:text-sm text-gray-600 dark:text-gray-400">({{ product.reviews_count || 0 }})</span>
      </div>

      <!-- Price -->
      <div class="mt-auto">
        <div class="flex items-center justify-between mb-2">
          <div class="flex items-baseline gap-2">
            <span class="text-lg font-bold text-primary-600 dark:text-primary-400">
              {{product.price }}
            </span>
            <span v-if="product.original_price > product.price" class="text-sm text-gray-500 line-through dark:text-gray-400">
              {{product.original_price }}
            </span>
          </div>
          <span v-if="product.rating" class="flex items-center text-sm text-yellow-500">
            <span class="font-medium">{{ product.rating.toFixed(1) }}</span>
            <svg class="w-4 h-4 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
          </span>
        </div>
      </div>

      <!-- Actions -->
      <div class="mt-3 flex gap-2">
        <button
          v-if="showAddToCart"
          @click="addToCart"
          :disabled="!product.in_stock || isAddingToCart"
          class="flex-1 flex items-center justify-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
          :class="{ 'opacity-50 cursor-not-allowed': !product.in_stock }"
        >
          <svg v-if="isAddingToCart" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <svg v-else class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          {{ isAddingToCart ? t('app.adding_to_cart') : (product.in_stock ? t('app.add_to_cart') : t('app.out_of_stock')) }}
        </button>

        <router-link
          v-if="showViewDetails"
          :to="`/products/${product.id}`"
          class="flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
        >
          {{ t('app.view_details') }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useWishlistStore } from '../../stores/wishlist'
import { useCartStore } from '../../stores/cart'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { useSiteStore } from "../../stores/site";

const { t } = useI18n()
const authStore = useAuthStore()
const wishlistStore = useWishlistStore()
const cartStore = useCartStore()
const siteStore = useSiteStore();
const toast = useToast()

const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  showAddToCart: {
    type: Boolean,
    default: true
  },
  showViewDetails: {
    type: Boolean,
    default: true
  },
  isHorizontal: {
    type: Boolean,
    default: false
  }
})

// Compute product image with fallback
const productImage = computed(() => {
  return props.product.images?.[0]?.url || '/images/placeholder-product.png'
})

// Product stock status
const stockStatus = computed(() => {
  if (!props.product.in_stock) return 'out_of_stock'
  if (props.product.quantity <= 3) return 'low_stock'
  return 'in_stock'
})

// Handle image load error
const handleImageError = (event) => {
  event.target.src = '/images/placeholder-product.png'
  event.target.alt = t('app.product_image_unavailable')
}

// Add product to cart
const isAddingToCart = ref(false)
const addToCart = async () => {
  if (!authStore.isAuthenticated) {
    toast.error(t('auth.login_required'))
    return
  }

  try {
    isAddingToCart.value = true
    await cartStore.addToCart({
      product_id: props.product.id,
      quantity: 1
    })
    toast.success(t('cart.added_to_cart'))
  } catch (error) {
    console.error('Error adding to cart:', error)
    toast.error(t('cart.add_to_cart_error'))
  } finally {
    isAddingToCart.value = false
  }
}

// Check if product is in wishlist
const isInWishlist = computed(() => {
  if (!authStore.isAuthenticated) return false
  return wishlistStore.isInWishlist(props.product.id)
})

// Toggle wishlist status
const isWishlistProcessing = ref(false)
const toggleWishlist = async () => {
  if (!authStore.isAuthenticated) {
    toast.error(t('auth.login_required'))
    return
  }

  if (isWishlistProcessing.value) return

  isWishlistProcessing.value = true

  try {
    if (isInWishlist.value) {
      await wishlistStore.removeFromWishlist(props.product.id)
      toast.success(t('app.removed_from_wishlist'))
    } else {
      await wishlistStore.addToWishlist(props.product.id)
      toast.success(t('app.added_to_wishlist'))
    }
  } catch (error) {
    console.error('Wishlist toggle error:', error)
    toast.error(error.message || 'Failed to update wishlist')
  }
}
</script>

<style scoped>

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-card {
  min-height: 380px;
  display: flex;
  flex-direction: column;
}
</style>
