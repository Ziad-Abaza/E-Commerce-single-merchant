<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
      <p class="mt-2 text-gray-600">Review your order and complete your purchase</p>
    </div>

    <div v-if="cartStore.items.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">Your cart is empty</h3>
      <p class="mt-1 text-sm text-gray-500">Add some items to your cart to proceed with checkout.</p>
      <div class="mt-6">
        <router-link to="/products" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
          Continue Shopping
        </router-link>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Order Summary -->
      <div class="lg:order-2">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h2>

          <!-- Cart Items -->
          <div class="space-y-4 mb-6">
            <div v-for="item in cartStore.items" :key="item.id" class="flex items-center space-x-4">
              <img
                :src="item.product?.media?.[0]?.url || '/images/placeholder-product.jpg'"
                :alt="item.product?.name"
                class="w-16 h-16 object-cover rounded-md"
              />
              <div class="flex-1 min-w-0">
                <h3 class="text-sm font-medium text-gray-900 truncate">{{ item.product?.name }}</h3>
                <p class="text-sm text-gray-500">Quantity: {{ item.quantity }}</p>
              </div>
              <div class="text-sm font-medium text-gray-900">
                ${{ (item.price * item.quantity).toFixed(2) }}
              </div>
            </div>
          </div>

          <!-- Order Totals -->
          <div class="border-t border-gray-200 pt-4 space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Subtotal</span>
              <span class="text-gray-900">${{ cartStore.cartTotal.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Shipping</span>
              <span class="text-gray-900">${{ shippingCost.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Tax</span>
              <span class="text-gray-900">${{ taxAmount.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-lg font-medium border-t border-gray-200 pt-2">
              <span class="text-gray-900">Total</span>
              <span class="text-gray-900">${{ totalAmount.toFixed(2) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Checkout Form -->
      <div class="lg:order-1">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Shipping Information -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Shipping Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                <input
                  id="first_name"
                  v-model="form.shipping.first_name"
                  type="text"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>
              <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                <input
                  id="last_name"
                  v-model="form.shipping.last_name"
                  type="text"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>
            </div>

            <div class="mt-4">
              <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
              <input
                id="address"
                v-model="form.shipping.address"
                type="text"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
              <div>
                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                <input
                  id="city"
                  v-model="form.shipping.city"
                  type="text"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>
              <div>
                <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                <input
                  id="state"
                  v-model="form.shipping.state"
                  type="text"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>
              <div>
                <label for="zip_code" class="block text-sm font-medium text-gray-700">ZIP Code</label>
                <input
                  id="zip_code"
                  v-model="form.shipping.zip_code"
                  type="text"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>
            </div>
          </div>

          <!-- Payment Information -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h2>

            <div>
              <label for="card_number" class="block text-sm font-medium text-gray-700">Card Number</label>
              <input
                id="card_number"
                v-model="form.payment.card_number"
                type="text"
                required
                placeholder="1234 5678 9012 3456"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              />
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
              <div>
                <label for="expiry_date" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                <input
                  id="expiry_date"
                  v-model="form.payment.expiry_date"
                  type="text"
                  required
                  placeholder="MM/YY"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>
              <div>
                <label for="cvv" class="block text-sm font-medium text-gray-700">CVV</label>
                <input
                  id="cvv"
                  v-model="form.payment.cvv"
                  type="text"
                  required
                  placeholder="123"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            {{ loading ? 'Processing...' : `Complete Order - $${totalAmount.toFixed(2)}` }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '../stores/cart'
import { useToast } from 'vue-toastification'

const router = useRouter()
const cartStore = useCartStore()
const toast = useToast()

const loading = ref(false)

const form = reactive({
  shipping: {
    first_name: '',
    last_name: '',
    address: '',
    city: '',
    state: '',
    zip_code: ''
  },
  payment: {
    card_number: '',
    expiry_date: '',
    cvv: ''
  }
})

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
  loading.value = true

  try {
    // This would integrate with a payment processor like Stripe
    // For now, we'll simulate a successful payment

    const orderData = {
      items: cartStore.items,
      shipping: form.shipping,
      payment: form.payment,
      total: totalAmount.value
    }

    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 2000))

    // Clear cart after successful order
    await cartStore.clearCart()

    toast.success('Order placed successfully!')
    router.push('/orders')

  } catch (error) {
    toast.error('Failed to process order. Please try again.')
  } finally {
    loading.value = false
  }
}
</script>
