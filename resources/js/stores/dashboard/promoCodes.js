import { defineStore } from 'pinia';
import axios from 'axios';

export const usePromoCodesStore = defineStore('promoCodes', {
    state: () => ({
        productPromoCodes: [],
        loading: false,
        error: null,
    }),

    actions: {
        async fetchPromoCodesForProduct(productId) {
            this.loading = true;
            this.error = null;
            try {
                // CORRECTED URL: Removed /api prefix
                const response = await axios.get(`/dashboard/promo-codes?product_id=${productId}`);
                this.productPromoCodes = response.data.data.data;
            } catch (error) {
                console.error('Error fetching promo codes:', error);
                this.error = 'Failed to fetch promo codes for the product.';
            } finally {
                this.loading = false;
            }
        },

        async createPromoCode(promoCodeData) {
            this.loading = true;
            this.error = null;
            try {
                if (!promoCodeData.product_id) {
                    throw new Error('Product ID is required to create a promo code.');
                }
                // CORRECTED URL: Removed /api prefix
                await axios.post('/dashboard/promo-codes', promoCodeData);
                await this.fetchPromoCodesForProduct(promoCodeData.product_id);
            } catch (error) {
                console.error('Error creating promo code:', error);
                this.error = error.response?.data?.message || 'Failed to create promo code.';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updatePromoCode(id, promoCodeData) {
            this.loading = true;
            this.error = null;
            try {
                // CORRECTED URL: Removed /api prefix
                await axios.post(`/dashboard/promo-codes/${id}`, promoCodeData);
                if (promoCodeData.product_id) {
                    await this.fetchPromoCodesForProduct(promoCodeData.product_id);
                }
            } catch (error) {
                console.error('Error updating promo code:', error);
                this.error = error.response?.data?.message || 'Failed to update promo code.';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deletePromoCode(promoCodeId, productId) {
            this.loading = true;
            this.error = null;
            try {
                // CORRECTED URL: Removed /api prefix
                await axios.delete(`/dashboard/promo-codes/${promoCodeId}`);
                if (productId) {
                    await this.fetchPromoCodesForProduct(productId);
                }
            } catch (error) {
                console.error('Error deleting promo code:', error);
                this.error = 'Failed to delete promo code.';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        clearError() {
            this.error = null;
        },
    },
});