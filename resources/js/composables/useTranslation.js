import { computed, ref } from "vue";
import i18n, { loadTranslations } from "@/i18n/i18n.js";

// Composable for translations
export function useTranslation() {
    const currentLocale = ref(i18n.global.locale.value || "en");

    const availableLanguages = ref([
        { code: "en", label: "English" },
        { code: "ar", label: "العربية" },
    ]);

    const currentLanguage = computed(
        () =>
            availableLanguages.value.find(
                (l) => l.code === currentLocale.value,
            ) || availableLanguages.value[0],
    );

    const t = (key) => i18n.global.t(key);

    const setLocale = async (locale) => {
        if (!locale || locale === currentLocale.value) return;

        const found = availableLanguages.value.find((l) => l.code === locale);
        if (!found) return;

        try {
            await loadTranslations(locale);
            i18n.global.locale.value = locale;
            currentLocale.value = locale;
            document.documentElement.dir = locale === "ar" ? "rtl" : "ltr";
            localStorage.setItem("locale", locale);
        } catch (error) {
            console.error("Failed to set locale", error);
        }
    };


    const isRtl = computed(() => currentLocale.value === "ar");

    return {
        t,
        locale: currentLocale,
        language: currentLanguage,
        languages: availableLanguages,
        changeLocale: setLocale,
        isRtl,
    };
}
