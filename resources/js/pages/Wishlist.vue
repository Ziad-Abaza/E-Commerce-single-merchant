<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="container py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    My Wishlist
                </h1>
                <p class="text-gray-600 mt-2 dark:text-gray-300">
                    Save your favorite products for later
                </p>
            </div>

            <!-- Loading State -->
            <div v-if="wishlistStore.loading" class="flex justify-center py-12">
                <div
                    class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 dark:border-primary-500"
                ></div>
            </div>

            <!-- Empty Wishlist State -->
            <div
                v-else-if="wishlistStore.items.length === 0"
                class="text-center py-16"
            >
                <div
                    class="inline-flex items-center justify-center w-24 h-24 bg-primary-100 rounded-full mb-6 dark:bg-primary-900/30"
                >
                    <svg
                        class="w-12 h-12 text-primary-600 dark:text-primary-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                        />
                    </svg>
                </div>
                <h3
                    class="text-2xl font-semibold text-gray-900 mb-2 dark:text-white"
                >
                    Your wishlist is empty
                </h3>
                <p
                    class="text-gray-500 max-w-md mx-auto mb-8 dark:text-gray-400"
                >
                    Discover amazing products and add them to your wishlist for
                    later.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <router-link
                        to="/products"
                        class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 transform hover:scale-105 dark:bg-primary-700 dark:hover:bg-primary-800"
                    >
                        <svg
                            class="h-5 w-5 mr-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                            />
                        </svg>
                        Continue Shopping
                    </router-link>
                    <button
                        @click="showAddCategoryModal = true"
                        class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700"
                    >
                        <svg
                            class="h-5 w-5 mr-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                            />
                        </svg>
                        Create Category
                    </button>
                </div>
            </div>

            <!-- Wishlist Content -->
            <div v-else class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar - Categories -->
                <div class="lg:w-64 flex-shrink-0">
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-4 dark:bg-gray-800 dark:border-gray-700"
                    >
                        <div class="flex items-center justify-between mb-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                Categories
                            </h3>
                            <button
                                @click="showAddCategoryModal = true"
                                class="p-2 bg-primary-100 text-primary-600 rounded-lg hover:bg-primary-200 transition-all duration-200 dark:bg-primary-900/30 dark:text-primary-400 dark:hover:bg-primary-800/30"
                                title="Add new category"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                    />
                                </svg>
                            </button>
                        </div>

                        <!-- Default Category -->
                        <div class="mb-4">
                            <button
                                @click="wishlistStore.selectCategory(null)"
                                :class="[
                                    'w-full text-left px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 flex items-center',
                                    !wishlistStore.selectedCategory
                                        ? 'bg-primary-100 text-primary-700 shadow-sm dark:bg-primary-900/30 dark:text-primary-400'
                                        : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700',
                                ]"
                            >
                                <div class="flex items-center flex-1">
                                    <div
                                        class="p-2 bg-primary-50 rounded-lg mr-3 dark:bg-primary-900/20"
                                    >
                                        <svg
                                            class="h-4 w-4 text-primary-600 dark:text-primary-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                                            />
                                        </svg>
                                    </div>
                                    <span class="flex-1">All Items</span>
                                    <span
                                        class="ml-2 text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded-full dark:bg-gray-700 dark:text-gray-300"
                                    >
                                        {{ wishlistStore.itemCount }}
                                    </span>
                                </div>
                            </button>
                        </div>

                        <!-- Default Favorites Category -->
                        <div v-if="wishlistStore.defaultCategory" class="mb-4">
                            <button
                                @click="
                                    wishlistStore.selectCategory(
                                        wishlistStore.defaultCategory.id,
                                    )
                                "
                                :class="[
                                    'w-full text-left px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 flex items-center',
                                    wishlistStore.selectedCategory?.id ===
                                    wishlistStore.defaultCategory.id
                                        ? 'bg-primary-100 text-primary-700 shadow-sm dark:bg-primary-900/30 dark:text-primary-400'
                                        : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700',
                                ]"
                            >
                                <div class="flex items-center flex-1">
                                    <div
                                        class="p-2 bg-yellow-50 rounded-lg mr-3 dark:bg-yellow-900/20"
                                    >
                                        <svg
                                            class="h-4 w-4 text-yellow-600 dark:text-yellow-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"
                                            />
                                        </svg>
                                    </div>
                                    <span class="flex-1">{{
                                        wishlistStore.defaultCategory.name
                                    }}</span>
                                    <span
                                        class="ml-2 text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded-full dark:bg-gray-700 dark:text-gray-300"
                                    >
                                        {{
                                            wishlistStore.defaultCategory
                                                .item_count
                                        }}
                                    </span>
                                </div>
                            </button>
                        </div>

                        <!-- Custom Categories -->
                        <div
                            v-if="wishlistStore.customCategories.length > 0"
                            class="mb-4"
                        >
                            <h4
                                class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3 dark:text-gray-400"
                            >
                                Custom Categories
                            </h4>
                            <div class="space-y-2">
                                <div
                                    v-for="category in wishlistStore.customCategories"
                                    :key="category.id"
                                    class="group relative"
                                >
                                    <button
                                        @click="
                                            wishlistStore.selectCategory(
                                                category.id,
                                            )
                                        "
                                        :class="[
                                            'w-full text-left px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 flex items-center',
                                            wishlistStore.selectedCategory
                                                ?.id === category.id
                                                ? 'bg-primary-100 text-primary-700 shadow-sm dark:bg-primary-900/30 dark:text-primary-400'
                                                : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700',
                                        ]"
                                    >
                                        <div class="flex items-center flex-1">
                                            <div
                                                class="p-2 bg-blue-50 rounded-lg mr-3 dark:bg-blue-900/20"
                                            >
                                                <svg
                                                    class="h-4 w-4 text-blue-600 dark:text-blue-400"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                                    />
                                                </svg>
                                            </div>
                                            <span class="flex-1">{{
                                                category.name
                                            }}</span>
                                            <span
                                                class="ml-2 text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded-full dark:bg-gray-700 dark:text-gray-300"
                                            >
                                                {{ category.item_count }}
                                            </span>
                                        </div>
                                    </button>

                                    <!-- Category Actions -->
                                    <div
                                        class="absolute right-2 top-1/2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all duration-200 flex space-x-1"
                                    >
                                        <button
                                            @click.stop="editCategory(category)"
                                            class="p-1.5 text-gray-400 hover:text-primary-600 bg-white rounded-lg shadow-sm hover:shadow transition-all duration-200 dark:bg-gray-700 dark:hover:text-primary-400"
                                            title="Edit category"
                                        >
                                            <svg
                                                class="h-4 w-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                                />
                                            </svg>
                                        </button>
                                        <button
                                            @click.stop="
                                                deleteCategory(category.id)
                                            "
                                            :disabled="category.is_default"
                                            class="p-1.5 text-gray-400 hover:text-red-600 bg-white rounded-lg shadow-sm hover:shadow transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:hover:text-red-400"
                                            :title="
                                                category.is_default
                                                    ? 'Cannot delete default category'
                                                    : 'Delete category'
                                            "
                                        >
                                            <svg
                                                class="h-4 w-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wishlist Items -->
                <div class="flex-1">
                    <!-- Category Header -->
                    <div v-if="wishlistStore.selectedCategory" class="mb-6">
                        <div
                            class="flex items-center justify-between bg-white rounded-xl p-6 shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700"
                        >
                            <div class="flex items-center">
                                <div
                                    class="p-3 bg-primary-100 rounded-lg mr-4 dark:bg-primary-900/20"
                                >
                                    <svg
                                        class="h-6 w-6 text-primary-600 dark:text-primary-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <h2
                                        class="text-xl font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{
                                            wishlistStore.selectedCategory.name
                                        }}
                                    </h2>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        {{
                                            wishlistStore.filteredItems.length
                                        }}
                                        items in this category
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button
                                    @click="moveItemsToCategory"
                                    class="px-4 py-2 text-sm text-gray-700 hover:text-primary-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 flex items-center dark:text-gray-300 dark:border-gray-600 dark:hover:text-primary-400 dark:hover:bg-gray-700"
                                >
                                    <svg
                                        class="h-4 w-4 mr-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"
                                        />
                                    </svg>
                                    Move Items
                                </button>
                                <button
                                    @click="clearCategory"
                                    class="px-4 py-2 text-sm text-red-700 hover:text-red-800 border border-red-300 rounded-lg hover:bg-red-50 transition-all duration-200 flex items-center dark:text-red-400 dark:border-red-600 dark:hover:text-red-300 dark:hover:bg-red-900/30"
                                >
                                    <svg
                                        class="h-4 w-4 mr-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        />
                                    </svg>
                                    Clear
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Items List Responsive -->
                    <div class="space-y-4">
                        <div
                            v-for="item in wishlistStore.filteredItems"
                            :key="item.id"
                            class="flex flex-col sm:flex-row items-start sm:items-center bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition duration-300 p-4 dark:bg-gray-800 dark:border-gray-700"
                        >
                            <!-- Product Image -->
                            <div
                                class="relative w-full sm:w-32 h-40 sm:h-28 flex-shrink-0 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700"
                            >
                                <router-link
                                    :to="{
                                        name: 'product-detail',
                                        params: { id: item.product?.id },
                                    }"
                                    class="block w-full h-full"
                                >
                                    <img
                                        :src="getProductImageUrl(item)"
                                        :alt="item.product?.name || 'Product'"
                                        class="w-full h-full object-cover object-center transition-transform duration-300 hover:scale-105"
                                        @error="handleImageError"
                                    />
                                </router-link>

                                <!-- Badges -->
                                <div class="absolute top-2 left-2 space-y-1">
                                    <span
                                        v-if="!item.is_product_in_stock"
                                        class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"
                                    >
                                        Out of Stock
                                    </span>
                                    <span
                                        v-if="item.has_product_discount"
                                        class="bg-green-500 text-white text-xs px-2 py-0.5 rounded-full"
                                    >
                                        On Sale
                                    </span>
                                </div>

                                <!-- Remove Button -->
                                <button
                                    @click="removeFromWishlist(item.id)"
                                    class="absolute top-2 right-2 p-1.5 bg-white rounded-full shadow hover:bg-gray-100 transition dark:bg-gray-700 dark:hover:bg-gray-600"
                                >
                                    <svg
                                        class="h-4 w-4 text-red-500"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <!-- Product Info -->
                            <div class="flex-1 mt-4 sm:mt-0 sm:ml-5 w-full">
                                <!-- Category -->
                                <div
                                    v-if="
                                        !wishlistStore.selectedCategory &&
                                        item.category
                                    "
                                    class="mb-1"
                                >
                                    <span
                                        class="text-xs font-medium text-primary-700 bg-primary-50 px-2 py-0.5 rounded-full dark:text-primary-400 dark:bg-primary-900/30"
                                    >
                                        {{ item.category.name }}
                                    </span>
                                </div>

                                <!-- Title -->
                                <h3
                                    class="text-base font-semibold text-gray-900 mb-1 line-clamp-1 dark:text-white"
                                >
                                    <router-link
                                        :to="{
                                            name: 'product-detail',
                                            params: { id: item.product?.id },
                                        }"
                                        class="hover:text-primary-600 transition dark:hover:text-primary-400"
                                    >
                                        {{ item.product?.name || "Product" }}
                                    </router-link>
                                </h3>

                                <!-- Description -->
                                <p
                                    v-if="item.product?.short_description"
                                    class="text-sm text-gray-500 line-clamp-2 sm:line-clamp-1 dark:text-gray-400"
                                >
                                    {{ item.product.short_description }}
                                </p>

                                <!-- Price -->
                                <div
                                    class="mt-2 flex flex-wrap items-center gap-x-2"
                                >
                                    <span
                                        class="text-lg font-bold text-gray-900 dark:text-white"
                                    >
                                        {{
                                            formatPrice(item.lowest_final_price)
                                        }}
                                        {{ siteStore.settings.currency }}
                                    </span>
                                    <span
                                        v-if="item.has_product_discount"
                                        class="text-sm text-gray-500 line-through dark:text-gray-400"
                                    >
                                        {{ formatPrice(item.lowest_price) }}
                                        {{ siteStore.settings.currency }}
                                    </span>
                                    <span
                                        v-if="item.has_product_discount"
                                        class="text-xs text-green-600 font-medium dark:text-green-400"
                                    >
                                        Save
                                        {{
                                            formatPrice(
                                                item.lowest_price -
                                                    item.lowest_final_price,
                                            )
                                        }}
                                        {{ siteStore.settings.currency }}
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div
                                class="mt-4 sm:mt-0 sm:ml-5 flex space-x-2 w-full sm:w-auto"
                            >
                                <button
                                v-if="siteStore.settings && !siteStore.settings.orders_via_whatsapp_only"
                                    @click="addToCart(item.product_id)"
                                    class="flex-1 sm:flex-none bg-primary-600 text-white py-2 px-3 rounded-lg hover:bg-primary-700 transition text-sm font-medium dark:bg-primary-700 dark:hover:bg-primary-800"
                                >
                                    Add to Cart
                                </button>
                                <button
                                    @click="moveItemToCategory(item.id)"
                                    class="p-2 text-gray-400 hover:text-primary-600 bg-gray-50 hover:bg-gray-100 rounded-lg transition dark:bg-gray-700 dark:hover:bg-gray-600 dark:hover:text-primary-400"
                                    title="Move to another category"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- No items in selected category -->
                    <div
                        v-if="
                            wishlistStore.filteredItems.length === 0 &&
                            wishlistStore.selectedCategory
                        "
                        class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700"
                    >
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4 dark:bg-gray-700"
                        >
                            <svg
                                class="w-8 h-8 text-gray-400 dark:text-gray-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                                />
                            </svg>
                        </div>
                        <h3
                            class="text-lg font-medium text-gray-900 mb-2 dark:text-white"
                        >
                            No items in this category
                        </h3>
                        <p class="text-gray-500 mb-6 dark:text-gray-400">
                            Add some items to get started.
                        </p>
                        <button
                            @click="showAddCategoryModal = true"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                        >
                            Add New Category
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Category Modal -->
        <div
            v-if="showAddCategoryModal"
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
            @click="showAddCategoryModal = false"
        >
            <div
                class="fixed inset-0 bg-black bg-opacity-50 transition-opacity dark:bg-gray-900 dark:bg-opacity-75"
            ></div>

            <div
                class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-auto p-6 transform transition-all dark:bg-gray-800"
                @click.stop
            >
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Add New Category
                    </h3>
                    <button
                        @click="showAddCategoryModal = false"
                        class="p-2 text-gray-400 hover:text-gray-600 transition-colors dark:text-gray-500 dark:hover:text-gray-300"
                    >
                        <svg
                            class="h-6 w-6"
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

                <div class="space-y-4">
                    <div>
                        <label
                            for="category-name"
                            class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                        >
                            Category Name
                        </label>
                        <input
                            v-model="newCategoryName"
                            type="text"
                            id="category-name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Enter category name"
                            @keyup.enter="addCategory"
                        />
                    </div>

                    <div class="flex items-center">
                        <input
                            v-model="isDefaultCategory"
                            type="checkbox"
                            id="is-default"
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                        />
                        <label
                            for="is-default"
                            class="ml-2 block text-sm text-gray-700 dark:text-gray-300"
                        >
                            Set as default category
                        </label>
                    </div>
                </div>

                <div class="flex space-x-3 mt-8">
                    <button
                        @click="addCategory"
                        :disabled="!newCategoryName.trim()"
                        class="flex-1 bg-primary-600 text-white py-3 px-4 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed dark:bg-primary-700 dark:hover:bg-primary-800"
                        :class="
                            !newCategoryName.trim()
                                ? 'opacity-50 cursor-not-allowed'
                                : 'hover:bg-primary-700'
                        "
                    >
                        Add Category
                    </button>
                    <button
                        @click="showAddCategoryModal = false"
                        class="px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Category Modal -->
        <div
            v-if="showEditCategoryModal"
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
            @click="showEditCategoryModal = false"
        >
            <div
                class="fixed inset-0 bg-black bg-opacity-50 transition-opacity dark:bg-gray-900 dark:bg-opacity-75"
            ></div>

            <div
                class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-auto p-6 transform transition-all dark:bg-gray-800"
                @click.stop
            >
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Edit Category
                    </h3>
                    <button
                        @click="showEditCategoryModal = false"
                        class="p-2 text-gray-400 hover:text-gray-600 transition-colors dark:text-gray-500 dark:hover:text-gray-300"
                    >
                        <svg
                            class="h-6 w-6"
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

                <div class="space-y-4">
                    <div>
                        <label
                            for="edit-category-name"
                            class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                        >
                            Category Name
                        </label>
                        <input
                            v-model="editCategoryName"
                            type="text"
                            id="edit-category-name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Enter category name"
                            @keyup.enter="updateCategory"
                        />
                    </div>

                    <div class="flex items-center">
                        <input
                            v-model="editIsDefault"
                            type="checkbox"
                            id="edit-is-default"
                            :disabled="editingCategory?.is_default"
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                            :title="
                                editingCategory?.is_default
                                    ? 'This is already the default category'
                                    : ''
                            "
                        />
                        <label
                            for="edit-is-default"
                            class="ml-2 block text-sm text-gray-700 dark:text-gray-300"
                        >
                            Set as default category
                        </label>
                    </div>
                </div>

                <div class="flex space-x-3 mt-8">
                    <button
                        @click="updateCategory"
                        :disabled="!editCategoryName.trim()"
                        class="flex-1 bg-primary-600 text-white py-3 px-4 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed dark:bg-primary-700 dark:hover:bg-primary-800"
                    >
                        Update Category
                    </button>
                    <button
                        @click="showEditCategoryModal = false"
                        class="px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Move Items Modal -->
        <div
            v-if="showMoveItemsModal"
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
            @click="showMoveItemsModal = false"
        >
            <div
                class="fixed inset-0 bg-black bg-opacity-50 transition-opacity dark:bg-gray-900 dark:bg-opacity-75"
            ></div>

            <div
                class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-auto p-6 transform transition-all dark:bg-gray-800"
                @click.stop
            >
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Move Items to Category
                    </h3>
                    <button
                        @click="showMoveItemsModal = false"
                        class="p-2 text-gray-400 hover:text-gray-600 transition-colors dark:text-gray-500 dark:hover:text-gray-300"
                    >
                        <svg
                            class="h-6 w-6"
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

                <div class="space-y-4">
                    <div>
                        <label
                            for="move-category"
                            class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                        >
                            Select Category
                        </label>
                        <select
                            v-model="selectedMoveCategory"
                            id="move-category"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option
                                v-for="category in wishlistStore.categories"
                                :key="category.id"
                                :value="category.id"
                                :disabled="
                                    category.id ===
                                    wishlistStore.selectedCategory?.id
                                "
                            >
                                {{ category.name }}
                                <span
                                    v-if="
                                        category.id ===
                                        wishlistStore.selectedCategory?.id
                                    "
                                    >(Current)</span
                                >
                            </option>
                        </select>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg dark:bg-blue-900/30">
                        <p class="text-sm text-blue-800 dark:text-blue-300">
                            This will move all
                            <span class="font-semibold">{{
                                wishlistStore.filteredItems.length
                            }}</span>
                            items from
                            <span class="font-semibold">{{
                                wishlistStore.selectedCategory?.name
                            }}</span>
                            to the selected category.
                        </p>
                    </div>
                </div>

                <div class="flex space-x-3 mt-8">
                    <button
                        @click="confirmMoveItems"
                        :disabled="
                            !selectedMoveCategory ||
                            selectedMoveCategory ===
                                wishlistStore.selectedCategory?.id
                        "
                        class="flex-1 bg-primary-600 text-white py-3 px-4 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed dark:bg-primary-700 dark:hover:bg-primary-800"
                    >
                        Move Items
                    </button>
                    <button
                        @click="showMoveItemsModal = false"
                        class="px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Move Single Item Modal -->
        <div
            v-if="showMoveItemModal"
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
            @click="showMoveItemModal = false"
        >
            <div
                class="fixed inset-0 bg-black bg-opacity-50 transition-opacity dark:bg-gray-900 dark:bg-opacity-75"
            ></div>

            <div
                class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-auto p-6 transform transition-all dark:bg-gray-800"
                @click.stop
            >
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Move Item to Category
                    </h3>
                    <button
                        @click="showMoveItemModal = false"
                        class="p-2 text-gray-400 hover:text-gray-600 transition-colors dark:text-gray-500 dark:hover:text-gray-300"
                    >
                        <svg
                            class="h-6 w-6"
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

                <div class="space-y-4">
                    <div>
                        <label
                            for="move-item-category"
                            class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                        >
                            Select Category
                        </label>
                        <select
                            v-model="selectedMoveItemCategory"
                            id="move-item-category"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option
                                v-for="category in wishlistStore.categories"
                                :key="category.id"
                                :value="category.id"
                                :disabled="category.id === currentItemCategory"
                            >
                                {{ category.name }}
                                <span v-if="category.id === currentItemCategory"
                                    >(Current)</span
                                >
                            </option>
                        </select>
                    </div>
                </div>

                <div class="flex space-x-3 mt-8">
                    <button
                        @click="confirmMoveItem"
                        :disabled="
                            !selectedMoveItemCategory ||
                            selectedMoveItemCategory === currentItemCategory
                        "
                        class="flex-1 bg-primary-600 text-white py-3 px-4 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed dark:bg-primary-700 dark:hover:bg-primary-800"
                    >
                        Move Item
                    </button>
                    <button
                        @click="showMoveItemModal = false"
                        class="px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useWishlistStore } from "../stores/wishlist";
