<!-- resources/js/pages/dashboard/components/AttributeManager.vue -->
<template>
    <div class="space-y-4">
        <div
            v-for="(attr, attrIndex) in transformedAttributes"
            :key="attr.id || attrIndex"
            class="mb-6 p-4 border rounded-lg dark:border-gray-600"
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

            <!-- Value Section -->
            <div class="space-y-2">
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Value
                </label>
                <div class="flex items-center gap-2">
                    <input
                        v-model="attr.value"
                        type="text"
                        class="flex-1 rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        :placeholder="'Enter ' + (attr.name || 'value')"
                    />
                    <div class="flex items-center space-x-2">
                        <!-- Visible Switch -->
                        <label class="flex items-center cursor-pointer">
                            <input
                                v-model="attr.is_visible"
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
                                v-model="attr.is_variant"
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
                                v-model="attr.is_filterable"
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
                </div>
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
import { ref, watch, computed, onMounted } from "vue";

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    availableAttributes: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["update:modelValue"]);
const selectedAttribute = ref("");
const internalAttributes = ref([]);

// Transform attribute data from parent format to internal format
const transformToInternal = (attributes) => {
    return attributes.map(attr => {
        if ('value' in attr) return { ...attr };
        return {
            id: attr.id,
            name: attr.name,
            code: attr.code || '',
            type: attr.type || 'select',
            value: attr.values?.[0]?.value || '',
            is_visible: attr.values?.[0]?.is_visible ?? true,
            is_variant: attr.values?.[0]?.is_variant ?? false,
            is_filterable: attr.values?.[0]?.is_filterable ?? true
        };
    });
};

// Transform internal format back to parent format
const transformToParent = (attributes) => {
    return attributes.map(attr => ({
        id: attr.id,
        name: attr.name,
        code: attr.code,
        type: attr.type,
        values: [{
            value: attr.value,
            is_visible: attr.is_visible,
            is_variant: attr.is_variant,
            is_filterable: attr.is_filterable
        }]
    }));
};

// Initialize with props
onMounted(() => {
    internalAttributes.value = transformToInternal(props.modelValue);
});

// Watch for changes in modelValue from parent
watch(() => props.modelValue, (newVal) => {
    const currentAsParent = transformToParent(internalAttributes.value);
    if (JSON.stringify(newVal) !== JSON.stringify(currentAsParent)) {
        internalAttributes.value = transformToInternal(newVal);
    }
}, { deep: true });

// Watch for changes in internal state and emit updates
watch(internalAttributes, (newVal) => {
    const parentFormat = transformToParent(newVal);
    if (JSON.stringify(parentFormat) !== JSON.stringify(props.modelValue)) {
        emit('update:modelValue', parentFormat);
    }
}, { deep: true });

// Add new attribute
const addAttribute = () => {
    if (!selectedAttribute.value) return;

    const selected = props.availableAttributes.find(
        (attr) => attr.id === selectedAttribute.value,
    );

    if (!selected) return;

    const newAttribute = {
        id: selected.id,
        name: selected.name,
        code: selected.code || "",
        type: selected.type || "select",
        value: "",
        is_visible: true,
        is_variant: false,
        is_filterable: true
    };

    internalAttributes.value = [...internalAttributes.value, newAttribute];
    selectedAttribute.value = "";
};

// Remove attribute
const removeAttribute = (index) => {
    const updated = [...internalAttributes.value];
    updated.splice(index, 1);
    internalAttributes.value = updated;
};

// Expose transformed attributes for template
const transformedAttributes = computed(() => internalAttributes.value);
</script>
