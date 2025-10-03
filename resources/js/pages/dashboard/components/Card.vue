<template>
  <div
    class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-900 shadow-sm"
  >
    <!-- Main Image at top -->
    <div v-if="mainImageKey && row[mainImageKey]" class="mb-3 h-40">
      <!-- image -->
      <img
        v-if="row[mainImageKey]?.type === 'image'"
        :src="row[mainImageKey].src"
        alt="Card Image"
        class="w-full h-full object-fill rounded cursor-pointer"
        @click="$emit('preview', row[mainImageKey].full || row[mainImageKey].src)"
      />

      <!-- file -->
      <a
        v-else-if="row[mainImageKey]?.type === 'file'"
        :href="row[mainImageKey].url"
        download
        class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700"
      >
        Download File
      </a>
    </div>

    <!-- Data Fields -->
    <div
      v-for="(header, colIndex) in displayHeaders"
      :key="'card-cell-' + colIndex"
      class="mb-2"
    >
      <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">
        {{ getHeaderLabel(header) }}
      </div>
      <div class="text-sm text-gray-900 dark:text-white mt-1">
        <!-- Actions -->
        <template v-if="Array.isArray(row[header]) && isActionArray(row[header])">
          <div class="flex gap-2 flex-wrap">
            <button
              v-for="(action, actionIndex) in row[header]"
              :key="'card-action-' + actionIndex"
              @click="action.onClick(row)"
              class="px-3 py-1 flex items-center gap-1 rounded"
              :class="action.class"
              :style="{ backgroundColor: action.color || '' }"
            >
              <span v-if="action.icon" class="text-sm">{{ getIconSymbol(action.icon) }}</span>
              <span v-if="action.label">{{ action.label }}</span>
            </button>
          </div>
        </template>

        <!-- Inline image -->
        <template v-else-if="row[header]?.type === 'image'">
          <img
            :src="row[header].src"
            alt="Row Image"
            class="w-16 h-16 object-cover rounded cursor-pointer"
            @click="$emit('preview', row[header].full || row[header].src)"
          />
        </template>

        <!-- Inline file -->
        <template v-else-if="row[header]?.type === 'file'">
          <a
            :href="row[header].url"
            download
            class="inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
          >
            Download
          </a>
        </template>

        <!-- Status -->
        <template v-else-if="row[header]?.type === 'status'">
          <span :class="row[header].class">
            {{ row[header].value }}
          </span>
        </template>

        <!-- Roles -->
        <template v-else-if="row[header]?.type === 'roles'">
          <div class="flex flex-wrap gap-1">
            <span
              v-for="role in row[header].value"
              :key="role.id"
              class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300"
            >
              {{ role.name }}
            </span>
            <span
              v-if="!row[header].value || row[header].value.length === 0"
              class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300"
            >
              No roles
            </span>
          </div>
        </template>

        <!-- Variant -->
        <template v-else-if="row[header]?.type === 'variant'">
          <VariantDisplay :variant="row[header]?.props?.variant || {}" />
        </template>

        <!-- Normal Text -->
        <template v-else>
          {{ row[header] ?? "" }}
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import VariantDisplay from "./VariantDisplay.vue";

// Props
const props = defineProps({
  row: { type: Object, required: true },
  headers: { type: Array, required: true },
});

const emit = defineEmits(["preview"]);

// Detect actions
const isActionArray = (val) => {
  return (
    Array.isArray(val) &&
    val.every((item) => typeof item === "object" && "onClick" in item)
  );
};

// Helper to get header label
const getHeaderLabel = (header) => {
  const originalHeader = props.headers?.find(
    (h) => (typeof h === "object" && h.key === header) || h === header
  );

  if (typeof originalHeader === "object" && originalHeader.label) {
    return originalHeader.label;
  }
  if (typeof header === "string") {
    return header
      .split("_")
      .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
      .join(" ");
  }
  return header;
};

// Find first media column (image/file)
const mainImageKey = computed(() => {
  for (const key of props.headers) {
    if (props.row[key]?.type === "image" || props.row[key]?.type === "file") {
      return key;
    }
  }
  return null;
});

// Filter visible headers
const visibleHeaders = computed(() => {
  return props.headers.filter((h) => {
    if (h === mainImageKey.value) {
      return !!props.row[h];
    }
    return true;
  });
});

// Filter headers excluding the main media key
const displayHeaders = computed(() => {
  return visibleHeaders.value.filter((header) => header !== mainImageKey.value);
});

// Helper to get icon symbol from FontAwesome icon names
const getIconSymbol = (iconName) => {
  const iconMap = {
    'edit': '✏️',
    'trash': '🗑️',
    'eye': '👁️',
    'plus': '➕',
    'check': '✅',
    'times': '❌',
    'download': '⬇️',
    'upload': '⬆️',
    'search': '🔍',
    'filter': '🔽',
    'sort': '↕️',
    'cog': '⚙️',
    'user': '👤',
    'users': '👥',
    'home': '🏠',
    'dashboard': '📊',
    'chart': '📈',
    'list': '📋',
    'grid': '⊞',
    'calendar': '📅',
    'clock': '🕐',
    'bell': '🔔',
    'envelope': '✉️',
    'phone': '📞',
    'location': '📍',
    'star': '⭐',
    'heart': '❤️',
    'bookmark': '🔖',
    'tag': '🏷️',
    'folder': '📁',
    'file': '📄',
    'image': '🖼️',
    'video': '🎥',
    'music': '🎵',
    'link': '🔗',
    'external-link': '↗️',
    'arrow-left': '←',
    'arrow-right': '→',
    'arrow-up': '↑',
    'arrow-down': '↓',
    'chevron-left': '‹',
    'chevron-right': '›',
    'chevron-up': '⌃',
    'chevron-down': '⌄'
  };
  
  // Handle array format like ['fas', 'edit']
  if (Array.isArray(iconName)) {
    return iconMap[iconName[1]] || iconMap[iconName[0]] || '•';
  }
  
  // Handle string format
  return iconMap[iconName] || '•';
};
</script>
