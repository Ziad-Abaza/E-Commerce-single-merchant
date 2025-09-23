<template>
  <div class="w-full">
    <!-- Image Preview / Swiper for multiple images -->
    <div v-if="isMultiple && previews.length" class="mb-4">
      <Swiper :slides-per-view="3" space-between="10" navigation class="mySwiper">
        <SwiperSlide
          v-for="(img, i) in previews"
          :key="i"
          class="w-32 h-32 rounded-lg overflow-hidden border border-gray-300"
        >
          <img :src="img" class="object-cover w-full h-full" />
        </SwiperSlide>
      </Swiper>
    </div>

    <!-- Single image preview -->
        <div
        v-else-if="!isMultiple && (previews.length || displayValue)"
        class="mb-4 w-64 h-64 rounded-lg overflow-hidden border border-gray-300 mx-auto"
        >
        <img
            :src="previews.length > 0 ? previews[0] : displayValue"
            class="object-cover w-full h-full"
        />
        </div>


    <!-- Dropzone -->
    <div
      class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600"
      @dragover.prevent="onDragOver"
      @dragleave.prevent="onDragLeave"
      @drop.prevent="onDrop"
      :class="{ 'bg-blue-100 dark:bg-blue-700': dragging }"
      @click="triggerInput"
    >
      <div class="flex flex-col items-center justify-center pt-5 pb-6">
        <svg
          class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
          aria-hidden="true"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 20 16"
        >
          <path
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
          />
        </svg>
        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
          <span class="font-semibold">Click or drag files</span>
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400">{{ fileTypesText }}</p>
      </div>
      <input
        ref="fileInput"
        type="file"
        :multiple="isMultiple"
        :accept="acceptedTypes"
        class="hidden"
        @change="handleFiles"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";

const props = defineProps({
  modelValue: { type: [File, Array], default: null },
  multiple: { type: Boolean, default: false },
  acceptedTypes: { type: String, default: "image/*" },
  previousValue: { type: String, default: null },
});

const emit = defineEmits(["update:modelValue"]);

const isMultiple = computed(() => props.multiple);
const previews = ref([]);
const dragging = ref(false);
const fileInput = ref(null);

// Show previous value if no new file is selected
const displayValue = computed(() => {
  if (props.modelValue && (props.modelValue instanceof File || Array.isArray(props.modelValue))) {
    return props.modelValue;
  }
  return props.previousValue;
});

const triggerInput = () => fileInput.value.click();
const handleFiles = (event) => processFiles(event.target.files);
const onDragOver = () => (dragging.value = true);
const onDragLeave = () => (dragging.value = false);
const onDrop = (event) => {
  dragging.value = false;
  processFiles(event.dataTransfer.files);
};

const processFiles = (files) => {
  if (!files.length) return;

  if (isMultiple.value) {
    const fileArray = Array.from(files);
    emit("update:modelValue", fileArray);
  } else {
    const file = files[0];
    emit("update:modelValue", file);
  }
};


const fileTypesText = computed(() =>
  isMultiple.value
    ? `Multiple files allowed (${props.acceptedTypes})`
    : `SVG, PNG, JPG or GIF (MAX. 800x400px)`
);

watch(
  () => props.modelValue,
  (newVal) => {
    previews.value = [];
    if (!newVal) return;

    if (isMultiple.value && Array.isArray(newVal)) {
      newVal.forEach((file) => {
        const reader = new FileReader();
        reader.onload = (e) => previews.value.push(e.target.result);
        reader.readAsDataURL(file);
      });
    } else if (!isMultiple.value && newVal instanceof File) {
      const reader = new FileReader();
      reader.onload = (e) => (previews.value = [e.target.result]);
      reader.readAsDataURL(newVal);
    }
  },
  { immediate: true }
);
</script>


