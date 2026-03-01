<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    form: Object,
});

// Form title editing
const isEditingTitle = ref(false);
const editingTitle = ref("");
const titleForm = useForm({
    name: props.form.name || "",
});

// Field editing
const editingFieldId = ref(null);
const showAddFieldModal = ref(false);
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
const draggedField = ref(null);
const quickAddType = ref(null);
const showImportModal = ref(false);
const importFile = ref(null);
const importForm = useForm({
    file: null,
});
const showPreviewMode = ref(false);

const fieldTypes = [
    { value: 'text', label: 'Short answer', icon: 'fa-align-left' },
    { value: 'textarea', label: 'Paragraph', icon: 'fa-align-justify' },
    { value: 'email', label: 'Email', icon: 'fa-envelope' },
    { value: 'number', label: 'Number', icon: 'fa-0' },
    { value: 'date', label: 'Date', icon: 'fa-calendar' },
    { value: 'select', label: 'Dropdown', icon: 'fa-chevron-down' },
    { value: 'radio', label: 'Multiple choice', icon: 'fa-circle' },
    { value: 'checkbox', label: 'Checkboxes', icon: 'fa-square' },
    { value: 'file', label: 'File upload', icon: 'fa-file' },
    { value: 'signature', label: 'Digital signature', icon: 'fa-signature' },
];

// Form fields sorted by order
const sortedFields = computed(() => {
    if (!props.form.form_fields) return [];
    return [...props.form.form_fields].sort((a, b) => (a.order || 0) - (b.order || 0));
});

// Title editing
const startEditingTitle = () => {
    editingTitle.value = props.form.name;
    isEditingTitle.value = true;
};

const saveTitle = () => {
    titleForm.name = editingTitle.value;
    titleForm.put(route("companies.forms.update", props.form.id), {
        preserveScroll: true,
        onSuccess: () => {
            isEditingTitle.value = false;
        },
    });
};

const cancelEditingTitle = () => {
    isEditingTitle.value = false;
    editingTitle.value = "";
};

// Field operations
const openAddFieldModal = (fieldType = null) => {
    editingFieldId.value = null;
    fieldForm.reset();
    fieldForm.field_type = fieldType || 'text';
    fieldForm.required = false;
    fieldForm.options = [];
    const maxOrder = sortedFields.value.length > 0 
        ? Math.max(...sortedFields.value.map(f => f.order || 0)) 
        : -1;
    fieldForm.order = maxOrder + 1;
    quickAddType.value = fieldType;
    showAddFieldModal.value = true;
};

const openEditFieldModal = (field) => {
    editingFieldId.value = field.id;
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
    editingFieldId.value = null;
    quickAddType.value = null;
    fieldForm.reset();
    newOption.value = '';
};

