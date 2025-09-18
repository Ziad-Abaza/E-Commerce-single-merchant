// resources/js/stores/dashboard/overview.js
import { defineStore } from "pinia";
import axios from "../../bootstrap";

export const useDashboardStore = defineStore("dashboard", {
    state: () => ({
        // Main data sections
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
            new_customers: 0,
            conversion_rate: 0,
        },
        recent_activity: {
            recent_orders: [],
            recent_products: [],
            recent_reviews: [],
        },
        analytics: {
            sales: {
                period: "30_days",
                start_date: "",
                end_date: "",
                top_products: [],
                order_status_distribution: {},
                top_selling_products_by_category: [],
            },
            orders: {
                period: "30_days",
                start_date: "",
                end_date: "",
                order_data: [],
                total_orders: 0,
                average_order_value: 0,
                order_status_distribution: {},
            },
            users: {
                period: "30_days",
                start_date: "",
                end_date: "",
                user_registration_data: [],
                user_role_distribution: {},
                total_users: 0,
                active_users: 0,
            },
        },

        // Loading & error states
        loading: false,
        error: null,

        // Current filters
        currentPeriod: "30_days",
        customStartDate: null,
        customEndDate: null,
    }),

    getters: {
        // Statistics Getters
        productActivationRate: (state) => {
            if (!state.statistics.total_products) return 0;
            return (
                (state.statistics.active_products /
                    state.statistics.total_products) *
                100
            ).toFixed(1);
        },

        categoryActivationRate: (state) => {
            if (!state.statistics.total_categories) return 0;
            return (
                (state.statistics.active_categories /
                    state.statistics.total_categories) *
                100
            ).toFixed(1);
        },

        orderCompletionRate: (state) => {
            if (!state.statistics.total_orders) return 0;
            return (
                (state.statistics.completed_orders /
                    state.statistics.total_orders) *
                100
            ).toFixed(1);
        },

        // Recent Activity Getters
        recentOrdersCount: (state) =>
            state.recent_activity.recent_orders.length,
        recentProductsCount: (state) =>
            state.recent_activity.recent_products.length,
        recentReviewsCount: (state) =>
            state.recent_activity.recent_reviews.length,

        // Analytics Getters - Sales
        topSellingProduct: (state) => {
            return state.analytics.sales.top_products[0] || null;
        },

        totalRevenueInPeriod: (state) => {
            return state.analytics.sales.top_products.reduce(
                (sum, product) => sum + parseFloat(product.total_revenue || 0),
                0,
            );
        },

        // Order Status Helpers
        pendingOrderPercentage: (state) =>
            state.analytics.sales.order_status_distribution.pending
                ?.percentage || 0,
        cancelledOrderPercentage: (state) =>
            state.analytics.sales.order_status_distribution.cancelled
                ?.percentage || 0,

        // User Analytics Getters
        monthlyNewUsers: (state) => {
            const currentMonth = new Date().getMonth() + 1;
            const currentYear = new Date().getFullYear();
            const monthData = state.analytics.users.user_registration_data.find(
                (item) =>
                    item.month === currentMonth && item.year === currentYear,
            );
            return monthData ? monthData.user_count : 0;
        },

        userRoleNames: (state) => {
            return Object.keys(state.analytics.users.user_role_distribution);
        },

        // Chart Data Formatters
        orderTrendChartData: (state) => {
            return {
                labels: state.analytics.orders.order_data.map(
                    (item) => item.date,
                ),
                datasets: [
                    {
                        label: "Orders per Day",
                        data: state.analytics.orders.order_data.map(
                            (item) => item.order_count,
                        ),
                        borderColor: "#3B82F6",
                        backgroundColor: "rgba(59, 130, 246, 0.2)",
                        tension: 0.4,
                    },
                ],
            };
        },

        orderStatusChartData: (state) => {
            const statuses = Object.keys(
                state.analytics.sales.order_status_distribution,
            );
            return {
                labels: statuses.map(
                    (status) =>
                        status.charAt(0).toUpperCase() + status.slice(1),
                ),
                datasets: [
                    {
                        data: statuses.map(
                            (status) =>
                                state.analytics.sales.order_status_distribution[
                                    status
                                ].count,
                        ),
                        backgroundColor: [
                            "#3B82F6", // pending - blue
                            "#F59E0B", // processing - amber
                            "#10B981", // shipped - emerald
                            "#8B5CF6", // delivered - violet
                            "#EF4444", // cancelled - red
                            "#6B7280", // refunded - gray
                        ],
                        borderWidth: 0,
                    },
                ],
            };
        },

        topProductsChartData: (state) => {
            return {
                labels: state.analytics.sales.top_products.map(
                    (product) => product.name,
                ),
                datasets: [
                    {
                        label: "Units Sold",
                        data: state.analytics.sales.top_products.map(
                            (product) => parseInt(product.total_sold),
                        ),
                        backgroundColor: "#3B82F6",
                        borderColor: "#2563EB",
                        borderWidth: 1,
                    },
                ],
            };
        },
    },

    actions: {
        async fetchOverview(
            period = "30_days",
            startDate = null,
            endDate = null,
        ) {
            this.loading = true;
            this.error = null;

            try {
                // Update current filters
                this.currentPeriod = period;
                this.customStartDate = startDate;
                this.customEndDate = endDate;

                // Build query parameters
                const params = { period };
                if (startDate && endDate) {
                    params.start_date = startDate;
                    params.end_date = endDate;
                }

                const response = await axios.get("/dashboard/overview", {
                    params,
                });

                if (response.data.success) {
                    const data = response.data.data;
                    // Update state with fresh data
                    this.statistics = { ...data.statistics };
                    this.recent_activity = { ...data.recent_activity };
                    this.analytics = { ...data.analytics };
                } else {
                    throw new Error(
                        response.data.message ||
                            "Failed to fetch dashboard data",
                    );
                }
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    err.message ||
                    "Failed to load dashboard overview";
                console.error(
                    "[DashboardOverview Store] Error fetching overview:",
                    err,
                );
                throw err;
            } finally {
                this.loading = false;
            }
        },

        // Quick filter actions
        async fetchLast7Days() {
            await this.fetchOverview("7_days");
        },

        async fetchLast30Days() {
            await this.fetchOverview("30_days");
        },

        async fetchLast90Days() {
            await this.fetchOverview("90_days");
        },

        async fetchLastYear() {
            await this.fetchOverview("1_year");
        },

        async fetchCustomPeriod(startDate, endDate) {
            await this.fetchOverview("custom", startDate, endDate);
        },

        // Utility Actions
        clearError() {
            this.error = null;
        },

        // Data Transformation Helpers (can be used in components if needed)
        formatCurrency(amount) {
            return new Intl.NumberFormat("en-EG", {
                style: "currency",
                currency: "EGP",
            }).format(amount);
        },

        formatDate(dateString) {
            if (!dateString) return "";
            return new Date(dateString).toLocaleDateString("en-EG", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
        },

        formatDateTime(dateString) {
            if (!dateString) return "";
            return new Date(dateString).toLocaleString("en-EG", {
                year: "numeric",
                month: "short",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            });
        },

        getStatusBadgeClass(status) {
            const classes = {
                pending: "bg-yellow-100 text-yellow-800",
                processing: "bg-blue-100 text-blue-800",
                shipped: "bg-green-100 text-green-800",
                delivered: "bg-purple-100 text-purple-800",
                cancelled: "bg-red-100 text-red-800",
                refunded: "bg-gray-100 text-gray-800",
            };
            return classes[status] || "bg-gray-100 text-gray-800";
        },

        getRatingStars(rating) {
            // Ensure rating is a number
            const numRating = Number(rating);
            // Return an array with 'true' repeated 'numRating' times
            return Array(numRating).fill(true);
        },
    },
});
