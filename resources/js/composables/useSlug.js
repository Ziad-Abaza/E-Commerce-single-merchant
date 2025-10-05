import { ref } from 'vue';
import axios from '../bootstrap';

export function useSlug() {
  const loading = ref(false);
  const error = ref(null);

  const generateSlug = async (text) => {
    if (!text) return '';
    
    loading.value = true;
    error.value = null;
    
    try {
      // Simple client-side slug generation
      let slug = text
        .toString()
        .toLowerCase()
        .trim()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '');
      
      // Optionally, you can make an API call to check for uniqueness
      // const response = await axios.post('/api/generate-slug', { text });
      // slug = response.data.slug;
      
      return slug;
    } catch (err) {
      console.error('Error generating slug:', err);
      error.value = err.response?.data?.message || 'Failed to generate slug';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    generateSlug,
    loading,
    error,
  };
}
