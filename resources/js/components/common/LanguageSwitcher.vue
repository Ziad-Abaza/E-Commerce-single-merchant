<template>
  <div class="relative inline-block">
    <!-- Trigger Button -->
    <button
      type="button"
      @click.stop="toggleDropdown"
      class="flex items-center space-x-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg px-3 py-1.5 shadow-sm border border-gray-200 dark:border-gray-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
    >
      <span class="font-medium text-sm hidden sm:inline">
        {{ currentLocaleDisplay }}
      </span>
      <svg
        :class="['w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform duration-200', isOpen ? 'rotate-180' : '']"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown Menu -->
    <transition
      enter-active-class="transition ease-out duration-150 transform"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition ease-in duration-100 transform"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <ul
        v-show="isOpen"
        class="absolute right-0 mt-2 w-36 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-50 overflow-hidden"
      >
        <li
          v-for="lang in languages"
          :key="lang.code"
          @click.stop="switchLocale(lang.code)"
          :class="[
            'flex items-center space-x-3 px-4 py-2 cursor-pointer text-sm transition-colors duration-150',
            currentLocale.value === lang.code
              ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
              : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
          ]"
        >
          <span>{{ lang.label }}</span>
        </li>
      </ul>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useTranslation } from "@/composables/useTranslation";

const { locale: currentLocale, languages, changeLocale, isRtl } = useTranslation();
const isOpen = ref(false);

const toggleDropdown = () => (isOpen.value = !isOpen.value);

const switchLocale = async (locale) => {
  await changeLocale(locale);
  isOpen.value = false;
};

const currentLocaleDisplay = computed(() => currentLocale.value?.toUpperCase() || "EN");

const handleClickOutside = (e) => {
  if (!e.target.closest(".relative.inline-block")) {
    isOpen.value = false;
  }
};

onMounted(() => {
  window.addEventListener("click", handleClickOutside);

  document.documentElement.dir = isRtl.value ? "rtl" : "ltr";
});
</script>
