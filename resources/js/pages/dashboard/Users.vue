<!-- resources/js/pages/dashboard/Users.vue -->
<template>
    <div class="p-2 sm:p-4 md:p-6 space-y-4 sm:space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col gap-4">
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
            >
                <div>
                    <h1
                        class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 dark:text-white"
                    >
                        Users Management
                    </h1>
                    <p
                        class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1"
                    >
                        Manage user accounts, permissions, and activity
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <button
                        @click="fetchUsers"
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
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                            />
                        </svg>
                        Add User
                    </button>
                </div>
            </div>
        </div>

        <!-- Error Alert -->
        <div
            v-if="usersStore.error"
            class="p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg animate-in slide-in-from-top-2 duration-300"
        >
            <div class="flex items-center">
                <svg
                    class="w-5 h-5 text-red-500 mr-2 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
                <span class="text-sm text-red-700 dark:text-red-300 flex-1">{{
                    usersStore.error
                }}</span>
                <button
                    @click="usersStore.clearError"
                    class="ml-2 text-red-500 hover:text-red-700 dark:hover:text-red-300 p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
                >
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <!-- Total Users -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-gray-500 dark:text-gray-400 text-xs font-medium"
                        >
                            Total Users
                        </p>
                        <p
                            class="text-lg font-bold text-gray-900 dark:text-white mt-1"
                        >
                            {{ usersStore.statistics?.total_users }}
                        </p>
                    </div>
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <svg
                            class="w-5 h-5 text-blue-600 dark:text-blue-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Users -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-gray-500 dark:text-gray-400 text-xs font-medium"
                        >
                            Active
                        </p>
                        <p
                            class="text-lg font-bold text-green-600 dark:text-green-400 mt-1"
                        >
                            {{ usersStore.statistics?.active_users }}
                        </p>
                    </div>
                    <div
                        class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg"
                    >
                        <svg
                            class="w-5 h-5 text-green-600 dark:text-green-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Inactive Users -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-gray-500 dark:text-gray-400 text-xs font-medium"
                        >
                            Inactive
                        </p>
                        <p
                            class="text-lg font-bold text-red-600 dark:text-red-400 mt-1"
                        >
                            {{ usersStore.statistics?.inactive_users }}
                        </p>
                    </div>
                    <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                        <svg
                            class="w-5 h-5 text-red-600 dark:text-red-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Trashed Users -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-gray-500 dark:text-gray-400 text-xs font-medium"
                        >
                            Trashed
                        </p>
                        <p
                            class="text-lg font-bold text-yellow-600 dark:text-yellow-400 mt-1"
                        >
                            {{ usersStore.statistics?.trashed_users }}
                        </p>
                    </div>
                    <div
                        class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg"
                    >
                        <svg
                            class="w-5 h-5 text-yellow-600 dark:text-yellow-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Verified Users -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-gray-500 dark:text-gray-400 text-xs font-medium"
                        >
                            Verified
                        </p>
                        <p
                            class="text-lg font-bold text-purple-600 dark:text-purple-400 mt-1"
                        >
                            {{ usersStore.statistics?.verified_users }}
                        </p>
                    </div>
                    <div
                        class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg"
                    >
                        <svg
                            class="w-5 h-5 text-purple-600 dark:text-purple-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Unverified Users -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-gray-500 dark:text-gray-400 text-xs font-medium"
                        >
                            Unverified
                        </p>
                        <p
                            class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-1"
                        >
                            {{ usersStore.statistics?.unverified_users }}
                        </p>
                    </div>
                    <div
                        class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg"
                    >
                        <svg
                            class="w-5 h-5 text-indigo-600 dark:text-indigo-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700"
        >
            <div class="flex flex-col sm:flex-row gap-3 items-end">
                <!-- Search -->
                <div class="flex-1">
                    <Search
                        v-model="usersStore.filters.search"
                        placeholder="Search by name, email, or phone..."
                        @submit="handleSearch"
                    />
                </div>

                <!-- Status Filter -->
                <Select
                    v-model="usersStore.filters.is_active"
                    :options="statusOptions"
                    placeholder="All Statuses"
                    label="Status"
                    @update:modelValue="handleStatusFilter"
                />

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
        <div
            v-if="usersStore.loading && !usersStore.users.length"
            class="space-y-4"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <div class="p-4 space-y-3">
                    <div
                        v-for="n in 5"
                        :key="n"
                        class="flex items-center space-x-4"
                    >
                        <div
                            class="w-12 h-12 bg-gray-300 dark:bg-gray-700 rounded-lg animate-pulse"
                        ></div>
                        <div class="flex-1 space-y-2">
                            <div
                                class="h-4 bg-gray-300 dark:bg-gray-700 rounded animate-pulse"
                            ></div>
                            <div
                                class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-2/3 animate-pulse"
                            ></div>
                        </div>
                        <div
                            class="w-20 h-8 bg-gray-300 dark:bg-gray-700 rounded animate-pulse"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div
            v-else
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
            <!-- Empty State -->
            <div v-if="usersStore.users.length === 0" class="text-center py-12">
                <svg
                    class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                    />
                </svg>
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                >
                    No users found
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    {{
                        hasActiveFilters
                            ? "Try adjusting your filters."
                            : "Get started by adding your first user."
                    }}
                </p>
                <button
                    v-if="!hasActiveFilters"
                    @click="openCreateForm"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                >
                    Add Your First User
                </button>
            </div>

            <!-- Users Table -->
            <div v-else>
                <Table :headers="tableHeaders" :rows="tableRows" />
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="usersStore.users.length > 0" class="flex justify-center">
            <Pagination
                :total="usersStore.pagination.total"
                :current-page="usersStore.pagination.current_page"
                :per-page="usersStore.pagination.per_page"
                :last-page="usersStore.pagination.last_page"
                @page-change="handlePageChange"
                @update:perPage="handlePerPageChange"
            />
        </div>

        <!-- Modals -->
        <!-- Create/Edit Form Modal -->
        <div
            v-if="showFormModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            @click.self="showFormModal = false"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3
                            class="text-xl font-bold text-gray-900 dark:text-white"
                        >
                            {{ isEditing ? "Edit User" : "Add New User" }}
                        </h3>
                        <button
                            @click="showFormModal = false"
                            class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>

                    <Form
                        :model-fields="formFields"
                        @submit="handleSubmitForm"
                    />
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmModal
            :show="showDeleteConfirm"
            title="Delete User"
            :message="`Are you sure you want to delete '${userToDelete?.name || 'this user'}'? This action cannot be undone.`"
            confirm-text="Delete"
            confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
            @confirm="handleDelete"
            @cancel="showDeleteConfirm = false"
            :loading="usersStore.deleting"
        />

        <!-- User Details Modal -->
        <div
            v-if="showDetailsModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            @click.self="showDetailsModal = false"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3
                            class="text-xl font-bold text-gray-900 dark:text-white"
                        >
                            User Details
                        </h3>
                        <button
                            @click="showDetailsModal = false"
                            class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>

                    <div v-if="currentUser" class="space-y-6">
                        <!-- User Info Card -->
                        <div
                            class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5"
                        >
                            <div class="flex flex-col sm:flex-row gap-4">
                                <div class="flex-shrink-0">
                                    <img
                                        :src="
                                            currentUser.avatar_url ||
                                            '/images/placeholder-user.png'
                                        "
                                        :alt="currentUser.name"
                                        class="w-24 h-24 object-cover rounded-full"
                                    />
                                </div>
                                <div class="flex-1">
                                    <h4
                                        class="text-lg font-bold text-gray-900 dark:text-white"
                                    >
                                        {{ currentUser.name }}
                                    </h4>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        {{ currentUser.email }}
                                    </p>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                                    >
                                        {{
                                            currentUser.phone ||
                                            "No phone number"
                                        }}
                                    </p>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                                    >
                                        {{
                                            currentUser.address || "No address"
                                        }}
                                    </p>
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        <span
                                            :class="[
                                                'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                                                currentUser.is_active
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                                                    : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                            ]"
                                        >
                                            {{
                                                currentUser.is_active
                                                    ? "Active"
                                                    : "Inactive"
                                            }}
                                        </span>
                                        <span
                                            :class="[
                                                'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                                                currentUser.email_verified_at
                                                    ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300'
                                                    : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                            ]"
                                        >
                                            {{
                                                currentUser.email_verified_at
                                                    ? "Verified"
                                                    : "Unverified"
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Details -->
                        <div
                            class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5"
                        >
                            <h4
                                class="font-semibold text-lg text-gray-900 dark:text-white mb-4"
                            >
                                Account Information
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >User ID</label
                                    >
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ currentUser.id }}
                                    </p>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >Email</label
                                    >
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ currentUser.email }}
                                    </p>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >Phone</label
                                    >
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ currentUser.phone || "N/A" }}
                                    </p>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >Address</label
                                    >
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ currentUser.address || "N/A" }}
                                    </p>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >Created At</label
                                    >
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ formatDate(currentUser.created_at) }}
                                    </p>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >Last Updated</label
                                    >
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ formatDate(currentUser.updated_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Roles Section -->
                        <div
                            class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5"
                        >
                            <div class="flex items-center justify-between mb-4">
                                <h4
                                    class="font-semibold text-lg text-gray-900 dark:text-white"
                                >
                                    User Roles
                                </h4>
                                <button
                                    @click="handleManageRoles(currentUser)"
                                    class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm transition-colors flex items-center"
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
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                        />
                                    </svg>
                                    Manage Roles
                                </button>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="role in currentUser.roles"
                                    :key="role.id"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300"
                                >
                                    {{ role.name }}
                                </span>
                                <span
                                    v-if="
                                        !currentUser.roles ||
                                        currentUser.roles.length === 0
                                    "
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300"
                                >
                                    No roles assigned
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-3 justify-end">
                            <button
                                @click="handleEdit(currentUser)"
                                class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
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
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                    />
                                </svg>
                                Edit User
                            </button>
                            <button
                                @click="handleDeleteClick(currentUser)"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
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
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                                Delete User
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Management Modal -->
        <div
            v-if="showRoleModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            @click.self="showRoleModal = false"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3
                            class="text-xl font-bold text-gray-900 dark:text-white"
                        >
                            Manage Roles for {{ userForRoles?.name }}
                        </h3>
                        <button
                            @click="showRoleModal = false"
                            class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Select the roles you want to assign to this user:
                        </p>

                        <div class="space-y-3 max-h-60 overflow-y-auto">
                            <div
                                v-for="role in usersStore.roles"
                                :key="role.id"
                                class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            >
                                <input
                                    :id="`role-${role.id}`"
                                    type="checkbox"
                                    :checked="selectedRoles.includes(role.id)"
                                    @change="
                                        handleRoleChange(
                                            role.id,
                                            $event.target.checked,
                                        )
                                    "
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                />
                                <label
                                    :for="`role-${role.id}`"
                                    class="ml-3 flex-1 cursor-pointer"
                                >
                                    <div
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ role.name }}
                                    </div>
                                    <div
                                        v-if="role.description"
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        {{ role.description }}
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button
                                @click="showRoleModal = false"
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                @click="handleSaveRoles"
                                :disabled="usersStore.loading"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-lg transition-colors flex items-center"
                            >
                                <svg
                                    v-if="usersStore.loading"
                                    class="w-4 h-4 mr-2 animate-spin"
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
                                {{
                                    usersStore.loading
                                        ? "Saving..."
                                        : "Save Roles"
                                }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useUsersStore } from "../../stores/dashboard/users";
