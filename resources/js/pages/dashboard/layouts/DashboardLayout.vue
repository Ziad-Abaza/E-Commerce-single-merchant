<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-0">
        <div class="flex py-0">
            <!-- Sidebar -->
            <Sidebar
                :isOpen="sidebarOpen"
                @close="sidebarOpen = false"
                @toggle="sidebarOpen = !sidebarOpen"
            />

            <!-- Main Content -->
            <div
                class="flex-1 flex flex-col overflow-hidden py-0 transition-all duration-300"
                :class="[
                    sidebarOpen
                        ? isMobile
                            ? ''
                            : 'ml-64'
                        : isMobile
                          ? ''
                          : 'ml-16',
                ]"
            >
                <!-- Dashboard Header -->
                <header
                    class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700"
                >
                    <div class="px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between items-center py-4">
                            <div class="flex items-center">
                                <button
                                    @click="toggleSidebar"
                                    class="lg:hidden -ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                                >
                                    <span class="sr-only">Open sidebar</span>
                                    <svg
                                        class="h-6 w-6"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"
                                        />
                                    </svg>
                                </button>

                                <h1
                                    class="ml-4 text-2xl font-semibold text-gray-900 dark:text-white"
                                >
                                    {{ pageTitle }}
                                </h1>
                            </div>

                            <div class="flex items-center space-x-4">
                                <!-- Theme Toggle -->
                                <ThemeToggle />
                                <!-- Home Button -->
                                <button
                                    @click="router.push('/')"
                                    :class="
                                        isDark
                                            ? 'p-2 text-slate-400 hover:text-white hover:bg-slate-700 rounded-md transition-colors duration-200'
                                            : 'p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-md transition-colors duration-200'
                                    "
                                    title="Home"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                        />
                                    </svg>
                                </button>
                                <!-- User Menu -->
                                <UserMenu />
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto">
                    <div class="px-4 sm:px-6 lg:px-8 py-8">
                        <router-view />
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useTheme } from "@/composables/useTheme.js";
import Sidebar from "../components/Sidebar.vue";
import UserMenu from "../components/UserMenu.vue";
import ThemeToggle from "@/components/common/ThemeToggle.vue";

const route = useRoute();
const router = useRouter();
const sidebarOpen = ref(true);
const isMobile = ref(false);

const pageTitle = computed(() => route.meta.title || "Dashboard");

const { isDark, toggleTheme } = useTheme();

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const checkScreen = () => {
    isMobile.value = window.innerWidth < 1024;
};
onMounted(() => {
    checkScreen();
    window.addEventListener("resize", checkScreen);
});
</script>
