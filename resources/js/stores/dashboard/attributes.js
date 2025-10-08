// resources/js/stores/dashboard/attributes.js
import { defineStore } from "pinia";
import axios from "../../bootstrap";

export const useAttributesStore = defineStore("attributes", {
    state: () => ({
        attributes: [],
        currentAttribute: null,
        loading: false,
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
            type: "",
            is_filterable: "",
            is_variant: "",
        },
    }),

    actions: {
        /**
         * Fetch paginated list of attributes with optional filters.
         */
        async fetchAttributes(page = 1, perPage = 15) {
            this.loading = true;
            this.error = null;

            try {
                const params = {
                    page,
                    per_page: perPage,
                    search: this.filters.search || undefined,
                    type: this.filters.type || undefined,
                    is_filterable:
                        this.filters.is_filterable === "true"
                            ? true
                            : this.filters.is_filterable === "false"
                                ? false
                                : undefined,
                    is_variant:
                        this.filters.is_variant === "true"
                            ? true
                            : this.filters.is_variant === "false"
                                ? false
                                : undefined,
                };

                // Clean undefined params
                Object.keys(params).forEach(
                    (key) => params[key] === undefined && delete params[key],
                );

                const response = await axios.get("/dashboard/attributes", {
                    params,
                });
                this.attributes = response.data.data || [];
                this.pagination = {
                    current_page: response.data.pagination.current_page,
                    per_page: response.data.pagination.per_page,
                    total: response.data.pagination.total,
                    last_page: response.data.pagination.last_page,
                    from: response.data.pagination.from,
                    to: response.data.pagination.to,
                };
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to load attributes";
                console.error(
                    "[Attributes Store] Error fetching attributes:",
                    err,
                );
                throw err;
            } finally {
                this.loading = false;
            }
        },

        /**
         * Fetch a single attribute by ID.
         */
        async fetchAttribute(id) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(`/dashboard/attributes/${id}`);
                this.currentAttribute = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to load attribute";
                console.error(
                    "[Attributes Store] Error fetching attribute:",
                    err,
                );
                throw err;
            } finally {
                this.loading = false;
            }
        },

        /**
         * Create a new attribute.
         */
        async createAttribute(attributeData) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post(
                    "/dashboard/attributes",
                    attributeData,
                );
                this.attributes.unshift(response.data.data);
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to create attribute";
                console.error(
                    "[Attributes Store] Error creating attribute:",
                    err,
                );
                throw err;
            } finally {
                this.loading = false;
            }
        },

        /**
         * Update an existing attribute.
         */
        async updateAttribute(id, attributeData) {
            this.loading = true;
            this.error = null;

            try {
                // 1. FIND THE CURRENT ATTRIBUTE: Look in the state for the attribute
                //    we are about to edit. This gives us the original slug.
                const originalAttribute = this.attributes.find(
                    (attr) => attr.id === id,
                );

                // 2. PREPARE THE DATA: Create a safe copy of the incoming form data.
                const payload = { ...attributeData };

                // 3. THE CRUCIAL CHECK:
                //    This `if` statement checks if the slug from the form (`payload.slug`)
                //    is identical to the one we already have saved (`originalAttribute.slug`).
                //    This condition will be TRUE when you change "Filterable" but NOT the "Name".
                if (originalAttribute && payload.slug === originalAttribute.slug) {
                    // If they are the same, we DELETE the slug from the data we're sending.
                    // Your backend will never even see the 'slug' field and won't trigger the error.
                    delete payload.slug;
                }

                // 4. SEND TO BACKEND: The `payload` is sent. It will only contain a `slug`
                //    if the user has actually typed a new name, generating a new slug.
                const response = await axios.post(
                    `/dashboard/attributes/${id}`,
                    payload,
                );

                const index = this.attributes.findIndex(
                    (attr) => attr.id === id,
                );
                if (index !== -1) {
                    this.attributes[index] = response.data.data;
                }
                this.currentAttribute = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to update attribute";
                console.error(
                    "[Attributes Store] Error updating attribute:",
                    err,
                );
                throw err;
            } finally {
                this.loading = false;
            }
        },

        /**
         * Delete an attribute (soft delete if applicable).
         */
        async deleteAttribute(id) {
            this.loading = true;
            this.error = null;

            try {
                await axios.delete(`/dashboard/attributes/${id}`);
                this.attributes = this.attributes.filter(
                    (attr) => attr.id !== id,
                );
                return true;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to delete attribute";
                console.error(
                    "[Attributes Store] Error deleting attribute:",
                    err,
                );
                throw err;
            } finally {
                this.loading = false;
            }
        },

        /**
         * Apply a filter and refetch attributes.
         */
        setFilter(key, value) {
            this.filters[key] = value;
            this.pagination.current_page = 1;
            this.fetchAttributes(1, this.pagination.per_page);
        },

        /**
         * Clear all filters.
         */
        clearFilters() {
            this.filters = {
                search: "",
                type: "",
                is_filterable: "",
                is_variant: "",
            };
            this.fetchAttributes(1, this.pagination.per_page);
        },

        /**
         * Clear error state.
         */
        clearError() {
            this.error = null;
        },
    },
});
