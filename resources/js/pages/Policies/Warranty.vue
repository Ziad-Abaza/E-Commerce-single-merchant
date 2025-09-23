<script setup>
import { onMounted, computed, ref } from 'vue';
import { usePolicyStore } from '@/stores/policy';
import { useTheme } from "@/composables/useTheme.js";

const { isDark, toggleTheme } = useTheme();
// Initialize store
const policyStore = usePolicyStore();

// Fetch policies on mount
onMounted(() => {
  policyStore.loadAllPolicies();
});

// Computed properties
const warrantyPolicies = computed(() => policyStore.getPoliciesByType('warranty'));
const isLoading = computed(() => policyStore.loading);
</script>

<template>
  <div class="min-h-screen transition-colors duration-300 dark:bg-gray-900  dark:text-gray-100 bg-gray-50 text-gray-800">

    <main class="max-w-4xl mx-auto px-6 md:px-10 py-12">

      <!-- Loading State -->
      <div v-if="isLoading" class="flex flex-col items-center justify-center py-20">
        <div class="w-16 h-16 border-4 border-t-transparent border-blue-500 rounded-full animate-spin mb-4"></div>
        <p class="text-lg opacity-75">Loading warranty policies...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="policyStore.error" class="p-6 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 mb-8">
        <div class="flex items-center">
          <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <strong class="font-medium">Oops! Something went wrong.</strong>
        </div>
        <p class="mt-2 ml-9 text-sm">{{ policyStore.error }}</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="warrantyPolicies.length === 0" class="text-center py-20">
        <svg class="w-16 h-16 mx-auto opacity-30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-xl font-medium mb-2">No warranty policies found.</h3>
        <p class="opacity-70">Check back later or contact support.</p>
      </div>

      <!-- Policy List -->
      <div v-else class="space-y-10">
        <article
          v-for="policy in warrantyPolicies"
          :key="policy.id"
          class="group relative p-8 rounded-2xl backdrop-blur-sm transition-all duration-500 hover:shadow-xl hover:-translate-y-1"
          :class="isDark
            ? 'bg-gray-800/50 hover:bg-gray-800 border border-gray-700'
            : 'bg-white/80 hover:bg-white border border-gray-100 shadow-sm'"
        >
          <!-- Decorative accent bar -->
          <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-2xl bg-gradient-to-b from-blue-500 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

          <h2 class="text-2xl font-bold mb-4 leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
            {{ policy.title }}
          </h2>
          <div
            v-html="policy.content"
            class="prose prose-lg max-w-none dark:prose-invert prose-headings:font-semibold prose-p:opacity-90 hover:prose-a:text-blue-600 dark:hover:prose-a:text-blue-400 transition-all"
          ></div>
        </article>
      </div>
    </main>
  </div>
</template>
