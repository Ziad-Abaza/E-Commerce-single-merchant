// stores/settings.js
import { defineStore } from "pinia";
import axios from "axios";

export const useSiteStore = defineStore("site", {
    state: () => ({
        settings: {
            site_name: "E-Commerce",
            site_description: "Your one-stop shop for quality products",
            contact_email: "support@store.com",
            theme_color: "blue",
            logo_url: "/images/logo.png",
            products_per_page: 15,
            currency: "EGP",
            free_shipping_threshold: 0,
        },
    }),
    actions: {
        async fetchSettings() {
            try {
                const res = await axios.get("/public/settings");
                if (res.data.success) {
                    this.settings = { ...this.settings, ...res.data.data };
                }
                console.log("Fetched settings: ", res.data);
            } catch (e) {
                console.error("Failed to fetch settings: ", e);
            }
        },
    },
});
