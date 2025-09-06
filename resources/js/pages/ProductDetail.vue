<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container py-8">
      <div v-if="productStore.loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      </div>

      <div v-else-if="!productStore.currentProduct" class="text-center py-12">
        <h1 class="text-2xl font-bold text-gray-900">Product not found</h1>
        <p class="text-gray-600 mt-2">The product you're looking for doesn't exist.</p>
        <router-link
          to="/products"
          class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
        >
          View All Products
        </router-link>
      </div>

      <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-8">
          <!-- Product Images -->
          <div class="space-y-4">
            <div class="aspect-w-1 aspect-h-1">
              <img
                :src="productStore.currentProduct.image || '/images/placeholder-product.jpg'"
                :alt="productStore.currentProduct.name"
                class="w-full h-96 object-cover object-center rounded-lg"
              >
            </div>
            
            <!-- Thumbnail Images -->
            <div v-if="productStore.currentProduct.images && productStore.currentProduct.images.length > 1" class="grid grid-cols-4 gap-2">
              <img
                v-for="(image, index) in productStore.currentProduct.images"
                :key="index"
                :src="image"
                :alt="productStore.currentProduct.name"
                class="w-full h-20 object-cover object-center rounded-md cursor-pointer hover:opacity-75"
                @click="selectedImage = image"
              >
            </div>
          </div>

          <!-- Product Info -->
          <div class="space-y-6">
            <!-- Breadcrumb -->
            <nav class="flex" aria-label="Breadcrumb">
              <ol class="flex items-center space-x-2">
                <li>
                  <router-link to="/" class="text-gray-400 hover:text-gray-500">Home</router-link>
                </li>
                <li>
                  <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </li>
                <li>
                  <router-link to="/products" class="text-gray-400 hover:text-gray-500">Products</router-link>
                </li>
                <li>
                  <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </li>
                <li>
                  <span class="text-gray-500">{{ productStore.currentProduct.name }}</span>
                </li>
              </ol>
            </nav>

            <!-- Product Title -->
            <div>
              <h1 class="text-3xl font-bold text-gray-900">{{ productStore.currentProduct.name }}</h1>
              <p class="mt-2 text-lg text-gray-600">{{ productStore.currentProduct.description }}</p>
            </div>

            <!-- Rating -->
            <div v-if="productStore.currentProduct.average_rating" class="flex items-center space-x-2">
              <StarRating
                :rating="productStore.currentProduct.average_rating"
                :read-only="true"
                :show-rating="true"
                :star-size="20"
              />
              <span class="text-sm text-gray-500">
                ({{ productStore.currentProduct.reviews_count || 0 }} reviews)
              </span>
            </div>

            <!-- Price -->
            <div class="flex items-center space-x-4">
              <span class="text-3xl font-bold text-gray-900">
                ${{ formatPrice(price) }}
              </span>
              <span
                v-if="productStore.currentProduct.original_price && productStore.currentProduct.original_price > price"
                class="text-xl text-gray-500 line-through"
              >
                ${{ formatPrice(productStore.currentProduct.original_price) }}
              </span>
              <span
                v-if="productStore.currentProduct.discount_percentage"
                class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800"
              >
                -{{ productStore.currentProduct.discount_percentage }}%
              </span>
            </div>

            <!-- Stock Status -->
            <div class="flex items-center space-x-2">
              <span
                v-if="productStore.currentProduct.stock_quantity > 10"
                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
              >
                In Stock
              </span>
              <span
                v-else-if="productStore.currentProduct.stock_quantity > 0"
                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
              >
                Only {{ productStore.currentProduct.stock_quantity }} left
              </span>
              <span
                v-else
                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
              >
                Out of Stock
              </span>
            </div>

            <!-- Quantity Selector -->
            <div class="flex items-center space-x-4">
              <label class="text-sm font-medium text-gray-700">Quantity:</label>
              <div class="flex items-center space-x-2">
                <button
                  @click="decreaseQuantity"
                  :disabled="quantity <= 1"
                  class="p-1 rounded-md border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                  </svg>
                </button>
                <span class="w-12 text-center text-sm font-medium text-gray-900">{{ quantity }}</span>
                <button
                  @click="increaseQuantity"
                  :disabled="quantity >= maxQuantity"
                  class="p-1 rounded-md border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-4">
              <button
                @click="addToCart"
                :disabled="isAddingToCart || productStore.currentProduct.stock_quantity === 0"
                class="flex-1 bg-primary-600 text-white py-3 px-6 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <span v-if="isAddingToCart" class="flex items-center justify-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Adding...
                </span>
                <span v-else>Add to Cart</span>
              </button>

              <button
                @click="toggleWishlist"
                :class="[
                  'px-4 py-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors',
                  isInWishlist
                    ? 'text-red-600 bg-red-50 border-red-300 hover:bg-red-100'
                    : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50'
                ]"
              >
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
              </button>
            </div>

            <!-- Product Details -->
            <div class="border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Product Details</h3>
              <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <dt class="text-sm font-medium text-gray-500">SKU</dt>
                  <dd class="text-sm text-gray-900">{{ productStore.currentProduct.sku || 'N/A' }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Category</dt>
                  <dd class="text-sm text-gray-900">
                    {{ productStore.currentProduct.categories?.[0]?.name || 'N/A' }}
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Weight</dt>
                  <dd class="text-sm text-gray-900">{{ productStore.currentProduct.weight || 'N/A' }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Dimensions</dt>
                  <dd class="text-sm text-gray-900">{{ productStore.currentProduct.dimensions || 'N/A' }}</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useProductStore } from '../stores/products'
import { useCartStore } from '../stores/cart'
import { useToast } from 'vue-toastification'
import StarRating from 'vue-star-rating'

const route = useRoute()
const productStore = useProductStore()
const cartStore = useCartStore()
const toast = useToast()

const quantity = ref(1)
const selectedImage = ref('')
const isAddingToCart = ref(false)
const isInWishlist = ref(false)

const price = computed(() => {
  if (productStore.currentProduct?.discount_percentage) {
    return productStore.currentProduct.price * (1 - productStore.currentProduct.discount_percentage / 100)
  }
  return productStore.currentProduct?.price || 0
})

const maxQuantity = computed(() => {
  return productStore.currentProduct?.stock_quantity || 0
})

const formatPrice = (price) => {
  return parseFloat(price).toFixed(2)
}

const increaseQuantity = () => {
  if (quantity.value < maxQuantity.value) {
    quantity.value++
  }
}

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--
  }
}

const addToCart = async () => {
  if (isAddingToCart.value) return
  
  isAddingToCart.value = true
  
  try {
    const result = await cartStore.addToCart(productStore.currentProduct.id, quantity.value, {
      price: price.value,
      product_name: productStore.currentProduct.name,
      product_image: productStore.currentProduct.image
    })
    
    if (result.success) {
      toast.success('Product added to cart!')
    }
  } catch (error) {
    toast.error('Failed to add product to cart')
  } finally {
    isAddingToCart.value = false
  }
}

const toggleWishlist = () => {
  isInWishlist.value = !isInWishlist.value
  toast.success(isInWishlist.value ? 'Added to wishlist' : 'Removed from wishlist')
}

onMounted(async () => {
  const productId = route.params.id
  await productStore.getProduct(productId)
  
  if (productStore.currentProduct?.image) {
    selectedImage.value = productStore.currentProduct.image
  }
})
</script>
