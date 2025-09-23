<template>
    <div class="p-2 sm:p-4 md:p-6 space-y-4 sm:space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                        Privacy & Policies
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Manage your store's privacy policy, terms of service, and other legal pages
                    </p>
                </div>
                <button
                    @click="openCreateModal"
                    class="px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add New Policy
                </button>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8 overflow-x-auto">
                    <button
                        v-for="tab in tabs"
                        :key="tab.type"
                        @click="activeTab = tab.type"
                        :class="[
                            activeTab === tab.type
                                ? 'border-blue-500 text-blue-600 dark:text-blue-400 dark:border-blue-400'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200',
                            'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                        ]"
                    >
                        {{ tab.label }}
                        <span
                            v-if="getPoliciesByType(tab.type).length > 0"
                            class="ml-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                        >
                            {{ getPoliciesByType(tab.type).length }}
                        </span>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Error Alert -->
        <div
            v-if="policiesStore.error"
            class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded relative"
            role="alert"
        >
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="block sm:inline">{{ policiesStore.error }}</span>
                <button @click="policiesStore.clearError()" class="ml-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="policiesStore.loading" class="flex justify-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>

        <!-- Empty State -->
        <div
            v-else-if="!hasPolicies"
            class="text-center py-12 px-4 sm:px-6 lg:px-8 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700"
        >
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No policies found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Get started by creating a new policy.
            </p>
            <div class="mt-6">
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    New Policy
                </button>
            </div>
        </div>

        <!-- Policies List -->
        <div v-else class="space-y-4">
            <div v-for="policy in filteredPolicies" :key="policy.id"
                 class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-4 sm:p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ policy.title }}
                                </h3>
                                <span
                                    v-if="policy.is_active"
                                    class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
                                >
                                    Active
                                </span>
                                <span
                                    v-else
                                    class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
                                >
                                    Inactive
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ formatDate(policy.updated_at) }}
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button
                                @click="togglePolicyStatus(policy)"
                                type="button"
                                class="mr-2 bg-white dark:bg-gray-700 rounded-md font-medium text-blue-600 hover:text-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                :title="policy.is_active ? 'Deactivate' : 'Activate'"
                            >
                                <svg v-if="policy.is_active" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <button
                                @click="editPolicy(policy)"
                                type="button"
                                class="mr-2 bg-white dark:bg-gray-700 rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button
                                @click="confirmDelete(policy)"
                                type="button"
                                class="bg-white dark:bg-gray-700 rounded-md font-medium text-red-600 hover:text-red-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 prose dark:prose-invert max-w-none">
                        <div v-html="truncateContent(policy.content, 200)"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Policy Modal -->
        <div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full sm:p-6">
                    <div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                {{ isEditing ? 'Edit Policy' : 'Create New Policy' }}
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                                    <select
                                        id="type"
                                        v-model="form.type"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-white"
                                        :disabled="isEditing"
                                    >
                                        <option v-for="tab in tabs" :key="tab.type" :value="tab.type">
                                            {{ tab.label }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                                    <input
                                        type="text"
                                        id="title"
                                        v-model="form.title"
                                        class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white"
                                        placeholder="Enter policy title"
                                    >
                                </div>
                                <div>
                                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Content</label>
                                    <RichTextEditor
                                        v-model="form.content"
                                        :key="isEditing ? 'edit' : 'create'"
                                        class="mt-1"
                                    />
                                </div>
                                <div class="flex items-center">
                                    <input
                                        id="is_active"
                                        v-model="form.is_active"
                                        type="checkbox"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                                    >
                                    <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm"
                            @click="savePolicy"
                            :disabled="saving"
                        >
                            <svg v-if="saving" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ saving ? 'Saving...' : 'Save' }}
                        </button>
                        <button
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                            @click="closeModal"
                            :disabled="saving"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Delete Policy
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Are you sure you want to delete this policy? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                            @click="deletePolicy"
                            :disabled="deleting"
                        >
                            <svg v-if="deleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ deleting ? 'Deleting...' : 'Delete' }}
                        </button>
                        <button
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
                            @click="closeDeleteModal"
                            :disabled="deleting"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { usePoliciesStore } from '../../stores/dashboard/policies';
import RichTextEditor from './components/RichTextEditor.vue';

const policiesStore = usePoliciesStore();

// State
const activeTab = ref('privacy');
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const saving = ref(false);
const deleting = ref(false);
const selectedPolicyId = ref(null);

const tabs = [
    { type: 'privacy', label: 'Privacy Policy' },
    { type: 'terms', label: 'Terms of Service' },
    { type: 'return', label: 'Return Policy' },
    { type: 'shipping', label: 'Shipping Policy' },
    { type: 'warranty', label: 'Warranty Policy' },
    { type: 'cookies', label: 'Cookies Policy' },
    { type: 'faq', label: 'FAQ' },
];

const form = ref({
    type: 'privacy',
    title: '',
    content: '',
    is_active: true,
});

// Computed
const filteredPolicies = computed(() => {
    return policiesStore.policies.filter(policy => policy.type === activeTab.value);
});

const hasPolicies = computed(() => {
    return filteredPolicies.value.length > 0;
});

// Methods
const openCreateModal = () => {
    isEditing.value = false;
    form.value = {
        type: activeTab.value,
        title: '',
        content: '',
        is_active: true,
    };
    showModal.value = true;
};

const editPolicy = (policy) => {
    isEditing.value = true;
    selectedPolicyId.value = policy.id;
    form.value = {
        type: policy.type,
        title: policy.title,
        content: policy.content,
        is_active: policy.is_active,
    };
    showModal.value = true;
};

const savePolicy = async () => {
    saving.value = true;
    try {
        if (isEditing.value) {
            await policiesStore.updatePolicy(selectedPolicyId.value, form.value);
        } else {
            await policiesStore.createPolicy(form.value);
        }
        closeModal();
        await policiesStore.fetchPolicies();
    } catch (error) {
        console.error('Error saving policy:', error);
    } finally {
        saving.value = false;
    }
};

const confirmDelete = (policy) => {
    selectedPolicyId.value = policy.id;
    showDeleteModal.value = true;
};

const deletePolicy = async () => {
    if (!selectedPolicyId.value) return;

    deleting.value = true;
    try {
        await policiesStore.deletePolicy(selectedPolicyId.value);
        closeDeleteModal();
        await policiesStore.fetchPolicies();
    } catch (error) {
        console.error('Error deleting policy:', error);
    } finally {
        deleting.value = false;
    }
};

const togglePolicyStatus = async (policy) => {
    try {
        await policiesStore.togglePolicyStatus(policy.id);
        await policiesStore.fetchPolicies();
    } catch (error) {
        console.error('Error toggling policy status:', error);
    }
};

const closeModal = () => {
    showModal.value = false;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    selectedPolicyId.value = null;
};

const getPoliciesByType = (type) => {
    return policiesStore.policies.filter(policy => policy.type === type);
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const truncateContent = (html, length) => {
    if (!html) return '';
    // Remove HTML tags and truncate
    const text = html.replace(/<[^>]*>?/gm, '');
    return text.length > length ? text.substring(0, length) + '...' : text;
};

// Lifecycle
onMounted(async () => {
    await policiesStore.fetchPolicies(1, 15, activeTab.value);
});

// Watch for tab changes to refetch policies
watch(activeTab, async (newTab) => {
    await policiesStore.fetchPolicies(1, 15, newTab);
});

// Watch for tab changes to update the form type
watch(activeTab, (newTab) => {
    form.value.type = newTab;
});
</script>
