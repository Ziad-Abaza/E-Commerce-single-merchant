import { defineStore } from "pinia";
import axios from "../../bootstrap";

export const usePoliciesStore = defineStore("policies", {
    state: () => ({
        policies: [],
        policyTypes: [],
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 15,
            total: 0,
        },
        filters: {
            search: "",
            type: "",
            is_active: null,
        },
        loading: false,
        saving: false,
        error: null,
        selectedPolicy: null,
    }),

    getters: {
        activePolicies: (state) => state.policies.filter(policy => policy.is_active),
        
        getPolicyById: (state) => (id) => {
            return state.policies.find(policy => policy.id === id) || null;
        },

        getPoliciesByType: (state) => (type) => {
            return state.policies.filter(policy => policy.type === type);
        },

        hasPolicies: (state) => state.policies.length > 0,
    },

    actions: {
        async fetchPolicies(page = 1, perPage = null, type = '') {
            this.loading = true;
            this.error = null;

            try {
                const params = {
                    page,
                    per_page: perPage || this.pagination.per_page,
                    ...this.filters,
                    type: type || this.filters.type, // Ensure type is included from parameters or filters
                };

                // Remove any undefined or null parameters
                Object.keys(params).forEach(key => {
                    if (params[key] === null || params[key] === undefined || params[key] === '') {
                        delete params[key];
                    }
                });

                const response = await axios.get("/dashboard/policies", { params });
                
                this.policies = response.data.data || [];
                this.policyTypes = response.data.types || [];
                this.pagination = {
                    current_page: response.data.pagination?.current_page || 1,
                    last_page: response.data.pagination?.last_page || 1,
                    per_page: response.data.pagination?.per_page || 15,
                    total: response.data.pagination?.total || 0,
                };
                
                return { success: true, data: response.data.data || [] };
            } catch (error) {
                this.error = error.response?.data?.message || error.message || 'Failed to fetch policies';
                console.error('Error fetching policies:', error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async fetchPolicy(id) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(`/dashboard/policies/${id}`);
                this.selectedPolicy = response.data.data;
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || error.message || 'Failed to fetch policy';
                console.error('Error fetching policy:', error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async createPolicy(policyData) {
            this.saving = true;
            this.error = null;

            try {
                const response = await axios.post("/dashboard/policies", policyData);
                this.policies.unshift(response.data.data);
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || error.message || 'Failed to create policy';
                console.error('Error creating policy:', error);
                return { 
                    success: false, 
                    error: this.error,
                    errors: error.response?.data?.errors 
                };
            } finally {
                this.saving = false;
            }
        },

        async updatePolicy(id, policyData) {
            this.saving = true;
            this.error = null;

            try {
                const response = await axios.post(`/dashboard/policies/${id}`, policyData);
                const index = this.policies.findIndex(p => p.id === id);
                if (index !== -1) {
                    this.policies.splice(index, 1, response.data.data);
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || error.message || 'Failed to update policy';
                console.error('Error updating policy:', error);
                return { 
                    success: false, 
                    error: this.error,
                    errors: error.response?.data?.errors 
                };
            } finally {
                this.saving = false;
            }
        },

        async deletePolicy(id) {
            this.loading = true;
            this.error = null;

            try {
                await axios.delete(`/dashboard/policies/${id}`);
                this.policies = this.policies.filter(policy => policy.id !== id);
                return { success: true };
            } catch (error) {
                this.error = error.response?.data?.message || error.message || 'Failed to delete policy';
                console.error('Error deleting policy:', error);
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async togglePolicyStatus(id) {
            try {
                const response = await axios.patch(`/dashboard/policies/${id}/toggle-status`);
                const index = this.policies.findIndex(p => p.id === id);
                if (index !== -1) {
                    this.policies.splice(index, 1, response.data.data);
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || error.message || 'Failed to toggle policy status';
                console.error('Error toggling policy status:', error);
                return { success: false, error: this.error };
            }
        },

        setFilters(filters) {
            this.filters = { ...this.filters, ...filters };
        },

        resetFilters() {
            this.filters = {
                search: "",
                type: "",
                is_active: null,
            };
        },

        resetState() {
            this.policies = [];
            this.policyTypes = [];
            this.pagination = {
                current_page: 1,
                last_page: 1,
                per_page: 15,
                total: 0,
            };
            this.filters = {
                search: "",
                type: "",
                is_active: null,
            };
            this.loading = false;
            this.saving = false;
            this.error = null;
            this.selectedPolicy = null;
        },
    },
});
