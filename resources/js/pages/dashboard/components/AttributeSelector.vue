<template>
  <div class="space-y-4">
    <!-- Attribute Selection -->
    <div>
      <label for="attribute-select" class="block text-sm font-medium text-gray-700">
        Select Attributes
      </label>
      <div class="mt-1 flex space-x-2">
        <select
          id="attribute-select"
          v-model="selectedAttributeId"
          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
          @change="addAttribute"
        >
          <option value="">Select an attribute</option>
          <option v-for="attribute in availableAttributes" :key="attribute.id" :value="attribute.id">
            {{ attribute.name }}
          </option>
        </select>
        <button
          type="button"
          @click="fetchCategoryAttributes"
          class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
        >
          <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
            Refresh
        </button>
      </div>
    </div>

    <!-- Selected Attributes -->
    <div v-if="selectedAttributes.length > 0" class="space-y-4">
      <div v-for="(attribute) in selectedAttributes" :key="attribute.id" class="flex items-start space-x-2">
        <div class="flex-1">
          <label class="block text-sm font-medium text-gray-700">
            {{ attribute.name }}
          </label>
          <input
            type="text"
            v-model="attribute.pivot.value"
            placeholder="Enter value"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
            @input="updateAttributeValue(attribute)"
          />
        </div>
        <button
          @click="removeAttribute(attribute.id)"
          class="mt-6 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useProductAttributesStore } from '@/stores/productAttributes';

const props = defineProps({
  categoryId: {
    type: [Number, String],
    required: true
  },
  modelValue: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const attributesStore = useProductAttributesStore();
const loading = ref(false);
const selectedAttributeId = ref('');
const selectedAttributes = ref([...props.modelValue]);

// Computed property to get available attributes (not already selected)
const availableAttributes = computed(() => {
  if (!attributesStore.categoryAttributes) return [];
  return attributesStore.categoryAttributes.filter(
    attr => !selectedAttributes.value.some(selected => selected.id === attr.id)
  );
});

// Fetch category attributes
const fetchCategoryAttributes = async () => {
  if (!props.categoryId) return;
  
  loading.value = true;
  try {
    await attributesStore.fetchCategoryAttributes(props.categoryId);
    // Clear any selected attributes that are no longer in the category
    selectedAttributes.value = selectedAttributes.value.filter(attr => 
      attributesStore.categoryAttributes.some(catAttr => catAttr.id === attr.id)
    );
  } catch (error) {
    console.error('Failed to fetch category attributes:', error);
  } finally {
    loading.value = false;
  }
};

// Watch for changes in the modelValue prop
watch(() => props.modelValue, (newVal) => {
  if (JSON.stringify(newVal) !== JSON.stringify(selectedAttributes.value)) {
    selectedAttributes.value = [...newVal];
  }
}, { deep: true });

// Watch for changes in selectedAttributes and emit update
watch(selectedAttributes, (newVal) => {
  emit('update:modelValue', [...newVal]);
  emit('change', [...newVal]);
}, { deep: true });

// Fetch category attributes when categoryId changes
watch(() => props.categoryId, (newVal) => {
  if (newVal) {
    fetchCategoryAttributes();
  }
}, { immediate: true });

// Add a new attribute
const addAttribute = () => {
  if (!selectedAttributeId.value) return;
  
  const attribute = attributesStore.categoryAttributes.find(
    attr => attr.id == selectedAttributeId.value
  );
  
  if (attribute && !selectedAttributes.value.some(a => a.id === attribute.id)) {
    selectedAttributes.value.push({
      ...attribute,
      pivot: { value: '' }
    });
  }
  
  selectedAttributeId.value = '';
};

// Remove an attribute
const removeAttribute = (attributeId) => {
  const index = selectedAttributes.value.findIndex(attr => attr.id === attributeId);
  if (index !== -1) {
    selectedAttributes.value.splice(index, 1);
  }
};

// Update attribute value
const updateAttributeValue = (attribute) => {
  const index = selectedAttributes.value.findIndex(a => a.id === attribute.id);
  if (index !== -1) {
    selectedAttributes.value[index] = { ...attribute };
  }
};

defineExpose({
  selectedAttributes,
  fetchCategoryAttributes
});
</script>