import { useAuthStore } from "../../stores/auth";
import debounce from "lodash/debounce";
import Search from "./components/Search.vue";
import Select from "./components/Select.vue";
import Table from "./components/Table.vue";
import Pagination from "./components/Pagination.vue";
import Form from "./components/Form.vue";
import ConfirmModal from "./components/ConfirmModal.vue";

const usersStore = useUsersStore();
const authStore = useAuthStore();

const showFormModal = ref(false);
const isEditing = ref(false);
const showDeleteConfirm = ref(false);
const showDetailsModal = ref(false);
const showRoleModal = ref(false);
const userToDelete = ref(null);
const currentUser = ref(null);
const userForRoles = ref(null);
const formFields = ref([]);
const selectedRoles = ref([]);
const searchQuery = ref(usersStore.filters.search);

const debouncedSearch = debounce(() => {
    usersStore.filters.search = searchQuery.value;
    handleSearch();
}, 400);

// Check permissions
if (!authStore.hasPermission("manage_users")) {
    throw new Error(
        "Access denied: You do not have permission to manage users",
    );
}

// Filter options
const statusOptions = ref([
    { value: "1", label: "Active" },
    { value: "0", label: "Inactive" },
]);

// Table headers
const tableHeaders = ref([
    { key: "avatar", label: "Avatar" },
    { key: "name", label: "Name" },
    { key: "email", label: "Email" },
    { key: "phone", label: "Phone" },
    { key: "roles", label: "Roles" },
    { key: "status", label: "Status" },
    { key: "actions", label: "Actions" },
]);

