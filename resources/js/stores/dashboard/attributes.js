import { defineStore } from "pinia";
import axios from "../../bootstrap"; // Assuming bootstrap.js sets up axios

export const useAttributeStore = defineStore("dashboardAttributes", {
    state: () => ({
        attributes: [],
        currentAttribute: null,
        loading: false,
        deleting: false,
        error: null,
        pagination: {
            current_page: 1,
            per_page: 15,
            total: 0,
            last_page: 1,
            from: 0,
            to: 0,
        },
        filters: {
            search: "",
        },
    }),

    actions: {
        async fetchAttributes(page = 1, perPage = 15) {
            this.loading = true;
            this.error = null;
            try {
                const params = {
                    page,
                    per_page: perPage,
                    search: this.filters.search || undefined,
                };
                Object.keys(params).forEach(
                    (key) => params[key] === undefined && delete params[key]
                );

                const response = await axios.get("/dashboard/attributes", { params });
                this.attributes = response.data.data || [];
                const apiPagination = response.data.pagination;
                this.pagination = {
                    current_page: apiPagination.current_page,
                    per_page: apiPagination.per_page,
                    total: apiPagination.total,
                    last_page: apiPagination.last_page,
                    from: apiPagination.from,
                    to: apiPagination.to,
                };

            } catch (err) {
                this.error = err.response?.data?.message || "Failed to load attributes";
            } finally {
                this.loading = false;
            }
        },

        async fetchAttribute(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get(`/dashboard/attributes/${id}`);
                this.currentAttribute = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error = err.response?.data?.message || "Failed to load attribute details";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async createAttribute(attributeData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post("/dashboard/attributes", attributeData);
                this.attributes.unshift(response.data.data);
                return response.data.data;
            } catch (err) {
                this.error = err.response?.data?.message || "Failed to create attribute";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async updateAttribute(id, attributeData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post(`/dashboard/attributes/${id}`, attributeData);
                const index = this.attributes.findIndex((a) => a.id === id);
                if (index !== -1) this.attributes[index] = response.data.data;
                this.currentAttribute = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error = err.response?.data?.message || "Failed to update attribute";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async deleteAttribute(id) {
            this.deleting = true;
            this.error = null;
            try {
                await axios.delete(`/dashboard/attributes/${id}`);
                this.attributes = this.attributes.filter((a) => a.id !== id);
                return true;
            } catch (err) {
                this.error = err.response?.data?.message || "Failed to delete attribute";
                throw err;
            } finally {
                this.deleting = false;
            }
        },

        setFilter(key, value) {
            this.filters[key] = value;
            this.pagination.current_page = 1;
            this.fetchAttributes(1, this.pagination.per_page);
        },

        clearFilters() {
            this.filters = { search: "" };
            this.fetchAttributes(1, this.pagination.per_page);
        },

        clearError() {
            this.error = null;
        },
    },
});

