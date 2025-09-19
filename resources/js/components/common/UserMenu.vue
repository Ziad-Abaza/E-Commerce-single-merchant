<template>
  <div class="relative" ref="menuRef">
    <button
      @click="toggleMenu"
      class="flex items-center space-x-2 p-2 text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
    >
      <!-- User Avatar -->
      <div class="h-8 w-8 bg-primary-600 rounded-full flex items-center justify-center">
        <span class="text-sm font-medium text-white">
          {{ userInitials }}
        </span>
      </div>

      <!-- User Name (hidden on mobile) -->
      <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-gray-200">
        {{ authStore.user?.name }}
      </span>

      <!-- Dropdown Arrow -->
      <svg
        class="h-4 w-4 transition-transform"
        :class="{ 'rotate-180': isOpen }"
        fill="none" stroke="currentColor" viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown Menu -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 z-50"
      >
        <div class="py-2">
          <!-- User Info -->
          <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
              <div class="h-10 w-10 bg-primary-600 rounded-full flex items-center justify-center">
                <span class="text-sm font-medium text-white">
                  {{ userInitials }}
                </span>
              </div>
              <div>
                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ authStore.user?.name }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ authStore.user?.email }}</div>
              </div>
            </div>
          </div>

          <!-- Menu Items -->
          <div class="py-2">
            <!-- Dashboard Link (only visible with view_dashboard permission) -->
            <router-link
              v-if="authStore.hasPermission('view_dashboard')"
              to="/dashboard"
              @click="closeMenu"
              class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
              Dashboard
            </router-link>

            <router-link
              to="/profile"
              @click="closeMenu"
              class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Profile
            </router-link>

            <router-link
              to="/orders"
              @click="closeMenu"
              class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Orders
            </router-link>

            <router-link
              to="/wishlist"
              @click="closeMenu"
              class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
              Wishlist
            </router-link>

            <!-- Notifications Link -->
            <router-link
              to="/notifications"
              @click="closeMenu"
              class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <span>Notifications</span>
              <span v-if="unreadCount > 0" class="ml-auto inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                {{ unreadCount > 9 ? '9+' : unreadCount }}
              </span>
            </router-link>
          </div>

          <!-- Divider -->
          <div class="border-t border-gray-100 dark:border-gray-700"></div>

          <!-- Settings -->
          <div class="py-2">
            <button
              @click="handleSettings"
              class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Settings
            </button>
          </div>

          <!-- Divider -->
          <div class="border-t border-gray-100 dark:border-gray-700"></div>

          <!-- Logout -->
          <div class="py-2">
            <button
              @click="handleLogout"
              class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
            >
              <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              Logout
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, onBeforeMount, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useNotificationStore } from '../../stores/notification'
import { useToast } from 'vue-toastification'

const router = useRouter()
const authStore = useAuthStore()
const notificationStore = useNotificationStore()
const toast = useToast()

const menuRef = ref(null)
const isOpen = ref(false)

const userInitials = computed(() => {
  if (!authStore.user?.name) return 'U'

  const names = authStore.user.name.split(' ')
  if (names.length >= 2) {
    return (names[0][0] + names[1][0]).toUpperCase()
  }
  return names[0][0].toUpperCase()
})

// Get unread notifications count
const unreadCount = computed(() => notificationStore.unreadCount || 0)

const toggleMenu = () => {
  isOpen.value = !isOpen.value

  // Fetch notifications when menu opens
  if (isOpen.value) {
    fetchNotifications()
  }
}

const closeMenu = () => {
  isOpen.value = false
}

const fetchNotifications = async () => {
  try {
    await notificationStore.fetchUnreadCount()
  } catch (error) {
    console.error('Error fetching notifications count:', error)
  }
}

const handleSettings = () => {
  closeMenu()
  // Navigate to settings page when available
  toast.info('Settings page coming soon!')
}

const handleLogout = async () => {
  closeMenu()

  try {
    await authStore.logout()
    toast.success('Logged out successfully')
    router.push('/')
  } catch (error) {
    toast.error('Failed to logout')
  }
}

// Close menu when clicking outside
const handleClickOutside = (event) => {
  if (menuRef.value && !menuRef.value.contains(event.target)) {
    closeMenu()
  }
}

// Initialize polling for notifications
onBeforeMount(() => {
  notificationStore.startPolling(300000) // Poll every 5 minutes
})

onBeforeUnmount(() => {
  notificationStore.stopPolling()
})

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  // Initial fetch
  fetchNotifications()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
