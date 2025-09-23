<!-- resources/js/pages/dashboard/components/SettingModal.vue -->
<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
  >
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        aria-hidden="true"
        @click="$emit('close')"
      ></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <form @submit.prevent="handleSubmit">
          <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                  {{ isEditing ? 'Edit Setting' : 'Create New Setting' }}
                </h3>
                
                <div class="mt-4 space-y-4">
                  <!-- Key -->
                  <div>
                    <label for="key" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      Key *
                    </label>
                    <input
                      id="key"
                      v-model="form.key"
                      type="text"
                      required
                      :disabled="isEditing"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white disabled:bg-gray-100 dark:disabled:bg-gray-600"
                      placeholder="e.g., site_name, max_upload_size"
                    />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                      Unique identifier for this setting (cannot be changed after creation)
                    </p>
                  </div>

                  <!-- Label -->
                  <div>
                    <label for="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      Label *
                    </label>
                    <input
                      id="label"
                      v-model="form.label"
                      type="text"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                      placeholder="e.g., Site Name, Maximum Upload Size"
                    />
                  </div>

                  <!-- Description -->
                  <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      Description
                    </label>
                    <textarea
                      id="description"
                      v-model="form.description"
                      rows="2"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                      placeholder="Brief description of what this setting controls"
                    ></textarea>
                  </div>

                  <!-- Type -->
                  <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      Type *
                    </label>
                    <select
                      id="type"
                      v-model="form.type"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    >
                      <option value="text">Text</option>
                      <option value="textarea">Textarea</option>
                      <option value="number">Number</option>
                      <option value="boolean">Boolean (Toggle)</option>
                      <option value="select">Select (Dropdown)</option>
                      <option value="file">File</option>
                      <option value="image">Image</option>
                      <option value="json">JSON</option>
                    </select>
                  </div>

                  <!-- Group -->
                  <div>
                    <label for="group" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      Group *
                    </label>
                    <select
                      id="group"
                      v-model="form.group"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    >
                      <option value="general">General</option>
                      <option value="appearance">Appearance</option>
                      <option value="email">Email</option>
                      <option value="payment">Payment</option>
                      <option value="security">Security</option>
                      <option value="performance">Performance</option>
                      <option value="social">Social Media</option>
                      <option value="seo">SEO</option>
                      <option value="analytics">Analytics</option>
                      <option value="notifications">Notifications</option>
                    </select>
                  </div>

                  <!-- Options (for select type) -->
                  <div v-if="form.type === 'select'">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      Options *
                    </label>
                    <div class="mt-1 space-y-2">
                      <div
                        v-for="(option, index) in form.options"
                        :key="index"
                        class="flex gap-2"
                      >
                        <input
                          v-model="option.value"
                          type="text"
                          placeholder="Value"
                          class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        />
                        <input
                          v-model="option.label"
                          type="text"
                          placeholder="Label"
                          class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        />
                        <button
                          type="button"
                          @click="removeOption(index)"
                          class="px-2 py-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      </div>
                      <button
                        type="button"
                        @click="addOption"
                        class="w-full px-3 py-2 border border-dashed border-gray-300 dark:border-gray-600 rounded-md text-gray-600 dark:text-gray-400 hover:border-gray-400 dark:hover:border-gray-500 transition-colors"
                      >
                        + Add Option
                      </button>
                    </div>
                  </div>

                  <!-- Default Value -->
                  <div>
                    <label for="value" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      Default Value
                    </label>
                    
                    <!-- Text/Textarea/Number -->
                    <input
                      v-if="['text', 'number'].includes(form.type)"
                      id="value"
                      v-model="form.value"
                      :type="form.type === 'number' ? 'number' : 'text'"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    />
                    
                    <textarea
                      v-else-if="form.type === 'textarea'"
                      id="value"
                      v-model="form.value"
                      rows="3"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    ></textarea>

                    <!-- Boolean -->
                    <div v-else-if="form.type === 'boolean'" class="mt-1">
                      <label class="flex items-center">
                        <input
                          v-model="form.value"
                          type="checkbox"
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Enabled by default</span>
                      </label>
                    </div>

                    <!-- Select -->
                    <select
                      v-else-if="form.type === 'select'"
                      id="value"
                      v-model="form.value"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    >
                      <option value="">Select default option</option>
                      <option
                        v-for="option in form.options"
                        :key="option.value"
                        :value="option.value"
                      >
                        {{ option.label }}
                      </option>
                    </select>
                  </div>

                  <!-- Is Public -->
                  <div>
                    <label class="flex items-center">
                      <input
                        v-model="form.is_public"
                        type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                      />
                      <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                        Public setting (accessible without authentication)
                      </span>
                    </label>
                  </div>

                  <!-- Sort Order -->
                  <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      Sort Order
                    </label>
                    <input
                      id="sort_order"
                      v-model.number="form.sort_order"
                      type="number"
                      min="0"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                      placeholder="0"
                    />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                      Lower numbers appear first
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal footer -->
          <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="submit"
              :disabled="loading"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ loading ? 'Saving...' : (isEditing ? 'Update' : 'Create') }}
            </button>
            <button
              type="button"
              @click="$emit('close')"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { useSettingsStore } from '@/stores/dashboard/settings';

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  setting: {
    type: Object,
    default: null,
  },
  isEditing: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['close', 'save']);

const loading = ref(false);

const form = ref({
  key: '',
  label: '',
  description: '',
  type: 'text',
  group: 'general',
  value: '',
  options: [],
  is_public: false,
  sort_order: 0,
});

const resetForm = () => {
  form.value = {
    key: '',
    label: '',
    description: '',
    type: 'text',
    group: 'general',
    value: '',
    options: [],
    is_public: false,
    sort_order: 0,
  };
};

// Watch for setting changes to populate form
watch(() => props.setting, (newSetting) => {
  if (newSetting) {
    form.value = {
      key: newSetting.key || '',
      label: newSetting.label || '',
      description: newSetting.description || '',
      type: newSetting.type || 'text',
      group: newSetting.group || 'general',
      value: newSetting.value || '',
      options: newSetting.options || [],
      is_public: newSetting.is_public || false,
      sort_order: newSetting.sort_order || 0,
    };
  } else {
    resetForm();
  }
}, { immediate: true });

// Watch for show prop changes to reset form
watch(() => props.show, (show) => {
  if (!show) {
    resetForm();
  }
});

const addOption = () => {
  form.value.options.push({ value: '', label: '' });
};

const removeOption = (index) => {
  form.value.options.splice(index, 1);
};

const handleSubmit = async () => {
  loading.value = true;
  
  try {
    // Prepare form data
    const formData = { ...form.value };
    
    // Handle boolean values
    if (formData.type === 'boolean') {
      formData.value = formData.value ? '1' : '0';
    }
    
    // Handle select options
    if (formData.type === 'select' && formData.options.length === 0) {
      alert('Please add at least one option for select type');
      return;
    }
    
    // Remove empty options
    if (formData.type === 'select') {
      formData.options = formData.options.filter(opt => opt.value && opt.label);
    }
    
    emit('save', formData);
  } catch (error) {
    console.error('Form submission error:', error);
  } finally {
    loading.value = false;
  }
};
</script>
