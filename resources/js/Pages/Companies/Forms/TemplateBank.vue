<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    templates: Object,
    departments: {
        type: Array,
        default: () => [],
    },
    industryName: {
        type: String,
        default: null,
    },
});

const selectedTemplate = ref(null);
const previewTemplate = ref(null);
const addForm = useForm({
    form_template_id: null,
    department_id: null,
});

const openAddModal = (template) => {
    selectedTemplate.value = template;
    addForm.form_template_id = template.id;
    addForm.department_id = null;
    addForm.clearErrors();
};

const closeAddModal = () => {
    selectedTemplate.value = null;
    addForm.reset();
};

const submitAdd = () => {
    addForm.post(route("companies.forms.from-template"), {
        preserveScroll: true,
        onSuccess: () => closeAddModal(),
    });
};

const openPreview = (template) => {
    previewTemplate.value = template;
};

const closePreview = () => {
    previewTemplate.value = null;
};

const sortedPreviewFields = (template) => {
    const fields = template.form_template_fields || [];
    return [...fields].sort((a, b) => (a.order || 0) - (b.order || 0));
};

const categories = computed(() => Object.keys(props.templates || {}));
const hasTemplates = computed(() => categories.value.length > 0);

const navs = computed(() => [
    { name: "Dashboard", linkName: "companies.dashboard" },
    { name: "Forms", linkName: "companies.forms.index" },
    { name: "Form Template Bank", linkName: "companies.forms.templates.bank" },
]);
</script>

