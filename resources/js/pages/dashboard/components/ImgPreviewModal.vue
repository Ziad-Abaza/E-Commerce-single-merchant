<template>
  <div
    v-if="visible"
    class="fixed inset-0 backdrop-blur-md bg-gray-500 opacity-95 flex items-center justify-center z-50"
    @click.self="close"
  >
    <div class="relative max-w-4xl max-h-[90vh] w-full flex justify-center">
      <!-- Close button -->
      <button
        @click="close"
        class="absolute top-3 right-3 text-white bg-black/50 rounded-full p-2 hover:bg-black/70"
      >
        ✕
      </button>

      <!-- Image -->
      <img
        v-if="type === 'image'"
        :src="src"
        alt="Preview"
        class="rounded shadow-lg object-contain max-h-[90vh] w-auto"
      />

      <!-- File -->
      <div
        v-else-if="type === 'file'"
        class="flex flex-col items-center justify-center bg-white dark:bg-gray-800 rounded-lg p-8 shadow-lg"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-16 w-16 text-gray-500 mb-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 4v16m8-8H4" />
        </svg>
        <p class="mb-4 text-gray-700 dark:text-gray-300">Download File</p>
        <a
          :href="src"
          download
          class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
        >
          Download
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  visible: { type: Boolean, required: true },
  src: { type: String, default: null },
  type: { type: String, default: "image" }, // "image" or "file"
});

const emit = defineEmits(["close"]);

const close = () => {
  emit("close");
};
</script>
