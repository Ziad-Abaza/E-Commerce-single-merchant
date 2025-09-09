<template>
  <div class="min-h-screen">
    <!-- Breadcrumb Navigation -->
    <Breadcrumb :breadcrumbs="breadcrumbs" />
    
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-r from-primary-600 to-primary-800 text-white">
      <div class="container py-20">
        <div class="max-w-4xl mx-auto text-center">
          <h1 class="text-4xl md:text-6xl font-bold mb-6">
            About Our Store
          </h1>
          <p class="text-xl md:text-2xl text-primary-100">
            Discover our story, values, and commitment to bringing you the best products
          </p>
        </div>
      </div>
    </section>

    <!-- Our Story Section -->
    <section class="py-16 bg-white">
      <div class="container">
        <div class="max-w-4xl mx-auto">
          <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
              <h2 class="text-3xl font-bold text-gray-900 mb-6">Our Story</h2>
              <p class="text-lg text-gray-600 mb-6">
                Founded with a passion for quality and customer satisfaction, our e-commerce store has been serving customers worldwide with carefully curated products that enhance everyday life.
              </p>
              <p class="text-lg text-gray-600 mb-6">
                What started as a small venture has grown into a trusted destination for shoppers seeking quality, value, and exceptional service. We believe that shopping should be an enjoyable experience, and we're committed to making that a reality for every customer.
              </p>
            </div>
            <div class="bg-gray-100 rounded-lg p-8">
              <div class="w-full h-64 bg-primary-100 rounded-lg flex items-center justify-center">
                <svg class="w-24 h-24 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Our Values Section -->
    <section class="py-16 bg-gray-50">
      <div class="container">
        <div class="max-w-4xl mx-auto">
          <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Values</h2>
            <p class="text-lg text-gray-600">
              The principles that guide everything we do
            </p>
          </div>

          <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center">
              <div class="w-16 h-16 mx-auto mb-4 bg-primary-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-3">Quality First</h3>
              <p class="text-gray-600">
                We carefully select every product to ensure it meets our high standards for quality and durability.
              </p>
            </div>

            <div class="text-center">
              <div class="w-16 h-16 mx-auto mb-4 bg-primary-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-3">Customer Focus</h3>
              <p class="text-gray-600">
                Our customers are at the heart of everything we do. We listen, adapt, and continuously improve.
              </p>
            </div>

            <div class="text-center">
              <div class="w-16 h-16 mx-auto mb-4 bg-primary-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-3">Innovation</h3>
              <p class="text-gray-600">
                We embrace new technologies and trends to provide you with the latest and greatest products.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Team Section -->
    <section class="py-16 bg-white">
      <div class="container">
        <div class="max-w-4xl mx-auto">
          <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Meet Our Team</h2>
            <p class="text-lg text-gray-600">
              The passionate people behind our success
            </p>
          </div>

          <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center">
              <div class="w-32 h-32 mx-auto mb-4 bg-gray-200 rounded-full flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Sarah Johnson</h3>
              <p class="text-primary-600 mb-3">Founder & CEO</p>
              <p class="text-gray-600 text-sm">
                Passionate about creating exceptional shopping experiences and building lasting customer relationships.
              </p>
            </div>

            <div class="text-center">
              <div class="w-32 h-32 mx-auto mb-4 bg-gray-200 rounded-full flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Michael Chen</h3>
              <p class="text-primary-600 mb-3">Head of Operations</p>
              <p class="text-gray-600 text-sm">
                Ensures smooth operations and timely delivery of products to customers worldwide.
              </p>
            </div>

            <div class="text-center">
              <div class="w-32 h-32 mx-auto mb-4 bg-gray-200 rounded-full flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Emily Rodriguez</h3>
              <p class="text-primary-600 mb-3">Customer Success Manager</p>
              <p class="text-gray-600 text-sm">
                Dedicated to providing outstanding customer support and ensuring customer satisfaction.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-primary-600">
      <div class="container">
        <div class="max-w-2xl mx-auto text-center">
          <h2 class="text-3xl font-bold text-white mb-4">
            Ready to Shop?
          </h2>
          <p class="text-xl text-primary-100 mb-8">
            Explore our collection and discover products you'll love
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <router-link
              to="/products"
              class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-primary-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors"
            >
              Browse Products
            </router-link>
            <router-link
              to="/contact"
              class="inline-flex items-center justify-center px-8 py-3 border border-white text-base font-medium rounded-md text-white hover:bg-white hover:text-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors"
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

// Breadcrumb navigation
const breadcrumbs = ref([
  { name: 'About Us' }
])

// Set page title and meta tags for SEO
onMounted(() => {
  document.title = 'About Us - E-Commerce Store | Our Story & Values'
  
  // Update meta description
  const metaDescription = document.querySelector('meta[name="description"]')
  if (metaDescription) {
    metaDescription.setAttribute('content', 'Learn about our e-commerce store, our story, values, and the passionate team behind our success. Discover our commitment to quality and customer satisfaction.')
  } else {
    const meta = document.createElement('meta')
    meta.name = 'description'
    meta.content = 'Learn about our e-commerce store, our story, values, and the passionate team behind our success. Discover our commitment to quality and customer satisfaction.'
    document.head.appendChild(meta)
  }

  // Add keywords meta tag
  const metaKeywords = document.querySelector('meta[name="keywords"]')
  if (metaKeywords) {
    metaKeywords.setAttribute('content', 'about us, our story, company values, team, e-commerce, online store, quality products, customer service')
  } else {
    const meta = document.createElement('meta')
    meta.name = 'keywords'
    meta.content = 'about us, our story, company values, team, e-commerce, online store, quality products, customer service'
    document.head.appendChild(meta)
  }
})
</script>
