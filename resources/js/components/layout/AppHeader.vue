<template>
  <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <div class="flex items-center">
          <router-link to="/" class="flex items-center space-x-2">
            <div class="h-8 w-8 bg-primary-600 rounded-lg flex items-center justify-center">
              <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
            </div>
            <span class="text-xl font-bold text-gray-900">{{ siteStore.settings.site_name }}</span>
          </router-link>
        </div>

        <!-- Desktop Navigation (hidden on mobile) -->
        <nav class="hidden md:flex items-center space-x-8">
          <router-link
            to="/"
            class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
            :class="{ 'text-primary-600 bg-primary-50': $route.name === 'home' }"
          >
            Home
          </router-link>

          <CategoriesDropdown />

          <router-link
            to="/products"
            class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
            :class="{ 'text-primary-600 bg-primary-50': $route.name === 'products' }"
          >
            All Products
          </router-link>

          <router-link
            to="/about"
            class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
            :class="{ 'text-primary-600 bg-primary-50': $route.name === 'about' }"
          >
            About Us
          </router-link>

          <router-link
            to="/contact"
            class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
            :class="{ 'text-primary-600 bg-primary-50': $route.name === 'contact' }"
          >
            Contact
          </router-link>
        </nav>

        <!-- Search Bar (hidden on mobile) -->
        <div class="hidden md:block flex-1 max-w-lg mx-8">
          <SearchBar />
        </div>

        <!-- Right Side Actions -->
        <div class="flex items-center space-x-4">
          <!-- Wishlist (visible on desktop only) -->
          <router-link
            v-if="isAuthenticated"
            to="/wishlist"
            class="hidden md:block p-2 text-gray-400 hover:text-primary-600 transition-colors relative"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <span v-if="wishlistCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
              {{ wishlistCount }}
            </span>
          </router-link>

          <!-- Cart (hidden on mobile - moved to sidebar) -->
          <router-link
            to="/cart"
            class="hidden md:block p-2 text-gray-400 hover:text-primary-600 transition-colors relative"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
            </svg>
            <span v-if="cartStore.cartItemCount > 0" class="absolute -top-1 -right-1 bg-primary-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
              {{ cartStore.cartItemCount }}
            </span>
          </router-link>

          <!-- User Menu (hidden on mobile - moved to sidebar) -->
          <UserMenu v-if="isAuthenticated" class="hidden md:block" />

          <!-- Login/Register Links (hidden on mobile - moved to sidebar) -->
          <div v-else class="hidden md:flex items-center space-x-2">
            <router-link
              to="/auth/login"
              class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
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
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useCartStore } from '../../stores/cart'
import { useSiteStore } from '../../stores/site'
import CategoriesDropdown from '../common/CategoriesDropdown.vue'
import SearchBar from '../common/SearchBar.vue'
import UserMenu from '../common/UserMenu.vue'

const siteStore = useSiteStore()
const authStore = useAuthStore()
const cartStore = useCartStore()

// Mock wishlist count - replace with actual wishlist store when available
const wishlistCount = computed(() => 0)
const isAuthenticated = computed(() => authStore.isAuthenticated)


const openMobileMenu = () => {
  // This will be handled by the parent AppLayout component
  const event = new CustomEvent('open-mobile-menu')
  window.dispatchEvent(event)
}
</script>
