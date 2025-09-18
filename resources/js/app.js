import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router/index.js'
import App from './App.vue'
import './bootstrap'
import './style.css'
                    // Send subscription to server
import axios from './bootstrap';

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

if ("serviceWorker" in navigator && "PushManager" in window) {
    navigator.serviceWorker
        .register("/service-worker.js")
        .then(async function (registration) {
            console.log(
                "Service Worker registered with scope:",
                registration.scope,
            );

            // اطلب الاشتراك الحالي (لو موجود)
            const existingSubscription =
                await registration.pushManager.getSubscription();

            if (existingSubscription) {
                console.log("Found existing subscription, unsubscribing...");
                await existingSubscription.unsubscribe();
            }

            // اطلب إذن الإشعارات
            const permission = await Notification.requestPermission();
            console.log("Notification permission status:", permission);

            if (permission === "granted") {
                registration.pushManager
                    .subscribe({
                        userVisibleOnly: true,
                        applicationServerKey:
                            "BBOGa5OIXzaVuf5qneFOg3T3MNJg6fbfU3HavEwt80OyEfcGsHZVeJA4XMQRGvam23PD-hcryMZA4_Qpg0MV8cQ",
                    })
                    .then(function (subscription) {
                        console.log(
                            "New Push subscription object:",
                            subscription,
                        );

                        axios
                            .post("/push/subscribe", subscription)
                            .then((response) => {
                                console.log(
                                    "Subscription saved successfully:",
                                    response.data,
                                );
                            })
                            .catch((error) => {
                                console.error(
                                    "Failed to save subscription to backend:",
                                    error.response?.data || error,
                                );
                            });
                    })
                    .catch(function (error) {
                        console.error("Failed to subscribe to push:", error);
                    });
            }
        })
        .catch(function (error) {
            console.error("Service Worker registration failed:", error);
        });
}

