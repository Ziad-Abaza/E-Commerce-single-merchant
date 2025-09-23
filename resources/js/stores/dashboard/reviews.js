// resources/js/stores/dashboard/reviews.js
import { defineStore } from "pinia";
import axios from "../../bootstrap";

export const useReviewsStore = defineStore("dashboardReviews", {
    state: () => ({
        reviews: [],
        loading: false,
        deleting: false,
        bulkDeleting: false,
        error: null,
        pagination: {
            current_page: 1,
            per_page: 15,
            total: 0,
            last_page: 1,
        },
        filters: {
            product_id: "",
            user_id: "",
            rating: "",
            status: "",
            search: "",
        },
        statistics: {
            total_reviews: 0,
            pending_reviews: 0,
            average_rating: 0,
            rating_distribution: [],
        },
        selectedReviews: [],
    }),

    actions: {
        async fetchReviews(page = 1, perPage = 15) {
            this.loading = true;
            this.error = null;

            try {
                const params = {
                    page: page,
                    per_page: perPage,
                    product_id: this.filters.product_id || undefined,
                    user_id: this.filters.user_id || undefined,
                    rating: this.filters.rating || undefined,
                    status: this.filters.status || undefined,
                    search: this.filters.search || undefined,
                };

                // Remove undefined values to keep URL clean
                Object.keys(params).forEach(
                    (key) => params[key] === undefined && delete params[key],
                );

                const response = await axios.get("/dashboard/reviews", {
                    params,
                });

                this.reviews = response.data.data || [];
                this.pagination = response.data.pagination || {
                    current_page: 1,
                    per_page: 15,
                    total: 0,
                    last_page: 1,
                };
                this.statistics = response.data.statistics || {
                    total_reviews: 0,
                    pending_reviews: 0,
                    average_rating: 0,
                    rating_distribution: [],
                };

                return this.reviews;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to load reviews";
                console.error("[Reviews Store] Error fetching reviews:", err);
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async deleteReview(id) {
            this.deleting = true;
            this.error = null;

            try {
                await axios.delete(`/dashboard/reviews/${id}`);

                // Remove from list
                this.reviews = this.reviews.filter((r) => r.id !== id);

                // Update statistics
                await this.fetchReviews(
                    this.pagination.current_page,
                    this.pagination.per_page,
                );

                return true;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to delete review";
                console.error("[Reviews Store] Error deleting review:", err);
                throw err;
            } finally {
                this.deleting = false;
            }
        },

        async toggleReviewActive(id) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post(`/dashboard/reviews/${id}/toggle-active`);

                // Update the review in the list
                const index = this.reviews.findIndex(r => r.id === id);
                if (index !== -1) {
                    this.reviews[index] = response.data.data;
                }

                return response.data;
            } catch (err) {
                this.error = err.response?.data?.message || "Failed to toggle review status";
                console.error("[Reviews Store] Error toggling review status:", err);
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async bulkActivateReviews(reviewIds) {
            this.bulkDeleting = true;
            this.error = null;

            try {
                const response = await axios.post(
                    "/dashboard/reviews/bulk-activate",
                    {
                        review_ids: reviewIds,
                    },
                );

                // Update reviews in list
                this.reviews = this.reviews.map(review => {
                    if (reviewIds.includes(review.id)) {
                        return { ...review, active: true };
                    }
                    return review;
                });

                // Clear selection
                this.selectedReviews = [];

                // Update statistics
                await this.fetchReviews(
                    this.pagination.current_page,
                    this.pagination.per_page,
                );

                return response.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to bulk activate reviews";
                console.error(
                    "[Reviews Store] Error bulk activating reviews:",
                    err,
                );
                throw err;
            } finally {
                this.bulkDeleting = false;
            }
        },

        async bulkDeactivateReviews(reviewIds) {
            this.bulkDeleting = true;
            this.error = null;

            try {
                const response = await axios.post(
                    "/dashboard/reviews/bulk-deactivate",
                    {
                        review_ids: reviewIds,
                    },
                );

                // Update reviews in list
                this.reviews = this.reviews.map(review => {
                    if (reviewIds.includes(review.id)) {
                        return { ...review, active: false };
                    }
                    return review;
                });

                // Clear selection
                this.selectedReviews = [];

                // Update statistics
                await this.fetchReviews(
                    this.pagination.current_page,
                    this.pagination.per_page,
                );

                return response.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to bulk deactivate reviews";
                console.error(
                    "[Reviews Store] Error bulk deactivating reviews:",
                    err,
                );
                throw err;
            } finally {
                this.bulkDeleting = false;
            }
        },

        async bulkDeleteReviews(reviewIds) {
            this.bulkDeleting = true;
            this.error = null;

            try {
                const response = await axios.post(
                    "/dashboard/reviews/bulk-delete",
                    {
                        review_ids: reviewIds,
                    },
                );

                // Remove deleted reviews from list
                this.reviews = this.reviews.filter(
                    (r) => !reviewIds.includes(r.id),
                );

                // Clear selection
                this.selectedReviews = [];

                // Update statistics
                await this.fetchReviews(
                    this.pagination.current_page,
                    this.pagination.per_page,
                );

                return response.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to bulk delete reviews";
                console.error(
                    "[Reviews Store] Error bulk deleting reviews:",
                    err,
                );
                throw err;
            } finally {
                this.bulkDeleting = false;
            }
        },

        setFilter(key, value) {
            this.filters[key] = value;
            this.pagination.current_page = 1;
            this.fetchReviews(1, this.pagination.per_page);
        },

        clearFilters() {
            this.filters = {
                product_id: "",
                user_id: "",
                rating: "",
                status: "",
                search: "",
            };
            this.fetchReviews(1, this.pagination.per_page);
        },

        toggleReviewSelection(reviewId) {
            const index = this.selectedReviews.indexOf(reviewId);
            if (index === -1) {
                this.selectedReviews.push(reviewId);
            } else {
                this.selectedReviews.splice(index, 1);
            }
        },

        selectAllReviews() {
            this.selectedReviews = this.reviews.map((r) => r.id);
        },

        clearSelection() {
            this.selectedReviews = [];
        },

        clearError() {
            this.error = null;
        },
    },
});
