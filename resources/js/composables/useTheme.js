import { ref, computed, watch } from "vue";

const systemPreference = ref(
    window.matchMedia("(prefers-color-scheme: dark)").matches
        ? "dark"
        : "light",
);
const theme = ref(null);

export const useTheme = () => {
    const isDark = computed(() => {
        if (theme.value === "system") {
            return systemPreference.value === "dark";
        }
        return theme.value === "dark";
    });

    const applyTheme = (darkMode) => {
        document.documentElement.classList.toggle("dark", darkMode);
    };

    const toggleTheme = () => {
        if (theme.value === "system") {
            theme.value = systemPreference.value === "dark" ? "light" : "dark";
        } else {
            theme.value = theme.value === "light" ? "dark" : "light";
        }
        localStorage.setItem("theme", theme.value);
        applyTheme(isDark.value);
    };

    const setTheme = (newTheme) => {
        theme.value = newTheme;
        localStorage.setItem("theme", newTheme);
        applyTheme(isDark.value);
    };

    const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");
    mediaQuery.addEventListener("change", (e) => {
        systemPreference.value = e.matches ? "dark" : "light";
        if (theme.value === "system") {
            applyTheme(isDark.value);
        }
    });

    const savedTheme = localStorage.getItem("theme");
    if (savedTheme && ["light", "dark", "system"].includes(savedTheme)) {
        theme.value = savedTheme;
    } else {
        theme.value = systemPreference.value;
    }

    // Apply on load
    watch(
        isDark,
        (newValue) => {
            applyTheme(newValue);
        },
        { immediate: true },
    );

    return {
        theme,
        isDark,
        toggleTheme,
        setTheme,
    };
};
