<template>
    <div class="p-2 sm:p-4 md:p-6 space-y-4 sm:space-y-6">
      <!-- Page Header -->
      <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
              Promo Codes Management
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
              Create and manage discount codes and promotions
            </p>
          </div>
          <div class="flex flex-col sm:flex-row gap-2">
            <button
              @click="fetchPromoCodes"
              class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
            >
              <svg
                class="w-4 h-4 mr-1"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                />
              </svg>
              Refresh
            </button>
            <button
              @click="openCreateForm"
              class="px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
            >
              <svg
                class="w-4 h-4 mr-1"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 4v16m8-8H4"
                />
              </svg>
              New Promo Code
            </button>
            <button
              v-if="showTrash"
              @click="showTrash = false"
              class="px-3 sm:px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Back to List
            </button>
            <button
              v-else
              @click="showTrash = true"
              class="px-3 sm:px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              View Trash ({{ deletedCount }})
            </button>
          </div>
        </div>
      </div>
  
      <!-- Filters -->
      <div v-if="!showTrash" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Search -->
          <div>
            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Search
            </label>
            <input
              type="text"
              id="search"
              v-model="filters.search"
              @keyup.enter="applyFilters"
              placeholder="Search by code or name..."
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
            />
          </div>
  
          <!-- Status -->
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Status
            </label>
            <select
              id="status"
              v-model="filters.status"
              @change="applyFilters"
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
            >
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
  
          <!-- Discount Type -->
          <div>
            <label for="discount_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Discount Type
            </label>
            <select
              id="discount_type"
              v-model="filters.discount_type"
              @change="applyFilters"
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
            >
              <option value="">All Types</option>
              <option value="percentage">Percentage</option>
              <option value="fixed">Fixed Amount</option>
            </select>
          </div>
  
          <!-- Target Type -->
          <div>
            <label for="target_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Target Type
            </label>
            <select
              id="target_type"
              v-model="filters.target_type"
              @change="applyFilters"
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
            >
              <option value="">All Targets</option>
              <option v-for="type in availableFilters.target_types" :key="type" :value="type">
                {{ capitalize(type) }}
              </option>
            </select>
          </div>
        </div>
  
        <div class="mt-4 flex justify-between items-center">
          <button
            @click="resetFilters"
            class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 flex items-center"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Reset Filters
          </button>
          <button
            @click="applyFilters"
            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md shadow-sm transition-colors duration-200"
          >
            Apply Filters
          </button>
        </div>
      </div>
  
      <!-- Stats Cards -->
      <div v-if="!showTrash" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Promo Codes -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Promo Codes</p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.total_promos || 0 }}</p>
            </div>
          </div>
        </div>
  
        <!-- Active Promo Codes -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Promos</p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.active_promos || 0 }}</p>
            </div>
          </div>
        </div>
  
        <!-- Expired Promo Codes -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Expired Promos</p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.expired_promos || 0 }}</p>
            </div>
          </div>
        </div>
  
        <!-- Total Usage -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Usage</p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.total_usage || 0 }}</p>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Promo Codes Table -->
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Loading State -->
        <div v-if="isLoading" class="p-8 text-center">
          <div class="inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-white bg-blue-600 rounded-md">
            <svg class="w-4 h-4 mr-2 -ml-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Loading {{ showTrash ? 'deleted ' : '' }}promo codes...
          </div>
        </div>
  
        <!-- Empty State -->
        <div v-else-if="promoCodes.length === 0" class="text-center py-12">
          <svg
            class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
            />
          </svg>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No {{ showTrash ? 'deleted ' : '' }}promo codes found</h3>
          <p v-if="!showTrash" class="text-gray-500 dark:text-gray-400 mb-4">
            Get started by creating a new promo code.
          </p>
          <div class="mt-4">
            <button
              v-if="!showTrash"
              @click="openCreateForm"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg
                class="-ml-1 mr-2 h-5 w-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 4v16m8-8H4"
                />
              </svg>
              New Promo Code
            </button>
            <button
              v-else
              @click="showTrash = false"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Back to Promo Codes
            </button>
          </div>
        </div>
  
        <!-- Table -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Code
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Name
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Discount
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Target
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Usage
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Status
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="promo in promoCodes" :key="promo.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ promo.code }}
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-white">{{ promo.name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-white">
                    {{ promo.discount_type === 'percentage' ? `${promo.discount_value}%` : `$${promo.discount_value}` }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-white capitalize">
                    {{ promo.target_type }}
                    <div v-if="promo.products && promo.products.length > 0" class="text-xs text-gray-500 dark:text-gray-400">
                      {{ promo.products.length }} products
                    </div>
                    <div v-else-if="promo.categories && promo.categories.length > 0" class="text-xs text-gray-500 dark:text-gray-400">
                      {{ promo.categories.length }} categories
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-white">
                    {{ promo.total_usage_count || 0 }}
                    <span v-if="promo.total_usage_limit" class="text-xs text-gray-500 dark:text-gray-400">
                      / {{ promo.total_usage_limit }}
                    </span>
                  </div>
                  <div v-if="promo.per_user_usage_limit" class="text-xs text-gray-500 dark:text-gray-400">
                    {{ promo.per_user_usage_limit }} per user
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="{
                      'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300': promo.is_active,
                      'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300': !promo.is_active
                    }"
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  >
                    {{ promo.is_active ? 'Active' : 'Inactive' }}
                  </span>
                  <div v-if="promo.end_date" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ formatDate(promo.end_date) }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-2">
                    <template v-if="!showTrash">
                      <button
                        @click="viewPromoCode(promo)"
                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                        title="View details"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                      </button>
                      <button
                        @click="editPromoCode(promo)"
                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                        title="Edit"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                      <button
                        @click="confirmDelete(promo)"
                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                        title="Delete"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </template>
                    <template v-else>
                      <button
                        @click="restorePromoCode(promo)"
                        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                        title="Restore"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                      </button>
                      <button
                        @click="confirmForceDelete(promo)"
                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                        title="Permanently Delete"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </template>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
  
        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              @click="previousPage"
              :disabled="pagination.current_page === 1"
              :class="{'opacity-50 cursor-not-allowed': pagination.current_page === 1}"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Previous
            </button>
            <button
              @click="nextPage"
              :disabled="pagination.current_page === pagination.last_page"
              :class="{'opacity-50 cursor-not-allowed': pagination.current_page === pagination.last_page}"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Next
            </button>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700 dark:text-gray-300">
                Showing
                <span class="font-medium">{{ pagination.from || 0 }}</span>
                to
                <span class="font-medium">{{ pagination.to || 0 }}</span>
                of
                <span class="font-medium">{{ pagination.total || 0 }}</span>
                results
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <button
                  @click="previousPage"
                  :disabled="pagination.current_page === 1"
                  :class="{'opacity-50 cursor-not-allowed': pagination.current_page === 1}"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                  <span class="sr-only">Previous</span>
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>
                
                <!-- Page numbers -->
                <template v-for="page in pagination.last_page" :key="page">
                  <button
                    v-if="Math.abs(page - pagination.current_page) < 3 || page === 1 || page === pagination.last_page"
                    @click="goToPage(page)"
                    :class="{
                      'z-10 bg-blue-50 border-blue-500 text-blue-600 dark:bg-blue-900/30 dark:border-blue-700 dark:text-blue-300': pagination.current_page === page,
                      'bg-white dark:bg-gray-700 border-gray-300 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-600': pagination.current_page !== page
                    }"
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                  >
                    {{ page }}
                  </button>
                  <span
                    v-else-if="Math.abs(page - pagination.current_page) === 3"
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300"
                  >
                    ...
                  </span>
                </template>
                
                <button
                  @click="nextPage"
                  :disabled="pagination.current_page === pagination.last_page"
                  :class="{'opacity-50 cursor-not-allowed': pagination.current_page === pagination.last_page}"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                  <span class="sr-only">Next</span>
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>
              </nav>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Promo Code Form Modal -->
      <TransitionRoot as="template" :show="isFormOpen">
        <Dialog as="div" class="fixed inset-0 z-10 overflow-y-auto" @close="closeForm">
          <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <TransitionChild
              as="template"
              enter="ease-out duration-300"
              enter-from="opacity-0"
              enter-to="opacity-100"
              leave="ease-in duration-200"
              leave-from="opacity-100"
              leave-to="opacity-0"
            >
              <DialogOverlay class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>
  
            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <TransitionChild
              as="template"
              enter="ease-out duration-300"
              enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
              enter-to="opacity-100 translate-y-0 sm:scale-100"
              leave="ease-in duration-200"
              leave-from="opacity-100 translate-y-0 sm:scale-100"
              leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
              <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full sm:p-6">
                <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                  <button
                    type="button"
                    class="bg-white dark:bg-gray-700 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
                    @click="closeForm"
                  >
                    <span class="sr-only">Close</span>
                    <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                  </button>
                </div>
                
                <div class="sm:flex sm:items-start">
                  <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 sm:mx-0 sm:h-10 sm:w-10">
                    <TagIcon class="h-6 w-6 text-blue-600 dark:text-blue-400" aria-hidden="true" />
                  </div>
                  <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <DialogTitle as="h3" class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                      {{ form.id ? 'Edit Promo Code' : 'Create New Promo Code' }}
                    </DialogTitle>
                    
                    <div class="mt-4">
                      <form @submit.prevent="submitForm" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <!-- Code -->
                          <div class="col-span-1">
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                              Code <span class="text-red-500">*</span>
                            </label>
                            <input
                              type="text"
                              id="code"
                              v-model="form.code"
                              required
                              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                              :disabled="!!form.id"
                            />
                          </div>
                          
                          <!-- Name -->
                          <div class="col-span-1">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                              Name <span class="text-red-500">*</span>
                            </label>
                            <input
                              type="text"
                              id="name"
                              v-model="form.name"
                              required
                              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                            />
                          </div>
                          
                          <!-- Discount Type -->
                          <div class="col-span-1">
                            <label for="discount_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                              Discount Type <span class="text-red-500">*</span>
                            </label>
                            <select
                              id="discount_type"
                              v-model="form.discount_type"
                              required
                              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                            >
                              <option value="">Select Type</option>
                              <option value="percentage">Percentage</option>
                              <option value="fixed">Fixed Amount</option>
                            </select>
                          </div>
                          
                          <!-- Discount Value -->
                          <div class="col-span-1">
                            <label for="discount_value" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                              Discount Value <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                              <div v-if="form.discount_type === 'percentage'" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">%</span>
                              </div>
                              <div v-else class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                              </div>
                              <input
                                type="number"
                                id="discount_value"
                                v-model.number="form.discount_value"
                                required
                                :min="form.discount_type === 'percentage' ? 1 : 0.01"
                                :max="form.discount_type === 'percentage' ? 100 : null"
                                step="0.01"
                                class="pl-7 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                              />
                            </div>
                          </div>
                          
                          <!-- Target Type -->
                          <div class="col-span-1">
                            <label for="target_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                              Target Type <span class="text-red-500">*</span>
                            </label>
                            <select
                              id="target_type"
                              v-model="form.target_type"
                              required
                              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                            >
                              <option value="">Select Target</option>
                              <option v-for="type in availableFilters.target_types" :key="type" :value="type">
                                {{ capitalize(type) }}
                              </option>
                            </select>
                          </div>
                          
                          <!-- Status -->
                          <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                              Status
                            </label>
                            <div class="mt-2">
                              <label class="inline-flex items-center">
                                <input
                                  type="checkbox"
                                  v-model="form.is_active"
                                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600"
                                >
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                              </label>
                            </div>
                          </div>
                          
                          <!-- Start Date -->
                          <div class="col-span-1">
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                              Start Date
                            </label>
                            <input
                              type="datetime-local"
                              id="start_date"
                              v-model="form.start_date"
                              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                            />
                          </div>
                          
                          <!-- End Date -->
                          <div class="col-span-1">
                            <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                              End Date
                            </label>
                            <input
                              type="datetime-local"
                              id="end_date"
                              v-model="form.end_date"
                              :min="form.start_date"
                              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                            />
                          </div>
                          
                          <!-- Total Usage Limit -->
                          <div class="col-span-1">
                            <label for="total_usage_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                              Total Usage Limit
                            </label>
                            <input
                              type="number"
                              id="total_usage_limit"
                              v-model.number="form.total_usage_limit"
                              min="1"
                              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                              placeholder="Leave empty for no limit"
                            />
                          </div>
                          
                          <!-- Per User Usage Limit -->
                          <div class="col-span-1">
                            <label for="per_user_usage_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                              Per User Usage Limit
                            </label>
                            <input
                              type="number"
                              id="per_user_usage_limit"
                              v-model.number="form.per_user_usage_limit"
                              min="1"
                              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                              placeholder="Leave empty for no limit"
                            />
                          </div>
                        </div>
                        
                        <!-- Target Selection (Products) -->
                        <div v-if="form.target_type === 'products'" class="mt-4">
                          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Select Products <span class="text-red-500">*</span>
                          </label>
                          <div class="border border-gray-300 dark:border-gray-600 rounded-md p-3 max-h-60 overflow-y-auto">
                            <div v-if="productsLoading" class="text-center py-4">
                              <svg class="animate-spin h-5 w-5 mx-auto text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                              </svg>
                            </div>
                            <div v-else-if="products.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                              No products available
                            </div>
                            <div v-else class="space-y-2">
                              <div v-for="product in products" :key="product.id" class="flex items-center">
                                <input
                                  :id="`product-${product.id}`"
                                  v-model="form.product_ids"
                                  type="checkbox"
                                  :value="product.id"
                                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label :for="`product-${product.id}`" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                  {{ product.name }} - ${{ product.price }}
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <!-- Target Selection (Categories) -->
                        <div v-else-if="form.target_type === 'categories'" class="mt-4">
                          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Select Categories <span class="text-red-500">*</span>
                          </label>
                          <div class="border border-gray-300 dark:border-gray-600 rounded-md p-3 max-h-60 overflow-y-auto">
                            <div v-if="categoriesLoading" class="text-center py-4">
                              <svg class="animate-spin h-5 w-5 mx-auto text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                              </svg>
                            </div>
                            <div v-else-if="categories.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                              No categories available
                            </div>
                            <div v-else class="space-y-2">
                              <div v-for="category in categories" :key="category.id" class="flex items-center">
                                <input
                                  :id="`category-${category.id}`"
                                  v-model="form.category_ids"
                                  type="checkbox"
                                  :value="category.id"
                                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label :for="`category-${category.id}`" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                  {{ category.name }}
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                  <button
                    type="button"
                    @click="submitForm"
                    :disabled="isSubmitting"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ form.id ? 'Update' : 'Create' }} Promo Code
                  </button>
                  <button
                    type="button"
                    @click="closeForm"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
                  >
                    Cancel
                  </button>
                </div>
              </div>
            </TransitionChild>
          </div>
        </Dialog>
      </TransitionRoot>
  
      <!-- View Promo Code Modal -->
      <TransitionRoot as="template" :show="isViewModalOpen">
        <Dialog as="div" class="fixed inset-0 z-10 overflow-y-auto" @close="isViewModalOpen = false">
          <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <TransitionChild
              as="template"
              enter="ease-out duration-300"
              enter-from="opacity-0"
              enter-to="opacity-100"
              leave="ease-in duration-200"
              leave-from="opacity-100"
              leave-to="opacity-0"
            >
              <DialogOverlay class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>
  
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <TransitionChild
              as="template"
              enter="ease-out duration-300"
              enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
              enter-to="opacity-100 translate-y-0 sm:scale-100"
              leave="ease-in duration-200"
              leave-from="opacity-100 translate-y-0 sm:scale-100"
              leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
              <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                  <button
                    type="button"
                    class="bg-white dark:bg-gray-700 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
                    @click="isViewModalOpen = false"
                  >
                    <span class="sr-only">Close</span>
                    <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                  </button>
                </div>
                
                <div>
                  <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30">
                    <TagIcon class="h-6 w-6 text-blue-600 dark:text-blue-400" aria-hidden="true" />
                  </div>
                  <div class="mt-3 text-center sm:mt-5">
                    <DialogTitle as="h3" class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                      {{ selectedPromoCode.name }} ({{ selectedPromoCode.code }})
                    </DialogTitle>
                    
                    <div class="mt-4 space-y-4">
                      <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                          <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            Promo Code Details
                          </h3>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700">
                          <dl>
                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Code</dt>
                              <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2 font-mono">{{ selectedPromoCode.code }}</dd>
                            </div>
                            <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                              <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">{{ selectedPromoCode.name }}</dd>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Discount</dt>
                              <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                {{ selectedPromoCode.discount_type === 'percentage' ? `${selectedPromoCode.discount_value}%` : `$${selectedPromoCode.discount_value}` }}
                                <span class="text-gray-500 dark:text-gray-400 ml-2">({{ selectedPromoCode.discount_type }})</span>
                              </dd>
                            </div>
                            <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Target Type</dt>
                              <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2 capitalize">{{ selectedPromoCode.target_type }}</dd>
                            </div>
                            <div v-if="selectedPromoCode.products && selectedPromoCode.products.length > 0" class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Products</dt>
                              <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                <ul class="border border-gray-200 dark:border-gray-600 rounded-md divide-y divide-gray-200 dark:divide-gray-600">
                                  <li v-for="product in selectedPromoCode.products" :key="product.id" class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                    <div class="w-0 flex-1 flex items-center">
                                      <span class="ml-2 flex-1 w-0 truncate">{{ product.name }}</span>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                      <span class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">${{ product.price }}</span>
                                    </div>
                                  </li>
                                </ul>
                              </dd>
                            </div>
                            <div v-if="selectedPromoCode.categories && selectedPromoCode.categories.length > 0" class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Categories</dt>
                              <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                <ul class="border border-gray-200 dark:border-gray-600 rounded-md divide-y divide-gray-200 dark:divide-gray-600">
                                  <li v-for="category in selectedPromoCode.categories" :key="category.id" class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                    <div class="w-0 flex-1 flex items-center">
                                      <span class="ml-2 flex-1 w-0 truncate">{{ category.name }}</span>
                                    </div>
                                  </li>
                                </ul>
                              </dd>
                            </div>
                            <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Usage</dt>
                              <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                <div>Total: {{ selectedPromoCode.total_usage_count || 0 }} 
                                  <span v-if="selectedPromoCode.total_usage_limit" class="text-gray-500 dark:text-gray-400">
                                    / {{ selectedPromoCode.total_usage_limit }} max
                                  </span>
                                </div>
                                <div v-if="selectedPromoCode.per_user_usage_limit" class="text-gray-500 dark:text-gray-400">
                                  {{ selectedPromoCode.per_user_usage_limit }} per user
                                </div>
                              </dd>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Validity</dt>
                              <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                <div>Start: {{ formatDateTime(selectedPromoCode.start_date) || 'Immediate' }}</div>
                                <div>End: {{ selectedPromoCode.end_date ? formatDateTime(selectedPromoCode.end_date) : 'No end date' }}</div>
                              </dd>
                            </div>
                            <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                              <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                <span
                                  :class="{
                                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300': selectedPromoCode.is_active,
                                    'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300': !selectedPromoCode.is_active
                                  }"
                                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                >
                                  {{ selectedPromoCode.is_active ? 'Active' : 'Inactive' }}
                                </span>
                              </dd>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</dt>
                              <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">{{ formatDateTime(selectedPromoCode.created_at) }}</dd>
                            </div>
                            <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                              <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">{{ formatDateTime(selectedPromoCode.updated_at) }}</dd>
                            </div>
                          </dl>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="mt-5 sm:mt-6">
                  <button
                    type="button"
                    @click="isViewModalOpen = false"
                    class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm"
                  >
                    Close
                  </button>
                </div>
              </div>
            </TransitionChild>
          </div>
        </Dialog>
      </TransitionRoot>
  
      <!-- Delete Confirmation Modal -->
      <TransitionRoot as="template" :show="isDeleteModalOpen">
        <Dialog as="div" class="fixed inset-0 z-10 overflow-y-auto" @close="isDeleteModalOpen = false">
          <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <TransitionChild
              as="template"
              enter="ease-out duration-300"
              enter-from="opacity-0"
              enter-to="opacity-100"
              leave="ease-in duration-200"
              leave-from="opacity-100"
              leave-to="opacity-0"
            >
              <DialogOverlay class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>
  
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <TransitionChild
              as="template"
              enter="ease-out duration-300"
              enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
              enter-to="opacity-100 translate-y-0 sm:scale-100"
              leave="ease-in duration-200"
              leave-from="opacity-100 translate-y-0 sm:scale-100"
              leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
              <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                  <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                    <ExclamationTriangleIcon class="h-6 w-6 text-red-600 dark:text-red-400" aria-hidden="true" />
                  </div>
                  <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <DialogTitle as="h3" class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                      {{ isForceDelete ? 'Permanently Delete Promo Code' : 'Delete Promo Code' }}
                    </DialogTitle>
                    <div class="mt-2">
                      <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ isForceDelete 
                          ? 'Are you sure you want to permanently delete this promo code? This action cannot be undone.' 
                          : 'Are you sure you want to delete this promo code? This will move it to trash.' 
                        }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                  <button
                    type="button"
                    @click="isForceDelete ? forceDeletePromoCode() : deletePromoCode()"
                    :disabled="isDeleting"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <svg v-if="isDeleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ isForceDelete ? 'Permanently Delete' : 'Delete' }}
                  </button>
                  <button
                    type="button"
                    @click="isDeleteModalOpen = false"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
                  >
                    Cancel
                  </button>
                </div>
              </div>
            </TransitionChild>
          </div>
        </Dialog>
      </TransitionRoot>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted, watch } from 'vue'
  import { useRouter } from 'vue-router'
  import { usePromoCodesStore } from '@/stores/dashboard/promoCodes'
  import { useProductsStore } from '@/stores/dashboard/products'
  import { useCategoriesStore } from '@/stores/dashboard/categories'
  import { Dialog, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
  import { XMarkIcon, TagIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline'
  
  // Props
  const props = defineProps({
    initialFilters: {
      type: Object,
      default: () => ({})
    }
  })
  
  // Router
  const router = useRouter()
  
  // Stores
  const promoCodeStore = usePromoCodesStore()
  const productStore = useProductsStore()
  const categoryStore = useCategoriesStore()
  
  // State
  const isLoading = ref(false)
  const isSubmitting = ref(false)
  const isDeleting = ref(false)
  const isFormOpen = ref(false)
  const isViewModalOpen = ref(false)
  const isDeleteModalOpen = ref(false)
  const isForceDelete = ref(false)
  const showTrash = ref(false)
  const selectedPromoCode = ref(null)
  const products = ref([])
  const categories = ref([])
  const productsLoading = ref(false)
  const categoriesLoading = ref(false)
  
  // Form data
  const form = ref({
    id: null,
    code: '',
    name: '',
    discount_type: 'percentage',
    discount_value: 10,
    target_type: 'products',
    total_usage_limit: null,
    per_user_usage_limit: null,
    start_date: '',
    end_date: '',
    is_active: true,
    product_ids: [],
    category_ids: []
  })
  
  // Filters
  const filters = ref({
    search: '',
    status: '',
    discount_type: '',
    target_type: '',
    start_date: '',
    end_date: '',
    sort_by: 'created_at',
    sort_order: 'desc',
    per_page: 15,
    ...props.initialFilters
  })
  
  // Computed
  const promoCodes = computed(() => {
    return showTrash.value ? (promoCodeStore.trashedPromoCodes || []) : (promoCodeStore.promoCodes || [])
  })
  
  const pagination = computed(() => {
    return showTrash.value ? promoCodeStore.trashedPagination : promoCodeStore.pagination
  })
  
  const stats = computed(() => {
    return promoCodeStore.stats
  })
  
  const deletedCount = computed(() => {
    return promoCodeStore.trashedPagination?.total || 0
  })
  
  const availableFilters = computed(() => {
    return promoCodeStore.availableFilters || {
      target_types: ['products', 'categories', 'shipping', 'order'],
      discount_types: ['percentage', 'fixed'],
      status_options: ['active', 'inactive']
    }
  })
  
  // Watchers
  watch(() => form.value.discount_type, (newVal) => {
    // Reset discount value when type changes
    if (newVal === 'percentage') {
      form.value.discount_value = 10
    } else {
      form.value.discount_value = 5
    }
  })
  
  watch(() => form.value.target_type, async (newVal) => {
    if (newVal === 'products' && products.value.length === 0) {
      await fetchProducts()
    } else if (newVal === 'categories' && categories.value.length === 0) {
      await fetchCategories()
    }
  })
  
  // Lifecycle hooks
  onMounted(async () => {
    await Promise.all([
      fetchPromoCodes(),
      fetchStats()
    ])
  })
  
  // Methods
const capitalize = (str) => {
  if (!str) return ''
  return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase()
}

// Methods
  const fetchPromoCodes = async () => {
    isLoading.value = true
    try {
      if (showTrash.value) {
        await promoCodeStore.fetchTrashedPromoCodes(1, filters.value.per_page, filters.value)
      } else {
        await promoCodeStore.fetchPromoCodes(1, filters.value.per_page, filters.value)
      }
    } catch (error) {
      console.error('Error fetching promo codes:', error)
    } finally {
      isLoading.value = false
    }
  }
  
  const fetchStats = async () => {
    try {
      await promoCodeStore.fetchStats()
    } catch (error) {
      console.error('Error fetching stats:', error)
    }
  }
  
  const fetchProducts = async () => {
    productsLoading.value = true
    try {
      await productStore.fetchAll({ per_page: 1000 }) // Fetch all products
      products.value = productStore.items
    } catch (error) {
      console.error('Error fetching products:', error)
    } finally {
      productsLoading.value = false
    }
  }
  
  const fetchCategories = async () => {
    categoriesLoading.value = true
    try {
      await categoryStore.fetchAll({ per_page: 1000 }) // Fetch all categories
      categories.value = categoryStore.items
    } catch (error) {
      console.error('Error fetching categories:', error)
    } finally {
      categoriesLoading.value = false
    }
  }
  
  const openCreateForm = () => {
    resetForm()
    isFormOpen.value = true
  }
  
  const editPromoCode = (promo) => {
    // Convert the promo code data to match the form structure
    form.value = {
      id: promo.id,
      code: promo.code,
      name: promo.name,
      discount_type: promo.discount_type,
      discount_value: parseFloat(promo.discount_value),
      target_type: promo.target_type,
      total_usage_limit: promo.total_usage_limit,
      per_user_usage_limit: promo.per_user_usage_limit,
      start_date: promo.start_date ? formatDateForInput(promo.start_date) : '',
      end_date: promo.end_date ? formatDateForInput(promo.end_date) : '',
      is_active: promo.is_active,
      product_ids: promo.products ? promo.products.map(p => p.id) : [],
      category_ids: promo.categories ? promo.categories.map(c => c.id) : []
    }
    
    isFormOpen.value = true
  }
  
  const viewPromoCode = (promo) => {
    selectedPromoCode.value = { ...promo }
    isViewModalOpen.value = true
  }
  
  const confirmDelete = (promo) => {
    selectedPromoCode.value = { ...promo }
    isForceDelete.value = false
    isDeleteModalOpen.value = true
  }
  
  const confirmForceDelete = (promo) => {
    selectedPromoCode.value = { ...promo }
    isForceDelete.value = true
    isDeleteModalOpen.value = true
  }
  
  const deletePromoCode = async () => {
    if (!selectedPromoCode.value) return
    
    isDeleting.value = true
    try {
      await promoCodeStore.deleteItem(selectedPromoCode.value.id)
      isDeleteModalOpen.value = false
      await fetchPromoCodes()} catch (error) {
      console.error('Error deleting promo code:', error)
    } finally {
      isDeleting.value = false
    }
  }
  
  const forceDeletePromoCode = async () => {
    if (!selectedPromoCode.value) return
    
    isDeleting.value = true
    try {
      await promoCodeStore.forceDelete(selectedPromoCode.value.id)
      isDeleteModalOpen.value = false
      await fetchPromoCodes()} catch (error) {
      console.error('Error force deleting promo code:', error)
    } finally {
      isDeleting.value = false
    }
  }
  
  const restorePromoCode = async (promo) => {
    if (!promo.id) return
    
    try {
      await promoCodeStore.restore(promo.id)
      await fetchPromoCodes()} catch (error) {
      console.error('Error restoring promo code:', error)
    }
  }
  
  const submitForm = async () => {
    isSubmitting.value = true
    try {
      const formData = { ...form.value }
      
      // Convert empty strings to null for optional fields
      if (formData.total_usage_limit === '') formData.total_usage_limit = null
      if (formData.per_user_usage_limit === '') formData.per_user_usage_limit = null
      if (formData.start_date === '') formData.start_date = null
      if (formData.end_date === '') formData.end_date = null
      
      if (formData.id) {
        await promoCodeStore.update(formData.id, formData)
      } else {
        await promoCodeStore.create(formData)
      }
      
      isFormOpen.value = false
      await fetchPromoCodes()} catch (error) {
      console.error('Error saving promo code:', error)
    } finally {
      isSubmitting.value = false
    }
  }
  
  const resetForm = () => {
    form.value = {
      id: null,
      code: '',
      name: '',
      discount_type: 'percentage',
      discount_value: 10,
      target_type: 'products',
      total_usage_limit: null,
      per_user_usage_limit: null,
      start_date: '',
      end_date: '',
      is_active: true,
      product_ids: [],
      category_ids: []
    }
  }
  
  const closeForm = () => {
    isFormOpen.value = false
    resetForm()
  }
  
  const applyFilters = () => {
    // Reset to first page when filters change
    filters.value.page = 1
    fetchPromoCodes()
  }
  
  const resetFilters = () => {
    filters.value = {
      search: '',
      status: '',
      discount_type: '',
      target_type: '',
      start_date: '',
      end_date: '',
      sort_by: 'created_at',
      sort_order: 'desc',
      per_page: 15
    }
    fetchPromoCodes()
  }
  
  const previousPage = () => {
    if (pagination.value.current_page > 1) {
      filters.value.page = pagination.value.current_page - 1
      fetchPromoCodes()
    }
  }
  
  const nextPage = () => {
    if (pagination.value.current_page < pagination.value.last_page) {
      filters.value.page = pagination.value.current_page + 1
      fetchPromoCodes()
    }
  }
  
  const goToPage = (page) => {
    if (page >= 1 && page <= pagination.value.last_page && page !== pagination.value.current_page) {
      filters.value.page = page
      fetchPromoCodes()
    }
  }
  
  const formatDate = (dateString) => {
    if (!dateString) return ''
    const options = { year: 'numeric', month: 'short', day: 'numeric' }
    return new Date(dateString).toLocaleDateString(undefined, options)
  }
  
  const formatDateTime = (dateString) => {
    if (!dateString) return ''
    const options = { 
      year: 'numeric', 
      month: 'short', 
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }
    return new Date(dateString).toLocaleString(undefined, options)
  }
  
  const formatDateForInput = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    const tzOffset = date.getTimezoneOffset() * 60000 // Offset in milliseconds
    const localISOTime = new Date(date - tzOffset).toISOString().slice(0, 16)
    return localISOTime
  }
  </script>