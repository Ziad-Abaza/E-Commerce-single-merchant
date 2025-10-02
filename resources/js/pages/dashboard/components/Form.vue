<template>
    <form @submit.prevent="submitForm" @click.stop class="space-y-6">
        <div
            v-for="(field, index) in fields"
            :key="field.id"
            :ref="(el) => (fieldRefs[index] = el)"
            v-show="
                !field.dependsOn ||
                (field.dependsOnValue !== undefined
                    ? fields.find((f) => f.id === field.dependsOn)?.value ===
                      field.dependsOnValue
                    : fields.find((f) => f.id === field.dependsOn)?.value)
            "
            class="space-y-2"
        >
            <label
                :for="field.id"
                class="block text-sm font-medium text-gray-700"
            >
                {{ field.label }}
                <span v-if="field.required" class="text-red-500">*</span>
            </label>

            <!-- Rich Text Editor -->
            <RichTextEditor
                v-if="field.type === 'richtext'"
                v-model="field.value"
                :placeholder="field.placeholder"
            />

            <!-- Plain Textarea -->
            <textarea
                v-else-if="field.type === 'textarea'"
                v-model="field.value"
                :id="field.id"
                :placeholder="field.placeholder"
                :class="inputClass(field)"
                rows="4"
            ></textarea>

            <!-- Text / Number Input -->
            <input
                v-else-if="field.type === 'text' || field.type === 'number'"
                v-model="field.value"
                :id="field.id"
                :type="field.type"
                :placeholder="field.placeholder"
                :class="inputClass(field)"
            />

            <!-- Password Input -->
            <input
                v-else-if="field.type === 'password'"
                v-model="field.value"
                :id="field.id"
                type="password"
                :placeholder="field.placeholder"
                :class="inputClass(field)"
            />

            <!-- Select Input -->
            <Select
                v-else-if="field.type === 'select'"
                v-model="field.value"
                :id="field.id"
                :options="field.options || []"
                :placeholder="field.placeholder || 'Select an option'"
                label=""
            />

            <!-- Multiple Select Input -->
            <CheckboxSelect
                v-else-if="
                    field.type === 'multipleselect' ||
                    field.type === 'checkboxselect'
                "
                v-model="field.value"
                :id="field.id"
                :options="field.options || []"
                :label="field.label"
            />

            <!-- Checkbox Input -->
            <div
                v-else-if="field.type === 'checkbox'"
                class="flex items-center"
            >
                <input
                    :id="field.id"
                    v-model="field.value"
                    type="checkbox"
                    :required="field.required"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label :for="field.id" class="ml-2 block text-sm text-gray-900">
                    {{ field.label }}
                </label>
            </div>

            <!-- Autocomplete Input -->
            <div v-else-if="field.type === 'autocomplete'" class="w-full">
                <AutocompleteInput
                    v-model="field.value"
                    :options="field.options || []"
                    :placeholder="field.placeholder || 'Type to search or add new...'"
                    :allow-new="field.allowNew !== false"
                    @add-new="(newOption) => {
                        if (field.onAddNew) {
                            field.onAddNew(newOption);
                        } else if (field.options) {
                            field.options.push(newOption);
                        }
                    }"
                />
            </div>

            <!-- Date Input -->
            <input
                v-else-if="field.type === 'date'"
                v-model="field.value"
                :id="field.id"
                type="date"
                :required="field.required"
                :class="inputClass(field)"
            />

            <!-- Hidden Input -->
            <input
                v-else-if="field.type === 'hidden'"
                v-model="field.value"
                :id="field.id"
                type="hidden"
            />

            <!-- File Input (single or multiple) -->
            <FileDropzone
                v-else-if="field.type === 'file' || field.type === 'files'"
                v-model="field.value"
                :multiple="field.type === 'files'"
                :previous-value="field.previousValue"
                :accepted-types="field.acceptedTypes || 'image/*'"
            />

            <!-- Dynamic Fields (Meta Keys / Values) -->
            <div v-if="field.type === 'dynamicFields'">
                <div
                    v-for="(item, idx) in field.fields"
                    :key="idx"
                    class="flex gap-2 items-center mb-2"
                >
                    <input
                        v-model="item.key"
                        type="text"
                        placeholder="Key"
                        class="block w-1/2 border border-gray-300 rounded-md p-2"
                    />
                    <input
                        v-model="item.value"
                        type="text"
                        placeholder="Value"
                        class="block w-1/2 border border-gray-300 rounded-md p-2"
                    />
                    <button
                        type="button"
                        @click="field.fields.splice(idx, 1)"
                        class="px-2 py-1 bg-red-600 text-white rounded"
                    >
                        Delete
                    </button>
                </div>
                <button
                    type="button"
                    @click="field.fields.push({ key: '', value: '' })"
                    class="px-3 py-1 bg-blue-600 text-white rounded"
                >
                    Add Meta
                </button>
            </div>

            <!-- Dynamic Multi-Entity Input -->
            <div v-else-if="field.type === 'multiEntity'" class="space-y-3">
                <div
                    v-for="(item, idx) in field.value"
                    :key="item.id || item.value"
                    class="flex flex-wrap gap-3 items-center border p-2 rounded-md"
                >
                    <!-- Label -->
                    <span class="flex-1">{{ item.label }}</span>

                    <!-- Attributes -->
                    <div v-for="attr in field.attributes" :key="attr.name">
                        <!-- Select attribute -->
                        <select
                            v-if="attr.type === 'select'"
                            v-model="item[attr.name]"
                            class="px-2 py-1 border border-gray-300 rounded-lg text-sm dark:bg-gray-700 dark:text-white"
                        >
                            <option
                                v-for="opt in attr.options"
                                :key="opt.value"
                                :value="opt.value"
                            >
                                {{ opt.label }}
                            </option>
                        </select>

                        <!-- Text / Number attribute -->
                        <input
                            v-else-if="
                                attr.type === 'text' || attr.type === 'number'
                            "
                            v-model="item[attr.name]"
                            :type="attr.type"
                            class="px-2 py-1 border border-gray-300 rounded-lg text-sm dark:bg-gray-700 dark:text-white"
                        />
                    </div>

                    <!-- Remove button -->
                    <button
                        type="button"
                        @click="field.value.splice(idx, 1)"
                        class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700"
                    >
                        Remove
                    </button>
                </div>

                <!-- button -->
                <select
                    @change="addEntity($event, field)"
                    class="mt-2 px-3 py-2 border border-gray-300 rounded-lg text-sm dark:bg-gray-700 dark:text-white"
                >
                    <option value="">+ Add</option>
                    <option
                        v-for="opt in field.options"
                        :key="opt.value"
                        :value="opt.value"
                    >
                        {{ opt.label }}
                    </option>
                </select>
            </div>
        </div>

        <button
            type="submit"
            :disabled="isSubmitting"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-75 disabled:cursor-not-allowed flex items-center justify-center min-w-[100px]"
        >
            <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isSubmitting ? 'Submitting...' : 'Submit' }}
        </button>
    </form>
    <!-- UiToast component not available yet -->
    <!-- <UiToast ref="toastRef" /> -->
