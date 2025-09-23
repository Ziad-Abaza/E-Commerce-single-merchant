<!-- resources/js/pages/dashboard/Roles.vue -->
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
            Roles & Permissions Management
          </h1>
          <p
            class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1"
          >
            Manage user roles and assign permissions
          </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
          <button
            @click="fetchRoles"
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
          <button
            @click="openCreateForm"
            class="px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
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
                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
              />
            </svg>
            Add Role
          </button>
        </div>
      </div>
    </div>

    <!-- Error Alert -->
    <div
      v-if="rolesStore.error"
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
          rolesStore.error
        }}</span>
        <button
          @click="rolesStore.clearError"
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
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
      <!-- Total Roles -->
      <div
        class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300"
      >
        <div class="flex items-center justify-between">
          <div>
            <p
              class="text-gray-500 dark:text-gray-400 text-xs font-medium"
            >
              Total Roles
            </p>
            <p
              class="text-lg font-bold text-gray-900 dark:text-white mt-1"
            >
              {{ rolesStore.statistics?.total_roles }}
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
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
              />
            </svg>
          </div>
        </div>
      </div>

      <!-- Protected Roles -->
      <div
        class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300"
      >
        <div class="flex items-center justify-between">
          <div>
            <p
              class="text-gray-500 dark:text-gray-400 text-xs font-medium"
            >
              Protected Roles
            </p>
            <p
              class="text-lg font-bold text-red-600 dark:text-red-400 mt-1"
            >
              {{ rolesStore.statistics?.protected_roles }}
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
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
              />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div
      v-if="rolesStore.loading && !rolesStore.roles.length"
      class="space-y-4"
    >
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

    <!-- Roles Table -->
    <div
      v-else
      class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
    >
      <!-- Empty State -->
      <div v-if="rolesStore.roles.length === 0" class="text-center py-12">
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
            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
          />
        </svg>
        <h3
          class="text-lg font-medium text-gray-900 dark:text-white mb-2"
        >
          No roles found
        </h3>
        <p class="text-gray-500 dark:text-gray-400 mb-4">
          Get started by adding your first role.
        </p>
        <button
          @click="openCreateForm"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
        >
          Add Your First Role
        </button>
      </div>

      <!-- Roles Table -->
      <div v-else>
        <Table :headers="tableHeaders" :rows="tableRows" />
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="rolesStore.roles.length > 0" class="flex justify-center">
      <Pagination
        :total="rolesStore.pagination.total"
        :current-page="rolesStore.pagination.current_page"
        :per-page="rolesStore.pagination.per_page"
        :last-page="rolesStore.pagination.last_page"
        @page-change="handlePageChange"
        @update:perPage="handlePerPageChange"
      />
    </div>

    <!-- Modals -->
    <!-- Create/Edit Form Modal -->
    <div
      v-if="showFormModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
      @click.self="showFormModal = false"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto"
      >
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h3
              class="text-xl font-bold text-gray-900 dark:text-white"
            >
              {{ isEditing ? "Edit Role" : "Add New Role" }}
            </h3>
            <button
              @click="showFormModal = false"
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

          <Form
            :model-fields="formFields"
            @submit="handleSubmitForm"
          />
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      :show="showDeleteConfirm"
      title="Delete Role"
      :message="`Are you sure you want to delete '${roleToDelete?.name || 'this role'}'? This action cannot be undone.`"
      confirm-text="Delete"
      confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
      @confirm="handleDelete"
      @cancel="showDeleteConfirm = false"
      :loading="rolesStore.deleting"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRolesStore } from "../../stores/dashboard/roles";
import { useAuthStore } from "../../stores/auth";
import Table from "./components/Table.vue";
import Pagination from "./components/Pagination.vue";
import Form from "./components/Form.vue";
import ConfirmModal from "./components/ConfirmModal.vue";

const rolesStore = useRolesStore();
const authStore = useAuthStore();

const showFormModal = ref(false);
const isEditing = ref(false);
const showDeleteConfirm = ref(false);
const roleToDelete = ref(null);
const formFields = ref([]);

// Check permissions
if (!authStore.hasPermission("manage_roles")) {
  throw new Error(
    "Access denied: You do not have permission to manage roles",
  );
}

// Table headers
const tableHeaders = ref([
  { key: "name", label: "Role Name" },
  { key: "permissions_count", label: "Permissions" },
  { key: "protected", label: "Protected" },
  { key: "actions", label: "Actions" },
]);

// Computed properties
const tableRows = computed(() => {
  return rolesStore.roles.map((role) => ({
    id: role.id,
    name: role.name,
    permissions_count: role.permissions?.length || 0,
    protected:
      role.name === "owner" || role.name === "admin" ? "Yes" : "No",
    actions: [
      {
        label: "Edit",
        icon: "edit",
        class: "bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300",
        onClick: () => handleEdit(role),
      },
      {
        label: "Delete",
        icon: "trash",
        class: "bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300",
        onClick: () => handleDeleteClick(role),
        disabled: role.name === "owner" || role.name === "admin",
      },
    ],
  }));
});

