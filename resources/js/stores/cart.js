import { defineStore } from 'pinia'
import axios from '../bootstrap'
import { useAuthStore } from './auth'
import { useToast } from 'vue-toastification'
import { useSiteStore } from './site'

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
        promoCode: null,
        promoCodeError: null,
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
            const siteStore = useSiteStore()
            // Get shipping settings from site settings
            const shippingRate = parseFloat(siteStore.settings?.shipping_rate) || 0.00
            const minShippingCost = parseFloat(siteStore.settings?.min_shipping_cost) || 0
            const maxShippingCost = parseFloat(siteStore.settings?.max_shipping_cost) || Infinity
            
            // Calculate base shipping cost
            const calculatedShipping = state.summary.subtotal * shippingRate
            
            // Apply min/max shipping cost limits
            return Math.max(minShippingCost, Math.min(maxShippingCost, calculatedShipping))
        },
        taxAmount(state) {
            const siteStore = useSiteStore()
            // Get tax rate from site settings
            const taxRate = parseFloat(siteStore.settings?.tax_rate) || 0.00
            // Calculate tax as a percentage of subtotal
            return state.summary.subtotal * taxRate
        },
        grandTotal(state) {
            const subtotal = state.summary.subtotal
            const shipping = this.shippingCost
            const tax = this.taxAmount
            const totalBeforeDiscount = subtotal + shipping + tax
            
            // Apply promo code discount if valid
            if (this.promoCode && this.promoCode.is_valid) {
                return totalBeforeDiscount - this.discount
            }
            
            return totalBeforeDiscount
        },
        
        // Get formatted discount amount
        formattedDiscount: (state) => {
            return state.discount?.toFixed(2) || '0.00'
        },
        
        // Get promo code data
        appliedPromoCode: (state) => state.promoCode,
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
            this.promoCodeError = null
        },
        
        // Validate promo code
        async validatePromoCode(code) {
            if (!code) {
                this.promoCodeError = 'Please enter a promo code'
                return { success: false, error: this.promoCodeError }
            }
            
            this.loading = true
            this.promoCodeError = null
            
            try {
                const response = await axios.post('/promo-codes/validate', {
                    code: code,
                    subtotal: this.summary.subtotal,
                    shipping: this.shippingCost
                })
                
                if (response.data.success) {
                    this.promoCode = {
                        ...response.data.data,
                        is_valid: true,
                        applied_at: new Date().toISOString()
                    }
                    this.discount = response.data.data.discount_amount || 0
                    
                    // Save to local storage
                    this.saveToLocalStorage()
                    
                    const toast = useToast()
                    toast.success('Promo code applied successfully!')
                    
                    return { success: true, data: response.data.data }
                } else {
                    this.promoCodeError = response.data.message || 'Invalid promo code'
                    return { success: false, error: this.promoCodeError }
                }
            } catch (error) {
                this.promoCodeError = error.response?.data?.message || 'Failed to validate promo code'
                this.handleError(this.promoCodeError)
                return { success: false, error: this.promoCodeError }
            } finally {
                this.loading = false
            }
        },
        
        // Remove promo code
        removePromoCode() {
            this.promoCode = null
            this.discount = 0
            this.promoCodeError = null
            this.saveToLocalStorage()
            
            const toast = useToast()
            toast.success('Promo code removed')
            
            return { success: true }
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

