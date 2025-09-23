// src/stores/wishlist.js
import { defineStore } from 'pinia'
import axios from '../bootstrap'
import { useAuthStore } from './auth'
import { useToast } from 'vue-toastification'

export const useWishlistStore = defineStore("wishlist", {
    state: () => ({
        categories: [],
        items: [],
        loading: false,
        error: null,
        selectedCategory: null,
        itemCount: 0,
        pagination: {
            currentPage: 1,
            lastPage: 1,
            perPage: 15,
            total: 0,
        },
    }),

    getters: {
        /**
         * Get default category
         */
        defaultCategory: (state) => {
            return state.categories.find((cat) => cat.is_default) || null;
        },

        /**
         * Get custom categories (non-default)
         */
        customCategories: (state) => {
            return state.categories.filter((cat) => !cat.is_default);
        },

        /**
         * Get items for selected category or all items
         */
        filteredItems: (state) => {
            if (!state.selectedCategory) {
                return state.items;
            }
            return state.items.filter(
                (item) =>
                    item.wishlist_category_id === state.selectedCategory.id,
            );
        },

        /**
         * Check if product is in wishlist
         */
        isInWishlist: (state) => (productId) => {
            return state.items.some((item) => item.product_id === productId);
        },

        /**
         * Get wishlist item by product ID
         */
        getItemByProductId: (state) => (productId) => {
            return state.items.find((item) => item.product_id === productId);
        },

        /**
         * Get category by ID
         */
        getCategoryById: (state) => (categoryId) => {
            return state.categories.find((cat) => cat.id === categoryId);
        },
    },

    actions: {
        /**
         * Load all wishlist categories for current user
         */
        async loadCategories() {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get("/wishlist-categories");

                this.categories = response.data.data || [];
                this.pagination = {
                    currentPage: response.data.pagination?.current_page || 1,
                    lastPage: response.data.pagination?.last_page || 1,
                    perPage: response.data.pagination?.per_page || 15,
                    total: response.data.pagination?.total || 0,
                };

                // If no default category, create one
                if (!this.defaultCategory) {
                    await this.createDefaultCategory();
                }

                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to load wishlist categories";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Load all wishlist items for current user
         */
        async loadItems() {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get("/wishlist-items");

                this.items = response.data.data || [];
                this.itemCount = response.data.data?.length || 0;
                this.pagination = {
                    currentPage: response.data.pagination?.current_page || 1,
                    lastPage: response.data.pagination?.last_page || 1,
                    perPage: response.data.pagination?.per_page || 15,
                    total: response.data.pagination?.total || 0,
                };

                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to load wishlist items";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Load wishlist items by category
         */
        async loadItemsByCategory(categoryId) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get("/wishlist-items", {
                    params: { wishlist_category_id: categoryId },
                });

                this.items = response.data.data || [];
                this.selectedCategory = this.getCategoryById(categoryId);
                this.itemCount = response.data.data?.length || 0;
                this.pagination = {
                    currentPage: response.data.pagination?.current_page || 1,
                    lastPage: response.data.pagination?.last_page || 1,
                    perPage: response.data.pagination?.per_page || 15,
                    total: response.data.pagination?.total || 0,
                };

                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to load wishlist items";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Get default category (create if doesn't exist)
         */
        async getDefaultCategory() {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(
                    "/wishlist-categories/default",
                );
                const defaultCategory = response.data.data;

                if (!defaultCategory) {
                    return {
                        success: false,
                        error: "No default category found",
                    };
                }

                // Add to state if not already stored
                const exists = this.categories.find(
                    (cat) => cat.id === defaultCategory.id,
                );
                if (!exists) {
                    this.categories.push(defaultCategory);
                }

                return { success: true, data: defaultCategory }; // ✅ return category object
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to get default category";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Create default category
         */
        async createDefaultCategory() {
            this.loading = true;
            this.error = null;
            const authStore = useAuthStore();

            if (!authStore.isAuthenticated) {
                throw new Error(
                    "User must be authenticated to create a category",
                );
            }

            try {
                const response = await axios.post("/wishlist-categories", {
                    name: "Favorites",
                    user_id: authStore.user?.id, // ✅ fixed here
                    is_default: true,
                });

                this.categories.push(response.data.data);
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to create default category";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Create new wishlist category
         */
        async createCategory(name, isDefault = false) {
            this.loading = true;
            this.error = null;
            const authStore = useAuthStore();

            if (!authStore.isAuthenticated) {
                throw new Error(
                    "User must be authenticated to create a category",
                );
            }

            try {
                await axios.post("/wishlist-categories", {
                    name,
                    user_id: authStore.user?.id, // ✅ fixed here
                    is_default: isDefault,
                });

                // Reload categories to sync
                await this.loadCategories();

                const toast = useToast();
                toast.success("Category created successfully");
                return { success: true };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to create category";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Update wishlist category
         */
        async updateCategory(categoryId, name, isDefault = false) {
            this.loading = true;
            this.error = null;

            try {
                const data = { name };
                if (isDefault !== undefined) {
                    data.is_default = isDefault;
                }

                const response = await axios.post(
                    `/wishlist-categories/${categoryId}`,
                    data,
                );

                // Update category in state
                const categoryIndex = this.categories.findIndex(
                    (cat) => cat.id === categoryId,
                );
                if (categoryIndex !== -1) {
                    this.categories[categoryIndex] = response.data.data;
                }

                // If setting as default, update other categories
                if (isDefault) {
                    this.categories.forEach((cat) => {
                        if (cat.id !== categoryId && cat.is_default) {
                            cat.is_default = false;
                        }
                    });
                }

                const toast = useToast();
                toast.success("Category updated successfully");

                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to update category";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Delete wishlist category
         */
        async deleteCategory(categoryId) {
            this.loading = true;
            this.error = null;

            try {
                // Check if it's default category
                const category = this.getCategoryById(categoryId);
                if (category?.is_default) {
                    this.error = "Cannot delete default category";
                    this.handleError(this.error);
                    return { success: false, error: this.error };
                }

                await axios.delete(`/wishlist-categories/${categoryId}`);

                // Remove from state
                this.categories = this.categories.filter(
                    (cat) => cat.id !== categoryId,
                );
                this.items = this.items.filter(
                    (item) => item.wishlist_category_id !== categoryId,
                );

                // If selected category was deleted, reset selection
                if (this.selectedCategory?.id === categoryId) {
                    this.selectedCategory = null;
                }

                const toast = useToast();
                toast.success("Category deleted successfully");

                return { success: true };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to delete category";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Add product to wishlist
         */
        async addToWishlist(productId, categoryId = null) {
            this.loading = true;
            this.error = null;

            try {
                let targetCategoryId = categoryId;

                // Use default category if no category specified
                if (!targetCategoryId) {
                    if (this.defaultCategory) {
                        targetCategoryId = this.defaultCategory.id;
                    } else {
                        const defaultCat = await this.getDefaultCategory();
                        targetCategoryId = defaultCat.data?.id; // ✅ fixed here
                    }
                }

                const response = await axios.post("/wishlist-items", {
                    wishlist_category_id: targetCategoryId,
                    product_id: productId,
                });

                // Add or update item in state
                const existingIndex = this.items.findIndex(
                    (item) => item.id === response.data.data.id,
                );
                if (existingIndex === -1) {
                    this.items.push(response.data.data);
                } else {
                    this.items[existingIndex] = response.data.data;
                }

                this.itemCount = this.items.length;

                const toast = useToast();
                toast.success("Product added to wishlist");

                return { success: true, data: response.data.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to add to wishlist";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Remove product from wishlist
         */
        async removeFromWishlist(itemId) {
            this.loading = true;
            this.error = null;

            try {
                await axios.delete(`/wishlist-items/${itemId}`);

                // Remove from items array
                this.items = this.items.filter((item) => item.id !== itemId);
                this.itemCount = this.items.length;

                const toast = useToast();
                toast.success("Product removed from wishlist");

                return { success: true };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to remove from wishlist";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Move item to different category
         */
        async moveItem(itemId, newCategoryId) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post(
                    `/wishlist-items/${itemId}/move`,
                    {
                        wishlist_category_id: newCategoryId,
                    },
                );

                // Update item in state
                const itemIndex = this.items.findIndex(
                    (item) => item.id === itemId,
                );
                if (itemIndex !== -1) {
                    this.items[itemIndex] = response.data.data;
                }

                const toast = useToast();
                toast.success("Item moved successfully");

                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to move item";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Check if product is in wishlist
         */
        async checkProductInWishlist(productId) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get("/wishlist-items/check", {
                    params: { product_id: productId },
                });

                return {
                    success: true,
                    inWishlist: response.data.data.in_wishlist,
                    item: response.data.data.wishlist_item,
                };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to check wishlist status";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Select category for filtering
         */
        selectCategory(categoryId) {
            this.selectedCategory = categoryId
                ? this.getCategoryById(categoryId)
                : null;

            if (categoryId) {
                this.loadItemsByCategory(categoryId);
            } else {
                this.loadItems();
            }
        },

        /**
         * Clear selected category
         */
        clearSelectedCategory() {
            this.selectedCategory = null;
            this.loadItems();
        },

        /**
         * Initialize wishlist (load categories and items)
         */
        async initializeWishlist() {
            const authStore = useAuthStore();

            if (!authStore.isAuthenticated) {
                this.error = "User not authenticated";
                return { success: false, error: this.error };
            }

            try {
                // Load categories first
                await this.loadCategories();

                // Then load items
                await this.loadItems();

                return { success: true };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to initialize wishlist";
                this.handleError(this.error);
                return { success: false, error: this.error };
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
    },
});
