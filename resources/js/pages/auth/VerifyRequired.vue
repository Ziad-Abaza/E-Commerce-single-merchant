<template>
  <div
    :class="[
      isDark
        ? 'min-h-screen bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-950 text-white'
        : 'min-h-screen bg-gradient-to-br from-blue-50 via-blue-100 to-white text-slate-900'
    ]"
  >
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div
        :class="[
          isDark
            ? 'absolute top-20 left-10 w-72 h-72 bg-blue-600/10 rounded-full blur-3xl opacity-60'
            : 'absolute top-20 left-10 w-72 h-72 bg-blue-200/30 rounded-full blur-3xl opacity-60'
        ]"
      ></div>
      <div
        :class="[
          isDark
            ? 'absolute bottom-20 right-10 w-80 h-80 bg-purple-600/10 rounded-full blur-3xl opacity-50'
            : 'absolute bottom-20 right-10 w-80 h-80 bg-purple-200/30 rounded-full blur-3xl opacity-50'
        ]"
      ></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">
      <div class="w-full max-w-md lg:max-w-lg">
        <div
          :class="[
            isDark
              ? 'glass-card-premium rounded-2xl overflow-hidden shadow-xl border border-white/10 backdrop-blur-xl relative animate-slide-up'
              : 'bg-white rounded-2xl overflow-hidden shadow-xl border border-blue-100 relative animate-slide-up'
          ]"
        >
          <!-- Gradient Overlay -->
          <div
            :class="[
              isDark
                ? 'absolute inset-0 bg-gradient-to-br from-purple-500/5 via-transparent to-blue-500/5 opacity-60 pointer-events-none'
                : 'absolute inset-0 bg-gradient-to-br from-purple-200/10 via-transparent to-blue-200/10 opacity-60 pointer-events-none'
            ]"
          ></div>

          <div class="relative z-10 p-8 lg:p-10">
            <!-- Icon -->
            <div class="text-center mb-6">
              <div
                :class="[
                  isDark
                    ? 'inline-flex items-center justify-center w-16 h-16 mx-auto bg-yellow-600 rounded-2xl shadow-lg'
                    : 'inline-flex items-center justify-center w-16 h-16 mx-auto bg-yellow-200 rounded-2xl shadow-lg'
                ]"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="28"
                  height="28"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  class="text-white"
                >
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                  <polyline points="22,6 12,13 2,6" />
                </svg>
              </div>
            </div>

            <!-- Title & Message -->
            <div class="text-center mb-8">
              <h2
                :class="[
                  isDark ? 'text-2xl font-bold text-white' : 'text-2xl font-bold text-blue-900'
                ]"
              >
                Email Verification Required
              </h2>
              <p
                :class="[
                  isDark ? 'text-slate-400 mt-2' : 'text-slate-600 mt-2'
                ]"
              >
                You must verify your email address to access this feature. A verification link was sent to <strong>{{ user?.email }}</strong>.
              </p>
            </div>

            <!-- Action Button -->
            <button
              @click="handleResend"
              :disabled="loading"
              :class="[
                loading
                  ? 'opacity-70 cursor-not-allowed'
                  : '',
                isDark
                  ? 'w-full py-3 bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-500 hover:to-orange-500 text-white rounded-xl font-semibold shadow transition-all transform hover:scale-105'
                  : 'w-full py-3 bg-gradient-to-r from-yellow-500 to-orange-400 hover:from-yellow-400 hover:to-orange-300 text-white rounded-xl font-semibold shadow transition-all transform hover:scale-105'
              ]"
            >
              <span v-if="loading" class="flex items-center justify-center">
                <div
                  :class="[
                    isDark ? 'border-white' : 'border-yellow-500'
                  ]"
                  class="w-5 h-5 border-2 border-t-transparent rounded-full animate-spin mr-2"
                ></div>
                Sending...
              </span>
              <span v-else>Resend Verification Email</span>
            </button>

            <!-- Back to Home -->
            <div class="text-center mt-6">
              <router-link
                to="/"
                :class="[
                  isDark
                    ? 'text-slate-400 hover:text-white'
                    : 'text-slate-500 hover:text-blue-600'
                ]"
              >
                ← Back to Home
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from "@/stores/auth";
import { useTheme } from "@/composables/useTheme";
import { useToast } from "vue-toastification";

export default {
  setup() {
    const authStore = useAuthStore();
    const { isDark } = useTheme();
    const toast = useToast();

    return { authStore, isDark, toast };
  },
  computed: {
    user() {
      return this.authStore.user;
    },
    loading() {
      return this.authStore.loading;
    }
  },
  methods: {
    async handleResend() {
      const result = await this.authStore.resendVerificationEmail();
      if (result.success) {
        //Disable button for 30s to prevent spam
        this.loading = true;
        setTimeout(() => {
          this.loading = false;
        }, 30000);
      }
    }
  }
};
</script>

<style scoped>
.glass-card-premium {
  background: linear-gradient(
    135deg,
    rgba(255, 255, 255, 0.05),
    rgba(255, 255, 255, 0.01)
  );
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 4px 24px -1px rgba(0, 0, 0, 0.2),
    inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

@keyframes slide-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-slide-up {
  animation: slide-up 0.6s ease-out;
}
</style>
