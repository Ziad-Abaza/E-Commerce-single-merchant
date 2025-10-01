import { defineStore } from "pinia";
import axios from "../bootstrap";
import { useAuthStore } from "./auth";
import { useToast } from "vue-toastification";

export const useCartStore = defineStore("cart", {
    state: () => ({
        items: [],
        loading: false,
        error: null,
        summary: {
            total_items: 0,
            subtotal: 0,
            total: 0,
        },
        promoCode: null,
        discount: 0,
        isInitialized: false,
        needsSync: false,
    }),

    getters: {
        cartTotal: (state) => state.summary.total || 0,
        cartItemCount: (state) => state.summary.total_items || 0,
        getItemById: (state) => (itemId) => {
            return state.items.find((item) => item.id === itemId);
        },
        isInCart: (state) => (productDetailId) => {
            return state.items.some(
                (item) => item.product_detail_id === productDetailId,
            );
        },
        getItemByProductDetailId: (state) => (productDetailId) => {
            return state.items.find(
                (item) => item.product_detail_id === productDetailId,
            );
        },
        shippingCost: (state) => {
            return state.summary.subtotal >= 50 ? 0 : 9.99;
        },
        taxAmount(state) {
            const taxableAmount = state.summary.subtotal - state.discount;
            return taxableAmount > 0 ? taxableAmount * 0.08 : 0;
        },
        grandTotal(state) {
            return (
                state.summary.subtotal +
                this.shippingCost +
                this.taxAmount -
                this.discount
            );
        },
    },

    actions: {
        /**
         * Load cart from server or local storage
         */
        async loadCart() {
            this.loading = true;
            this.error = null;
            const authStore = useAuthStore();

            try {
                if (authStore.isAuthenticated) {
                    // Load from server for authenticated users
                    const response = await axios.get("/carts");
                    if (response.data.data) {
                        this.items = response.data.data.items || [];
                        this.summary = response.data.data.summary || {
                            total_items: 0,
                            subtotal: 0,
                            total: 0,
                        };
                        this.promoCode = response.data.data.promo_code || null;
                        this.discount = parseFloat(
                            response.data.data.discount || 0,
                        );
                        // If we had local cart items before login, sync them
                        if (this.needsSync) {
                            await this.syncLocalCartWithServer();
                        }
                    }
                } else {
                    // Load from localStorage for guests
                    this.loadFromLocalStorage();
                }

                this.isInitialized = true;
                return { success: true };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to load cart";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Add item to cart (works for both authenticated and guest users)
         */
        async addToCart(productDetailId, quantity = 1) {
            this.loading = true;
            this.error = null;
            const authStore = useAuthStore();
            const toast = useToast();

            try {
                if (authStore.isAuthenticated) {
                    const existingItem = this.items.find(
                        (item) => item.product_detail_id === productDetailId,
                    );

                    if (existingItem) {
                        await axios.post(`/carts/${existingItem.id}`, {
                            quantity: existingItem.quantity + quantity,
                        });
                    } else {
                        await axios.post("/carts", {
                            user_id: authStore.user?.id,
                            product_detail_id: productDetailId,
                            quantity: quantity,
                        });
                    }

                    await this.loadCart();
                } else {
                    const existingIndex = this.items.findIndex(
                        (item) => item.product_detail_id === productDetailId,
                    );

                    if (existingIndex >= 0) {
                        this.items[existingIndex].quantity += quantity;
                        this.items[existingIndex].updated_at =
                            new Date().toISOString();
                    } else {
                        this.items.push({
                            id: "local-" + Date.now(),
                            product_detail_id: productDetailId,
                            quantity: quantity,
                            created_at: new Date().toISOString(),
                            updated_at: new Date().toISOString(),
                        });
                    }

                    this.items = this.items.reduce((acc, item) => {
                        if (
                            !acc.some(
                                (i) =>
                                    i.product_detail_id ===
                                    item.product_detail_id,
                            )
                        )
                            acc.push(item);
                        return acc;
                    }, []);

                    this.updateLocalSummary();
                    this.saveToLocalStorage();
                }

                toast.success("Product added to cart!");
                return { success: true };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to add to cart";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },
        /**
         * Update cart item quantity
         */
        async updateCartItem(itemId, quantity) {
            this.loading = true;
            this.error = null;
            const authStore = useAuthStore();

            try {
                if (quantity <= 0) {
                    return await this.removeFromCart(itemId);
                }

                if (authStore.isAuthenticated) {
                    // For authenticated users, update on server
                    await axios.post(`/carts/${itemId}`, {
                        quantity: quantity,
                    });
                    await this.loadCart();
                } else {
                    // For guests, update local storage
                    const itemIndex = this.items.findIndex(
                        (item) => item.id === itemId,
                    );
                    if (itemIndex >= 0) {
                        this.items[itemIndex].quantity = quantity;
                        this.items[itemIndex].updated_at =
                            new Date().toISOString();
                        this.updateLocalSummary();
                        this.saveToLocalStorage();
                    }
                }

                return { success: true };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to update cart item";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Remove item from cart
         */
        async removeFromCart(itemId) {
            this.loading = true;
            this.error = null;
            const authStore = useAuthStore();

            try {
                if (authStore.isAuthenticated) {
                    await axios.delete(`/carts/${itemId}`);
                    await this.loadCart();
                } else {
                    // For guests, remove from local storage
                    const itemIndex = this.items.findIndex(
                        (item) => item.id === itemId,
                    );
                    if (itemIndex >= 0) {
                        this.items.splice(itemIndex, 1);
                        this.updateLocalSummary();
                        this.saveToLocalStorage();
                    }
                }

                const toast = useToast();
                toast.success("Item removed from cart");
                return { success: true };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to remove from cart";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Clear the entire cart
         */
        async clearCart() {
            this.loading = true;
            this.error = null;

            try {
                const authStore = useAuthStore();
                if (authStore.isAuthenticated) {
                    await axios.delete("/carts/clear");
                }

                // Clear local state and storage
                this.clearLocalCart();

                const toast = useToast();
                toast.success("Cart cleared");
                return { success: true };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to clear cart";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Apply promo code
         */
        async applyPromoCode(code) {
            this.loading = true;
            this.error = null;

            try {
                const authStore = useAuthStore();
                if (authStore.isAuthenticated) {
                    const response = await axios.post("/promo/apply", {
                        code: code,
                    });
                    this.promoCode = response.data.promo_code;
                    this.discount = parseFloat(response.data.discount);
                    await this.loadCart();
                } else {
                    // For guests, just store the promo code locally
                    // Note: Actual discount calculation would need to be handled in the UI
                    this.promoCode = code;
                    this.saveToLocalStorage();
                }

                return { success: true };
            } catch (error) {
                this.promoCode = null;
                this.discount = 0;
                this.error =
                    error.response?.data?.message || "Invalid promo code";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Remove promo code
         */
        async removePromoCode() {
            this.loading = true;
            this.error = null;

            try {
                const authStore = useAuthStore();
                if (authStore.isAuthenticated) {
                    await axios.post("/promo/remove");
                }

                this.promoCode = null;
                this.discount = 0;

                if (!authStore.isAuthenticated) {
                    this.saveToLocalStorage();
                } else {
                    await this.loadCart();
                }

                return { success: true };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to remove promo code";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Save cart to localStorage
         */
        saveToLocalStorage() {
            if (typeof window === "undefined") return;

            const cartData = {
                items: this.items,
                summary: this.summary,
                promoCode: this.promoCode,
                discount: this.discount,
                lastUpdated: new Date().toISOString(),
            };

            try {
                localStorage.setItem(
                    "ecommerce_cart",
                    JSON.stringify(cartData),
                );
            } catch (error) {
                console.error("Error saving cart to localStorage:", error);
                if (error.name === "QuotaExceededError") {
                    this.clearOldCartData();
                }
            }
        },

        /**
         * Clear old cart data to free up space
         */
        clearOldCartData() {
            try {
                // Keep only the most recent items (last 20 items)
                if (this.items.length > 20) {
                    this.items = this.items.slice(-20);
                    this.updateLocalSummary();
                    this.saveToLocalStorage();
                }
            } catch (e) {
                console.error("Failed to clear old cart data:", e);
            }
        },

        /**
         * Update local cart summary (for guest users)
         */
        updateLocalSummary() {
            const totalItems = this.items.reduce(
                (sum, item) => sum + item.quantity,
                0,
            );
            const subtotal = this.items.reduce((sum, item) => {
                return sum + (item.product?.price || 0) * item.quantity;
            }, 0);

            this.summary = {
                total_items: totalItems,
                subtotal: subtotal,
                total: subtotal, // Will be updated by getters with tax/shipping
            };
        },

        /**
         * Load cart from localStorage
         */
        loadFromLocalStorage() {
            if (typeof window === "undefined") return false;

            try {
                const cartData = localStorage.getItem("ecommerce_cart");
                if (!cartData) return false;

                const parsedCart = JSON.parse(cartData);

                // Basic validation
                if (!parsedCart || typeof parsedCart !== "object") {
                    console.error("Invalid cart data in localStorage");
                    return false;
                }

                this.items = Array.isArray(parsedCart.items)
                    ? parsedCart.items
                    : [];
                this.summary = parsedCart.summary || {
                    total_items: 0,
                    subtotal: 0,
                    total: 0,
                };
                this.promoCode = parsedCart.promoCode || null;
                this.discount = parseFloat(parsedCart.discount) || 0;
                this.needsSync = true; // Mark for sync when user logs in

                // Ensure we have valid data
                this.items = this.items.filter(
                    (item) =>
                        item &&
                        item.id &&
                        item.product_detail_id &&
                        item.quantity > 0,
                );

                // Update summary in case it's out of sync
                this.updateLocalSummary();

                return true;
            } catch (error) {
                console.error("Error loading cart from localStorage:", error);
                return false;
            }
        },

        /**
         * Sync local cart items with server after login
         */
        async syncLocalCartWithServer() {
            const authStore = useAuthStore();
            if (!authStore.isAuthenticated || !this.items.length) return false;

            const localItems = [...this.items]; // Copy local items

            try {
                const payload = localItems.map((item) => ({
                    user_id: authStore.user?.id,
                    product_detail_id: item.product_detail_id,
                    quantity: item.quantity,
                }));

                if (payload.length > 0) {
                    // Send local items to server
                    await axios.post("/carts/sync", { items: payload });

                    // Mark sync as done BEFORE clearing local cart
                    this.needsSync = false;

                    // Clear local cart to prevent duplicate syncing
                    this.clearLocalCart();
                }

                return true;
            } catch (error) {
                console.error("Failed to sync cart with server:", error);

                // Restore local items if sync failed
                if (this.items.length === 0) {
                    this.items = localItems;
                    this.updateLocalSummary();
                    this.saveToLocalStorage();
                }

                return false;
            }
        },
        /**
         * Initialize cart (called on app startup)
         */
        async initializeCart() {
            const authStore = useAuthStore();
            if (authStore.isAuthenticated) {
                await this.loadCart();
            } else {
                this.loadFromLocalStorage();
                this.isInitialized = true;
            }
        },

        /**
         * Get cart item count (for display in header)
         */
        getCartItemCount() {
            if (!this.isInitialized) {
                this.loadFromLocalStorage();
            }
            return this.summary.total_items || 0;
        },

        /**
         * Clear cart data (both local and state)
         */
        clearLocalCart() {
            this.items = [];
            this.summary = { total_items: 0, subtotal: 0, total: 0 };
            this.promoCode = null;
            this.discount = 0;
            this.needsSync = false;

            if (typeof window !== "undefined") {
                localStorage.removeItem("ecommerce_cart");
            }
        },

        /**
         * Handle errors with toast notifications
         */
        handleError(message) {
            const toast = useToast();
            toast.error(message);
            console.error("Cart Error:", message);
        },

        /**
         * Clear error state
         */
        clearError() {
            this.error = null;
        },
    },
});
