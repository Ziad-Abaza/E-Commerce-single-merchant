<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Checkout</h1>
      <p class="mt-2 text-gray-600 dark:text-gray-300">Review your order and complete your purchase</p>
    </div>

    <div v-if="cartStore.items.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Your cart is empty</h3>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add some items to your cart to proceed with checkout.</p>
      <div class="mt-6">
        <router-link to="/products" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 dark:bg-primary-700 dark:hover:bg-primary-800">
          Continue Shopping
        </router-link>
      </div>
    </div>

    <div v-if="cartLoading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto"></div>
      <p class="mt-4 text-sm text-gray-600 dark:text-gray-300">Loading your cart...</p>
    </div>
    <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Order Summary -->
      <div class="lg:order-2">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 dark:bg-gray-800 dark:border-gray-700">
          <h2 class="text-lg font-medium text-gray-900 mb-4 dark:text-white">Order Summary</h2>

          <!-- Cart Items -->
          <div class="space-y-4 mb-6">
            <div v-for="item in cartStore.items" :key="item.id" class="flex items-start space-x-4 py-2">
              <div class="flex-shrink-0">
                <img
                  :src="item.product?.media?.[0]?.url || '/images/placeholder-product.jpg'"
                  :alt="item.name || `Product ${item.product_id || item.id}`"
                  class="w-16 h-16 object-cover rounded-md"
                />
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ item.name || `Product #${item.product_id || item.id}` }}
                </h3>
                
                <div v-if="item.color || item.size" class="mt-1 text-xs text-gray-500">
                  <p v-if="item.color" class="truncate">{{ item.color }}</p>
                  <p v-if="item.size" class="truncate">{{ item.size }}</p>
                </div>
                
                <p v-if="item.sku" class="mt-0.5 text-xs text-gray-400">
                  SKU: {{ item.sku }}
                </p>
                
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                  Quantity: {{ item.quantity }}
                </p>
              </div>
              
              <div class="text-right">
                <div class="text-sm font-medium text-gray-900 dark:text-white whitespace-nowrap">
                  {{ formatPrice(item.final_price || item.price, item.quantity) }}
                </div>
              </div>
            </div>
          </div>

          <!-- Order Totals -->
          <div class="border-t border-gray-200 pt-4 space-y-2 dark:border-gray-600">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-gray-300">Subtotal</span>
              <span class="text-gray-900 dark:text-white">{{ formatPrice(cartStore.cartTotal) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-gray-300">Shipping</span>
              <span class="text-gray-900 dark:text-white">{{ formatPrice(shippingCost) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-gray-300">Tax</span>
              <span class="text-gray-900 dark:text-white">{{ formatPrice(taxAmount) }}</span>
            </div>
            <div class="flex justify-between text-lg font-medium border-t border-gray-200 pt-2 dark:border-gray-600">
              <span class="text-gray-900 dark:text-white">Total</span>
              <span class="text-gray-900 dark:text-white">{{ formatPrice(totalAmount) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Checkout Form -->
      <div class="lg:order-1">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Contact Information -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 dark:bg-gray-800 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-900 mb-4 dark:text-white">Contact Information</h2>
            
            <!-- Phone Number -->
            <div class="mb-4">
              <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Phone Number <span class="text-red-500">*</span>
              </label>
              <div class="mt-1 relative">
                <input
                  id="phone"
                  v-model="form.phone"
                  type="tel"
                  placeholder="+1 (555) 123-4567"
                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                  :class="{ 'border-red-500': errors.phone }"
                  @input="errors.phone = ''"
                />
                <p v-if="errors.phone" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.phone }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">We'll use this to contact you about your order</p>
              </div>
            </div>

            <!-- Shipping Address -->
            <div>
              <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Delivery Address <span class="text-red-500">*</span>
              </label>
              <div class="mt-1">
                <textarea
                  id="address"
                  v-model="form.address"
                  rows="3"
                  placeholder="123 Main St, Apartment 4B, City, Country"
                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                  :class="{ 'border-red-500': errors.address }"
                  @input="errors.address = ''"
                ></textarea>
                <p v-if="errors.address" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.address }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Please include any relevant landmarks or delivery instructions</p>
              </div>
            </div>
          </div>

          <!-- Order Confirmation -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 dark:bg-gray-800 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-900 mb-4 dark:text-white">Order Confirmation</h2>
            <p class="text-sm text-gray-600 dark:text-gray-300">
              By placing this order, you agree to our terms and conditions. You'll receive an email confirmation once your order is processed.
            </p>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-primary-700 dark:hover:bg-primary-800"
          >
            <span v-if="loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            {{ loading ? 'Placing Order...' : `Place Order - ${totalAmount.toFixed(2)}` }} {{ siteStore.settings.currency }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '../stores/cart'
import { useToast } from 'vue-toastification'
import { useSiteStore } from "../stores/site";

const siteStore = useSiteStore();
const router = useRouter()
const cartStore = useCartStore()
const toast = useToast()

const loading = ref(false)
const cartLoading = ref(true)

// Load cart when component mounts
onMounted(async () => {
  try {
    await cartStore.loadCart()
  } catch (error) {
    toast.error('Failed to load cart. Please try again.')
    console.error('Error loading cart:', error)
  } finally {
    cartLoading.value = false
  }
})

const form = reactive({
  phone: '',
  address: ''
})

const errors = reactive({
  phone: '',
  address: ''
})

const validateForm = () => {
  let isValid = true
  
  // Reset errors
  errors.phone = ''
  errors.address = ''
  
  // Phone validation - basic international format (supports + and numbers)
  const phoneRegex = /^\+?[0-9\s-()]{8,20}$/
  if (!form.phone.trim()) {
    errors.phone = 'Phone number is required'
    isValid = false
  } else if (!phoneRegex.test(form.phone)) {
    errors.phone = 'Please enter a valid phone number'
    isValid = false
  }
  
  // Address validation
  if (!form.address.trim()) {
    errors.address = 'Address is required'
    isValid = false
  } else if (form.address.trim().length < 10) {
    errors.address = 'Please provide a more detailed address'
    isValid = false
  }
  
  return isValid
}

// Format price with currency
const formatPrice = (price, quantity = 1) => {
  const total = parseFloat(price || 0) * (quantity || 1)
  return `${total.toFixed(2)} ${siteStore.settings.currency}`
}

const shippingCost = computed(() => {
  // Free shipping for orders over $50
  return cartStore.cartTotal > 50 ? 0 : 9.99
})

const taxAmount = computed(() => {
  return cartStore.cartTotal * 0.08 // 8% tax
})

const totalAmount = computed(() => {
  return cartStore.cartTotal + shippingCost.value + taxAmount.value
})

const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }
  
  loading.value = true

  try {
    const orderData = {
      items: cartStore.items,
      contact: {
        phone: form.phone,
        address: form.address
      },
      total: totalAmount.value,
      shipping: shippingCost.value,
      tax: taxAmount.value
    }

    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 2000))

    // Clear cart after successful order
    await cartStore.clearCart()

    toast.success('Order placed successfully! You will receive a confirmation call shortly.', {
      timeout: 5000
    })
    router.push('/orders')

  } catch (error) {
    console.error('Order submission error:', error)
    toast.error('Failed to process order. Please try again or contact support.', {
      timeout: 5000
    })
  } finally {
    loading.value = false
  }
}
</script>
