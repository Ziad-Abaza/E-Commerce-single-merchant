<!-- resources/js/pages/dashboard/Settings.vue -->
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
                        Settings Management
                    </h1>
                    <p
                        class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1"
                    >
                        Configure application settings and preferences
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <!-- Save Changes Button -->
                    <button
                        v-if="settingsStore.hasUnsavedChanges"
                        @click="saveAllChanges"
                        :disabled="settingsStore.saving"
                        class="px-3 sm:px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm disabled:opacity-50"
                    >
                        <svg
                            v-if="settingsStore.saving"
                            class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            ></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                        {{
                            settingsStore.saving ? "Saving..." : "Save Changes"
                        }}
                    </button>

                    <!-- Discard Changes Button -->
                    <button
                        v-if="settingsStore.hasUnsavedChanges"
                        @click="discardChanges"
                        class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
                    >
                        Discard Changes
                    </button>

                    <!-- Add New Setting Button -->
                    <button
                        @click="showCreateModal = true"
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
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                        Add Setting
                    </button>
                </div>
            </div>
        </div>

        <!-- Error Alert -->
        <div
            v-if="settingsStore.error"
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
                    settingsStore.error
                }}</span>
                <button
                    @click="settingsStore.clearError"
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

        <!-- Settings Groups Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8 overflow-x-auto">
                <button
                    v-for="group in settingsStore.groups"
                    :key="group"
                    @click="settingsStore.setSelectedGroup(group)"
                    :class="[
                        'py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap',
                        settingsStore.selectedGroup === group
                            ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                    ]"
                >
                    {{ formatGroupName(group) }}
                </button>
            </nav>
        </div>

        <!-- Settings Content -->
        <div class="space-y-6">
            <!-- Loading State -->
            <div v-if="settingsStore.loading" class="flex justify-center py-12">
                <div
                    class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
                ></div>
            </div>

            <!-- Settings Form -->
            <div v-else-if="currentGroupSettings.length > 0" class="space-y-6">
                <div
                    v-for="setting in currentGroupSettings"
                    :key="setting.id"
                    class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 shadow-sm border border-gray-200 dark:border-gray-700"
                >
                    <div
                        class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4"
                    >
                        <div class="flex-1">
                            <label
                                :for="`setting-${setting.id}`"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-1"
                            >
                                {{ setting.label }}
                                <span
                                    v-if="setting._isDirty"
                                    class="text-orange-500 ml-1"
                                    >*</span
                                >
                            </label>
                            <p
                                v-if="setting.description"
                                class="text-xs text-gray-500 dark:text-gray-400 mb-3"
                            >
                                {{ setting.description }}
                            </p>

                            <!-- Text Input -->
                            <input
                                v-if="setting.type === 'text'"
                                :id="`setting-${setting.id}`"
                                v-model="setting.value"
                                @input="markDirty(setting)"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            />

                            <!-- Textarea -->
                            <textarea
                                v-else-if="setting.type === 'textarea'"
                                :id="`setting-${setting.id}`"
                                v-model="setting.value"
                                @input="markDirty(setting)"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            ></textarea>

                            <!-- Number Input -->
                            <input
                                v-else-if="setting.type === 'number'"
                                :id="`setting-${setting.id}`"
                                v-model.number="setting.value"
                                @input="markDirty(setting)"
                                type="number"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            />

                            <!-- Boolean Toggle -->
                            <div
                                v-else-if="setting.type === 'boolean'"
                                class="flex items-center"
                            >
                                <input
                                    :id="`setting-${setting.id}`"
                                    v-model="setting.value"
                                    type="checkbox"
                                    true-value="1"
                                    false-value="0"
                                    @change="markDirty(setting)"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                />
                                <label
                                    :for="`setting-${setting.id}`"
                                    class="ml-2 text-sm text-gray-900 dark:text-white"
                                >
                                    Enable this setting
                                </label>
                            </div>

                            <!-- Select Dropdown -->
                            <select
                                v-else-if="
                                    setting.type === 'select' && setting.options
                                "
                                :id="`setting-${setting.id}`"
                                v-model="setting.value"
                                @change="markDirty(setting)"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                                <option
                                    v-for="option in setting.options"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>

                            <!-- File Upload -->
                            <div
                                v-else-if="
                                    setting.type === 'file' ||
                                    setting.type === 'image'
                                "
                            >
                                <div class="flex items-center gap-4">
                                    <div class="flex-1">
                                        <input
                                            :id="`setting-${setting.id}`"
                                            @change="
                                                handleFileUpload(
                                                    setting,
                                                    $event,
                                                )
                                            "
                                            type="file"
                                            :accept="
                                                setting.type === 'image'
                                                    ? 'image/*'
                                                    : '*'
                                            "
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                        />
                                        <p
                                            v-if="setting.value"
                                            class="mt-2 text-sm text-gray-600 dark:text-gray-400"
                                        >
                                            Current: {{ setting.value }}
                                        </p>
                                    </div>
                                    <div
                                        v-if="
                                            setting.value &&
                                            setting.type === 'image'
                                        "
                                        class="flex-shrink-0"
                                    >
                                        <img
                                            :src="setting.file_url"
                                            :alt="setting.label"
                                            class="h-16 w-16 object-cover rounded-md border border-gray-200 dark:border-gray-600"
                                        />
                                    </div>
                                </div>
                                <p
                                    v-if="setting.type === 'image'"
                                    class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Recommended size: 512x512px (for logos), Max
                                    size: 2MB
                                </p>
                            </div>
                        </div>

                        <!-- Setting Actions -->
                        <div class="flex gap-2">
                            <button
                                @click="editSetting(setting)"
                                class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                title="Edit Setting"
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
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                    />
                                </svg>
                            </button>
                            <button
                                @click="deleteSetting(setting)"
                                class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                                title="Delete Setting"
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
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <svg
                    class="mx-auto h-12 w-12 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                    />
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                    />
                </svg>
                <h3
                    class="mt-2 text-sm font-medium text-gray-900 dark:text-white"
                >
                    No settings
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Get started by creating a new setting for this group.
                </p>
                <div class="mt-6">
                    <button
                        @click="showCreateModal = true"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <svg
                            class="-ml-1 mr-2 h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                        Add Setting
                    </button>
                </div>
            </div>
        </div>

        <!-- Create/Edit Setting Modal -->
        <SettingModal
            :show="showCreateModal || showEditModal"
            :setting="editingSetting"
            :is-editing="showEditModal"
            @close="closeModal"
            @save="handleSaveSetting"
        />

        <!-- Delete Confirmation Modal -->
        <ConfirmModal
            :show="showDeleteConfirm"
            title="Delete Setting"
            :message="`Are you sure you want to delete the setting '${settingToDelete?.label}'? This action cannot be undone.`"
            confirm-text="Delete"
            confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
            @confirm="handleDeleteSetting"
            @cancel="showDeleteConfirm = false"
            :loading="settingsStore.deleting"
        />

        <!-- Upload Progress Modal -->
        <div
            v-if="showUploadModal"
            class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 w-full max-w-sm text-center"
            >
                <h3
                    class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                >
                    File Upload
                </h3>

                <!-- Progress bar -->
                <div
                    class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden mb-4"
                >
                    <div
                        class="bg-blue-600 h-3 transition-all duration-300"
                        :style="{ width: uploadProgress + '%' }"
                    ></div>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                    {{ uploadMessage }}
                </p>

                <!-- Success / Error icon -->
                <div v-if="uploadStatus !== 'uploading'" class="mt-3">
                    <span
                        v-if="uploadStatus === 'success'"
                        class="text-green-600 font-medium"
                    >
                        ✅ Upload completed!
                    </span>
                    <span
                        v-else-if="uploadStatus === 'error'"
                        class="text-red-600 font-medium"
                    >
                        ❌ Upload failed
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useSettingsStore } from "../../stores/dashboard/settings";
import { toast } from "vue3-toastify";
import axios from "axios";
import { useAuthStore } from "../../stores/auth";
import SettingModal from "./components/SettingModal.vue";
import ConfirmModal from "./components/ConfirmModal.vue";

