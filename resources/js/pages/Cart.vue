<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="container py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Shopping Cart</h1>
        <p class="text-gray-600 mt-2 dark:text-gray-300">{{ cartStore.cartItemCount }} item(s) in your cart</p>
      </div>

      <!-- Loading State -->
      <div v-if="cartStore.loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 dark:border-primary-500"></div>
      </div>

      <!-- Empty Cart State -->
      <div v-else-if="cartStore.items.length === 0" class="text-center py-12">
        <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
          viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
        </svg>
        <h3 class="mt-4 text-xl font-medium text-gray-900 dark:text-white">Your cart is empty</h3>
        <p class="mt-2 text-gray-500 dark:text-gray-400">Start adding some items to your cart.</p>
        <div class="mt-6">
          <router-link to="/products"
            class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors dark:bg-primary-700 dark:hover:bg-primary-800">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Continue Shopping
          </router-link>
        </div>
      </div>

      <!-- Cart Content -->
      <div v-else class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content: Cart Items + Promo Code -->
        <div class="flex-1 space-y-8">
          <!-- Cart Items -->
          <div
            class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden dark:bg-gray-800 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
              <h2 class="text-lg font-medium text-gray-900 dark:text-white">Cart Items</h2>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
              <div v-for="item in cartStore.items" :key="item.id"
                class="p-6 hover:bg-gray-50 transition-colors dark:hover:bg-gray-700">
                <div class="flex flex-col lg:flex-row items-start lg:items-center space-y-4 lg:space-y-0 lg:space-x-4">
                  <!-- Product Image -->
                  <div class="flex-shrink-0 w-full lg:w-24">
                    <div class="aspect-w-1 aspect-h-1 rounded-lg bg-gray-100 overflow-hidden dark:bg-gray-600">
                      <img :src="getProductImageUrl(item)" :alt="getProductName(item)"
                        class="w-full h-full object-cover object-center" @error="handleImageError">
                      <div v-if="imageError"
                        class="absolute inset-0 flex items-center justify-center bg-gray-200 dark:bg-gray-600">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-300" fill="none" stroke="currentColor"
                          viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </div>
                    </div>
                  </div>

                  <!-- Product Info -->
                  <div class="flex-1 min-w-0">
                    <h3 class="text-base font-medium text-gray-900 dark:text-white">
                      <template v-if="item.product_detail?.product?.id">
                        <router-link :to="{ name: 'product-detail', params: { id: item.product_detail.product.id } }"
                          class="hover:text-primary-600 transition-colors dark:hover:text-primary-400">
                          {{ getProductName(item) }}
                        </router-link>
                      </template>
                      <template v-else>
                        {{ getProductName(item) }}
                      </template>
                    </h3>
                    <div class="mt-2 space-y-1 text-sm text-gray-500 dark:text-gray-400">
                      <div v-if="item.product_detail?.size" class="flex items-center">
                        <span class="font-medium">Size:</span>
                        <span class="ml-1">{{ item.product_detail.size }}</span>
                      </div>
                      <div v-if="item.product_detail?.color" class="flex items-center">
                        <span class="font-medium">Color:</span>
                        <span class="ml-1">{{ item.product_detail.color }}</span>
                      </div>
                      <div v-if="item.product_detail?.material" class="flex items-center">
                        <span class="font-medium">Material:</span>
                        <span class="ml-1">{{ item.product_detail.material }}</span>
                      </div>
                    </div>
                    <div class="mt-3 flex items-center space-x-4">
                      <div class="flex items-baseline">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">
                          {{ formatPrice(item.product_detail?.final_price || 0) }} {{ siteStore.settings.currency }}
                        </span>
                        <span v-if="item.product_detail?.discount > 0"
                          class="ml-2 text-sm text-gray-500 line-through dark:text-gray-400">
                          {{ formatPrice(item.product_detail?.price || 0) }} {{ siteStore.settings.currency }}
                        </span>
                      </div>
                      <span v-if="item.product_detail?.sku_variant"
                        class="text-xs bg-gray-100 px-2 py-1 rounded dark:bg-gray-700 dark:text-gray-300">
                        {{ item.product_detail.sku_variant }}
                      </span>
                    </div>
                  </div>

                  <!-- Quantity Controls -->
                  <div class="flex items-center space-x-3 lg:w-32">
                    <button @click="updateQuantity(item.id, item.quantity - 1)" :disabled="cartStore.loading"
                      class="p-2 rounded-md border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors dark:border-gray-600 dark:hover:bg-gray-600 dark:bg-gray-700"
                      :class="cartStore.loading ? 'opacity-50 cursor-not-allowed' : ''">
                      <svg class="h-4 w-4 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                      </svg>
                    </button>
                    <span class="w-12 text-center text-sm font-medium text-gray-900 dark:text-white">
                      {{ item.quantity }}
                    </span>
                    <button @click="updateQuantity(item.id, item.quantity + 1)" :disabled="cartStore.loading"
                      class="p-2 rounded-md border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors dark:border-gray-600 dark:hover:bg-gray-600 dark:bg-gray-700"
                      :class="cartStore.loading ? 'opacity-50 cursor-not-allowed' : ''">
                      <svg class="h-4 w-4 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                      </svg>
                    </button>
                  </div>

                  <!-- Item Total -->
                  <div class="text-right lg:w-24">
                    <div class="text-lg font-bold text-gray-900 dark:text-white">
                      {{ formatPrice((item.product_detail?.final_price || 0) * item.quantity) }} {{
                        siteStore.settings.currency }}
                    </div>
                    <div v-if="item.product_detail?.discount > 0" class="text-sm text-red-500 dark:text-red-400">
                      You save {{ formatPrice(item.product_detail.discount * item.quantity) }} {{
                        siteStore.settings.currency }}
                    </div>
                  </div>

                  <!-- Remove Button -->
                  <div class="flex-shrink-0">
                    <button @click="removeItem(item.id)" :disabled="cartStore.loading"
                      class="p-2 text-gray-400 hover:text-red-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors dark:text-gray-400 dark:hover:text-red-400"
                      :class="cartStore.loading ? 'opacity-50 cursor-not-allowed' : ''">
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Cart Actions -->
            <div class="p-6 flex flex-col sm:flex-row gap-4">
              <router-link to="/products"
                class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="h-4 w-4 mr-2 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Continue Shopping
              </router-link>
              <button @click="clearCart" :disabled="cartStore.loading"
                class="inline-flex items-center justify-center px-6 py-3 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors dark:bg-gray-800 dark:text-red-400 dark:border-red-600 dark:hover:bg-red-900/30"
                :class="cartStore.loading ? 'opacity-50 cursor-not-allowed' : ''">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Clear Cart
              </button>
            </div>
          </div>

          <!-- MOVED: Promo Code Section -->
          <div
            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 dark:bg-gray-800 dark:border-gray-700">
            <div v-if="!cartStore.promoCode">
              <label for="promo-code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Have a promo code?
              </label>
              <div class="mt-1 flex rounded-md shadow-sm">
                <input v-model="promoCodeInput" @keyup.enter="handleApplyPromoCode" type="text" id="promo-code"
                  placeholder="Enter code"
                  class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md focus:ring-primary-500 focus:border-primary-500 sm:text-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                <button @click="handleApplyPromoCode" :disabled="cartStore.loading || !promoCodeInput"
                  class="inline-flex items-center px-4 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-600 text-sm font-medium hover:bg-gray-100 disabled:opacity-50 dark:bg-gray-600 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                  Apply
                </button>
              </div>
            </div>
            <div v-else class="flex justify-between items-center">
              <p class="text-sm font-medium text-green-600 dark:text-green-400">
                Code <span class="font-bold">{{ cartStore.promoCode }}</span> applied!
              </p>
              <button @click="handleRemovePromoCode" :disabled="cartStore.loading"
                class="p-1 text-red-500 hover:text-red-700 dark:hover:text-red-400 rounded-full hover:bg-red-100 dark:hover:bg-red-900/50 text-xs">
                Remove
              </button>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:w-80">
          <div
            class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-4 dark:bg-gray-800 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
              <h2 class="text-lg font-medium text-gray-900 dark:text-white">Order Summary</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
              <!-- Subtotal -->
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-300">Subtotal</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ formatPrice(cartStore.summary.subtotal) }} {{
                  siteStore.settings.currency }}</span>
              </div>

              <!-- Shipping -->
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-300">Shipping</span>
                <span class="font-medium text-gray-900 dark:text-white">
                  {{ cartStore.shippingCost === 0 ? 'Free' : `${formatPrice(cartStore.shippingCost)}` }} {{
                    siteStore.settings.currency }}
                </span>
              </div>

              <!-- Tax -->
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-300">Tax (8%)</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ formatPrice(cartStore.taxAmount) }} {{
                  siteStore.settings.currency }}</span>
              </div>
              
              <!-- NEW: Discount line, appears when a promo code is active -->
              <div v-if="cartStore.discount > 0" class="flex justify-between text-green-600 dark:text-green-400">
                <div class="flex items-center">
                  <span>Discount</span>
                  <span class="ml-2 text-xs font-bold bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 px-2 py-0.5 rounded-full">{{ cartStore.promoCode }}</span>
                </div>
                <span class="font-medium">-{{ formatPrice(cartStore.discount) }} {{ siteStore.settings.currency }}</span>
              </div>

              <!-- Total -->
              <div class="border-t border-gray-200 pt-4 dark:border-gray-600">
                <div class="flex justify-between text-xl font-bold">
                  <span class="text-gray-900 dark:text-white">Total</span>
                  <span class="text-gray-900 dark:text-white">{{ formatPrice(cartStore.grandTotal) }} {{
                    siteStore.settings.currency }}</span>
                </div>
                <p class="text-sm text-gray-500 mt-1 dark:text-gray-400">Taxes and shipping calculated at checkout</p>
              </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600">
              <router-link to="/checkout"
                class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors dark:bg-primary-700 dark:hover:bg-primary-800">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
                Proceed to Checkout
              </router-link>
            </div>

            <!-- Security Badges -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600">
              <div class="text-xs text-gray-500 space-y-1 dark:text-gray-400">
                <div class="flex items-center">
                  <svg class="h-4 w-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Secure checkout
                </div>
                <div class="flex items-center">
                  <svg class="h-4 w-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Encrypted payment
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useCartStore } from '../stores/cart'
import { useAuthStore } from '../stores/auth'
import { useToast } from 'vue-toastification'
import { useSiteStore } from "../stores/site";

