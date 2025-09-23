<template>
  <div class="product-slider relative">
    <!-- Title & Navigation -->
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white">
        {{ title || 'Recommended Products' }}
      </h2>
      <div class="hidden md:flex items-center space-x-3">
        <button ref="prevEl" class="nav-btn">
          <svg class="h-5 w-5 md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button ref="nextEl" class="nav-btn">
          <svg class="h-5 w-5 md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Swiper Slider -->
    <Swiper
      :modules="[Navigation, Pagination, Autoplay]"
      :slides-per-view="itemsDesktop"
      :space-between="24"
      :breakpoints="{
        320: { slidesPerView: itemsMobile, spaceBetween: 12 },
        640: { slidesPerView: 1.2, spaceBetween: 16 },
        768: { slidesPerView: itemsTablet, spaceBetween: 20 },
        1024: { slidesPerView: itemsDesktop, spaceBetween: 24 }
      }"
      :autoplay="autoplay ? { delay: autoplaySpeed, disableOnInteraction: false } : false"
      :pagination="{ clickable: true, el: '.custom-pagination' }"
      :navigation="{ prevEl: prevEl, nextEl: nextEl }"
      class="rounded-2xl"
    >
      <SwiperSlide
        v-for="product in products"
        :key="product.id"
        class="pb-12"
      >
        <div class="h-full">
          <ProductCard :product="product" class="product-card" />
        </div>
      </SwiperSlide>
    </Swiper>

    <!-- Custom Pagination -->
    <div class="custom-pagination absolute bottom-2 left-1/2 -translate-x-1/2 flex space-x-2"></div>
  </div>
</template>

<script setup>
import { ref } from "vue"
import { Swiper, SwiperSlide } from "swiper/vue"
import { Navigation, Pagination, Autoplay } from "swiper/modules"
import "swiper/css"
import "swiper/css/navigation"
import "swiper/css/pagination"

import ProductCard from "./ProductCard.vue"

const props = defineProps({
  products: Array,
  title: String,
  autoplay: { type: Boolean, default: true },
  autoplaySpeed: { type: Number, default: 4000 },
  itemsDesktop: { type: Number, default: 4 },
  itemsTablet: { type: Number, default: 2 },
  itemsMobile: { type: Number, default: 1.1 }
})

const prevEl = ref(null)
const nextEl = ref(null)
</script>

<style scoped>
.nav-btn {
  @apply p-2 rounded-full bg-white shadow hover:shadow-md transition disabled:opacity-30 dark:bg-gray-700 dark:hover:bg-gray-600 dark:shadow-gray-800;
}

.custom-pagination .swiper-pagination-bullet {
  @apply w-2.5 h-2.5 rounded-full bg-gray-300 transition dark:bg-gray-600;
}
.custom-pagination .swiper-pagination-bullet-active {
  @apply bg-blue-600 scale-110 dark:bg-blue-500;
}

.product-card {
  @apply bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 flex flex-col dark:bg-gray-800 dark:hover:shadow-gray-700/20;
  height: 100%;
  min-height: 420px;
  display: flex;
  justify-content: space-between;
  overflow: hidden;
}

.product-card img {
  @apply w-full h-48 object-cover;
  border-top-left-radius: 0.75rem;
  border-top-right-radius: 0.75rem;
}

@media (max-width: 640px) {
  .nav-btn {
    @apply hidden;
  }
  .product-card {
    min-height: 280px;
    max-height: 320px;
  }
}
</style>
