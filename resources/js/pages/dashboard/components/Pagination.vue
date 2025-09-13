<!-- Dashboard Pagination Component -->
<template>
    <div
        v-if="total > 0"
        class="px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-gray-700 dark:text-gray-300"
    >
        <!-- Results Info & Per Page Selector -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="text-sm">
                Showing
                <span class="font-semibold">{{ from }}</span>
                to
                <span class="font-semibold">{{ to }}</span>
                of
                <span class="font-semibold">{{ total }}</span>
                results
            </div>
            <div class="flex items-center gap-2">
                <label for="per-page-select" class="text-sm whitespace-nowrap"
                    >Show:</label
                >
                <Select
                    :model-value="perPage"
                    :options="perPageOptions"
                    id="per-page-select"
                    @update:model-value="$emit('update:perPage', $event)"
                    class="w-20"
                />
            </div>
        </div>

        <!-- Pagination Buttons -->
        <div class="flex items-center gap-1">
            <!-- Previous Button -->
            <button
                @click="$emit('page-change', currentPage - 1)"
                :disabled="currentPage <= 1"
                class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition"
                aria-label="Previous page"
            >
                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>
            </button>

            <!-- Page Numbers -->
            <div class="flex gap-1">
                <template v-for="page in visiblePages" :key="page">
                    <button
                        v-if="page !== '...'"
                        @click="$emit('page-change', page)"
                        :class="[
                            'flex items-center justify-center w-10 h-10 text-sm font-medium rounded-lg transition',
                            page === currentPage
                                ? 'bg-blue-600 text-white'
                                : 'hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600',
                        ]"
                    >
                        {{ page }}
                    </button>
                    <span
                        v-else
                        class="flex items-center justify-center w-10 h-10 text-gray-500 dark:text-gray-400"
                    >
                        ...
                    </span>
                </template>
            </div>

            <!-- Next Button -->
            <button
                @click="$emit('page-change', currentPage + 1)"
                :disabled="currentPage >= lastPage"
                class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition"
                aria-label="Next page"
            >
                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5l7 7-7 7"
                    />
                </svg>
            </button>
        </div>
    </div>
</template>

<!-- Pagination.vue -->
<script setup>
import { computed } from "vue";
import Select from "./Select.vue";

// Props
const props = defineProps({
    total: { type: Number, required: true },
    currentPage: { type: Number, required: true },
    perPage: {
        type: [Number, String],
        required: true,
        validator: (value) => {
            const num = Number(value);
            return !isNaN(num) && num > 0;
        }
    },
    lastPage: { type: Number, required: true },
    from: { type: Number, default: 0 },
    to: { type: Number, default: 0 },
});

// Emit
const emit = defineEmits(["page-change", "update:perPage"]);

// Per page options
const perPageOptions = [
    { value: 5, label: "5" },
    { value: 10, label: "10" },
    { value: 25, label: "25" },
    { value: 50, label: "50" },
];

// Visible Pages Logic
const visiblePages = computed(() => {
    const current = props.currentPage;
    const last = props.lastPage;

    if (last <= 7) {
        return Array.from({ length: last }, (_, i) => i + 1);
    }
    if (current <= 4) {
        return [1, 2, 3, 4, 5, "...", last];
    }
    if (current >= last - 3) {
        return [1, "...", last - 4, last - 3, last - 2, last - 1, last];
    }
    return [1, "...", current - 1, current, current + 1, "...", last];
});
</script>
