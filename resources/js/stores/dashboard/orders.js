import { defineStore } from "pinia";
import axios from "../../bootstrap";

export const useOrdersStore = defineStore("dashboardOrders", {
    state: () => ({
        orders: [],
        currentOrder: null,
        loading: false,
        deleting: false,
        error: null,
        pagination: {
            current_page: 1,
            per_page: 10,
            total: 0,
            last_page: 1,
            from: 0,
            to: 0,
        },
        filters: {
            search: "",
            status: "",
            user_id: "",
            start_date: "",
            end_date: "",
        },
        statistics: {
            total_orders: 0,
            pending: 0,
            processing: 0,
            shipped: 0,
            delivered: 0,
            cancelled: 0,
            refunded: 0,
            total_revenue: 0,
        },
    }),

    actions: {
        // Fetch all orders with filters
        async fetchOrders(page = 1, perPage = 10) {
            this.loading = true;
            this.error = null;

            try {
                const params = {
                    page,
                    per_page: perPage,
                    search: this.filters.search || undefined,
                    status: this.filters.status || undefined,
                    user_id: this.filters.user_id || undefined,
                    start_date: this.filters.start_date || undefined,
                    end_date: this.filters.end_date || undefined,
                };

                Object.keys(params).forEach(
                    (key) => params[key] === undefined && delete params[key],
                );

                const response = await axios.get("/dashboard/orders", {
                    params,
                });

                this.orders = response.data.data || [];
                this.pagination = response.data.pagination || this.pagination;
                this.statistics = response.data.statistics || this.statistics;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to load orders.";
            } finally {
                this.loading = false;
            }
        },

        // Fetch single order details
        async fetchOrder(id) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(`/dashboard/orders/${id}`);
                this.currentOrder = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to load order details.";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        // Update order info
        async updateOrder(id, orderData) {
            this.loading = true;
            this.error = null;
            try {
                // Format the data to match the API expectations
                const formattedData = { ...orderData };
                
                // Only include items if they are provided
                if (orderData.items) {
                    formattedData.items = orderData.items.map(item => ({
                        id: item.id, // May be undefined for new items
                        product_detail_id: item.product_detail_id,
                        product_name: item.product_name,
                        product_sku: item.product_sku,
                        quantity: item.quantity,
                        unit_price: item.unit_price,
                        _destroy: item._destroy // For marking items for deletion
                    }));
                }

                const response = await axios.post(
                    `/dashboard/orders/${id}`,
                    formattedData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                );
                
                // Update in orders list
                const index = this.orders.findIndex((o) => o.id === id);
                if (index !== -1) {
                    this.orders[index] = response.data.data;
                }
                
                // Update current order if it's the same
                if (this.currentOrder && this.currentOrder.id === id) {
                    this.currentOrder = response.data.data;
                }
                
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to update order.";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        // Update order status
        async updateOrderStatus(id, statusData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.patch(
                    `/dashboard/orders/${id}/status`,
                    statusData,
                );
                const index = this.orders.findIndex((o) => o.id === id);
                if (index !== -1) this.orders[index] = response.data.data;
                this.currentOrder = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to update order status.";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        // Cancel order
        async cancelOrder(id, reason = "") {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.patch(
                    `/dashboard/orders/${id}/cancel`,
                    { reason },
                );
                const index = this.orders.findIndex((o) => o.id === id);
                if (index !== -1) this.orders[index] = response.data.data;
                this.currentOrder = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to cancel order.";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        // Handle expired orders
        async handleExpiredOrders() {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post(
                    "/dashboard/orders/handle-expired",
                );
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to process expired orders.";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        // Filters
        setFilter(key, value) {
            this.filters[key] = value;
            this.pagination.current_page = 1;
            this.fetchOrders(1, this.pagination.per_page);
        },

        clearFilters() {
            this.filters = {
                search: "",
                status: "",
                user_id: "",
                start_date: "",
                end_date: "",
            };
            this.fetchOrders(1, this.pagination.per_page);
        },

        clearError() {
            this.error = null;
        },
    },
});
