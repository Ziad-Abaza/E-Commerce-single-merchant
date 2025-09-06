<template>
  <div class="min-h-screen">
    <!-- Hero Section -->
    <section
      class="relative bg-gradient-to-r from-primary-600 to-primary-800 text-white"
    >
      <div class="container py-20">
        <div class="max-w-3xl">
          <h1 class="text-4xl md:text-6xl font-bold mb-6">
            Discover Amazing Products
          </h1>
          <p class="text-xl md:text-2xl mb-8 text-primary-100">
            Shop the latest trends and find everything you need in
            one place
          </p>
          <div class="flex flex-col sm:flex-row gap-4">
            <router-link
              to="/products"
              class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-primary-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors"
            >
              Shop Now
            </router-link>
            <router-link
              to="/about"
              class="inline-flex items-center justify-center px-8 py-3 border border-white text-base font-medium rounded-md text-white hover:bg-white hover:text-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors"
            >
              Learn More
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Categories -->
    <section class="py-16 bg-white">
      <div class="container">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Shop by Category
          </h2>
          <p class="text-lg text-gray-600">
            Find what you're looking for in our organized categories
          </p>
        </div>

        <div
          v-if="homeStore.loading"
          class="grid grid-cols-2 md:grid-cols-4 gap-6"
        >
          <div v-for="n in 4" :key="n" class="animate-pulse">
            <div class="bg-gray-200 h-32 rounded-lg mb-4"></div>
            <div class="bg-gray-200 h-4 rounded w-3/4"></div>
          </div>
        </div>

        <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <router-link
            v-for="category in (homeStore.categories || []).slice(0, 8)"
            :key="category.id"
            :to="{ name: 'category', params: { id: category.id } }"
            class="group text-center hover:transform hover:scale-105 transition-transform duration-200"
            @click.prevent="filterByCategory(category.id)"
          >
            <div
              class="bg-gray-100 rounded-lg p-6 mb-4 group-hover:bg-primary-50 transition-colors"
            >
              <div
                class="w-16 h-16 mx-auto mb-4 bg-primary-100 rounded-full flex items-center justify-center group-hover:bg-primary-200 transition-colors"
              >
                <svg
                  class="w-8 h-8 text-primary-600"
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
                class="font-medium text-gray-900 group-hover:text-primary-600 transition-colors"
              >
                {{ category.name }}
              </h3>
              <p class="text-sm text-gray-500 mt-1">
                {{ category.products_count }} products
              </p>
            </div>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-gray-50">
      <div class="container">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Featured Products
          </h2>
          <p class="text-lg text-gray-600">
            Handpicked products just for you
          </p>
        </div>

        <div v-if="homeStore.loading" class="product-grid">
          <div v-for="n in 8" :key="n" class="animate-pulse">
            <div
              class="bg-white rounded-lg shadow-sm border border-gray-200"
            >
              <div class="h-48 bg-gray-200 rounded-t-lg"></div>
              <div class="p-4">
                <div
                  class="bg-gray-200 h-4 rounded w-3/4 mb-2"
                ></div>
                <div
                  class="bg-gray-200 h-4 rounded w-1/2"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="product-grid">
          <ProductCard
            v-for="product in homeStore.featuredProducts"
            :key="product.id"
            :product="product"
          />
        </div>

        <div class="text-center mt-12">
          <router-link
            to="/products"
            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
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
    <section class="py-16 bg-white">
      <div class="container">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Latest Products
          </h2>
          <p class="text-lg text-gray-600">
            Fresh arrivals just added to our collection
          </p>
        </div>

        <div v-if="homeStore.loading" class="product-grid">
          <div v-for="n in 8" :key="n" class="animate-pulse">
            <div
              class="bg-white rounded-lg shadow-sm border border-gray-200"
            >
              <div class="h-48 bg-gray-200 rounded-t-lg"></div>
              <div class="p-4">
                <div
                  class="bg-gray-200 h-4 rounded w-3/4 mb-2"
                ></div>
                <div
                  class="bg-gray-200 h-4 rounded w-1/2"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="product-grid">
          <ProductCard
            v-for="product in homeStore.latestProducts"
            :key="product.id"
            :product="product"
          />
        </div>
      </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-16 bg-primary-600">
      <div class="container">
        <div class="max-w-2xl mx-auto text-center">
          <h2 class="text-3xl font-bold text-white mb-4">
            Stay Updated
          </h2>
          <p class="text-xl text-primary-100 mb-8">
            Subscribe to our newsletter and never miss out on new
            products and exclusive offers
          </p>
          <form
            @submit.prevent="subscribeNewsletter"
            class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto"
          >
            <input
              v-model="email"
              type="email"
              placeholder="Enter your email"
              class="flex-1 px-4 py-3 rounded-md border-0 focus:ring-2 focus:ring-primary-300 focus:outline-none"
              required
            />
            <button
              type="submit"
              :disabled="isSubscribing"
              class="px-6 py-3 bg-white text-primary-600 font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-300 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <span v-if="isSubscribing">Subscribing...</span>
              <span v-else>Subscribe</span>
            </button>
          </form>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useHomeStore } from "../stores/home";
import { useToast } from "vue-toastification";
import ProductCard from "../components/common/ProductCard.vue";

const homeStore = useHomeStore();
const toast = useToast();

const email = ref("");
const isSubscribing = ref(false);

const subscribeNewsletter = async () => {
  if (isSubscribing.value) return;

  isSubscribing.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    toast.success("Successfully subscribed to newsletter!");
    email.value = "";
  } catch (error) {
    toast.error("Failed to subscribe. Please try again.");
  } finally {
    isSubscribing.value = false;
  }
};

const filterByCategory = async (categoryId) => {
  await homeStore.filterByCategory(categoryId);
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

onMounted(() => {
  homeStore.loadHomeData();
});
</script>
