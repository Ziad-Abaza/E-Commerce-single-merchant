<template>
    <form class="max-w-sm mx-auto">
        <label v-if="label" :for="id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ label }}</label>
        <select :id="id"
            :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option :value="''">{{ placeholder }}</option>
            <option v-for="opt in options" :key="opt.value || opt" :value="opt.value || opt">
                {{ opt.label || opt }}
            </option>
        </select>
    </form>
</template>

<script setup>
const props = defineProps({
    modelValue: { type: [String, Number, null], default: "" },
    options: { type: Array, default: () => [] },
    placeholder: { type: String, default: "Select" },
    label: { type: String, default: "" },
    id: { type: String, default: "select-input" }
});

defineEmits(["update:modelValue"]);
</script>
