<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <AppHeader />

    <!-- Main Content -->
    <main>
      <router-view />
    </main>

    <!-- Footer -->
    <AppFooter />
  </div>
</template>

<script setup>
import AppHeader from '../../../components/layout/AppHeader.vue'
import AppFooter from '../../../components/layout/AppFooter.vue'
</script>
