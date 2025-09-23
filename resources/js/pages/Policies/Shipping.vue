<script setup>
import { onMounted, computed } from 'vue';
import { usePolicyStore } from '@/stores/policy';

// Initialize store
const policyStore = usePolicyStore();

// Fetch policies on mount
onMounted(() => {
  policyStore.loadAllPolicies();
});

// Computed: Get "Shipping" policies
const shippingPolicies = computed(() => policyStore.getPoliciesByType('shipping'));

// Computed: Loading state
const isLoading = computed(() => policyStore.loading);
</script>

<template>
  <div class="min-h-screen bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100 transition-colors duration-300">

    <main class="max-w-4xl mx-auto px-6 md:px-10 py-16">

      <!-- Page Title -->
      <div class="text-center mb-12">
        <h1 class="text-4xl font-extrabold tracking-tight bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent">
          Shipping & Delivery Policy
        </h1>
        <p class="mt-3 text-lg opacity-80 dark:opacity-90">
          Learn how and when your orders will reach you.
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="flex flex-col items-center justify-center py-24">
        <div class="w-14 h-14 border-4 border-t-transparent border-emerald-500 rounded-full animate-spin mb-5"></div>
        <p class="text-lg text-gray-500 dark:text-gray-400">Loading shipping policies...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="policyStore.error" class="p-6 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 mb-10">
        <div class="flex items-start">
          <svg class="w-6 h-6 text-red-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <strong class="font-semibold">Oops! Something went wrong.</strong>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ policyStore.error }}</p>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="shippingPolicies.length === 0" class="text-center py-24">
        <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        <h3 class="text-xl font-medium text-gray-600 dark:text-gray-300 mb-2">No shipping policies available.</h3>
        <p class="text-gray-500 dark:text-gray-400">We’re updating our delivery info. Please check back soon.</p>
      </div>

      <!-- Policy List -->
      <div v-else class="space-y-8">
        <div
          v-for="policy in shippingPolicies"
          :key="policy.id"
          class="group relative p-8 rounded-2xl bg-white/70 dark:bg-gray-800/60 backdrop-blur-sm border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300"
        >
          <!-- Decorative left accent bar (appears on hover) -->
          <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-2xl bg-gradient-to-b from-emerald-500 to-teal-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

          <h2 class="text-2xl font-bold mb-5 text-gray-800 dark:text-gray-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
            {{ policy.title }}
          </h2>

          <div
            v-html="policy.content"
            class="prose prose-lg max-w-none dark:prose-invert prose-headings:font-semibold prose-p:text-gray-700 dark:prose-p:text-gray-300 prose-a:text-emerald-600 hover:prose-a:text-emerald-800 dark:prose-a:text-emerald-400 dark:hover:prose-a:text-emerald-300 transition-all"
          ></div>
        </div>
      </div>

      <!-- Footer CTA -->
      <div v-if="!isLoading && shippingPolicies.length > 0" class="mt-16 pt-8 border-t text-center border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Need faster delivery?
          <a href="/contact" class="font-medium underline hover:text-emerald-600 dark:hover:text-emerald-400 transition">Contact our support team</a>.
        </p>
      </div>

    </main>
  </div>
</template>

<style scoped>
/* Fade-in animation for each policy card */
div[class*="group"] {
  opacity: 0;
  transform: translateY(15px);
  animation: fadeInUp 0.6s ease forwards;
}

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

div:nth-child(1) { animation-delay: 0.1s; }
div:nth-child(2) { animation-delay: 0.2s; }
div:nth-child(3) { animation-delay: 0.3s; }
div:nth-child(4) { animation-delay: 0.4s; }
div:nth-child(5) { animation-delay: 0.5s; }
</style>
