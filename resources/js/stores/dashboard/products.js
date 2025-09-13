import { defineStore } from "pinia";
import axios from "../../bootstrap";

export const useProductsStore = defineStore("dashboardProducts", {
    state: () => ({
        products: [],
        trashedProducts: [],
        categories: [],
        currentProduct: null,
        loading: false,
        deleting: false,
        restoring: false,
        forcing: false,
        error: null,
        pagination: {
            current_page: 1,
            per_page: 15,
            total: 0,
            last_page: 1,
            from: 0,
            to: 0,
        },
        trashedPagination: {
            current_page: 1,
            per_page: 15,
            total: 0,
            last_page: 1,
            from: 0,
            to: 0,
        },
        filters: {
            search: "",
            category: "",
            status: "",
        },
        statistics: {
            total_products: 0,
            active_products: 0,
            inactive_products: 0,
            low_stock: 0,
        },
        loadingCategories: false,
    }),

    actions: {
        async fetchProducts(page = 1, perPage = 15) {
            this.loading = true;
            this.error = null;

            try {
                const params = {
                    page,
                    per_page: perPage,
                    search: this.filters.search || undefined,
                    category_id: this.filters.category || undefined,
                    status: this.filters.status || undefined,
                };
                Object.keys(params).forEach(
                    (key) => params[key] === undefined && delete params[key],
                );

                const response = await axios.get("/dashboard/products", {
                    params,
                });
                this.products = response.data.data || [];
                this.categories = response.data.categories || [];
                this.pagination = response.data.pagination || this.pagination;
                this.statistics = {
                    total_products: this.pagination.total,
                    active_products: this.products.filter((p) => p.is_active)
                        .length,
                    inactive_products: this.products.filter((p) => !p.is_active)
                        .length,
                    low_stock: this.products.filter((p) => p.stock_quantity < 5)
                        .length,
                };
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to load products";
            } finally {
                this.loading = false;
            }
        },

        async fetchProduct(id) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(`/dashboard/products/${id}`);
                this.currentProduct = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to load product";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async createProduct(productData) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post(
                    "/dashboard/products",
                    productData,
                );
                this.products.unshift(response.data.data);
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to create product";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async updateProduct(id, productData) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post(
                    `/dashboard/products/${id}`,
                    productData,
                );
                const index = this.products.findIndex((p) => p.id === id);
                if (index !== -1) this.products[index] = response.data.data;
                this.currentProduct = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to update product";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async deleteProduct(id, confirm = false) {
            this.deleting = true;
            this.error = null;

            try {
                await axios.delete(`/dashboard/products/${id}`, {
                    params: { confirm },
                });
                this.products = this.products.filter((p) => p.id !== id);
                this.fetchProducts(
                    this.pagination.current_page,
                    this.pagination.per_page,
                );
                return true;
            } catch (err) {
                if (err.response?.data?.requires_confirmation)
                    throw { requiresConfirmation: true, id };
                this.error =
                    err.response?.data?.message || "Failed to delete product";
                throw err;
            } finally {
                this.deleting = false;
            }
        },

        async fetchTrashedProducts(page = 1, perPage = 15) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get("/dashboard/products/trash", {
                    params: { page, per_page: perPage },
                });
                this.trashedProducts = response.data.data || [];
                this.trashedPagination =
                    response.data.pagination || this.trashedPagination;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to load trashed products";
            } finally {
                this.loading = false;
            }
        },

        async restoreProduct(id) {
            this.restoring = true;
            this.error = null;

            try {
                const response = await axios.post(
                    `/dashboard/products/${id}/restore`,
                );
                this.trashedProducts = this.trashedProducts.filter(
                    (p) => p.id !== id,
                );
                this.products.unshift(response.data.data);
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to restore product";
                throw err;
            } finally {
                this.restoring = false;
            }
        },

        async forceDeleteProduct(id) {
            this.forcing = true;
            this.error = null;

            try {
                await axios.delete(`/dashboard/products/${id}/force-delete`);
                this.trashedProducts = this.trashedProducts.filter(
                    (p) => p.id !== id,
                );
                return true;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to permanently delete product";
                throw err;
            } finally {
                this.forcing = false;
            }
        },

        setFilter(key, value) {
            this.filters[key] = value;
            this.pagination.current_page = 1;
            this.fetchProducts(1, this.pagination.per_page);
        },

        clearFilters() {
            this.filters = { search: "", category: "", status: "" };
            this.fetchProducts(1, this.pagination.per_page);
        },

        clearError() {
            this.error = null;
        },
    },
});
