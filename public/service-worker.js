self.addEventListener("push", function (event) {
    console.log("[Service Worker] Push event received:", event);

    let data = {};
    try {
        data = event.data.json();
        console.log("[Service Worker] Push data:", data);
    } catch (e) {
        console.error("[Service Worker] Failed to parse push data:", e);
    }

    const options = {
        body: data.body || "No body received",
        icon: data.icon || "/images/defult-logo.png",
        badge: data.badge || "/images/defult-logo.png",
        data: {
            url: data.url || "/",
        },
    };

    event.waitUntil(
        self.registration.showNotification(data.title || "No title", options),
    );
});

self.addEventListener("notificationclick", function (event) {
    console.log("[Service Worker] Notification clicked:", event.notification);

    event.notification.close();
    const url = event.notification.data.url;

    event.waitUntil(
        clients
            .matchAll({ type: "window", includeUncontrolled: true })
            .then((windowClients) => {
                for (let client of windowClients) {
                    if (client.url === url && "focus" in client) {
                        console.log(
                            "[Service Worker] Focusing existing window:",
                            url,
                        );
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    console.log("[Service Worker] Opening new window:", url);
                    return clients.openWindow(url);
                }
            }),
    );
});
