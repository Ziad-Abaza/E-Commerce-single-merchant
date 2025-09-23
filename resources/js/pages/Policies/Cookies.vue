<script setup>
import { ref, onMounted, computed } from 'vue';
import { usePolicyStore } from '@/stores/policy';
import { storeToRefs } from 'pinia';

// Initialize the store
const policyStore = usePolicyStore();
const loading = ref(true);
const error = ref(null);

// Get cookies policies using storeToRefs for reactive destructuring
const { getPoliciesByType } = storeToRefs(policyStore);
const cookiesPolicies = computed(() => {
  return getPoliciesByType.value('cookies');
});

// A single computed property for the main policy content
const cookiesPolicy = computed(() => {
  return cookiesPolicies.value[0]?.content || '';
});

// Mock data for cookie types, as this is often not from the API
const cookieTypes = ref([
  {
    name: 'Essential',
    purpose: 'Necessary for core functionality. Cannot be disabled.',
    duration: 'Session / 1 year',
    color: 'bg-red-500',
    icon: '🔑'
  },
  {
    name: 'Analytics',
    purpose: 'Helps us understand how you use our site to improve experience.',
    duration: '1 day - 2 years',
    color: 'bg-blue-500',
    icon: '📊'
  },
  {
    name: 'Functional',
    purpose: 'Enables personalized features like saved preferences.',
    duration: '1 day - 1 year',
    color: 'bg-green-500',
    icon: '⚙️'
  },
  {
    name: 'Targeting',
    purpose: 'Used by partners to show relevant ads based on your interests.',
    duration: '1 day - 1 year',
    color: 'bg-purple-500',
    icon: '🎯'
  }
]);

// Computed property for the last updated date
const lastUpdated = computed(() => {
  const policy = cookiesPolicies.value[0];
  if (!policy?.updated_at) return 'N/A';
  return new Date(policy.updated_at).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
});

// Async function to load the policy data
const loadCookiesPolicy = async () => {
  loading.value = true;
  error.value = null;

  try {
    // Check if policies are already loaded in the cache
    if (!policyStore.isLoaded) {
      await policyStore.loadAllPolicies();
    }
    // If specific 'cookies' policies are still not present, fetch them
    if (cookiesPolicies.value.length === 0) {
      await policyStore.fetchPolicyByType('cookies');
    }
  } catch (err) {
    console.error('Error loading cookies policy:', err);
    error.value = 'Failed to load cookies policy. Please try again later.';
  } finally {
    loading.value = false;
  }
};

// Open a modal or emit an event for cookie settings
const openCookieSettings = () => {
  // This is a custom event that can be listened to by a parent component or a global event bus
  const event = new CustomEvent('open-cookie-consent');
  window.dispatchEvent(event);
};

// Print the policy content
const printPolicy = () => {
  window.print();
};