<template>
    <Head title="Form Template Bank" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            title="Form Template Bank"
            :showButton="false"
            :addSearchInput="false"
        />

        <p class="text-gray-600 dark:text-gray-400 mb-6">
            <template v-if="industryName">
                Showing form templates for your industry (<strong>{{ industryName }}</strong>). Select a template and choose a department to add it to your company's form list.
            </template>
            <template v-else>
                Select a template and choose a department to add it to your company's form list. You can later edit the form and its fields as needed.
            </template>
        </p>

        <div v-if="!hasTemplates" class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-6 text-amber-800 dark:text-amber-200">
            <p class="font-medium">No form templates available for your industry.</p>
            <p class="mt-1 text-sm">Contact your administrator to attach form templates to your industry.</p>
        </div>

        <div v-else class="space-y-8">
            <div
                v-for="category in categories"
                :key="category"
                class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden"
            >
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ category }} Forms
                    </h3>
                </div>
                <div class="p-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="template in templates[category]"
                        :key="template.id"
                        class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:border-blue-400 dark:hover:border-blue-500 transition-colors"
                    >
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                            {{ template.name }}
                        </h4>
                        <p
                            v-if="template.description"
                            class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2"
                        >
                            {{ template.description }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mb-4">
                            {{ template.form_template_fields?.length || 0 }} fields
                        </p>
                        <div class="flex gap-2">
                            <button
                                @click="openPreview(template)"
                                type="button"
                                class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600"
                            >
                                Preview
                            </button>
                            <button
                                @click="openAddModal(template)"
                                type="button"
                                class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                            >
                                Add to Forms
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <Link
                :href="route('companies.forms.index')"
                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
            >
                ← Back to Forms
            </Link>
        </div>

        <!-- Preview Template Modal -->
        <div
            v-if="previewTemplate"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            aria-labelledby="preview-modal"
            role="dialog"
            aria-modal="true"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closePreview"></div>

            <div class="relative w-full max-w-2xl max-h-[85vh] flex flex-col bg-white dark:bg-gray-800 rounded-xl shadow-2xl ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
                <!-- Header -->
                <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 bg-gradient-to-r from-slate-50 to-gray-50 dark:from-gray-800 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/50">
                            <i class="fa-solid fa-eye text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ previewTemplate.name }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Form preview</p>
                        </div>
                    </div>
                    <button
                        @click="closePreview"
                        class="p-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors"
                    >
                        <i class="fa-solid fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Scrollable content - min-h-0 is critical for flex scroll -->
                <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain">
                    <div class="p-6 space-y-5">
                        <div
                            v-for="field in sortedPreviewFields(previewTemplate)"
                            :key="field.id"
                            class="group rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800/50 p-4 shadow-sm hover:shadow-md transition-shadow"
                        >
                            <label class="block text-sm font-medium text-gray-800 dark:text-gray-200 mb-2.5">
                                {{ field.label }}
                                <span v-if="field.required" class="text-red-500 ml-0.5">*</span>
                            </label>
                            <!-- Text, Email, Number -->
                            <input
                                v-if="['text', 'email', 'number'].includes(field.field_type)"
                                :type="field.field_type"
                                :placeholder="field.placeholder || 'Enter...'"
                                disabled
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 placeholder-gray-400 dark:placeholder-gray-500"
                            />
                            <textarea
                                v-else-if="field.field_type === 'textarea'"
                                :placeholder="field.placeholder || 'Enter...'"
                                disabled
                                rows="3"
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 placeholder-gray-400 dark:placeholder-gray-500 resize-none"
                            ></textarea>
                            <input
                                v-else-if="field.field_type === 'date'"
                                type="date"
                                disabled
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400"
                            />
                            <select
                                v-else-if="field.field_type === 'select'"
                                disabled
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400"
                            >
                                <option value="">Select an option...</option>
                                <option v-for="opt in (field.options || [])" :key="opt">{{ opt }}</option>
                            </select>
                            <div v-else-if="field.field_type === 'radio'" class="space-y-2.5">
                                <label v-for="opt in (field.options || [])" :key="opt" class="flex items-center gap-3 py-1.5 px-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-default">
                                    <input type="radio" disabled class="w-4 h-4 text-blue-600" />
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ opt }}</span>
                                </label>
                            </div>
                            <div v-else-if="field.field_type === 'checkbox'" class="space-y-2.5">
                                <label v-for="opt in (field.options || [])" :key="opt" class="flex items-center gap-3 py-1.5 px-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-default">
                                    <input type="checkbox" disabled class="w-4 h-4 rounded text-blue-600" />
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ opt }}</span>
                                </label>
                            </div>
                            <div v-else-if="field.field_type === 'file'" class="flex items-center gap-3 px-4 py-3 border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/30">
                                <i class="fa-solid fa-cloud-arrow-up text-gray-400 dark:text-gray-500"></i>
                                <span class="text-sm text-gray-500 dark:text-gray-400">File upload</span>
                            </div>
                            <div v-else-if="field.field_type === 'signature'" class="flex items-center justify-center gap-3 px-4 py-6 border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/30">
                                <i class="fa-solid fa-signature text-gray-400 dark:text-gray-500"></i>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Digital signature</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex-shrink-0 px-6 py-4 bg-gray-50 dark:bg-gray-800/80 border-t border-gray-200 dark:border-gray-700">
                    <button
                        @click="closePreview"
                        class="w-full px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                    >
                        Close Preview
                    </button>
                </div>
            </div>
        </div>

        <!-- Add from Template Modal -->
        <div
            v-if="selectedTemplate"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    @click="closeAddModal"
                ></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            Add "{{ selectedTemplate.name }}" to Forms
                        </h3>

                        <form @submit.prevent="submitAdd">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Department (Optional)
                                </label>
                                <select
                                    v-model="addForm.department_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                                    <option :value="null">-- No department --</option>
                                    <option
                                        v-for="dept in departments"
                                        :key="dept.id"
                                        :value="dept.id"
                                    >
                                        {{ dept.name }}
                                    </option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Select a department to assign this form under.</p>
                                <div v-if="addForm.errors.department_id" class="mt-1 text-sm text-red-600">
                                    {{ addForm.errors.department_id }}
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-6">
                                <button
                                    type="button"
                                    @click="closeAddModal"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    :disabled="addForm.processing"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                                >
                                    {{ addForm.processing ? "Adding..." : "Add Form" }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
