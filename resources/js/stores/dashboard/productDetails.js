// resources/js/stores/dashboard/productDetails.js
import { defineStore } from "pinia";
import axios from "../../bootstrap";

export const useProductDetailsStore = defineStore("dashboardProductDetails", {
    state: () => ({
        details: [],
        trashedDetails: [],
        currentDetail: null,
        product: null,
        loading: false,
        error: null,
        pagination: {
            current_page: 1,
            per_page: 10,
            total: 0,
            last_page: 1,
        },
        filters: {
            search: "",
            is_active: "",
            in_stock: "",
        },
    }),

    actions: {
        // resources/js/stores/dashboard/productDetails.js
        async fetchProductDetails(productId, page = 1, perPage = 10) {
            this.loading = true;
            this.error = null;

            try {
                const params = {
                    page: page,
                    per_page: perPage,
                    search: this.filters.search || undefined,
                    is_active:
                        this.filters.is_active === "active"
                            ? true
                            : this.filters.is_active === "inactive"
                              ? false
                              : undefined,
                    in_stock:
                        this.filters.in_stock === "in_stock"
                            ? true
                            : this.filters.in_stock === "out_of_stock"
                              ? false
                              : undefined,
                };

                Object.keys(params).forEach(
                    (key) => params[key] === undefined && delete params[key],
                );

                const response = await axios.get(
                    `/dashboard/products/${productId}/details`,
                    {
                        params,
                    },
                );

                this.details = response.data.data || [];
                this.pagination = {
                    current_page: response.data.pagination.current_page,
                    per_page: response.data.pagination.per_page,
                    total: response.data.pagination.total,
                    last_page: response.data.pagination.total_pages,
                    from: response.data.pagination.from,
                    to: response.data.pagination.to,
                };

                return this.details;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to load product details";
                console.error(
                    "[ProductDetails Store] Error fetching product details:",
                    err,
                );
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async fetchProductDetail(productId, detailId) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(
                    `/dashboard/products/${productId}/details/${detailId}`,
                );
                this.currentDetail = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to load product detail";
                console.error(
                    "[ProductDetails Store] Error fetching product detail:",
                    err,
                );
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async createProductDetail(productId, detailData) {
            this.loading = true;
            this.error = null;

            try {
                const formData = new FormData();
                if (detailData.is_active !== undefined) {
                    detailData.is_active =
                        detailData.is_active === true ||
                        detailData.is_active === "true"
                            ? 1
                            : 0;
                }
                
                for (let key in detailData) {
                    if (key === 'images' && detailData[key]?.length) {
                        // Handle file uploads
                        detailData[key].forEach((file, index) => {
                            formData.append(`images[${index}]`, file);
                        });
                    } else if (key === 'attributes' && Array.isArray(detailData[key])) {
                        // Stringify the attributes array
                        formData.append('attributes', JSON.stringify(detailData[key]));
                    } else if (key !== 'images') {
                        // Handle all other fields
                        if (detailData[key] !== null && detailData[key] !== undefined) {
                            // Convert boolean values to 1 or 0
                            const value = typeof detailData[key] === 'boolean' 
                                ? (detailData[key] ? 1 : 0)
                                : detailData[key];
                            formData.append(key, value);
                        }
                    }
                }
                formData.append("product_id", productId);

                const response = await axios.post(
                    `/dashboard/products/${productId}/details`,
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    },
                );

                this.details.unshift(response.data.data);
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to create product detail";
                console.error(
                    "[ProductDetails Store] Error creating product detail:",
                    err,
                );
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async updateProductDetail(productId, detailId, detailData) {
            this.loading = true;
            this.error = null;

            try {
                const formData = new FormData();
                if (detailData.is_active !== undefined) {
                    detailData.is_active =
                        detailData.is_active === true ||
                        detailData.is_active === "true"
                            ? 1
                            : 0;
                }
                for (let key in detailData) {
                    if (key === 'images' && detailData[key]?.length) {
                        // Handle file uploads
                        detailData[key].forEach((file, index) => {
                            formData.append(`images[${index}]`, file);
                        });
                    } else if (key === 'attributes' && Array.isArray(detailData[key])) {
                        // Stringify the attributes array
                        formData.append('attributes', JSON.stringify(detailData[key]));
                    } else if (key !== 'images') {
                        // Handle all other fields
                        if (detailData[key] !== null && detailData[key] !== undefined) {
                            // Convert boolean values to 1 or 0
                            const value = typeof detailData[key] === 'boolean' 
                                ? (detailData[key] ? 1 : 0)
                                : detailData[key];
                            formData.append(key, value);
                        }
                    }
                }

                formData.append("_method", "POST");

                const response = await axios.post(
                    `/dashboard/products/${productId}/details/${detailId}`,
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    },
                );

                const index = this.details.findIndex((d) => d.id === detailId);
                if (index !== -1) {
                    this.details[index] = response.data.data;
                }
                this.currentDetail = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to update product detail";
                console.error(
                    "[ProductDetails Store] Error updating product detail:",
                    err,
                );
                throw err;
            } finally {
                this.loading = false;
            }
        },
        async deleteProductDetail(productId, detailId, confirm = false) {
            this.loading = true;
            this.error = null;

            try {
                await axios.delete(
                    `/dashboard/products/${productId}/details/${detailId}`,
                    { data: { confirm } },
                );

                this.details = this.details.filter((d) => d.id !== detailId);
                return true;
            } catch (err) {
                if (err.response?.data?.requires_confirmation) {
                    throw { requiresConfirmation: true, detailId };
                }

                this.error =
                    err.response?.data?.message ||
                    "Failed to delete product detail";
                console.error(
                    "[ProductDetails Store] Error deleting product detail:",
                    err,
                );
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async fetchTrashedDetails(productId, page = 1, perPage = 10) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(
                    `/dashboard/products/${productId}/details/trash`,
                    { params: { page, per_page: perPage } },
                );

                this.trashedDetails = response.data.data || [];
                this.pagination = {
                    current_page: response.data.pagination.current_page,
                    per_page: response.data.pagination.per_page,
                    total: response.data.pagination.total,
                    last_page: response.data.pagination.total_pages,
                    from: response.data.pagination.from,
                    to: response.data.pagination.to,
                };

                return this.trashedDetails;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to load trashed details";
                console.error(
                    "[ProductDetails Store] Error fetching trashed details:",
                    err,
                );
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async restoreDetail(productId, detailId) {
            this.loading = true;
            this.error = null;

            try {
                await axios.post(
                    `/dashboard/products/${productId}/details/${detailId}/restore`,
                );
                this.trashedDetails = this.trashedDetails.filter(
                    (d) => d.id !== detailId,
                );
                return true;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to restore product detail";
                console.error(
                    "[ProductDetails Store] Error restoring detail:",
                    err,
                );
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async forceDeleteDetail(productId, detailId) {
            this.loading = true;
            this.error = null;

            try {
                await axios.delete(
                    `/dashboard/products/${productId}/details/${detailId}/force-delete`,
                );
                this.trashedDetails = this.trashedDetails.filter(
                    (d) => d.id !== detailId,
                );
                return true;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to force delete detail";
                console.error(
                    "[ProductDetails Store] Error force deleting detail:",
                    err,
                );
                throw err;
            } finally {
                this.loading = false;
            }
        },

        setFilter(key, value) {
            this.filters[key] = value;
            this.pagination.current_page = 1;
            if (this.product && this.product.id) {
                this.fetchProductDetails(
                    this.product.id,
                    1,
                    this.pagination.per_page,
                );
            }
        },

        clearFilters() {
            this.filters = { search: "", is_active: "", in_stock: "" };
            this.pagination.current_page = 1;
            if (this.product && this.product.id) {
                this.fetchProductDetails(
                    this.product.id,
                    1,
                    this.pagination.per_page,
                );
            }
        },

        clearError() {
            this.error = null;
        },

        setProduct(product) {
            this.product = product;
        },
    },
});
