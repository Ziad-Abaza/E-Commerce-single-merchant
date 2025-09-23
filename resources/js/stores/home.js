// src/stores/home.js
import { defineStore } from "pinia";
import axios from "../bootstrap";

export const useHomeStore = defineStore("home", {
    state: () => ({
        // data home page
        featuredProducts: [],
        latestProducts: [],
        categories: [],
        statistics: {
            total_products: 0,
            total_categories: 0,
            total_orders: 0,
            total_customers: 0,
            featured_products_count: 0,
            latest_products_count: 0,
        },

        // state generale
        loading: false,
        error: null,

        // search results
        searchResults: [],
        pagination: {
            currentPage: 1,
            lastPage: 1,
            perPage: 12,
            total: 0,
        },

        // search state
        isSearching: false,
        searchQuery: "",
        searchFilters: {
            category_id: null,
            min_price: null,
            max_price: null,
            sort: "created_at",
        },
    }),

    getters: {
        // Getters to access state properties
        getCategoryById: (state) => (id) => {
            return state.categories.find((category) => category.id === id);
        },

        getFeaturedProductById: (state) => (id) => {
            return state.featuredProducts.find((product) => product.id === id);
        },

        getLatestProductById: (state) => (id) => {
            return state.latestProducts.find((product) => product.id === id);
        },
    },

    actions: {
        /**
         *  fetch all home page data (featured, latest, categories, statistics) in one request
         */
        async loadHomeData() {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get("/public/home");
                const data = response.data.data;

                this.featuredProducts = data.featured_products || [];
                this.latestProducts = data.latest_products || [];
                this.categories = data.categories || [];
                this.statistics = data.statistics || {};

                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "field to load home data";
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * search products on home page
         */
        async searchHome(query, filters = {}) {
            this.isSearching = true;
            this.error = null;
            this.searchQuery = query;

            try {
                const params = {
                    search: query,
                    ...filters,
                };

                const response = await axios.get("/public/home", { params });
                const data = response.data.data;

                this.searchResults = data.search_results || [];
                this.pagination = data.pagination || {
                    currentPage: 1,
                    lastPage: 1,
                    perPage: 12,
                    total: 0,
                };

                return { success: true, data: response.data };
            } catch (error) {
                this.error = error.response?.data?.message || "Search failed";
                return { success: false, error: this.error };
            } finally {
                this.isSearching = false;
            }
        },

        /**
         * filter products by category on home page
         */
        async filterByCategory(categoryId, filters = {}) {
            this.loading = true;
            this.error = null;

            try {
                const params = {
                    category_id: categoryId,
                    ...filters,
                };

                const response = await axios.get("/public/home", { params });
                const data = response.data.data;

                this.featuredProducts = data.featured_products || [];
                this.latestProducts = data.latestProducts || [];
                this.categories = data.categories || [];
                this.statistics = data.statistics || {};

                if (data.category_products) {
                    this.latestProducts = data.category_products;
                    this.pagination = data.pagination || this.pagination;
                }

                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to filter by category";
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * update search filters
         */
        updateFilters(filters) {
            this.searchFilters = { ...this.searchFilters, ...filters };
        },

        /**
         * clear error messages
         */
        clearError() {
            this.error = null;
        },

        /**
         * reset search state
         */
        resetSearch() {
            this.searchResults = [];
            this.searchQuery = "";
            this.isSearching = false;
            this.pagination = {
                currentPage: 1,
                lastPage: 1,
                perPage: 12,
                total: 0,
            };
        },
    },
});
