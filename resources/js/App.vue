<template>
    <div id="app" class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <router-view />
        <MobileMenuOverlay v-if="showMobileMenu" @close="closeMobileMenu" />
        <LoadingOverlay v-if="isLoading" />
        <WhatsAppChat
            v-if="
                !$route.path.startsWith('/dashboard') &&
                !$route.path.startsWith('/auth')
            "
        />
    </div>
</template>

<script setup>
import { computed, onMounted, ref, onBeforeUnmount } from "vue";
import { useAuthStore } from "./stores/auth";
import { useProductStore } from "./stores/products";
import { useSiteStore } from "./stores/site";
import { useCartStore } from "./stores/cart";
import LoadingOverlay from "./components/LoadingOverlay.vue";
import MobileMenuOverlay from "./components/layout/MobileMenuOverlay.vue";
import WhatsAppChat from "./components/WhatsAppChat.vue";
import { useTheme } from "./composables/useTheme.js";
import { usePolicyStore } from "@/stores/policy";

const authStore = useAuthStore();
const productStore = useProductStore();
const cartStore = useCartStore();
const siteStore = useSiteStore();
const policyStore = usePolicyStore();

const { theme, isDark, setTheme } = useTheme();

const isLoading = computed(() => {
    return authStore.loading || productStore.loading || cartStore.loading;
});

const showMobileMenu = ref(false);

const openMobileMenu = () => {
    showMobileMenu.value = true;
    document.body.style.overflow = "hidden"; // Prevent scrolling when menu is open
};

const closeMobileMenu = () => {
    showMobileMenu.value = false;
    document.body.style.overflow = ""; // Re-enable scrolling
};

// Listen for the custom event from AppHeader
const handleOpenMobileMenu = () => {
    openMobileMenu();
};

// Initialize app data
const initializeApp = async () => {
    try {
        await siteStore.fetchSettings();
        await authStore.checkAuth();

        // Load initial data
        await Promise.all([
            productStore.loadCategories(),
            productStore.loadFeaturedProducts(),
            productStore.loadLatestProducts(),
        ]);

        // Load cart if user is authenticated
        if (authStore.isAuthenticated) {
            await cartStore.loadCart();
        }

        // Set theme
        setTheme(theme.value);

        // Load policies
        policyStore.loadAllPolicies();
    } catch (error) {
        console.error("Failed to initialize app:", error);
    }
};

onMounted(() => {
    initializeApp();
    window.addEventListener("open-mobile-menu", handleOpenMobileMenu);
});

onBeforeUnmount(() => {
    window.removeEventListener("open-mobile-menu", handleOpenMobileMenu);
    document.body.style.overflow = ""; // Ensure scrolling is re-enabled if component is unmounted
});
</script>

<style>
/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* NProgress customization */
#nprogress .bar {
    background: #3b82f6 !important;
    height: 3px !important;
}

#nprogress .peg {
    box-shadow:
        0 0 10px #3b82f6,
        0 0 5px #3b82f6 !important;
}

#nprogress .spinner-icon {
    border-top-color: #3b82f6 !important;
    border-left-color: #3b82f6 !important;
}
</style>
