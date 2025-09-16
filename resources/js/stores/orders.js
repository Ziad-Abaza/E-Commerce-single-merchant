// src/stores/orders.js
import { defineStore } from "pinia";
import axios from "../bootstrap";
import { useToast } from "vue-toastification";

export const useOrderStore = defineStore("orders", {
    state: () => ({
        orders: [],
        currentOrder: null,
        pagination: {
            currentPage: 1,
            lastPage: 1,
            perPage: 15,
            total: 0,
        },
        loading: false,
        error: null,
    }),

    getters: {
        /**
         * Get order by ID
         */
        getOrderById: (state) => (id) => {
            return state.orders.find((order) => order.id === id);
        },

        /**
         * Get formatted status text
         */
        getStatusText: () => (status) => {
            const texts = {
                pending: "Pending",
                confirmed: "Confirmed",
                shipped: "Shipped",
                delivered: "Delivered",
                cancelled: "Cancelled",
            };
            return texts[status] || "Unknown";
        },

        /**
         * Get status color classes
         */
        getStatusColor: () => (status) => {
            const colors = {
                pending: "bg-yellow-100 text-yellow-800",
                confirmed: "bg-blue-100 text-blue-800",
                shipped: "bg-purple-100 text-purple-800",
                delivered: "bg-green-100 text-green-800",
                cancelled: "bg-red-100 text-red-800",
            };
            return colors[status] || "bg-gray-100 text-gray-800";
        },

        /**
         * Check if order can be cancelled
         */
        canBeCancelled: (state) => (orderId) => {
            const order = state.orders.find((o) => o.id === orderId);
            return order ? order.can_be_cancelled : false;
        },
    },

    actions: {
        /**
         * Load orders list
         */
        async loadOrders(params = {}) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get("/orders", { params });

                this.orders = response.data.data;
                this.pagination = {
                    currentPage: response.data.pagination.current_page,
                    lastPage: response.data.pagination.last_page,
                    perPage: response.data.pagination.per_page,
                    total: response.data.pagination.total,
                };

                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to load orders";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Load single order
         */
        async getOrder(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get(`/orders/${id}`);

                this.currentOrder = response.data.data;
                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to load order";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Create new order
         */
        async createOrder(orderData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post("/orders", orderData);

                // Add to orders list if we're on the list page
                if (this.orders.length > 0) {
                    this.orders.unshift(response.data.data);
                }

                const toast = useToast();
                toast.success("Order created successfully");
                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to create order";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Update order
         */
        async updateOrder(id, orderData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post(`/orders/${id}`, orderData);

                // Update in orders list
                const index = this.orders.findIndex((order) => order.id === id);
                if (index !== -1) {
                    this.orders[index] = response.data.data;
                }

                // Update current order if it's the same
                if (this.currentOrder && this.currentOrder.id === id) {
                    this.currentOrder = response.data.data;
                }

                const toast = useToast();
                toast.success("Order updated successfully");
                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to update order";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Cancel order
         */
        async cancelOrder(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post(`/orders/${id}`, {
                    status: "cancelled",
                });

                // Update in orders list
                const index = this.orders.findIndex((order) => order.id === id);
                if (index !== -1) {
                    this.orders[index] = response.data.data;
                }

                // Update current order if it's the same
                if (this.currentOrder && this.currentOrder.id === id) {
                    this.currentOrder = response.data.data;
                }

                const toast = useToast();
                toast.success("Order cancelled successfully");
                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to cancel order";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Delete order
         */
        async deleteOrder(id) {
            this.loading = true;
            this.error = null;
            try {
                await axios.delete(`/orders/${id}`);

                // Remove from orders list
                this.orders = this.orders.filter((order) => order.id !== id);

                // Clear current order if it's the same
                if (this.currentOrder && this.currentOrder.id === id) {
                    this.currentOrder = null;
                }

                const toast = useToast();
                toast.success("Order deleted successfully");
                return { success: true };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to delete order";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Handle errors with toast notifications
         */
        handleError(message) {
            const toast = useToast();
            toast.error(message || "An error occurred");
        },

        /**
         * Clear error state
         */
        clearError() {
            this.error = null;
        },

        /**
         * Initialize orders store
         */
        async initializeOrders() {
            await this.loadOrders();
        },
    },
});
