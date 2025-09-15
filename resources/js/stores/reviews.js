// src/stores/reviews.js
import { defineStore } from "pinia";
import axios from "../bootstrap";
import { useAuthStore } from "./auth";
import { useToast } from "vue-toastification";

export const useReviewStore = defineStore("reviews", {
    state: () => ({
        reviews: [],
        loading: false,
        error: null,
        pagination: {
            currentPage: 1,
            lastPage: 1,
            perPage: 5,
            total: 0,
        },
    }),

    getters: {
        /**
         * Get review by ID
         */
        getReviewById: (state) => (id) => {
            return state.reviews.find((review) => review.id === id);
        },
    },

    actions: {
        /**
         * Load reviews for a specific product with pagination
         */
        async loadReviews(productId, page = 1) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(`/reviews/${productId}`, {
                    params: { page, per_page: this.pagination.perPage },
                });

                this.reviews = response.data.data || [];
                this.pagination = {
                    currentPage: response.data.pagination?.current_page || 1,
                    lastPage: response.data.pagination?.last_page || 1,
                    perPage: response.data.pagination?.per_page || 5,
                    total: response.data.pagination?.total || 0,
                };

                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to load reviews";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Create a new review for a specific product
         */
        async createReview(productId, reviewData) {
            this.loading = true;
            this.error = null;

            try {
                const authStore = useAuthStore();
                if (!authStore.isAuthenticated) {
                    throw new Error(
                        "User must be authenticated to create a review",
                    );
                }

                const data = {
                    ...reviewData,
                    user_id: authStore.user.id,
                    is_verified_purchase: authStore.isVerified,
                };

                const response = await axios.post(
                    `/reviews/${productId}`,
                    data,
                );

                const toast = useToast();
                toast.success("Review submitted successfully!");

                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to create review";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Update an existing review
         */
        async updateReview(reviewId, reviewData) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post(
                    `/reviews/${reviewId}`,
                    reviewData,
                );
                // Find and update the review in state
                const index = this.reviews.findIndex((r) => r.id === reviewId);
                if (index !== -1) {
                    this.reviews[index] = response.data.data;
                }
                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to update review";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Delete a review
         */
        async deleteReview(reviewId) {
            this.loading = true;
            this.error = null;

            try {
                await axios.delete(`/reviews/${reviewId}`);
                // Remove from state
                this.reviews = this.reviews.filter((r) => r.id !== reviewId);
                return { success: true };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to delete review";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Get product rating statistics
         */
        async getProductRatingStats(productId) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(`/reviews/${productId}/stats`);
                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to get rating statistics";
                this.handleError(this.error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
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
