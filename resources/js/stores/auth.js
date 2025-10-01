import { defineStore } from "pinia";
import axios from "../bootstrap";
import { useToast } from "vue-toastification";
import { registerPushSubscription } from "./push-subscription";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: JSON.parse(localStorage.getItem("auth_user")) || null,
        token: localStorage.getItem("auth_token"),
        permissions: JSON.parse(localStorage.getItem("auth_permissions")) || [],
        roles: JSON.parse(localStorage.getItem("auth_roles")) || [],
        is_verified:
            JSON.parse(localStorage.getItem("auth_is_verified")) || false,
        loading: false,
        error: null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        userRole: (state) => state.user?.role || null,
        isAdmin: (state) => state.user?.role === "admin",
        isCustomer: (state) => state.user?.role === "customer",
        isVerified: (state) => state.is_verified,
        hasPermission: (state) => (permission) =>
            state.permissions.includes(permission),
        hasRole: (state) => (role) => state.roles.includes(role),
        hasAnyPermission: (state) => (permissions) =>
            permissions.some((permission) =>
                state.permissions.includes(permission),
            ),
        hasAllPermissions: (state) => (permissions) =>
            permissions.every((permission) =>
                state.permissions.includes(permission),
            ),
    },

    actions: {
        async login(credentials) {
            this.loading = true;
            this.error = null;
            const toast = useToast();

            try {
                const response = await axios.post("/login", credentials);

                const token = response.data?.token || null;
                const user = response.data?.data || null;

                if (!token) {
                    throw new Error("Token not found in response");
                }
                if (!user) {
                    throw new Error("User data not found in response");
                }

                // check is verified and send email
                if (response.data.resend_email) {
                    toast.info("Please check your email for verification.");
                }

                this.token = token;
                this.user = user;
                this.permissions = user.permissions || [];
                this.roles = user.roles || [];
                this.is_verified = user.is_verified || false;

                localStorage.setItem("auth_token", token);
                localStorage.setItem("auth_user", JSON.stringify(user));
                localStorage.setItem(
                    "auth_permissions",
                    JSON.stringify(this.permissions),
                );
                localStorage.setItem("auth_roles", JSON.stringify(this.roles));
                localStorage.setItem(
                    "auth_is_verified",
                    JSON.stringify(this.is_verified),
                );

                // Register push subscription after successful login
                setTimeout(() => {
                    registerPushSubscription();
                }, 1000);

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
                const isFormData = userData instanceof FormData;
                const config = isFormData 
                    ? { 
                        headers: { 
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json'
                        } 
                    } 
                    : {};

                const response = await axios.post("/register", userData, config);

                this.user = response.data.data;
                this.token = response.data.token;
                this.permissions = this.user.permissions || [];
                this.roles = this.user.roles || [];
                this.is_verified = this.user.is_verified || false;

                localStorage.setItem("auth_token", this.token);
                localStorage.setItem("auth_user", JSON.stringify(this.user));
                localStorage.setItem(
                    "auth_permissions",
                    JSON.stringify(this.permissions),
                );
                localStorage.setItem("auth_roles", JSON.stringify(this.roles));
                localStorage.setItem(
                    "auth_is_verified",
                    JSON.stringify(this.is_verified),
                );

                // Register push subscription after successful registration
                setTimeout(() => {
                    registerPushSubscription();
                }, 1000);

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
                    const response = await axios.post(
                        "/logout",
                        {},
                        { withCredentials: true },
                    );

                    if (response.data?.success) {
                        this.user = null;
                        this.token = null;
                        this.permissions = [];
                        this.roles = [];
                        this.is_verified = false;

                        localStorage.removeItem("auth_token");
                        localStorage.removeItem("auth_user");
                        localStorage.removeItem("auth_permissions");
                        localStorage.removeItem("auth_roles");
                        localStorage.removeItem("auth_is_verified");

                        document.cookie =
                            "XSRF-TOKEN=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                        document.cookie =
                            "laravel_session=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                        document.cookie =
                            "remember_web_5=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                            
                        // Refresh the page and redirect to login
                        window.location.href = '/login';
                        window.location.reload();
                        return;
                    }
                }
            } catch (error) {
                console.error("Logout error:", error);
            } finally {
                this.user = null;
                this.token = null;
                this.permissions = [];
                this.roles = [];
                this.is_verified = false;
                localStorage.removeItem("auth_token");
                localStorage.removeItem("auth_user");
                localStorage.removeItem("auth_permissions");
                localStorage.removeItem("auth_roles");
                localStorage.removeItem("auth_is_verified");
                
                // Ensure redirection happens even if there was an error
                window.location.href = '/login';
                window.location.reload();
            }
        },

        async checkAuth() {
            if (!this.token) return false;

            if (this.user) return true;

            this.loading = true;
            try {
                const response = await axios.get("/user");
                this.user = response.data.data;
                this.permissions = this.user.permissions || [];
                this.roles = this.user.roles || [];
                this.is_verified = this.user.is_verified || false;

                localStorage.setItem("auth_user", JSON.stringify(this.user));
                localStorage.setItem(
                    "auth_permissions",
                    JSON.stringify(this.permissions),
                );
                localStorage.setItem("auth_roles", JSON.stringify(this.roles));
                localStorage.setItem(
                    "auth_is_verified",
                    JSON.stringify(this.is_verified),
                );
                return true;
            } catch (error) {
                this.logout();
                return false;
            } finally {
                this.loading = false;
            }
        },

        async refreshUser() {
            if (!this.token) return;

            try {
                const response = await axios.get("/user");
                const user = response.data.data;

                this.user = user;
                this.permissions = user.permissions || [];
                this.roles = user.roles || [];
                this.is_verified = user.is_verified || false;

                // Update localStorage
                localStorage.setItem("auth_user", JSON.stringify(user));
                localStorage.setItem(
                    "auth_permissions",
                    JSON.stringify(this.permissions),
                );
                localStorage.setItem("auth_roles", JSON.stringify(this.roles));
                localStorage.setItem(
                    "auth_is_verified",
                    JSON.stringify(this.is_verified),
                );
            } catch (error) {
                console.error("Failed to refresh user:", error);
                if (error.response?.status === 401) {
                    this.logout();
                }
            }
        },

        /**
         * Resend email verification link
         */
        async resendVerificationEmail() {
            this.loading = true;
            const toast = useToast();

            try {
                const response = await axios.post(
                    "/email/verification-notification",
                );

                const data = response.data;

                if (data.sent) {
                    toast.success(data.message || "Verification link sent!");
                } else {
                    toast.info(
                        data.message || "Your email is already verified.",
                    );
                }

                return { success: true, message: data.message };
            } catch (error) {
                const message =
                    error.response?.data?.message ||
                    "Failed to send verification email. Please try again.";

                toast.error(message);
                return { success: false, message };
            } finally {
                this.loading = false;
            }
        },


        /**
         * Send password reset link to the user's email
         */
        async sendResetLink(email) {
            this.loading = true;
            this.error = null;
            const toast = useToast();

            try {
                await axios.post("/forgot-password", { email });
                toast.success("Password reset link sent to your email.");
                return { success: true };
            } catch (err) {
                const message =
                    err.response?.data?.message ||
                    "Failed to send reset link. Please try again.";
                this.error = message;
                toast.error(message);
                return { success: false, error: message };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Reset password using token and new password
         */
        async resetPassword(payload) {
            this.loading = true;
            this.error = null;
            const toast = useToast();

            if (payload.password !== payload.password_confirmation) {
                toast.error("Passwords do not match.");
                this.loading = false;
                return { success: false, error: "Passwords do not match." };
            }

            try {
                await axios.post("/reset-password", payload);
                toast.success("Your password has been updated successfully!");
                return { success: true };
            } catch (err) {
                const message =
                    err.response?.data?.message ||
                    "Failed to reset password. Please try again.";
                this.error = message;
                toast.error(message);
                return { success: false, error: message };
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
