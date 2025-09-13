<!-- resources/js/pages/dashboard/products/components/VariantDisplay.vue -->
<template>
  <div class="flex flex-wrap gap-2">
    <!-- Color -->
    <div v-if="variant.color && variant.color !== 'N/A'"
         class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
      <span class="w-2 h-2 rounded-full mr-1" :style="{ backgroundColor: getColorHex(variant.color) }"></span>
      {{ variant.color }}
    </div>

    <!-- Size -->
    <div v-if="variant.size && variant.size !== 'N/A'"
         class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
      </svg>
      {{ variant.size }}
    </div>

    <!-- Material -->
    <div v-if="variant.material && variant.material !== 'N/A'"
         class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
      </svg>
      {{ variant.material }}
    </div>

    <!-- Default message if no variant info -->
    <div v-if="(!variant.color || variant.color === 'N/A') &&
              (!variant.size || variant.size === 'N/A') &&
              (!variant.material || variant.material === 'N/A')"
         class="text-gray-500 dark:text-gray-400 text-sm">
      No variant details
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  variant: {
    type: Object,
    required: true,
    default: () => ({})
  }
});

// Helper function to get color hex code from color name
const getColorHex = (colorName) => {
  const colorMap = {
    'red': '#ef4444',
    'blue': '#3b82f6',
    'green': '#10b981',
    'yellow': '#f59e0b',
    'black': '#000000',
    'white': '#ffffff',
    'silver': '#c0c0c0',
    'gold': '#ffd700',
    'pink': '#ec4899',
    'purple': '#8b5cf6',
    'orange': '#f97316',
    'brown': '#92400e',
    'gray': '#6b7280',
    'navy': '#1e40af',
    'teal': '#0d9488',
    'lime': '#84cc16',
    'indigo': '#6366f1',
    'maroon': '#991b1b',
    'olive': '#65743a',
    'cyan': '#06b6d4',
    'magenta': '#be185d',
    'midnight blue': '#1e3a8a',
    'midnightblue': '#1e3a8a'
  };

  // Convert to lowercase and remove spaces for matching
  const normalizedColor = colorName.toLowerCase().replace(/\s+/g, '');

  // Return hex code if found, otherwise return a default color
  return colorMap[normalizedColor] || '#6b7280';
};
</script>

<style scoped>
/* Ensure white text is visible on white background */
.bg-white .text-white {
  color: #374151;
}

/* Ensure proper contrast for dark mode */
.dark .bg-white .text-white {
  color: #f3f4f6;
}
</style>
