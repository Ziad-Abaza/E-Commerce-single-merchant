import axios from "../bootstrap";
import { useAuthStore } from "./auth";
import { useToast } from "vue-toastification";

export async function registerPushSubscription() {
    const authStore = useAuthStore();
    const toast = useToast();

    if (!authStore.isAuthenticated) {
        toast.error("User must be authenticated to register for push.");
        return;
    }

    if (!("serviceWorker" in navigator) || !("PushManager" in window)) {
        toast.warning("Push notifications are not supported in this browser.");
        return;
    }

    try {
        const registration =
            await navigator.serviceWorker.register("/service-worker.js");
        const existingSubscription =
            await registration.pushManager.getSubscription();
        if (existingSubscription) {
            await existingSubscription.unsubscribe();
        }

        const permission = await Notification.requestPermission();
        if (permission !== "granted") return;

        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey:
                "BBOGa5OIXzaVuf5qneFOg3T3MNJg6fbfU3HavEwt80OyEfcGsHZVeJA4XMQRGvam23PD-hcryMZA4_Qpg0MV8cQ",
        });

        await axios.post("/push/subscribe", subscription);
    } catch (error) {
        console.error(
            "Push subscription failed:",
            error.response?.data || error,
        );
    }
}