import { useCartStore } from "../stores/cart";
import { useToast } from "vue-toastification";
import { useSiteStore } from "../stores/site";

const siteStore = useSiteStore();
const wishlistStore = useWishlistStore();
const cartStore = useCartStore();
const toast = useToast();

// Modals
const showAddCategoryModal = ref(false);
const showEditCategoryModal = ref(false);
const showMoveItemsModal = ref(false);
const showMoveItemModal = ref(false);

// Form data
const newCategoryName = ref("");
const isDefaultCategory = ref(false);
const editCategoryName = ref("");
const editIsDefault = ref(false);
const editingCategory = ref(null);
const selectedMoveCategory = ref(null);
const selectedMoveItemCategory = ref(null);
const currentItemToMove = ref(null);
const currentItemCategory = ref(null);

// Image error handling
const imageError = ref(false);

// Format price helper
const formatPrice = (price) => {
    return parseFloat(price || 0).toFixed(2);
};

// Get product image URL helper
const getProductImageUrl = (item) => {
    if (
        item.product?.gallery_images &&
        item.product.gallery_images.length > 0
    ) {
        return item.product.gallery_images[0];
    }
    if (item.product?.main_image_url) {
        return item.product.main_image_url;
    }
    return null;
};

// Handle image error
const handleImageError = () => {
    imageError.value = true;
};