const settingsStore = useSettingsStore();
const authStore = useAuthStore();

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteConfirm = ref(false);
const editingSetting = ref(null);
const settingToDelete = ref(null);

const showUploadModal = ref(false);
const uploadProgress = ref(0);
const uploadMessage = ref("Preparing upload...");
const uploadStatus = ref("uploading");

// Check permissions
if (!authStore.hasPermission("manage_settings")) {
    throw new Error(
        "Access denied: You do not have permission to manage settings",
    );
}

// Computed properties
const currentGroupSettings = computed(() => {
    return settingsStore.getSettingsByGroup(settingsStore.selectedGroup);
});

// Methods
const formatGroupName = (group) => {
    return group
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
};

const markDirty = (setting) => {
    setting._isDirty = true;
};

const saveAllChanges = async () => {
    try {
        await settingsStore.saveAllChanges();
    } catch (error) {
        console.error("Save error:", error);
    }
};

const discardChanges = () => {
    settingsStore.discardChanges();
};

const editSetting = (setting) => {
    editingSetting.value = { ...setting };
    showEditModal.value = true;
};

const deleteSetting = (setting) => {
    settingToDelete.value = setting;
    showDeleteConfirm.value = true;
};

const handleDeleteSetting = async () => {
    if (!settingToDelete.value) return;

    try {
        await settingsStore.deleteSetting(settingToDelete.value.id);
        showDeleteConfirm.value = false;
        settingToDelete.value = null;
    } catch (error) {
        console.error("Delete error:", error);
    }
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    editingSetting.value = null;
};

