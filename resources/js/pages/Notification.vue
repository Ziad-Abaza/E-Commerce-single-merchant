<!-- src/views/dashboard/NotificationsPage.vue -->
<template>
  <div class="p-6">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h1>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your notifications</p>
    </div>

    <!-- Actions -->
    <div class="mb-6 flex items-center justify-between">
      <div>
        <button
          v-if="unreadCount > 0"
          @click="markAllAsRead"
          :disabled="isMarkingAllAsRead"
          class="px-4 py-2 text-sm font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 disabled:opacity-50"
        >
          {{ isMarkingAllAsRead ? 'Marking...' : 'Mark all as read' }}
        </button>
      </div>
      <div>
        <button
          @click="deleteAllNotifications"
          :disabled="isDeletingAll"
          class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 disabled:opacity-50"
        >
          {{ isDeletingAll ? 'Deleting...' : 'Delete all' }}
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex justify-center py-8">
      <div class="w-8 h-8 border-4 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="notifications.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No notifications</h3>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You're all caught up.</p>
    </div>

    <!-- Notifications List -->
    <div v-else class="space-y-2">
      <Notification
        v-for="notification in notifications"
        :key="notification.id"
        :notification="notification"
        @updated="fetchNotifications"
        @deleted="fetchNotifications"
      />
    </div>

    <!-- Pagination -->
    <div v-if="pagination.last_page > 1" class="mt-6 flex items-center justify-between">
      <button
        :disabled="pagination.current_page === 1"
        @click="changePage(pagination.current_page - 1)"
        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
      >
        Previous
      </button>
      <span class="text-sm text-gray-700 dark:text-gray-300">
        Page {{ pagination.current_page }} of {{ pagination.last_page }}
      </span>
      <button
        :disabled="pagination.current_page === pagination.last_page"
        @click="changePage(pagination.current_page + 1)"
        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
      >
        Next
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useNotificationStore } from '@/stores/notification';
import Notification from '@/components/common/Notification.vue';

const notificationStore = useNotificationStore();

const isLoading = ref(false);
const isMarkingAllAsRead = ref(false);
const isDeletingAll = ref(false);

const notifications = computed(() => notificationStore.notifications);
const unreadCount = computed(() => notificationStore.unreadCount);
const pagination = computed(() => notificationStore.pagination);

async function fetchNotifications(page = 1) {
  try {
    isLoading.value = true;
    await notificationStore.fetchNotifications(page, 10);
  } catch (error) {
    console.error('Error fetching notifications:', error);
  } finally {
    isLoading.value = false;
  }
}

async function markAllAsRead() {
  try {
    isMarkingAllAsRead.value = true;
    await notificationStore.markAllAsRead();
    await fetchNotifications(pagination.value.current_page);
  } catch (error) {
    console.error('Error marking all as read:', error);
  } finally {
    isMarkingAllAsRead.value = false;
  }
}

async function deleteAllNotifications() {
  try {
    isDeletingAll.value = true;
    await notificationStore.deleteAllNotifications();
    await fetchNotifications(1);
  } catch (error) {
    console.error('Error deleting all notifications:', error);
  } finally {
    isDeletingAll.value = false;
  }
}

function changePage(page) {
  fetchNotifications(page);
}

onMounted(() => {
  fetchNotifications(1);
});
</script>
