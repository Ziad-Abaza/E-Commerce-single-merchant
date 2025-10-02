// stores/settings.js
import { defineStore } from "pinia";
import axios from "axios";

export const useSiteStore = defineStore("site", {
    state: () => ({
        settings: {
            // General Settings
            site_name: "E-Commerce Store",
            site_description: "Your one-stop shop for quality products",
            maintenance_mode: false,
            address: "EGYPT, Alexandria, al-Ajami",
            business_hours: "Mon-Fri: 9am-5pm",
            who_we_are: "We are a leading e-commerce store committed to providing quality products and excellent customer service. Our goal is to make your shopping experience simple, enjoyable, and secure.",

            // Contact Settings
            contact_email: "contact@example.com",
            contact_phone: "+1 (234) 567-8900",
            whatsapp_number: "+1 (234) 567-8900",
            facebook_url: "https://www.facebook.com/example",
            twitter_url: "https://twitter.com/example",
            instagram_url: "https://www.instagram.com/example",
            youtube_url: "https://www.youtube.com/channel/example",
            tiktok_url: "https://www.tiktok.com/@example",

            // Appearance Settings
            theme_color: "blue",
            logo_url: "/images/default-logo.webp",
            products_per_page: 12,

            // Email Settings
            smtp_host: "smtp.gmail.com",
            smtp_port: 587,
            email_notifications: true,

            // Payment Settings
            currency: "USD",
            tax_rate: 0.1,
            free_shipping_threshold: 100,

            // Security Settings
            max_login_attempts: 5,
            session_timeout: 120,
            require_email_verification: true,
        },
    }),
    actions: {
        async fetchSettings() {
            try {
                const res = await axios.get("/public/settings");
                if (res.data.success) {
                    this.settings = { ...this.settings, ...res.data.data };
                }
            } catch (e) {
                console.error("Failed to fetch settings: ", e);
            }
        },
    },
});
