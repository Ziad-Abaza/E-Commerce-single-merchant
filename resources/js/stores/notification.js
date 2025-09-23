import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axios from "../bootstrap";

export const useNotificationStore = defineStore("notification", () => {
    const notifications = ref([]);
    const unreadCount = ref(0);
    const isLoading = ref(false);
    const error = ref(null);
    const pagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
    });

    // Getters
    const hasUnread = computed(() => unreadCount.value > 0);
    const allNotifications = computed(() => notifications.value);
    const unreadNotifications = computed(() =>
        notifications.value.filter((notification) => !notification.read_at),
    );

    // Actions
    async function fetchNotifications(page = 1, perPage = 10) {
        isLoading.value = true;
        error.value = null;

        try {
            const response = await axios.get("/notifications", {
                params: { page, per_page: perPage },
            });

            notifications.value = response.data.data;
            pagination.value = response.data.pagination;

            return response.data;
        } catch (err) {
            error.value =
                err.response?.data?.message || "Failed to fetch notifications";
            throw error.value;
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchUnreadCount() {
        try {
            const response = await axios.get("/notifications/unread-count");
            unreadCount.value = response.data.count;
            return response.data.count;
        } catch (err) {
            console.error("Failed to fetch unread count:", err);
            return 0;
        }
    }

    async function markAsRead(notificationId) {
        try {
            await axios.post(`/notifications/${notificationId}/read`);

            const notification = notifications.value.find(
                (n) => n.id === notificationId,
            );
            if (notification && !notification.read_at) {
                notification.read_at = new Date().toISOString();
                unreadCount.value = Math.max(0, unreadCount.value - 1);
            }

            return true;
        } catch (err) {
            console.error("Failed to mark notification as read:", err);
            throw err;
        }
    }

    async function markAllAsRead() {
        try {
            await axios.post("/notifications/read-all");

            let unreadBefore = 0;
            notifications.value.forEach((notification) => {
                if (!notification.read_at) {
                    unreadBefore++;
                    notification.read_at = new Date().toISOString();
                }
            });

            unreadCount.value = Math.max(0, unreadCount.value - unreadBefore);
            return true;
        } catch (err) {
            console.error("Failed to mark all notifications as read:", err);
            throw err;
        }
    }

    async function deleteNotification(notificationId) {
        try {
            await axios.delete(`/notifications/${notificationId}`);

            const index = notifications.value.findIndex(
                (n) => n.id === notificationId,
            );
            if (index !== -1) {
                const deletedNotification = notifications.value[index];
                notifications.value.splice(index, 1);

                if (deletedNotification && !deletedNotification.read_at) {
                    unreadCount.value = Math.max(0, unreadCount.value - 1);
                }
            }

            return true;
        } catch (err) {
            console.error("Failed to delete notification:", err);
            throw err;
        }
    }

    async function deleteAllNotifications() {
        try {
            await axios.delete("/notifications");

            let unreadDeleted = notifications.value.filter(
                (n) => !n.read_at,
            ).length;
            notifications.value = [];
            unreadCount.value = Math.max(0, unreadCount.value - unreadDeleted);

            return true;
        } catch (err) {
            console.error("Failed to delete all notifications:", err);
            throw err;
        }
    }

    // Initialize with polling for new notifications
    let pollInterval = null;

    function startPolling(interval = 60000) {
        stopPolling();

        fetchUnreadCount();
        pollInterval = setInterval(() => {
            fetchUnreadCount();
        }, interval);
    }

    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }

    return {
        // State
        notifications,
        unreadCount,
        isLoading,
        error,
        pagination,

        // Getters
        hasUnread,
        allNotifications,
        unreadNotifications,

        // Actions
        fetchNotifications,
        fetchUnreadCount,
        markAsRead,
        markAllAsRead,
        deleteNotification,
        deleteAllNotifications,
        startPolling,
        stopPolling,
    };
});
