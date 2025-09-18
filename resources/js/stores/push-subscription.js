import axios from "../bootstrap";
import { useAuthStore } from "./auth";

export async function registerPushSubscription() {
    const authStore = useAuthStore();

    if (!authStore.isAuthenticated) {
        console.warn("User not authenticated. Push subscription skipped.");
        return;
    }

    if (!("serviceWorker" in navigator) || !("PushManager" in window)) {
        console.warn("Push notifications not supported in this browser.");
        return;
    }

    try {
        const registration =
            await navigator.serviceWorker.register("/service-worker.js");
        console.log(
            "Service Worker registered with scope:",
            registration.scope,
        );

        const existingSubscription =
            await registration.pushManager.getSubscription();
        if (existingSubscription) {
            console.log("Unsubscribing existing subscription...");
            await existingSubscription.unsubscribe();
        }

        const permission = await Notification.requestPermission();
        console.log("Notification permission status:", permission);

        if (permission !== "granted") return;

        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey:
                "BBOGa5OIXzaVuf5qneFOg3T3MNJg6fbfU3HavEwt80OyEfcGsHZVeJA4XMQRGvam23PD-hcryMZA4_Qpg0MV8cQ",
        });

        console.log("New Push subscription object:", subscription);

        await axios.post("/push/subscribe", subscription, {
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                Authorization: `Bearer ${authStore.token}`,
            },
        });

        console.log("Subscription saved successfully.");
    } catch (error) {
        console.error(
            "Push subscription failed:",
            error.response?.data || error,
        );
    }
}
