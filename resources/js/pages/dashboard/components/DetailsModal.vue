<!-- resources/js/components/DetailsModal.vue -->
<template>
    <div
        v-if="show"
        class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex justify-center items-start p-4"
        @click.self="$emit('close')"
    >
        <div
            class="relative max-w-4xl w-full mx-auto my-8 bg-white dark:bg-gray-800 rounded-xl shadow-2xl transform transition-all duration-300 animate-scale-in"
            @click.stop
        >
            <!-- Modal Header -->
            <div
                class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700"
            >
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <svg
                            class="w-5 h-5 text-blue-600 dark:text-blue-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <div>
                        <h3
                            class="text-xl font-bold text-gray-900 dark:text-white"
                        >
                            {{ modalTitle }}
                        </h3>
                        <p
                            class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                        >
                            {{ modalSubtitle }}
                        </p>
                    </div>
                </div>
                <button
                    @click="$emit('close')"
                    class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200"
                    aria-label="Close modal"
                >
                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div v-if="item" class="p-6">
                <div class="grid grid-cols-1 gap-6">
                    <!-- Main Image Section -->
                    <div v-if="mainMedia" class="lg:col-span-1">
                        <div
                            class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 h-full flex flex-col"
                        >
                            <h4
                                class="font-semibold text-gray-900 dark:text-white mb-4 text-center"
                            >
                                {{ imageSectionTitle }}
                            </h4>
                            <div
                                class="flex-1 flex items-center justify-center"
                            >
                                <img
                                    :src="mainMedia.src"
                                    :alt="item.name || 'Item'"
                                    class="w-full max-h-80 object-contain rounded-lg shadow-md cursor-pointer transition-transform duration-300 hover:scale-105"
                                    @click="
                                        $emit(
                                            'preview',
                                            mainMedia.full || mainMedia.src,
                                        )
                                    "
                                />
                            </div>

                            <!-- Gallery Images -->
                            <div v-if="hasGalleryImages" class="mt-4">
                                <h5
                                    class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                >
                                    Gallery ({{ galleryImages.length }})
                                </h5>
                                <div
                                    class="flex space-x-2 overflow-x-auto pb-2"
                                >
                                    <div
                                        v-for="(
                                            image, index
                                        ) in galleryImages.slice(0, 5)"
                                        :key="index"
                                        class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden cursor-pointer border-2 border-transparent hover:border-blue-500 transition-colors duration-200"
                                        @click="
                                            $emit('preview', image.url || image)
                                        "
                                    >
                                        <img
                                            :src="image.url || image"
                                            :alt="`Gallery image ${index + 1}`"
                                            class="w-full h-full object-cover"
                                        />
                                    </div>
                                    <div
                                        v-if="galleryImages.length > 5"
                                        class="flex-shrink-0 w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center text-xs font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        +{{ galleryImages.length - 5 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details Information Section -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Sections dynamically generated based on item type -->
                        <div
                            v-for="(section, sectionIndex) in sections"
                            :key="sectionIndex"
                            class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5"
                        >
                            <h4
                                class="font-semibold text-lg text-gray-900 dark:text-white mb-4 flex items-center"
                            >
                                <svg
                                    class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>
                                {{ section.title }}
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div
                                    v-for="(
                                        field, fieldIndex
                                    ) in section.fields"
                                    :key="fieldIndex"
                                    class="space-y-2"
                                >
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        {{ field.label }}
                                    </label>

                                    <!-- Handle different field types -->
                                    <template v-if="field.type === 'boolean'">
                                        <span
                                            :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                field.value
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                                                    : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                            ]"
                                        >
                                            {{
                                                field.value
                                                    ? "Active"
                                                    : "Inactive"
                                            }}
                                        </span>
                                    </template>

                                    <template
                                        v-else-if="
                                            field.type === 'array' &&
                                            Array.isArray(field.value)
                                        "
                                    >
                                        <div class="flex flex-wrap gap-2">
                                            <span
                                                v-for="(
                                                    item, idx
                                                ) in field.value"
                                                :key="idx"
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300"
                                            >
                                                {{
                                                    typeof item === "object"
                                                        ? item.name ||
                                                          item.label ||
                                                          JSON.stringify(item)
                                                        : item
                                                }}
                                            </span>
                                            <span
                                                v-if="field.value.length === 0"
                                                class="text-sm text-gray-500"
                                                >No items</span
                                            >
                                        </div>
                                    </template>

                                    <template
                                        v-else-if="
                                            field.type === 'object' &&
                                            typeof field.value === 'object'
                                        "
                                    >
                                        <div
                                            class="bg-white dark:bg-gray-700 p-3 rounded-lg text-sm"
                                        >
                                            <pre class="whitespace-pre-wrap">{{
                                                JSON.stringify(
                                                    field.value,
                                                    null,
                                                    2,
                                                )
                                            }}</pre>
                                        </div>
                                    </template>

                                    <template
                                        v-else-if="field.type === 'image'"
                                    >
                                        <img
                                            :src="field.value"
                                            :alt="field.label"
                                            class="w-32 h-32 object-cover rounded cursor-pointer"
                                            @click="
                                                $emit('preview', field.value)
                                            "
                                        />
                                    </template>

                                    <template v-else-if="field.type === 'file'">
                                        <a
                                            :href="field.value"
                                            download
                                            class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700"
                                        >
                                            Download File
                                        </a>
                                    </template>

                                    <template
                                        v-else-if="
                                            field.type === 'richtext' ||
                                            field.label
                                                .toLowerCase()
                                                .includes('description')
                                        "
                                    >
                                        <div
                                            v-html="field.value"
                                            class="prose max-w-none text-sm text-gray-900 dark:text-white max-h-64 overflow-y-auto"
                                        ></div>
                                    </template>

                                    <template v-else>
                                        <p
                                            class="text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            {{
                                                field.value !== null &&
                                                field.value !== undefined
                                                    ? field.value.toString()
                                                    : "N/A"
                                            }}
                                        </p>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-b-xl"
            >
                <div
                    class="text-sm text-gray-500 dark:text-gray-400 mb-3 sm:mb-0"
                >
                    {{ idLabel }}: <span class="font-mono">{{ item.id }}</span>
                </div>
                <div class="flex space-x-3">
                    <button
                        @click="$emit('close')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
                    >
                        Close
                    </button>
                    <button
                        v-if="editable"
                        @click="$emit('edit', item)"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center transition-all duration-200 transform hover:scale-105"
                    >
                        <svg
                            class="w-4 h-4 mr-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            />
                        </svg>
                        Edit
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";

