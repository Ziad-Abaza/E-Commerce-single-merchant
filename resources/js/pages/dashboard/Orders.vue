<!-- resources/js/pages/dashboard/Orders.vue -->
<template>
  <div class="p-2 sm:p-4 md:p-6 space-y-4 sm:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
            Orders Management
          </h1>
          <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
            Manage customer orders and track order status
          </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
          <button
            @click="fetchOrders"
            class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh
          </button>
          <button
            @click="handleExpiredOrders"
            class="px-3 sm:px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Process Expired
          </button>
        </div>
      </div>
    </div>

    <!-- Error Alert -->
    <div
      v-if="ordersStore.error"
      class="p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg animate-in slide-in-from-top-2 duration-300"
    >
      <div class="flex items-center">
        <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="text-sm text-red-700 dark:text-red-300 flex-1">{{ ordersStore.error }}</span>
        <button
          @click="ordersStore.clearError"
          class="ml-2 text-red-500 hover:text-red-700 dark:hover:text-red-300 p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
      <!-- Total Orders -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Total Orders</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
              {{ ordersStore.statistics?.total_orders }}
            </p>
          </div>
          <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Pending Orders -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Pending</p>
            <p class="text-lg font-bold text-yellow-600 dark:text-yellow-400 mt-1">
              {{ ordersStore.statistics?.pending }}
            </p>
          </div>
          <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Processing Orders -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Processing</p>
            <p class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-1">
              {{ ordersStore.statistics?.processing }}
            </p>
          </div>
          <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Shipped Orders -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Shipped</p>
            <p class="text-lg font-bold text-purple-600 dark:text-purple-400 mt-1">
              {{ ordersStore.statistics?.shipped }}
            </p>
          </div>
          <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Delivered Orders -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Delivered</p>
            <p class="text-lg font-bold text-green-600 dark:text-green-400 mt-1">
              {{ ordersStore.statistics?.delivered }}
            </p>
          </div>
          <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Cancelled Orders -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Cancelled</p>
            <p class="text-lg font-bold text-red-600 dark:text-red-400 mt-1">
              {{ ordersStore.statistics?.cancelled }}
            </p>
          </div>
          <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700">
      <div class="flex flex-col sm:flex-row gap-3 items-end">
        <!-- Search -->
        <div class="flex-1">
          <Search
            v-model="ordersStore.filters.search"
            placeholder="Search by order number, customer name, or email..."
            @submit="handleSearch"
          />
        </div>

        <!-- Status Filter -->
        <Select
          v-model="ordersStore.filters.status"
          :options="statusOptions"
          placeholder="All Statuses"
          label="Status"
          @update:modelValue="handleStatusFilter"
        />

        <!-- Date Range -->
        <div class="flex gap-2">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
            <input
              v-model="ordersStore.filters.start_date"
              type="date"
              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              @change="handleDateFilter"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
            <input
              v-model="ordersStore.filters.end_date"
              type="date"
              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              @change="handleDateFilter"
            />
          </div>
        </div>

        <!-- Clear Filters -->
        <button
          v-if="hasActiveFilters"
          @click="clearAllFilters"
          class="px-3 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm"
        >
          Clear
        </button>
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="ordersStore.loading" class="space-y-4">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 space-y-3">
          <div v-for="n in 5" :key="n" class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-300 dark:bg-gray-700 rounded-lg animate-pulse"></div>
            <div class="flex-1 space-y-2">
              <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded animate-pulse"></div>
              <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-2/3 animate-pulse"></div>
            </div>
            <div class="w-20 h-8 bg-gray-300 dark:bg-gray-700 rounded animate-pulse"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Orders Table -->
    <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
      <!-- Empty State -->
      <div
        v-if="ordersStore.orders.length === 0"
        class="text-center py-12"
      >
        <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No orders found</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-4">
          {{ hasActiveFilters ? 'Try adjusting your filters.' : 'Orders will appear here once customers place them.' }}
        </p>
      </div>

      <!-- Orders Table -->
      <div v-else>
        <Table
          :headers="tableHeaders"
          :rows="tableRows"
        />
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="ordersStore.orders.length > 0" class="flex justify-center">
      <Pagination
        :total="ordersStore.pagination.total"
        :current-page="ordersStore.pagination.current_page"
        :per-page="ordersStore.pagination.per_page"
        :last-page="ordersStore.pagination.last_page"
        :from="ordersStore.pagination.from"
        :to="ordersStore.pagination.to"
        @page-change="handlePageChange"
        @update:perPage="handlePerPageChange"
      />
    </div>

    <!-- Modals -->


            <!-- Update Status Modal -->
          <div
  v-if="showStatusModal"
  class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  @click.self="showStatusModal = false"