// Initialize wishlist
onMounted(async () => {
    await wishlistStore.initializeWishlist();
});

// Add new category
const addCategory = async () => {
    if (!newCategoryName.value.trim()) {
        toast.error("Please enter a category name");
        return;
    }

    const result = await wishlistStore.createCategory(
        newCategoryName.value,
        isDefaultCategory.value,
    );

    if (result.success) {
        showAddCategoryModal.value = false;
        newCategoryName.value = "";
        isDefaultCategory.value = false;
        // toast.success('Category created successfully')
    }
};

// Edit category
const editCategory = (category) => {
    editingCategory.value = category;
    editCategoryName.value = category.name;
    editIsDefault.value = category.is_default;
    showEditCategoryModal.value = true;
};

// Update category
const updateCategory = async () => {
    if (!editCategoryName.value.trim()) {
        toast.error("Please enter a category name");
        return;
    }

    const result = await wishlistStore.updateCategory(
        editingCategory.value.id,
        editCategoryName.value,
        editIsDefault.value,
    );

    if (result.success) {
        showEditCategoryModal.value = false;
        editingCategory.value = null;
        editCategoryName.value = "";
        editIsDefault.value = false;
        // toast.success('Category updated successfully')
    }
};

// Delete category
const deleteCategory = async (categoryId) => {
    if (
        !confirm(
            "Are you sure you want to delete this category? All items in this category will be removed.",
        )
    ) {
        return;
    }

    const result = await wishlistStore.deleteCategory(categoryId);

    if (result.success) {
        // If current category was deleted, switch to all items
        if (wishlistStore.selectedCategory?.id === categoryId) {
            wishlistStore.clearSelectedCategory();
        }
    }
};

