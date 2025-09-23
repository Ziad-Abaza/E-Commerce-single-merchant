<template>
    <div class="min-h-screen bg-white dark:bg-gray-900">
        <!-- Hero Section -->
        <section
            class="relative bg-gradient-to-r from-primary-600 to-primary-800 text-white"
        >
            <div class="container py-20">
                <div class="max-w-3xl">
                    <h1 class="text-4xl md:text-6xl font-bold mb-6">
                        Discover Amazing Products
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-primary-100 dark:text-primary-200">
                        Shop the latest trends and find everything you need in
                        one place
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <router-link
                            to="/products"
                            class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-primary-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors dark:text-primary-700 dark:bg-gray-100 dark:hover:bg-gray-200"
                        >
                            Shop Now
                        </router-link>
                        <router-link
                            to="/about"
                            class="inline-flex items-center justify-center px-8 py-3 border border-white text-base font-medium rounded-md text-white hover:bg-white hover:text-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors dark:border-gray-300 dark:hover:bg-gray-100 dark:hover:text-primary-700"
                        >
                            Learn More
                        </router-link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Categories -->
        <section class="py-16 bg-white dark:bg-gray-900">
            <div class="container">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                        Shop by Category
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300">
                        Find what you're looking for in our organized categories
                    </p>
                </div>

                <div
                    v-if="homeStore.loading"
                    class="grid grid-cols-2 md:grid-cols-4 gap-6"
                >
                    <div v-for="n in 4" :key="n" class="animate-pulse">
                        <div class="bg-gray-200 dark:bg-gray-700 h-32 rounded-lg mb-4"></div>
                        <div class="bg-gray-200 dark:bg-gray-700 h-4 rounded w-3/4"></div>
                    </div>
                </div>

                <div
                    v-else-if="homeStore.categories.length > 0"
                    class="grid grid-cols-2 md:grid-cols-4 gap-6"
                >
                    <router-link
                        v-for="category in (homeStore.categories || []).slice(
                            0,
                            8,
                        )"
                        :key="category.id"
                        :to="{ name: 'category', params: { id: category.id } }"
                        class="group text-center hover:transform hover:scale-105 transition-transform duration-200"
                        @click.prevent="filterByCategory(category.id)"
                    >
                        <div
                            class="bg-gray-100 dark:bg-gray-800 rounded-lg p-6 mb-4 group-hover:bg-primary-50 dark:group-hover:bg-gray-700 transition-colors"
                        >
                            <div
                                class="w-16 h-16 mx-auto mb-4 bg-primary-100 dark:bg-gray-700 rounded-full flex items-center justify-center group-hover:bg-primary-200 dark:group-hover:bg-gray-600 transition-colors"
                            >
                                <svg
                                    class="w-8 h-8 text-primary-600 dark:text-primary-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                    />
                                </svg>
                            </div>
                            <h3
                                class="font-medium text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors"
                            >
                                {{ category.name }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ category.products_count }} products
                            </p>
                        </div>
                    </router-link>
                </div>

                <div v-else class="text-center py-12">
                    <svg
                        class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                        />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                        No categories available
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Categories will appear here once they are added.
                    </p>
                </div>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="py-16 bg-gray-50 dark:bg-gray-800">
            <div class="container">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                        Featured Products
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300">
                        Handpicked products just for you
                    </p>
                </div>

                <div v-if="homeStore.loading" class="product-grid">
                    <div v-for="n in 8" :key="n" class="animate-pulse">
                        <div
                            class="bg-white dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600"
                        >
                            <div class="h-48 bg-gray-200 dark:bg-gray-600 rounded-t-lg"></div>
                            <div class="p-4">
                                <div
                                    class="bg-gray-200 dark:bg-gray-600 h-4 rounded w-3/4 mb-2"
                                ></div>
                                <div
                                    class="bg-gray-200 dark:bg-gray-600 h-4 rounded w-1/2"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-else-if="homeStore.featuredProducts.length > 0"
                    class="product-grid"
                >
                    <ProductCard
                        v-for="product in homeStore.featuredProducts"
                        :key="product.id"
                        :product="product"
                    />
                </div>

                <div v-else class="text-center py-12">
                    <svg
                        class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-4h-2m-3-3v2m-4-2v2"
                        />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                        No featured products available
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Check back later for featured items.
                    </p>
                </div>

                <div class="text-center mt-12">
                    <router-link
                        to="/products"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors dark:bg-primary-700 dark:hover:bg-primary-800"
                    >
                        View All Products
                        <svg
                            class="ml-2 -mr-1 w-5 h-5"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </router-link>
                </div>
            </div>
        </section>

        <!-- Latest Products -->
        <section class="py-16 bg-white dark:bg-gray-900">
            <div class="container">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                        Latest Products
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300">
                        Fresh arrivals just added to our collection
                    </p>
                </div>

                <div v-if="homeStore.loading" class="product-grid">
                    <div v-for="n in 8" :key="n" class="animate-pulse">
                        <div
                            class="bg-white dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600"
                        >
                            <div class="h-48 bg-gray-200 dark:bg-gray-600 rounded-t-lg"></div>
                            <div class="p-4">
                                <div
                                    class="bg-gray-200 dark:bg-gray-600 h-4 rounded w-3/4 mb-2"
                                ></div>
                                <div
                                    class="bg-gray-200 dark:bg-gray-600 h-4 rounded w-1/2"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-else-if="homeStore.latestProducts.length > 0"
                    class="product-grid"
                >
                    <ProductCard
                        v-for="product in homeStore.latestProducts"
                        :key="product.id"
                        :product="product"
                    />
                </div>

                <div v-else class="text-center py-12">
                    <svg
                        class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-4h-2m-3-3v2m-4-2v2"
                        />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                        No products available
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Check back later for new arrivals.
                    </p>
                </div>
            </div>
        </section>

        <section class="py-16 bg-blue-600 text-center dark:bg-blue-800">
            <div class="container max-w-2xl mx-auto">
                <h2 class="text-3xl font-bold text-white mb-4">Who We Are</h2>
                <p class="text-lg text-gray-300 dark:text-gray-200">
                    {{ siteStore.settings.who_we_are }}
                </p>
            </div>
        </section>

        <!-- Login / Register Call to Action -->
        <section
            v-if="!authStore.isAuthenticated"
            class="py-16 bg-gray-50 dark:bg-gray-800 text-center"
        >
            <div class="container max-w-xl mx-auto">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                    Join Us Today
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-6">
                    Create an account or log in to enjoy a personalized shopping
                    experience.
                </p>
                <div class="flex justify-center gap-4">
                    <router-link
                        to="/auth/login"
                        class="px-6 py-3 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition dark:bg-primary-700 dark:hover:bg-primary-800"
                    >
                        Login
                    </router-link>
                    <router-link
                        to="/auth/register"
                        class="px-6 py-3 border border-primary-600 text-primary-600 rounded-md hover:bg-primary-50 transition dark:border-primary-500 dark:text-primary-400 dark:hover:bg-gray-700"
                    >
                        Register
                    </router-link>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="py-16 bg-white dark:bg-gray-900 text-center">
            <div class="container max-w-2xl mx-auto">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                    Get in Touch
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-6">
                    Have any questions or feedback? We'd love to hear from you.
                </p>
                <router-link
                    to="/contact"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors dark:bg-primary-700 dark:hover:bg-primary-800"
                >
                    Contact Us
                    <svg
                        class="ml-2 -mr-1 w-5 h-5"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </router-link>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useHomeStore } from "../stores/home";
import { useAuthStore } from "../stores/auth";
import { useToast } from "vue-toastification";
import ProductCard from "../components/common/ProductCard.vue";
import axios from "../bootstrap";
import { useSiteStore } from "../stores/site";

const siteStore = useSiteStore();
const homeStore = useHomeStore();
const toast = useToast();
const authStore = useAuthStore();

const filterByCategory = async (categoryId) => {
    await homeStore.filterByCategory(categoryId);
    window.scrollTo({ top: 0, behavior: "smooth" });
};

onMounted(() => {
    homeStore.loadHomeData();
});
</script>
