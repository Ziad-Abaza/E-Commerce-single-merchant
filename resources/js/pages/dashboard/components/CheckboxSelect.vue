<template>
  <div class="w-full">
    <label v-if="label" :for="id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
      {{ label }}
    </label>

    <!-- Search input for filtering options -->
    <div class="relative mb-3">
      <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
        </svg>
      </div>
      <input
        v-model="searchQuery"
        type="text"
        :placeholder="`Search ${label || 'options'}...`"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
      />
    </div>

    <!-- Checkbox options container -->
    <div class="max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 p-3">
      <div v-if="filteredOptions.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <p class="text-sm">No options found</p>
      </div>

      <div v-else class="space-y-2">
        <label
          v-for="option in filteredOptions"
          :key="option.value || option"
          class="flex items-center p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer transition-colors"
        >
          <input
            :id="`${id}-${option.value || option}`"
            type="checkbox"
            :value="option.value || option"
            :checked="isOptionSelected(option.value || option)"
            @change="toggleOption(option.value || option)"
            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
          />
          <span class="ml-3 text-sm font-medium text-gray-900 dark:text-white">
            {{ option.label || option }}
          </span>
        </label>
      </div>
    </div>

    <!-- Selected count and clear all button -->
    <div v-if="modelValue.length > 0" class="flex items-center justify-between mt-2">
      <span class="text-xs text-gray-500 dark:text-gray-400">
        {{ modelValue.length }} selected
      </span>
      <button
        type="button"
        @click="clearAll"
        class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium"
      >
        Clear all
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  options: {
    type: Array,
    default: () => []
  },
  label: {
    type: String,
    default: ""
  },
  id: {
    type: String,
    default: "checkbox-select"
  }
});

const emit = defineEmits(["update:modelValue"]);

const searchQuery = ref("");

// Filter options based on search query
const filteredOptions = computed(() => {
  if (!searchQuery.value) return props.options;

  const query = searchQuery.value.toLowerCase();
  return props.options.filter(option => {
    const label = (option.label || option).toLowerCase();
    return label.includes(query);
  });
});

// Check if an option is selected
const isOptionSelected = (value) => {
  return props.modelValue.includes(value);
};

// Toggle option selection
const toggleOption = (value) => {
  console.log('=== CHECKBOX SELECT ===');
  console.log('Toggling option:', value);
  console.log('Current modelValue:', props.modelValue);
  console.log('Type:', typeof props.modelValue, 'Is Array:', Array.isArray(props.modelValue));
  
  const newValue = [...props.modelValue];
  const index = newValue.indexOf(value);

  if (index > -1) {
    newValue.splice(index, 1);
    console.log('Removed option, new value:', newValue);
  } else {
    newValue.push(value);
    console.log('Added option, new value:', newValue);
  }

  emit("update:modelValue", newValue);
};

// Clear all selections
const clearAll = () => {
  emit("update:modelValue", []);
};

// Clear search when options change
watch(() => props.options, () => {
  searchQuery.value = "";
});
</script>
