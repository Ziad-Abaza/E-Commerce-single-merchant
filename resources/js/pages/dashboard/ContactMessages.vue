<!-- resources/js/pages/dashboard/ContactMessages.vue -->
<template>
    <div class="p-2 sm:p-4 md:p-6 space-y-4 sm:space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col gap-4">
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
            >
                <div>
                    <h1
                        class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 dark:text-white"
                    >
                        Contact Messages Management
                    </h1>
                    <p
                        class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1"
                    >
                        Manage messages from your website visitors
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <button
                        @click="fetchMessages"
                        class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
                    >
                        <svg
                            class="w-4 h-4 mr-1"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                            />
                        </svg>
                        Refresh
                    </button>
                    <div class="flex gap-2">
                        <button
                            @click="showTrashed = false"
                            :class="
                                showTrashed
                                    ? 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                                    : 'bg-blue-600 text-white'
                            "
                            class="px-3 py-2 rounded-lg text-sm bg-green-500 text-white hover:bg-green-600 transition-colors duration-200"
                        >
                            Active Messages
                        </button>
                        <button
                            @click="handleShowTrashed"
                            :class="
                                showTrashed
                                    ? 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                                    : 'bg-blue-600 text-white'
                            "
                            class="px-3 py-2 rounded-lg text-sm bg-red-500 text-white hover:bg-red-600 transition-colors duration-200"
                        >
                            Trash
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Alert -->
        <div
            v-if="messagesStore.error"
            class="p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg animate-in slide-in-from-top-2 duration-300"
        >
            <div class="flex items-center">
                <svg
                    class="w-5 h-5 text-red-500 mr-2 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
                <span class="text-sm text-red-700 dark:text-red-300 flex-1">{{
                    messagesStore.error
                }}</span>
                <button
                    @click="messagesStore.clearError"
                    class="ml-2 text-red-500 hover:text-red-700 dark:hover:text-red-300 p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
                >
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-gray-500 dark:text-gray-400 text-xs font-medium"
                        >
                            Total Messages
                        </p>
                        <p
                            class="text-lg font-bold text-gray-900 dark:text-white mt-1"
                        >
                            {{ messagesStore.statistics?.total_messages }}
                        </p>
                    </div>
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <svg
                            class="w-5 h-5 text-blue-600 dark:text-blue-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-gray-500 dark:text-gray-400 text-xs font-medium"
                        >
                            Unread
                        </p>
                        <p
                            class="text-lg font-bold text-yellow-600 dark:text-yellow-400 mt-1"
                        >
                            {{ messagesStore.statistics?.unread_messages }}
                        </p>
                    </div>
                    <div
                        class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg"
                    >
                        <svg
                            class="w-5 h-5 text-yellow-600 dark:text-yellow-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 17h5l-5 5v-5zM9 7H4l5-5v5z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-gray-500 dark:text-gray-400 text-xs font-medium"
                        >
                            Replied
                        </p>
                        <p
                            class="text-lg font-bold text-green-600 dark:text-green-400 mt-1"
                        >
                            {{ messagesStore.statistics?.replied_messages }}
                        </p>
                    </div>
                    <div
                        class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg"
                    >
                        <svg
                            class="w-5 h-5 text-green-600 dark:text-green-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-gray-500 dark:text-gray-400 text-xs font-medium"
                        >
                            Spam
                        </p>
                        <p
                            class="text-lg font-bold text-red-600 dark:text-red-400 mt-1"
                        >
                            {{ messagesStore.statistics?.spam_messages }}
                        </p>
                    </div>
                    <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                        <svg
                            class="w-5 h-5 text-red-600 dark:text-red-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700"
        >
            <div class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="flex-1">
                    <Search
                        v-model="messagesStore.filters.search"
                        placeholder="Search by name, email, subject..."
                        @submit="handleSearch"
                    />
                </div>
                <Select
                    v-model="messagesStore.filters.status"
                    :options="statusOptions"
                    placeholder="All Statuses"
                    label="Status"
                    @update:modelValue="handleStatusFilter"
                />
                <button
                    v-if="hasActiveFilters"
                    @click="clearAllFilters"
                    class="px-3 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm"
                >
                    Clear
                </button>
            </div>
        </div>

        <!-- Loading Skeleton -->
        <div v-if="messagesStore.loading" class="space-y-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <div class="p-4 space-y-3">
                    <div
                        v-for="n in 5"
                        :key="n"
                        class="flex items-center space-x-4"
                    >
                        <div
                            class="w-12 h-12 bg-gray-300 dark:bg-gray-700 rounded-lg animate-pulse"
                        ></div>
                        <div class="flex-1 space-y-2">
                            <div
                                class="h-4 bg-gray-300 dark:bg-gray-700 rounded animate-pulse"
                            ></div>
                            <div
                                class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-2/3 animate-pulse"
                            ></div>
                        </div>
                        <div
                            class="w-20 h-8 bg-gray-300 dark:bg-gray-700 rounded animate-pulse"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages Table -->
        <div
            v-else
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
            <div
                v-if="messagesStore.messages.length === 0 && !showTrashed"
                class="text-center py-12"
            >
                <svg
                    class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                    />
                </svg>
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                >
                    No messages found
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    {{
                        hasActiveFilters
                            ? "Try adjusting your filters."
                            : "Messages will appear here."
                    }}
                </p>
            </div>

            <div
                v-else-if="
                    showTrashed && messagesStore.trashedMessages.length === 0
                "
                class="text-center py-12"
            >
                <p class="text-gray-500 dark:text-gray-400">
                    No trashed messages found.
                </p>
            </div>

            <div v-else>
                <Table
                    :headers="showTrashed ? trashedTableHeaders : tableHeaders"
                    :rows="showTrashed ? trashedTableRows : tableRows"
                />
            </div>
        </div>

        <!-- Pagination -->
        <div
            v-if="
                (messagesStore.messages.length > 0 && !showTrashed) ||
                (showTrashed && messagesStore.trashedMessages.length > 0)
            "
            class="flex justify-center"
        >
            <Pagination
                :total="messagesStore.pagination.total"
                :current-page="messagesStore.pagination.current_page"
                :per-page="messagesStore.pagination.per_page"
                :last-page="messagesStore.pagination.last_page"
                :from="messagesStore.pagination.from"
                :to="messagesStore.pagination.to"
                @page-change="handlePageChange"
                @update:perPage="handlePerPageChange"
            />
        </div>

        <!-- Modals -->
        <DetailsModal
            v-if="messagesStore.currentMessage"
            :show="showDetailModal"
            :item="messagesStore.currentMessage"
            :editable="false"
            title="Message Details"
            subtitle="Complete information about this message"
            id-label="Message ID"
            :custom-sections="messageDetailSections"
            @close="showDetailModal = false"
        />

        <ConfirmModal
            :show="showDeleteConfirm"
            title="Delete Message"
            message="Are you sure you want to delete this message? This action cannot be undone."
            confirm-text="Delete"
            confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
            @confirm="handleDelete"
            @cancel="showDeleteConfirm = false"
            :loading="messagesStore.loading"
        />

        <ConfirmModal
            :show="showRestoreConfirm"
            title="Restore Message"
            message="Are you sure you want to restore this message?"
            confirm-text="Restore"
            confirm-class="bg-green-600 hover:bg-green-700 focus:ring-green-500"
            @confirm="handleRestore"
            @cancel="showRestoreConfirm = false"
            :loading="messagesStore.loading"
        />

        <ConfirmModal
            :show="showForceDeleteConfirm"
            title="Permanently Delete Message"
            message="Are you sure you want to permanently delete this message? This action cannot be undone."
            confirm-text="Delete Permanently"
            confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
            @confirm="handleForceDelete"
            @cancel="showForceDeleteConfirm = false"
            :loading="messagesStore.loading"
        />

        <div
            v-if="showStatusModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            @click.self="showStatusModal = false"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md p-6"
            >
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Update Message Status
                    </h3>
                    <button
                        @click="showStatusModal = false"
                        class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <Select
                        v-model="selectedStatus"
                        :options="statusOptions"
                        label="Status"
                        required
                    />
                    <div class="flex gap-3">
                        <button
                            @click="showStatusModal = false"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="handleUpdateStatus"
                            :disabled="!selectedStatus"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-lg transition-colors"
                        >
                            Update
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useContactMessagesStore } from "../../stores/dashboard/contact";
import { useAuthStore } from "../../stores/auth";
import Search from "./components/Search.vue";
import Select from "./components/Select.vue";
import Table from "./components/Table.vue";
import Pagination from "./components/Pagination.vue";
import DetailsModal from "./components/DetailsModal.vue";
import ConfirmModal from "./components/ConfirmModal.vue";

