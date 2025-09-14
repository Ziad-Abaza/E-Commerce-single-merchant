<template>
  <div class="p-2 sm:p-4 md:p-6 space-y-4 sm:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
            Dashboard Overview
          </h1>
          <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
            Welcome back! Here's what's happening with your store today.
          </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
          <select
            v-model="selectedPeriod"
            @change="updatePeriod"
            class="px-3 sm:px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200 text-sm"
          >
            <option value="7_days">Last 7 Days</option>
            <option value="30_days">Last 30 Days</option>
            <option value="90_days">Last 90 Days</option>
            <option value="1_year">Last Year</option>
          </select>
          <button
            @click="dashboardStore.loadDashboardData"
            class="px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh
          </button>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700">
<div class="flex flex-wrap gap-2">
  <router-link
    to="/dashboard/products"
    class="flex-1 min-w-[120px] px-3 py-2 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded-md text-xs font-medium transition-colors flex items-center"
  >
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
    </svg>
    Products
  </router-link>

  <router-link
    to="/dashboard/orders"
    class="flex-1 min-w-[120px] px-3 py-2 bg-green-50 hover:bg-green-100 dark:bg-green-900/30 dark:hover:bg-green-900/50 text-green-700 dark:text-green-300 rounded-md text-xs font-medium transition-colors flex items-center"
  >
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    </svg>
    Orders
  </router-link>

  <router-link
    to="/dashboard/categories"
    class="flex-1 min-w-[120px] px-3 py-2 bg-purple-50 hover:bg-purple-100 dark:bg-purple-900/30 dark:hover:bg-purple-900/50 text-purple-700 dark:text-purple-300 rounded-md text-xs font-medium transition-colors flex items-center"
  >
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
    </svg>
    Categories
  </router-link>

  <router-link
    to="/dashboard/users"
    class="flex-1 min-w-[120px] px-3 py-2 bg-orange-50 hover:bg-orange-100 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 text-orange-700 dark:text-orange-300 rounded-md text-xs font-medium transition-colors flex items-center"
  >
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
    </svg>
    Users
  </router-link>