</template>

<script setup>
import { reactive, ref, watch, computed } from "vue";
import AutocompleteInput from "./AutocompleteInput.vue";

const isSubmitting = ref(false);
import FileDropzone from "./FileDropzone.vue";
import Select from "./Select.vue";
import CheckboxSelect from "./CheckboxSelect.vue";
import RichTextEditor from "./RichTextEditor.vue";
// Note: These UI components don't exist yet - commented out for now
// import UiToast from "@/components/ui/Toast.vue";

// const toastRef = ref(null); // Commented out since UiToast is not available

const emit = defineEmits(["submit"]);

defineExpose({
    setSubmitting: (value) => {
        isSubmitting.value = value;
    }
});
const props = defineProps({
    modelFields: { type: Array, required: true },
});

const fields = reactive([]);
const invalidFields = reactive(new Set());
const fieldRefs = ref([]);

// Watch for changes in modelFields and update fields dynamically
watch(
    () => props.modelFields,
    (newFields) => {
        // Clear existing fields
        fields.length = 0;

        // Deep clone each field to avoid reactivity issues
        newFields.forEach((field) => {
            const clonedField = JSON.parse(JSON.stringify(field));
            
            // If it's a multipleselect field, ensure options are reactive
            if (field.type === 'multipleselect' || field.type === 'checkboxselect') {
                clonedField.options = [...(field.options || [])];
            }
            
            fields.push(clonedField);
        });
    },
    { immediate: true, deep: true }
);

// Watch for changes in individual field values and sync back to parent
watch(
    () => fields.map(f => ({ id: f.id, value: f.value })),
    (newValues) => {
        newValues.forEach(({ id, value }) => {
            const index = props.modelFields.findIndex(f => f.id === id);
            if (index !== -1) {
                // Update the parent's modelFields
                props.modelFields[index].value = value;
            }
        });
    },
    { deep: true }
);

// Function to add a new entity
const addEntity = (event, field) => {
    const value = event.target.value;
    if (!value) return;

    const selected = field.options.find((o) => o.value === value);
    if (selected && !field.value.some((v) => v.value === value)) {
        const newItem = {
            id: value,
            value,
            label: selected.label,
        };

        if (field.attributes) {
            field.attributes.forEach((attr) => {
                newItem[attr.name] =
                    attr.default !== undefined
                        ? attr.default
                        : attr.type === "select" && attr.options.length
                          ? attr.options[0].value
                          : "";
            });
        }

        field.value.push(newItem);
    }

    event.target.value = "";
};

// Also watch for changes in individual field values to ensure reactivity
watch(
    () => props.modelFields.map((field) => field.value),
    () => {
        props.modelFields.forEach((field, index) => {
            if (fields[index] && field.value !== undefined) {
                fields[index].value = field.value;
            }
        });
    },
    { deep: true },
);

const inputClass = (field) =>
    [
        "block w-full border rounded-md shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500",
        field.type !== "textarea" ? "h-10" : "",
        invalidFields.has(field.id) ? "border-red-500" : "border-gray-300",
    ].join(" ");

const submitForm = async () => {
    if (isSubmitting.value) return; // Prevent multiple submissions
    
    const formData = {};
    let hasErrors = false;

    // Set loading state
    isSubmitting.value = true;

    try {
        // Reset all error states
        fields.forEach((field) => {
            field.error = "";
        });

        // Validate required fields
        fields.forEach((field) => {
            if (field.required && !field.value && field.value !== 0) {
                field.error = `${field.label} is required`;
                hasErrors = true;
            }
        });

        // If there are validation errors, scroll to the first error
        if (hasErrors) {
            const firstErrorIndex = fields.findIndex(
                (field) => field.error
            );
            if (firstErrorIndex !== -1 && fieldRefs.value[firstErrorIndex]) {
                fieldRefs.value[firstErrorIndex].scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                });
            }
            return;
        }

        // Prepare form data
        fields.forEach((field) => {
            formData[field.id] = field.value;
        });

        // Emit the form data
        emit("submit", formData);
    } catch (error) {
        console.error("Form submission error:", error);
    } finally {
        // The parent component should call setSubmitting(false) when done
        // to re-enable the form after successful submission or error handling
    }
};
</script>

<style scoped>
textarea {
    font-family: "Calibri", sans-serif;
    line-height: 1.5;
}
</style>
