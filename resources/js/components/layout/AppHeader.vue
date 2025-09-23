<template>
    <header
        class="bg-white dark:bg-gray-900 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40 transition-colors duration-200"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <router-link to="/" class="flex items-center space-x-2">
                        <div
                            class="h-16 w-16 rounded-lg flex items-center justify-center"
                        >
                            <svg
                                class="h-full w-full text-primary-600"
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
                            <img
                                v-if="siteStore.settings.logo_url"
                                :src="siteStore.settings.logo_url"
                                alt="Logo"
                                class="h-full w-full object-contain rounded-lg"
                            />
                        </div>
                        <span
                            class="text-xl font-bold text-gray-900 dark:text-white"
                            >{{ siteStore.settings.site_name }}</span
                        >
                    </router-link>
                </div>

                <!-- Desktop Navigation (hidden on mobile) -->
                <nav class="hidden md:flex items-center space-x-8 ml-4">
                    <router-link
                        to="/"
                        class="text-gray-700 dark:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                        :class="{
                            'text-primary-50 bg-primary-600 hover:text-white dark:hover:text-white':
                                $route.name === 'home',
                            'hover:text-primary-600 dark:hover:text-primary-400':
                                $route.name !== 'home',
                        }"
                    >
                        Home
                    </router-link>

                    <CategoriesDropdown />

                    <router-link
                        to="/products"
                        class="text-gray-700 dark:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                        :class="{
                            'text-primary-50 bg-primary-600 hover:text-white dark:hover:text-white':
                                $route.name === 'products',
                            'hover:text-primary-600 dark:hover:text-primary-400':
                                $route.name !== 'products',
                        }"
                    >
                        All Products
                    </router-link>
                </nav>

                <!-- Search Bar (hidden on mobile) -->
                <div class="hidden md:block flex-1 max-w-lg mx-8">
                    <SearchBar />
                </div>

                <!-- Theme Toggle (hidden on mobile) -->
                <div class="hidden md:block">
                    <ThemeToggle />
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Wishlist (visible on desktop only) -->
                    <router-link
                        v-if="isAuthenticated"
                        to="/wishlist"
                        class="p-2 text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors relative"
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
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                            />
                        </svg>
                        <span
                            v-if="wishlistCount > 0"
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
                        >
                            {{ wishlistCount }}
                        </span>
                    </router-link>

                    <!-- Cart (hidden on mobile - moved to sidebar) -->
                    <router-link
                        to="/cart"
                        class="p-2 text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors relative"
                        v-if="siteStore.settings && !siteStore.settings.orders_via_whatsapp_only"
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

                        <span
                            v-if="cartStore.cartItemCount > 0"
                            class="absolute -top-1 -right-1 bg-primary-600 dark:bg-primary-700 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
                        >
                            {{ cartStore.cartItemCount }}
                        </span>
                    </router-link>

                    <!-- <NotificationDropdown v-if="isAuthenticated" class="hidden md:block" /> -->

                    <!-- User Menu (hidden on mobile - moved to sidebar) -->
                    <UserMenu v-if="isAuthenticated" class="hidden md:block" />

                    <!-- Login/Register Links (hidden on mobile - moved to sidebar) -->
                    <div v-else class="hidden md:flex items-center space-x-2">
                        <router-link
                            to="/auth/login"
                            class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                        >
                            Login
                        </router-link>
                        <router-link
                            to="/auth/register"
                            class="bg-primary-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-primary-700 transition-colors"
                        >
                            Register
                        </router-link>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button
                        @click="openMobileMenu"
                        class="md:hidden p-2 text-gray-400 hover:text-primary-600 transition-colors"
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
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { computed } from "vue";
import { useAuthStore } from "../../stores/auth";
import { useCartStore } from "../../stores/cart";
import { useSiteStore } from "../../stores/site";
import CategoriesDropdown from "../common/CategoriesDropdown.vue";
import SearchBar from "../common/SearchBar.vue";
import UserMenu from "../common/UserMenu.vue";
import ThemeToggle from "../common/ThemeToggle.vue";
import NotificationDropdown from "../common/NotificationDropdown.vue";

const siteStore = useSiteStore();
const authStore = useAuthStore();
const cartStore = useCartStore();

// Mock wishlist count - replace with actual wishlist store when available
const wishlistCount = computed(() => 0);
const isAuthenticated = computed(() => authStore.isAuthenticated);

const openMobileMenu = () => {
    // This will be handled by the parent AppLayout component
    const event = new CustomEvent("open-mobile-menu");
    window.dispatchEvent(event);
};
</script>
