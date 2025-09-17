<template>
  <div v-if="loading">Verifying your email...</div>
  <div v-else-if="success">✅ Email verified successfully!</div>
  <div v-else-if="error">{{ error }}</div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'VerifyEmail',
  data() {
    return {
      loading: true,
      success: false,
      error: null
    };
  },
  async mounted() {
    try {
      const { id, hash } = this.$route.params;
      const query = this.$route.query; // contains expires & signature

      // نرسل كل البيانات (بما فيها توقيع URL)
      await axios.get(`/api/verify-email/${id}/${hash}`, {
        params: query
      });

      this.success = true;
      this.loading = false;
    } catch (err) {
      this.error = err.response?.data?.message || 'Verification failed.';
      this.loading = false;
    }
  }
};
</script>
