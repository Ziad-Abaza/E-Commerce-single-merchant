import { defineStore } from 'pinia'
import axios from '../bootstrap'
import { useAuthStore } from './auth'
import { useToast } from 'vue-toastification'

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: [],
        loading: false,
        error: null,
        summary: {
            total_items: 0,
            subtotal: 0,
            total: 0,
        },
        // NEW: State for promo codes
        promoCode: null,
        discount: 0,
    }),

    getters: {
        cartTotal: (state) => state.summary.total || 0,
        cartItemCount: (state) => state.summary.total_items || 0,
        getItemById: (state) => (itemId) => {
            return state.items.find((item) => item.id === itemId)
        },
        isInCart: (state) => (productDetailId) => {
            return state.items.some((item) => item.product_detail_id === productDetailId)
        },
        getItemByProductDetailId: (state) => (productDetailId) => {
            return state.items.find((item) => item.product_detail_id === productDetailId)
        },
        shippingCost: (state) => {
            // Shipping cost calculated on subtotal *before* discount
            return state.summary.subtotal >= 50 ? 0 : 9.99
        },
        taxAmount(state) {
            // MODIFIED: Tax is calculated on the subtotal *after* the discount
            const taxableAmount = state.summary.subtotal - state.discount
            return taxableAmount > 0 ? taxableAmount * 0.08 : 0
        },
        grandTotal(state) {
            // MODIFIED: Grand total now includes the discount
            const totalBeforeDiscount = state.summary.subtotal + this.shippingCost + this.taxAmount - this.discount 
            return totalBeforeDiscount
        },
    },

    actions: {
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
                        total: 0,
                    }
                    // ASSUMPTION: The backend sends promo details with the cart
                    this.promoCode = response.data.data.promo_code || null
                    this.discount = parseFloat(response.data.data.discount || 0)
                    this.saveToLocalStorage()
                }
                return { success: true, data: response.data }
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to load cart'
                this.handleError(this.error)
                return { success: false, error: this.error }
            } finally {
                this.loading = false
            }
        },

        async addToCart(productDetailId, quantity = 1) {
            this.loading = true
            this.error = null
            try {
                const authStore = useAuthStore()
                console.log("Auth Store User:", authStore.user?.id);
                console.log("product_detail_id:", productDetailId, "quantity:", quantity);
                const response = await axios.post('/carts', {
                    user_id: authStore.user?.id,
                    product_detail_id: productDetailId,
                    quantity: quantity,
                })
                await this.loadCart()
                const toast = useToast()
                toast.success('Product added to cart!')
                return { success: true, data: response.data }
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to add to cart'
                this.handleError(this.error)
                return { success: false, error: this.error }
            } finally {
                this.loading = false
            }
        },

        async updateCartItem(itemId, quantity) {
            this.loading = true
            this.error = null
            try {
                if (quantity <= 0) {
                    await this.removeFromCart(itemId)
                } else {
                    await axios.post(`/carts/${itemId}`, { quantity: quantity })
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

        async removeFromCart(itemId) {
            this.loading = true
            this.error = null
            try {
                await axios.delete(`/carts/${itemId}`)
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

        async clearCart() {
            this.loading = true
            this.error = null
            try {
                await axios.delete('/carts/clear')
                this.items = []
                this.summary = { total_items: 0, subtotal: 0, total: 0 }
                // Also clear promo code info
                this.promoCode = null
                this.discount = 0
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

        // NEW ACTION: Apply a promo code
        async applyPromoCode(code) {
            this.loading = true
            this.error = null
            try {
                const response = await axios.post('/promo/apply', { code: code })
                this.promoCode = response.data.promo_code
                this.discount = parseFloat(response.data.discount)
                // Optionally reload the entire cart to ensure all totals are synced
                await this.loadCart()
                return response.data
            } catch (error) {
                this.promoCode = null
                this.discount = 0
                console.error('Failed to apply promo code:', error)
                throw error
            } finally {
                this.loading = false
            }
        },

        // NEW ACTION: Remove the applied promo code
        async removePromoCode() {
            this.loading = true
            try {
                const response = await axios.post('/promo/remove')
                this.promoCode = null
                this.discount = 0
                await this.loadCart()
                return response.data
            } catch (error) {
                console.error('Failed to remove promo code:', error)
                throw error
            } finally {
                this.loading = false
            }
        },

        saveToLocalStorage() {
            localStorage.setItem(
                'cart_items',
                JSON.stringify({
                    items: this.items,
                    summary: this.summary,
                    promoCode: this.promoCode,
                    discount: this.discount,
                }),
            )
        },

        loadFromLocalStorage() {
            const savedCart = localStorage.getItem('cart_items')
            if (savedCart) {
                try {
                    const cartData = JSON.parse(savedCart)
                    this.items = cartData.items || []
                    this.summary = cartData.summary || {
                        total_items: 0,
                        subtotal: 0,
                        total: 0,
                    }
                    this.promoCode = cartData.promoCode || null
                    this.discount = cartData.discount || 0
                } catch (e) {
                    console.error('Failed to parse cart from local storage', e)
                    this.items = []
                    this.summary = { total_items: 0, subtotal: 0, total: 0 }
                }
            }
        },

        handleError(message) {
            const toast = useToast()
            toast.error(message || 'An error occurred')
        },

        clearError() {
            this.error = null
        },

        async initializeCart() {
            const authStore = useAuthStore()
            if (authStore.isAuthenticated) {
                await this.loadCart()
            } else {
                this.loadFromLocalStorage()
            }
        },
    },
})

