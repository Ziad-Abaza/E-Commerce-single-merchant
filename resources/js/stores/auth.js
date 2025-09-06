import { defineStore } from "pinia";
import axios from "../bootstrap";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
        token: localStorage.getItem("auth_token"),
        loading: false,
        error: null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token && !!state.user,
        userRole: (state) => state.user?.role || null,
        isAdmin: (state) => state.user?.role === "admin",
        isCustomer: (state) => state.user?.role === "customer",
    },

    actions: {
async login(credentials) {
    this.loading = true
    this.error = null

    try {
        const response = await axios.post("/login", credentials)

        this.token = response.data?.token || response.data?.data?.token || null
        this.user = response.data?.data || null

        if (this.token) {
            localStorage.setItem("auth_token", this.token)
        }

        console.log("TOKEN:", this.token)
        return { success: true, data: response.data }
    } catch (error) {
        console.error("Login error:", error)
        this.error = error.response?.data?.message || "Login failed"
        return { success: false, error: this.error }
    } finally {
        this.loading = false
    }
}
,

        async register(userData) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post("/register", userData);

                this.user = response.data.data;
                this.token = response.data.token;
                localStorage.setItem("auth_token", this.token);

                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Registration failed";
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            this.loading = true;

            try {
                if (this.token) {
                    await axios.post("/logout");
                }
            } catch (error) {
                console.error("Logout error:", error);
            } finally {
                this.user = null;
                this.token = null;
                localStorage.removeItem("auth_token");
                this.loading = false;
            }
        },

        async checkAuth() {
            if (!this.token) {
                return false;
            }

            this.loading = true;

            try {
                const response = await axios.get("/user");
                this.user = response.data.data;
                return true;
            } catch (error) {
                this.logout();
                return false;
            } finally {
                this.loading = false;
            }
        },

        async updateProfile(profileData) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post("/profile", profileData);
                this.user = { ...this.user, ...response.data.data };
                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Profile update failed";
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async changePassword(passwordData) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post(
                    "/profile/change-password",
                    passwordData,
                );
                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Password change failed";
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        clearError() {
            this.error = null;
        },
    },
});
