<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <!-- Logo -->
      <div class="flex justify-center">
        <div class="flex items-center">
          <div class="h-12 w-12 bg-primary-600 rounded-lg flex items-center justify-center">
            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
          </div>
          <span class="ml-3 text-2xl font-bold text-gray-900">E-Commerce</span>
        </div>
      </div>
      
      <!-- Title -->
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        {{ title }}
      </h2>
      
      <!-- Subtitle -->
      <p class="mt-2 text-center text-sm text-gray-600">
        {{ subtitle }}
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <router-view />
      </div>
    </div>
    
    <!-- Footer Links -->
    <div class="mt-8 text-center">
      <div class="flex justify-center space-x-6">
        <router-link to="/" class="text-sm text-gray-500 hover:text-gray-900">
          Home
        </router-link>
        <router-link to="/products" class="text-sm text-gray-500 hover:text-gray-900">
          Products
        </router-link>
        <a href="#" class="text-sm text-gray-500 hover:text-gray-900">
          Support
        </a>
        <a href="#" class="text-sm text-gray-500 hover:text-gray-900">
          Privacy
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const title = computed(() => {
  switch (route.name) {
    case 'login':
      return 'Sign in to your account'
    case 'register':
      return 'Create your account'
    default:
      return 'Welcome'
  }
})

const subtitle = computed(() => {
  switch (route.name) {
    case 'login':
      return 'Enter your credentials to access your account'
    case 'register':
      return 'Join us and start shopping today'
    default:
      return ''
  }
})
</script>