// Computed properties
const hasActiveFilters = computed(() => {
    return usersStore.filters.search || usersStore.filters.is_active;
});

const tableRows = computed(() => {
    return usersStore.users.map((user) => ({
        id: user.id,
        avatar: {
            type: "image",
            src: user.avatar_url || "/images/placeholder-user.png",
            full: user.avatar_url || "/images/placeholder-user.png",
        },
        name: user.name || "N/A",
        email: user.email || "N/A",
        phone: user.phone || "N/A",
        roles: {
            type: "roles",
            value: user.roles || [],
        },
        status: {
            type: "status",
            value: user.is_active ? "Active" : "Inactive",
            class: user.is_active
                ? "px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300"
                : "px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300",
        },
        actions: [
            {
                label: "View",
                icon: "eye",
                class: "bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300",
                onClick: () => handleView(user),
            },
            {
                label: "Edit",
                icon: "edit",
                class: "bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300",
                onClick: () => handleEdit(user),
            },
            {
                label: "Manage Roles",
                icon: "users",
                class: "bg-purple-100 text-purple-700 hover:bg-purple-200 dark:bg-purple-900/30 dark:text-purple-300",
                onClick: () => handleManageRoles(user),
            },
            {
                label: "Delete",
                icon: "trash",
                class: "bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300",
                onClick: () => handleDeleteClick(user),
            },
        ],
    }));
});