// Remove item from wishlist
const removeFromWishlist = async (itemId) => {
    if (
        !confirm(
            "Are you sure you want to remove this item from your wishlist?",
        )
    ) {
        return;
    }

    await wishlistStore.removeFromWishlist(itemId);
    //   toast.success('Item removed from wishlist')
};

// Add item to cart
const addToCart = async (productId) => {
    try {
        const result = await cartStore.addToCart(productId, 1);
        if (result.success) {
            //   toast.success('Product added to cart')
        }
    } catch (error) {
        // toast.error('Failed to add to cart')
    }
};

// Move items to category
const moveItemsToCategory = () => {
    selectedMoveCategory.value = null;
    showMoveItemsModal.value = true;
};

// Confirm move items
const confirmMoveItems = async () => {
    if (!selectedMoveCategory.value) {
        toast.error("Please select a category");
        return;
    }

    // Move each item
    const promises = wishlistStore.filteredItems.map((item) =>
        wishlistStore.moveItem(item.id, selectedMoveCategory.value),
    );

    try {
        await Promise.all(promises);
        showMoveItemsModal.value = false;
        selectedMoveCategory.value = null;
        // toast.success('Items moved successfully')
    } catch (error) {
        // toast.error('Failed to move items')
    }
};

// Move single item to category
const moveItemToCategory = (itemId) => {
    const item = wishlistStore.items.find((i) => i.id === itemId);
    if (item) {
        currentItemToMove.value = item;
        currentItemCategory.value = item.wishlist_category_id;
        selectedMoveItemCategory.value = null;
        showMoveItemModal.value = true;
    }
};

