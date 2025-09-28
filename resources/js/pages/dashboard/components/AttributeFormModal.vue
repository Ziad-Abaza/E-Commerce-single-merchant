<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center p-4 z-50 transition-opacity duration-300"
    @click.self="closeModal"
  >
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0 animate-scale-in">
      <div class="p-6">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-xl font-bold text-gray-900 dark:text-white">
            Add New Attribute
          </h3>
          <button
            @click="closeModal"
            class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="mt-6 space-y-5">
          <!-- Attribute Name -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Attribute Name</label>
            <input type="text" id="name" v-model="formData.name" placeholder="e.g., Color, Size, Material" required
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
          </div>

          <!-- Attribute Type -->
          <div>
            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Attribute Type</label>
            <select id="type" v-model="formData.type" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
              <option value="text">Text</option>
              <option value="number">Number</option>
              <option value="select">Select (Dropdown)</option>
              <option value="multiselect">Multi-Select</option>
              <option value="checkbox">Checkbox</option>
              <option value="radio">Radio Button</option>
              <option value="textarea">Textarea</option>
              <option value="boolean">Yes/No (Boolean)</option>
            </select>
          </div>

          <!-- Options (for select/multiselect) -->
          <div v-if="showOptionsInput" class="transition-all duration-300">
            <label for="options" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Options</label>
            <input type="text" id="options" v-model="optionsString" placeholder="Enter options, separated by commas"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">e.g., Red, Green, Blue</p>
          </div>

          <!-- Toggles -->
          <div class="space-y-3 pt-2">
            <div class="flex items-center">
              <input type="checkbox" id="is_filterable" v-model="formData.is_filterable" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
              <label for="is_filterable" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Is this attribute filterable?</label>
            </div>
            <div class="flex items-center">
              <input type="checkbox" id="is_visible_on_frontend" v-model="formData.is_visible_on_frontend" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
              <label for="is_visible_on_frontend" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Is this attribute visible on the frontend?</label>
            </div>
          </div>
          
          <!-- Error Message -->
           <div v-if="error" class="text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg">
             {{ error }}
           </div>

          <!-- Form Actions -->
          <div class="flex justify-end gap-4 pt-4">
            <button type="button" @click="closeModal"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
              Cancel
            </button>
            <button type="submit" :disabled="loading"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-blue-400 disabled:cursor-not-allowed flex items-center">
              <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ loading ? 'Saving...' : 'Save Attribute' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { useAttributeStore } from '../../../stores/dashboard/attributes';

// Props and Emits
const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['close', 'attribute-created']);

// Store
const attributeStore = useAttributeStore();

// Component State
const loading = ref(false);
const error = ref(null);
const optionsString = ref('');

const defaultFormData = {
  name: '',
  type: 'text',
  options: [],
  is_filterable: false,
  is_visible_on_frontend: true,
};

const formData = reactive({ ...defaultFormData });

// Computed Properties
const showOptionsInput = computed(() => {
  return ['select', 'multiselect'].includes(formData.type);
});

// Methods
const closeModal = () => {
  if (loading.value) return;
  emit('close');
  // Reset form after a short delay to allow modal to transition out
  setTimeout(() => {
      Object.assign(formData, defaultFormData);
      optionsString.value = '';
      error.value = null;
  }, 300);
};

const submitForm = async () => {
  loading.value = true;
  error.value = null;

  const dataToSubmit = { ...formData };

  // Convert comma-separated string to array for options
  if (showOptionsInput.value) {
    dataToSubmit.options = optionsString.value
      .split(',')
      .map(opt => opt.trim())
      .filter(Boolean); // Remove any empty strings
  } else {
      dataToSubmit.options = [];
  }

  try {
    const newAttribute = await attributeStore.createAttribute(dataToSubmit);
    if (newAttribute) {
      emit('attribute-created', newAttribute);
      closeModal();
    } else {
       error.value = attributeStore.error || 'An unknown error occurred.';
    }
  } catch (err) {
    error.value = err.response?.data?.message || err.message || 'Failed to create attribute.';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
@keyframes scale-in {
  from {
    transform: scale(0.95);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}
.animate-scale-in {
  animation: scale-in 0.2s ease-out forwards;
}
</style>
