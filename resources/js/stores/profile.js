// src/stores/profile.js
import { defineStore } from 'pinia'
import axios from '../bootstrap'
import { useAuthStore } from './auth'
import { useToast } from 'vue-toastification'

export const useProfileStore = defineStore('profile', {
  state: () => ({
    // User profile data
    user: null,
    stats: null,

    // Form data
    formData: {
      name: '',
      phone: '',
      address: '',
      avatar: null
    },

    // Password change form data
    passwordData: {
      current_password: '',
      password: '',
      password_confirmation: ''
    },

    // UI state
    loading: false,
    loadingStats: false,
    error: null,
    successMessage: null
  }),

  getters: {
    /**
     * Get user initials for avatar fallback
     */
    userInitials: (state) => {
      if (!state.user?.name) return 'U'
      return state.user.name
        .split(' ')
        .map(word => word[0])
        .join('')
        .substring(0, 2)
        .toUpperCase()
    },

    /**
     * Check if user has avatar
     */
    hasAvatar: (state) => {
      return state.user?.avatar_url !== null
    },

    /**
     * Get formatted member since date
     */
    memberSince: (state) => {
      return state.stats?.member_since || 'N/A'
    }
  },

  actions: {
    /**
     * Load user profile
     */
    async loadProfile() {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/profile')

        if (response.data.success) {
          this.user = response.data.data

          // Initialize form data
          this.formData = {
            name: this.user.name || '',
            phone: this.user.phone || '',
            address: this.user.address || '',
            avatar: null
          }

          // Load stats
          await this.loadStats()

          return { success: true, data: response.data };
        } else {
          throw new Error(response.data.message || 'Failed to load profile')
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to load profile'
        this.handleError(this.error)
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    /**
     * Load user statistics
     */
    async loadStats() {
      this.loadingStats = true
      this.error = null

      try {
        const response = await axios.get('/profile/stats')

        if (response.data.success) {
          this.stats = response.data.data
          return { success: true,data: response.data }
        } else {
          throw new Error(response.data.message || 'Failed to load stats')
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to load statistics'
        this.handleError(this.error)
        return { success: false, error: this.error }
      } finally {
        this.loadingStats = false
      }
    },

    /**
     * Update user profile
     */
    async updateProfile() {
      this.loading = true
      this.error = null
      this.successMessage = null

      try {
        const formData = new FormData()

        // Add form fields
        if (this.formData.name) formData.append('name', this.formData.name)
        if (this.formData.phone) formData.append('phone', this.formData.phone)
        if (this.formData.address) formData.append('address', this.formData.address)

        // Add avatar if exists
        if (this.formData.avatar) {
          formData.append('avatar', this.formData.avatar)
        }

        const response = await axios.post('/profile', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        if (response.data.success) {
          this.user = response.data.data
          this.successMessage = response.data.message

          // Update auth store
          const authStore = useAuthStore()
          authStore.user = { ...authStore.user, ...this.user }

            // Update local storage
          localStorage.setItem("auth_user", JSON.stringify(authStore.user));


          this.showToast(response.data.message, 'success')
          return { success: true,data: response.data }
        } else {
          throw new Error(response.data.message || 'Failed to update profile')
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update profile'
        this.handleError(this.error)
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    /**
     * Change user password
     */
    async changePassword() {
      this.loading = true
      this.error = null
      this.successMessage = null

      try {
        const response = await axios.post('/profile/change-password', this.passwordData)

        if (response.data.success) {
          this.successMessage = response.data.message
          this.passwordData = {
            current_password: '',
            password: '',
            password_confirmation: ''
          }

          this.showToast(response.data.message, 'success')
          return { success: true,data: response.data }
        } else {
          throw new Error(response.data.message || 'Failed to change password')
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to change password'
        this.handleError(this.error)
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    /**
     * Reset password form
     */
    resetPasswordForm() {
      this.passwordData = {
        current_password: '',
        password: '',
        password_confirmation: ''
      }
      this.error = null
      this.successMessage = null
    },

    /**
     * Set avatar file
     */
    setAvatar(file) {
      if (file) {
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif']
        if (!allowedTypes.includes(file.type)) {
          this.showToast('Please select a valid image file (jpg, png, webp, gif)', 'error')
          return
        }

        // Validate file size (8MB)
        if (file.size > 8 * 1024 * 1024) {
          this.showToast('Image size should not exceed 8MB', 'error')
          return
        }

        this.formData.avatar = file
      }
    },

    /**
     * Remove avatar
     */
    async removeAvatar() {
      this.loading = true
      this.error = null

      try {
        // Clear avatar in backend
        const response = await axios.post('/profile', {
          avatar: null
        })

        if (response.data.success) {
          this.user = response.data.data
          this.formData.avatar = null

          // Update auth store
          const authStore = useAuthStore()
          authStore.user = { ...authStore.user, ...this.user }

          this.showToast('Avatar removed successfully', 'success')
          return { success: true,data: response.data }
        } else {
          throw new Error(response.data.message || 'Failed to remove avatar')
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to remove avatar'
        this.handleError(this.error)
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    /**
     * Handle errors with toast notifications
     */
    handleError(message) {
      this.showToast(message || 'An error occurred', 'error')
    },

    /**
     * Show toast notification
     */
    showToast(message, type = 'info') {
      const toast = useToast()
      switch (type) {
        case 'success':
          toast.success(message)
          break
        case 'error':
          toast.error(message)
          break
        case 'warning':
          toast.warning(message)
          break
        default:
          toast.info(message)
      }
    },

    /**
     * Clear error state
     */
    clearError() {
      this.error = null
    },

    /**
     * Clear success message
     */
    clearSuccessMessage() {
      this.successMessage = null
    },

    /**
     * Initialize profile store
     */
    async initializeProfile() {
      await this.loadProfile()
    }
  }
})
