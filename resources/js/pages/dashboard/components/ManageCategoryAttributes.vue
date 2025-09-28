<template>
  <div class="space-y-4 rounded-lg bg-gray-50 dark:bg-gray-900/50 p-4">
    <div>
      <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200">Manage Attributes</h4>
      <p class="text-sm text-gray-500 dark:text-gray-400">Add, link, or remove attributes for this category.</p>
    </div>

    <!-- List of Existing Attributes -->
    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 space-y-2 bg-white dark:bg-gray-800">
        <p v-if="attributes.length === 0" class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
          No attributes assigned yet.
        </p>
        <div v-for="attr in attributes" :key="attr.id" class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 p-2 rounded-md transition-all duration-200">
            <div>
                <p class="font-medium text-gray-900 dark:text-white">{{ attr.name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">Type: {{ attr.type }}</p>
            </div>
            <button @click="$emit('remove', attr.id)" type="button" class="p-1.5 text-red-500 hover:text-red-700 dark:hover:text-red-400 rounded-full hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-3 pt-2">
       <button @click="$emit('add-new')" type="button" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm">
           <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
           Add & Link New Attribute
       </button>
       <!-- A button for linking existing attributes could be added here in the future -->
    </div>
  </div>
</template>

<script setup>
defineProps({
  attributes: {
    type: Array,
    default: () => []
  }
});

defineEmits(['add-new', 'remove']);
</script>
