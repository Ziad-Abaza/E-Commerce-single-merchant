<!-- components/StarRating.vue -->
<template>
  <div class="inline-flex items-center" :class="{ 'cursor-pointer': interactive }">
    <div
      v-for="star in 5"
      :key="star"
      class="relative"
      @click="interactive && onRatingSelect(star)"
      @mouseenter="interactive && hoverRating(star)"
      @mouseleave="interactive && hoverRating(0)"
    >
      <!-- Background Star (Empty) -->
      <svg
        class="w-5 h-5 transition-colors"
        :class="{
          'text-yellow-400': (hoverValue || modelValue) >= star,
          'text-gray-300': (hoverValue || modelValue) < star,
        }"
        fill="currentColor"
        viewBox="0 0 20 20"
      >
        <path
          d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
        />
      </svg>
    </div>

    <!-- Optional: Display rating number -->
    <span v-if="showRating" class="ml-2 text-sm font-medium text-gray-700">
      {{ (hoverValue || modelValue).toFixed(1) }}
    </span>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: Number,
    default: 0,
  },
  interactive: {
    type: Boolean,
    default: false,
  },
  showRating: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue'])

const hoverValue = ref(0)

const onRatingSelect = (rating) => {
  if (props.interactive) {
    emit('update:modelValue', rating)
  }
}

const hoverRating = (rating) => {
  if (props.interactive) {
    hoverValue.value = rating
  }
}

// Reset hover when modelValue changes externally
watch(
  () => props.modelValue,
  () => {
    hoverValue.value = 0
  }
)
</script>
