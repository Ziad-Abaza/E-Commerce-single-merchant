<template>
  <div 
    v-if="show" 
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    :class="{ 'absolute': !fullScreen }"
  >
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex flex-col items-center space-y-4 shadow-xl">
      <!-- Spinner -->
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      
      <!-- Loading text -->
      <p class="text-gray-700 dark:text-gray-300 font-medium">
        {{ message }}
      </p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false
  },
  message: {
    type: String,
    default: 'Loading...'
  },
  fullScreen: {
    type: Boolean,
    default: true
  }
})
</script>
