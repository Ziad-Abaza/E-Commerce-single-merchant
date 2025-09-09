// resources/js/stores/dashboard.js
import { defineStore } from "pinia";
import axios from "../../bootstrap";

export const useDashboardStore = defineStore("dashboard", {
    state: () => ({
        statistics: {
            total_products: 0,
            active_products: 0,
            total_categories: 0,
            active_categories: 0,
            total_orders: 0,
            pending_orders: 0,
            completed_orders: 0,
            total_users: 0,
            total_reviews: 0,
            total_revenue: 0,
        },

        // recent activity
        recentOrders: [],
        recentProducts: [],
        recentReviews: [],

        // analytics data
        salesAnalytics: {
            period: "30_days",
            start_date: "",
            end_date: "",
            sales_data: [],
            top_products: [],
            order_status_distribution: [],
        },
        revenueAnalytics: {
            period: "30_days",
            start_date: "",
            end_date: "",
            revenue_data: [],
            total_revenue: 0,
            average_order_value: 0,
        },

        // updated user analytics
        userAnalytics: {
            user_registration_data: [],
            user_role_distribution: {},
            total_users: 0,
            active_users: 0,
        },

        // state generale
        loading: false,
        error: null,
    }),

    getters: {
        // statistics getters
        completionRate: (state) => {
            if (state.statistics.total_orders === 0) return 0;
            return Math.round(
                (state.statistics.completed_orders /
                    state.statistics.total_orders) *
                    100,
            );
        },

        // top selling products
        topSellingProducts: (state) => {
            return state.salesAnalytics.top_products || [];
        },
    },

    actions: {
        /**
         * upload all dashboard overview data in one request
         */
        async loadDashboardData() {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get("/dashboard/overview");
                const data = response.data.data;

                this.statistics = data.statistics || this.statistics;
                this.recentOrders = data.recent_orders || [];
                this.recentProducts = data.recent_products || [];
                this.recentReviews = data.recent_reviews || [];
                this.salesAnalytics =
                    data.sales_analytics || this.salesAnalytics;
                this.revenueAnalytics =
                    data.revenue_analytics || this.revenueAnalytics;
                this.userAnalytics = data.user_analytics || this.userAnalytics;

                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to load dashboard data";
                console.error("Dashboard Load Error:", error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * update sales or revenue analytics period (e.g., '7_days', '30_days', '90_days')
         */
        async updateAnalyticsPeriod(period) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get("/dashboard/overview", {
                    params: { period },
                });
                const data = response.data.data;

                this.salesAnalytics =
                    data.sales_analytics || this.salesAnalytics;
                this.revenueAnalytics =
                    data.revenue_analytics || this.revenueAnalytics;

                return { success: true };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to update period";
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        /**
         * delete error message
         */
        clearError() {
            this.error = null;
        },
    },
});