// Event handlers
const handlePageChange = (page) => {
  rolesStore.fetchRoles(page, rolesStore.pagination.per_page);
};

const handlePerPageChange = (perPage) => {
  const perPageNumber = parseInt(perPage);
  rolesStore.pagination.per_page = perPageNumber;
  rolesStore.pagination.current_page = 1;
  rolesStore.fetchRoles(1, perPageNumber);
};

const fetchRoles = async () => {
  await rolesStore.fetchRoles(
    rolesStore.pagination.current_page,
    rolesStore.pagination.per_page,
  );
};

const openCreateForm = async () => {
  isEditing.value = false;
  roleToDelete.value = null;

  // Ensure permissions are loaded
  if (!rolesStore.permissions || rolesStore.permissions.length === 0) {
    try {
      await rolesStore.fetchRoles(1, 20);
    } catch (error) {
      console.error("Error loading permissions:", error);
      return;
    }
  }

  initializeFormFields(null);
  showFormModal.value = true;
};

const handleEdit = async (role) => {
  isEditing.value = true;
  roleToDelete.value = role;

  try {
    // Ensure permissions are loaded before fetching role details
    if (!rolesStore.permissions || rolesStore.permissions.length === 0) {
      await rolesStore.fetchRoles(1, 20);
    }

    await rolesStore.fetchRole(role.id);
    initializeFormFields(rolesStore.currentRole);
    showFormModal.value = true;
  } catch (error) {
    console.error("Error loading role for edit:", error);
  }
};

const initializeFormFields = (role) => {
  // Validate that permissions exist
  if (!rolesStore.permissions || rolesStore.permissions.length === 0) {
    console.warn("Permissions list is empty or not loaded yet");
    formFields.value = [
      {
        id: "name",
        label: "Role Name",
        type: "text",
        value: role?.name || "",
        required: true,
        placeholder: "Enter role name (e.g., editor, moderator)",
        help: "Role names should be unique and descriptive",
      },
      {
        id: "permissions",
        label: "Permissions",
        type: "multipleselect",
        value: [],
        options: [],
        required: false,
        placeholder: "No permissions available",
        disabled: true
      }
    ];
    return;
  }

  // Get all permissions as options
  const permissionOptions = rolesStore.permissions.map((permission) => ({
    value: Number(permission.id),
    label: permission.name
      .replace(/_/g, " ")
      .replace(/\b\w/g, (l) => l.toUpperCase()),
  }));

  // Get selected permissions for the role
  const selectedPermissions = role?.permissions?.map(p => Number(p.id)) || [];

  formFields.value = [
    {
      id: "name",
      label: "Role Name",
      type: "text",
      value: role?.name || "",
      required: true,
      placeholder: "Enter role name (e.g., editor, moderator)",
      help: "Role names should be unique and descriptive",
    },
    {
      id: "permissions",
      label: "Permissions",
      type: "multipleselect",
      value: selectedPermissions,
      options: permissionOptions,
      required: false,
      placeholder: "Select permissions for this role",
    },
  ];
};

const handleDeleteClick = (role) => {
  // Check if role is protected
  if (role.name === "owner" || role.name === "admin") {
    alert("Protected roles cannot be deleted.");
    return;
  }

  roleToDelete.value = role;
  showDeleteConfirm.value = true;
};

const handleSubmitForm = async (data) => {
  try {
    // Prepare data for API
    const roleData = {
      name: data.name,
      permissions: data.permissions || [],
    };

    if (isEditing.value && roleToDelete.value) {
      await rolesStore.updateRole(roleToDelete.value.id, roleData);
    } else {
      await rolesStore.createRole(roleData);
    }

    // Refresh the list
    await fetchRoles();

    // Close the form
    showFormModal.value = false;
    formFields.value = [];
    roleToDelete.value = null;
  } catch (error) {
    console.error("Form submission error:", error);
  }
};

const handleDelete = async () => {
  if (!roleToDelete.value) return;

  try {
    await rolesStore.deleteRole(roleToDelete.value.id);
    showDeleteConfirm.value = false;
    roleToDelete.value = null;
  } catch (error) {
    console.error("Delete error:", error);
  }
};

// Lifecycle
onMounted(async () => {
  await fetchRoles();
});
</script>

<style scoped>
/* Add smooth animations */
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

.animate-in {
  animation: slide-in-from-top 0.3s ease-out forwards;
}

/* Improve card hover effects */
.group:hover .text-gray-900 {
  color: #1f2937;
}

.group:hover .dark\:text-white {
  color: #f9fafb;
}

/* Mobile responsive adjustments */
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
