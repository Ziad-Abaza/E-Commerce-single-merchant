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

      console.log("query from gmail: ", query);
      console.log("id: ", id);
      console.log("hash: ", hash);

      const response = await axios.get(`/api/verify-email/${id}/${hash}`, {
        params: query
      });

      console.log("response: ", response.data);

      if (response.data.success) {
        this.success = true;
        this.loading = false;
        return;
      }

      this.error = response.data.message;
      this.loading = false;
    } catch (err) {
      this.error = err.response?.data?.message || 'Verification failed.';
      this.loading = false;
    }
  }
};
</script>
