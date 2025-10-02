// src/stores/products.js
import { defineStore } from "pinia";
import axios from "../bootstrap";

export const useProductStore = defineStore("products", {
    state: () => ({
        // Core product data
        products: [],
        featuredProducts: [],
        latestProducts: [],
        currentProduct: null,
        categories: [],

        // Search & filtering
        searchResults: [],
        filters: {
            category: null,
            minPrice: null,
            maxPrice: null,
            sort: "created_at",
            search: "",
            type: "all", // 'all', 'featured', 'latest', 'category', 'search'
        },

        // Pagination state
        pagination: {
            currentPage: 1,
            lastPage: 1,
            perPage: 12,
            total: 0,
        },

        // UI state
        loading: false,
        error: null,
    }),

    getters: {
        /**
         * Get product by ID from main products list
         */
        getProductById: (state) => (id) => {
            return state.products.find((product) => product.id === id);
        },

        /**
         * Get category by ID
         */
        getCategoryById: (state) => (id) => {
            return state.categories.find((category) => category.id === id);
        },

        /**
         * Get featured product by ID
         */
        getFeaturedProductById: (state) => (id) => {
            return state.featuredProducts.find((product) => product.id === id);
        },

        /**
         * Get latest product by ID
         */
        getLatestProductById: (state) => (id) => {
            return state.latestProducts.find((product) => product.id === id);
        },
    },

    actions: {
        /**
         * Load products based on type and filters
         * @param {Object} params - Additional query parameters
         * @returns {Object} - API response
         */
        async loadProducts(params = {}) {
            this.loading = true;
            this.error = null;

            try {
                const queryParams = {};

                if (Object.keys(params).length > 0) {
                    Object.assign(queryParams, params);
                }

                if (this.filters.type && this.filters.type !== "all") {
                    queryParams.type = this.filters.type;
                }
                if (this.filters.category) {
                    queryParams.category_id = this.filters.category;
                }
                if (this.filters.minPrice) {
                    queryParams.min_price = this.filters.minPrice;
                }
                if (this.filters.maxPrice) {
                    queryParams.max_price = this.filters.maxPrice;
                }
                if (this.filters.search) {
                    queryParams.search = this.filters.search;
                }
                if (this.filters.sort && this.filters.sort !== "created_at") {
                    queryParams.sort = this.filters.sort;
                }
                if (this.pagination.currentPage !== 1) {
                    queryParams.page = this.pagination.currentPage;
                }
                if (this.pagination.perPage !== 12) {
                    queryParams.per_page = this.pagination.perPage;
                }

                const response = await axios.get("/public/products", {
                    params:
                        Object.keys(queryParams).length > 0
                            ? queryParams
                            : undefined,
                });

                // Update state based on product type
                switch (this.filters.type) {
                    case "featured":
                        this.featuredProducts = response.data.data;
                        break;
                    case "latest":
                        this.latestProducts = response.data.data;
                        break;
                    case "search":
                        this.searchResults = response.data.data;
                        break;
                    default:
                        this.products = response.data.data;
                        break;
                }

                // Always update pagination
                this.pagination = {
                    currentPage: response.data.pagination.current_page,
                    lastPage: response.data.pagination.last_page,
                    perPage: response.data.pagination.per_page,
                    total: response.data.pagination.total,
                };

                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to load products";
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },
        /**
         * Load featured products
         * @returns {Object} - API response
         */
        async loadFeaturedProducts() {
            this.filters.type = "featured";
            return await this.loadProducts({ per_page: 8 });
        },

        /**
         * Load latest products
         * @returns {Object} - API response
         */
        async loadLatestProducts() {
            this.filters.type = "latest";
            return await this.loadProducts({ per_page: 12 });
        },

        /**
         * Load categories (separate endpoint, unchanged)
         * @returns {Object} - API response
         */
        async loadCategories() {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get("/public/categories");
                this.categories = response.data.data;
                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to load categories";
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Load single product by ID
         * @param {number} id - Product ID
         * @returns {Object} - API response
         */
        async getProduct(id) {
            this.loading = true;
            this.error = null;

            try {
                // Request product — no need for "include" parameter anymore
                const response = await axios.get(`/public/products/${id}`);
                // Extract product data
                const productData = response.data.data.product;

                // Attach related products (always included by backend)
                productData.related_products =
                    response.data.data.related_products || [];

                // Set current product
                this.currentProduct = productData;

                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to load product";
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },
        /**
         * Search products
         * @param {string} query - Search query
         * @param {Object} filters - Additional filters
         * @returns {Object} - API response
         */
        async searchProducts(query, filters = {}) {
            this.filters.type = "search";
            this.filters.search = query;
            this.filters = { ...this.filters, ...filters };
            return await this.loadProducts();
        },

        /**
         * Load products by category
         * @param {number} categoryId - Category ID
         * @param {Object} params - Additional parameters
         * @returns {Object} - API response
         */
        async getProductsByCategory(categoryId, params = {}) {
            this.filters.type = "category";
            this.filters.category = categoryId;
            return await this.loadProducts(params);
        },

        /**
         * Update filters
         * @param {Object} filters - New filter values
         */
        setFilters(filters) {
            this.filters = { ...this.filters, ...filters };
        },

        /**
         * Clear all filters
         */
        clearFilters() {
            this.filters = {
                category: null,
                minPrice: null,
                maxPrice: null,
                sort: "created_at",
                search: "",
                type: "all",
            };
        },

        /**
         * Clear error state
         */
        clearError() {
            this.error = null;
        },

        /**
         * Reset pagination to first page
         */
        resetPagination() {
            this.pagination.currentPage = 1;
        },
    },
});