const handleSaveSetting = async (settingData) => {
    try {
        if (showEditModal.value) {
            await settingsStore.updateSetting(
                editingSetting.value.id,
                settingData,
            );
        } else {
            await settingsStore.createSetting(settingData);
        }
        closeModal();
    } catch (error) {
        console.error("Save setting error:", error);
    }
};

const handleFileUpload = async (setting, event) => {
    const file = event.target.files[0];
    if (!file) return;

    // Validation
    if (file.size > 5 * 1024 * 1024) {
        alert("File too large. Max size 5MB.");
        event.target.value = "";
        return;
    }
    if (setting.type === "image" && !file.type.startsWith("image/")) {
        alert("Please upload a valid image file.");
        event.target.value = "";
        return;
    }

    try {
        showUploadModal.value = true;
        uploadProgress.value = 0;
        uploadStatus.value = "uploading";
        uploadMessage.value = "Uploading file...";

        settingsStore.updateSetting(
            setting.id,
            { ...setting, file },
            (progressEvent) => {
                const percentCompleted = Math.round(
                    (progressEvent.loaded * 100) / progressEvent.total,
                );
                uploadProgress.value = percentCompleted;
                uploadMessage.value = `Uploading: ${percentCompleted}%`;
            },
        );

        uploadStatus.value = "success";
        uploadMessage.value = "Upload completed successfully 🎉";

        setTimeout(() => {
            showUploadModal.value = false;
        }, 2000);
    } catch (error) {
        uploadStatus.value = "error";
        uploadMessage.value =
            error.response?.data?.message || error.message || "Upload failed";

        setTimeout(() => {
            showUploadModal.value = false;
        }, 3000);
    } finally {
        event.target.value = "";
    }
};

// Lifecycle
onMounted(async () => {
    await settingsStore.fetchSettings();
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

/* Mobile responsive adjustments */
@media (max-width: 640px) {
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
</style>
