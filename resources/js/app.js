import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'

import App from './App.vue'
import './bootstrap'
import './style.css'

// Import stores
import { useAuthStore } from './stores/auth'
import { useCartStore } from './stores/cart'
import { useProductStore } from './stores/products'

// Import components
import AppLayout from './layouts/AppLayout.vue'
import AuthLayout from './layouts/AuthLayout.vue'
import LoadingOverlay from './components/LoadingOverlay.vue'

// Import pages
import Home from './pages/Home.vue'
import Products from './pages/Products.vue'
import ProductDetail from './pages/ProductDetail.vue'
import Cart from './pages/Cart.vue'
import Checkout from './pages/Checkout.vue'
import Login from './pages/auth/Login.vue'
import Register from './pages/auth/Register.vue'
import Profile from './pages/Profile.vue'
import Orders from './pages/Orders.vue'
import Wishlist from './pages/Wishlist.vue'
import Search from './pages/Search.vue'
import Category from './pages/Category.vue'
import NotFound from './pages/NotFound.vue'

// Router configuration
const routes = [
    {
        path: '/',
        component: AppLayout,
        children: [
            { path: '', name: 'home', component: Home },
            { path: '/products', name: 'products', component: Products },
            { path: '/products/:id', name: 'product-detail', component: ProductDetail, props: true },
            { path: '/category/:id', name: 'category', component: Category, props: true },
            { path: '/search', name: 'search', component: Search },
            { path: '/cart', name: 'cart', component: Cart },
            { path: '/checkout', name: 'checkout', component: Checkout, meta: { requiresAuth: true } },
            { path: '/profile', name: 'profile', component: Profile, meta: { requiresAuth: true } },
            { path: '/orders', name: 'orders', component: Orders, meta: { requiresAuth: true } },
            { path: '/wishlist', name: 'wishlist', component: Wishlist, meta: { requiresAuth: true } },
        ]
    },
    {
        path: '/auth',
        component: AuthLayout,
        children: [
            { path: 'login', name: 'login', component: Login },
            { path: 'register', name: 'register', component: Register },
        ]
    },
    { path: '/:pathMatch(.*)*', name: 'not-found', component: NotFound }
]

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        } else {
            return { top: 0 }
        }
    }
})

// Router guards
router.beforeEach(async (to, from, next) => {
    NProgress.start()

    const authStore = useAuthStore()

    // Check if route requires authentication
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next({ name: 'login' })
        return
    }

    // Redirect authenticated users away from auth pages
    if ((to.name === 'login' || to.name === 'register') && authStore.isAuthenticated) {
        next({ name: 'home' })
        return
    }

    next()
})

router.afterEach(() => {
    NProgress.done()
})

// Create Pinia store
const pinia = createPinia()

// Create Vue app
const app = createApp(App)

// Configure Toast
const toastOptions = {
    position: 'top-right',
    timeout: 3000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: 'button',
    icon: true,
    rtl: false
}

// Use plugins
app.use(pinia)
app.use(router)
app.use(Toast, toastOptions)

// Register global components
app.component('AppLayout', AppLayout)
app.component('AuthLayout', AuthLayout)
app.component('LoadingOverlay', LoadingOverlay)

// Mount app
app.mount('#app')