// Initialize the component on mount
onMounted(() => {
  loadCookiesPolicy();
  document.title = 'Cookie Policy | ' + (import.meta.env.VITE_APP_NAME || 'E-Commerce');
});
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-50 dark:from-gray-900 dark:to-gray-800 py-10 px-4 transition-colors duration-300">
    <div class="max-w-5xl mx-auto">
      <div v-if="loading" class="flex flex-col items-center justify-center py-32">
        <div class="relative">
          <div class="w-20 h-20 border-4 border-t-transparent border-orange-500 rounded-full animate-spin"></div>
          <div class="absolute inset-0 flex items-center justify-center text-2xl">🍪</div>
        </div>
        <p class="mt-6 text-lg font-medium text-gray-600 dark:text-gray-300">Baking your cookie policy...</p>
      </div>

      <div v-else-if="error" class="bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 backdrop-blur-sm border border-red-200 dark:border-red-800 rounded-3xl p-8 text-center shadow-lg">
        <div class="text-red-500 dark:text-red-400 mb-4">
          <div class="text-6xl mb-4">🍪‍🔥</div>
          <h3 class="text-xl font-bold text-gray-800 dark:text-white">Oops! Cookie Crumbled</h3>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ error }}</p>
        </div>
        <button
          @click="loadCookiesPolicy"
          class="mt-6 inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white font-medium rounded-full shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Try Again
        </button>
      </div>

      <div v-else class="space-y-8">
        <div class="bg-gradient-to-r from-amber-400 via-orange-500 to-red-500 rounded-3xl p-1 shadow-xl">
          <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-12 -mr-12 w-64 h-64 bg-gradient-to-bl from-orange-200 to-transparent rounded-full opacity-30 dark:opacity-10"></div>
            <div class="absolute bottom-0 left-0 -mb-12 -ml-12 w-64 h-64 bg-gradient-to-tr from-red-200 to-transparent rounded-full opacity-30 dark:opacity-10"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between">
              <div>
                <div class="inline-flex items-center gap-2 mb-3">
                  <span class="text-3xl">🍪</span>
                  <h1 class="text-3xl md:text-4xl font-extrabold bg-gradient-to-r from-amber-600 to-red-600 bg-clip-text text-transparent">
                    Cookie Policy
                  </h1>
                </div>
                <p class="text-gray-600 dark:text-gray-300">
                  Last updated: <span class="font-medium">{{ lastUpdated }}</span>
                </p>
              </div>

              <div class="mt-6 md:mt-0 flex flex-wrap gap-3">
                <button
                  @click="printPolicy"
                  class="inline-flex items-center gap-2 px-5 py-3 bg-white dark:bg-gray-700 text-gray-800 dark:text-white border border-gray-300 dark:border-gray-600 font-medium rounded-xl shadow hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300"
                >
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                  </svg>
                  Print
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white/80 dark:bg-gray-800/70 backdrop-blur-sm rounded-3xl p-8 shadow-xl border border-gray-200 dark:border-gray-700">
          <div class="prose prose-lg dark:prose-invert max-w-none prose-headings:font-bold">
            <div v-if="!cookiesPolicy" class="py-16 text-center">
              <div class="text-6xl mb-4">🤔</div>
              <p class="text-gray-500 dark:text-gray-400 text-lg">No cookie policy content available.</p>
            </div>
            <div v-else>
              <div v-for="policy in cookiesPolicies" :key="policy.id">
                <div v-html="policy.content"></div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="cookieTypes && cookieTypes.length > 0" class="space-y-6">
          <h2 class="text-2xl font-bold text-gray-800 dark:text-white text-center mb-2">🍪 Types of Cookies We Use</h2>
          <p class="text-center text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
            We use different types of cookies to enhance your experience and understand how our website is used.
          </p>

          <div class="grid gap-6 md:grid-cols-2">
            <div
              v-for="(cookie, index) in cookieTypes"
              :key="index"
              class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
            >
              <div :class="`${cookie.color} h-1.5 rounded-t-2xl absolute top-0 left-0 right-0`"></div>

              <div class="flex items-start gap-4 mb-4">
                <div class="text-3xl mt-1">{{ cookie.icon }}</div>
                <div>
                  <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ cookie.name }}</h3>
                  <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 mt-2">
                    {{ cookie.duration }}
                  </span>
                </div>
              </div>

              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ cookie.purpose }}</p>
            </div>
          </div>
        </div>

        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 backdrop-blur-sm rounded-3xl p-8 border border-blue-200 dark:border-blue-800">
          <div class="flex flex-col md:flex-row gap-6">
            <div class="flex-shrink-0">
              <div class="w-16 h-16 bg-blue-100 dark:bg-blue-800 rounded-2xl flex items-center justify-center text-2xl">
                🛡️
              </div>
            </div>
            <div>
              <h3 class="text-xl font-bold text-blue-900 dark:text-blue-100 mb-3">Your Cookie Control Panel</h3>
              <p class="text-blue-800 dark:text-blue-200 mb-4 leading-relaxed">
                You're in control. Adjust your cookie preferences anytime through our settings panel or directly via your browser.
                Disabling non-essential cookies won't break the site — but might limit some personalized features.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Print styles */
@media print {
  body * {
    visibility: hidden;
  }
  .max-w-5xl, .max-w-5xl * {
    visibility: visible;
    max-width: 100% !important;
    width: 100% !important;
  }
  .max-w-5xl {
    position: absolute;
    left: 0;
    top: 0;
    margin: 0;
    padding: 20px;
    background: white;
  }
}

/* Custom toggle switch styling */
input[type="checkbox"]:checked + label > span {
  @apply translate-x-6;
}

/* Animation for cookie cards */
.grid > div {
  opacity: 0;
  transform: translateY(20px);
  animation: slideUp 0.6s ease forwards;
}

@keyframes slideUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.grid > div:nth-child(1) { animation-delay: 0.1s; }
.grid > div:nth-child(2) { animation-delay: 0.2s; }
.grid > div:nth-child(3) { animation-delay: 0.3s; }
.grid > div:nth-child(4) { animation-delay: 0.4s; }
</style>
