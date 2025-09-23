import { defineStore } from "pinia";
import axios from "../../bootstrap";

export const useSettingsStore = defineStore("settings", {
    state: () => ({
        settings: [],
        groupedSettings: {},
        groups: [],
        loading: false,
        saving: false,
        deleting: false,
        error: null,
        selectedGroup: "general",
    }),

    getters: {
        getSettingsByGroup: (state) => (group) => {
            return state.groupedSettings[group] || [];
        },

        getSettingByKey: (state) => (key) => {
            return state.settings.find((setting) => setting.key === key);
        },

        getSettingValue:
            (state) =>
            (key, defaultValue = null) => {
                const setting = state.settings.find(
                    (setting) => setting.key === key,
                );
                return setting ? setting.typed_value : defaultValue;
            },

        hasUnsavedChanges: (state) => {
            return state.settings.some((setting) => setting._isDirty);
        },
    },

    actions: {
        async fetchSettings(group = null) {
            this.loading = true;
            this.error = null;

            try {
                const params = group ? { group } : {};
                const response = await axios.get("/dashboard/settings", {
                    params,
                });

                this.groupedSettings = response.data.data;
                this.groups = response.data.groups;

                // Flatten settings for easier access
                this.settings = Object.values(this.groupedSettings).flat();

                return response.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to fetch settings";
                console.error("[Settings Store] Error fetching settings:", err);
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async createSetting(settingData) {
            this.saving = true;
            this.error = null;

            try {
                const normalizedData = this.normalizeSettingData(settingData);
                console.log(normalizedData);
                const response = await axios.post(
                    "/dashboard/settings",
                    normalizedData,
                    { headers: { "Content-Type": "multipart/form-data" } },
                );

                // Add to local state
                this.settings.push(response.data.data);

                // Update grouped settings
                const group = response.data.data.group;
                if (!this.groupedSettings[group]) {
                    this.groupedSettings[group] = [];
                }
                this.groupedSettings[group].push(response.data.data);

                // Update groups if new
                if (!this.groups.includes(group)) {
                    this.groups.push(group);
                }

                return response.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to create setting";
                console.error("[Settings Store] Error creating setting:", err);
                throw err;
            } finally {
                this.saving = false;
            }
        },

        async updateSetting(id, settingData, onUploadProgress = null) {
            this.saving = true;
            this.error = null;

            try {
                const normalizedData = this.normalizeSettingData(settingData);
                console.log("Normalized Data:", normalizedData);
                const response = await axios.post(
                    `/dashboard/settings/${id}`,
                    normalizedData,
                    {
                        headers: { "Content-Type": "multipart/form-data" },
                        onUploadProgress,
                    },
                );

                // Update in local state
                const index = this.settings.findIndex((s) => s.id === id);
                if (index !== -1) {
                    this.settings[index] = response.data.data;
                    this.settings[index]._isDirty = false;
                }

                // Update in grouped settings
                Object.keys(this.groupedSettings).forEach((group) => {
                    const groupIndex = this.groupedSettings[group].findIndex(
                        (s) => s.id === id,
                    );
                    if (groupIndex !== -1) {
                        this.groupedSettings[group][groupIndex] =
                            response.data.data;
                    }
                });

                return response.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to update setting";
                console.error("[Settings Store] Error updating setting:", err);
                throw err;
            } finally {
                this.saving = false;
            }
        },

        async deleteSetting(id) {
            this.deleting = true;
            this.error = null;

            try {
                await axios.delete(`/dashboard/settings/${id}`);

                // Remove from local state
                this.settings = this.settings.filter((s) => s.id !== id);

                // Remove from grouped settings
                Object.keys(this.groupedSettings).forEach((group) => {
                    this.groupedSettings[group] = this.groupedSettings[
                        group
                    ].filter((s) => s.id !== id);
                });

                return true;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to delete setting";
                console.error("[Settings Store] Error deleting setting:", err);
                throw err;
            } finally {
                this.deleting = false;
            }
        },

        async bulkUpdateSettings(settingsData) {
            this.saving = true;
            this.error = null;

            try {
                const response = await axios.post(
                    "/dashboard/settings/bulk-update",
                    {
                        settings: settingsData,
                    },
                );

                // Update local state
                response.data.data.forEach((updatedSetting) => {
                    const index = this.settings.findIndex(
                        (s) => s.id === updatedSetting.id,
                    );
                    if (index !== -1) {
                        this.settings[index] = updatedSetting;
                        this.settings[index]._isDirty = false;
                    }
                });

                // Update grouped settings
                Object.keys(this.groupedSettings).forEach((group) => {
                    this.groupedSettings[group] = this.groupedSettings[
                        group
                    ].map((setting) => {
                        const updated = response.data.data.find(
                            (s) => s.id === setting.id,
                        );
                        return updated || setting;
                    });
                });

                return response.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to update settings";
                console.error(
                    "[Settings Store] Error bulk updating settings:",
                    err,
                );
                throw err;
            } finally {
                this.saving = false;
            }
        },

        normalizeSettingData(data) {
            const formData = new FormData();

            formData.append("key", data.key ?? "");
            formData.append("type", data.type ?? "text");
            formData.append("group", data.group ?? "general");
            formData.append(
                "label",
                data.label ?? data.key ?? "Unnamed Setting",
            );
            formData.append("description", data.description ?? "");
            formData.append("is_public", data.is_public === true ? 1 : 0);

            if (data.sort_order !== undefined) {
                formData.append("sort_order", data.sort_order.toString());
            }

            if (data.file) {
                formData.append("file", data.file);
            }

            if (data.options && Array.isArray(data.options)) {
                data.options.forEach((opt, index) => {
                    formData.append(`options[${index}]`, opt);
                });
            } else {
                formData.append("options[]", "");
            }

            formData.append("_method", "POST");

            return formData;
        },
        updateSettingValue(key, value) {
            const setting = this.settings.find((s) => s.key === key);
            if (setting) {
                setting.value = value;
                setting._isDirty = true;
            }
        },

        markSettingClean(key) {
            const setting = this.settings.find((s) => s.key === key);
            if (setting) {
                setting._isDirty = false;
            }
        },

        setSelectedGroup(group) {
            this.selectedGroup = group;
        },

        clearError() {
            this.error = null;
        },

        async saveAllChanges() {
            const dirtySettings = this.settings
                .filter((s) => s._isDirty)
                .map((s) => ({ key: s.key, value: s.value }));

            if (dirtySettings.length === 0) {
                return;
            }

            return await this.bulkUpdateSettings(dirtySettings);
        },

        discardChanges() {
            this.settings.forEach((setting) => {
                if (setting._isDirty) {
                    // Reset to original value (you might want to store original values)
                    setting._isDirty = false;
                }
            });
        },

        async fetchPublicSettings() {
            try {
                const response = await axios.get("/dashboard/settings/public");
                return response.data.data;
            } catch (err) {
                console.error(
                    "[Settings Store] Error fetching public settings:",
                    err,
                );
                throw err;
            }
        },
    },
});
