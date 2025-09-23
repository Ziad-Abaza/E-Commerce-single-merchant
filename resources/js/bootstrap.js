import axios from "axios";
import { useAuthStore } from "./stores/auth";

// Set up axios defaults
axios.defaults.baseURL = import.meta.env.APP_URL || "/api";
axios.defaults.headers.common["Accept"] = "application/json";
axios.defaults.headers.common["Content-Type"] = "application/json";
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.withCredentials = true;

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
} else {
    console.error(
        "CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token",
    );
}

// Request interceptor to add auth token
axios.interceptors.request.use(
    (config) => {
        const authStore = useAuthStore();

        if (authStore.token) {
            config.headers.Authorization = `Bearer ${authStore.token}`;
        }

        return config;
    },
    (error) => Promise.reject(error),
);

// Response interceptor to handle auth errors
axios.interceptors.response.use(
    (response) => response,
    async (error) => {
        const authStore = useAuthStore();

        if (error.response?.status === 401) {
            const stillValid = await authStore.checkAuth();
            if (!stillValid) {
                authStore.logout();
                window.location.href = "/auth/login";
            }
        }

        return Promise.reject(error);
    },
);

// Export axios instance
export default axios;
