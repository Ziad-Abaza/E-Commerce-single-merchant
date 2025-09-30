<template>
    <footer
        class="bg-gray-900 text-white dark:bg-gray-800 dark:text-gray-300 border-t border-gray-700 transition-colors duration-200"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div
                            class="h-20 w-20 rounded-lg flex items-center justify-center"
                        >
                            <div
                                v-if="siteStore.settings.logo_url"
                                class="h-16 w-16 rounded-lg flex items-center justify-center"
                            >
                                <img
                                    :src="siteStore.settings.logo_url"
                                    alt="Logo"
                                    class="h-full w-full"
                                />
                            </div>
                            <div
                                v-else
                                class="h-16 w-16 rounded-lg flex items-center justify-center bg-blue-700"
                            >
                                <svg
                                    class="h-16 w-16 text-white"
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
                        </div>
                        <span class="text-xl font-bold">{{
                            siteStore.settings.site_name
                        }}</span>
                    </div>
                    <p class="text-gray-300 dark:text-gray-400 mb-4 max-w-md">
                        {{ siteStore.settings.site_description }}
                    </p>
                    <div class="flex space-x-4">
                        <a
                            v-if="siteStore.settings.facebook_url_visible"
                            :href="siteStore.settings.facebook_url"
                            class="w-10 h-10 bg-blue-600 dark:bg-blue-700 rounded-full flex items-center justify-center text-white hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z"
                                />
                            </svg>
                        </a>
                        <a
                            v-if="siteStore.settings.twitter_url_visible"
                            :href="siteStore.settings.twitter_url"
                            class="w-10 h-10 bg-blue-800 dark:bg-blue-900 rounded-full flex items-center justify-center text-white hover:bg-blue-900 dark:hover:bg-blue-800 transition-colors"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"
                                />
                            </svg>
                        </a>
                        <a
                            v-if="siteStore.settings.instagram_url_visible"
                            :href="siteStore.settings.instagram_url"
                            class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 dark:from-purple-600 dark:to-pink-600 rounded-full flex items-center justify-center text-white hover:from-purple-600 hover:to-pink-600 dark:hover:from-purple-500 dark:hover:to-pink-500 transition-colors"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"
                                />
                            </svg>
                        </a>
                        <a
                            v-if="siteStore.settings.tiktok_url_visible"
                            :href="siteStore.settings.tiktok_url"
                            target="_blank"
                            class="w-10 h-10 bg-black dark:bg-gray-900 rounded-full flex items-center justify-center text-white hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="16"
                                height="16"
                                fill="currentColor"
                                class="bi bi-tiktok"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3z"
                                />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li>
                            <router-link
                                to="/"
                                class="text-gray-300 hover:text-white dark:hover:text-gray-100 transition-colors"
                            >
                                Home
                            </router-link>
                        </li>
                        <li>
                            <router-link
                                to="/products"
                                class="text-gray-300 hover:text-white transition-colors"
                            >
                                Products
                            </router-link>
                        </li>
                        <li>
                            <router-link
                                to="/cart"
                                class="text-gray-300 hover:text-white transition-colors"
                            >
                                Cart
                            </router-link>
                        </li>
                        <li>
                            <router-link
                                to="/about"
                                class="text-gray-300 hover:text-white transition-colors"
                            >
                                About Us
                            </router-link>
                        </li>
                        <li>
                            <router-link
                                to="/contact"
                                class="text-gray-300 hover:text-white transition-colors"
                            >
                                Contact
                            </router-link>
                        </li>
                    </ul>
                </div>

                <!-- Customer Service -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Customer Service</h3>
                    <ul class="space-y-2">
                        <li>
                            <router-link
                                to="/faq"
                                class="text-gray-300 hover:text-white transition-colors"
                            >
                                FAQ
                            </router-link>
                        </li>
                        <li>
                            <router-link
                                to="/return-policy"
                                class="text-gray-300 hover:text-white transition-colors"
                            >
                                returns
                            </router-link>
                        </li>
                        <li>
                            <router-link
                                to="/shipping-policy"
                                class="text-gray-300 hover:text-white transition-colors"
                            >
                                Shipping Policy
                            </router-link>
                        </li>
                        <li>
                            <router-link
                                to="/warranty-policy"
                                class="text-gray-300 hover:text-white transition-colors"
                            >
                                Warranty Policy
                            </router-link>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div
                class="border-t border-gray-800 dark:border-gray-700 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center"
            >
                <p class="text-gray-400 text-sm">
                    &copy; {{ currentYear }} {{ siteStore.settings.site_name }}.
                    All rights reserved.
                </p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <!-- router link -->
                    <router-link
                        to="/privacy-policy"
                        class="text-gray-400 hover:text-white dark:hover:text-gray-200 text-sm transition-colors"
                    >
                        Privacy Policy
                    </router-link>
                    <router-link
                        to="/terms-and-conditions"
                        class="text-gray-400 hover:text-white dark:hover:text-gray-200 text-sm transition-colors"
                    >
                        Terms & Conditions
                    </router-link>
                    <router-link
                        to="/cookies-policy"
                        class="text-gray-400 hover:text-white dark:hover:text-gray-200 text-sm transition-colors"
                    >
                        Cookie Policy
                    </router-link>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { computed } from "vue";
import { useSiteStore } from "../../stores/site";

const siteStore = useSiteStore();

const currentYear = computed(() => new Date().getFullYear());
</script>
