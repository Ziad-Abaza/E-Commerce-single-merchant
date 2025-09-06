import { defineStore } from 'pinia'
import axios from '../bootstrap'
import { useToast } from 'vue-toastification'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    loading: false,
    error: null,
    total: 0,
    itemCount: 0
  }),

  getters: {
    cartTotal: (state) => {
      return state.items.reduce((total, item) => {
        return total + (item.price * item.quantity)
      }, 0)
    },

    cartItemCount: (state) => {
      return state.items.reduce((count, item) => {
        return count + item.quantity
      }, 0)
    },

    getItemById: (state) => (productId) => {
      return state.items.find(item => item.product_id === productId)
    },

    isInCart: (state) => (productId) => {
      return state.items.some(item => item.product_id === productId)
    }
  },

  actions: {
    async loadCart() {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/carts')
        this.items = response.data.data
        this.updateTotals()
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to load cart'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async addToCart(productId, quantity = 1, productDetails = {}) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post('/carts', {
          product_id: productId,
          quantity,
          ...productDetails
        })

        const newItem = response.data.data

        // Check if item already exists in cart
        const existingItem = this.items.find(item => item.product_id === productId)

        if (existingItem) {
          // Update existing item
          existingItem.quantity += quantity
          existingItem.price = newItem.price
        } else {
          // Add new item
          this.items.push(newItem)
        }

        this.updateTotals()

        const toast = useToast()
        toast.success('Product added to cart!')

        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to add to cart'
        const toast = useToast()
        toast.error(this.error)
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async updateCartItem(itemId, quantity) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post(`/carts/${itemId}`, {
          quantity
        })

        const updatedItem = response.data.data
        const itemIndex = this.items.findIndex(item => item.id === itemId)

        if (itemIndex !== -1) {
          if (quantity <= 0) {
            this.items.splice(itemIndex, 1)
          } else {
            this.items[itemIndex] = updatedItem
          }
        }

        this.updateTotals()

        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update cart item'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async removeFromCart(itemId) {
      this.loading = true
      this.error = null

      try {
        await axios.delete(`/carts/${itemId}`)

        const itemIndex = this.items.findIndex(item => item.id === itemId)
        if (itemIndex !== -1) {
          this.items.splice(itemIndex, 1)
        }

        this.updateTotals()

        const toast = useToast()
        toast.success('Item removed from cart')

        return { success: true }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to remove from cart'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async clearCart() {
      this.loading = true
      this.error = null

      try {
        // Remove all items one by one
        for (const item of this.items) {
          await axios.delete(`/carts/${item.id}`)
        }

        this.items = []
        this.updateTotals()

        const toast = useToast()
        toast.success('Cart cleared')

        return { success: true }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to clear cart'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    updateTotals() {
      this.total = this.cartTotal
      this.itemCount = this.cartItemCount
    },

    // Local cart operations (for guest users)
    addToLocalCart(product, quantity = 1) {
      const existingItem = this.items.find(item => item.product_id === product.id)

      if (existingItem) {
        existingItem.quantity += quantity
      } else {
        this.items.push({
          id: Date.now(), // Temporary ID for local storage
          product_id: product.id,
          product: product,
          quantity,
          price: product.price
        })
      }

      this.updateTotals()
      this.saveToLocalStorage()

      const toast = useToast()
      toast.success('Product added to cart!')
    },

    removeFromLocalCart(productId) {
      const itemIndex = this.items.findIndex(item => item.product_id === productId)
      if (itemIndex !== -1) {
        this.items.splice(itemIndex, 1)
        this.updateTotals()
        this.saveToLocalStorage()

        const toast = useToast()
        toast.success('Item removed from cart')
      }
    },

    updateLocalCartItem(productId, quantity) {
      const item = this.items.find(item => item.product_id === productId)
      if (item) {
        if (quantity <= 0) {
          this.removeFromLocalCart(productId)
        } else {
          item.quantity = quantity
          this.updateTotals()
          this.saveToLocalStorage()
        }
      }
    },

    clearLocalCart() {
      this.items = []
      this.updateTotals()
      this.saveToLocalStorage()

      const toast = useToast()
      toast.success('Cart cleared')
    },

    saveToLocalStorage() {
      localStorage.setItem('cart_items', JSON.stringify(this.items))
    },

    loadFromLocalStorage() {
      const savedItems = localStorage.getItem('cart_items')
      if (savedItems) {
        this.items = JSON.parse(savedItems)
        this.updateTotals()
      }
    },

    clearError() {
      this.error = null
    }
  }
})
