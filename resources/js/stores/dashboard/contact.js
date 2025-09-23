// resources/js/stores/dashboard/contactMessages.js
import { defineStore } from "pinia";
import axios from "../../bootstrap";

export const useContactMessagesStore = defineStore("dashboardContactMessages", {
    state: () => ({
        messages: [],
        trashedMessages: [],
        currentMessage: null,
        loading: false,
        error: null,
        pagination: {
            current_page: 1,
            per_page: 15,
            total: 0,
            last_page: 1,
            from: 0,
            to: 0,
        },
        filters: {
            search: "",
            status: "",
        },
        statistics: {
            total_messages: 0,
            unread_messages: 0,
            read_messages: 0,
            replied_messages: 0,
            spam_messages: 0,
            trashed_messages: 0,
        },
    }),

    actions: {
        async fetchMessages(page = 1, perPage = 15) {
            this.loading = true;
            this.error = null;
            try {
                const params = {
                    page,
                    per_page: perPage,
                    search: this.filters.search || undefined,
                    status: this.filters.status || undefined,
                };
                Object.keys(params).forEach(
                    (key) => params[key] === undefined && delete params[key],
                );

                const response = await axios.get(
                    "/dashboard/contact-messages",
                    { params },
                );
                this.messages = response.data.data || [];
                this.pagination = response.data.pagination || this.pagination;
                this.statistics = response.data.statistics || this.statistics;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to load messages";
            } finally {
                this.loading = false;
            }
        },

        async fetchMessage(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get(
                    `/dashboard/contact-messages/${id}`,
                );
                this.currentMessage = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to load message";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async createMessage(messageData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post(
                    "/dashboard/contact-messages",
                    messageData,
                );
                this.messages.unshift(response.data.data);
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to create message";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async updateMessage(id, messageData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post(
                    `/dashboard/contact-messages/${id}`,
                    messageData,
                );
                const index = this.messages.findIndex((m) => m.id === id);
                if (index !== -1) this.messages[index] = response.data.data;
                this.currentMessage = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to update message";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async deleteMessage(id) {
            this.loading = true;
            this.error = null;
            try {
                await axios.delete(`/dashboard/contact-messages/${id}`);
                this.messages = this.messages.filter((m) => m.id !== id);
                return true;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to delete message";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async fetchTrashedMessages(page = 1, perPage = 15) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get(
                    "/dashboard/contact-messages/trashed",
                    {
                        params: { page, per_page: perPage },
                    },
                );
                this.trashedMessages = response.data.data || [];
                this.pagination = response.data.pagination || this.pagination;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to load trashed messages";
            } finally {
                this.loading = false;
            }
        },

        async restoreMessage(id) {
            this.loading = true;
            this.error = null;
            try {
                await axios.post(`/dashboard/contact-messages/${id}/restore`);
                this.trashedMessages = this.trashedMessages.filter(
                    (m) => m.id !== id,
                );
                return true;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to restore message";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async forceDeleteMessage(id) {
            this.loading = true;
            this.error = null;
            try {
                await axios.delete(`/dashboard/contact-messages/${id}/force`);
                this.trashedMessages = this.trashedMessages.filter(
                    (m) => m.id !== id,
                );
                return true;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to permanently delete message";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async updateMessageStatus(id, status) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.patch(
                    `/dashboard/contact-messages/${id}/status`,
                    { status },
                );
                const index = this.messages.findIndex((m) => m.id === id);
                if (index !== -1) this.messages[index] = response.data.data;
                this.currentMessage = response.data.data;
                return response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to update message status";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        setFilter(key, value) {
            this.filters[key] = value;
            this.pagination.current_page = 1;
            this.fetchMessages(1, this.pagination.per_page);
        },

        clearFilters() {
            this.filters = { search: "", status: "" };
            this.fetchMessages(1, this.pagination.per_page);
        },

        clearError() {
            this.error = null;
        },
    },
});