// Event handlers
const handleSearch = (searchTerm) => {
    usersStore.setFilter("search", searchTerm);
};

const handleStatusFilter = (value) => {
    usersStore.setFilter("is_active", value);
};

const clearAllFilters = () => {
    usersStore.clearFilters();
};

const handlePageChange = (page) => {
    usersStore.fetchUsers(page, usersStore.pagination.per_page);
};

const handlePerPageChange = (perPage) => {
    const perPageNumber = parseInt(perPage);
    usersStore.pagination.per_page = perPageNumber;
    usersStore.pagination.current_page = 1;
    usersStore.fetchUsers(1, perPageNumber);
};

const fetchUsers = async () => {
    await usersStore.fetchUsers(
        usersStore.pagination.current_page,
        usersStore.pagination.per_page,
    );
};

const openCreateForm = () => {
    isEditing.value = false;
    currentUser.value = null;
    initializeFormFields(null);
    showFormModal.value = true;
};

const handleView = async (user) => {
    try {
        const fullUser = await usersStore.fetchUser(user.id);
        currentUser.value = fullUser;
        showDetailsModal.value = true;
    } catch (error) {
        console.error("Error loading user details:", error);
    }
};

const handleEdit = async (user) => {
    isEditing.value = true;
    userToDelete.value = user;

    try {
        const fullUser = await usersStore.fetchUser(user.id);
        currentUser.value = fullUser;
        initializeFormFields(fullUser);
        showFormModal.value = true;
    } catch (error) {
        console.error("Error loading user for edit:", error);
    }
};

