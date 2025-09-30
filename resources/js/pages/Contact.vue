<template>
    <div class="min-h-screen bg-white dark:bg-gray-900">
        <!-- Breadcrumb Navigation -->
        <Breadcrumb :breadcrumbs="breadcrumbs" />

        <!-- Hero Section -->
        <section
            class="relative bg-gradient-to-r from-primary-600 to-primary-800 text-white"
        >
            <div class="container py-20">
                <div class="max-w-4xl mx-auto text-center">
                    <h1 class="text-4xl md:text-6xl font-bold mb-6">
                        Contact Us
                    </h1>
                    <p
                        class="text-xl md:text-2xl text-primary-100 dark:text-primary-200"
                    >
                        Get in touch with us - we'd love to hear from you
                    </p>
                </div>
            </div>
        </section>

        <!-- Contact Form & Info Section -->
        <section class="py-16 bg-white dark:bg-gray-900">
            <div class="container">
                <div class="max-w-6xl mx-auto">
                    <div class="grid lg:grid-cols-2 gap-12">
                        <!-- Contact Form -->
                        <div v-if="siteStore.settings.contact_form_visible">
                            <h2
                                class="text-3xl font-bold text-gray-900 mb-6 dark:text-white"
                            >
                                Send us a Message
                            </h2>
                            <p
                                class="text-lg text-gray-600 mb-8 dark:text-gray-300"
                            >
                                Have a question, suggestion, or need help? Fill
                                out the form below and we'll get back to you as
                                soon as possible.
                            </p>

                            <form
                                @submit.prevent="submitForm"
                                class="space-y-6"
                            >
                                <div
                                    v-if="contactStore.isSuccess"
                                    class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900/30 dark:text-green-300"
                                >
                                    <p>
                                        Thank you for your message! We'll get
                                        back to you soon.
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="name"
                                        class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                                    >
                                        Full Name *
                                    </label>
                                    <input
                                        id="name"
                                        v-model="contactStore.form.name"
                                        type="text"
                                        required
                                        :class="[
                                            'w-full px-4 py-3 border rounded-md focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors dark:bg-gray-800 dark:text-white',
                                            contactStore.errors.name
                                                ? 'border-red-500 dark:border-red-400'
                                                : 'border-gray-300 dark:border-gray-600',
                                        ]"
                                        placeholder="Enter your full name"
                                    />
                                    <p
                                        v-if="contactStore.errors.name"
                                        class="mt-1 text-sm text-red-600 dark:text-red-400"
                                    >
                                        {{ contactStore.errors.name[0] }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="email"
                                        class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                                    >
                                        Email Address *
                                    </label>
                                    <input
                                        id="email"
                                        v-model="contactStore.form.email"
                                        type="email"
                                        required
                                        :class="[
                                            'w-full px-4 py-3 border rounded-md focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors dark:bg-gray-800 dark:text-white',
                                            contactStore.errors.email
                                                ? 'border-red-500 dark:border-red-400'
                                                : 'border-gray-300 dark:border-gray-600',
                                        ]"
                                        placeholder="Enter your email address"
                                    />
                                    <p
                                        v-if="contactStore.errors.email"
                                        class="mt-1 text-sm text-red-600 dark:text-red-400"
                                    >
                                        {{ contactStore.errors.email[0] }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="phone"
                                        class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                                    >
                                        Phone Number
                                    </label>
                                    <input
                                        id="phone"
                                        v-model="contactStore.form.phone"
                                        type="tel"
                                        :class="[
                                            'w-full px-4 py-3 border rounded-md focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors dark:bg-gray-800 dark:text-white',
                                            contactStore.errors.phone
                                                ? 'border-red-500 dark:border-red-400'
                                                : 'border-gray-300 dark:border-gray-600',
                                        ]"
                                        placeholder="Enter your phone number"
                                    />
                                    <p
                                        v-if="contactStore.errors.phone"
                                        class="mt-1 text-sm text-red-600 dark:text-red-400"
                                    >
                                        {{ contactStore.errors.phone[0] }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="subject"
                                        class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                                    >
                                        Subject *
                                    </label>
                                    <input
                                        id="subject"
                                        v-model="contactStore.form.subject"
                                        type="text"
                                        required
                                        :class="[
                                            'w-full px-4 py-3 border rounded-md focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors dark:bg-gray-800 dark:text-white',
                                            contactStore.errors.subject
                                                ? 'border-red-500 dark:border-red-400'
                                                : 'border-gray-300 dark:border-gray-600',
                                        ]"
                                        placeholder="Enter the subject of your message"
                                    />
                                    <p
                                        v-if="contactStore.errors.subject"
                                        class="mt-1 text-sm text-red-600 dark:text-red-400"
                                    >
                                        {{ contactStore.errors.subject[0] }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="message"
                                        class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300"
                                    >
                                        Message *
                                    </label>
                                    <textarea
                                        id="message"
                                        v-model="contactStore.form.message"
                                        rows="5"
                                        required
                                        :class="[
                                            'w-full px-4 py-3 border rounded-md focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors dark:bg-gray-800 dark:text-white',
                                            contactStore.errors.message
                                                ? 'border-red-500 dark:border-red-400'
                                                : 'border-gray-300 dark:border-gray-600',
                                        ]"
                                        placeholder="How can we help you?"
                                    ></textarea>
                                    <p
                                        v-if="contactStore.errors.message"
                                        class="mt-1 text-sm text-red-600 dark:text-red-400"
                                    >
                                        {{ contactStore.errors.message[0] }}
                                    </p>
                                </div>

                                <div class="pt-2">
                                    <button
                                        type="submit"
                                        :disabled="contactStore.isLoading"
                                        class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors disabled:opacity-70 disabled:cursor-not-allowed dark:bg-primary-700 dark:hover:bg-primary-800"
                                    >
                                        <svg
                                            v-if="contactStore.isLoading"
                                            class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                class="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                stroke-width="4"
                                            ></circle>
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            ></path>
                                        </svg>
                                        {{
                                            contactStore.isLoading
                                                ? "Sending..."
                                                : "Send Message"
                                        }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Contact Information -->
                        <div>
                            <h2
                                class="text-3xl font-bold text-gray-900 mb-6 dark:text-white"
                            >
                                Get in Touch
                            </h2>
                            <p
                                class="text-lg text-gray-600 mb-8 dark:text-gray-300"
                            >
                                We're here to help! Reach out to us through any
                                of the following channels.
                            </p>

                            <div class="space-y-6">
                                <!-- Address -->
                                <div class="flex items-start" v-if="siteStore.settings.address_visible">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center dark:bg-gray-700"
                                        >
                                            <svg
                                                class="w-6 h-6 text-primary-600 dark:text-primary-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3
                                            class="text-lg font-semibold text-gray-900 mb-1 dark:text-white"
                                        >
                                            Our Address
                                        </h3>
                                        <p
                                            class="text-gray-600 dark:text-gray-300"
                                        >
                                            {{ siteStore.settings.address }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="flex items-start" v-if="siteStore.settings.contact_phone_visible">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center dark:bg-gray-700"
                                        >
                                            <svg
                                                class="w-6 h-6 text-primary-600 dark:text-primary-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3
                                            class="text-lg font-semibold text-gray-900 mb-1 dark:text-white"
                                        >
                                            Phone Number
                                        </h3>
                                        <p
                                            class="text-gray-600 dark:text-gray-300"
                                        >
                                            <a
                                                :href="`tel:${siteStore.settings.contact_phone}`"
                                                class="hover:text-primary-600 transition-colors dark:hover:text-primary-400"
                                            >
                                                {{
                                                    siteStore.settings
                                                        .contact_phone
                                                }}
                                            </a>
                                        </p>
                                        <p
                                            class="text-sm text-gray-500 mt-1 dark:text-gray-400"
                                        >
                                            Monday - Friday, 9AM - 6PM
                                        </p>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="flex items-start" v-if="siteStore.settings.contact_email_visible">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center dark:bg-gray-700"
                                        >
                                            <svg
                                                class="w-6 h-6 text-primary-600 dark:text-primary-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4" >
                                        <h3
                                            class="text-lg font-semibold text-gray-900 mb-1 dark:text-white"
                                        >
                                            Email Address
                                        </h3>
                                        <p
                                            class="text-gray-600 dark:text-gray-300"
                                        >
                                            <a
                                                :href="`mailto:${siteStore.settings.contact_email}`"
                                                class="hover:text-primary-600 transition-colors dark:hover:text-primary-400"
                                            >
                                                {{
                                                    siteStore.settings
                                                        .contact_email
                                                }}
                                            </a>
                                        </p>
                                        <p
                                            class="text-sm text-gray-500 mt-1 dark:text-gray-400"
                                        >
                                            We'll respond within 24 hours
                                        </p>
                                    </div>
                                </div>

                                <!-- Live Chat -->
                                <div class="flex items-start" v-if="siteStore.settings.contact_whatsapp_visible">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center dark:bg-gray-700"
                                        >
                                            <svg
                                                class="w-6 h-6 text-primary-600 dark:text-primary-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3
                                            class="text-lg font-semibold text-gray-900 mb-1 dark:text-white"
                                        >
                                            WhatsApp Chat
                                        </h3>
                                        <p
                                            class="text-gray-600 dark:text-gray-300"
                                        >
                                            Chat with us on WhatsApp
                                        </p>
                                        <button
                                            class="text-primary-600 hover:text-primary-700 font-medium mt-1 transition-colors dark:text-primary-400 dark:hover:text-primary-300"
                                        >
                                            <a
                                                :href="`https://wa.me/${siteStore.settings.contact_whatsapp}`"
                                                target="_blank"
                                            >
                                                Start Chat →
                                            </a>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Business Hours -->
                            <div
                                v-if="siteStore.settings.business_hours_visible"
                                class="mt-12 p-6 bg-gray-50 rounded-lg dark:bg-gray-800"
                            >
                                <h3
                                    class="text-lg font-semibold text-gray-900 mb-4 dark:text-white"
                                >
                                    Business Hours
                                </h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span
                                            class="text-gray-900 font-medium dark:text-white"
                                        >
                                            {{
                                                siteStore.settings
                                                    .business_hours
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Social Media -->
                            <div
                                class="mt-8 p-6 bg-primary-50 rounded-lg dark:bg-primary-900/20"
                            >
                                <h3
                                    class="text-lg font-semibold text-gray-900 mb-4 dark:text-white"
                                >
                                    Follow Us
                                </h3>
                                <p
                                    class="text-gray-600 mb-4 dark:text-gray-300"
                                >
                                    Stay connected with us on social media for
                                    updates, promotions, and more!
                                </p>
                                <div class="flex space-x-4">
                                    <a
                                        v-if="siteStore.settings.facebook_url_visible"
                                        :href="siteStore.settings.facebook_url"
                                        class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white hover:bg-blue-700 transition-colors dark:hover:bg-blue-800"
                                    >
                                        <svg
                                            class="w-5 h-5"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z"
                                            />
                                        </svg>
                                    </a>
                                    <a
                                        v-if="siteStore.settings.twitter_url_visible"
                                        :href="siteStore.settings.twitter_url"
                                        class="w-10 h-10 bg-blue-800 rounded-full flex items-center justify-center text-white hover:bg-blue-900 transition-colors dark:hover:bg-blue-950"
                                    >
                                        <svg
                                            class="w-5 h-5"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"
                                            />
                                        </svg>
                                    </a>
                                    <a
                                        v-if="siteStore.settings.instagram_url_visible"
                                        :href="siteStore.settings.instagram_url"
                                        class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white hover:from-purple-600 hover:to-pink-600 transition-colors"
                                    >
                                        <svg
                                            class="w-5 h-5"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M12 2c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.267-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.073 4.948.073 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.438-.645 1.438-1.44s-.643-1.44-1.438-1.44z"
                                            />
                                        </svg>
                                    </a>
                                    <a
                                        v-if="siteStore.settings.tiktok_url_visible"
                                        :href="siteStore.settings.tiktok_url"
                                        target="_blank"
                                        class="w-10 h-10 bg-black rounded-full flex items-center justify-center text-white hover:bg-gray-800 transition-colors dark:hover:bg-gray-700"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            fill="currentColor"
                                            class="bi bi-tiktok"
                                            viewBox="0 0 16 16"
                                        >
                                            <path
                                                d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3z"
                                            />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "vue-toastification";
import { useContactStore } from "@/stores/contact";
import { useSiteStore } from "@/stores/site";
import Breadcrumb from "../components/common/Breadcrumb.vue";
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();
const contactStore = useContactStore();
const siteStore = useSiteStore();
const toast = useToast();

const breadcrumbs = ref([{ name: "Contact Us" }]);

onMounted(() => {
    document.title = "Contact Us - E-Commerce Store | Get in Touch";

    const metaDescription = document.querySelector('meta[name="description"]');
    if (metaDescription) {
        metaDescription.setAttribute(
            "content",
            "Contact our e-commerce store for support, questions, or feedback. Find our contact information, business hours, and send us a message through our contact form.",
        );
    } else {
        const meta = document.createElement("meta");
        meta.name = "description";
        meta.content =
            "Contact our e-commerce store for support, questions, or feedback. Find our contact information, business hours, and send us a message through our contact form.";
        document.head.appendChild(meta);
    }

    if (authStore.isAuthenticated && authStore.user) {
        contactStore.form.name = authStore.user.name || "";
        contactStore.form.email = authStore.user.email || "";
        contactStore.form.phone = authStore.user.phone || "";
    }
});

const submitForm = async () => {
    try {
        await contactStore.submitForm();
    } catch (error) {
        console.error("Form submission error:", error);
    }
};
</script>