const submitField = () => {
    if (editingFieldId.value) {
        fieldForm.put(route('companies.forms.fields.update', [props.form.id, editingFieldId.value]), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    } else {
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

const duplicateField = (field) => {
    const newField = {
        field_type: field.field_type,
        label: field.label + ' (Copy)',
        name: field.name + '_copy',
        placeholder: field.placeholder || '',
        required: field.required || false,
        options: field.options ? [...field.options] : [],
        order: (field.order || 0) + 1,
    };
    
    fieldForm.field_type = newField.field_type;
    fieldForm.label = newField.label;
    fieldForm.name = newField.name;
    fieldForm.placeholder = newField.placeholder;
    fieldForm.required = newField.required;
    fieldForm.options = newField.options;
    fieldForm.order = newField.order;
    
    fieldForm.post(route('companies.forms.fields.store', props.form.id), {
        preserveScroll: true,
    });
};

// Options management
const addOption = () => {
    if (newOption.value.trim()) {
        fieldForm.options.push(newOption.value.trim());
        newOption.value = '';
    }
};

const removeOption = (index) => {
    fieldForm.options.splice(index, 1);
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

// Drag and drop
const onDragStart = (event, field) => {
    draggedField.value = field;
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/html', event.target);
};

const onDragOver = (event) => {
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';
};

const onDrop = (event, targetField) => {
    event.preventDefault();
    
    if (!draggedField.value || draggedField.value.id === targetField.id) {
        draggedField.value = null;
        return;
    }

    const draggedIndex = sortedFields.value.findIndex(f => f.id === draggedField.value.id);
    const targetIndex = sortedFields.value.findIndex(f => f.id === targetField.id);

    if (draggedIndex === -1 || targetIndex === -1) {
        draggedField.value = null;
        return;
    }

    // Update orders
    const fields = sortedFields.value.map((field, index) => ({
        id: field.id,
        order: index === draggedIndex ? targetIndex : (index === targetIndex ? draggedIndex : index),
    }));

    // Reorder array
    const newFields = [...sortedFields.value];
    const [removed] = newFields.splice(draggedIndex, 1);
    newFields.splice(targetIndex, 0, removed);

    // Update orders based on new positions
    const updatedFields = newFields.map((field, index) => ({
        id: field.id,
        order: index,
    }));

    router.post(route('companies.forms.fields.update-order', props.form.id), {
        fields: updatedFields,
    }, {
        preserveScroll: true,
        only: ['form'],
    });

    draggedField.value = null;
};

const deleteForm = () => {
    if (confirm("Are you sure you want to delete this form?")) {
        router.delete(route("companies.forms.destroy", props.form.id));
    }
};

// Excel Import/Export - download handled via anchor tag

const openImportModal = () => {
    showImportModal.value = true;
    importFile.value = null;
    importForm.reset();
};

const closeImportModal = () => {
    showImportModal.value = false;
    importFile.value = null;
    importForm.reset();
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        importFile.value = file;
        importForm.file = file;
    }
};

const togglePreviewMode = () => {
    showPreviewMode.value = !showPreviewMode.value;
};

const submitImport = () => {
    if (!importFile.value) {
        alert('Please select a file to import');
        return;
    }

    importForm.file = importFile.value;
    
    importForm.post(route('companies.forms.import', props.form.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            closeImportModal();
        },
        onError: (errors) => {
            console.error('Import errors:', errors);
        },
    });
};

</script>

<template>
    <Head :title="form.name" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 pb-20">
        <!-- Top Toolbar -->
        <div class="sticky top-0 z-40 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('companies.forms.index')"
                        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                    >
                        <i class="fa-solid fa-arrow-left text-gray-600 dark:text-gray-400"></i>
                    </Link>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Forms</span>
                </div>
                <div class="flex items-center gap-2">
                    <template v-if="!showPreviewMode">
                        <a
                            :href="route('companies.forms.template.download')"
                            class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors inline-flex items-center"
                            title="Download Excel Template"
                            download
                        >
                            <i class="fa-solid fa-download mr-2"></i>Download Template
                        </a>
                        <button
                            @click="openImportModal"
                        class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                        title="Import from Excel"
                    >
                            <i class="fa-solid fa-upload mr-2"></i>Import
                        </button>
                    </template>
                    <button
                        @click="togglePreviewMode"
                        :class="[
                            'px-4 py-2 text-sm rounded-md transition-colors',
                            showPreviewMode
                                ? 'bg-amber-500 text-white hover:bg-amber-600'
                                : 'text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'
                        ]"
                        :title="showPreviewMode ? 'Exit Preview' : 'Preview Form'"
                    >
                        <i :class="showPreviewMode ? 'fa-solid fa-pencil mr-2' : 'fa-solid fa-eye mr-2'"></i>
                        {{ showPreviewMode ? 'Edit' : 'Preview' }}
                    </button>
                    <button
                        v-if="!showPreviewMode"
                        @click="deleteForm"
                        class="px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors"
                    >
                        <i class="fa-solid fa-trash mr-2"></i>Delete
                    </button>
                    <button
                        v-if="!showPreviewMode"
                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors"
                    >
                        <i class="fa-solid fa-paper-plane mr-2"></i>Send
                    </button>
                </div>
            </div>
        </div>

        <!-- Preview Mode Banner -->
        <div
            v-if="showPreviewMode"
            class="max-w-4xl mx-auto px-4 py-3"
        >
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border border-amber-200/80 dark:border-amber-700/50 shadow-sm">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-800/50">
                    <i class="fa-solid fa-eye text-amber-600 dark:text-amber-400"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-amber-900 dark:text-amber-100">Preview mode</p>
                    <p class="text-xs text-amber-700/80 dark:text-amber-300/80">This is how the form will appear when filled.</p>
                </div>
            </div>
        </div>

        <!-- Main Form Container -->
        <div class="max-w-4xl mx-auto px-4 py-8">
            <!-- Form Header -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 mb-4">
                <template v-if="showPreviewMode">
                    <h1 class="text-3xl font-normal text-gray-900 dark:text-white mb-2">
                        {{ form.name }}
                    </h1>
                </template>
                <template v-else>
                    <div v-if="!isEditingTitle" @click="startEditingTitle" class="cursor-text group">
                        <h1 class="text-3xl font-normal text-gray-900 dark:text-white mb-2 group-hover:border-b-2 group-hover:border-gray-300 dark:group-hover:border-gray-600 pb-1 transition-all">
                            {{ form.name }}
                        </h1>
                    </div>
                    <div v-else class="mb-2">
                        <input
                            v-model="editingTitle"
                            @keyup.enter="saveTitle"
                            @keyup.esc="cancelEditingTitle"
                            @blur="saveTitle"
                            type="text"
                            class="text-3xl font-normal w-full border-none outline-none bg-transparent text-gray-900 dark:text-white focus:border-b-2 focus:border-blue-500 pb-1"
                            autofocus
                        />
                    </div>
                </template>
                <div v-if="form.department" class="mt-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        <i class="fa-solid fa-building mr-2"></i>{{ form.department.name }}
                    </span>
                </div>
                <p v-if="!showPreviewMode" class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                    Form description (click to edit)
                </p>
            </div>

            <!-- Form Fields -->
            <div class="space-y-4">
                <div
                    v-for="(field, index) in sortedFields"
                    :key="field.id"
                    :draggable="!showPreviewMode"
                    @dragstart="!showPreviewMode && onDragStart($event, field)"
                    @dragover="onDragOver"
                    @drop="onDrop($event, field)"
                    :class="[
                        'rounded-lg border p-6 transition-all',
                        showPreviewMode
                            ? 'bg-white dark:bg-gray-800 shadow-sm border-gray-200 dark:border-gray-600 hover:shadow-md'
                            : 'bg-white dark:bg-gray-800 shadow-sm border-gray-200 dark:border-gray-700 hover:shadow-md group relative'
                    ]"
                >
                    <!-- Field Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span v-if="!showPreviewMode" class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ index + 1 }}.
                                </span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ field.label }}
                                </span>
                                <span v-if="field.required" class="text-red-500 text-sm">*</span>
                            </div>
                            <span v-if="!showPreviewMode" class="text-xs text-gray-400 dark:text-gray-500">
                                {{ fieldTypes.find(t => t.value === field.field_type)?.label || field.field_type }}
                            </span>
                        </div>
                        <div v-if="!showPreviewMode" class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button
                                @click="duplicateField(field)"
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                                title="Duplicate"
                            >
                                <i class="fa-solid fa-copy text-xs"></i>
                            </button>
                            <button
                                @click="openEditFieldModal(field)"
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                                title="Edit"
                            >
                                <i class="fa-solid fa-pencil text-xs"></i>
                            </button>
                            <button
                                @click="deleteField(field.id)"
                                class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                                title="Delete"
                            >
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                            <button
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded cursor-move"
                                title="Drag to reorder"
                            >
                                <i class="fa-solid fa-grip-vertical text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Field Preview -->
                    <div :class="showPreviewMode ? 'mt-3' : 'mt-4'">
                        <!-- Text, Email, Number -->
                        <input
                            v-if="['text', 'email', 'number'].includes(field.field_type)"
                            :type="field.field_type"
                            :placeholder="field.placeholder || (showPreviewMode ? 'Enter...' : '')"
                            :required="field.required"
                            disabled
                            :class="[
                                'w-full px-4 py-2.5 text-sm border rounded-lg',
                                showPreviewMode
                                    ? 'border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 placeholder-gray-400'
                                    : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
                            ]"
                        />
                        
                        <!-- Textarea -->
                        <textarea
                            v-else-if="field.field_type === 'textarea'"
                            :placeholder="field.placeholder || (showPreviewMode ? 'Enter...' : '')"
                            :required="field.required"
                            disabled
                            :class="[
                                'w-full px-4 py-2.5 text-sm border rounded-lg resize-none',
                                showPreviewMode
                                    ? 'border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 placeholder-gray-400'
                                    : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
                            ]"
                            rows="3"
                        ></textarea>
                        
                        <!-- Date -->
                        <input
                            v-else-if="field.field_type === 'date'"
                            type="date"
                            :required="field.required"
                            disabled
                            :class="[
                                'w-full px-4 py-2.5 text-sm border rounded-lg',
                                showPreviewMode
                                    ? 'border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400'
                                    : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
                            ]"
                        />
                        
                        <!-- Select -->
                        <select
                            v-else-if="field.field_type === 'select'"
                            :required="field.required"
                            disabled
                            :class="[
                                'w-full px-4 py-2.5 text-sm border rounded-lg',
                                showPreviewMode
                                    ? 'border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400'
                                    : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
                            ]"
                        >
                            <option v-if="!field.options || field.options.length === 0">Select...</option>
                            <option v-for="(opt, idx) in field.options" :key="idx">{{ opt }}</option>
                        </select>
                        
                        <!-- Radio -->
                        <div v-else-if="field.field_type === 'radio'" class="space-y-2">
                            <div v-if="!field.options || field.options.length === 0" class="text-gray-400 dark:text-gray-500 text-sm">
                                No options
                            </div>
                            <div v-for="(opt, idx) in field.options" :key="idx" class="flex items-center gap-3 py-1.5">
                                <input
                                    type="radio"
                                    :name="`preview_${field.id}`"
                                    :id="`preview_${field.id}_${idx}`"
                                    disabled
                                    class="w-4 h-4"
                                />
                                <label :for="`preview_${field.id}_${idx}`" class="text-gray-700 dark:text-gray-300 text-sm">
                                    {{ opt }}
                                </label>
                            </div>
                        </div>
                        
                        <!-- Checkbox -->
                        <div v-else-if="field.field_type === 'checkbox'" class="space-y-2">
                            <div v-if="!field.options || field.options.length === 0" class="text-gray-400 dark:text-gray-500 text-sm">
                                No options
                            </div>
                            <div v-for="(opt, idx) in field.options" :key="idx" class="flex items-center gap-3 py-1.5">
                                <input
                                    type="checkbox"
                                    :id="`preview_${field.id}_${idx}`"
                                    disabled
                                    class="w-4 h-4 rounded"
                                />
                                <label :for="`preview_${field.id}_${idx}`" class="text-gray-700 dark:text-gray-300 text-sm">
                                    {{ opt }}
                                </label>
                            </div>
                        </div>
                        
                        <!-- File -->
                        <div v-else-if="field.field_type === 'file'" :class="[
                            'flex items-center gap-3 px-4 py-3 border-2 border-dashed rounded-lg',
                            showPreviewMode
                                ? 'border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/30'
                                : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700'
                        ]">
                            <i class="fa-solid fa-cloud-arrow-up text-gray-400 dark:text-gray-500"></i>
                            <span class="text-sm text-gray-500 dark:text-gray-400">File upload</span>
                        </div>

                        <!-- Digital Signature -->
                        <div v-else-if="field.field_type === 'signature'" :class="[
                            'flex items-center justify-center gap-3 px-4 py-6 border-2 border-dashed rounded-lg',
                            showPreviewMode
                                ? 'border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/30'
                                : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700'
                        ]">
                            <i class="fa-solid fa-signature text-gray-400 dark:text-gray-500"></i>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Sign here</span>
                        </div>
                    </div>
                </div>

                <!-- Add Field Button -->
                <div
                    v-if="!showPreviewMode"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border-2 border-dashed border-gray-300 dark:border-gray-600 p-8 text-center"
                >
                    <button
                        @click="openAddFieldModal()"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium"
                    >
                        <i class="fa-solid fa-plus mr-2"></i>Add question
                    </button>
                </div>
            </div>
        </div>

        <!-- Floating Action Button -->
        <div v-if="!showPreviewMode" class="fixed bottom-6 right-6">
            <div class="relative">
                <button
                    @click="openAddFieldModal()"
                    class="w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-all hover:scale-110 flex items-center justify-center"
                >
                    <i class="fa-solid fa-plus text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Import Modal -->
        <div
            v-if="showImportModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            @click.self="closeImportModal"
        >
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeImportModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                                Import Form Fields from Excel
                            </h3>
                            <button
                                @click="closeImportModal"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            >
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Upload an Excel file (.xlsx, .xls, or .csv) with your form fields. 
                                <Link
                                    :href="route('companies.forms.template.download')"
                                    class="text-blue-600 dark:text-blue-400 hover:underline"
                                >
                                    Download the template
                                </Link>
                                to see the required format.
                            </p>
                        </div>

                        <form @submit.prevent="submitImport">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Select Excel File <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="file"
                                    @change="handleFileChange"
                                    accept=".xlsx,.xls,.csv"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    Accepted formats: .xlsx, .xls, .csv (Max: 10MB)
                                </p>
                                <div v-if="importForm.errors.file" class="mt-1 text-sm text-red-600">
                                    {{ importForm.errors.file }}
                                </div>
                            </div>

                            <div v-if="importFile" class="mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fa-solid fa-file-excel text-green-600 dark:text-green-400 mr-2"></i>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ importFile.name }}</span>
                                    </div>
                                    <span class="text-xs text-gray-500">
                                        {{ (importFile.size / 1024).toFixed(2) }} KB
                                    </span>
                                </div>
                            </div>

                            <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-md">
                                <p class="text-sm text-blue-800 dark:text-blue-300 font-medium mb-2">
                                    <i class="fa-solid fa-info-circle mr-2"></i>Import Instructions:
                                </p>
                                <ul class="text-xs text-blue-700 dark:text-blue-400 list-disc list-inside space-y-1">
                                    <li>Field Type: text, textarea, email, number, date, select, radio, checkbox, file, signature</li>
                                    <li>Label: The question text shown to users</li>
                                    <li>Field Name: Internal name (lowercase, underscores only)</li>
                                    <li>Required: 1 for yes, 0 for no</li>
                                    <li>Options: Comma-separated values for select/radio/checkbox types</li>
                                </ul>
                            </div>

                            <div v-if="importForm.errors.import" class="mb-4">
                                <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-md">
                                    <p class="text-sm font-medium text-red-800 dark:text-red-300 mb-2">Import Errors:</p>
                                    <ul class="text-xs text-red-700 dark:text-red-400 list-disc list-inside space-y-1">
                                        <li v-for="(error, index) in importForm.errors.import" :key="index">{{ error }}</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button
                                    type="button"
                                    @click="closeImportModal"
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    :disabled="importForm.processing || !importFile"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                                >
                                    {{ importForm.processing ? 'Importing...' : 'Import Fields' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Field Modal -->
        <div
            v-if="showAddFieldModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            @click.self="closeModal"
        >
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                                {{ editingFieldId ? 'Edit Question' : 'Add Question' }}
                            </h3>
                            <button
                                @click="closeModal"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            >
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>

                        <!-- Quick Add Buttons (only when adding new and no type pre-selected) -->
                        <div v-if="!editingFieldId && !quickAddType" class="mb-6 grid grid-cols-3 gap-2">
                            <button
                                v-for="type in fieldTypes.slice(0, 6)"
                                :key="type.value"
                                @click="openAddFieldModal(type.value)"
                                class="p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-left"
                            >
                                <i :class="`fa-solid ${type.icon} mr-2 text-gray-600 dark:text-gray-400`"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ type.label }}</span>
                            </button>
                        </div>

                        <form @submit.prevent="submitField">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Question Type <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="fieldForm.field_type"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                >
                                    <option v-for="type in fieldTypes" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Question <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="fieldForm.label"
                                    @input="generateFieldName"
                                    type="text"
                                    required
                                    placeholder="Enter your question"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
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
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                />
                                <p class="mt-1 text-xs text-gray-500">Only lowercase letters, numbers, and underscores</p>
                                <div v-if="fieldForm.errors.name" class="mt-1 text-sm text-red-600">{{ fieldForm.errors.name }}</div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Placeholder
                                </label>
                                <input
                                    v-model="fieldForm.placeholder"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                />
                            </div>

                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input
                                        v-model="fieldForm.required"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Required</span>
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
                                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                        />
                                        <button
                                            type="button"
                                            @click="removeOption(index)"
                                            class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                                        >
                                            <i class="fa-solid fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="flex gap-2">
                                        <input
                                            v-model="newOption"
                                            @keyup.enter="addOption"
                                            type="text"
                                            placeholder="Add option..."
                                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
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

                            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    :disabled="fieldForm.processing"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                                >
                                    {{ fieldForm.processing ? 'Saving...' : (editingFieldId ? 'Update' : 'Add') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
