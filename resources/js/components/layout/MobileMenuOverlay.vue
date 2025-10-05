<template>
    <div class="fixed inset-0 z-50 lg:hidden">
        <!-- Backdrop -->
        <div
            class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70"
            @click="$emit('close')"
        ></div>

        <!-- Menu Panel -->
        <div
            class="fixed inset-y-0 right-0 w-full max-w-sm bg-white dark:bg-gray-800 shadow-xl transition-colors duration-200"
        >
            <div class="flex flex-col h-full">
                <!-- Header -->
                <div
                    class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700"
                >
                    <div class="flex items-center space-x-2">
                        <div
                            class="h-8 w-8 bg-primary-600 rounded-lg flex items-center justify-center"
                        >
                            <svg
                                class="h-5 w-5 text-white"
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
                        </div>
                        <span
                            class="text-lg font-bold text-gray-900 dark:text-white"
                            >{{
                                siteStore.settings.site_name || "E-Commerce"
                            }}</span
                        >
                    </div>
                    <button
                        @click="$emit('close')"
                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                        aria-label="Close menu"
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

                <!-- Search Bar -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <SearchBar />
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto p-4">
                    <ul class="space-y-2">
                        <li>
                            <router-link
                                to="/"
                                @click="$emit('close')"
                                class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md transition-colors"
                                :class="{
                                    'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-gray-700':
                                        $route.name === 'home',
                                }"
                            >
                                <svg
                                    class="h-5 w-5 mr-3"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                    />
                                </svg>
                                Home
                            </router-link>
                        </li>

                        <li>
                            <router-link
                                to="/products"
                                @click="$emit('close')"
                                class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-md transition-colors dark:text-gray-400 dark:hover:text-primary-400 dark:hover:bg-gray-700"
                                :class="{
                                    'text-primary-600 bg-primary-50 dark:text-primary-400 dark:bg-gray-700':
                                        $route.name === 'products',
                                }"
                            >
                                <svg
                                    class="h-5 w-5 mr-3"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                    />
                                </svg>
                                All Products
                            </router-link>
                        </li>

                        <li v-if="siteStore.settings.enable_about_page">
                            <router-link
                                to="/about"
                                @click="$emit('close')"
                                class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-md transition-colors dark:text-gray-400 dark:hover:text-primary-400 dark:hover:bg-gray-700"
                                :class="{
                                    'text-primary-600 bg-primary-50 dark:text-primary-400 dark:bg-gray-700':
                                        $route.name === 'about',
                                }"
                            >
                                <svg
                                    class="h-5 w-5 mr-3"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                About Us
                            </router-link>
                        </li>

                        <li v-if="siteStore.settings.enable_contact_page">
                            <router-link
                                to="/contact"
                                @click="$emit('close')"
                                class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-md transition-colors dark:text-gray-400 dark:hover:text-primary-400 dark:hover:bg-gray-700"
                                :class="{
                                    'text-primary-600 bg-primary-50 dark:text-primary-400 dark:bg-gray-700':
                                        $route.name === 'contact',
                                }"
                            >
                                <svg
                                    class="w-5 h-5 mr-3"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    />
                                </svg>
                                Contact
                            </router-link>

                            <!-- Dashboard Link (permission-based) -->
                            <router-link
                                v-if="authStore.hasPermission('view_dashboard')"
                                to="/dashboard"
                                @click="$emit('close')"
                                class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-md transition-colors dark:text-gray-400 dark:hover:text-primary-400 dark:hover:bg-gray-700"
                                :class="{
                                    'text-primary-600 bg-primary-50 dark:text-primary-400 dark:bg-gray-700':
                                        $route.path.startsWith('/dashboard'),
                                }"
                            >
                                <svg
                                    class="w-5 h-5 mr-3"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                    />
                                </svg>
                                Dashboard
                            </router-link>
                        </li>

                        <!-- Categories -->
                        <li>
                            <button
                                @click="toggleCategories"
                                class="flex items-center justify-between w-full px-3 py-2 text-gray-700 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-gray-700 rounded-md transition-colors"
                            >
                                <div class="flex items-center">
                                    <svg
                                        class="h-5 w-5 mr-3"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                        />
                                    </svg>
                                    Categories
                                </div>
                                <svg
                                    class="h-4 w-4 transition-transform"
                                    :class="{ 'rotate-180': showCategories }"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    />
                                </svg>
                            </button>

                            <!-- Categories Dropdown -->
                            <div
                                v-if="showCategories"
                                class="ml-8 mt-2 space-y-1"
                            >
                                <router-link
                                    v-for="category in categories"
                                    :key="category.id"
                                    :to="`/category/${category.id}`"
                                    @click="$emit('close')"
                                    class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-gray-700 rounded-md transition-colors"
                                >
                                    {{ category.name }}
                                </router-link>
                            </div>
                        </li>

                        <!-- User-specific links -->
                        <template v-if="isAuthenticated">
                            <li>
                                <router-link
                                    to="/wishlist"
                                    @click="$emit('close')"
                                    class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-md transition-colors dark:text-gray-400 dark:hover:text-primary-400 dark:hover:bg-gray-700"
                                    :class="{
                                        'text-primary-600 bg-primary-50 dark:text-primary-400 dark:bg-gray-700':
                                            $route.name === 'wishlist',
                                    }"
                                >
                                    <svg
                                        class="h-5 w-5 mr-3"
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
                                    Wishlist
                                </router-link>
                            </li>

                            <li>
                                <router-link
                                    to="/orders"
                                    @click="$emit('close')"
                                    class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-md transition-colors dark:text-gray-400 dark:hover:text-primary-400 dark:hover:bg-gray-700"
                                    :class="{
                                        'text-primary-600 bg-primary-50 dark:text-primary-400 dark:bg-gray-700':
                                            $route.name === 'orders',
                                    }"
                                >
                                    <svg
                                        class="h-5 w-5 mr-3"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                        />
                                    </svg>
                                    Orders
                                </router-link>
                            </li>

                            <li>
                                <router-link
                                    to="/profile"
                                    @click="$emit('close')"
                                    class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-md transition-colors dark:text-gray-400 dark:hover:text-primary-400 dark:hover:bg-gray-700"
                                    :class="{
                                        'text-primary-600 bg-primary-50 dark:text-primary-400 dark:bg-gray-700':
                                            $route.name === 'profile',
                                    }"
                                >
                                    <svg
                                        class="h-5 w-5 mr-3"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                        />
                                    </svg>
                                    Profile
                                </router-link>
                            </li>

                            <!-- Notifications Link for Mobile -->
                            <li>
                                <router-link
                                    to="/notifications"
                                    @click="$emit('close')"
                                    class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-md transition-colors dark:text-gray-400 dark:hover:text-primary-400 dark:hover:bg-gray-700"
                                    :class="{
                                        'text-primary-600 bg-primary-50 dark:text-primary-400 dark:bg-gray-700':
                                            $route.path === '/notifications',
                                    }"
                                >
                                    <svg
                                        class="h-5 w-5 mr-3"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                        />
                                    </svg>
                                    <span>Notifications</span>
                                    <span
                                        v-if="unreadCount > 0"
                                        class="ml-auto inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full"
                                    >
                                        {{
                                            unreadCount > 9 ? "9+" : unreadCount
                                        }}
                                    </span>
                                </router-link>
                            </li>
                        </template>
                    </ul>
                </nav>

                <!-- Bottom Section: Cart & Auth (Always visible on mobile) -->
                <div
                    class="border-t border-gray-200 dark:border-gray-700 p-4 space-y-3"
                >
                    <div class="flex items-center justify-between gap-4">
                        <!-- Left: Theme Toggle -->
                        <div class="flex-shrink-0">
                            <ThemeToggle />
                        </div>

                        <!-- Right: Cart Section -->
                        <div
                            class="flex-shrink-0"
                            v-if="
                                siteStore.settings &&
                                !siteStore.settings.orders_via_whatsapp_only
                            "
                        >
                            <router-link
                                to="/cart"
                                @click="$emit('close')"
                                class="flex items-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 dark:bg-primary-700 dark:hover:bg-primary-600 transition-colors gap-2"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="size-6"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"
                                    />
                                </svg>
                                <span>Cart</span>
                                <span
                                    v-if="cartStore.cartItemCount > 0"
                                    class="bg-white text-primary-600 text-xs rounded-full h-5 w-5 flex items-center justify-center"
                                >
                                    {{ cartStore.cartItemCount }}
                                </span>
                            </router-link>
                        </div>
                    </div>

                    <!-- Auth Section -->
                    <div v-if="!isAuthenticated" class="space-y-2">
                        <router-link
                            to="/auth/login"
                            @click="$emit('close')"
                            class="block w-full px-4 py-3 text-center text-gray-700 border bg bg-gray-400 border-gray-300 rounded-md hover:bg-gray-400 transition-colors dark:text-gray-50 dark:border-gray-600 dark:hover:bg-gray-700"
                        >
                            Login
                        </router-link>
                        <router-link
                            to="/auth/register"
                            @click="$emit('close')"
                            class="block w-full px-4 py-3 text-center bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors"
                        >
                            Register
                        </router-link>
                    </div>

                    <!-- Logout button for authenticated users -->
                    <button
                        v-if="isAuthenticated"
                        @click="logout"
                        class="block w-full px-4 py-3 text-center text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
                    >
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useAuthStore } from "../../stores/auth";
import { useSiteStore } from "../../stores/site";
import { useCartStore } from "../../stores/cart";
import { useProductStore } from "../../stores/products";
import { useNotificationStore } from "../../stores/notification";
import SearchBar from "../common/SearchBar.vue";
import ThemeToggle from "../common/ThemeToggle.vue";

defineEmits(["close"]);

const authStore = useAuthStore();
const cartStore = useCartStore();
const siteStore = useSiteStore();
const productStore = useProductStore();
const notificationStore = useNotificationStore();

const isAuthenticated = computed(() => authStore.isAuthenticated);
const unreadCount = computed(() => notificationStore.unreadCount || 0);

const showCategories = ref(false);
const categories = computed(() => productStore.categories);

const toggleCategories = () => {
    showCategories.value = !showCategories.value;
};

const logout = async () => {
    try {
        await authStore.logout();
        // Close sidebar after logout
        const event = new CustomEvent("close-mobile-menu");
        window.dispatchEvent(event);
    } catch (error) {
        console.error("Logout failed:", error);
    }
};

// Fetch notifications count when component mounts
onMounted(async () => {
    if (isAuthenticated.value) {
        try {
            await notificationStore.fetchUnreadCount();
            // Start polling for notifications
            notificationStore.startPolling(300000); // Poll every 5 minutes
        } catch (error) {
            console.error("Error fetching notifications:", error);
        }
    }
});

// Clean up polling when component unmounts
onUnmounted(() => {
    notificationStore.stopPolling();
});
</script>
