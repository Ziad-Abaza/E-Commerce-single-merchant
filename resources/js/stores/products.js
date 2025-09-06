import { defineStore } from 'pinia'
import axios from '../bootstrap'

export const useProductStore = defineStore('products', {
  state: () => ({
    products: [],
    categories: [],
    featuredProducts: [],
    latestProducts: [],
    currentProduct: null,
    searchResults: [],
    filters: {
      category: null,
      minPrice: null,
      maxPrice: null,
      sort: 'created_at',
      search: ''
    },
    pagination: {
      currentPage: 1,
      lastPage: 1,
      perPage: 12,
      total: 0
    },
    loading: false,
    error: null
  }),

  getters: {
    getProductById: (state) => (id) => {
      return state.products.find(product => product.id === id)
    },

    getCategoryById: (state) => (id) => {
      return state.categories.find(category => category.id === id)
    },

    filteredProducts: (state) => {
      let filtered = [...state.products]

      if (state.filters.category) {
        filtered = filtered.filter(product =>
          product.categories.some(cat => cat.id === state.filters.category)
        )
      }

      if (state.filters.minPrice) {
        filtered = filtered.filter(product => product.price >= state.filters.minPrice)
      }

      if (state.filters.maxPrice) {
        filtered = filtered.filter(product => product.price <= state.filters.maxPrice)
      }

      if (state.filters.search) {
        const search = state.filters.search.toLowerCase()
        filtered = filtered.filter(product =>
          product.name.toLowerCase().includes(search) ||
          product.description.toLowerCase().includes(search)
        )
      }

      // Apply sorting
      switch (state.filters.sort) {
        case 'name':
          filtered.sort((a, b) => a.name.localeCompare(b.name))
          break
        case 'price_asc':
          filtered.sort((a, b) => a.price - b.price)
          break
        case 'price_desc':
          filtered.sort((a, b) => b.price - a.price)
          break
        case 'rating':
          filtered.sort((a, b) => (b.average_rating || 0) - (a.average_rating || 0))
          break
        case 'created_at':
        default:
          filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
          break
      }

      return filtered
    }
  },

  actions: {
    async loadProducts(params = {}) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/public/products', { params })
        this.products = response.data.data
        this.pagination = response.data.pagination
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to load products'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async loadFeaturedProducts() {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/public/featured-products')
        this.featuredProducts = response.data.data
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to load featured products'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async loadLatestProducts() {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/public/latest-products')
        this.latestProducts = response.data.data
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to load latest products'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async loadCategories() {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/public/categories')
        this.categories = response.data.data
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to load categories'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async getProduct(id) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get(`/public/products/${id}`)
        this.currentProduct = response.data.data
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to load product'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async getRelatedProducts(id) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get(`/public/products/${id}/related`)
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to load related products'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async searchProducts(query, filters = {}) {
      this.loading = true
      this.error = null

      try {
        const params = {
          query,
          ...filters,
          ...this.filters
        }

        const response = await axios.get('/public/search', { params })
        this.searchResults = response.data.data
        this.pagination = response.data.pagination
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Search failed'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async getProductsByCategory(categoryId, params = {}) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get(`/public/products/category/${categoryId}`, { params })
        this.products = response.data.data
        this.pagination = response.data.pagination
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to load category products'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    setFilters(filters) {
      this.filters = { ...this.filters, ...filters }
    },

    clearFilters() {
      this.filters = {
        category: null,
        minPrice: null,
        maxPrice: null,
        sort: 'created_at',
        search: ''
      }
    },

    clearError() {
      this.error = null
    }
  }
})
