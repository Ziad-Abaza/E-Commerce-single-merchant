<template>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <!-- Desktop Table -->
        <table
            class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 hidden md:table"
        >
            <thead
                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
            >
                <tr>
                    <th
                        v-for="(header, index) in visibleHeaders"
                        :key="'headers-' + index"
                        class="px-6 py-3"
                    >
                        <!-- Header Checkbox -->
                        <template v-if="getHeaderObject(header)?.type === 'checkbox'">
                            <input
                                type="checkbox"
                                :checked="getHeaderChecked(header)"
                                @change="getHeaderObject(header)?.onChange"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            />
                        </template>
                        <template v-else>
                            {{ getHeaderLabel(header) }}
                        </template>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(row, rowIndex) in rows"
                    :key="'row-' + rowIndex"
                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200"
                >
                    <td
                        v-for="(headers, colIndex) in visibleHeaders"
                        :key="'cell-' + rowIndex + '-' + colIndex"
                        :class="[
                            'font-medium text-gray-900 whitespace-nowrap dark:text-white',
                            row[headers]?.type === 'image'
                                ? 'px-2 py-2'
                                : 'px-6 py-4',
                        ]"
                    >
                        <!-- Checkbox Cell -->
                        <template v-if="row[headers]?.type === 'checkbox'">
                            <input
                                type="checkbox"
                                :checked="row[headers].checked"
                                @change="row[headers].onChange"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            />
                        </template>

                        <!-- Image Cell -->
                        <template v-else-if="row[headers]?.type === 'image'">
                            <img
                                :src="row[headers].src"
                                alt="Row Image"
                                class="w-16 h-16 object-cover rounded-full cursor-pointer"
                                @click="
                                    $emit(
                                        'preview',
                                        row[headers].full || row[headers].src,
                                    )
                                "
                            />
                        </template>

                        <!-- File Cell -->
                        <template v-else-if="row[headers]?.type === 'file'">
                            <a
                                :href="row[headers].url"
                                download
                                class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700"
                            >
                                Download
                            </a>
                        </template>

                        <!-- Component Cell -->
                        <template
                            v-else-if="
                                row[headers]?.type === 'component' &&
                                row[headers].component
                            "
                        >
                            <component
                                :is="row[headers].component"
                                v-bind="row[headers].props"
                            />
                        </template>

                        <!-- Actions Cell -->
                        <template
                            v-else-if="
                                Array.isArray(row[headers]) &&
                                isActionArray(row[headers])
                            "
                        >
                            <div class="flex gap-2 flex-wrap">
                                <button
                                    v-for="(action, actionIndex) in row[headers]"
                                    :key="'action-' + rowIndex + '-' + actionIndex"
                                    @click="action.onClick(row)"
                                    class="px-3 py-1 flex items-center gap-1 rounded"
                                    :class="action.class"
                                    :style="{ backgroundColor: action.color || '' }"
                                >
                                    <component
                                        v-if="action.icon"
                                        :is="getIconSymbol(action.icon)"
                                        class="w-5 h-5"
                                    />
                                    <span v-if="action.label">{{ action.label }}</span>
                                </button>
                            </div>
                        </template>

                        <!-- Status Cell -->
                        <template v-else-if="row[headers]?.type === 'status'">
                            <span :class="row[headers].class">
                                {{ row[headers].value }}
                            </span>
                        </template>

                        <!-- Roles Cell -->
                        <template v-else-if="row[headers]?.type === 'roles'">
                            <div class="flex flex-wrap gap-1">
                                <span
                                    v-for="role in row[headers].value"
                                    :key="role.id"
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300"
                                >
                                    {{ role.name }}
                                </span>
                                <span
                                    v-if="!row[headers].value || row[headers].value.length === 0"
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300"
                                >
                                    No roles
                                </span>
                            </div>
                        </template>

                        <!-- Variant Cell -->
                        <template v-else-if="row[headers]?.type === 'variant'">
                            <VariantDisplay :variant="row[headers]?.props?.variant || {}" />
                        </template>

                        <!-- Normal Cell -->
                        <template v-else>
                            {{ row[headers] ?? "" }}
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Mobile Card View -->
        <div class="space-y-4 md:hidden">
            <Card
                v-for="(row, rowIndex) in rows"
                :key="'card-' + rowIndex"
                :row="row"
                :headers="visibleHeaders"
                @preview="$emit('preview', $event)"
            />
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import Card from "./Card.vue";
import VariantDisplay from "./VariantDisplay.vue";
import {
  PencilSquareIcon,
  TrashIcon,
  EyeIcon,
  EyeSlashIcon,
  PlusIcon,
  CheckIcon,
  XMarkIcon,
  ArrowDownTrayIcon,
  ArrowUpTrayIcon,
  MagnifyingGlassIcon,
  FunnelIcon,
  ArrowsUpDownIcon,
  Cog6ToothIcon,
  UserIcon,
  UsersIcon,
  HomeIcon,
  ChartBarIcon,
  ListBulletIcon,
  Squares2X2Icon,
  CalendarDaysIcon,
  ClockIcon,
  BellIcon,
  EnvelopeIcon,
  PhoneIcon,
  MapPinIcon,
  StarIcon,
  HeartIcon,
  BookmarkIcon,
  TagIcon,
  FolderIcon,
  DocumentIcon,
  PhotoIcon,
  VideoCameraIcon,
  MusicalNoteIcon,
  LinkIcon,
  ArrowTopRightOnSquareIcon,
  ArrowLeftIcon,
  ArrowRightIcon,
  ArrowUpIcon,
  ArrowDownIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ChevronUpIcon,
  ChevronDownIcon,
  PauseIcon,
  PlayIcon
} from "@heroicons/vue/24/outline";

