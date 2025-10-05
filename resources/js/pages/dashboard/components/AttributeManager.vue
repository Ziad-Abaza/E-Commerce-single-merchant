<!-- resources/js/pages/dashboard/components/AttributeManager.vue -->
<template>
    <div class="space-y-4">
        <div
            v-for="(attr, attrIndex) in modelValue"
            :key="attr.id"
            class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-700/30"
        >
            <!-- Attribute Header -->
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-medium text-gray-900 dark:text-white">
                    {{ attr.name || "Unnamed Attribute" }}
                </h4>
                <button
                    type="button"
                    @click="removeAttribute(attrIndex)"
                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                >
                    <svg
                        class="w-5 h-5"
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

            <!-- Attribute Type Info -->
            <div class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                Type: <span class="font-mono">{{ attr.type }}</span>
            </div>

            <!-- Values Section -->
            <div class="space-y-2">
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Values
                </label>
                <div
                    v-for="(val, valIndex) in attr.values"
                    :key="val.id"
                    class="flex items-center gap-2"
                >
                    <input
                        v-model="val.value"
                        type="text"
                        class="flex-1 rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Enter value"
                    />
                    <div class="flex items-center space-x-2">
                        <!-- Visible Switch -->
                        <label class="flex items-center cursor-pointer">
                            <input
                                v-model="val.is_visible"
                                type="checkbox"
                                class="sr-only peer"
                            />
                            <div
                                class="relative w-10 h-6 bg-gray-200 dark:bg-gray-600 rounded-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white"
                            ></div>
                            <span
                                class="ml-2 text-xs text-gray-500 dark:text-gray-400"
                                >Visible</span
                            >
                        </label>

                        <!-- Variant Switch -->
                        <label class="flex items-center cursor-pointer">
                            <input
                                v-model="val.is_variant"
                                type="checkbox"
                                class="sr-only peer"
                            />
                            <div
                                class="relative w-10 h-6 bg-gray-200 dark:bg-gray-600 rounded-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white"
                            ></div>
                            <span
                                class="ml-2 text-xs text-gray-500 dark:text-gray-400"
                                >Variant</span
                            >
                        </label>

                        <!-- Filterable Switch -->
                        <label class="flex items-center cursor-pointer">
                            <input
                                v-model="val.is_filterable"
                                type="checkbox"
                                class="sr-only peer"
                            />
                            <div
                                class="relative w-10 h-6 bg-gray-200 dark:bg-gray-600 rounded-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white"
                            ></div>
                            <span
                                class="ml-2 text-xs text-gray-500 dark:text-gray-400"
                                >Filterable</span
                            >
                        </label>
                    </div>

                    <button
                        type="button"
                        @click="removeValue(attrIndex, valIndex)"
                        class="p-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
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
                <button
                    type="button"
                    @click="addValue(attrIndex)"
                    class="mt-2 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center"
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
                    Add Value
                </button>
            </div>
        </div>

        <!-- Add Attribute Button -->
        <div class="flex items-center gap-2">
            <select
                v-model="selectedAttribute"
                class="flex-1 border border-gray-300 dark:border-gray-600 rounded-md p-2 dark:bg-gray-700 dark:text-white"
            >
                <option value="">Select attribute...</option>
                <option
                    v-for="attr in props.availableAttributes"
                    :key="attr.id"
                    :value="attr.id"
                >
                    {{ attr.name }}
                </option>
            </select>

            <button
                type="button"
                @click="addAttribute"
                class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
                Add
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    modelValue: {
        type: Array,
        required: true,
    },
    availableAttributes: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["update:modelValue"]);
const selectedAttribute = ref("");

// Generate unique ID
const generateId = () => Date.now() + Math.random().toString(36).substr(2, 9);

// Add new attribute

const addAttribute = () => {
    const attr = props.availableAttributes.find(
        (a) => a.id === selectedAttribute.value,
    );
    if (!attr) return;

    const newAttr = {
        id: generateId(),
        name: attr.name,
        code: attr.code,
        type: attr.type || "select",
        values: [],
    };

    emit("update:modelValue", [...props.modelValue, newAttr]);
    selectedAttribute.value = "";
};

// Remove attribute
const removeAttribute = (index) => {
    const updated = [...props.modelValue];
    updated.splice(index, 1);
    emit("update:modelValue", updated);
};

// Add value to attribute
const addValue = (attrIndex) => {
    const updated = [...props.modelValue];
    updated[attrIndex].values = [
        ...(updated[attrIndex].values || []),
        {
            id: generateId(),
            value: "",
            is_visible: true,
            is_variant: false,
            is_filterable: true,
        },
    ];
    emit("update:modelValue", updated);
};

// Remove value from attribute
const removeValue = (attrIndex, valIndex) => {
    const updated = [...props.modelValue];
    updated[attrIndex].values.splice(valIndex, 1);
    emit("update:modelValue", updated);
};

// Sync changes from parent
watch(
    () => props.modelValue,
    (newVal) => {
        emit("update:modelValue", newVal);
    },
    { deep: true },
);
</script>
