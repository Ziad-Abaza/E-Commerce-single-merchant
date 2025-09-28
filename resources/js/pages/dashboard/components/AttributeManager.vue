<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="text-sm font-medium text-gray-700">
        {{ label || 'Product Attributes' }}
        <span v-if="required" class="text-red-500">*</span>
      </h3>
      <button
        type="button"
        @click="addAttribute"
        class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Add Attribute
      </button>
    </div>

    <!-- Attributes List -->
    <div v-if="attributes.length > 0" class="space-y-4">
      <div v-for="(attr, index) in attributes" :key="index" class="p-4 border rounded-lg bg-gray-50">
        <div class="flex justify-between items-start mb-3">
          <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">Name</label>
              <input
                type="text"
                v-model="attr.name"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                placeholder="e.g. Color, Size"
                @input="updateAttribute(attr, 'name', $event.target.value)"
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
              <select
                v-model="attr.type"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              >
                <option value="select">Dropdown</option>
                <option value="radio">Radio Buttons</option>
                <option value="checkbox">Checkbox</option>
                <option value="text">Text Field</option>
              </select>
            </div>
            <div class="flex items-end">
              <button
                type="button"
                @click="removeAttribute(index)"
                class="text-red-600 hover:text-red-800"
                title="Remove attribute"
              >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Attribute Values -->
        <div v-if="attr.type !== 'text'" class="mt-3">
          <div class="flex items-center justify-between mb-2">
            <label class="block text-xs font-medium text-gray-700">Values</label>
            <button
              type="button"
              @click="addValue(index)"
              class="text-xs text-blue-600 hover:text-blue-800 flex items-center"
            >
              <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Add Value
            </button>
          </div>
          
          <div v-if="attr.values && attr.values.length > 0" class="space-y-2">
            <div v-for="(value, valueIndex) in attr.values" :key="valueIndex" class="flex items-center">
              <input
                type="text"
                v-model="value.value"
                class="flex-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                placeholder="Enter value"
              />
              <button
                type="button"
                @click="removeValue(index, valueIndex)"
                class="ml-2 text-red-500 hover:text-red-700"
              >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
          <p v-else class="text-xs text-gray-500">No values added yet. Add values for dropdown/radio/checkbox options.</p>
        </div>
      </div>
    </div>
    
    <p v-else class="text-sm text-gray-500">No attributes added yet. Click 'Add Attribute' to get started.</p>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  // For direct usage in Form.vue
  field: {
    type: Object,
    default: null
  },
  // For v-model binding
  modelValue: {
    type: Array,
    default: () => []
  },
  // For direct props (alternative to field object)
  label: {
    type: String,
    default: 'Product Attributes'
  },
  required: {
    type: Boolean,
    default: false
  },
  value: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['update:modelValue']);

// Use the appropriate source for the initial value
const attributes = ref([...props.modelValue || props.value || []]);

// Watch for changes in the modelValue prop
watch(() => props.modelValue || props.value, (newVal) => {
  if (newVal && JSON.stringify(newVal) !== JSON.stringify(attributes.value)) {
    attributes.value = JSON.parse(JSON.stringify(newVal));
  }
}, { immediate: true });

// Watch for changes in attributes and emit update
watch(attributes, (newVal) => {
  emit('update:modelValue', [...newVal]);
}, { deep: true });

const addAttribute = () => {
  const newId = Date.now();
  attributes.value.push({
    id: newId,
    name: '',
    code: '',
    type: 'select',
    values: [{
      id: newId + 1,
      value: '',
      is_visible: true,
      is_variant: 0,
      is_filterable: true
    }]
  });
};

const removeAttribute = (index) => {
  if (confirm('Are you sure you want to remove this attribute?')) {
    attributes.value.splice(index, 1);
  }
};

const addValue = (attrIndex) => {
  if (!attributes.value[attrIndex].values) {
    attributes.value[attrIndex].values = [];
  }
  attributes.value[attrIndex].values.push({
    id: Date.now(),
    value: '',
    is_visible: true,
    is_variant: 0,
    is_filterable: true
  });
};

const removeValue = (attrIndex, valueIndex) => {
  if (attributes.value[attrIndex].values.length > 1) {
    attributes.value[attrIndex].values.splice(valueIndex, 1);
  }
};

const updateAttribute = (attr, field, value) => {
  // Ensure we're working with a reactive object
  const index = attributes.value.findIndex(a => a.id === attr.id);
  if (index !== -1) {
    attributes.value[index][field] = value;
  }
};
</script>

<style scoped>
/* Add any custom styles here */
</style>