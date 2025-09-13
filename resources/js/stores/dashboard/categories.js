import { defineStore } from "pinia";
import axios from "../../bootstrap";

export const useCategoriesStore = defineStore("dashboardCategories", {
    state: () => ({
        categories: [],
        currentCategory: null,
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
            parent_id: "",
            status: "",
        },
        statistics: {
            total: 0,
            active: 0,
            inactive: 0,
            root: 0,
        },
    }),

    actions: {
        async fetchCategories(page = 1, perPage = 10) {
            this.loading = true;
            this.error = null;

            try {
                const params = {
                    page,
                    per_page: perPage,
                    search: this.filters.search || undefined,
                    parent_id: this.filters.parent_id || undefined,
                    status: this.filters.status || undefined,
                };
                Object.keys(params).forEach(
                    (key) => params[key] === undefined && delete params[key],
                );

                const response = await axios.get("/dashboard/categories", {
                    params,
                });

                this.categories = response.data.data || [];
                this.pagination = response.data.pagination || this.pagination;
                this.statistics = response.data.stats || this.statistics;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to load categories";
            } finally {
                this.loading = false;
            }
        },

        async fetchCategory(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get(`/dashboard/categories/${id}`);
                this.currentCategory = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to load category details";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async createCategory(categoryData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post(
                    "/dashboard/categories",
                    categoryData,
                );
                this.categories.unshift(response.data.data);
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to create category";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async updateCategory(id, categoryData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post(
                    `/dashboard/categories/${id}`,
                    categoryData,
                );
                const index = this.categories.findIndex((c) => c.id === id);
                if (index !== -1) this.categories[index] = response.data.data;
                this.currentCategory = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to update category";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async deleteCategory(id) {
            this.deleting = true;
            this.error = null;
            try {
                await axios.delete(`/dashboard/categories/${id}`);
                this.categories = this.categories.filter((c) => c.id !== id);
                return true;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to delete category";
                throw err;
            } finally {
                this.deleting = false;
            }
        },

        setFilter(key, value) {
            this.filters[key] = value;
            this.pagination.current_page = 1;
            this.fetchCategories(1, this.pagination.per_page);
        },

        clearFilters() {
            this.filters = { search: "", parent_id: "", status: "" };
            this.fetchCategories(1, this.pagination.per_page);
        },

        clearError() {
            this.error = null;
        },
    },
});
