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

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');
    const hash = params.get('hash');
    const expires = params.get('expires');
    const signature = params.get('signature');

    if (!id || !hash || !expires || !signature) {
        error.value = 'Invalid verification link';
        loading.value = false;
        return;
    }

    window.location.href = `/api/verify-email/${id}/${hash}?expires=${expires}&signature=${signature}`;
});

</script>
