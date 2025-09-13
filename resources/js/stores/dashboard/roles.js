// resources/js/stores/dashboard/roles.js
import { defineStore } from "pinia";
import axios from "../../bootstrap";

export const useRolesStore = defineStore("dashboardRoles", {
    state: () => ({
        roles: [],
        permissions: [],
        currentRole: null,
        loading: false,
        deleting: false,
        error: null,
        pagination: {
            current_page: 1,
            per_page: 20,
            total: 0,
            last_page: 1,
            from: 0,
            to: 0,
        },
        statistics: {
            total_roles: 0,
            protected_roles: 0,
        },
    }),

    actions: {
        async fetchRoles(page = 1, perPage = 20) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get("/dashboard/roles", {
                    params: {
                        page: page,
                        per_page: perPage,
                    },
                });

                this.roles = response.data.data.roles || [];
                this.permissions = response.data.data.permissions || [];
                this.pagination = response.data.pagination || {
                    current_page: 1,
                    per_page: 20,
                    total: 0,
                    last_page: 1,
                    from: 0,
                    to: 0,
                };
                this.statistics = response.data.stats || {
                    total_roles: 0,
                    protected_roles: 0,
                };

                return this.roles;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to load roles";
                console.error("[Roles Store] Error fetching roles:", err);
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async fetchRole(id) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(`/dashboard/roles/${id}`);
                this.currentRole = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to load role";
                console.error("[Roles Store] Error fetching role:", err);
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async createRole(roleData) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post("/dashboard/roles", roleData);
                this.roles.unshift(response.data.data);
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to create role";
                console.error("[Roles Store] Error creating role:", err);
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async updateRole(id, roleData) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post(
                    `/dashboard/roles/${id}`,
                    roleData,
                );

                const index = this.roles.findIndex((r) => r.id === id);
                if (index !== -1) {
                    this.roles[index] = response.data.data;
                }

                this.currentRole = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to update role";
                console.error("[Roles Store] Error updating role:", err);
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async deleteRole(id) {
            this.deleting = true;
            this.error = null;

            try {
                await axios.delete(`/dashboard/roles/${id}`);

                // Remove from list
                this.roles = this.roles.filter((r) => r.id !== id);

                // Refresh pagination
                await this.fetchRoles(
                    this.pagination.current_page,
                    this.pagination.per_page,
                );

                return true;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to delete role";
                console.error("[Roles Store] Error deleting role:", err);
                throw err;
            } finally {
                this.deleting = false;
            }
        },

        clearError() {
            this.error = null;
        },
    },
});
