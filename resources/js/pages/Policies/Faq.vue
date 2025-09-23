<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-8 sm:py-16 px-4 sm:px-6 lg:px-8 transition-colors duration-300">
    <div class="max-w-5xl mx-auto">
      <div class="text-center mb-12 sm:mb-16">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-primary-100 dark:bg-primary-900/30 mb-6">
          <svg class="w-10 h-10 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 dark:text-white mb-4">
          Policies & Agreements
        </h1>
        <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
          Here you can find all our store policies, including Privacy Policy, Return Policy, and Shipping Policy.
        </p>
      </div>

      <div v-if="loading && !error" class="flex flex-col items-center justify-center py-20">
        <div class="relative">
          <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-primary-500"></div>
          <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <div class="w-6 h-6 bg-primary-500 rounded-full animate-ping"></div>
          </div>
        </div>
        <p class="mt-6 text-gray-500 dark:text-gray-400 text-lg">Loading content...</p>
        <div class="mt-4 h-2 w-80 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
          <div class="h-full bg-primary-500 animate-pulse" style="width: 70%"></div>
        </div>
      </div>

      <div v-else-if="error" class="bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 rounded-2xl p-8 text-center shadow-lg mb-8">
        <div class="text-red-600 dark:text-red-400 mb-4">
          <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <h3 class="text-xl font-bold text-red-800 dark:text-red-300 mb-2">Oops! Something went wrong</h3>
        <p class="text-gray-600 dark:text-gray-300 mb-6">{{ error }}</p>
        <button
          @click="loadPolicies"
          class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 transform hover:scale-105"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Try Again
        </button>
      </div>

      <div v-else>
        <div v-if="filteredPolicies.length === 0" class="text-center py-16">
          <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <h3 class="mt-4 text-xl font-medium text-gray-900 dark:text-white">No policies available</h3>
          <p class="mt-2 text-gray-500 dark:text-gray-400">Please try again later or contact our support team.</p>
        </div>

        <div v-else class="space-y-6 sm:space-y-8">
          <div
            v-for="policy in filteredPolicies"
            :key="policy.id"
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700"
          >
            <div class="px-6 sm:px-8 py-5 sm:py-6 text-left border-b border-gray-200 dark:border-gray-700">
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                {{ policy.title }}
              </h2>
            </div>
            <div class="px-6 sm:px-8 pb-6 sm:pb-8 pt-4 sm:pt-6">
              <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 leading-relaxed" v-html="policy.content"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-16 sm:mt-20 text-center">
        <div class="bg-gradient-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 rounded-2xl p-8 sm:p-12 border border-primary-100 dark:border-primary-800">
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Still have questions?
          </h3>
          <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto">
            If you didn't find the answer you're looking for, our support team is here to help you.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <router-link
              to="/contact"
              class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-medium rounded-xl text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-lg transition-all duration-200 transform hover:scale-105"
            >
              Contact Support
              <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </router-link>
            <a
              :href="`https://wa.me/${siteStore.settings.whatsapp_number}`"
              target="_blank"
              class="inline-flex items-center justify-center px-8 py-4 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-xl text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-lg transition-all duration-200"
            >
              <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
              </svg>
              Chat on WhatsApp
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePolicyStore } from '@/stores/policy';
import { useSiteStore } from '@/stores/site';

const siteStore = useSiteStore();
const policyStore = usePolicyStore();

const loading = ref(true);
const error = ref(null);

// Main data structure for policies
const policiesData = ref({
  title: "Policies & Agreements",
  policies: []
});

// Computed property to filter policies (optional, can be used for future filtering)
const filteredPolicies = computed(() => {
  return policiesData.value.policies;
});

// Load policies from the store
const loadPolicies = async () => {
  loading.value = true;
  error.value = null;

  try {
    if (!policyStore.isLoaded) {
      await policyStore.loadAllPolicies();
    }

    const loadedPolicies = policyStore.getPoliciesByType('faq');

    if (loadedPolicies.length === 0) {
      throw new Error('No policies found with type "faq".');
    }

    policiesData.value.policies = loadedPolicies;

    document.title = `${policiesData.value.title} | ${siteStore.settings.site_name || 'E-Commerce'}`;
  } catch (err) {
    console.error('Error loading policies:', err);
    error.value = 'Failed to load policies. Please try again later.';
  } finally {
    loading.value = false;
  }
};

onMounted(loadPolicies);
</script>
