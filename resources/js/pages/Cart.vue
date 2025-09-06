<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
        <p class="text-gray-600 mt-2">{{ cartStore.itemCount }} item(s) in your cart</p>
      </div>

      <div v-if="cartStore.loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      </div>

      <div v-else-if="cartStore.items.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Your cart is empty</h3>
        <p class="mt-1 text-sm text-gray-500">Start adding some items to your cart.</p>
        <div class="mt-6">
          <router-link
            to="/products"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            Continue Shopping
          </router-link>
        </div>
      </div>

      <div v-else class="flex flex-col lg:flex-row gap-8">
        <!-- Cart Items -->
        <div class="flex-1">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">Cart Items</h2>
            </div>
            
            <div class="divide-y divide-gray-200">
              <div
                v-for="item in cartStore.items"
                :key="item.id"
                class="p-6"
              >
                <div class="flex items-center space-x-4">
                  <!-- Product Image -->
                  <div class="flex-shrink-0">
                    <img
                      :src="item.product?.image || '/images/placeholder-product.jpg'"
                      :alt="item.product?.name || 'Product'"
                      class="h-20 w-20 object-cover object-center rounded-lg"
                    >
                  </div>

                  <!-- Product Info -->
                  <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-medium text-gray-900">
                      <router-link
                        :to="{ name: 'product-detail', params: { id: item.product_id } }"
                        class="hover:text-primary-600 transition-colors"
                      >
                        {{ item.product?.name || 'Product' }}
                      </router-link>
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                      {{ item.product?.description || 'No description available' }}
                    </p>
                    <div class="mt-2 flex items-center space-x-4">
                      <span class="text-sm font-medium text-gray-900">
                        ${{ formatPrice(item.price) }}
                      </span>
                      <span class="text-sm text-gray-500">
                        SKU: {{ item.product?.sku || 'N/A' }}
                      </span>
                    </div>
                  </div>

                  <!-- Quantity Controls -->
                  <div class="flex items-center space-x-2">
                    <button
                      @click="updateQuantity(item.id, item.quantity - 1)"
                      :disabled="isUpdating"
                      class="p-1 rounded-md border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                      </svg>
                    </button>
                    
                    <span class="w-12 text-center text-sm font-medium text-gray-900">
                      {{ item.quantity }}
                    </span>
                    
                    <button
                      @click="updateQuantity(item.id, item.quantity + 1)"
                      :disabled="isUpdating"
                      class="p-1 rounded-md border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                      </svg>
                    </button>
                  </div>

                  <!-- Item Total -->
                  <div class="text-right">
                    <div class="text-sm font-medium text-gray-900">
                      ${{ formatPrice(item.price * item.quantity) }}
                    </div>
                  </div>

                  <!-- Remove Button -->
                  <div class="flex-shrink-0">
                    <button
                      @click="removeItem(item.id)"
                      :disabled="isRemoving"
                      class="p-2 text-gray-400 hover:text-red-600 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Cart Actions -->
          <div class="mt-6 flex flex-col sm:flex-row gap-4">
            <router-link
              to="/products"
              class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
              <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Continue Shopping
            </router-link>
            
            <button
              @click="clearCart"
              :disabled="isClearing"
              class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Clear Cart
            </button>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:w-80">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-4">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">Order Summary</h2>
            </div>
            
            <div class="px-6 py-4 space-y-4">
              <!-- Subtotal -->
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-medium text-gray-900">${{ formatPrice(cartStore.total) }}</span>
              </div>
              
              <!-- Shipping -->
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Shipping</span>
                <span class="font-medium text-gray-900">
                  {{ shippingCost === 0 ? 'Free' : `$${formatPrice(shippingCost)}` }}
                </span>
              </div>
              
              <!-- Tax -->
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Tax</span>
                <span class="font-medium text-gray-900">${{ formatPrice(tax) }}</span>
              </div>
              
              <!-- Total -->
              <div class="border-t border-gray-200 pt-4">
                <div class="flex justify-between text-base font-medium">
                  <span class="text-gray-900">Total</span>
                  <span class="text-gray-900">${{ formatPrice(total) }}</span>
                </div>
              </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
              <router-link
                to="/checkout"
                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
              >
                Proceed to Checkout
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCartStore } from '../stores/cart'
import { useAuthStore } from '../stores/auth'
import { useToast } from 'vue-toastification'

const cartStore = useCartStore()
const authStore = useAuthStore()
const toast = useToast()

const isUpdating = ref(false)
const isRemoving = ref(false)
const isClearing = ref(false)

const shippingCost = computed(() => {
  // Free shipping over $50
  return cartStore.total >= 50 ? 0 : 9.99
})

const tax = computed(() => {
  // 8% tax rate
  return cartStore.total * 0.08
})

const total = computed(() => {
  return cartStore.total + shippingCost.value + tax.value
})

const formatPrice = (price) => {
  return parseFloat(price).toFixed(2)
}

const updateQuantity = async (itemId, newQuantity) => {
  if (newQuantity < 1) {
    await removeItem(itemId)
    return
  }
  
  isUpdating.value = true
  
  try {
    const result = await cartStore.updateCartItem(itemId, newQuantity)
    if (result.success) {
      toast.success('Cart updated')
    }
  } catch (error) {
    toast.error('Failed to update cart')
  } finally {
    isUpdating.value = false
  }
}

const removeItem = async (itemId) => {
  isRemoving.value = true
  
  try {
    const result = await cartStore.removeFromCart(itemId)
    if (result.success) {
      toast.success('Item removed from cart')
    }
  } catch (error) {
    toast.error('Failed to remove item')
  } finally {
    isRemoving.value = false
  }
}

const clearCart = async () => {
  if (!confirm('Are you sure you want to clear your cart?')) {
    return
  }
  
  isClearing.value = true
  
  try {
    const result = await cartStore.clearCart()
    if (result.success) {
      toast.success('Cart cleared')
    }
  } catch (error) {
    toast.error('Failed to clear cart')
  } finally {
    isClearing.value = false
  }
}

onMounted(() => {
  // Load cart if user is authenticated
  if (authStore.isAuthenticated) {
    cartStore.loadCart()
  } else {
    // Load from localStorage for guest users
    cartStore.loadFromLocalStorage()
  }
})
</script>
