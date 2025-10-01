<template>
    <aside
        class="h-screen flex-shrink-0 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 flex flex-col transition-all duration-300 ease-in-out"
        :class="[
            isMobile
                ? 'fixed top-0 left-0 z-50 transform' +
                  (props.isOpen ? ' translate-x-0' : ' -translate-x-full')
                : 'fixed left-0 top-0',
            props.isOpen ? 'w-64' : 'w-16',
        ]"
    >
        <!-- Scrollable Content -->
        <div class="flex-1 flex flex-col overflow-y-auto pt-1">
            <!-- Logo -->
            <div
                class="px-4 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center space-x-3"
            >
                <div
                    class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0"
                >
                    <svg
                        class="w-5 h-5 text-white"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                        />
                    </svg>
                </div>
                <div v-show="props.isOpen" class="flex-1 min-w-0">
                    <h1
                        class="text-lg font-bold text-gray-900 dark:text-white truncate"
                    >
                        E-Commerce
                    </h1>
                    <p
                        class="text-xs text-gray-500 dark:text-gray-400 truncate"
                    >
                        Admin Dashboard
                    </p>
                </div>
            </div>

            <!-- User -->
            <div
                class="py-2 border-b border-gray-200 dark:border-gray-700 flex items-center justify-center space-x-3"
            >
                <template v-if="userAvatar">
                    <img
                        :src="userAvatar"
                        :alt="userName"
                        class="w-10 h-10 rounded-full object-cover flex-shrink-0"
                    />
                </template>
                <template v-else>
                    <div
                        class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0"
                    >
                        <span class="text-white font-semibold text-sm">{{
                            userInitials
                        }}</span>
                    </div>
                </template>
                <div v-show="props.isOpen" class="flex-1 min-w-0">
                    <p
                        class="text-sm font-medium text-gray-900 dark:text-white truncate"
                    >
                        {{ userName }}
                    </p>
                    <p
                        class="text-xs text-gray-500 dark:text-gray-400 truncate"
                    >
                        {{ userRole }}
                    </p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-2 py-4 space-y-1">
                <template v-for="section in menuSections" :key="section.title">
                    <div
                        v-show="props.isOpen && section.title"
                        class="px-2 mb-2"
                    >
                        <h3
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            {{ section.title }}
                        </h3>
                    </div>

                    <template v-for="item in section.items" :key="item.name">
                        <router-link
                            v-if="!item.permission || authStore.hasPermission(item.permission)"
                            :to="item.to"
                            :class="[
                                'group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200',
                                $route.name === item.routeName ||
                                $route.meta.parent === item.routeName
                                    ? 'bg-blue-600 text-white'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white',
                            ]"
                            :title="!props.isOpen ? item.name : ''"
                            @click="isMobile && $emit('close')"
                        >
                            <component
                                :is="item.icon"
                                class="flex-shrink-0 text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300"
                                :class="props.isOpen ? 'w-5 h-5' : 'w-6 h-6'"
                            />
                            <span v-show="props.isOpen" class="ml-3">{{
                                item.name
                            }}</span>
                        </router-link>
                    </template>

                    <div
                        v-if="props.isOpen && section.divider"
                        class="border-t border-gray-200 dark:border-gray-700 my-4"
                    ></div>
                </template>
            </nav>
        </div>

        <!-- Footer Buttons -->
        <div
            class="px-2 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col space-y-2"
        >
            <!-- Toggle Sidebar -->
            <button
                @click="$emit('toggle')"
                class="group flex items-center justify-center w-full px-0 py-2 rounded-md transition-colors duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white"
                :title="!props.isOpen ? 'Expand Sidebar' : 'Collapse Sidebar'"
            >
                <svg
                    v-if="props.isOpen"
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>
                <svg
                    v-else
                    class="w-8 h-8"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5l7 7-7 7"
                    />
                </svg>
                <span v-show="props.isOpen" class="ml-3">Toggle Sidebar</span>
            </button>

            <!-- Logout -->
            <button
                @click="logout"
                class="group flex items-center w-full px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white"
                :title="!props.isOpen ? 'Logout' : ''"
            >
                <svg
                    class="flex-shrink-0 text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300"
                    :class="props.isOpen ? 'w-5 h-5' : 'w-6 h-6'"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                    />
                </svg>
                <span v-show="props.isOpen" class="ml-3">Logout</span>
            </button>
        </div>
    </aside>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useTheme } from "@/composables/useTheme.js";
