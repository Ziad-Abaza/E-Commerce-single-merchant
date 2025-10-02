<template>
  <div class="relative w-full">
    <!-- Input field -->
    <div class="relative">
      <input
        ref="input"
        v-model="searchTerm"
        type="text"
        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-150 ease-in-out"
        :placeholder="placeholder"
        @input="onInput"
        @focus="isOpen = true"
        @keydown.down="highlightNext"
        @keydown.up="highlightPrev"
        @keydown.enter.prevent="selectHighlighted"
        @keydown.esc="closeDropdown"
      />
      <!-- Clear button -->
      <button
        v-if="searchTerm"
        @click="clearInput"
        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none"
      >
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Dropdown menu -->
    <div
      v-if="isOpen && filteredOptions.length > 0"
      class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-auto"
    >
      <ul class="py-1">
        <!-- Existing options -->
        <li
          v-for="(option, index) in filteredOptions"
          :key="option.value"
          @click="selectOption(option)"
          @mouseenter="highlightedIndex = index"
          class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer flex items-center"
          :class="{ 'bg-blue-50 dark:bg-blue-900/30': highlightedIndex === index }"
        >
          <span v-html="highlightMatch(option.label, searchTerm)"></span>
          <span v-if="option.description" class="text-xs text-gray-500 ml-2">{{ option.description }}</span>
        </li>

        <!-- Add new option -->
        <li
          v-if="showAddNewOption"
          @click="addNewOption"
          class="px-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer flex items-center"
          :class="{ 'bg-blue-50 dark:bg-blue-900/30': highlightedIndex === filteredOptions.length }"
        >
          <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Add "{{ searchTerm }}"
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number, Object],
    default: '',
  },
  options: {
    type: Array,
    default: () => [],
    validator: (value) => {
      return value.every(option => 
        typeof option === 'object' && 
        'value' in option && 
        'label' in option
      );
    }
  },
  placeholder: {
    type: String,
    default: 'Type to search or add new...',
  },
  allowNew: {
    type: Boolean,
    default: true,
  },
});

const emit = defineEmits(['update:modelValue', 'add-new']);

const searchTerm = ref('');
const isOpen = ref(false);
const highlightedIndex = ref(-1);
const input = ref(null);

// Filter options based on search term
const filteredOptions = computed(() => {
  if (!searchTerm.value) return props.options;
  const term = searchTerm.value.toLowerCase();
  return props.options.filter(option => 
    option.label.toLowerCase().includes(term) ||
    (option.description && option.description.toLowerCase().includes(term))
  );
});

// Show "Add new" option if search term doesn't match any existing options
const showAddNewOption = computed(() => {
  return (
    props.allowNew &&
    searchTerm.value &&
    !props.options.some(option => 
      option.label.toLowerCase() === searchTerm.value.toLowerCase()
    )
  );
});

// Handle input event
const onInput = () => {
  isOpen.value = true;
  highlightedIndex.value = -1;
  emit('update:modelValue', searchTerm.value);
};

// Select an option
const selectOption = (option) => {
  searchTerm.value = option.label;
  emit('update:modelValue', option.value);
  closeDropdown();
};

// Add a new option
const addNewOption = () => {
  if (!searchTerm.value.trim()) return;
  
  const newOption = {
    value: searchTerm.value,
    label: searchTerm.value,
  };
  
  emit('add-new', newOption);
  searchTerm.value = '';
  closeDropdown();
};

// Clear the input
const clearInput = () => {
  searchTerm.value = '';
  emit('update:modelValue', '');
  input.value.focus();
};

// Close dropdown
const closeDropdown = () => {
  isOpen.value = false;
  highlightedIndex.value = -1;
};

// Highlight next item in the list
const highlightNext = () => {
  const maxIndex = filteredOptions.value.length - 1 + (showAddNewOption.value ? 1 : 0);
  highlightedIndex.value = (highlightedIndex.value + 1) % (maxIndex + 1);
  scrollToHighlighted();
};

// Highlight previous item in the list
const highlightPrev = () => {
  const maxIndex = filteredOptions.value.length - 1 + (showAddNewOption.value ? 1 : 0);
  highlightedIndex.value = (highlightedIndex.value - 1 + maxIndex + 1) % (maxIndex + 1);
  scrollToHighlighted();
};

// Select the currently highlighted item
const selectHighlighted = () => {
  if (highlightedIndex.value === -1) return;
  
  if (showAddNewOption.value && highlightedIndex.value === filteredOptions.value.length) {
    addNewOption();
  } else if (filteredOptions.value[highlightedIndex.value]) {
    selectOption(filteredOptions.value[highlightedIndex.value]);
  }
};

// Scroll to the highlighted item in the dropdown
const scrollToHighlighted = () => {
  if (!isOpen.value || highlightedIndex.value === -1) return;
  
  const dropdown = input.value?.parentElement?.nextElementSibling;
  if (!dropdown) return;
  
  const items = dropdown.querySelectorAll('li');
  const item = items[highlightedIndex.value];
  
  if (item) {
    item.scrollIntoView({
      behavior: 'smooth',
      block: 'nearest',
    });
  }
};

// Highlight matching text in the dropdown options
const highlightMatch = (text, search) => {
  if (!search) return text;
  
  const regex = new RegExp(`(${search.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
  return text.replace(regex, '<span class="font-semibold text-blue-600 dark:text-blue-400">$1</span>');
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (input.value && !input.value.contains(event.target)) {
    closeDropdown();
  }
};

// Set initial value if modelValue is provided
watch(() => props.modelValue, (newValue) => {
  if (newValue !== searchTerm.value) {
    const selectedOption = props.options.find(opt => opt.value === newValue);
    searchTerm.value = selectedOption ? selectedOption.label : newValue || '';
  }
}, { immediate: true });

// Add event listeners
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

// Clean up event listeners
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>