const props = defineProps({
    show: { type: Boolean, default: false },
    item: { type: Object, default: null },
    editable: { type: Boolean, default: false },
    // Customization props
    title: { type: String, default: "" },
    subtitle: { type: String, default: "" },
    imageSectionTitle: { type: String, default: "Image" },
    idLabel: { type: String, default: "ID" },
    // Custom sections configuration
    customSections: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["close", "edit", "preview"]);

// Computed properties for dynamic content
const modalTitle = computed(() => {
    return (
        props.title ||
        (props.item?.name ? `${props.item.name} Details` : "Item Details")
    );
});

const modalSubtitle = computed(() => {
    return props.subtitle || "Complete information about this item";
});

const imageSectionTitle = computed(() => {
    return props.imageSectionTitle;
});

const idLabel = computed(() => {
    return props.idLabel;
});

// Determine main media (first image/file)
const mainMedia = computed(() => {
    if (!props.item) return null;

    // Try main image first
    if (props.item.main_image) {
        return {
            type: "image",
            src: props.item.main_image,
            full: props.item.main_image,
        };
    }

    // Try main_image_url
    if (props.item.main_image_url) {
        return {
            type: "image",
            src: props.item.main_image_url,
            full: props.item.main_image_url,
        };
    }

    // Try image_url
    if (props.item.image_url) {
        return {
            type: "image",
            src: props.item.image_url,
            full: props.item.image_url,
        };
    }

    // Try first image from images array
    if (Array.isArray(props.item.images) && props.item.images.length > 0) {
        const firstImage = props.item.images[0];
        if (typeof firstImage === "string") {
            return {
                type: "image",
                src: firstImage,
                full: firstImage,
            };
        } else if (firstImage && firstImage.url) {
            return {
                type: "image",
                src: firstImage.url,
                full: firstImage.url,
            };
        }
    }

    // Try first image from gallery_images
    if (
        Array.isArray(props.item.gallery_images) &&
        props.item.gallery_images.length > 0
    ) {
        const firstImage = props.item.gallery_images[0];
        if (typeof firstImage === "string") {
            return {
                type: "image",
                src: firstImage,
                full: firstImage,
            };
        } else if (firstImage && firstImage.url) {
            return {
                type: "image",
                src: firstImage.url,
                full: firstImage.url,
            };
        }
    }

    // Try images_url
    if (
        Array.isArray(props.item.images_url) &&
        props.item.images_url.length > 0
    ) {
        return {
            type: "image",
            src: props.item.images_url[0],
            full: props.item.images_url[0],
        };
    }

    return null;
});

// Gallery images
const galleryImages = computed(() => {
    if (!props.item) return [];

    // Try gallery_images first
    if (Array.isArray(props.item.gallery_images)) {
        return props.item.gallery_images;
    }

    // Try images array
    if (Array.isArray(props.item.images)) {
        return props.item.images;
    }

    // Try images_url
    if (Array.isArray(props.item.images_url)) {
        return props.item.images_url.map((url) => ({ url }));
    }

    return [];
});

const hasGalleryImages = computed(() => {
    return galleryImages.value && galleryImages.value.length > 0;
});

// Generate sections dynamically based on item type
const sections = computed(() => {
    // If custom sections are provided, use them
    if (props.customSections && props.customSections.length > 0) {
        return props.customSections;
    }

    // Otherwise, generate sections based on item properties
    if (!props.item) return [];

    const sections = [];

    // Basic Information section
    const basicFields = [];
    const basicKeys = [
        "name",
        "brand",
        "sku",
        "price",
        "stock",
        "stock_quantity",
        "final_price",
        "discount",
        "discount_percentage",
    ];

    basicKeys.forEach((key) => {
        if (props.item[key] !== undefined && props.item[key] !== null) {
            basicFields.push({
                label: formatLabel(key),
                value: props.item[key],
                type: typeof props.item[key],
            });
        }
    });

    if (basicFields.length > 0) {
        sections.push({
            title: "Basic Information",
            fields: basicFields,
        });
    }

    // Status & Stock section
    const statusFields = [];
    const statusKeys = [
        "is_active",
        "status",
        "is_in_stock",
        "is_low_stock",
        "is_out_of_stock",
        "min_stock_alert",
        "stock_alert_level",
    ];

    statusKeys.forEach((key) => {
        if (props.item[key] !== undefined && props.item[key] !== null) {
            statusFields.push({
                label: formatLabel(key),
                value: props.item[key],
                type: typeof props.item[key],
            });
        }
    });

    if (statusFields.length > 0) {
        sections.push({
            title: "Status & Stock",
            fields: statusFields,
        });
    }

    // Product Details section
    const productFields = [];
    const productKeys = [
        "size",
        "color",
        "material",
        "weight",
        "weight_in_grams",
        "origin",
        "quality",
        "packaging",
        "sku_variant",
        "barcode",
    ];

    productKeys.forEach((key) => {
        if (props.item[key] !== undefined && props.item[key] !== null) {
            productFields.push({
                label: formatLabel(key),
                value: props.item[key],
                type: typeof props.item[key],
            });
        }
    });

    if (productFields.length > 0) {
        sections.push({
            title: "Product Details",
            fields: productFields,
        });
    }

    // Product Attributes section
    if (
        props.item.attributes &&
        Array.isArray(props.item.attributes) &&
        props.item.attributes.length > 0
    ) {
        const attributeFields = [];

        // Group attributes by their name
        const groupedAttributes = {};

        props.item.attributes.forEach((attr) => {
            if (!groupedAttributes[attr.name]) {
                groupedAttributes[attr.name] = [];
            }
            if (attr.values && Array.isArray(attr.values)) {
                groupedAttributes[attr.name].push(...attr.values);
            }
        });

        // Create fields for each attribute group
        Object.entries(groupedAttributes).forEach(([name, values]) => {
            if (values && values.length > 0) {
                const valueList = values
                    .filter((v) => v.is_visible !== false)
                    .map((v) => v.value || v.name || v)
                    .join(", ");

                if (valueList) {
                    attributeFields.push({
                        label: name,
                        value: valueList,
                        type: "string",
                    });
                }
            }
        });

        if (attributeFields.length > 0) {
            sections.push({
                title: "Product Attributes",
                fields: attributeFields,
                icon: "<svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z' /></svg>",
            });
        }
    }

    // Dimensions section
    if (props.item.dimensions && typeof props.item.dimensions === "object") {
        const dimensionFields = [];
        const dimensionKeys = ["length", "width", "height", "volume"];

        dimensionKeys.forEach((key) => {
            if (
                props.item.dimensions[key] !== undefined &&
                props.item.dimensions[key] !== null
            ) {
                dimensionFields.push({
                    label: formatLabel(key),
                    value: props.item.dimensions[key],
                    type: typeof props.item.dimensions[key],
                });
            }
        });

        if (dimensionFields.length > 0) {
            sections.push({
                title: "Dimensions",
                fields: dimensionFields,
            });
        }
    }

    // Additional Information section
    const additionalFields = [];
    const additionalKeys = [
        "created_at",
        "updated_at",
        "published_at",
        "expires_at",
    ];

    additionalKeys.forEach((key) => {
        if (props.item[key] !== undefined && props.item[key] !== null) {
            additionalFields.push({
                label: formatLabel(key),
                value: formatDate(props.item[key]),
                type: "string",
            });
        }
    });

    if (additionalFields.length > 0) {
        sections.push({
            title: "Additional Information",
            fields: additionalFields,
        });
    }

    // Categories section
    if (
        props.item.categories &&
        Array.isArray(props.item.categories) &&
        props.item.categories.length > 0
    ) {
        sections.push({
            title: "Categories",
            fields: [
                {
                    label: "Categories",
                    value: props.item.categories,
                    type: "array",
                },
            ],
        });
    }

    return sections;
});

// Helper functions
const formatLabel = (key) => {
    return key
        .replace(/_/g, " ")
        .replace(/\b\w/g, (l) => l.toUpperCase())
        .replace("Id", "ID")
        .replace("Url", "URL");
};

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    try {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        }).format(date);
    } catch (e) {
        return dateString;
    }
};
</script>

<style scoped>
.animate-scale-in {
    animation: scaleIn 0.3s ease-out forwards;
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.prose :deep(*) {
    margin: 0 0 0.5em 0;
}

.prose :deep(p) {
    margin-bottom: 0.5em;
}

.prose :deep(p:last-child) {
    margin-bottom: 0;
}
</style>