const messagesStore = useContactMessagesStore();
const authStore = useAuthStore();

const showTrashed = ref(false);
const showDetailModal = ref(false);
const showDeleteConfirm = ref(false);
const showRestoreConfirm = ref(false);
const showForceDeleteConfirm = ref(false);
const showStatusModal = ref(false);

const messageToDelete = ref(null);
const messageToRestore = ref(null);
const messageToForceDelete = ref(null);
const selectedStatus = ref("");

const statusOptions = ref([
    { value: "unread", label: "Unread" },
    { value: "read", label: "Read" },
    { value: "replied", label: "Replied" },
    { value: "spam", label: "Spam" },
]);

const tableHeaders = ref([
    { key: "name", label: "Name" },
    { key: "email", label: "Email" },
    { key: "subject", label: "Subject" },
    { key: "status", label: "Status" },
    { key: "created_at", label: "Date" },
    { key: "actions", label: "Actions" },
]);

const trashedTableHeaders = ref([
    { key: "name", label: "Name" },
    { key: "email", label: "Email" },
    { key: "subject", label: "Subject" },
    { key: "deleted_at", label: "Deleted At" },
    { key: "actions", label: "Actions" },
]);

const hasActiveFilters = computed(() => {
    return messagesStore.filters.search || messagesStore.filters.status;
});

