<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link } from "@inertiajs/vue3";
import { computed } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    department: Object,
    report: Object,
    form: Object,
    response: Object,
});

const navs = computed(() => [
    { name: "Dashboard", linkName: "companies.dashboard" },
    { name: "Departments", linkName: "companies.departments.index" },
    { name: props.department.name, linkName: "companies.departments.show", param: { department: props.department.id } },
    { name: "Reports", linkName: "companies.departments.reports.index", param: { department: props.department.id } },
    { name: props.report.title, linkName: "companies.departments.reports.show", param: { department: props.department.id, report: props.report.id } },
    { name: "Form Response", linkName: "companies.departments.reports.forms.show", param: { department: props.department.id, report: props.report.id, form: props.form.id } },
]);
</script>

<template>
    <Head :title="`Form Response: ${form.name}`" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            :title="`Form Response: ${form.name}`"
            navLinkName="companies.departments.reports.forms.create"
            NavLinkText="Update Response"
            :showButton="true"
            :addSearchInput="false"
            :navLinkValue="{ department: department.id, report: report.id, form: form.id }"
        />

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mt-6">
            <div class="mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Report: <span class="font-medium text-gray-900 dark:text-white">{{ report.title }}</span>
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Submitted: <span class="font-medium text-gray-900 dark:text-white">{{ new Date(response.created_at).toLocaleString() }}</span>
                </p>
                <p v-if="response.updated_at !== response.created_at" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Last Updated: <span class="font-medium text-gray-900 dark:text-white">{{ new Date(response.updated_at).toLocaleString() }}</span>
                </p>
            </div>

            <div v-if="form.form_fields && form.form_fields.length > 0" class="space-y-6">
                <div
                    v-for="field in form.form_fields"
                    :key="field.id"
                    class="border-b border-gray-200 dark:border-gray-700 pb-6 last:border-b-0"
                >
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ field.label }}
                    </label>
                    <div class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                        <span v-if="!response.responses || !response.responses[field.name] || (Array.isArray(response.responses[field.name]) && response.responses[field.name].length === 0)" class="text-gray-400 italic">
                            No response provided
                        </span>
                        <img
                            v-else-if="field.field_type === 'signature' && response.responses[field.name] && response.responses[field.name].startsWith('data:image')"
                            :src="response.responses[field.name]"
                            alt="Signature"
                            class="max-w-xs border border-gray-200 dark:border-gray-600 rounded"
                        />
                        <span v-else-if="Array.isArray(response.responses[field.name])">
                            {{ response.responses[field.name].join(', ') }}
                        </span>
                        <span v-else>
                            {{ response.responses[field.name] }}
                        </span>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                <p>No fields in this form.</p>
            </div>
        </div>
    </div>
</template>

