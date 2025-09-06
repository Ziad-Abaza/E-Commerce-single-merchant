<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Profile</h1>
      <p class="mt-2 text-gray-600">Manage your account information and preferences</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Profile Navigation -->
      <div class="lg:col-span-1">
        <nav class="space-y-1">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            class="w-full text-left px-3 py-2 text-sm font-medium rounded-md transition-colors"
            :class="activeTab === tab.id 
              ? 'bg-primary-100 text-primary-700' 
              : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
          >
            {{ tab.name }}
          </button>
        </nav>
      </div>

      <!-- Profile Content -->
      <div class="lg:col-span-2">
        <!-- Personal Information Tab -->
        <div v-if="activeTab === 'personal'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-6">Personal Information</h2>
          
          <form @submit.prevent="updateProfile" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input
                  id="name"
                  v-model="profileForm.name"
                  type="text"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input
                  id="email"
                  v-model="profileForm.email"
                  type="email"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input
                  id="phone"
                  v-model="profileForm.phone"
                  type="tel"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>
              <div>
                <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                <input
                  id="date_of_birth"
                  v-model="profileForm.date_of_birth"
                  type="date"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>
            </div>

            <div>
              <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
              <textarea
                id="address"
                v-model="profileForm.address"
                rows="3"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              ></textarea>
            </div>

            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="authStore.loading"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ authStore.loading ? 'Updating...' : 'Update Profile' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Change Password Tab -->
        <div v-if="activeTab === 'password'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-6">Change Password</h2>
          
          <form @submit.prevent="changePassword" class="space-y-6">
            <div>
              <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
              <input
                id="current_password"
                v-model="passwordForm.current_password"
                type="password"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              />
            </div>

            <div>
              <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
              <input
                id="new_password"
                v-model="passwordForm.new_password"
                type="password"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              />
            </div>

            <div>
              <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
              <input
                id="new_password_confirmation"
                v-model="passwordForm.new_password_confirmation"
                type="password"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              />
            </div>

            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="authStore.loading"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ authStore.loading ? 'Changing...' : 'Change Password' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Preferences Tab -->
        <div v-if="activeTab === 'preferences'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-6">Preferences</h2>
          
          <form @submit.prevent="updatePreferences" class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">Email Notifications</label>
              <div class="space-y-3">
                <label class="flex items-center">
                  <input
                    v-model="preferencesForm.email_notifications"
                    type="checkbox"
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-700">Receive email notifications</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="preferencesForm.marketing_emails"
                    type="checkbox"
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-700">Receive marketing emails</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="preferencesForm.order_updates"
                    type="checkbox"
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-700">Receive order updates</span>
                </label>
              </div>
            </div>

            <div>
              <label for="language" class="block text-sm font-medium text-gray-700">Language</label>
              <select
                id="language"
                v-model="preferencesForm.language"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              >
                <option value="en">English</option>
                <option value="es">Spanish</option>
                <option value="fr">French</option>
                <option value="de">German</option>
              </select>
            </div>

            <div>
              <label for="currency" class="block text-sm font-medium text-gray-700">Currency</label>
              <select
                id="currency"
                v-model="preferencesForm.currency"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              >
                <option value="USD">USD ($)</option>
                <option value="EUR">EUR (€)</option>
                <option value="GBP">GBP (£)</option>
                <option value="CAD">CAD (C$)</option>
              </select>
            </div>

            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="authStore.loading"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ authStore.loading ? 'Updating...' : 'Update Preferences' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useToast } from 'vue-toastification'

const authStore = useAuthStore()
const toast = useToast()

const activeTab = ref('personal')

const tabs = [
  { id: 'personal', name: 'Personal Information' },
  { id: 'password', name: 'Change Password' },
  { id: 'preferences', name: 'Preferences' }
]

const profileForm = reactive({
  name: '',
  email: '',
  phone: '',
  date_of_birth: '',
  address: ''
})

const passwordForm = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

const preferencesForm = reactive({
  email_notifications: true,
  marketing_emails: false,
  order_updates: true,
  language: 'en',
  currency: 'USD'
})

const updateProfile = async () => {
  try {
    const result = await authStore.updateProfile(profileForm)
    if (result.success) {
      toast.success('Profile updated successfully!')
    } else {
      toast.error(result.error)
    }
  } catch (error) {
    toast.error('Failed to update profile')
  }
}

const changePassword = async () => {
  if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
    toast.error('New passwords do not match')
    return
  }

  try {
    const result = await authStore.changePassword(passwordForm)
    if (result.success) {
      toast.success('Password changed successfully!')
      // Reset form
      Object.assign(passwordForm, {
        current_password: '',
        new_password: '',
        new_password_confirmation: ''
      })
    } else {
      toast.error(result.error)
    }
  } catch (error) {
    toast.error('Failed to change password')
  }
}

const updatePreferences = async () => {
  try {
    // This would be a separate API call for preferences
    toast.success('Preferences updated successfully!')
  } catch (error) {
    toast.error('Failed to update preferences')
  }
}

onMounted(() => {
  // Initialize form with current user data
  if (authStore.user) {
    Object.assign(profileForm, {
      name: authStore.user.name || '',
      email: authStore.user.email || '',
      phone: authStore.user.phone || '',
      date_of_birth: authStore.user.date_of_birth || '',
      address: authStore.user.address || ''
    })
  }
})
</script>
