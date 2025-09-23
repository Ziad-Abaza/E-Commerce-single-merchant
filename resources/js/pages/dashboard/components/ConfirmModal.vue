<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fade-in"
    @click.self="handleCancel"
  >
    <div
      class="relative w-full max-w-md mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl transform transition-all duration-300 scale-100 animate-scale-in"
      @click.stop
    >
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-3">
          <div
            class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
            :class="iconBgClass"
          >
            <span :class="iconTextClass" class="text-xl font-bold">{{ icon }}</span>
          </div>
          <h3 class="text-lg font-bold text-gray-900 dark:text-white">
            {{ title }}
          </h3>
        </div>
        <button
          @click="handleCancel"
          :disabled="loading"
          class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200 disabled:opacity-50"
          aria-label="Close modal"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="p-6">
        <div class="text-center">
          <div class="mb-4">
            <div
              class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4 mx-auto"
              :class="iconBgClass"
            >
              <span :class="iconTextClass" class="text-3xl font-bold">{{ icon }}</span>
            </div>
          </div>
          <p class="text-gray-700 dark:text-gray-300 text-base leading-relaxed">
            {{ message }}
          </p>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="flex flex-col sm:flex-row gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-b-2xl">
        <button
          type="button"
          @click="handleCancel"
          :disabled="loading"
          class="flex-1 px-4 py-3 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
        >
          {{ cancelText }}
        </button>
        <button
          type="button"
          @click="handleConfirm"
          :disabled="loading"
          :class="[
            'flex-1 px-4 py-3 text-sm font-medium text-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed',
            loading ? 'bg-gray-400 dark:bg-gray-600' : confirmBtnClass
          ]"
        >
          <span v-if="loading" class="flex items-center justify-center">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loadingText }}
          </span>
          <span v-else>{{ confirmText }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  show: { type: Boolean, default: false },
  title: { type: String, default: 'Confirm Action' },
  message: {
    type: String,
    default: 'Are you sure you want to perform this action? This cannot be undone.'
  },
  loading: { type: Boolean, default: false },
  icon: { type: String, default: '⚠️' },
  iconBg: { type: String, default: 'bg-red-100 dark:bg-red-900/30' },
  iconTextColor: { type: String, default: 'text-red-600 dark:text-red-400' },
  confirmText: { type: String, default: 'Delete' },
  cancelText: { type: String, default: 'Cancel' },
  loadingText: { type: String, default: 'Processing...' },
  confirmClass: {
    type: String,
    default: 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
  }
});

const emit = defineEmits(['confirm', 'cancel']);

const iconBgClass = computed(() => props.iconBg);
const iconTextClass = computed(() => props.iconTextColor);
const confirmBtnClass = computed(() => `bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 ${props.confirmClass}`);

// Handle confirm action
const handleConfirm = () => {
  emit('confirm');
};

// Handle cancel action
const handleCancel = () => {
  emit('cancel');
};
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-out forwards;
}

.animate-scale-in {
  animation: scaleIn 0.3s ease-out forwards;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .max-w-md {
    max-width: 100%;
  }

  .p-6 {
    padding: 1rem;
  }

  .text-lg {
    font-size: 1.125rem;
  }
}
</style>
