<script setup>
import { ref, computed } from "vue";
import { router, useForm } from "@inertiajs/vue3";

const props = defineProps({
    form: Object,
});

const showAddFieldModal = ref(false);
const editingField = ref(null);

const fieldTypes = [
    { value: 'text', label: 'Text Input' },
    { value: 'textarea', label: 'Textarea' },
    { value: 'email', label: 'Email' },
    { value: 'number', label: 'Number' },
    { value: 'date', label: 'Date' },
    { value: 'select', label: 'Select Dropdown' },
    { value: 'radio', label: 'Radio Buttons' },
    { value: 'checkbox', label: 'Checkbox' },
    { value: 'file', label: 'File Upload' },
    { value: 'signature', label: 'Digital Signature' },
];

const fieldForm = useForm({
    field_type: 'text',
    label: '',
    name: '',
    placeholder: '',
    required: false,
    options: [],
    order: 0,
});

const newOption = ref('');

const addOption = () => {
    if (newOption.value.trim()) {
        fieldForm.options.push(newOption.value.trim());
        newOption.value = '';
    }
};

const removeOption = (index) => {
    fieldForm.options.splice(index, 1);
};

const openAddFieldModal = () => {
    editingField.value = null;
    fieldForm.reset();
    fieldForm.field_type = 'text';
    fieldForm.required = false;
    fieldForm.options = [];
    showAddFieldModal.value = true;
};

const openEditFieldModal = (field) => {
    editingField.value = field;
    fieldForm.field_type = field.field_type;
    fieldForm.label = field.label;
    fieldForm.name = field.name;
    fieldForm.placeholder = field.placeholder || '';
    fieldForm.required = field.required || false;
    fieldForm.options = field.options ? [...field.options] : [];
    fieldForm.order = field.order || 0;
    showAddFieldModal.value = true;
};

const closeModal = () => {
    showAddFieldModal.value = false;
    editingField.value = null;
    fieldForm.reset();
    newOption.value = '';
};

const submitField = () => {
    if (editingField.value) {
        // Update existing field
        fieldForm.put(route('companies.forms.fields.update', [props.form.id, editingField.value.id]), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    } else {
        // Create new field
        fieldForm.post(route('companies.forms.fields.store', props.form.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    }
};

const deleteField = (fieldId) => {
    if (confirm('Are you sure you want to delete this field?')) {
        router.delete(route('companies.forms.fields.destroy', [props.form.id, fieldId]), {
            preserveScroll: true,
        });
    }
};

const needsOptions = computed(() => {
    return ['select', 'radio', 'checkbox'].includes(fieldForm.field_type);
});

const generateFieldName = () => {
    if (fieldForm.label) {
        fieldForm.name = fieldForm.label
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '_')
            .replace(/^_+|_+$/g, '');
    }
};
</script>

<template>
    <div class="mt-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Form Fields</h3>
            <button
                @click="openAddFieldModal"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
                Add Field
            </button>
        </div>

        <div v-if="form.form_fields && form.form_fields.length > 0" class="space-y-3">
            <div
                v-for="field in form.form_fields"
                :key="field.id"
                class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600"
            >
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ fieldTypes.find(t => t.value === field.field_type)?.label || field.field_type }}
                            </span>
                            <span v-if="field.required" class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                Required
                            </span>
                        </div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ field.label }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Name: {{ field.name }}</p>
                        <p v-if="field.placeholder" class="text-sm text-gray-600 dark:text-gray-400">Placeholder: {{ field.placeholder }}</p>
                        <div v-if="field.options && field.options.length > 0" class="mt-2">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Options:</p>
                            <div class="flex flex-wrap gap-2 mt-1">
                                <span
                                    v-for="(option, index) in field.options"
                                    :key="index"
                                    class="px-2 py-1 text-xs rounded bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-300"
                                >
                                    {{ option }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button
                            @click="openEditFieldModal(field)"
                            class="px-3 py-1 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700"
                        >
                            Edit
                        </button>
                        <button
                            @click="deleteField(field.id)"
                            class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
            <p>No fields added yet. Click "Add Field" to start building your form.</p>
        </div>

        <!-- Add/Edit Field Modal -->
        <div
            v-if="showAddFieldModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            {{ editingField ? 'Edit Field' : 'Add New Field' }}
                        </h3>

                        <form @submit.prevent="submitField">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Field Type <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="fieldForm.field_type"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                                    <option v-for="type in fieldTypes" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Label <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="fieldForm.label"
                                    @input="generateFieldName"
                                    type="text"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                                <div v-if="fieldForm.errors.label" class="mt-1 text-sm text-red-600">{{ fieldForm.errors.label }}</div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Field Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="fieldForm.name"
                                    type="text"
                                    required
                                    pattern="[a-z_][a-z0-9_]*"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                                <p class="mt-1 text-xs text-gray-500">Only lowercase letters, numbers, and underscores. Must start with a letter or underscore.</p>
                                <div v-if="fieldForm.errors.name" class="mt-1 text-sm text-red-600">{{ fieldForm.errors.name }}</div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Placeholder
                                </label>
                                <input
                                    v-model="fieldForm.placeholder"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                            </div>

                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input
                                        v-model="fieldForm.required"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Required Field</span>
                                </label>
                            </div>

                            <div v-if="needsOptions" class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Options <span class="text-red-500">*</span>
                                </label>
                                <div class="space-y-2">
                                    <div v-for="(option, index) in fieldForm.options" :key="index" class="flex items-center gap-2">
                                        <input
                                            type="text"
                                            v-model="fieldForm.options[index]"
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        />
                                        <button
                                            type="button"
                                            @click="removeOption(index)"
                                            class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                    <div class="flex gap-2">
                                        <input
                                            v-model="newOption"
                                            @keyup.enter="addOption"
                                            type="text"
                                            placeholder="Add option..."
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        />
                                        <button
                                            type="button"
                                            @click="addOption"
                                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                                        >
                                            Add
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-6">
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    :disabled="fieldForm.processing"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                                >
                                    {{ fieldForm.processing ? 'Saving...' : (editingField ? 'Update Field' : 'Add Field') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

