<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="container py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    My Profile
                </h1>
                <p class="text-gray-600 mt-2 dark:text-gray-300">
                    Manage your account settings and preferences
                </p>
            </div>

            <!-- Loading State -->
            <div
                v-if="profileStore.loading && !profileStore.user"
                class="flex justify-center py-12"
            >
                <div
                    class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 dark:border-primary-500"
                ></div>
            </div>

            <!-- Profile Content -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Profile Sidebar -->
                <div class="lg:col-span-1">
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-4 dark:bg-gray-800 dark:border-gray-700"
                    >
                        <!-- Profile Header -->
                        <div class="text-center mb-6">
                            <div class="relative inline-block">
                                <!-- Avatar -->
                                <div
                                    class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden mx-auto mb-4 dark:bg-gray-700"
                                    :class="
                                        profileStore.loading
                                            ? 'animate-pulse'
                                            : ''
                                    "
                                >
                                    <img
                                        v-if="
                                            profileStore.user &&
                                            profileStore.user.avatar_url
                                        "
                                        :src="profileStore.user.avatar_url"
                                        :alt="profileStore.user?.name || 'User'"
                                        class="w-full h-full object-cover"
                                        @error="handleImageError"
                                    />
                                    <div
                                        v-else
                                        class="w-full h-full flex items-center justify-center"
                                    >
                                        <span
                                            class="text-2xl font-semibold text-gray-600 dark:text-gray-300"
                                        >
                                            {{ profileStore.userInitials }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Edit Avatar Button -->
                                <button
                                    @click="showAvatarModal = true"
                                    class="absolute bottom-0 right-0 p-2 bg-primary-600 text-white rounded-full shadow-lg hover:bg-primary-700 transition-all duration-200 transform hover:scale-110 dark:bg-primary-700 dark:hover:bg-primary-800"
                                    :title="t('profile.editAvatar')"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <h2
                                class="text-xl font-semibold text-gray-900 dark:text-white"
                            >
                                {{ profileStore.user?.name || "User" }}
                            </h2>
                            <p class="text-gray-500 dark:text-gray-400">
                                {{ profileStore.user?.email || "N/A" }}
                            </p>
                            <p
                                class="text-sm text-gray-500 mt-1 dark:text-gray-400"
                            >
                                Member since {{ profileStore.memberSince }}
                            </p>
                        </div>

                        <!-- Profile Stats -->
                        <div v-if="profileStore.stats" class="space-y-4">
                            <div
                                class="border-t border-gray-200 pt-4 dark:border-gray-700"
                            >
                                <h3
                                    class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3 dark:text-gray-400"
                                >
                                    {{ t("profile.statistics") }}
                                </h3>
                                <div class="space-y-3">
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span
                                            class="text-sm text-gray-600 dark:text-gray-400"
                                            >{{ t("profile.orders") }}</span
                                        >
                                        <span
                                            class="font-medium text-gray-900 dark:text-white"
                                            >{{
                                                profileStore.stats.total_orders
                                            }}</span
                                        >
                                    </div>
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span
                                            class="text-sm text-gray-600 dark:text-gray-400"
                                            >{{ t("profile.reviews") }}</span
                                        >
                                        <span
                                            class="font-medium text-gray-900 dark:text-white"
                                            >{{
                                                profileStore.stats.total_reviews
                                            }}</span
                                        >
                                    </div>
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span
                                            class="text-sm text-gray-600 dark:text-gray-400"
                                            >{{ t("profile.wishlist") }}</span
                                        >
                                        <span
                                            class="font-medium text-gray-900 dark:text-white"
                                            >{{
                                                profileStore.stats
                                                    .total_wishlist_items
                                            }}</span
                                        >
                                    </div>
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span
                                            class="text-sm text-gray-600 dark:text-gray-400"
                                            >{{ t("profile.spent") }}</span
                                        >
                                        <span
                                            class="font-medium text-gray-900 dark:text-white"
                                            >{{
                                                formatPrice(
                                                    profileStore.stats
                                                        .total_spent,
                                                )
                                            }}
                                            {{
                                                siteStore.settings.currency
                                            }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div
                            class="border-t border-gray-200 pt-4 mt-6 dark:border-gray-700"
                        >
                            <h3
                                class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3 dark:text-gray-400"
                            >
                                {{ t("profile.navigation") }}
                            </h3>
                            <nav class="space-y-2">
                                <router-link
                                    to="/orders"
                                    class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:bg-gray-700"
                                >
                                    <svg
                                        class="h-4 w-4 mr-3 text-gray-400 dark:text-gray-500"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                        />
                                    </svg>
                                    {{ t("profile.orders") }}
                                </router-link>
                                <router-link
                                    to="/wishlist"
                                    class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:bg-gray-700"
                                >
                                    <svg
                                        class="h-4 w-4 mr-3 text-gray-400 dark:text-gray-500"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                                        />
                                    </svg>
                                    {{ t("profile.wishlist") }}
                                </router-link>
                                <router-link
                                    to="/addresses"
                                    class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:bg-gray-700"
                                >
                                    <svg
                                        class="h-4 w-4 mr-3 text-gray-400 dark:text-gray-500"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                    </svg>
                                    {{ t("profile.addresses") }}
                                </router-link>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="lg:col-span-2">
                    <!-- Profile Form -->
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 dark:bg-gray-800 dark:border-gray-700"
                    >
                        <h2
                            class="text-xl font-semibold text-gray-900 mb-6 dark:text-white"
                        >
                            {{ t("profile.basicInfo") }}
                        </h2>

                        <form @submit.prevent="updateProfile" class="space-y-6">
                            <!-- Name -->
                            <div>
                                <label
                                    for="name"
                                    class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                                >
                                    {{ t("profile.name") }}
                                </label>
                                <input
                                    id="name"
                                    v-model="profileStore.formData.name"
                                    type="text"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    :disabled="profileStore.loading"
                                    required
                                />
                            </div>

                            <!-- Email (Read-only) -->
                            <div>
                                <label
                                    for="email"
                                    class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                                >
                                    {{ t("profile.email") }}
                                </label>
                                <input
                                    id="email"
                                    :value="profileStore.user?.email || ''"
                                    type="email"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400"
                                    disabled
                                />
                                <p
                                    class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ t("profile.emailReadOnly") }}
                                </p>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label
                                    for="phone"
                                    class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                                >
                                    {{ t("profile.phone") }}
                                </label>
                                <input
                                    id="phone"
                                    v-model="profileStore.formData.phone"
                                    type="tel"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    :disabled="profileStore.loading"
                                    placeholder="+1 (555) 123-4567"
                                />
                            </div>

                            <!-- Address -->
                            <div>
                                <label
                                    for="address"
                                    class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                                >
                                    {{ t("profile.address") }}
                                </label>
                                <textarea
                                    id="address"
                                    v-model="profileStore.formData.address"
                                    rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors resize-none dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    :disabled="profileStore.loading"
                                    placeholder="Enter your full address"
                                ></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4">
                                <button
                                    type="submit"
                                    :disabled="profileStore.loading"
                                    class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-primary-700 dark:hover:bg-primary-800"
                                >
                                    <span v-if="profileStore.loading">
                                        <svg
                                            class="animate-spin h-5 w-5 mr-2 inline"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                class="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                stroke-width="4"
                                            ></circle>
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            ></path>
                                        </svg>
                                        {{ t("profile.updating") }}
                                    </span>
                                    <span v-else>{{
                                        t("profile.updateProfile")
                                    }}</span>
                                </button>
                            </div>

                            <!-- Success Message -->
                            <div
                                v-if="
                                    profileStore.successMessage &&
                                    !profileStore.error
                                "
                                class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg dark:bg-green-900/30 dark:border-green-800"
                            >
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg
                                            class="h-5 w-5 text-green-400"
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
                                    <div class="ml-3">
                                        <p
                                            class="text-sm text-green-700 dark:text-green-300"
                                        >
                                            {{ profileStore.successMessage }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Error Message -->
                            <div
                                v-if="profileStore.error"
                                class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg dark:bg-red-900/30 dark:border-red-800"
                            >
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg
                                            class="h-5 w-5 text-red-400"
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
                                    </div>
                                    <div class="ml-3">
                                        <p
                                            class="text-sm text-red-700 dark:text-red-300"
                                        >
                                            {{ profileStore.error }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Password Change Form -->
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 dark:bg-gray-800 dark:border-gray-700"
                    >
                        <h2
                            class="text-xl font-semibold text-gray-900 mb-6 dark:text-white"
                        >
                            {{ t("profile.changePassword") }}
                        </h2>

                        <form
                            @submit.prevent="changePassword"
                            class="space-y-6"
                        >
                            <!-- Current Password -->
                            <div>
                                <label
                                    for="current_password"
                                    class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                                >
                                    {{ t("profile.currentPassword") }}
                                </label>
                                <input
                                    id="current_password"
                                    v-model="
                                        profileStore.passwordData
                                            .current_password
                                    "
                                    type="password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    :disabled="profileStore.loading"
                                    required
                                />
                            </div>

                            <!-- New Password -->
                            <div>
                                <label
                                    for="password"
                                    class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                                >
                                    {{ t("profile.newPassword") }}
                                </label>
                                <input
                                    id="password"
                                    v-model="profileStore.passwordData.password"
                                    type="password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    :disabled="profileStore.loading"
                                    required
                                    minlength="8"
                                />
                                <p
                                    class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ t("profile.passwordRequirements") }}
                                </p>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label
                                    for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                                >
                                    {{ t("profile.confirmPassword") }}
                                </label>
                                <input
                                    id="password_confirmation"
                                    v-model="
                                        profileStore.passwordData
                                            .password_confirmation
                                    "
                                    type="password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    :disabled="profileStore.loading"
                                    required
                                />
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4">
                                <button
                                    type="submit"
                                    :disabled="profileStore.loading"
                                    class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-primary-700 dark:hover:bg-primary-800"
                                >
                                    <span v-if="profileStore.loading">
                                        <svg
                                            class="animate-spin h-5 w-5 mr-2 inline"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                class="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                stroke-width="4"
                                            ></circle>
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            ></path>
                                        </svg>
                                        {{ t("profile.changingPassword") }}
                                    </span>
                                    <span v-else>{{
                                        t("profile.changePassword")
                                    }}</span>
                                </button>
                                <button
                                    type="button"
                                    @click="profileStore.resetPasswordForm"
                                    class="ml-4 px-6 py-3 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
                                >
                                    {{ t("profile.cancel") }}
                                </button>
                            </div>

                            <!-- Success Message -->
                            <div
                                v-if="
                                    profileStore.successMessage &&
                                    !profileStore.error
                                "
                                class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg dark:bg-green-900/30 dark:border-green-800"
                            >
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg
                                            class="h-5 w-5 text-green-400"
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
                                    <div class="ml-3">
                                        <p
                                            class="text-sm text-green-700 dark:text-green-300"
                                        >
                                            {{ profileStore.successMessage }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Error Message -->
                            <div
                                v-if="profileStore.error"
                                class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg dark:bg-red-900/30 dark:border-red-800"
                            >
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg
                                            class="h-5 w-5 text-red-400"
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
                                    </div>
                                    <div class="ml-3">
                                        <p
                                            class="text-sm text-red-700 dark:text-red-300"
                                        >
                                            {{ profileStore.error }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avatar Modal -->
        <div
            v-if="showAvatarModal"
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
            @click="showAvatarModal = false"
        >
            <div
                class="fixed inset-0 bg-black bg-opacity-50 transition-opacity dark:bg-gray-900 dark:bg-opacity-75"
            ></div>

            <div
                class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-auto p-6 transform transition-all dark:bg-gray-800"
                @click.stop
            >
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ t("profile.changeAvatar") }}
                    </h3>
                    <button
                        @click="showAvatarModal = false"
                        class="p-2 text-gray-400 hover:text-gray-600 transition-colors dark:text-gray-500 dark:hover:text-gray-300"
                    >
                        <svg
                            class="h-6 w-6"
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
                    <!-- Current Avatar Preview -->
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden dark:bg-gray-700"
                        >
                            <img
                                v-if="
                                    profileStore.user &&
                                    profileStore.user.avatar_url
                                "
                                :src="profileStore.user.avatar_url"
                                :alt="profileStore.user?.name || 'User'"
                                class="w-full h-full object-cover"
                            />
                            <div
                                v-else
                                class="w-full h-full flex items-center justify-center"
                            >
                                <span
                                    class="text-lg font-semibold text-gray-600 dark:text-gray-300"
                                >
                                    {{ profileStore.userInitials }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <p
                                class="text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ profileStore.user?.name || "User" }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Current avatar
                            </p>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label
                            for="avatar-upload"
                            class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                        >
                            {{ t("profile.uploadNewAvatar") }}
                        </label>
                        <input
                            id="avatar-upload"
                            type="file"
                            accept="image/*"
                            @change="handleAvatarUpload"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                        <p
                            class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                        >
                            {{ t("profile.avatarRequirements") }}
                        </p>
                    </div>

                    <!-- Preview -->
                    <div v-if="previewUrl" class="mt-4">
                        <p
                            class="text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                        >
                            {{ t("profile.preview") }}
                        </p>
                        <div
                            class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden mx-auto dark:bg-gray-700"
                        >
                            <img
                                :src="previewUrl"
                                alt="Preview"
                                class="w-full h-full object-cover"
                            />
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3 mt-8">
                    <button
                        @click="saveAvatar"
                        :disabled="!previewUrl || profileStore.loading"
                        class="flex-1 bg-primary-600 text-white py-3 px-4 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed dark:bg-primary-700 dark:hover:bg-primary-800"
                    >
                        {{ t("profile.saveAvatar") }}
                    </button>
                    <button
                        @click="removeAvatar"
                        class="px-4 py-3 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all dark:border-red-600 dark:text-red-400 dark:hover:bg-red-900/30"
                    >
                        {{ t("profile.removeAvatar") }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useProfileStore } from "../stores/profile";
import { useAuthStore } from "../stores/auth";
import { useSiteStore } from "../stores/site";

const siteStore = useSiteStore();
const profileStore = useProfileStore();
const authStore = useAuthStore();

// Modal state
const showAvatarModal = ref(false);
const previewUrl = ref(null);

// Image error handling
const imageError = ref(false);

// Initialize profile on mount
onMounted(async () => {
    await profileStore.initializeProfile();
});

// Handle image error
const handleImageError = () => {
    imageError.value = true;
};

// Handle avatar upload
const handleAvatarUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        profileStore.setAvatar(file);
        previewUrl.value = URL.createObjectURL(file);
    }
};