// Confirm move single item
const confirmMoveItem = async () => {
    if (!selectedMoveItemCategory.value) {
        toast.error("Please select a category");
        return;
    }

    try {
        await wishlistStore.moveItem(
            currentItemToMove.value.id,
            selectedMoveItemCategory.value,
        );
        showMoveItemModal.value = false;
        currentItemToMove.value = null;
        currentItemCategory.value = null;
        selectedMoveItemCategory.value = null;
        // toast.success('Item moved successfully')
    } catch (error) {
        // toast.error('Failed to move item')
    }
};

// Clear category
const clearCategory = async () => {
    if (!wishlistStore.selectedCategory) return;

    if (
        !confirm(
            `Are you sure you want to remove all items from ${wishlistStore.selectedCategory.name}?`,
        )
    ) {
        return;
    }

    try {
        // Remove each item in the category
        const itemsToRemove = wishlistStore.items.filter(
            (item) =>
                item.wishlist_category_id === wishlistStore.selectedCategory.id,
        );

        const promises = itemsToRemove.map((item) =>
            wishlistStore.removeFromWishlist(item.id),
        );

        await Promise.all(promises);
        // toast.success('Category cleared successfully')
    } catch (error) {
        // toast.error('Failed to clear category')
    }
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.aspect-w-4 {
    position: relative;
    padding-bottom: 75%; /* 4:3 aspect ratio */
}

.aspect-w-4 > * {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

/* Smooth transitions */
.transition-all {
    transition: all 0.3s ease;
}

.transform {
    transform: translateZ(0);
}
</style>
