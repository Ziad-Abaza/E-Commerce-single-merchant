// index.js
import { createRouter, createWebHistory } from "vue-router";
import NProgress from "nprogress";
import "nprogress/nprogress.css";

// Import stores
import { useAuthStore } from "../stores/auth";

// Import components
import AppLayout from "../pages/dashboard/layouts/AppLayout.vue";
import AuthLayout from "../pages/dashboard/layouts/AuthLayout.vue";

// Import pages
import Home from "../pages/Home.vue";
import Products from "../pages/Products.vue";
import ProductDetail from "../pages/ProductDetail.vue";
import Cart from "../pages/Cart.vue";
import Checkout from "../pages/Checkout.vue";
import Login from "../pages/auth/Login.vue";
import Register from "../pages/auth/Register.vue";
import Profile from "../pages/Profile.vue";
import Orders from "../pages/Orders.vue";
import Wishlist from "../pages/Wishlist.vue";
import Search from "../pages/Search.vue";
import Category from "../pages/Category.vue";
import About from "../pages/About.vue";
import Contact from "../pages/Contact.vue";
import NotFound from "../pages/NotFound.vue";
import VerifyEmail from "../pages/auth/VerifyEmail.vue";
import ReSendVerifyEmail from "../pages/auth/VerifyRequired.vue";

// Import dashboard router
import dashboardRouter from "./dashboard";

// Router configuration
const routes = [
    {
        path: "/",
        component: AppLayout,
        children: [
            { path: "", name: "home", component: Home },
            { path: "/products", name: "products", component: Products },
            {
                path: "/products/:id",
                name: "product-detail",
                component: ProductDetail,
                props: true,
            },
            {
                path: "/category/:id",
                name: "category",
                component: Category,
                props: true,
            },
            { path: "/search", name: "search", component: Search },
            { path: "/cart", name: "cart", component: Cart },
            {
                path: "/checkout",
                name: "checkout",
                component: Checkout,
                meta: { requiresAuth: true, requiresVerified: true},
            },
            {
                path: "/profile",
                name: "profile",
                component: Profile,
                meta: { requiresAuth: true },
            },
            {
                path: "/orders",
                name: "orders",
                component: Orders,
                meta: { requiresAuth: true, requiresVerified: true },
            },
            {
                path: "/wishlist",
                name: "wishlist",
                component: Wishlist,
                meta: { requiresAuth: true },
            },
            { path: "/about", name: "about", component: About },
            { path: "/contact", name: "contact", component: Contact },
        ],
    },
    {
        path: "/auth",
        component: AuthLayout,
        children: [
            { path: "login", name: "login", component: Login },
            { path: "register", name: "register", component: Register },
        ],
    },
    {
        path: "/email/verify/:id/:hash",
        name: "VerifyEmail",
        component: VerifyEmail,
        props: true,
    },
    {
        path: "/verify-required",
        name: "VerifyRequired",
        component: ReSendVerifyEmail,
        meta: { requiresAuth: true},
    },
    { path: "/:pathMatch(.*)*", name: "not-found", component: NotFound },
];

const router = createRouter({
    history: createWebHistory(),
    routes: [...routes, ...dashboardRouter.getRoutes()],
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { top: 0 };
        }
    },
});

// Router guards
router.beforeEach(async (to, from, next) => {
    NProgress.start();

    const authStore = useAuthStore();

    // Sync auth state if needed
    if (authStore.token && !authStore.user) {
        await authStore.checkAuth();
    }

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

    if (
        to.meta.requiresVerified &&
        authStore.isAuthenticated &&
        !authStore.isVerified
    ) {
        next({ name: "VerifyRequired" });
        return;
    }

    // Check if route requires admin access (legacy support)
    if (
        to.meta.requiresAdmin &&
        (!authStore.user || authStore.user.role !== "admin")
    ) {
        next({ name: "home" });
        return;
    }

    // Redirect authenticated users away from auth pages
    if (
        (to.name === "login" || to.name === "register") &&
        authStore.isAuthenticated
    ) {
        next({ name: "home" });
        return;
    }

    next();
});

router.afterEach(() => {
    NProgress.done();
});

export default router;
