import { createI18n } from "vue-i18n";
import axios from "../bootstrap";

const defaultLocale = "en";

const i18n = createI18n({
    legacy: false,
    locale: defaultLocale,
    fallbackLocale: "en",
    messages: {},
});

// Load translations dynamically from API
export async function loadTranslations(locale) {
    try {
        const { data } = await axios.get(`/translations/${locale}`);
        if (data.success && data.translations) {
            i18n.global.setLocaleMessage(locale, data.translations);
            i18n.global.locale.value = locale;
            return true; 
        }
    } catch (error) {
        console.error(
            `[i18n] Failed to load translations for ${locale}`,
            error,
        );
        return false;
    }
}


export default i18n;
