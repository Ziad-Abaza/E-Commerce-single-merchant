// stores/search.js
import { defineStore } from "pinia";
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useProductStore } from "./products";
import { debounce } from "lodash-es";

export const useSearchStore = defineStore("search", () => {
    const router = useRouter();
    const productStore = useProductStore();

    const query = ref("");
    const suggestions = ref([]);
    const results = ref([]);
    const recentSearches = ref([]);
    const loading = ref(false);

    // Load recent searches from localStorage
    const loadRecentSearches = () => {
        const saved = localStorage.getItem("recent_searches");
        if (saved) recentSearches.value = JSON.parse(saved);
    };

    const saveRecentSearches = () => {
        localStorage.setItem(
            "recent_searches",
            JSON.stringify(recentSearches.value),
        );
    };

    const addToRecentSearches = (term) => {
        recentSearches.value = recentSearches.value.filter((s) => s !== term);
        recentSearches.value.unshift(term);
        recentSearches.value = recentSearches.value.slice(0, 5);
        saveRecentSearches();
    };

    const clearSearch = () => {
        query.value = "";
        suggestions.value = [];
        results.value = [];
        router.push({ name: "home" });
    };

    // search suggestions with debounce
    const fetchSuggestions = debounce(async (term) => {
        if (term.length < 2) {
            suggestions.value = [];
            return;
        }
        try {
            loading.value = true;
            const res = await productStore.searchProducts(term, { limit: 5 });
            suggestions.value = res.success ? res.data.data : [];
        } finally {
            loading.value = false;
        }
    }, 300);

    // search products
    const performSearch = async (extraParams = {}) => {
        if (!query.value.trim()) {
            clearSearch();
            return;
        }
        loading.value = true;
        try {
            addToRecentSearches(query.value.trim());
            router.push({
                name: "search",
                query: { q: query.value.trim(), ...extraParams },
            });
            const res = await productStore.searchProducts(
                query.value.trim(),
                extraParams,
            );
            results.value = res.success ? res.data.data : [];
        } finally {
            loading.value = false;
        }
    };

    return {
        query,
        suggestions,
        results,
        recentSearches,
        loading,
        fetchSuggestions,
        performSearch,
        clearSearch,
        loadRecentSearches,
    };
});
