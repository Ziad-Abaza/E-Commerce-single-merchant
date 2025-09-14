<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
      <p class="mt-2 text-gray-600">Track and manage your orders</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="orders.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">No orders yet</h3>
      <p class="mt-1 text-sm text-gray-500">Start shopping to see your orders here.</p>
      <div class="mt-6">
        <router-link to="/products" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
          Start Shopping
        </router-link>
      </div>
    </div>

    <!-- Orders List -->
    <div v-else class="space-y-6">
      <div v-for="order in orders" :key="order.id" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Order Header -->
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-medium text-gray-900">Order #{{ order.order_number }}</h3>
              <p class="text-sm text-gray-500">Placed on {{ formatDate(order.created_at) }}</p>
            </div>
            <div class="text-right">
              <div class="text-lg font-medium text-gray-900">{{ order.total.toFixed(2) }} {{ siteStore.settings.currency }}</div>
              <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="getStatusColor(order.status)"
              >
                {{ getStatusText(order.status) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Order Items -->
        <div class="px-6 py-4">
          <div class="space-y-4">
            <div v-for="item in order.items" :key="item.id" class="flex items-center space-x-4">
              <img
                :src="item.product?.media?.[0]?.url || '/images/placeholder-product.jpg'"
                :alt="item.product?.name"
                class="w-16 h-16 object-cover rounded-md"
              />
              <div class="flex-1 min-w-0">
                <h4 class="text-sm font-medium text-gray-900 truncate">{{ item.product?.name }}</h4>
                <p class="text-sm text-gray-500">Quantity: {{ item.quantity }}</p>
                <p class="text-sm text-gray-500">Price: {{ item.price.toFixed(2) }} {{ siteStore.settings.currency }}</p>
              </div>
              <div class="text-sm font-medium text-gray-900">
                {{ (item.price * item.quantity).toFixed(2) }} {{ siteStore.settings.currency }}
              </div>
            </div>
          </div>
        </div>

        <!-- Order Actions -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500">
              <span v-if="order.tracking_number">Tracking: {{ order.tracking_number }}</span>
            </div>
            <div class="flex space-x-3">
              <button
                @click="viewOrderDetails(order)"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              >
                View Details
              </button>
              <button
                v-if="canReorder(order)"
                @click="reorder(order)"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              >
                Reorder
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Details Modal -->
    <div v-if="selectedOrder" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeOrderDetails"></div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  Order #{{ selectedOrder.order_number }}
                </h3>

                <!-- Order Details -->
                <div class="space-y-4">
                  <div>
                    <h4 class="font-medium text-gray-900">Shipping Address</h4>
                    <p class="text-sm text-gray-600">{{ selectedOrder.shipping_address }}</p>
                  </div>

                  <div>
                    <h4 class="font-medium text-gray-900">Payment Method</h4>
                    <p class="text-sm text-gray-600">{{ selectedOrder.payment_method }}</p>
                  </div>

                  <div>
                    <h4 class="font-medium text-gray-900">Order Status</h4>
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="getStatusColor(selectedOrder.status)"
                    >
                      {{ getStatusText(selectedOrder.status) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="closeOrderDetails"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import { useCartStore } from '../stores/cart'
import { useSiteStore } from "../stores/site";

const siteStore = useSiteStore();
const toast = useToast()
const cartStore = useCartStore()

const loading = ref(false)
const orders = ref([])
const selectedOrder = ref(null)

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
  const texts = {
    pending: 'Pending',
    processing: 'Processing',
    shipped: 'Shipped',
    delivered: 'Delivered',
    cancelled: 'Cancelled'
  }
  return texts[status] || 'Unknown'
}

const canReorder = (order) => {
  return order.status === 'delivered' || order.status === 'cancelled'
}

const viewOrderDetails = (order) => {
  selectedOrder.value = order
}

const closeOrderDetails = () => {
  selectedOrder.value = null
}

const reorder = async (order) => {
  try {
    // Add all items from the order to cart
    for (const item of order.items) {
      await cartStore.addToCart(item.product_id, item.quantity, {
        price: item.price
      })
    }

    toast.success('Items added to cart!')
    // Navigate to cart
    // router.push('/cart')
  } catch (error) {
    toast.error('Failed to reorder items')
  }
}

const loadOrders = async () => {
  loading.value = true

  try {
    // This would be an actual API call
    // For now, we'll use mock data
    await new Promise(resolve => setTimeout(resolve, 1000))

    orders.value = [
      {
        id: 1,
        order_number: 'ORD-001',
        status: 'delivered',
        total: 129.99,
        created_at: '2024-01-15T10:30:00Z',
        tracking_number: 'TRK123456789',
        shipping_address: '123 Main St, City, State 12345',
        payment_method: 'Credit Card ending in 1234',
        items: [
          {
            id: 1,
            product_id: 1,
            quantity: 2,
            price: 49.99,
            product: {
              name: 'Sample Product 1',
              media: [{ url: '/images/placeholder-product.jpg' }]
            }
          },
          {
            id: 2,
            product_id: 2,
            quantity: 1,
            price: 29.99,
            product: {
              name: 'Sample Product 2',
              media: [{ url: '/images/placeholder-product.jpg' }]
            }
          }
        ]
      },
      {
        id: 2,
        order_number: 'ORD-002',
        status: 'processing',
        total: 79.99,
        created_at: '2024-01-20T14:15:00Z',
        tracking_number: null,
        shipping_address: '456 Oak Ave, City, State 12345',
        payment_method: 'PayPal',
        items: [
          {
            id: 3,
            product_id: 3,
            quantity: 1,
            price: 79.99,
            product: {
              name: 'Sample Product 3',
              media: [{ url: '/images/placeholder-product.jpg' }]
            }
          }
        ]
      }
    ]
  } catch (error) {
    toast.error('Failed to load orders')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadOrders()
})
</script>
