// resources/js/stores/dashboard/promoCodes.js
import { defineStore } from "pinia";
import axios from "../../bootstrap";

export const usePromoCodesStore = defineStore("promoCodes", {
    state: () => ({
        promoCodes: [],
        trashedPromoCodes: [],
        products: [],
        categories: [],
        currentPromoCode: null,
        stats: {
            total_promos: 0,
            active_promos: 0,
            expired_promos: 0,
            upcoming_promos: 0,
        },
        loading: false,
        saving: false,
        deleting: false,
        error: null,
        pagination: {
            current_page: 1,
            per_page: 15,
            total: 0,
            last_page: 1,
            from: 1,
            to: 1,
        },
        trashedPagination: {
            current_page: 1,
            per_page: 15,
            total: 0,
            last_page: 1,
            from: 1,
            to: 1,
        },
        filters: {
            search: "",
            status: "",
            discount_type: "",
            target_type: "",
            start_date: "",
            end_date: "",
            sort_by: "created_at",
            sort_order: "desc",
        },
        availableFilters: {
            target_types: [],
            discount_types: ["fixed", "percentage"],
            status_options: ["active", "inactive"],
        },
        targetTypes: [],
    }),

    getters: {
        activePromoCodes: (state) =>
            state.promoCodes.filter((p) => p.is_active),
        getPromoCodeById: (state) => (id) =>
            state.promoCodes.find((p) => p.id === id) || null,
    },

    actions: {
        // ============= FETCH =============
        async fetchPromoCodes(page = 1, perPage = null) {
            this.loading = true;
            this.error = null;
            try {
                const params = {
                    page,
                    per_page: perPage || this.pagination.per_page,
                    search: this.filters.search || undefined,
                    status: this.filters.status || undefined,
                    discount_type: this.filters.discount_type || undefined,
                    target_type: this.filters.target_type || undefined,
                    start_date: this.filters.start_date || undefined,
                    end_date: this.filters.end_date || undefined,
                    sort_by: this.filters.sort_by,
                    sort_order: this.filters.sort_order,
                };

                // Remove empty/undefined params
                Object.keys(params).forEach(
                    (key) =>
                        (params[key] === "" ||
                            params[key] === null ||
                            params[key] === undefined) &&
                        delete params[key],
                );

                const response = await axios.get("/dashboard/promo-codes", {
                    params,
                });

                this.promoCodes = response.data.data || [];
                this.pagination = response.data.pagination || this.pagination;
                this.availableFilters =
                    response.data.available_filters || this.availableFilters;
                this.targetTypes = response.data.target_types;

                return { success: true, data: response.data.data };
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to fetch promo codes";
                console.error("[PromoCodes Store] fetch error:", err);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async fetchPromoCode(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get(
                    `/dashboard/promo-codes/${id}`,
                );
                this.currentPromoCode = response.data.data;
                return { success: true, data: response.data.data };
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to fetch promo code details";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        // ============= CREATE / UPDATE =============
        async createPromoCode(promoCodeData) {
            this.saving = true;
            this.error = null;
            try {
                const response = await axios.post(
                    "/dashboard/promo-codes",
                    promoCodeData,
                );
                this.promoCodes.unshift(response.data.data);
                return { success: true, data: response.data.data };
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to create promo code";
                return {
                    success: false,
                    error: this.error,
                    errors: err.response?.data?.errors,
                };
            } finally {
                this.saving = false;
            }
        },

        async updatePromoCode(id, promoCodeData) {
            this.saving = true;
            this.error = null;
            try {
                const response = await axios.post(
                    `/dashboard/promo-codes/${id}`,
                    { ...promoCodeData, _method: "POST" },
                );
                const index = this.promoCodes.findIndex((p) => p.id === id);
                if (index !== -1) {
                    this.promoCodes.splice(index, 1, response.data.data);
                }
                this.currentPromoCode = response.data.data;
                return { success: true, data: response.data.data };
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to update promo code";
                return {
                    success: false,
                    error: this.error,
                    errors: err.response?.data?.errors,
                };
            } finally {
                this.saving = false;
            }
        },

        // ============= DELETE / TRASH =============
        async deletePromoCode(id) {
            this.deleting = true;
            this.error = null;
            try {
                await axios.delete(`/dashboard/promo-codes/${id}`);
                this.promoCodes = this.promoCodes.filter((p) => p.id !== id);
                return { success: true };
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to delete promo code";
                return { success: false, error: this.error };
            } finally {
                this.deleting = false;
            }
        },

        // ============= TOGGLE STATUS =============
        async toggleStatus(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post(
                    `/dashboard/promo-codes/${id}/toggle-status`,
                );
                const index = this.promoCodes.findIndex((p) => p.id === id);
                if (index !== -1) {
                    this.promoCodes[index].is_active =
                        response.data.data.is_active;
                }
                return { success: true, data: response.data.data };
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to toggle promo code status";
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        // ============= FETCH PRODUCTS & CATEGORIES =============
        async fetchProductsAndCategories() {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get(
                    "/dashboard/promo-codes/related-data",
                );
                this.products = response.data.data.products || [];
                this.categories = response.data.data.categories || [];
                return { success: true, data: response.data };
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to fetch products and categories";
                console.error(
                    "[PromoCodes Store] fetchProductsAndCategories error:",
                    err,
                );
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },
        
        // ============= FILTERS =============
        setFilter(key, value) {
            this.filters[key] = value;
            this.pagination.current_page = 1;
            return this.fetchPromoCodes(1, this.pagination.per_page);
        },

        clearFilters() {
            this.filters = {
                search: "",
                status: "",
                discount_type: "",
                target_type: "",
                start_date: "",
                end_date: "",
                sort_by: "created_at",
                sort_order: "desc",
            };
            return this.fetchPromoCodes(1, this.pagination.per_page);
        },

        clearError() {
            this.error = null;
        },

        // ============= STATS =============
        async fetchStats() {
            try {
                const response = await axios.get(
                    "/dashboard/promo-codes/stats",
                );
                this.stats = response.data.data || this.stats;
                return { success: true, data: this.stats };
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to fetch promo code stats";
                console.error("[PromoCodes Store] fetchStats error:", err);
                return { success: false, error: this.error };
            }
        },
    },
});
