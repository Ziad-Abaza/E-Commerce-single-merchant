<!-- components/ReviewForm.vue -->
<template>
  <div class="bg-white rounded-lg shadow p-6 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    <h3 class="text-lg font-medium text-gray-900 mb-4 dark:text-white">
      {{ isEditing ? 'Edit Your Review' : 'Leave a Review' }}
    </h3>

    <form @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Rating -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">Your Rating *</label>
        <StarRating
          v-model="form.rating"
          :interactive="true"
          :show-rating="true"
          class="text-2xl"
        />
        <p v-if="errors.rating" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.rating }}</p>
      </div>

      <!-- Title -->
      <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">Review Title</label>
        <input
          id="title"
          v-model="form.title"
          type="text"
          maxlength="255"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
          placeholder="Give your review a title"
        />
        <p v-if="errors.title" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.title }}</p>
      </div>

      <!-- Comment -->
      <div>
        <label for="comment" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">Your Review *</label>
        <textarea
          id="comment"
          v-model="form.comment"
          rows="5"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
          placeholder="Share your thoughts about this product..."
        ></textarea>
        <p v-if="errors.comment" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.comment }}</p>
      </div>

      <!-- Submit Button -->
      <div class="flex justify-end space-x-3 pt-4">
        <button
          type="button"
          @click="$emit('cancel')"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
          :disabled="loading"
        >
          Cancel
        </button>
        <button
          type="submit"
          class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:opacity-50 dark:bg-primary-700 dark:hover:bg-primary-800"
          :disabled="loading"
        >
          <span v-if="loading" class="flex items-center">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isEditing ? 'Updating...' : 'Submitting...' }}
          </span>
          <span v-else>{{ isEditing ? 'Update Review' : 'Submit Review' }}</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, watch,computed } from 'vue'
import { useToast } from 'vue-toastification'
import StarRating from './StarRating.vue'
import { useReviewStore } from '@/stores/reviews'

const props = defineProps({
  productId: {
    type: Number,
    required: true,
  },
  initialData: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['submitted', 'cancel'])

const toast = useToast()
const loading = ref(false)
const errors = ref({})
const isEditing = computed(() => !!props.initialData)

const form = reactive({
  rating: props.initialData?.rating || 5,
  title: props.initialData?.title || '',
  comment: props.initialData?.comment || '',
})

// Reset form when initialData changes
watch(
  () => props.initialData,
  (newData) => {
    if (newData) {
      form.rating = newData.rating || 5
      form.title = newData.title || ''
      form.comment = newData.comment || ''
    } else {
      form.rating = 5
      form.title = ''
      form.comment = ''
    }
    errors.value = {}
  },
  { immediate: true }
)

const validate = () => {
  const newErrors = {}

  if (!form.rating || form.rating < 1 || form.rating > 5) {
    newErrors.rating = 'Please select a rating between 1 and 5 stars.'
  }

  if (!form.comment || form.comment.trim().length === 0) {
    newErrors.comment = 'Review comment is required.'
  }

  errors.value = newErrors
  return Object.keys(newErrors).length === 0
}

const handleSubmit = async () => {
  if (!validate()) return

  loading.value = true
  errors.value = {}

  try {
    let result
    if (isEditing.value) {
      result = await useReviewStore().updateReview(props.initialData.id, {
        rating: form.rating,
        title: form.title,
        comment: form.comment,
      })
    } else {
      result = await useReviewStore().createReview(
        props.productId,
        {
        rating: form.rating,
        title: form.title,
        comment: form.comment,
      })
    }

    if (result.success) {
      emit('submitted', result.data)
    } else {
      throw new Error(result.error)
    }
  } catch (error) {
    console.error(error)
    errors.value = { form: error.message || 'An unexpected error occurred' }
  } finally {
    loading.value = false
  }
}
</script>
