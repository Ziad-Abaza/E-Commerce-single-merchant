<template>
  <div>
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Avatar Upload -->
      <div class="flex flex-col items-center">
        <div class="relative">
          <img 
            :src="avatarPreview || '/images/default-avatar.png'" 
            class="w-24 h-24 rounded-full object-cover border-2 border-gray-300 dark:border-gray-600"
            alt="Profile preview"
          />
          <label 
            for="avatar" 
            class="absolute bottom-0 right-0 bg-primary-500 text-white rounded-full p-2 cursor-pointer hover:bg-primary-600 transition-colors"
            title="Upload profile picture"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </label>
          <input
            id="avatar"
            type="file"
            accept="image/*"
            class="hidden"
            @change="handleAvatarChange"
            ref="avatarInput"
          />
        </div>
        <p v-if="errors.avatar" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ errors.avatar }}</p>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Click the icon to upload a profile picture (optional)</p>
      </div>

      <!-- Name -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Full Name
        </label>
        <div class="mt-1">
          <input
            id="name"
            v-model="form.name"
            type="text"
            required
            class="appearance-none block w-full px-3 py-2 border rounded-md placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100"
            :class="{ 'border-red-300 dark:border-red-600': errors.name }"
            placeholder="Enter your full name"
          />
          <p v-if="errors.name" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ errors.name }}</p>
        </div>
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Email Address
        </label>
        <div class="mt-1">
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            class="appearance-none block w-full px-3 py-2 border rounded-md placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100"
            :class="{ 'border-red-300 dark:border-red-600': errors.email }"
            placeholder="Enter your email address"
          />
          <p v-if="errors.email" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ errors.email }}</p>
        </div>
      </div>

      <!-- Phone -->
      <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Phone Number
        </label>
        <div class="mt-1">
          <input
            id="phone"
            v-model="form.phone"
            type="tel"
            required
            class="appearance-none block w-full px-3 py-2 border rounded-md placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100"
            :class="{ 'border-red-300 dark:border-red-600': errors.phone }"
            placeholder="Enter your phone number"
          />
          <p v-if="errors.phone" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ errors.phone }}</p>
        </div>
      </div>

      <!-- Address -->
      <div>
        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Address
        </label>
        <div class="mt-1">
          <textarea
            id="address"
            v-model="form.address"
            rows="3"
            required
            class="appearance-none block w-full px-3 py-2 border rounded-md placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100"
            :class="{ 'border-red-300 dark:border-red-600': errors.address }"
            placeholder="Enter your address "
          ></textarea>
          <p v-if="errors.address" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ errors.address }}</p>
        </div>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Password
        </label>
        <div class="mt-1">
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            class="appearance-none block w-full px-3 py-2 border rounded-md placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100"
            :class="{ 'border-red-300 dark:border-red-600': errors.password }"
            placeholder="Enter your password"
          />
          <p v-if="errors.password" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ errors.password }}</p>
        </div>
      </div>

      <!-- Confirm Password -->
      <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Confirm Password
        </label>
        <div class="mt-1">
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            required
            class="appearance-none block w-full px-3 py-2 border rounded-md placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100"
            :class="{ 'border-red-300 dark:border-red-600': errors.password_confirmation }"
            placeholder="Confirm your password"
          />
          <p v-if="errors.password_confirmation" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ errors.password_confirmation }}</p>
        </div>
      </div>

      <!-- Terms and Conditions -->
      <div class="flex items-center">
        <input
          id="terms"
          v-model="form.terms"
          type="checkbox"
          required
          class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded"
        />
        <label for="terms" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
          I agree to the
          <a href="#" class="text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">Terms and Conditions</a>
          and
          <a href="#" class="text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">Privacy Policy</a>
        </label>
      </div>

      <!-- Submit Button -->
      <div>
        <button
          type="submit"
          :disabled="authStore.loading"
          class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <span v-if="authStore.loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
            <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </span>
          {{ authStore.loading ? 'Creating Account...' : 'Create Account' }}
        </button>
      </div>

      <!-- Login Link -->
      <div class="text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
          Already have an account?
          <router-link to="/auth/login" class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">
            Sign in here
          </router-link>
        </p>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useToast } from 'vue-toastification'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const form = reactive({
  name: '',
  email: '',
  phone: '',
  address: '',
  password: '',
  password_confirmation: '',
  terms: false,
  avatar: null
})

const errors = ref({})
const avatarPreview = ref(null)
const avatarInput = ref(null)

const validateForm = () => {
  errors.value = {}

  if (!form.name.trim()) {
    errors.value.name = 'Name is required'
  }

  if (!form.email.trim()) {
    errors.value.email = 'Email is required'
  } else if (!/\S+@\S+\.\S+/.test(form.email)) {
    errors.value.email = 'Email is invalid'
  }

  if (form.phone && !/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/.test(form.phone)) {
    errors.value.phone = 'Please enter a valid phone number'
  }

  if (!form.password) {
    errors.value.password = 'Password is required'
  } else if (form.password.length < 8) {
    errors.value.password = 'Password must be at least 8 characters'
  }

  if (form.password !== form.password_confirmation) {
    errors.value.password_confirmation = 'Passwords do not match'
  }

  if (!form.terms) {
    errors.value.terms = 'You must agree to the terms and conditions'
  }

  return Object.keys(errors.value).length === 0
}

const handleAvatarChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Validate file type
    const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg']
    if (!validTypes.includes(file.type)) {
      errors.value.avatar = 'Please select a valid image file (JPEG, PNG, GIF, jpg, or WebP)'
      return
    }

    // Validate file size (max 5MB)
    const maxSize = 5 * 1024 * 1024 // 5MB
    if (file.size > maxSize) {
      errors.value.avatar = 'Image size should be less than 2MB'
      return
    }

    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
      avatarPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
    
    // Clear any previous errors
    errors.value.avatar = ''
    form.avatar = file
  }
}

const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }

  try {
    const formData = new FormData()
    formData.append('name', form.name)
    formData.append('email', form.email)
    formData.append('phone', form.phone)
    formData.append('address', form.address)
    formData.append('password', form.password)
    formData.append('password_confirmation', form.password_confirmation)
    if (form.avatar) {
      formData.append('avatar', form.avatar)
    }

    const result = await authStore.register(formData)

    if (result.success) {
      toast.success('Account created successfully!')
      toast.success('Please check your email to verify your account.')
      router.push('/')
    } else {
      toast.error(result.error)
    }
  } catch (error) {
    toast.error('Registration failed. Please try again.')
  }
}
</script>