const tableRows = computed(() => {
    return messagesStore.messages.map((message) => ({
        id: message.id,
        name: message.name,
        email: message.email,
        subject: message.subject,
        status: {
            type: "status",
            value: message.status,
            class: getStatusColor(message.status),
        },
        created_at: new Date(message.created_at).toLocaleString(),
        actions: [
            {
                label: "View",
                icon: "eye",
                class: "bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300",
                onClick: () => handleView(message),
            },
            {
                label: "Update Status",
                icon: "edit",
                class: "bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300",
                onClick: () => handleStatusClick(message),
            },
            {
                label: "Delete",
                icon: "trash",
                class: "bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300",
                onClick: () => handleDeleteClick(message),
            },
        ],
    }));
});

const trashedTableRows = computed(() => {
    return messagesStore.trashedMessages.map((message) => ({
        id: message.id,
        name: message.name,
        email: message.email,
        subject: message.subject,
        deleted_at: new Date(message.deleted_at).toLocaleString(),
        actions: [
            {
                label: "Restore",
                class: "flex items-center gap-1 px-3 py-1 rounded-lg bg-green-600 text-white hover:bg-green-800 dark:bg-green-900/30 dark:text-white transition-colors duration-200",
                onClick: () => handleRestoreClick(message),
            },
            {
                label: "Delete Permanently",
                class: "flex items-center gap-1 px-3 py-1 rounded-lg bg-red-600 text-white hover:bg-red-800 dark:bg-red-900/30 dark:text-white transition-colors duration-200",
                onClick: () => handleForceDeleteClick(message),
            },
        ],
    }));
});

const getStatusColor = (status) => {
    switch (status) {
        case "unread":
            return "bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300 p-1 rounded";
        case "read":
            return "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 p-1 rounded";
        case "replied":
            return "bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 p-1 rounded";
        case "spam":
            return "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 p-1 rounded";
        default:
            return "bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-300 p-1 rounded";
    }
};

const messageDetailSections = computed(() => {
    const msg = messagesStore.currentMessage;
    if (!msg) return [];

    return [
        {
            title: "Sender Information",
            fields: [
                { label: "Name", value: msg.name },
                { label: "Email", value: msg.email },
                { label: "Phone", value: msg.phone },
                { label: "IP Address", value: msg.ip_address },
            ],
        },
        {
            title: "Message Details",
            fields: [
                { label: "Subject", value: msg.subject },
                { label: "Message", value: msg.message, type: "richtext" },
                { label: "Status", value: msg.status },
                { label: "Created At", value: msg.created_at },
            ],
        },
    ];
});

