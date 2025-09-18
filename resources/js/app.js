import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router/index.js'
import App from './App.vue'
import './bootstrap'
import './style.css'
import i18n, { loadTranslations } from "@/i18n/i18n.js";

// Import toast notification
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

async function bootstrap() {
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

// Load translations
app.use(i18n);
 const savedLocale = localStorage.getItem("locale") || "en";
 await loadTranslations(savedLocale);

// Mount the app
app.mount('#app')
}

bootstrap()
