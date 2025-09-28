// resources/js/stores/dashboard/productAttributes.js
import { defineStore } from "pinia";
import axios from "../bootstrap";

export const useProductAttributesStore = defineStore("dashboardProductAttributes", {
    state: () => ({
        attributes: [],
        categoryAttributes: [],
        variantAttributes: [],
        loading: false,
        error: null,
    }),

    actions: {
        /**
         * Fetch attributes for a specific product variant
         * @param {number} productDetailId - The ID of the product detail/variant
         * @returns {Promise<Array>} - The list of attributes
         */
        async fetchAttributes(productDetailId) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.get(`/product-attributes/${productDetailId}`);
                this.attributes = response.data.data || [];
                return this.attributes;
            } catch (error) {
                this.error = error.response?.data?.message || error.message || 'Failed to fetch attributes';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        /**
         * Update or create attribute values for a product variant
         * @param {number} productDetailId - The ID of the product detail/variant
         * @param {Array} attributes - Array of attributes with values to update
         * @returns {Promise<Object>} - The updated product detail
         */
        async updateAttributes(productDetailId, attributes) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post(`/product-attributes/${productDetailId}`, {
                    attributes: attributes
                });
                
                // Update the local state with the updated attributes
                this.attributes = response.data.data || [];
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || error.message || 'Failed to update attributes';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        /**
         * Remove an attribute value from a product variant
         * @param {number} productDetailId - The ID of the product detail/variant
         * @param {number} attributeId - The ID of the attribute to remove
         * @returns {Promise<Object>} - The response data
         */
        async removeAttribute(productDetailId, attributeId) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.delete(`/product-attributes/${productDetailId}/attribute/${attributeId}`);
                // Remove the attribute from the local state
                this.attributes = this.attributes.filter(attr => attr.id !== attributeId);
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || error.message || 'Failed to remove attribute';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        /**
         * Get attributes for a product's categories
         * @param {number} productDetailId - The ID of the product detail/variant
         * @returns {Promise<Array>} - The list of category attributes
         */
        async fetchCategoryAttributes(productDetailId) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(`/product-attributes/${productDetailId}/category-attributes`);
                this.categoryAttributes = response.data.data || [];
                return this.categoryAttributes;
            } catch (error) {
                this.error = error.response?.data?.message || error.message || 'Failed to fetch category attributes';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        /**
         * Get variant attributes for a product
         * @param {number} productId - The ID of the product
         * @returns {Promise<Array>} - The list of variant attributes
         */
        async fetchVariantAttributes(productId) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(`/product-attributes/product/${productId}/variant-attributes`);
                this.variantAttributes = response.data.data || [];
                return this.variantAttributes;
            } catch (error) {
                this.error = error.response?.data?.message || error.message || 'Failed to fetch variant attributes';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        /**
         * Clear the current error
         */
        clearError() {
            this.error = null;
        },

        /**
         * Reset the store state
         */
        reset() {
            this.attributes = [];
            this.categoryAttributes = [];
            this.variantAttributes = [];
            this.loading = false;
            this.error = null;
        }
    },

    getters: {
        /**
         * Get the loading state
         * @returns {boolean} - Whether the store is loading
         */
        isLoading: (state) => state.loading,

        /**
         * Get the current error
         * @returns {string|null} - The error message, if any
         */
        getError: (state) => state.error,

        /**
         * Get all attributes for the current product variant
         * @returns {Array} - The list of attributes
         */
        getAttributes: (state) => state.attributes,

        /**
         * Get all category attributes
         * @returns {Array} - The list of category attributes
         */
        getCategoryAttributes: (state) => state.categoryAttributes,

        /**
         * Get all variant attributes
         * @returns {Array} - The list of variant attributes
         */
        getVariantAttributes: (state) => state.variantAttributes,
    },
});