>
  <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md">
    <h3 class="text-lg font-bold mb-4">Update Order Status</h3>
    <select v-model="newStatus" class="w-full border rounded-lg p-2 mb-4">
      <option disabled value="">Select status</option>
      <option value="pending">Pending</option>
      <option value="processing">Processing</option>
      <option value="shipped">Shipped</option>
      <option value="delivered">Delivered</option>
      <option value="cancelled">Cancelled</option>
    </select>
    <div class="flex justify-end gap-2">
      <button @click="showStatusModal = false" class="px-3 py-2 bg-gray-200 rounded-lg">Cancel</button>
      <button @click="updateOrderStatus" class="px-3 py-2 bg-blue-600 text-white rounded-lg">Update</button>
    </div>
  </div>
</div>

    <!-- Order Details Modal -->
    <div
      v-if="showDetailsModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
      @click.self="showDetailsModal = false"
    >
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
              Order Details #{{ currentOrder?.order_number }}
            </h3>
            <button
              @click="showDetailsModal = false"
              class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Order Summary -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Order Info -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5">
              <h4 class="font-semibold text-lg text-gray-900 dark:text-white mb-4">Order Information</h4>
              <div class="space-y-3">
                <div class="flex justify-between">
                  <span class="text-gray-500 dark:text-gray-400">Order Number:</span>
                  <span class="font-medium">{{ currentOrder?.order_number }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500 dark:text-gray-400">Status:</span>
                  <span :class="getStatusClass(currentOrder?.status)">{{ currentOrder?.status }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500 dark:text-gray-400">Created At:</span>
                  <span class="font-medium">{{ formatDate(currentOrder?.created_at) }}</span>
                </div>
              </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5">
              <h4 class="font-semibold text-lg text-gray-900 dark:text-white mb-4">Customer Information</h4>
              <div class="space-y-3">
                <div class="flex justify-between">
                  <span class="text-gray-500 dark:text-gray-400">Customer:</span>
                  <span class="font-medium">{{ currentOrder?.user?.name }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500 dark:text-gray-400">Email:</span>
                  <span class="font-medium">{{ currentOrder?.user?.email }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500 dark:text-gray-400">Phone:</span>
                  <span class="font-medium">{{ currentOrder?.user?.phone || 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500 dark:text-gray-400">Shipping Address:</span>
                  <span class="font-medium text-right">{{ currentOrder?.shipping_address }}</span>
                </div>
              </div>
            </div>

            <!-- Order Items -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5">
              <h4 class="font-semibold text-lg text-gray-900 dark:text-white mb-4">Order Items</h4>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">SKU</th>
                      <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                      <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="item in currentOrder?.items" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ item.product_name }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ item.product_sku }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">{{ item.quantity }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white text-right">{{ formatCurrency(item.total_price) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-wrap gap-3 justify-end">
            <button
              v-if="currentOrder?.can_be_cancelled"
              @click="handleCancelOrder"
              class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Cancel Order
            </button>
            <button
              v-if="currentOrder?.status !== 'delivered'"
              @click="handleUpdateStatus('delivered')"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Mark as Delivered
            </button>
            <button
              v-if="currentOrder?.status !== 'shipped'"
              @click="handleUpdateStatus('shipped')"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4" />
              </svg>
              Mark as Shipped
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Cancel Order Confirmation Modal -->
    <ConfirmModal
      :show="showCancelModal"
      title="Cancel Order"
      :message="`Are you sure you want to cancel order #${currentOrder?.order_number}? This action cannot be undone.`"
      confirm-text="Cancel Order"
      confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
      @confirm="confirmCancelOrder"
      @cancel="showCancelModal = false"
      :loading="ordersStore.loading"
    >
      <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cancellation Reason</label>
        <textarea
          v-model="cancelReason"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
          rows="3"
          placeholder="Enter reason for cancellation..."
        ></textarea>
      </div>
    </ConfirmModal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useOrdersStore } from '../../stores/dashboard/orders';
import { useAuthStore } from '../../stores/auth';
import Search from './components/Search.vue';
import Select from './components/Select.vue';
import Table from './components/Table.vue';
import Pagination from './components/Pagination.vue';
import ConfirmModal from './components/ConfirmModal.vue';
import { useSiteStore } from "@/stores/site";

const siteStore = useSiteStore();
const ordersStore = useOrdersStore();
const authStore = useAuthStore();

const showDetailsModal = ref(false);
const showCancelModal = ref(false);
const showStatusModal = ref(false);
const newStatus = ref("");

const currentOrder = ref(null);
const cancelReason = ref('');

// Check permissions
if (!authStore.hasPermission('manage_orders')) {
  throw new Error('Access denied: You do not have permission to manage orders');
}

// Filter options
const statusOptions = ref([
  { value: 'pending', label: 'Pending' },
  { value: 'processing', label: 'Processing' },
  { value: 'shipped', label: 'Shipped' },
  { value: 'delivered', label: 'Delivered' },
  { value: 'cancelled', label: 'Cancelled' },
]);

// Table headers
const tableHeaders = ref([
  { key: 'order_number', label: 'Order #' },
  { key: 'customer', label: 'Customer' },
  { key: 'status', label: 'Status' },
  { key: 'date', label: 'Date' },
  { key: 'actions', label: 'Actions' },
]);

// Computed properties
const hasActiveFilters = computed(() => {
  return ordersStore.filters.search ||
         ordersStore.filters.status ||
         ordersStore.filters.user_id ||
         ordersStore.filters.start_date ||
         ordersStore.filters.end_date;
});

const tableRows = computed(() => {
  return ordersStore.orders.map(order => ({
    id: order.id,
    order_number: order.order_number,
    customer: `${order.user?.name || 'N/A'} (${order.user?.email || 'N/A'})`,
    status: {
      type: 'status',
      value: order.status,
      class: getStatusClass(order.status)
    },
    date: formatDate(order.created_at),
    actions: [
      {
        label: 'View',
        icon: 'eye',
        class: 'bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300',
        onClick: () => handleViewOrder(order)
      },
      {
        label: 'Update Status',
        icon: 'edit',
        class: 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300',
        onClick: () => handleUpdateStatusClick(order)
      },
      {
        label: 'Cancel',
        icon: 'trash',
        class: 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300',
        onClick: () => handleCancelOrderClick(order),
        disabled: !order.can_be_cancelled
      }
    ]
  }));
});

// Helper functions
function getStatusClass(status) {
  switch (status) {
    case 'pending':
      return 'px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'
    case 'processing':
      return 'px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'
    case 'shipped':
      return 'px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300'
    case 'delivered':
      return 'px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
    case 'cancelled':
      return 'px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'
    default:
      return 'px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300'
  }
}

function formatCurrency(amount) {
  if (amount == null) return "0" + " " + siteStore.settings.currency
  return new Intl.NumberFormat("en-EG", {
    style: "currency",
    currency: siteStore.settings.currency,
  }).format(amount)
}


const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  try {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }).format(date);
  } catch (e) {
    return dateString;
  }
};

// Event handlers
const handleSearch = (searchTerm) => {
  ordersStore.setFilter('search', searchTerm);
};

const handleStatusFilter = (value) => {
  ordersStore.setFilter('status', value);
};

const handleDateFilter = () => {
  // The filters are already bound with v-model, so we just need to trigger the fetch
  ordersStore.fetchOrders(ordersStore.pagination.current_page, ordersStore.pagination.per_page);
};

const clearAllFilters = () => {
  ordersStore.clearFilters();
};

const handlePageChange = (page) => {
  ordersStore.fetchOrders(page, ordersStore.pagination.per_page);
};

const handlePerPageChange = (perPage) => {
  const perPageNumber = parseInt(perPage);
  ordersStore.pagination.per_page = perPageNumber;
  ordersStore.pagination.current_page = 1;
  ordersStore.fetchOrders(1, perPageNumber);
};

const fetchOrders = async () => {
  await ordersStore.fetchOrders(
    ordersStore.pagination.current_page,
    ordersStore.pagination.per_page
  );
};

const handleExpiredOrders = async () => {
  try {
    await ordersStore.handleExpiredOrders();
    await fetchOrders();
  } catch (error) {
    console.error('Error processing expired orders:', error);
  }
};

const handleViewOrder = async (order) => {
  try {
    const fullOrder = await ordersStore.fetchOrder(order.id);
    currentOrder.value = fullOrder;
    showDetailsModal.value = true;
  } catch (error) {
    console.error("Error loading order details:", error);
  }
};

// open modal and load full order details (better UX)
const handleUpdateStatusClick = async (order) => {
  try {
    // try to fetch full order from API (so currentOrder has payment/items/etc)
    const fullOrder = await ordersStore.fetchOrder(order.id);
    currentOrder.value = fullOrder;
    showStatusModal.value = true;
  } catch (err) {
    // fallback: use the minimal row if fetch failed
    currentOrder.value = order;
  }

  newStatus.value = currentOrder.value?.status || "";
  showStatusModal.value = true;
};

// called by "Update" button inside modal
const updateOrderStatus = async () => {
  if (!currentOrder.value) return;

  if (!newStatus.value) {
    // optional: show small client-side guard
    console.warn("No status selected");
    return;
  }

  try {
    await ordersStore.updateOrderStatus(currentOrder.value.id, { status: newStatus.value });

    // close modal and refresh list (and refresh opened details if shown)
    showStatusModal.value = false;
    await fetchOrders();

    if (showDetailsModal.value) {
      // refresh details view if it's open
      const updated = await ordersStore.fetchOrder(currentOrder.value.id);
      currentOrder.value = updated;
      showDetailsModal.value = true;
    }
  } catch (error) {
    console.error("Failed to update status:", error);
    // you may want to show a toast / set ordersStore.error
  }
};

const handleUpdateStatus = async (status) => {
  if (!currentOrder.value) return;

  try {
    await ordersStore.updateOrderStatus(currentOrder.value.id, { status });
    await fetchOrders();
    if (showDetailsModal.value) {
      // Refresh the current order details
      const updatedOrder = await ordersStore.fetchOrder(currentOrder.value.id);
      currentOrder.value = updatedOrder;
    }
  } catch (error) {
    console.error('Error updating order status:', error);
  }
};

const handleCancelOrderClick = (order) => {
  currentOrder.value = order;
  showCancelModal.value = true;
  cancelReason.value = '';
};

const handleCancelOrder = () => {
  showCancelModal.value = true;
};

const confirmCancelOrder = async () => {
  if (!currentOrder.value) return;

  try {
    await ordersStore.cancelOrder(currentOrder.value.id, cancelReason.value);
    showCancelModal.value = false;
    await fetchOrders();
  } catch (error) {
    console.error('Error cancelling order:', error);
  }
};

// Lifecycle
onMounted(async () => {
  await fetchOrders();
});

// Watch for route changes or other updates
watch(
  () => [ordersStore.filters.search, ordersStore.filters.status, ordersStore.filters.user_id, ordersStore.filters.start_date, ordersStore.filters.end_date],
  () => {
    // The filters are already handled by the store's setFilter method
  },
  { deep: true }
);
</script>

<style scoped>
/* Add smooth animations */
@keyframes slide-in-from-top {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-in {
  animation: slide-in-from-top 0.3s ease-out forwards;
}

/* Improve card hover effects */
.group:hover .text-gray-900 {
  color: #1f2937;
}

.group:hover .dark\:text-white {
  color: #f9fafb;
}

/* Mobile responsive adjustments */
@media (max-width: 640px) {
  .grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .p-3 {
    padding: 0.75rem;
  }

  .text-sm {
    font-size: 0.875rem;
  }

  .text-xs {
    font-size: 0.75rem;
  }
}

@media (min-width: 640px) {
  .sm\:grid-cols-4 {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }

  .sm\:p-4 {
    padding: 1rem;
  }

  .sm\:text-2xl {
    font-size: 1.5rem;
  }
}

@media (min-width: 768px) {
  .md\:p-6 {
    padding: 1.5rem;
  }

  .md\:text-3xl {
    font-size: 1.875rem;
  }
}

/* Custom status badge styles */
.status-badge {
  padding: 0.25rem 0.5rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  display: inline-block;
}
</style>