// Props
const props = defineProps({
    headers: {
        type: Array,
        default: null, // optional
    },
    rows: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(["preview"]);

// Helper to detect action arrays
const isActionArray = (val) => {
    return (
        Array.isArray(val) &&
        val.every((item) => typeof item === "object" && "onClick" in item)
    );
};

// Helper to get header object
const getHeaderObject = (header) => {
    return props.headers?.find(
        (h) => (typeof h === "object" && h.key === header) || h === header,
    );
};

// Helper to get header checked value (handles getter properties)
const getHeaderChecked = (header) => {
    const headerObj = getHeaderObject(header);
    if (headerObj && typeof headerObj.checked === 'function') {
        return headerObj.checked();
    }
    return headerObj?.checked || false;
};

// Helper to get header label
const getHeaderLabel = (header) => {
    const originalHeader = getHeaderObject(header);

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

// If no headers prop, generate from first row keys
const allHeaders = computed(() => {
    if (props.headers && props.headers.length) {
        return props.headers.map((header) => {
            if (typeof header === "object" && header.key) {
                return header.key;
            }
            return header;
        });
    }
    if (props.rows.length) {
        return Object.keys(props.rows[0]);
    }
    return [];
});

const visibleHeaders = computed(() => allHeaders.value);

// Helper to get icon symbol from FontAwesome icon names
const getIconSymbol = (iconName) => {
  const iconMap = {
    edit: PencilSquareIcon,
    trash: TrashIcon,
    eye: EyeIcon,
    "eye-slash": EyeSlashIcon,
    plus: PlusIcon,
    check: CheckIcon,
    times: XMarkIcon,
    download: ArrowDownTrayIcon,
    upload: ArrowUpTrayIcon,
    search: MagnifyingGlassIcon,
    filter: FunnelIcon,
    sort: ArrowsUpDownIcon,
    cog: Cog6ToothIcon,
    user: UserIcon,
    users: UsersIcon,
    home: HomeIcon,
    dashboard: ChartBarIcon,
    chart: ChartBarIcon,
    list: ListBulletIcon,
    grid: Squares2X2Icon,
    calendar: CalendarDaysIcon,
    clock: ClockIcon,
    bell: BellIcon,
    envelope: EnvelopeIcon,
    phone: PhoneIcon,
    location: MapPinIcon,
    star: StarIcon,
    heart: HeartIcon,
    bookmark: BookmarkIcon,
    tag: TagIcon,
    folder: FolderIcon,
    file: DocumentIcon,
    image: PhotoIcon,
    video: VideoCameraIcon,
    music: MusicalNoteIcon,
    link: LinkIcon,
    "external-link": ArrowTopRightOnSquareIcon,
    "arrow-left": ArrowLeftIcon,
    "arrow-right": ArrowRightIcon,
    "arrow-up": ArrowUpIcon,
    "arrow-down": ArrowDownIcon,
    "chevron-left": ChevronLeftIcon,
    "chevron-right": ChevronRightIcon,
    "chevron-up": ChevronUpIcon,
    "chevron-down": ChevronDownIcon,
    pause: PauseIcon,
    play: PlayIcon,
  };

  return iconMap[iconName] || null;
};

</script>
