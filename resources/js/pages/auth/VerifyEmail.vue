<template>
    <div
        :class="[
            isDark
                ? 'min-h-screen bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-950 text-white'
                : 'min-h-screen bg-gradient-to-br from-blue-50 via-blue-100 to-white text-slate-900',
        ]"
    >
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div
                :class="[
                    isDark
                        ? 'absolute top-20 left-10 w-72 h-72 bg-blue-600/10 rounded-full blur-3xl opacity-60'
                        : 'absolute top-20 left-10 w-72 h-72 bg-blue-200/30 rounded-full blur-3xl opacity-60',
                ]"
            ></div>
            <div
                :class="[
                    isDark
                        ? 'absolute bottom-20 right-10 w-80 h-80 bg-purple-600/10 rounded-full blur-3xl opacity-50'
                        : 'absolute bottom-20 right-10 w-80 h-80 bg-purple-200/30 rounded-full blur-3xl opacity-50',
                ]"
            ></div>
        </div>

        <!-- Main Content -->
        <div
            class="relative z-10 flex items-center justify-center min-h-screen p-4"
        >
            <div class="w-full max-w-md lg:max-w-lg">
                <div
                    :class="[
                        isDark
                            ? 'glass-card-premium rounded-2xl overflow-hidden shadow-xl border border-white/10 backdrop-blur-xl relative animate-slide-up'
                            : 'bg-white rounded-2xl overflow-hidden shadow-xl border border-blue-100 relative animate-slide-up',
                    ]"
                >
                    <!-- Gradient Overlay -->
                    <div
                        :class="[
                            isDark
                                ? 'absolute inset-0 bg-gradient-to-br from-purple-500/5 via-transparent to-blue-500/5 opacity-60 pointer-events-none'
                                : 'absolute inset-0 bg-gradient-to-br from-purple-200/10 via-transparent to-blue-200/10 opacity-60 pointer-events-none',
                        ]"
                    ></div>

                    <div class="relative z-10 p-8 lg:p-10">
                        <!-- Icon & Title -->
                        <div class="text-center mb-8">
                            <div
                                :class="[
                                    loading
                                        ? isDark
                                            ? 'animate-pulse bg-yellow-600'
                                            : 'animate-pulse bg-yellow-400'
                                        : success
                                          ? isDark
                                              ? 'bg-green-600'
                                              : 'bg-green-500'
                                          : isDark
                                            ? 'bg-red-600'
                                            : 'bg-red-500',
                                    'inline-flex items-center justify-center w-16 h-16 mx-auto rounded-2xl shadow-lg mb-4 transition-all duration-500 transform hover:scale-110',
                                ]"
                            >
                                <svg
                                    v-if="loading"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-8 h-8 text-white animate-spin"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"
                                    />
                                </svg>

                                <svg
                                    v-else-if="success"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-8 h-8 text-white"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M20 6L9 17l-5-5" />
                                </svg>

                                <svg
                                    v-else
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-8 h-8 text-white"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="15" y1="9" x2="9" y2="15" />
                                    <line x1="9" y1="9" x2="15" y2="15" />
                                </svg>
                            </div>

                            <h2
                                :class="[
                                    isDark
                                        ? 'text-xl font-bold text-white'
                                        : 'text-xl font-bold text-blue-900',
                                ]"
                            >
                                {{ statusTitle }}
                            </h2>
                            <p
                                :class="[
                                    isDark
                                        ? 'text-slate-400 text-sm mt-2'
                                        : 'text-slate-500 text-sm mt-2',
                                ]"
                            >
                                {{ statusSubtitle }}
                            </p>
                        </div>

                        <!-- Message Box -->
                        <div
                            v-if="error || success"
                            :class="[
                                success
                                    ? isDark
                                        ? 'bg-green-500/15 border border-green-500/30'
                                        : 'bg-green-100 border border-green-300'
                                    : isDark
                                      ? 'bg-red-500/15 border border-red-500/30'
                                      : 'bg-red-100 border border-red-300',
                                'rounded-xl p-4 mb-6 text-center',
                            ]"
                        >
                            <p
                                :class="[
                                    success
                                        ? isDark
                                            ? 'text-green-300 font-medium'
                                            : 'text-green-700 font-medium'
                                        : isDark
                                          ? 'text-red-300 font-medium'
                                          : 'text-red-700 font-medium',
                                ]"
                            >
                                {{ message }}
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div v-if="!success" class="space-y-4">
                            <router-link
                                to="/login"
                                :class="[
                                    isDark
                                        ? 'w-full py-3 text-center block bg-gray-700 hover:bg-gray-600 rounded-xl font-semibold text-white transition-colors'
                                        : 'w-full py-3 text-center block bg-gray-100 hover:bg-gray-200 rounded-xl font-semibold text-gray-800 transition-colors',
                                ]"
                            >
                                ← Back to Login
                            </router-link>
                        </div>

                        <div v-else class="text-center pt-4">
                            <button
                                @click="$router.push('/')"
                                :class="[
                                    isDark
                                        ? 'px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-500 hover:to-blue-500 text-white rounded-xl font-semibold shadow-lg transition-all transform hover:scale-105'
                                        : 'px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-400 hover:to-purple-400 text-white rounded-xl font-semibold shadow-lg transition-all transform hover:scale-105',
                                ]"
                            >
                                Go to Dashboard
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../bootstrap";
import { useTheme } from "@/composables/useTheme";
import { useAuthStore } from "@/stores/auth";

export default {
    name: "VerifyEmail",
    setup() {
        const { isDark } = useTheme();
        return { isDark };
    },
    data() {
        return {
            loading: true,
            success: false,
            error: null,
            message: "",
        };
    },
    computed: {
        statusTitle() {
            if (this.loading) return "Verifying Your Email";
            return this.success ? "Email Verified!" : "Verification Failed";
        },
        statusSubtitle() {
            if (this.loading)
                return "Please wait while we verify your email address...";
            if (this.success)
                return "Your email has been successfully verified. Welcome aboard!";
            return "There was a problem with the verification link.";
        },
    },
    async mounted() {
        try {
            const { id, hash } = this.$route.params;
            const query = this.$route.query;

            const response = await axios.get(`/verify-email/${id}/${hash}`, {
                params: query,
            });

            if (response.data.success) {
                this.success = true;
                this.message = response.data.message;
                const authStore = useAuthStore();
                authStore.refreshUser();
            }

            this.loading = false;
        } catch (err) {
            this.message =
                err.response?.data?.message ||
                "Verification failed. Link may be invalid or expired.";
        } finally {
            this.loading = false;
        }
    },
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
    box-shadow:
        0 4px 24px -1px rgba(0, 0, 0, 0.2),
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

@media (prefers-reduced-motion: reduce) {
    .animate-slide-up {
        animation: none;
    }
}
</style>
