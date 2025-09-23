// stores/policy.js
import { defineStore } from "pinia";
import axios from "../bootstrap";

export const usePolicyStore = defineStore("policy", {
    state: () => ({
        // Main cache for all policies — loaded once
        allPolicies: [],
        // Loading states
        loading: false,
        loadingSingle: false,
        // Error handling
        error: null,
        // Policy types we support
        policyTypes: ["privacy", "terms", "return", "shipping", "warranty", "cookies", "faq"],
    }),

    getters: {
        /**
         * Get first policy of a specific type from cache
         * @param {string} type - policy type (e.g., 'privacy')
         * @returns {Object|null} first policy object of the type or null
         */
        getPolicyByType: (state) => (type) => {
            const policies = state.allPolicies.filter(policy => policy.type === type);
            return policies.length > 0 ? policies[0] : null;
        },

        /**
         * Get all policies of a specific type from cache
         * @param {string} type - policy type (e.g., 'privacy')
         * @returns {Array} array of policy objects
         */
        getPoliciesByType: (state) => (type) => {
            return state.allPolicies.filter(policy => policy.type === type);
        },

        /**
         * Check if policy exists and is active
         * @param {string} type
         * @returns {boolean}
         */
        hasPolicy: (state) => (type) => {
            const policy = state.getPolicyByType(type);
            return policy !== null && policy.is_active === true;
        },

        /**
         * Get all active policies as key-value map for fast access
         * e.g., policiesMap.value.privacy
         */
        policiesMap: (state) => {
            const map = {};
            state.allPolicies.forEach((policy) => {
                if (policy.is_active) {
                    map[policy.type] = policy;
                }
            });
            return map;
        },

        /**
         * Check if all policies are loaded
         */
        isLoaded: (state) => {
            return state.allPolicies.length > 0;
        },
    },

    actions: {
        /**
         * Load ALL policies once (on app/page init)
         * Stores them in cache for fast access
         * @returns {Promise<boolean>}
         */
        async loadAllPolicies() {
            if (this.allPolicies.length > 0) {
                // Already loaded — no need to fetch again
                return true;
            }

            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get("/public/policies");

                if (
                    response.data.success &&
                    Array.isArray(response.data.data)
                ) {
                    this.allPolicies = response.data.data;
                    return true;
                } else {
                    throw new Error("Invalid response format");
                }
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    error.message ||
                    "Failed to load policies";
                console.error("[PolicyStore] Failed to load policies:", error);
                return false;
            } finally {
                this.loading = false;
            }
        },

        /**
         * Fetch single policy by type — fallback to API if not in cache
         * Optimized: only fetch if not already loaded
         * @param {string} type - policy type (e.g., 'privacy')
         * @returns {Promise<Object|null>}
         */
        async fetchPolicyByType(type) {
            // First, check cache
            let policy = this.getPolicyByType(type);

            if (policy) {
                return policy;
            }

            // Not in cache — fetch from API
            this.loadingSingle = true;

            try {
                const response = await axios.get(
                    `/public/policies/${type}`,
                );

                if (response.data.success && Array.isArray(response.data.data)) {
                    const policies = response.data.data;
                    
                    // Add all returned policies to cache
                    policies.forEach(policy => {
                        // Only add if not already in cache
                        if (!this.allPolicies.some(p => p.id === policy.id)) {
                            this.allPolicies.push(policy);
                        }
                    });

                    // Return the first policy for backward compatibility
                    return policies.length > 0 ? policies[0] : null;
                } else {
                    throw new Error(
                        response.data.message || "Policy not found",
                    );
                }
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    error.message ||
                    `Failed to load ${type} policy`;
                console.error(
                    `[PolicyStore] Failed to load policy type "${type}":`,
                    error,
                );
                return null;
            } finally {
                this.loadingSingle = false;
            }
        },

        /**
         * Prefetch specific policy types (optional optimization)
         * Useful if you know user will navigate to certain pages
         * @param {string[]} types - array of policy types to prefetch
         */
        async prefetchPolicies(types = []) {
            const promises = types.map((type) => this.fetchPolicyByType(type));
            await Promise.allSettled(promises);
        },

        /**
         * Clear cache and reload (for admin or data refresh scenarios)
         */
        async refreshPolicies() {
            this.allPolicies = [];
            return await this.loadAllPolicies();
        },

        /**
         * Clear error state
         */
        clearError() {
            this.error = null;
        },
    },
});