</div>

      </div>
    </div>

    <!-- Error Alert -->
    <div
      v-if="dashboardStore.error"
      class="p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg animate-in slide-in-from-top-2 duration-300"
    >
      <div class="flex items-center">
        <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="text-sm text-red-700 dark:text-red-300 flex-1">{{ dashboardStore.error }}</span>
        <button
          @click="dashboardStore.clearError"
          class="ml-2 text-red-500 hover:text-red-700 dark:hover:text-red-300 p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="dashboardStore.loading" class="space-y-4">
      <!-- Stats Skeleton -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div
          v-for="n in 4"
          :key="n"
          class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3 animate-pulse"
        >
          <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-16 mb-2"></div>
          <div class="h-6 bg-gray-300 dark:bg-gray-700 rounded w-12"></div>
        </div>
      </div>

      <!-- Recent Activity Skeleton -->
      <div class="grid grid-cols-1 gap-4">
        <div
          v-for="n in 3"
          :key="n"
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
          <div class="p-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-24"></div>
          </div>
          <div class="p-3 space-y-3">
            <div v-for="item in 2" :key="item" class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-gray-300 dark:bg-gray-700 rounded-full"></div>
              <div class="flex-1 space-y-2">
                <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded"></div>
                <div class="h-2 bg-gray-300 dark:bg-gray-700 rounded w-2/3"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else class="space-y-4">
      <!-- Key Statistics Cards -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Total Revenue -->
        <div
          class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-3 text-white shadow-sm hover:shadow-md transition-all duration-300"
        >
          <div class="flex items-center justify-between">
            <div>
              <p class="text-blue-100 text-xs font-medium">Total Revenue</p>
              <p class="text-lg font-bold mt-1">
                {{ formatNumber(dashboardStore.statistics.total_revenue) }} {{ siteStore.settings.currency }}
              </p>
            </div>
            <div class="p-2 bg-blue-400/20 rounded-lg">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Orders -->
        <div
          class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300"
        >
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Total Orders</p>
              <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                {{ dashboardStore.statistics.total_orders }}
              </p>
              <div class="mt-1 flex flex-wrap gap-1">
                <span
                  class="text-xs px-1.5 py-0.5 bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 rounded-full"
                >
                  {{ dashboardStore.statistics.pending_orders }}
                </span>
                <span
                  class="text-xs px-1.5 py-0.5 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded-full"
                >
                  {{ dashboardStore.statistics.completed_orders }}
                </span>
              </div>
            </div>
            <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
              <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Products -->
        <div
          class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300"
        >
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Products</p>
              <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                {{ dashboardStore.statistics.total_products }}
              </p>
              <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                {{ dashboardStore.statistics.active_products }} Active
              </div>
            </div>
            <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
              <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Users -->
        <div
          class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300"
        >
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Users</p>
              <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                {{ dashboardStore.statistics.total_users }}
              </p>
              <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                {{ dashboardStore.userAnalytics.active_users }} Active
              </div>
            </div>
            <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
              <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity Section -->
      <div class="space-y-4">
        <!-- Recent Orders -->
        <div
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
          <div class="p-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            <div class="flex items-center justify-between">
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Recent Orders</h2>
              <router-link
                to="/dashboard/orders"
                class="text-xs text-blue-600 dark:text-blue-400 hover:underline"
              >
                View All
              </router-link>
            </div>
          </div>
          <div class="p-3">
            <div
              v-if="dashboardStore.recentOrders.length === 0"
              class="text-center py-4 text-xs text-gray-500 dark:text-gray-400"
            >
              No recent orders
            </div>
            <div v-else class="space-y-3">
              <div
                v-for="order in dashboardStore.recentOrders.slice(0, 3)"
                :key="order.id"
                class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-sm transition-shadow cursor-pointer group"
                @click="viewOrderDetails(order)"
              >
                <div class="flex justify-between items-start mb-2">
                  <div class="min-w-0">
                    <p class="font-medium text-gray-900 dark:text-white text-sm truncate">
                      #{{ order.order_number }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                      {{ order.user?.name || "Customer" }}
                    </p>
                  </div>
                  <div class="text-right ml-2 flex-shrink-0">
                    <p class="font-bold text-gray-900 dark:text-white text-sm">
                      {{ order.total_amount }} {{ siteStore.settings.currency }}
                    </p>
                    <span
                      class="inline-block px-2 py-0.5 text-xs font-medium rounded-full mt-1 whitespace-nowrap"
                      :class="{
                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300': order.status === 'pending',
                        'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300': order.status === 'completed',
                        'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300': order.status === 'cancelled',
                      }"
                    >
                      {{ order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                    </span>
                  </div>
                </div>
                <div class="flex flex-wrap gap-1 mt-2">
                  <span
                    v-for="(item, index) in order.items.slice(0, 2)"
                    :key="index"
                    class="text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full"
                  >
                    {{ item.product_name }}
                  </span>
                  <span
                    v-if="order.items.length > 2"
                    class="text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full"
                  >
                    +{{ order.items.length - 2 }}
                  </span>
                </div>
                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                  {{ formatDate(order.created_at) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Products -->
        <div
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
          <div class="p-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            <div class="flex items-center justify-between">
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Recent Products</h2>
              <router-link
                to="/dashboard/products"
                class="text-xs text-blue-600 dark:text-blue-400 hover:underline"
              >
                View All
              </router-link>
            </div>
          </div>
          <div class="p-3">
            <div
              v-if="dashboardStore.recentProducts.length === 0"
              class="text-center py-4 text-xs text-gray-500 dark:text-gray-400"
            >
              No recent products
            </div>
            <div v-else class="space-y-3">
              <div
                v-for="product in dashboardStore.recentProducts.slice(0, 3)"
                :key="product.id"
                class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer group"
                @click="viewProductDetails(product)"
              >
                <!-- Brand Icon -->
                <div
                  class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-md flex items-center justify-center text-xs font-medium text-white"
                >
                  {{ product.brand?.charAt(0) || "?" }}
                </div>

                <!-- Product Info -->
                <div class="ml-3 flex-1 min-w-0">
                  <p class="font-medium text-gray-900 dark:text-white text-sm truncate">
                    {{ product.name }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    {{ product.brand }}
                  </p>
                </div>

                <!-- Variants Count -->
                <div class="flex-shrink-0 text-right ml-2">
                  <p class="text-xs font-medium text-gray-900 dark:text-white">
                    {{ product.details?.length || 0 }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Reviews -->
        <div
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
          <div class="p-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            <div class="flex items-center justify-between">
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Recent Reviews</h2>
              <span class="text-xs text-blue-600 dark:text-blue-400 hover:underline cursor-pointer">
                View All
              </span>
            </div>
          </div>
          <div class="p-3">
            <div
              v-if="dashboardStore.recentReviews.length === 0"
              class="text-center py-4 text-xs text-gray-500 dark:text-gray-400"
            >
              No recent reviews
            </div>
            <div v-else class="space-y-3">
              <div
                v-for="review in dashboardStore.recentReviews.slice(0, 3)"
                :key="review.id"
                class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-sm transition-shadow cursor-pointer group"
                @click="viewReviewDetails(review)"
              >
                <div class="flex items-center mb-2">
                  <div class="flex items-center">
                    <span
                      v-for="i in 5"
                      :key="i"
                      class="text-yellow-400 mr-0.5 text-xs"
                      :class="{ 'text-gray-300 dark:text-gray-600': i > review.rating }"
                    >
                      ★
                    </span>
                  </div>
                  <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">
                    ({{ review.rating }})
                  </span>
                </div>
                <h3 class="font-medium text-gray-900 dark:text-white text-sm mb-1 line-clamp-1">
                  {{ review.title }}
                </h3>
                <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2 mb-2">
                  {{ review.comment }}
                </p>
                <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                  <span>{{ review.user?.name || "User" }}</span>
                  <span>{{ formatDate(review.created_at) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Analytics Section -->
      <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
      >
        <div class="p-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
          <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Sales & Revenue Analytics</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            Period: {{ formatDate(dashboardStore.salesAnalytics.start_date) }} to {{ formatDate(dashboardStore.salesAnalytics.end_date) }}
          </p>
        </div>
        <div class="p-3">
          <div class="grid grid-cols-2 gap-3 mb-4">
            <!-- Revenue -->
            <div
              class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-3 text-white"
            >
              <p class="text-green-100 text-xs font-medium">Total Revenue</p>
              <p class="text-lg font-bold mt-1">
                {{ formatNumber(dashboardStore.revenueAnalytics.total_revenue) }} {{ siteStore.settings.currency }}
              </p>
            </div>

            <!-- Average Order Value -->
            <div
              class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700"
            >
              <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Avg Order Value</p>
              <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                {{ formatNumber(dashboardStore.revenueAnalytics.average_order_value) }} {{ siteStore.settings.currency }}
              </p>
            </div>

            <!-- Order Completion Rate -->
            <div
              class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700"
            >
              <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Completion Rate</p>
              <div class="mt-1">
                <div class="flex items-center justify-between mb-1">
                  <span class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ dashboardStore.completionRate }}%
                  </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                  <div
                    class="bg-blue-600 h-1.5 rounded-full transition-all duration-500 ease-out"
                    :style="{ width: dashboardStore.completionRate + '%' }"
                  ></div>
                </div>
              </div>
            </div>

            <!-- Active Users -->
            <div
              class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700"
            >
              <p class="text-gray-500 dark:text-gray-400 text-xs font-medium">Active Users</p>
              <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                {{ dashboardStore.userAnalytics.active_users }}
              </p>
            </div>
          </div>

          <!-- Top Products -->
          <div
            v-if="dashboardStore.topSellingProducts.length > 0"
            class="mb-4"
          >
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Top Selling Products</h3>
            <div class="space-y-2">
              <div
                v-for="(product, index) in dashboardStore.topSellingProducts.slice(0, 3)"
                :key="index"
                class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-2 bg-gray-50 dark:bg-gray-900/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800/50 transition-colors cursor-pointer group"
                @click="viewProductDetails(product)"
              >
                <div class="flex items-center mb-1 sm:mb-0">
                  <span class="text-xs font-bold text-gray-500 dark:text-gray-400 mr-2 min-w-4">
                    {{ index + 1 }}
                  </span>
                  <div>
                    <p class="font-medium text-gray-900 dark:text-white text-sm">
                      {{ product.name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      {{ formatNumber(product.total_sold) }} sold
                    </p>
                  </div>
                </div>
                <div class="w-full sm:w-32 mt-1 sm:mt-0">
                  <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                    <div
                      class="bg-purple-600 h-1.5 rounded-full transition-all duration-500 ease-out"
                      :style="{ width: getRelativeWidth(product.total_sold) + '%' }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Status Distribution -->
          <div
            v-if="dashboardStore.salesAnalytics.order_status_distribution.length > 0"
          >
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Order Status</h3>
            <div class="grid grid-cols-2 gap-3">
              <div
                v-for="status in dashboardStore.salesAnalytics.order_status_distribution"
                :key="status.status"
                class="p-2 bg-gray-50 dark:bg-gray-900/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800/50 transition-colors cursor-pointer group"
                @click="filterOrdersByStatus(status.status)"
              >
                <div class="text-center">
                  <div class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ status.count }}
                  </div>
                  <div class="text-xs text-gray-500 dark:text-gray-400 capitalize mt-1">
                    {{ status.status }}
                  </div>
                  <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                    <div
                      class="h-1.5 rounded-full transition-all duration-500 ease-out"
                      :class="{
                        'bg-yellow-500': status.status === 'pending',
                        'bg-green-500': status.status === 'completed',
                        'bg-red-500': status.status === 'cancelled',
                        'bg-gray-500': status.status === 'refunded',
                      }"
                      :style="{ width: getDistributionWidth(status.count) + '%' }"
                    ></div>
                  </div>
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
import { ref, onMounted, watch } from "vue";
import { useDashboardStore } from "@/stores/dashboard/overview";
import { useTheme } from "@/composables/useTheme";
import { useRouter } from 'vue-router';
import { useSiteStore } from "@/stores/site";

const siteStore = useSiteStore();
const dashboardStore = useDashboardStore();
const { isDark } = useTheme();
const router = useRouter();

const selectedPeriod = ref("30_days");

watch(() => dashboardStore.salesAnalytics.period, (newPeriod) => {
  if (newPeriod) {
    selectedPeriod.value = newPeriod;
  }
});

const updatePeriod = async () => {
  await dashboardStore.updateAnalyticsPeriod(selectedPeriod.value);
};

const formatNumber = (num) => {
  if (!num) return "0.00";
  return parseFloat(num).toLocaleString(undefined, {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
};

const getRelativeWidth = (value) => {
  if (!dashboardStore.topSellingProducts.length) return 0;
  const max = Math.max(
    ...dashboardStore.topSellingProducts.map((p) => p.total_sold),
  );
  return (value / max) * 100;
};

const getDistributionWidth = (count) => {
  if (!dashboardStore.salesAnalytics.order_status_distribution.length)
    return 0;
  const total =
    dashboardStore.salesAnalytics.order_status_distribution.reduce(
      (sum, item) => sum + item.count,
      0,
    );
  return (count / total) * 100;
};

const formatDate = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleDateString(undefined, {
    month: 'short',
    day: 'numeric',
  });
};

const viewOrderDetails = (order) => {
  router.push(`/dashboard/orders/${order.id}`);
};

const viewProductDetails = (product) => {
  router.push(`/dashboard/products/${product.id}`);
};

const viewReviewDetails = (review) => {
  console.log("View review details:", review);
};

const filterOrdersByStatus = (status) => {
  router.push(`/dashboard/orders?status=${status}`);
};

onMounted(() => {
  dashboardStore.loadDashboardData();
});
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
  color: #3044ca;
}

/* Ensure proper text wrapping */
.text-wrap {
  word-wrap: break-word;
  word-break: break-word;
}

/* Mobile first design */
@media (max-width: 640px) {
  .grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .flex-col {
    flex-direction: column;
  }

  .gap-3 > *:not(:last-child) {
    margin-bottom: 0.5rem;
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

/* Tablet and larger */
@media (min-width: 640px) {
  .sm\:grid-cols-4 {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }

  .sm\:flex-row {
    flex-direction: row;
  }

  .sm\:p-4 {
    padding: 1rem;
  }

  .sm\:text-2xl {
    font-size: 1.5rem;
  }
}

/* Desktop and larger */
@media (min-width: 768px) {
  .md\:p-6 {
    padding: 1.5rem;
  }

  .md\:text-3xl {
    font-size: 1.875rem;
  }
}
</style>
