import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router/index.js'
import App from './App.vue'
import './bootstrap'
import './style.css'
// Import toast notification
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

// Create Vue app
const app = createApp(App)

// Use Pinia for state management
app.use(createPinia())

// Use Vue Router
app.use(router)

// Use Toast notifications
app.use(Toast, {
  transition: 'Vue-Toastification__bounce',
  maxToasts: 20,
  newestOnTop: true
})

// Mount the app
app.mount('#app')
