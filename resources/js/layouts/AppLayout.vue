<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <AppHeader />
    
    <!-- Main Content -->
    <main class="min-h-screen">
      <router-view />
    </main>
    
    <!-- Footer -->
    <AppFooter />
    
    <!-- Mobile Menu Overlay -->
    <MobileMenuOverlay v-if="isMobileMenuOpen" @close="closeMobileMenu" />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useCartStore } from '../stores/cart'
import { useAuthStore } from '../stores/auth'
import AppHeader from '../components/layout/AppHeader.vue'
import AppFooter from '../components/layout/AppFooter.vue'
import MobileMenuOverlay from '../components/layout/MobileMenuOverlay.vue'

const cartStore = useCartStore()
const authStore = useAuthStore()

const isMobileMenuOpen = ref(false)

const openMobileMenu = () => {
  isMobileMenuOpen.value = true
  document.body.style.overflow = 'hidden'
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
  document.body.style.overflow = 'auto'
}

// Load cart from localStorage if user is not authenticated
onMounted(() => {
  if (!authStore.isAuthenticated) {
    cartStore.loadFromLocalStorage()
  }
})

// Handle window resize
const handleResize = () => {
  if (window.innerWidth >= 1024) {
    closeMobileMenu()
  }
}

// Listen for mobile menu open event
const handleMobileMenuEvent = () => {
  openMobileMenu()
}

onMounted(() => {
  window.addEventListener('resize', handleResize)
  window.addEventListener('open-mobile-menu', handleMobileMenuEvent)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
  window.removeEventListener('open-mobile-menu', handleMobileMenuEvent)
})

// Expose methods for child components
defineExpose({
  openMobileMenu,
  closeMobileMenu
})
</script>
