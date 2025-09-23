<template>
    <form class="max-w-sm mx-auto">
        <label
            v-if="label"
            :for="id"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
            >{{ label }}</label
        >
        <select
            multiple
            :id="id"
            :value="modelValue"
            @change="handleChange"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:ring-blue-500"
        >
            <option
                v-for="opt in options"
                :key="opt.value || opt"
                :value="opt.value || opt"
            >
                {{ opt.label || opt }}
            </option>
        </select>
    </form>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    options: { type: Array, default: () => [] },
    label: { type: String, default: "" },
    id: { type: String, default: "multiple-select" },
});

const emit = defineEmits(["update:modelValue"]);

const handleChange = (event) => {
    const selectedValues = Array.from(event.target.selectedOptions).map(
        (option) => option.value
    );
    emit("update:modelValue", selectedValues);
};
</script>
