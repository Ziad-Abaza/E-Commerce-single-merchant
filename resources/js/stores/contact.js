import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from '../bootstrap';
import { useToast } from 'vue-toastification';

export const useContactStore = defineStore("contact", () => {
    const toast = useToast();
    const isLoading = ref(false);
    const errors = ref({});
    const isSuccess = ref(false);

    const form = ref({
        name: "",
        email: "",
        subject: "",
        message: "",
        phone: "",
    });

    const resetForm = () => {
        form.value = {
            name: "",
            email: "",
            subject: "",
            message: "",
            phone: "",
        };
        errors.value = {};
        isSuccess.value = false;
    };

    const submitForm = async () => {
        isLoading.value = true;
        errors.value = {};
        isSuccess.value = false;

        try {
            const response = await axios.post("/public/contact", form.value);

            if (response.status === 201) {
                isSuccess.value = true;
                resetForm();
                toast.success("Your message has been sent successfully!");
            }
            return response.data;
        } catch (error) {
            if (error.response) {
                if (error.response.status === 422) {
                    errors.value = error.response.data.errors || {};
                    toast.error("Please correct the errors in the form.");
                } else {
                    toast.error(
                        error.response.data.message ||
                            "An error occurred while sending your message.",
                    );
                }
            } else {
                toast.error("Network error. Please check your connection.");
            }
            throw error;
        } finally {
            isLoading.value = false;
        }
    };

    return {
        form,
        errors,
        isLoading,
        isSuccess,
        submitForm,
        resetForm,
    };
});
