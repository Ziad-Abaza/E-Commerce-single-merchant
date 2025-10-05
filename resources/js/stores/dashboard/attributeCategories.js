// resources/js/stores/dashboard/attributeCategories.js
import { defineStore } from "pinia";
import axios from "../../bootstrap";

export const useAttributeCategoriesStore = defineStore(
    "attributeCategories",
    {
        state: () => ({
            categories: [], // list of all categories (for dropdowns)
            categoryAttributes: [], // attributes assigned to a specific category
            loading: false,
            error: null,
        }),

        actions: {
            /**
             * Fetch all categories (used for assignment UI).
             */
            async fetchAllCategories() {
                this.loading = true;
                this.error = null;

                try {
                    const response = await axios.get(
                        "/dashboard/attribute-categories/all",
                    );
                    this.categories = response.data.data || [];
                } catch (err) {
                    this.error =
                        err.response?.data?.message ||
                        "Failed to load categories";
                    console.error(
                        "[AttributeCategories Store] Error fetching categories:",
                        err,
                    );
                    throw err;
                } finally {
                    this.loading = false;
                }
            },

            /**
             * Fetch all attributes assigned to a specific category.
             */
            async fetchAttributesForCategory(categoryId) {
                this.loading = true;
                this.error = null;

                try {
                    const response = await axios.get(
                        `/dashboard/attribute-categories/category/${categoryId}`,
                    );
                    this.categoryAttributes = response.data.data || [];
                    return this.categoryAttributes;
                } catch (err) {
                    this.error =
                        err.response?.data?.message ||
                        "Failed to load category attributes";
                    console.error(
                        "[AttributeCategories Store] Error fetching attributes for category:",
                        err,
                    );
                    throw err;
                } finally {
                    this.loading = false;
                }
            },

            /**
             * Assign an attribute to a category (or update pivot data).
             */
            async assignAttributeToCategory(payload) {
                // payload: { attribute_id, category_id, is_required, sort_order }
                this.loading = true;
                this.error = null;

                try {
                    await axios.post(
                        "/dashboard/attribute-categories/assign",
                        payload,
                    );
                    // Optionally refetch category attributes
                    if (payload.category_id) {
                        await this.fetchAttributesForCategory(
                            payload.category_id,
                        );
                    }
                    return true;
                } catch (err) {
                    this.error =
                        err.response?.data?.message ||
                        "Failed to assign attribute to category";
                    console.error(
                        "[AttributeCategories Store] Error assigning attribute:",
                        err,
                    );
                    throw err;
                } finally {
                    this.loading = false;
                }
            },

            /**
             * Detach an attribute from a category.
             */
            async detachAttributeFromCategory(attributeId, categoryId) {
                this.loading = true;
                this.error = null;

                try {
                    await axios.delete(
                        `/dashboard/attribute-categories/${attributeId}/${categoryId}/detach`,
                    );
                    // Refetch to update UI
                    await this.fetchAttributesForCategory(categoryId);
                    return true;
                } catch (err) {
                    this.error =
                        err.response?.data?.message ||
                        "Failed to detach attribute from category";
                    console.error(
                        "[AttributeCategories Store] Error detaching attribute:",
                        err,
                    );
                    throw err;
                } finally {
                    this.loading = false;
                }
            },

            /**
             * Clear error state.
             */
            clearError() {
                this.error = null;
            },
        },
    },
);
