// src/stores/cart.js
import { defineStore } from 'pinia'
import axios from '../bootstrap'
import { useAuthStore } from './auth'
import { useToast } from 'vue-toastification'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    loading: false,
    error: null,
    total: 0,
    itemCount: 0,
    subtotal: 0,
    shipping: 0,
    tax: 0,
    summary: {
      total_items: 0,
      subtotal: 0,
      total: 0
    }
  }),

  getters: {
    /**
     * Get cart total
     */
    cartTotal: (state) => state.summary.total || 0,

    /**
     * Get cart item count
     */
    cartItemCount: (state) => state.summary.total_items || 0,

    /**
     * Get item by ID
     */
    getItemById: (state) => (itemId) => {
      return state.items.find(item => item.id === itemId)
    },

    /**
     * Check if product is in cart
     */
    isInCart: (state) => (productDetailId) => {
      return state.items.some(item => item.product_detail_id === productDetailId)
    },

    /**
     * Get item by product detail ID
     */
    getItemByProductDetailId: (state) => (productDetailId) => {
      return state.items.find(item => item.product_detail_id === productDetailId)
    },

    /**
     * Calculate shipping cost
     */
    shippingCost: (state) => {
      return state.summary.subtotal >= 50 ? 0 : 9.99
    },

    /**
     * Calculate tax (8%)
     */
    taxAmount: (state) => {
      return state.summary.subtotal * 0.08
    },

    /**
     * Calculate grand total
     */
    grandTotal: (state) => {
      return state.summary.subtotal + state.shippingCost + state.taxAmount
    }
  },

  actions: {
    /**
     * Load cart for authenticated user
     */
    async loadCart() {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/carts')

        if (response.data.data) {
          this.items = response.data.data.items || []
          this.summary = response.data.data.summary || {
            total_items: 0,
            subtotal: 0,
            total: 0
          }

          // Update local storage for persistence
          this.saveToLocalStorage()
        }

        return { success: true,data:  response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to load cart'
        this.handleError(this.error)
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    /**
     * Add item to cart
     */
    async addToCart(productDetailId, quantity = 1) {
      this.loading = true
      this.error = null

      try {
        const authStore = useAuthStore()

        const response = await axios.post('/carts', {
          user_id: authStore.user?.id,
          product_detail_id: productDetailId,
          quantity: quantity
        })

        // Reload cart to get updated state
        await this.loadCart()

        const toast = useToast()
        toast.success('Product added to cart!')

        return { success: true,data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to add to cart'
        this.handleError(this.error)
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    /**
     * Update cart item quantity
     */
    async updateCartItem(itemId, quantity) {
      this.loading = true
      this.error = null

      try {
        if (quantity <= 0) {
          // Remove item if quantity is 0 or less
          await this.removeFromCart(itemId)
        } else {
          // Update quantity
          await axios.post(`/carts/${itemId}`, {
            quantity: quantity
          })

          // Reload cart to get updated state
          await this.loadCart()
        }

        return { success: true }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update cart item'
        this.handleError(this.error)
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    /**
     * Remove item from cart
     */
    async removeFromCart(itemId) {
      this.loading = true
      this.error = null

      try {
        await axios.delete(`/carts/${itemId}`)

        // Reload cart to get updated state
        await this.loadCart()

        const toast = useToast()
        toast.success('Item removed from cart')

        return { success: true }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to remove from cart'
        this.handleError(this.error)
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    /**
     * Clear all items from cart
     */
    async clearCart() {
      this.loading = true
      this.error = null

      try {
        await axios.delete('/carts/clear')

        this.items = []
        this.summary = {
          total_items: 0,
          subtotal: 0,
          total: 0
        }

        // Clear local storage
        localStorage.removeItem('cart_items')

        const toast = useToast()
        toast.success('Cart cleared')

        return { success: true }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to clear cart'
        this.handleError(this.error)
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    /**
     * Save cart to local storage
     */
    saveToLocalStorage() {
      localStorage.setItem('cart_items', JSON.stringify({
        items: this.items,
        summary: this.summary
      }))
    },

    /**
     * Load cart from local storage
     */
    loadFromLocalStorage() {
      const savedCart = localStorage.getItem('cart_items')
      if (savedCart) {
        try {
          const cartData = JSON.parse(savedCart)
          this.items = cartData.items || []
          this.summary = cartData.summary || {
            total_items: 0,
            subtotal: 0,
            total: 0
          }
        } catch (e) {
          console.error('Failed to parse cart from local storage', e)
          this.items = []
          this.summary = {
            total_items: 0,
            subtotal: 0,
            total: 0
          }
        }
      }
    },

    /**
     * Handle errors with toast notifications
     */
    handleError(message) {
      const toast = useToast()
      toast.error(message || 'An error occurred')
    },

    /**
     * Clear error state
     */
    clearError() {
      this.error = null
    },

    /**
     * Initialize cart
     */
    async initializeCart() {
      const authStore = useAuthStore()

      if (authStore.isAuthenticated) {
        // Load from API for authenticated users
        await this.loadCart()
      } else {
        // Load from local storage for guest users
        this.loadFromLocalStorage()
      }
    }
  }
})
