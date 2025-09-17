<template>
  <div class="verify-email-page">
    <h2>Email Verification</h2>
    <p v-if="loading">Verifying your email...</p>
    <p v-if="success" class="text-green-600">Your email has been verified successfully!</p>
    <p v-if="error" class="text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '@/bootstrap';
import { useRouter, useRoute } from 'vue-router';

const route = useRoute();
const router = useRouter();
const loading = ref(true);
const success = ref(false);
const error = ref('');

onMounted(async () => {
    const verificationUrl = route.query.url;
    if (!verificationUrl) {
        error.value = 'Invalid verification link';
        loading.value = false;
        return;
    }

    try {
        const response = await axios.post('/email/verify-link', { url: verificationUrl });

        if (response.data.success) {
            success.value = true;
        } else {
            error.value = response.data.message || 'Verification failed';
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Server error';
    } finally {
        loading.value = false;
    }
});
</script>