const handleSearch = (searchTerm) => {
    messagesStore.setFilter("search", searchTerm);
};

const handleStatusFilter = (value) => {
    messagesStore.setFilter("status", value);
};

const clearAllFilters = () => {
    messagesStore.clearFilters();
};

const handlePageChange = (page) => {
    if (showTrashed.value) {
        messagesStore.fetchTrashedMessages(
            page,
            messagesStore.pagination.per_page,
        );
    } else {
        messagesStore.fetchMessages(page, messagesStore.pagination.per_page);
    }
};

const handlePerPageChange = (perPage) => {
    const perPageNumber = parseInt(perPage);
    messagesStore.pagination.per_page = perPageNumber;
    messagesStore.pagination.current_page = 1;
    if (showTrashed.value) {
        messagesStore.fetchTrashedMessages(1, perPageNumber);
    } else {
        messagesStore.fetchMessages(1, perPageNumber);
    }
};

const fetchMessages = async () => {
    await messagesStore.fetchMessages(
        messagesStore.pagination.current_page,
        messagesStore.pagination.per_page,
    );
};

const handleShowTrashed = async () => {
    showTrashed.value = true;
    await messagesStore.fetchTrashedMessages(
        messagesStore.pagination.current_page,
        messagesStore.pagination.per_page,
    );
};

const handleView = async (message) => {
    try {
        await messagesStore.fetchMessage(message.id);
        showDetailModal.value = true;
    } catch (error) {
        console.error("Error loading message:", error);
    }
};

const handleStatusClick = (message) => {
    messageToDelete.value = message;
    selectedStatus.value = message.status;
    showStatusModal.value = true;
};

const handleUpdateStatus = async () => {
    if (!messageToDelete.value || !selectedStatus.value) return;
    try {
        await messagesStore.updateMessageStatus(
            messageToDelete.value.id,
            selectedStatus.value,
        );
        showStatusModal.value = false;
        selectedStatus.value = "";
        messageToDelete.value = null;
    } catch (error) {
        console.error("Error updating status:", error);
    }
};

const handleDeleteClick = (message) => {
    messageToDelete.value = message;
    showDeleteConfirm.value = true;
};

const handleDelete = async () => {
    if (!messageToDelete.value) return;
    try {
        await messagesStore.deleteMessage(messageToDelete.value.id);
        showDeleteConfirm.value = false;
        messageToDelete.value = null;
    } catch (error) {
        console.error("Delete error:", error);
    }
};

const handleRestoreClick = (message) => {
    messageToRestore.value = message;
    showRestoreConfirm.value = true;
};

const handleRestore = async () => {
    if (!messageToRestore.value) return;
    try {
        await messagesStore.restoreMessage(messageToRestore.value.id);
        showRestoreConfirm.value = false;
        messageToRestore.value = null;
    } catch (error) {
        console.error("Restore error:", error);
    }
};

const handleForceDeleteClick = (message) => {
    messageToForceDelete.value = message;
    showForceDeleteConfirm.value = true;
};

const handleForceDelete = async () => {
    if (!messageToForceDelete.value) return;
    try {
        await messagesStore.forceDeleteMessage(messageToForceDelete.value.id);
        showForceDeleteConfirm.value = false;
        messageToForceDelete.value = null;
    } catch (error) {
        console.error("Force delete error:", error);
    }
};

onMounted(() => {
    fetchMessages();
});
</script>

<style scoped>
.animate-in {
    animation: slide-in-from-top 0.3s ease-out forwards;
}

@keyframes slide-in-from-top {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 640px) {
    .grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .p-3 {
        padding: 0.75rem;
    }
    .text-sm {
        font-size: 0.875rem;
    }
    .text-xs {
        font-size: 0.75rem;
    }
}

@media (min-width: 640px) {
    .sm\:grid-cols-4 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
    .sm\:p-4 {
        padding: 1rem;
    }
    .sm\:text-2xl {
        font-size: 1.5rem;
    }
}

@media (min-width: 768px) {
    .md\:p-6 {
        padding: 1.5rem;
    }
    .md\:text-3xl {
        font-size: 1.875rem;
    }
}
</style>
