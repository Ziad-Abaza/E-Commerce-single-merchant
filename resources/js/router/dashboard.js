import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

// Dashboard Layout
import DashboardLayout from "../pages/dashboard/layouts/DashboardLayout.vue";

// Dashboard Pages
import Dashboard from '../pages/dashboard/Dashboard.vue'
import Products from '../pages/dashboard/Products.vue'
import Orders from '../pages/dashboard/Orders.vue'
import Categories from '../pages/dashboard/Categories.vue'

const routes = [
    {
        path: '/dashboard',
        component: DashboardLayout,
        meta: {
            requiresAuth: true,
            requiresPermission: 'view_dashboard'
        },
        children: [
            {
                path: '',
                name: 'dashboard.home',
                component: Dashboard,
                meta: {
                    title: 'Dashboard',
                    breadcrumb: [
                        { name: 'Dashboard', path: '/dashboard' }
                    ]
                }
            },
            {
                path: 'products',
                name: 'dashboard.products',
                component: Products,
                meta: {
                    title: 'Products Management',
                    requiresPermission: 'manage_products',
                    breadcrumb: [
                        { name: 'Dashboard', path: '/dashboard' },
                        { name: 'Products', path: '/dashboard/products' }
                    ]
                }
            },
            {
                path: 'orders',
                name: 'dashboard.orders',
                component: Orders,
                meta: {
                    title: 'Orders Management',
                    requiresPermission: 'manage_orders',
                    breadcrumb: [
                        { name: 'Dashboard', path: '/dashboard' },
                        { name: 'Orders', path: '/dashboard/orders' }
                    ]
                }
            },
            {
                path: 'categories',
                name: 'dashboard.categories',
                component: Categories,
                meta: {
                    title: 'Categories Management',
                    requiresPermission: 'manage_categories',
                    breadcrumb: [
                        { name: 'Dashboard', path: '/dashboard' },
                        { name: 'Categories', path: '/dashboard/categories' }
                    ]
                }
            }
        ]
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// Dashboard-specific route guards
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore()

    // Check if route requires authentication
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next({ name: 'login' })
        return
    }

    // Check if route requires specific permission
    if (to.meta.requiresPermission) {
        const hasPermission = authStore.hasPermission(to.meta.requiresPermission)

        if (!hasPermission) {
            next({ name: 'home' })
            return
        }
    }

    // Set page title
    if (to.meta.title) {
        document.title = `${to.meta.title} - E-Commerce Dashboard`
    }

    next()
})

export default router
