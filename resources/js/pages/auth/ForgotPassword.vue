<template>
    <div class="max-w-md w-full space-y-8">
      <div class="text-center">
        <h2 class="mt-6 text-3xl font-bold text-gray-900 dark:text-white">Reset Your Password</h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
          Enter your email address and we'll send you a link to reset your password.
        </p>
      </div>

      <form class="mt-8 space-y-6" @submit.prevent="sendResetLink">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white"
            placeholder="you@example.com"
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
              Sending...
            </span>
            <span v-else>Send Reset Link</span>
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
import AuthLayout from "@/pages/dashboard/layouts/AuthLayout.vue";
import { ref } from "vue";
import { useToast } from "vue-toastification";
import axios from "@/bootstrap";

export default {
  components: { AuthLayout },
  setup() {
    const form = ref({
      email: "",
    });
    const loading = ref(false);
    const toast = useToast();

    const sendResetLink = async () => {
      loading.value = true;
      try {
        await axios.post("/forgot-password", form.value);
        toast.success("Password reset link sent to your email.");
        form.value.email = "";
      } catch (err) {
        toast.error(
          err.response?.data?.message ||
            "Failed to send reset link. Please try again."
        );
      } finally {
        loading.value = false;
      }
    };

    return { form, loading, sendResetLink };
  },
};
</script>