const siteStore = useSiteStore();
const cartStore = useCartStore()
const authStore = useAuthStore()
const toast = useToast()
const promoCodeInput = ref('')
const imageError = ref(false)

const formatPrice = (price) => {
  return parseFloat(price || 0).toFixed(2)
}

const getProductName = (item) => {
  return item.product_detail?.product?.name || item.name || 'Product'
}

const getProductImageUrl = (item) => {
  if (item.product_detail?.images_url && item.product_detail.images_url.length > 0) {
    return item.product_detail.images_url[0]
  }
  if (item.product_detail?.product?.main_image_url) {
    return item.product_detail.product.main_image_url
  }
  return null
}

const handleImageError = () => {
  imageError.value = true
}

const handleApplyPromoCode = async () => {
  if (!promoCodeInput.value.trim() || cartStore.loading) return;
  // Use the action directly, it handles success/error toasts
  await cartStore.applyPromoCode(promoCodeInput.value.trim().toUpperCase());
  // Clear input only on success, which `applyPromoCode` now ensures by reloading
  if (!cartStore.error) {
     promoCodeInput.value = '';
  }
};

const handleRemovePromoCode = async () => {
  if (cartStore.loading) return;
  await cartStore.removePromoCode();
};

const updateQuantity = async (itemId, newQuantity) => {
  if (cartStore.loading) return
  await cartStore.updateCartItem(itemId, newQuantity)
}

const removeItem = async (itemId) => {
  if (cartStore.loading) return
  await cartStore.removeFromCart(itemId)
}

const clearCart = async () => {
  if (cartStore.loading) return
  if (confirm('Are you sure you want to clear your cart? This action cannot be undone.')) {
     await cartStore.clearCart()
  }
}

onMounted(async () => {
  await cartStore.initializeCart()
})
</script>

<style scoped>
.aspect-w-1 {
  position: relative;
  padding-bottom: 100%;
}

.aspect-w-1>* {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
</style>

