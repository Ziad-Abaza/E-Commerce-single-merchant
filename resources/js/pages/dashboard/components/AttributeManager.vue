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
        // If we have a direct value and value_type (new format)
        if ('value' in attr && 'value_type' in attr) {
            // Try to find the attribute in availableAttributes by ID or name
            const attributeInfo = props.availableAttributes.find(a => 
                a.id === attr.id || a.name === attr.name
            ) || {};
            
            return {
                id: attr.id || attributeInfo.id || undefined,
                name: attr.name || attributeInfo.name || '',
                code: attributeInfo.code || '',
                type: attr.value_type || attributeInfo.type || 'text',
                value: attr.value,
            };
        }
        
        // Handle old format or fallback
        const attributeInfo = props.availableAttributes.find(a => 
            a.id === attr.id || a.name === attr.name
        ) || {};
        
        return {
            id: attr.id || attributeInfo.id || undefined,
            name: attr.name || attributeInfo.name || '',
            code: attr.code || attributeInfo.code || '',
            type: attr.type || attributeInfo.type || 'text',
            value: attr.values?.[0]?.value || attr.value || '',
        };
    });
};

// Transform internal format back to parent format
const transformToParent = (attributes) => {
    return attributes
        .filter(attr => {
            // Include attributes that have a value (id is not strictly required)
            const hasValue = attr && (attr.value || attr.value === 0 || attr.value === '');
            if (!hasValue) {
                console.warn('Skipping invalid attribute (missing value):', attr);
                return false;
            }
            return true;
        })
        .map(attr => {
            // Try to find the attribute in availableAttributes by ID or name
            const attrDef = props.availableAttributes.find(a => 
                a.id === attr.id || a.name === attr.name
            ) || {};
            
            // Use the ID from availableAttributes if we matched by name
            const attributeId = attr.id || attrDef.id;
            
            if (!attributeId) {
                console.warn('No ID found for attribute:', attr);
            }
            
            return {
                id: attributeId ? Number(attributeId) : null,
                name: attr.name || attrDef.name || '',
                value: String(attr.value),
                value_type: attr.type || attrDef.type || 'text'
            };
        });
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
