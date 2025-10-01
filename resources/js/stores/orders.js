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
         * Send order via WhatsApp
         * @param {Object} orderData - Order data
         * @param {string} orderData.phone - Customer phone number
         * @param {string} orderData.shipping_address - Shipping address
         * @param {string} orderData.shipping_phone - Shipping phone
         * @param {string} [orderData.notes] - Order notes
         * @param {Array} orderData.items - Order items
         * @param {number} orderData.subtotal - Order subtotal
         * @param {number} orderData.shipping_fee - Shipping fee
         * @param {number} orderData.tax - Tax amount
         * @param {number} orderData.total - Order total
         * @param {string} orderData.currency - Currency code
         * @param {Object} settings - Site settings
         * @param {string} settings.whatsapp_number - Merchant's WhatsApp number
         * @param {string} [settings.whatsapp_order_message] - WhatsApp message template
         * @param {string} settings.currency - Default currency
         * @returns {Promise<{success: boolean, data: Object|null, error: string|null}>}
         */
        async sendOrderViaWhatsApp(orderData, settings) {
            this.loading = true;
            const toast = useToast();

            try {
                const merchantWhatsApp = settings?.whatsapp_number || "";
                const template =
                    settings?.whatsapp_order_message ||
                    "ORDER #{order_number}\nTotal: {total}\nItems:\n{items}\nLink: {order_link}";

                if (!merchantWhatsApp || merchantWhatsApp.length < 5) {
                    throw new Error("WhatsApp number is missing or invalid");
                }

                // Create the order first
                const { success, data, error } = await this.createOrder({
                    ...orderData,
                    notes:
                        orderData.notes || "Order placed via WhatsApp checkout",
                });

                if (!success) {
                    throw new Error(error || "Failed to create order");
                }

                // Format items for WhatsApp message
                const formatPrice = (price) => {
                    return `${parseFloat(price || 0).toFixed(2)} ${settings?.currency || "USD"}`;
                };

                let itemsText = orderData.items
                    .map(
                        (item) =>
                            `• ${item.name || "Product"} x${item.quantity} = ${formatPrice(item.price * item.quantity)}`,
                    )
                    .join("\n");

                const orderLink = `${window.location.origin}/orders/${data.data.id}`;
                let message = template
                    .replace("{order_number}", data.data.order_number || "N/A")
                    .replace("{total}", formatPrice(orderData.total))
                    .replace("{items}", itemsText || "No items")
                    .replace("{order_link}", orderLink);

                const encodedMessage = encodeURIComponent(message);
                const whatsappUrl = `https://wa.me/${merchantWhatsApp}?text=${encodedMessage}`;

                // Return the WhatsApp URL and order data
                return {
                    success: true,
                    data: {
                        ...data.data,
                        whatsappUrl,
                    },
                    error: null,
                };
            } catch (error) {
                console.error("WhatsApp order error:", error);
                toast.error(
                    error.message || "Failed to process WhatsApp order",
                );
                return {
                    success: false,
                    data: null,
                    error: error.message || "Failed to process WhatsApp order",
                };
            } finally {
                this.loading = false;
            }
        },
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
         * @param {Object} orderData - Order data
         * @param {string} orderData.shipping_address - Delivery address
         * @param {string} orderData.phone - Contact phone number
         * @param {string} [orderData.notes] - Optional order notes
         * @param {File} [orderData.receipt] - Receipt file (pdf, jpg, jpeg, png, max 8MB)
         * @param {File} [orderData.invoice] - Invoice file (pdf, jpg, jpeg, png, max 8MB)
         * @param {Array<File>} [orderData.attachments] - Additional attachment files (pdf, jpg, jpeg, png, max 8MB each)
         * @param {Array} orderData.items - Array of order items
         * @param {number} orderData.items[].product_detail_id - Product detail ID (required)
         * @param {number} orderData.items[].quantity - Item quantity (min: 1)
         */
        async createOrder(orderData) {
            this.loading = true;
            this.error = null;
            try {
                // Create FormData to handle file uploads
                const formData = new FormData();
                
                // Add text fields
                if (orderData.shipping_address) formData.append('shipping_address', orderData.shipping_address);
                if (orderData.phone) formData.append('phone', orderData.phone);
                if (orderData.notes) formData.append('notes', orderData.notes);
                
                // Add files if provided
                if (orderData.receipt) formData.append('receipt', orderData.receipt);
                if (orderData.invoice) formData.append('invoice', orderData.invoice);
                
                // Add attachments if provided
                if (orderData.attachments && orderData.attachments.length > 0) {
                    orderData.attachments.forEach((file, index) => {
                        formData.append(`attachments[${index}]`, file);
                    });
                }
                
                // Add order items
                if (orderData.items && orderData.items.length > 0) {
                    orderData.items.forEach((item, index) => {
                        formData.append(`items[${index}][product_detail_id]`, item.product_detail_id);
                        formData.append(`items[${index}][quantity]`, item.quantity);
                    });
                }

                const response = await axios.post("/orders", formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });

                // Add to orders list if we're on the list page
                if (this.orders.length > 0) {
                    this.orders.unshift(response.data.data);
                }

                const toast = useToast();
                toast.success("Order created successfully");
                return {
                    success: true,
                    data: response.data,
                    order: response.data.data, // Return the created order with calculated fields
                };
            } catch (error) {
                let errorMessage = "Failed to create order";
                
                if (error.response?.data?.errors) {
                    // Format validation errors
                    const errors = error.response.data.errors;
                    errorMessage = Object.values(errors)
                        .flat()
                        .join(' ');
                } else if (error.response?.data?.message) {
                    errorMessage = error.response.data.message;
                }
                
                this.error = errorMessage;
                this.handleError(errorMessage);
                return {
                    success: false,
                    error: errorMessage,
                    errors: error.response?.data?.errors,
                };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Update order
         * @param {number} id - Order ID
         * @param {Object} orderData - Order data
         * @param {string} [orderData.shipping_address] - Delivery address
         * @param {string} [orderData.phone] - Contact phone number
         * @param {string} [orderData.notes] - Optional order notes
         * @param {Array} [orderData.items] - Array of order items (optional for partial updates)
         * @param {number} [orderData.items[].id] - Order item ID (for updates)
         * @param {number} orderData.items[].product_detail_id - Product variant ID
         * @param {number} orderData.items[].quantity - Item quantity
         */
        async updateOrder(id, orderData) {
            this.loading = true;
            this.error = null;
            try {
                // Format the data to match the API expectations
                const formattedData = {
                    shipping_address: orderData.shipping_address,
                    phone: orderData.phone,
                    notes: orderData.notes,
                    _method: "PUT", // For Laravel to handle as PUT request
                };

                // Only include items if they are provided
                if (orderData.items) {
                    formattedData.items = orderData.items.map((item) => ({
                        id: item.id, // May be undefined for new items
                        product_detail_id: item.product_detail_id,
                        quantity: item.quantity,
                    }));
                }

                const response = await axios.post(
                    `/orders/${id}`,
                    formattedData,
                );

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
                const response = await axios.get(`/orders/${id}/cancel`);

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
