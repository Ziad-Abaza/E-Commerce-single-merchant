<template>
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-bold text-gray-900 dark:text-white">
                Set New Password
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Enter a new password for your account.
            </p>
        </div>

        <form class="mt-8 space-y-6" @submit.prevent="handleSubmit">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    readonly
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                />
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                />
            </div>

            <div>
                <button
                    :disabled="loading"
                    type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-70"
                >
                    <span v-if="loading" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Updating...
                    </span>
                    <span v-else>Update Password</span>
                </button>
            </div>
        </form>

        <div class="text-center">
            <router-link to="/auth/login" class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                ← Back to Login
            </router-link>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

export default {
    setup() {
        const route = useRoute();
        const router = useRouter();
        const authStore = useAuthStore();

        const form = ref({
            token: "",
            email: "",
            password: "",
            password_confirmation: "",
        });

        onMounted(() => {
            const { token, email } = route.query;
            if (token && email) {
                form.value.token = token;
                form.value.email = decodeURIComponent(email);
            } else {
                authStore.$toast.error("Invalid reset link.");
                router.push("/auth/forgot-password");
            }
        });

        const handleSubmit = async () => {
            const result = await authStore.resetPassword({ ...form.value });
            if (result.success) {
                router.push("/auth/login");
            }
        };

        return {
            form,
            loading: authStore.loading,
            handleSubmit,
        };
    },
};
</script>
