import { defineStore } from "pinia";
import axios from "../../bootstrap";

export const useUsersStore = defineStore("dashboardUsers", {
    state: () => ({
        users: [],
        trashedUsers: [],
        currentUser: null,
        roles: {},
        loading: false,
        deleting: false,
        error: null,
        pagination: {
            current_page: 1,
            per_page: 10,
            total: 0,
            last_page: 1,
        },
        trashedPagination: {
            current_page: 1,
            per_page: 10,
            total: 0,
            last_page: 1,
        },
        filters: {
            search: "",
            is_active: "",
        },
        statistics: {
            total_users: 0,
            active_users: 0,
            inactive_users: 0,
            trashed_users: 0,
            verified_users: 0,
            unverified_users: 0,
        },
    }),

    actions: {
        // Fetch all users
        async fetchUsers(page = 1, perPage = 10) {
            this.loading = true;
            this.error = null;
            try {
                const params = {
                    page,
                    per_page: perPage,
                    search: this.filters.search || undefined,
                    is_active: this.filters.is_active || undefined,
                };
                Object.keys(params).forEach(
                    (key) => params[key] === undefined && delete params[key],
                );
                const response = await axios.get("/dashboard/users", {
                    params,
                });
                // Store users data directly (API now returns roles correctly as array of objects)
                this.users = response.data.data || [];

                // Store pagination and statistics
                this.pagination = response.data.pagination || this.pagination;
                this.statistics = response.data.statistics || this.statistics;

                this.roles = response.data.roles || [];
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to load users";
                console.error("Error in fetchUsers:", err);
            } finally {
                this.loading = false;
            }
        },

        // Fetch single user
        async fetchUser(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get(`/dashboard/users/${id}`);
                this.currentUser = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to load user details";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async createUser(userData) {
            this.loading = true;
            this.error = null;
            try {
                const formData = new FormData();

                if (userData.is_active !== undefined) {
                    userData.is_active =
                        userData.is_active === true ||
                        userData.is_active === "true"
                            ? 1
                            : 0;
                }

                for (let key in userData) {
                    if (key === "avatar" && userData[key] instanceof File) {
                        formData.append("avatar", userData[key]);
                    } else if (key !== "avatar") {
                        formData.append(key, userData[key]);
                    }
                }

                const response = await axios.post(
                    "/dashboard/users",
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    },
                );

                this.users.unshift(response.data.data);
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to create user";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async updateUser(id, userData) {
            this.loading = true;
            this.error = null;
            try {
                const formData = new FormData();

                if (userData.is_active !== undefined) {
                    userData.is_active =
                        userData.is_active === true ||
                        userData.is_active === "true"
                            ? 1
                            : 0;
                }

                for (let key in userData) {
                        if (key === "avatar" && userData[key] instanceof File) {
                        formData.append("avatar", userData[key]);
                    } else if (key !== "avatar") {
                        formData.append(key, userData[key]);
                    }
                }

                formData.append("_method", "POST");

                const response = await axios.post(
                    `/dashboard/users/${id}`,
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    },
                );

                const index = this.users.findIndex((u) => u.id === id);
                if (index !== -1) this.users[index] = response.data.data;
                this.currentUser = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to update user";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        // Soft delete user
        async deleteUser(id) {
            this.deleting = true;
            this.error = null;
            try {
                await axios.delete(`/dashboard/users/${id}`);
                this.users = this.users.filter((u) => u.id !== id);
                return true;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to delete user";
                throw err;
            } finally {
                this.deleting = false;
            }
        },

        // Fetch trashed users
        async fetchTrashedUsers(page = 1, perPage = 10) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get("/dashboard/users/trashed", {
                    params: { page, per_page: perPage },
                });
                this.trashedUsers = response.data.data || [];
                this.trashedPagination =
                    response.data.pagination || this.trashedPagination;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to load trashed users";
            } finally {
                this.loading = false;
            }
        },

        // Restore user
        async restoreUser(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post(
                    `/dashboard/users/${id}/restore`,
                );
                this.trashedUsers = this.trashedUsers.filter(
                    (u) => u.id !== id,
                );
                this.users.unshift(response.data.data);
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to restore user";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        // Permanently delete user
        async forceDeleteUser(id) {
            this.deleting = true;
            this.error = null;
            try {
                await axios.delete(`/dashboard/users/${id}/force`);
                this.trashedUsers = this.trashedUsers.filter(
                    (u) => u.id !== id,
                );
                return true;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to permanently delete user";
                throw err;
            } finally {
                this.deleting = false;
            }
        },

        // Manage filters
        setFilter(key, value) {
            this.filters[key] = value;
            this.pagination.current_page = 1;
            this.fetchUsers(1, this.pagination.per_page);
        },

        clearFilters() {
            this.filters = { search: "", is_active: "" };
            this.fetchUsers(1, this.pagination.per_page);
        },

        clearError() {
            this.error = null;
        },

        async assignRole(userId, roleName) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post(
                    `/dashboard/users/${userId}/assign-role`,
                    { role: roleName },
                );

                // Update user in the list
                const index = this.users.findIndex((u) => u.id === userId);
                if (index !== -1) this.users[index] = response.data.data;

                // Update current user if it's the same
                if (this.currentUser && this.currentUser.id === userId) {
                    this.currentUser = response.data.data;
                }

                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to assign role";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        // Remove role from user
        async removeRole(userId, roleName) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.delete(
                    `/dashboard/users/${userId}/remove-role`,
                    { data: { role: roleName } }, // DELETE with payload
                );

                // Update user in the list
                const index = this.users.findIndex((u) => u.id === userId);
                if (index !== -1) this.users[index] = response.data.data;

                // Update current user if it's the same
                if (this.currentUser && this.currentUser.id === userId) {
                    this.currentUser = response.data.data;
                }

                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to remove role";
                throw err;
            } finally {
                this.loading = false;
            }
        },
    },
});
