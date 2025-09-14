<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <router-view />
    <LoadingOverlay v-if="isLoading" />
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useAuthStore } from './stores/auth'
import { useProductStore } from './stores/products'
import { useSiteStore } from './stores/site'
import { useCartStore } from './stores/cart'
import LoadingOverlay from './components/LoadingOverlay.vue'

const authStore = useAuthStore()
const productStore = useProductStore()
const cartStore = useCartStore()
const siteStore = useSiteStore()

const isLoading = computed(() => {
  return authStore.loading || productStore.loading || cartStore.loading
})

// Initialize app data
const initializeApp = async () => {
  try {
    await siteStore.fetchSettings()
    await authStore.checkAuth()

    // Load initial data
    await Promise.all([
      productStore.loadCategories(),
      productStore.loadFeaturedProducts(),
      productStore.loadLatestProducts()
    ])

    // Load cart if user is authenticated
    if (authStore.isAuthenticated) {
      await cartStore.loadCart()
    }
  } catch (error) {
    console.error('Failed to initialize app:', error)
  }
}

onMounted(() => {
  initializeApp()
})
</script>

<style>
/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* NProgress customization */
#nprogress .bar {
  background: #3b82f6 !important;
  height: 3px !important;
}

#nprogress .peg {
  box-shadow: 0 0 10px #3b82f6, 0 0 5px #3b82f6 !important;
}

#nprogress .spinner-icon {
  border-top-color: #3b82f6 !important;
  border-left-color: #3b82f6 !important;
}
</style>
