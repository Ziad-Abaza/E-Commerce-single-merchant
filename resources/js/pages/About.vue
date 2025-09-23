<template>
  <div class="min-h-screen bg-white dark:bg-gray-900">
    <!-- Breadcrumb Navigation -->
    <Breadcrumb :breadcrumbs="breadcrumbs" />

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-r from-primary-600 to-primary-800 text-white">
      <div class="container py-20">
        <div class="max-w-4xl mx-auto text-center">
          <h1 class="text-4xl md:text-6xl font-bold mb-6">
            About Our Store
          </h1>
          <p class="text-xl md:text-2xl text-primary-100 dark:text-primary-200">
            {{ siteStore.settings.who_we_are }}
          </p>
        </div>
      </div>
    </section>

    <!-- Our Story Section -->
    <section class="py-16 bg-white dark:bg-gray-900">
      <div class="container">
        <div class="max-w-4xl mx-auto">
          <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
              <h2 class="text-3xl font-bold text-gray-900 mb-6 dark:text-white">Our Story</h2>
              <p class="text-lg text-gray-600 mb-6 dark:text-gray-300">
                {{ siteStore.settings.our_story }}
              </p>
            </div>
            <div class="bg-gray-100 rounded-lg p-8 dark:bg-gray-800">
              <div class="w-full h-64 bg-primary-100 rounded-lg flex items-center justify-center dark:bg-gray-700">
                <img :src="siteStore.settings.about_image" alt="Our Story" class="w-full h-full object-cover rounded-lg" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-16 bg-gray-50 dark:bg-gray-800">
      <div class="container">
        <div class="max-w-4xl mx-auto">
          <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4 dark:text-white">Why Choose Us</h2>
            <p class="text-lg text-gray-600 dark:text-gray-300">
              {{ siteStore.settings.why_choose_us }}
            </p>
          </div>

          <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center">
              <div class="w-16 h-16 mx-auto mb-4 bg-primary-100 rounded-full flex items-center justify-center dark:bg-gray-700">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-3 dark:text-white">Trusted Products</h3>
              <p class="text-gray-600 dark:text-gray-300">
                Every item is personally selected to meet our standards of quality and reliability.
              </p>
            </div>

            <div class="text-center">
              <div class="w-16 h-16 mx-auto mb-4 bg-primary-100 rounded-full flex items-center justify-center dark:bg-gray-700">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-3 dark:text-white">Personal Commitment</h3>
              <p class="text-gray-600 dark:text-gray-300">
                As a single-merchant store, we take responsibility for every product and every customer.
              </p>
            </div>

            <div class="text-center">
              <div class="w-16 h-16 mx-auto mb-4 bg-primary-100 rounded-full flex items-center justify-center dark:bg-gray-700">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-3 dark:text-white">Reliable Service</h3>
              <p class="text-gray-600 dark:text-gray-300">
                From order to delivery, we aim to make the entire process smooth and hassle-free.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Commitment Section -->
    <section class="py-16 bg-white dark:bg-gray-900">
      <div class="container">
        <div class="max-w-3xl mx-auto text-center">
          <h2 class="text-3xl font-bold text-gray-900 mb-4 dark:text-white">Our Commitment</h2>
          <p class="text-lg text-gray-600 mb-6 dark:text-gray-300">
            {{ siteStore.settings.our_commitment }}
          </p>
          <p class="text-lg text-gray-600 dark:text-gray-300">
            Thank you for choosing our store — your support makes it possible to keep providing carefully selected products and personalized service.
          </p>
        </div>
      </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-primary-600 dark:bg-primary-800">
      <div class="container">
        <div class="max-w-2xl mx-auto text-center">
          <h2 class="text-3xl font-bold text-white mb-4">
            Ready to Shop?
          </h2>
          <p class="text-xl text-primary-100 mb-8 dark:text-primary-200">
            Explore our collection and discover products you'll love
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <router-link
              to="/products"
              class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-primary-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors dark:text-primary-700 dark:bg-gray-100 dark:hover:bg-gray-200"
            >
              Browse Products
            </router-link>
            <router-link
              to="/contact"
              class="inline-flex items-center justify-center px-8 py-3 border border-white text-base font-medium rounded-md text-white hover:bg-white hover:text-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors dark:border-gray-300 dark:hover:bg-gray-100 dark:hover:text-primary-700"
            >
              Contact Us
            </router-link>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import Breadcrumb from '../components/common/Breadcrumb.vue'
import { useSiteStore } from "../stores/site";

const siteStore = useSiteStore();
// Breadcrumb navigation
const breadcrumbs = ref([
  { name: 'About Us' }
])


onMounted(() => {
  // SEO
  document.title = 'About Us - E-Commerce Store | Our Story & Values'
  const metaDescription = document.querySelector('meta[name="description"]')
  if (metaDescription) {
    metaDescription.setAttribute('content', 'Learn about our store, our story, values, and the team behind our success.')
  } else {
    const meta = document.createElement('meta')
    meta.name = 'description'
    meta.content = 'Learn about our store, our story, values, and the team behind our success.'
    document.head.appendChild(meta)
  }
})
</script>