const initializeFormFields = (user) => {
    formFields.value = [
        {
            id: "name",
            label: "Full Name",
            type: "text",
            value: user?.name || "",
            required: true,
            placeholder: "Enter full name",
        },
        {
            id: "email",
            label: "Email Address",
            type: "email",
            value: user?.email || "",
            required: true,
            placeholder: "Enter email address",
        },
        {
            id: "avatar",
            label: "User Avatar",
            type: "file",
            value: user?.avatar || null,
            required: false,
            acceptedTypes:
                "image/jpeg,image/png,image/jpg,image/webp,image/gif",
            placeholder: "Upload user avatar image",
        },
        {
            id: "phone",
            label: "Phone Number",
            type: "text",
            value: user?.phone || "",
            required: false,
            placeholder: "Enter phone number",
        },
        {
            id: "address",
            label: "Address",
            type: "textarea",
            value: user?.address || "",
            required: false,
            placeholder: "Enter address",
        },
        {
            id: "password",
            label: isEditing.value
                ? "New Password (leave blank to keep current)"
                : "Password",
            type: "password",
            value: "",
            required: !isEditing.value,
            placeholder: isEditing.value
                ? "Enter new password"
                : "Enter password",
        },
        {
            id: "password_confirmation",
            label: isEditing.value
                ? "Confirm New Password"
                : "Confirm Password",
            type: "password",
            value: "",
            required: !isEditing.value,
            placeholder: isEditing.value
                ? "Confirm new password"
                : "Confirm password",
        },
        {
            id: "is_active",
            label: "Active Status",
            type: "checkbox",
            value: user?.is_active || false,
            required: false,
        },
    ];
};

const handleDeleteClick = (user) => {
    userToDelete.value = user;
    showDeleteConfirm.value = true;
};

const handleSubmitForm = async (data) => {
    try {
        if (isEditing.value && userToDelete.value) {
            // Remove password fields if they're empty during edit
            const updateData = { ...data };
            if (!updateData.password) {
                delete updateData.password;
                delete updateData.password_confirmation;
            }

            await usersStore.updateUser(userToDelete.value.id, updateData);
        } else {
            await usersStore.createUser(data);
        }

        // Refresh the list
        await fetchUsers();

        // Close the form
        showFormModal.value = false;
        formFields.value = [];
        userToDelete.value = null;
        currentUser.value = null;
    } catch (error) {
        console.error("Form submission error:", error);
    }
};

const handleDelete = async () => {
    if (!userToDelete.value) return;

    try {
        await usersStore.deleteUser(userToDelete.value.id);
        showDeleteConfirm.value = false;
        userToDelete.value = null;
    } catch (error) {
        console.error("Delete error:", error);
    }
};

// Inside Users.vue script setup
const handleManageRoles = async (user) => {
    try {
        // Close other modals first
        showDetailsModal.value = false;
        showFormModal.value = false;
        // Set up role management
        userForRoles.value = user;
        selectedRoles.value =
            user.roles && Array.isArray(user.roles)
                ? user.roles.map((role) => role.id).filter((id) => id != null)
                : [];
        showRoleModal.value = true;
    } catch (error) {
        console.error("Error opening role management:", error);
        usersStore.error = "Failed to open role management: " + error.message;
    }
};

const handleRoleChange = (roleId, isChecked) => {
    if (isChecked) {
        if (!selectedRoles.value.includes(roleId)) {
            selectedRoles.value.push(roleId);
        }
    } else {
        selectedRoles.value = selectedRoles.value.filter((id) => id !== roleId);
    }
};

const handleSaveRoles = async () => {
    if (!userForRoles.value) {
        console.error("No user selected for role update");
        return;
    }

    try {
        await usersStore.assignRole(userForRoles.value.id, selectedRoles.value);

        // Refresh user data
        await fetchUsers();

        // Update current user if it's the same user
        if (
            currentUser.value &&
            currentUser.value.id === userForRoles.value.id
        ) {
            const updatedUser = await usersStore.fetchUser(
                userForRoles.value.id,
            );
            currentUser.value = updatedUser;
        }

        // Close modal and reset states
        showRoleModal.value = false;
        userForRoles.value = null;
        selectedRoles.value = [];
    } catch (error) {
        console.error("Error updating user roles:", error);
        usersStore.error =
            "Failed to update user roles: " +
            (error.response?.data?.message || error.message);
    }
};

// Helper functions
const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    try {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        }).format(date);
    } catch (e) {
        return dateString;
    }
};

// Lifecycle
onMounted(async () => {
    await fetchUsers();
});

// Watch for filter changes
watch(searchQuery, () => {
    debouncedSearch();
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
</style>
