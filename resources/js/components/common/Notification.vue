<template>
  <div
    v-if="notification"
    class="flex items-start p-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
    :class="{ 'bg-gray-50 dark:bg-gray-700': !notification.read_at }"
  >
    <!-- Notification Icon -->
    <div class="flex-shrink-0 pt-0.5">
      <div
        class="flex items-center justify-center w-8 h-8 rounded-full"
        :class="getNotificationIconClass"
      >
        <component :is="getNotificationIcon" class="w-4 h-4 text-white" />
      </div>
    </div>

    <!-- Notification Content -->
    <div class="ml-3 flex-1">
      <div class="flex items-start justify-between">
        <p class="text-sm font-medium text-gray-900 dark:text-white">
          {{ notification.data?.title || 'Notification' }}
        </p>
        <button
          v-if="!notification.read_at"
          @click.stop="markAsRead"
          class="ml-2 text-xs text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300"
        >
          Mark as read
        </button>
      </div>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
        {{ notification.data?.body || 'No message' }}
      </p>
      <div class="flex items-center justify-between mt-2">
        <p class="text-xs text-gray-400">
          {{ formatTimeAgo }}
        </p>
        <button
          @click.stop="deleteNotification"
          class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
          title="Delete notification"
        >
          Delete
        </button>
      </div>
    </div>
  </div>

  <!-- Fallback UI if notification is missing -->
  <div v-else class="p-4 text-red-500 text-sm">
    Invalid notification data
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useNotificationStore } from '@/stores/notification';
import { BellIcon, ShoppingCartIcon, ExclamationCircleIcon, XMarkIcon, StarIcon, ChatBubbleLeftRightIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  notification: {
    type: Object,
    required: true,
    default: null
  },
});

const emit = defineEmits(['updated', 'deleted']);

const notificationStore = useNotificationStore();

const markAsRead = async () => {
  if (!props.notification) return;

  try {
    await notificationStore.markAsRead(props.notification.id);
    emit('updated');
  } catch (error) {
    console.error('Error marking notification as read:', error);
  }
};

const deleteNotification = async () => {
  if (!props.notification) return;

  try {
    await notificationStore.deleteNotification(props.notification.id);
    emit('deleted');
  } catch (error) {
    console.error('Error deleting notification:', error);
  }
};

// Computed properties for safety
const getNotificationIcon = computed(() => {
  if (!props.notification?.data?.title) return BellIcon;

  if (props.notification.data.title.includes('New Order')) return ShoppingCartIcon;
  if (props.notification.data.title.includes('Order Status')) return ExclamationCircleIcon;
  if (props.notification.data.title.includes('Order Cancelled')) return XMarkIcon;
  if (props.notification.data.title.includes('New Product Review')) return StarIcon;
  if (props.notification.data.title.includes('New Contact Message')) return StarIcon;
  return BellIcon;
});

const getNotificationIconClass = computed(() => {
  if (!props.notification?.data?.title) return 'bg-gray-500';

  if (props.notification.data.title.includes('New Order')) return 'bg-blue-500';
  if (props.notification.data.title.includes('Order Status')) return 'bg-yellow-500';
  if (props.notification.data.title.includes('Order Cancelled')) return 'bg-red-500';
  if (props.notification.data.title.includes('New Product Review')) return 'bg-purple-500';
  if (props.notification.data.title.includes('New Contact Message')) return 'bg-green-500';
  return 'bg-gray-500';
});

const formatTimeAgo = computed(() => {
  if (!props.notification?.created_at) return 'Unknown time';

  const date = new Date(props.notification.created_at);
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
});
</script>
