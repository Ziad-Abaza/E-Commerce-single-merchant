<!-- src/views/OrdersView.vue -->
<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Orders</h1>
      <p class="mt-2 text-gray-600 dark:text-gray-300">Track and manage your orders</p>
    </div>

    <!-- Loading State -->
    <div v-if="orderStore.loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 dark:border-primary-500"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="orderStore.orders.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No orders yet</h3>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start shopping to see your orders here.</p>
      <div class="mt-6">
        <router-link to="/products" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 dark:bg-primary-700 dark:hover:bg-primary-800">
          Start Shopping
        </router-link>
      </div>
    </div>

    <!-- Orders List -->
    <div v-else class="space-y-6">
      <div v-for="order in orderStore.orders" :key="order.id" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden dark:bg-gray-800 dark:border-gray-700">
        <!-- Order Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order #{{ order.order_number }}</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400">Placed on {{ formatDate(order.created_at) }}</p>
            </div>
            <div class="text-right">
              <div class="text-lg font-medium text-gray-900 dark:text-white">{{ parseFloat(order.total_amount).toFixed(2) }} {{ order.currency }}</div>
              <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="orderStore.getStatusColor(order.status)"
              >
                {{ orderStore.getStatusText(order.status) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Order Items -->
        <div class="px-6 py-4">
          <div class="space-y-4">
            <div v-for="item in order.items" :key="item.id" class="flex items-center space-x-4">
              <div class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center dark:bg-gray-700">
                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <h4 class="text-sm font-medium text-gray-900 truncate dark:text-white">{{ item.product_name }}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">SKU: {{ item.product_sku }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Quantity: {{ item.quantity }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Price: {{ parseFloat(item.unit_price).toFixed(2) }} {{ order.currency }}</p>
              </div>
              <div class="text-sm font-medium text-gray-900 dark:text-white">
                {{ parseFloat(item.total_price).toFixed(2) }} {{ order.currency }}
              </div>
            </div>
          </div>
        </div>

        <!-- Order Actions -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 dark:bg-gray-700 dark:border-gray-600">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500 dark:text-gray-400">
              <span v-if="order.tracking_number">Tracking: {{ order.tracking_number }}</span>
            </div>
            <div class="flex space-x-3">
              <button
                @click="viewOrderDetails(order)"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
              >
                View Details
              </button>
              <button
                v-if="orderStore.canBeCancelled(order.id)"
                @click="cancelOrder(order.id)"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-700 dark:hover:bg-red-800"
              >
                Cancel Order
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Details Modal -->
    <div v-if="orderStore.currentOrder" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity dark:bg-gray-900 dark:bg-opacity-75" @click="closeOrderDetails"></div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full dark:bg-gray-800">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-gray-800">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 dark:text-white">
                  Order #{{ orderStore.currentOrder.order_number }}
                </h3>

                <!-- Order Details -->
                <div class="space-y-6">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <h4 class="font-medium text-gray-900 dark:text-white">Order Information</h4>
                      <div class="mt-2 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                        <p><span class="font-medium">Order Date:</span> {{ formatDate(orderStore.currentOrder.created_at) }}</p>
                        <p><span class="font-medium">Status:</span>
                          <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2"
                            :class="orderStore.getStatusColor(orderStore.currentOrder.status)"
                          >
                            {{ orderStore.getStatusText(orderStore.currentOrder.status) }}
                          </span>
                        </p>
                      </div>
                    </div>

                    <div>
                      <h4 class="font-medium text-gray-900 dark:text-white">Shipping Information</h4>
                      <div class="mt-2 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                        <p><span class="font-medium">Shipping Address:</span> {{ orderStore.currentOrder.shipping_address }}</p>
                        <p v-if="orderStore.currentOrder.tracking_number" class="mt-2">
                          <span class="font-medium">Tracking Number:</span>
                          <a :href="getTrackingUrl(orderStore.currentOrder.tracking_number)" target="_blank" class="text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 ml-1">
                            {{ orderStore.currentOrder.tracking_number }}
                          </a>
                        </p>
                      </div>
                    </div>
                  </div>

                  <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">Order Items</h4>
                    <div class="mt-2 divide-y divide-gray-200 dark:divide-gray-700">
                      <div v-for="item in orderStore.currentOrder.items" :key="item.id" class="py-3 flex justify-between">
                        <div>
                          <p class="text-sm font-medium text-gray-900 dark:text-white">{{ item.product_name }}</p>
                          <p class="text-sm text-gray-500 dark:text-gray-400">SKU: {{ item.product_sku }}</p>
                          <p class="text-sm text-gray-500 dark:text-gray-400">Quantity: {{ item.quantity }}</p>
                        </div>
                        <div class="text-right">
                          <p class="text-sm font-medium text-gray-900 dark:text-white">{{ parseFloat(item.unit_price).toFixed(2) }} {{ orderStore.currentOrder.currency }}</p>
                          <p class="text-sm text-gray-500 dark:text-gray-400">Total: {{ parseFloat(item.total_price).toFixed(2) }} {{ orderStore.currentOrder.currency }}</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="border-t border-gray-200 pt-4 dark:border-gray-700">
                    <div class="space-y-2">
                      <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-300">Subtotal</span>
                        <span class="text-gray-900 dark:text-white">{{ parseFloat(orderStore.currentOrder.subtotal).toFixed(2) }} {{ orderStore.currentOrder.currency }}</span>
                      </div>
                      <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-300">Shipping</span>
                        <span class="text-gray-900 dark:text-white">{{ parseFloat(orderStore.currentOrder.shipping_cost).toFixed(2) }} {{ orderStore.currentOrder.currency }}</span>
                      </div>
                      <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-300">Tax</span>
                        <span class="text-gray-900 dark:text-white">{{ parseFloat(orderStore.currentOrder.tax_amount).toFixed(2) }} {{ orderStore.currentOrder.currency }}</span>
                      </div>
                      <div v-if="orderStore.currentOrder.discount_amount > 0" class="flex justify-between text-sm text-red-600 dark:text-red-400">
                        <span>Discount</span>
                        <span>-{{ parseFloat(orderStore.currentOrder.discount_amount).toFixed(2) }} {{ orderStore.currentOrder.currency }}</span>
                      </div>
                      <div class="flex justify-between text-base font-medium border-t border-gray-200 pt-2 dark:border-gray-700">
                        <span class="text-gray-900 dark:text-white">Total</span>
                        <span class="text-gray-900 dark:text-white">{{ parseFloat(orderStore.currentOrder.total_amount).toFixed(2) }} {{ orderStore.currentOrder.currency }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-gray-700">
            <button
              @click="closeOrderDetails"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm dark:bg-primary-700 dark:hover:bg-primary-800"
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
import { ref, onMounted, computed } from 'vue'
import { useOrderStore } from '../stores/orders'
import { useSiteStore } from '../stores/site'
const orderStore = useOrderStore()
const siteStore = useSiteStore()

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' }
  return new Date(dateString).toLocaleDateString(undefined, options)
}

const getTrackingUrl = (trackingNumber) => {
  // This is a placeholder - you might want to use the actual shipping provider's tracking URL
  // For example, if you use a specific shipping provider, you can add logic here
  return `https://tracking.example.com/?tracking=${trackingNumber}`
}

const viewOrderDetails = async (order) => {
    await orderStore.getOrder(order.id)
}

const closeOrderDetails = () => {
  orderStore.currentOrder = null
}

const cancelOrder = async (orderId) => {
  if (confirm('Are you sure you want to cancel this order?')) {
      await orderStore.cancelOrder(orderId)
  }
}

onMounted(() => {
  orderStore.loadOrders()
})
</script>
