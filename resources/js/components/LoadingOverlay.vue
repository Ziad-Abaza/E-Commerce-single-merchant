<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg p-6 flex flex-col items-center space-y-4">
      <!-- Spinner -->
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      
      <!-- Loading Text -->
      <p class="text-gray-600 font-medium">{{ message }}</p>
      
      <!-- Progress Bar (optional) -->
      <div v-if="showProgress" class="w-64 bg-gray-200 rounded-full h-2">
        <div 
          class="bg-primary-600 h-2 rounded-full transition-all duration-300" 
          :style="{ width: `${progress}%` }"
        ></div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  message: {
    type: String,
    default: 'Loading...'
  },
  showProgress: {
    type: Boolean,
    default: false
  },
  progress: {
    type: Number,
    default: 0
  }
})
</script>