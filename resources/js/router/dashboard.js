// dashboard.js
import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";

// Dashboard Layout
import DashboardLayout from "../pages/dashboard/layouts/DashboardLayout.vue";

// Dashboard Pages
import Dashboard from "../pages/dashboard/Overview.vue";
import DashboardProducts from "../pages/dashboard/Products.vue";
import ProductDetails from "../pages/dashboard/ProductDetails.vue";
import Orders from "../pages/dashboard/Orders.vue";
import Categories from "../pages/dashboard/Categories.vue";
import Users from "../pages/dashboard/Users.vue";
import Roles from "../pages/dashboard/Roles.vue";
import Reviews from "../pages/dashboard/Reviews.vue";
import Settings from "../pages/dashboard/Settings.vue";
import Contact from "../pages/dashboard/ContactMessages.vue";
import PrivacyPolicy from "../pages/dashboard/PrivacyPolicy.vue";
const routes = [
    {
        path: "/dashboard",
        component: DashboardLayout,
        meta: {
            requiresAuth: true,
            requiresPermission: "view_dashboard",
            requiresVerified: true,
        },
        children: [
            {
                path: "",
                name: "dashboard",
                component: Dashboard,
                meta: {
                    title: "Dashboard",
                    breadcrumb: [{ name: "Dashboard", path: "/dashboard" }],
                },
            },
            {
                path: "products",
                name: "dashboard-products",
                component: DashboardProducts,
                meta: {
                    title: "Products Management",
                    requiresPermission: "manage_products",
                    breadcrumb: [
                        { name: "Dashboard", path: "/dashboard" },
                        { name: "Products", path: "/dashboard/products" },
                    ],
                },
            },
            {
                path: "products/:id/details",
                name: "dashboard.products.details",
                component: ProductDetails,
                meta: {
                    title: "Product Details",
                    requiresAuth: true,
                    requiresPermission: "manage_products",
                    parent: "dashboard-products",
                    breadcrumb: [
                        { name: "Dashboard", path: "/dashboard" },
                        { name: "Products", path: "/dashboard/products" },
                        { name: "Details", path: "" },
                    ],
                },
            },
            {
                path: "orders",
                name: "dashboard.orders",
                component: Orders,
                meta: {
                    title: "Orders Management",
                    requiresAuth: true,
                    requiresPermission: "manage_orders",
                    breadcrumb: [
                        { name: "Dashboard", path: "/dashboard" },
                        { name: "Orders", path: "/dashboard/orders" },
                    ],
                },
            },
            {
                path: "categories",
                name: "dashboard.categories",
                component: Categories,
                meta: {
                    title: "Categories Management",
                    requiresAuth: true,
                    requiresPermission: "manage_categories",
                    breadcrumb: [
                        { name: "Dashboard", path: "/dashboard" },
                        { name: "Categories", path: "/dashboard/categories" },
                    ],
                },
            },
            {
                path: "users",
                name: "dashboard.users",
                component: Users,
                meta: {
                    title: "Users Management",
                    requiresAuth: true,
                    requiresPermission: "manage_users",
                    breadcrumb: [
                        { name: "Dashboard", path: "/dashboard" },
                        { name: "Users", path: "/dashboard/users" },
                    ],
                },
            },
            {
                path: "roles",
                name: "dashboard.roles",
                component: Roles,
                meta: {
                    title: "Roles Management",
                    requiresAuth: true,
                    requiresPermission: "manage_roles",
                    breadcrumb: [
                        { name: "Dashboard", path: "/dashboard" },
                        { name: "Roles", path: "/dashboard/roles" },
                    ],
                },
            },
            {
                path: "contact-messages",
                name: "dashboard.contact-messages",
                component: Contact,
                meta: {
                    title: "Contact Messages",
                    requiresAuth: true,
                    requiresPermission: "manage_contact_messages",
                    breadcrumb: [
                        { name: "Dashboard", path: "/dashboard" },
                        {
                            name: "Contact Messages",
                            path: "/dashboard/contact-messages",
                        },
                    ],
                },
            },
            {
                path: "reviews",
                name: "dashboard.reviews",
                component: Reviews,
                meta: {
                    title: "Reviews Management",
                    requiresAuth: true,
                    requiresPermission: "manage_reviews",
                    breadcrumb: [
                        { name: "Dashboard", path: "/dashboard" },
                        { name: "Reviews", path: "/dashboard/reviews" },
                    ],
                },
            },
            {
                path: "settings",
                name: "dashboard.settings",
                component: Settings,
                meta: {
                    title: "Settings",
                    requiresPermission: "manage_settings",
                    breadcrumb: [
                        { name: "Dashboard", path: "/dashboard" },
                        { name: "Settings", path: "/dashboard/settings" },
                    ],
                },
            },
            {
                path: "privacy-policy",
                name: "dashboard.privacy-policy",
                component: PrivacyPolicy,
                meta: {
                    title: "Privacy & Policies",
                    requiresPermission: "manage_settings",
                    breadcrumb: [
                        { name: "Dashboard", path: "/dashboard" },
                        {
                            name: "Privacy & Policies",
                            path: "/dashboard/privacy-policy",
                        },
                    ],
                },
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Dashboard-specific route guards
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // Check if route requires authentication
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next({ name: "login" });
        return;
    }

    // Check if route requires specific permission
    if (to.meta.requiresPermission) {
        const hasPermission = authStore.hasPermission(
            to.meta.requiresPermission,
        );

        if (!hasPermission) {
            next({ name: "home" });
            return;
        }
    }

    // Set page title
    if (to.meta.title) {
        document.title = `${to.meta.title} - E-Commerce Dashboard`;
    }

    next();
});

export default router;
