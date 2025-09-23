<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <router-link to="/" class="flex justify-center">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ siteStore.settings.site_name }}</h2>
      </router-link>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup>
import { useSiteStore } from "@/stores/site";
const siteStore = useSiteStore();
</script>
