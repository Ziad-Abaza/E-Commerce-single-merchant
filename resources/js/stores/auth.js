import { defineStore } from "pinia";
import axios from "../bootstrap";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: JSON.parse(localStorage.getItem("auth_user")) || null,
        token: localStorage.getItem("auth_token"),
        permissions: JSON.parse(localStorage.getItem("auth_permissions")) || [],
        roles: JSON.parse(localStorage.getItem("auth_roles")) || [],
        loading: false,
        error: null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        userRole: (state) => state.user?.role || null,
        isAdmin: (state) => state.user?.role === "admin",
        isCustomer: (state) => state.user?.role === "customer",
        hasPermission: (state) => (permission) => state.permissions.includes(permission),
        hasRole: (state) => (role) => state.roles.includes(role),
        hasAnyPermission: (state) => (permissions) => permissions.some(permission => state.permissions.includes(permission)),
        hasAllPermissions: (state) => (permissions) => permissions.every(permission => state.permissions.includes(permission)),
    },

    actions: {
        async login(credentials) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post("/login", credentials);

                // اتأكد من وجود التوكن
                const token = response.data?.token || null;
                const user = response.data?.data || null;

                if (!token) {
                    throw new Error("Token not found in response");
                }
                if (!user) {
                    throw new Error("User data not found in response");
                }

                // خزّن البيانات
                this.token = token;
                this.user = user;
                this.permissions = user.permissions || [];
                this.roles = user.roles || [];

                localStorage.setItem("auth_token", token);
                localStorage.setItem("auth_user", JSON.stringify(user));
                localStorage.setItem("auth_permissions", JSON.stringify(this.permissions));
                localStorage.setItem("auth_roles", JSON.stringify(this.roles));

                return { success: true, data: response.data };
            } catch (error) {
                console.error("Login error:", error);
                this.error =
                    error.response?.data?.message ||
                    error.message ||
                    "Login failed";
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },
        async register(userData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post("/register", userData);

                this.user = response.data.data;
                this.token = response.data.token;
                this.permissions = this.user.permissions || [];
                this.roles = this.user.roles || [];

                localStorage.setItem("auth_token", this.token);
                localStorage.setItem("auth_user", JSON.stringify(this.user));
                localStorage.setItem("auth_permissions", JSON.stringify(this.permissions));
                localStorage.setItem("auth_roles", JSON.stringify(this.roles));

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
            try {
                if (this.token) {
                    const response = await axios.post("/logout");

                    if (response.data?.success) {
                        this.user = null;
                        this.token = null;
                        this.permissions = [];
                        this.roles = [];

                        localStorage.removeItem("auth_token");
                        localStorage.removeItem("auth_user");
                        localStorage.removeItem("auth_permissions");
                        localStorage.removeItem("auth_roles");

                        document.cookie =
                            "XSRF-TOKEN=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                        document.cookie =
                            "laravel_session=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                    }
                }
            } catch (error) {
                console.error("Logout error:", error);
            } finally {
                this.user = null;
                this.token = null;
                this.permissions = [];
                this.roles = [];
                localStorage.removeItem("auth_token");
                localStorage.removeItem("auth_user");
                localStorage.removeItem("auth_permissions");
                localStorage.removeItem("auth_roles");
            }
        },

        async checkAuth() {
            if (!this.token) return false;

            // لو عندك user مخزن خلاص
            if (this.user) return true;

            this.loading = true;
            try {
                const response = await axios.get("/user");
                this.user = response.data.data;
                this.permissions = this.user.permissions || [];
                this.roles = this.user.roles || [];
                localStorage.setItem("auth_user", JSON.stringify(this.user));
                localStorage.setItem("auth_permissions", JSON.stringify(this.permissions));
                localStorage.setItem("auth_roles", JSON.stringify(this.roles));
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
                localStorage.setItem("auth_user", JSON.stringify(this.user));
                return { success: true, data: response.data };
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Profile update failed";
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