import {
    HomeIcon,
    CubeIcon,
    ShoppingCartIcon,
    Squares2X2Icon,
    ChartBarIcon,
    DocumentChartBarIcon,
    UserIcon,
    Cog6ToothIcon,
    DocumentTextIcon,
} from "@heroicons/vue/24/outline";

// Props & Emit
const props = defineProps({ isOpen: Boolean });
const emit = defineEmits(["close", "toggle"]);

const router = useRouter();
const authStore = useAuthStore();
const { isDark } = useTheme();

const isMobile = ref(false);
const checkScreenSize = () => {
    isMobile.value = window.innerWidth < 1024;
};
onMounted(() => {
    checkScreenSize();
    window.addEventListener("resize", checkScreenSize);
});
onUnmounted(() => window.removeEventListener("resize", checkScreenSize));

// User Info
const userName = computed(() => authStore.user?.name || "Admin User");
const userRole = computed(() => authStore.user?.role || "Administrator");
const userAvatar = computed(() => authStore.user?.avatar || null);
const userInitials = computed(() =>
    userName.value
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase(),
);

// Logout
const logout = async () => {
    await authStore.logout();
    router.push("/auth/login");
};

// Icons
const menuSections = [
    {
        title: "",
        divider: false,
        items: [
            {
                name: "Dashboard",
                to: "/dashboard",
                routeName: "dashboard",
                icon: HomeIcon,
            },
        ],
    },
    {
        title: "Manage Store",
        divider: true,
        items: [
            {
                name: "Products",
                to: "/dashboard/products",
                routeName: "dashboard-products",
                icon: CubeIcon,
            },
            {
                name: "Orders",
                to: "/dashboard/orders",
                routeName: "dashboard.orders",
                icon: ShoppingCartIcon,
            },
            {
                name: "Categories",
                to: "/dashboard/categories",
                routeName: "dashboard.categories",
                icon: Squares2X2Icon,
            },
             {
                name: "Reviews",
                to: "/dashboard/reviews",
                routeName: "dashboard.reviews",
                icon: DocumentChartBarIcon,
            },
            {
                name: "Contact Messages",
                to: "/dashboard/contact-messages",
                routeName: "dashboard.contact-messages",
                icon: ChartBarIcon,
            },
            {
                name: "Promo Codes",
                to: "/dashboard/promo-codes",
                routeName: "dashboard.promo-codes",
                icon: DocumentChartBarIcon,
                permission: "manage_promo_codes",
            },
        ],
    },
    {
        title: "Manage Users",
        divider: true,
        items: [
            {
                name: "Users",
                to: "/dashboard/users",
                routeName: "dashboard.users",
                icon: UserIcon,
            },
            {
                name: "Roles",
                to: "/dashboard/roles",
                routeName: "dashboard.roles",
                icon: DocumentChartBarIcon,
            },
        ],
    },
    {
        title: "Settings",
        divider: false,
        items: [
            {
                name: "Settings",
                to: "/dashboard/settings",
                routeName: "dashboard.settings",
                icon: Cog6ToothIcon,
                permission: "manage_settings",
            },
            {
                name: "Privacy & Policies",
                to: "/dashboard/privacy-policy",
                routeName: "dashboard.privacy-policy",
                icon: DocumentTextIcon,
                permission: "manage_settings",
            },
        ],
    },
];
</script>
