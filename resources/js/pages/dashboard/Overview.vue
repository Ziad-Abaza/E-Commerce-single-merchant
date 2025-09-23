<!-- resources/js/pages/dashboard/Overview.vue -->
<template>
  <div class="p-2 sm:p-4 md:p-6 space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
          Dashboard Overview
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
          Get a comprehensive view of your store's performance
        </p>
      </div>
      <div class="flex flex-wrap gap-2">
        <button
          @click="refreshData"
          :disabled="dashboardStore.loading"
          class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 disabled:bg-gray-500 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
        >
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Refresh
        </button>
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

    <!-- Date Range Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700">
      <div class="flex flex-wrap gap-2">
        <button
          @click="dashboardStore.fetchLast7Days"
          :class="dashboardStore.currentPeriod === '7_days' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
          class="px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors duration-200"
        >
          Last 7 Days
        </button>
        <button
          @click="dashboardStore.fetchLast30Days"
          :class="dashboardStore.currentPeriod === '30_days' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
          class="px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors duration-200"
        >
          Last 30 Days
        </button>
        <button
          @click="dashboardStore.fetchLast90Days"
          :class="dashboardStore.currentPeriod === '90_days' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
          class="px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors duration-200"
        >
          Last 90 Days
        </button>
        <button
          @click="dashboardStore.fetchLastYear"
          :class="dashboardStore.currentPeriod === '1_year' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
          class="px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors duration-200"
        >
          Last Year
        </button>
        <div class="flex items-center gap-2">
          <input
            v-model="customStartDate"
            type="date"
            class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
            @change="applyCustomDateRange"
          />
          <span class="text-gray-500 dark:text-gray-400">to</span>
          <input
            v-model="customEndDate"
            type="date"
            class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
            @change="applyCustomDateRange"
          />
        </div>
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="dashboardStore.loading" class="space-y-6">
      <!-- Stats Cards Skeleton -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div v-for="n in 4" :key="n" class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="animate-pulse">
            <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-1/2 mb-2"></div>
            <div class="h-8 bg-gray-300 dark:bg-gray-700 rounded w-3/4"></div>
          </div>
        </div>
      </div>

      <!-- Charts Skeleton -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="animate-pulse h-64 bg-gray-300 dark:bg-gray-700 rounded"></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="animate-pulse h-64 bg-gray-300 dark:bg-gray-700 rounded"></div>
        </div>
      </div>

      <!-- Recent Activity Skeleton -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="animate-pulse">
          <div class="h-6 bg-gray-300 dark:bg-gray-700 rounded w-1/3 mb-4"></div>
          <div class="space-y-3">
            <div v-for="n in 3" :key="n" class="flex items-center space-x-4">
              <div class="w-10 h-10 bg-gray-300 dark:bg-gray-700 rounded-full"></div>
              <div class="flex-1 space-y-2">
                <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded"></div>
                <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-2/3"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards (Visible when not loading) -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Total Products -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Products</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
              {{ dashboardStore.statistics.total_products }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {{ dashboardStore.productActivationRate }}% active
            </p>
          </div>
          <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Total Orders -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Orders</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
              {{ dashboardStore.statistics.total_orders }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {{ dashboardStore.orderCompletionRate }}% completed
            </p>
          </div>
          <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Total Revenue -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
              {{ dashboardStore.formatCurrency(dashboardStore.statistics.total_revenue) }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {{ dashboardStore.totalRevenueInPeriod ? dashboardStore.formatCurrency(dashboardStore.totalRevenueInPeriod) : '0' }} in period
            </p>
          </div>
          <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Total Users -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Users</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
              {{ dashboardStore.statistics.total_users }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {{ dashboardStore.monthlyNewUsers }} new this month
            </p>
          </div>
          <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div v-if="!dashboardStore.loading" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Orders Trend Chart -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Orders Trend</h3>
        <div class="h-64">
          <LineChart :data="dashboardStore.orderTrendChartData" :options="chartOptions" />
        </div>
      </div>

      <!-- Order Status Distribution -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Status Distribution</h3>
        <div class="h-64">
          <PieChart :data="dashboardStore.orderStatusChartData" :options="pieChartOptions" />
        </div>
      </div>
    </div>

    <!-- Top Selling Products Chart -->
    <div v-if="!dashboardStore.loading" class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Selling Products</h3>
      <div class="h-80">
        <BarChart :data="dashboardStore.topProductsChartData" :options="barChartOptions" />
      </div>
    </div>

    <!-- Recent Activity Section -->
    <div v-if="!dashboardStore.loading" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Recent Orders -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Orders</h3>
          <span class="text-sm text-gray-500 dark:text-gray-400">{{ dashboardStore.recentOrdersCount }} orders</span>
        </div>
        <div class="space-y-4">
          <div
            v-for="order in dashboardStore.recent_activity.recent_orders.slice(0, 5)"
            :key="order.id"
            class="border-b border-gray-200 dark:border-gray-700 pb-3 last:border-b-0"
          >
            <div class="flex justify-between items-start">
              <div>
                <p class="font-medium text-gray-900 dark:text-white">{{ order.order_number }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ order.user?.name || 'Unknown User' }}</p>
              </div>
              <div class="text-right">
                <p class="font-medium text-gray-900 dark:text-white">{{ dashboardStore.formatCurrency(parseFloat(order.total)) }}</p>
                <span
                  :class="dashboardStore.getStatusBadgeClass(order.status)"
                  class="inline-block px-2 py-1 text-xs rounded-full mt-1"
                >
                  {{ order.status }}
                </span>
              </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {{ dashboardStore.formatDate(order.created_at) }}
            </p>
          </div>
        </div>
      </div>

      <!-- Recent Products -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Products</h3>
          <span class="text-sm text-gray-500 dark:text-gray-400">{{ dashboardStore.recentProductsCount }} products</span>
        </div>
        <div class="space-y-4">
          <div
            v-for="product in dashboardStore.recent_activity.recent_products.slice(0, 5)"
            :key="product.id"
            class="border-b border-gray-200 dark:border-gray-700 pb-3 last:border-b-0"
          >
            <div class="flex justify-between items-start">
              <div>
                <p class="font-medium text-gray-900 dark:text-white">{{ product.name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  {{ product.categories?.join(', ') || 'No Category' }}
                </p>
              </div>
              <span
                :class="product.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'"
                class="inline-block px-2 py-1 text-xs rounded-full"
              >
                {{ product.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Reviews -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Reviews</h3>
          <span class="text-sm text-gray-500 dark:text-gray-400">{{ dashboardStore.recentReviewsCount }} reviews</span>
        </div>
        <div class="space-y-4">
          <div
            v-for="review in dashboardStore.recent_activity.recent_reviews.slice(0, 5)"
            :key="review.id"
            class="border-b border-gray-200 dark:border-gray-700 pb-3 last:border-b-0"
          >
            <div class="flex items-center mb-2">
                <div class="flex mr-2">
                    <svg
                    v-for="(star, index) in dashboardStore.getRatingStars(review.rating)"
                    :key="index"
                    class="w-4 h-4 text-yellow-400"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <svg
                    v-for="(star, index) in dashboardStore.getRatingStars(5 - parseInt(review.rating))"
                    :key="index"
                    class="w-4 h-4 text-gray-300 dark:text-gray-600"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ review.user?.name || 'Unknown User' }}</span>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">{{ review.comment }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {{ dashboardStore.formatDate(review.created_at) }} • {{ review.product?.name || 'Unknown Product' }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useDashboardStore } from '../../stores/dashboard/overview';
import LineChart from './components/overview/LineChart.vue';
import PieChart from './components/overview/PieChart.vue';
import BarChart from './components/overview/BarChart.vue';

const dashboardStore = useDashboardStore();
const customStartDate = ref('');
const customEndDate = ref('');

// Chart options
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      mode: 'index',
      intersect: false,
    },
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: {
        color: 'rgba(0, 0, 0, 0.1)',
      },
      ticks: {
        color: '#6b7280',
      }
    },
    x: {
      grid: {
        display: false,
      },
      ticks: {
        color: '#6b7280',
      }
    }
  }
};

const pieChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
      labels: {
        color: '#6b7280',
      }
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          return `${context.label}: ${context.raw} (${context.parsed}%)`;
        }
      }
    }
  }
};

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          return `${context.dataset.label}: ${context.raw}`;
        }
      }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: {
        color: 'rgba(0, 0, 0, 0.1)',
      },
      ticks: {
        color: '#6b7280',
      }
    },
    x: {
      grid: {
        display: false,
      },
      ticks: {
        color: '#6b7280',
      }
    }
  }
};

// Lifecycle
onMounted(async () => {
  await dashboardStore.fetchLast30Days();
});

// Methods
const refreshData = async () => {
  await dashboardStore.fetchOverview(
    dashboardStore.currentPeriod,
    dashboardStore.customStartDate,
    dashboardStore.customEndDate
  );
};

const applyCustomDateRange = () => {
  if (customStartDate.value && customEndDate.value) {
    dashboardStore.fetchCustomPeriod(customStartDate.value, customEndDate.value);
  }
};
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
</style>