// Save avatar
const saveAvatar = async () => {
    if (!profileStore.formData.avatar) return;

    const result = await profileStore.updateProfile();
    if (result.success) {
        showAvatarModal.value = false;
        previewUrl.value = null;
    }
};

// Remove avatar
const removeAvatar = async () => {
    if (!confirm("Are you sure you want to remove your avatar?")) {
        return;
    }

    const result = await profileStore.removeAvatar();
    if (result.success) {
        showAvatarModal.value = false;
        previewUrl.value = null;
    }
};

// Update profile
const updateProfile = async () => {
    await profileStore.updateProfile();
};

// Change password
const changePassword = async () => {
    await profileStore.changePassword();
};

// Format price helper
const formatPrice = (price) => {
    return parseFloat(price || 0).toFixed(2);
};

// Translation helper
const t = (key) => {
    const translations = {
        "profile.basicInfo": "Basic Information",
        "profile.changePassword": "Change Password",
        "profile.name": "Full Name",
        "profile.email": "Email Address",
        "profile.emailReadOnly": "Email cannot be changed for security reasons",
        "profile.phone": "Phone Number",
        "profile.address": "Address",
        "profile.updateProfile": "Update Profile",
        "profile.updating": "Updating...",
        "profile.currentPassword": "Current Password",
        "profile.newPassword": "New Password",
        "profile.confirmPassword": "Confirm New Password",
        "profile.passwordRequirements": "Minimum 8 characters",
        "profile.changingPassword": "Changing Password...",
        "profile.cancel": "Cancel",
        "profile.statistics": "Statistics",
        "profile.orders": "Orders",
        "profile.reviews": "Reviews",
        "profile.wishlist": "Wishlist Items",
        "profile.spent": "Total Spent",
        "profile.navigation": "Quick Links",
        "profile.addresses": "Addresses",
        "profile.editAvatar": "Edit Avatar",
        "profile.changeAvatar": "Change Avatar",
        "profile.uploadNewAvatar": "Upload New Avatar",
        "profile.avatarRequirements": "JPG, PNG, GIF or WEBP. Max 8MB.",
        "profile.preview": "Preview",
        "profile.saveAvatar": "Save Avatar",
        "profile.removeAvatar": "Remove Avatar",
    };
    return translations[key] || key;
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
