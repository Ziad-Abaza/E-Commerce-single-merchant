<template>
  <nav class="bg-gray-50 border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700" aria-label="Breadcrumb">
    <div class="container py-3">
      <ol class="flex items-center space-x-2 text-sm">
        <li>
          <router-link
            to="/"
            class="text-gray-500 hover:text-primary-600 transition-colors dark:text-gray-400 dark:hover:text-primary-400"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
            </svg>
          </router-link>
        </li>

        <li v-for="(item, index) in breadcrumbs" :key="index" class="flex items-center">
          <svg class="w-4 h-4 text-gray-400 mx-2 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
          </svg>

          <router-link
            v-if="item.to && index < breadcrumbs.length - 1"
            :to="item.to"
            class="text-gray-500 hover:text-primary-600 transition-colors dark:text-gray-400 dark:hover:text-primary-400"
          >
            {{ item.name }}
          </router-link>

          <span
            v-else
            class="text-gray-900 font-medium dark:text-white"
            :aria-current="index === breadcrumbs.length - 1 ? 'page' : undefined"
          >
            {{ item.name }}
          </span>
        </li>
      </ol>
    </div>
  </nav>
</template>

<script setup>
defineProps({
  breadcrumbs: {
    type: Array,
    required: true,
    default: () => []
  }
})
</script>
