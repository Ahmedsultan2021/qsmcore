<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import SignaturePad from "@/Components/SignaturePad.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    department: Object,
    report: Object,
    form: Object,
    existingResponse: Object,
});

const submissionForm = useForm({
    responses: props.existingResponse?.responses || {},
});

const submit = () => {
    submissionForm.post(route("companies.departments.reports.forms.store", [props.department.id, props.report.id, props.form.id]));
};

const getFieldValue = (fieldName) => {
    return submissionForm.responses[fieldName] || '';
};

const setFieldValue = (fieldName, value) => {
    submissionForm.responses[fieldName] = value;
};

const navs = computed(() => [
    { name: "Dashboard", linkName: "companies.dashboard" },
    { name: "Departments", linkName: "companies.departments.index" },
    { name: props.department.name, linkName: "companies.departments.show", param: { department: props.department.id } },
    { name: "Reports", linkName: "companies.departments.reports.index", param: { department: props.department.id } },
    { name: props.report.title, linkName: "companies.departments.reports.show", param: { department: props.department.id, report: props.report.id } },
    { name: "Fill Form", linkName: "companies.departments.reports.forms.create", param: { department: props.department.id, report: props.report.id, form: props.form.id } },
]);
</script>

<template>
    <Head :title="`Fill Form: ${form.name}`" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            :title="`Fill Form: ${form.name}`"
            :showButton="false"
            :addSearchInput="false"
        />

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mt-6">
            <div class="mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Report: <span class="font-medium text-gray-900 dark:text-white">{{ report.title }}</span>
                </p>
            </div>

            <form @submit.prevent="submit">
                <div v-if="form.form_fields && form.form_fields.length > 0" class="space-y-6">
                    <div
                        v-for="field in form.form_fields"
                        :key="field.id"
                        class="border-b border-gray-200 dark:border-gray-700 pb-6 last:border-b-0"
                    >
                        <label :for="field.name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ field.label }}
                            <span v-if="field.required" class="text-red-500">*</span>
                        </label>

                        <!-- Text Input -->
                        <input
                            v-if="field.field_type === 'text'"
                            :id="field.name"
                            :name="field.name"
                            :value="getFieldValue(field.name)"
                            @input="setFieldValue(field.name, $event.target.value)"
                            :required="field.required"
                            :placeholder="field.placeholder || ''"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />

                        <!-- Textarea -->
                        <textarea
                            v-else-if="field.field_type === 'textarea'"
                            :id="field.name"
                            :name="field.name"
                            :value="getFieldValue(field.name)"
                            @input="setFieldValue(field.name, $event.target.value)"
                            :required="field.required"
                            :placeholder="field.placeholder || ''"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        ></textarea>

                        <!-- Email -->
                        <input
                            v-else-if="field.field_type === 'email'"
                            :id="field.name"
                            :name="field.name"
                            :value="getFieldValue(field.name)"
                            @input="setFieldValue(field.name, $event.target.value)"
                            :required="field.required"
                            :placeholder="field.placeholder || ''"
                            type="email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />

                        <!-- Number -->
                        <input
                            v-else-if="field.field_type === 'number'"
                            :id="field.name"
                            :name="field.name"
                            :value="getFieldValue(field.name)"
                            @input="setFieldValue(field.name, $event.target.value)"
                            :required="field.required"
                            :placeholder="field.placeholder || ''"
                            type="number"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />

                        <!-- Date -->
                        <input
                            v-else-if="field.field_type === 'date'"
                            :id="field.name"
                            :name="field.name"
                            :value="getFieldValue(field.name)"
                            @input="setFieldValue(field.name, $event.target.value)"
                            :required="field.required"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />

                        <!-- Select -->
                        <select
                            v-else-if="field.field_type === 'select'"
                            :id="field.name"
                            :name="field.name"
                            :value="getFieldValue(field.name)"
                            @change="setFieldValue(field.name, $event.target.value)"
                            :required="field.required"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="">Select an option...</option>
                            <option v-for="option in field.options" :key="option" :value="option">
                                {{ option }}
                            </option>
                        </select>

                        <!-- Radio -->
                        <div v-else-if="field.field_type === 'radio'" class="space-y-2">
                            <label
                                v-for="option in field.options"
                                :key="option"
                                class="flex items-center"
                            >
                                <input
                                    type="radio"
                                    :name="field.name"
                                    :value="option"
                                    :checked="getFieldValue(field.name) === option"
                                    @change="setFieldValue(field.name, option)"
                                    :required="field.required"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                />
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ option }}</span>
                            </label>
                        </div>

                        <!-- Checkbox -->
                        <div v-else-if="field.field_type === 'checkbox'" class="space-y-2">
                            <label
                                v-for="option in field.options"
                                :key="option"
                                class="flex items-center"
                            >
                                <input
                                    type="checkbox"
                                    :name="`${field.name}[]`"
                                    :value="option"
                                    :checked="getFieldValue(field.name) && getFieldValue(field.name).includes(option)"
                                    @change="
                                        const current = getFieldValue(field.name) || [];
                                        if ($event.target.checked) {
                                            setFieldValue(field.name, [...current, option]);
                                        } else {
                                            setFieldValue(field.name, current.filter(v => v !== option));
                                        }
                                    "
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                />
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ option }}</span>
                            </label>
                        </div>

                        <!-- File -->
                        <input
                            v-else-if="field.field_type === 'file'"
                            :id="field.name"
                            :name="field.name"
                            @change="setFieldValue(field.name, $event.target.files[0])"
                            :required="field.required"
                            type="file"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />

                        <!-- Digital Signature -->
                        <div v-else-if="field.field_type === 'signature'" class="mt-1">
                            <SignaturePad
                                :model-value="getFieldValue(field.name)"
                                @update:model-value="setFieldValue(field.name, $event)"
                            />
                        </div>

                        <div v-if="submissionForm.errors[`responses.${field.name}`]" class="mt-1 text-sm text-red-600">
                            {{ submissionForm.errors[`responses.${field.name}`] }}
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>No fields available in this form. Please add fields first.</p>
                </div>

                <div class="flex items-center justify-end space-x-3 mt-6">
                    <Link
                        :href="route('companies.departments.reports.show', { department: department.id, report: report.id })"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="submissionForm.processing"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        {{ submissionForm.processing ? "Submitting..." : (existingResponse ? "Update Response" : "Submit Form") }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

