<template>
  <div class="relative">
    <!-- Notification Bell -->
    <button
      @click.stop="toggleDropdown"
      class="relative p-2 text-gray-500 hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400 focus:outline-none"
      aria-label="Notifications"
      type="button"
    >
      <svg
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
        ></path>
      </svg>

      <!-- Unread Badge -->
      <span
        v-if="hasUnread"
        class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>

    <!-- Dropdown Panel -->
    <transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <div v-if="isOpen" class="fixed inset-0 z-40" @click="closeDropdown">
        <div
          v-click-outside="closeDropdown"
          class="absolute right-0 z-50 w-80 mt-2 origin-top-right bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
          role="menu"
          aria-orientation="vertical"
          aria-labelledby="notification-menu"
          @click.stop
        >
          <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">Notifications</h3>
              <button
                v-if="unreadCount > 0"
                @click="markAllAsRead"
                class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300"
                :disabled="isMarkingAllAsRead"
              >
                {{ isMarkingAllAsRead ? 'Marking...' : 'Mark all as read' }}
              </button>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="isLoading" class="p-4 text-center">
            <div class="flex justify-center">
              <div class="w-8 h-8 border-4 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Loading notifications...</p>
          </div>

          <!-- Empty State -->
          <div v-else-if="notifications.length === 0" class="p-4 text-center">
            <div class="text-gray-400 dark:text-gray-500">
              <svg
                class="w-12 h-12 mx-auto mb-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                ></path>
              </svg>
              <p class="text-sm">No new notifications</p>
            </div>
          </div>

          <!-- Notifications List -->
          <div v-else class="max-h-96 overflow-y-auto">
            <div
              v-for="notification in notifications"
              :key="notification.id"
              @click="handleNotificationClick(notification)"
              class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-150"
              :class="{ 'bg-gray-50 dark:bg-gray-700': !notification.read_at }"
            >
              <div class="flex items-start">
                <!-- Notification Icon -->
                <div class="flex-shrink-0 pt-0.5">
                  <div
                    class="flex items-center justify-center w-8 h-8 rounded-full"
                    :class="getNotificationIconClass(notification.data.title)"
                  >
                    <component :is="getNotificationIcon(notification.data.title)" class="w-4 h-4 text-white" />
                  </div>
                </div>

                <!-- Notification Content -->
                <div class="ml-3 flex-1">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ notification.data.title }}
                  </p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ notification.data.body }}
                  </p>
                  <p class="mt-1 text-xs text-gray-400">
                    {{ formatTimeAgo(notification.created_at) }}
                  </p>
                </div>

                <!-- Unread Indicator -->
                <div v-if="!notification.read_at" class="ml-2">
                  <span class="inline-block w-2 h-2 bg-primary-500 rounded-full"></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="p-2 text-center border-t border-gray-200 dark:border-gray-700">
            <router-link
              to="/dashboard/notifications"
              class="inline-block px-4 py-2 text-sm font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300"
              @click="closeDropdown"
            >
              View all notifications
            </router-link>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useNotificationStore } from '@/stores/notification';
import { BellIcon, ShoppingCartIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline';

defineEmits(['close']);

const router = useRouter();
const notificationStore = useNotificationStore();

const isOpen = ref(false);
const isLoading = ref(false);
const isMarkingAllAsRead = ref(false);

// Computed
const notifications = computed(() => {
  return notificationStore.notifications?.slice(0, 5) || [];
});
const unreadCount = computed(() => notificationStore.unreadCount || 0);
const hasUnread = computed(() => (unreadCount.value || 0) > 0);

// Methods
const toggleDropdown = async (event) => {
  event.stopPropagation();
  isOpen.value = !isOpen.value;

  if (isOpen.value) {
    try {
      await fetchNotifications();
    } catch (error) {
      console.error('Error fetching notifications:', error);
    }
  }
};

const closeDropdown = () => {
  isOpen.value = false;
};

const fetchNotifications = async () => {
  try {
    isLoading.value = true;
    await notificationStore.fetchNotifications(1, 5);
  } catch (error) {
    console.error('Error fetching notifications:', error);
  } finally {
    isLoading.value = false;
  }
};

const markAllAsRead = async () => {
  try {
    isMarkingAllAsRead.value = true;
    await notificationStore.markAllAsRead();
  } catch (error) {
    console.error('Error marking all as read:', error);
  } finally {
    isMarkingAllAsRead.value = false;
  }
};

const handleNotificationClick = async (notification) => {
  try {
    // Mark as read if unread
    if (!notification.read_at) {
      await notificationStore.markAsRead(notification.id);
    }

    // Navigate to the notification URL if available
    if (notification.data.url) {
      router.push(notification.data.url);
    }

    closeDropdown();
  } catch (error) {
    console.error('Error handling notification click:', error);
  }
};

// Notification helpers
const getNotificationIcon = (title) => {
  if (title.includes('New Order')) return ShoppingCartIcon;
  if (title.includes('Order Status')) return ExclamationCircleIcon;
  return BellIcon;
};

const getNotificationIconClass = (title) => {
  if (title.includes('New Order')) return 'bg-blue-500';
  if (title.includes('Order Status')) return 'bg-yellow-500';
  return 'bg-gray-500';
};

const formatTimeAgo = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const seconds = Math.floor((now - date) / 1000);

  const intervals = {
    year: 31536000,
    month: 2592000,
    week: 604800,
    day: 86400,
    hour: 3600,
    minute: 60,
  };

  for (const [unit, secondsInUnit] of Object.entries(intervals)) {
    const interval = Math.floor(seconds / secondsInUnit);
    if (interval >= 1) {
      return interval === 1 ? `1 ${unit} ago` : `${interval} ${unit}s ago`;
    }
  }

  return 'Just now';
};

// Lifecycle Hooks
onMounted(async () => {
  // Start polling for new notifications
  notificationStore.startPolling(300000); // Poll every 5 minutes

  // Initial fetch if needed
  if (isOpen.value && (!notificationStore.notifications || notificationStore.notifications.length === 0)) {
    try {
      await fetchNotifications();
    } catch (error) {
      console.error('Error fetching notifications on mount:', error);
    }
  }
});

onUnmounted(() => {
  // Clean up polling
  notificationStore.stopPolling();
});
</script>
