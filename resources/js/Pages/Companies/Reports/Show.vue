<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    department: Object,
    report: Object,
    submittedForms: Array,
    generalReportStatus: String,
});

const navs = computed(() => [
    { name: "Dashboard", linkName: "companies.dashboard" },
    { name: "Departments", linkName: "companies.departments.index" },
    { name: props.department.name, linkName: "companies.departments.show", param: { department: props.department.id } },
    { name: "Reports", linkName: "companies.departments.reports.index", param: { department: props.department.id } },
    { name: props.report.title, linkName: "companies.departments.reports.show", param: { department: props.department.id, report: props.report.id } },
]);

const isFormSubmitted = (formId) => {
    return props.submittedForms && props.submittedForms.includes(formId);
};

const deleteReport = () => {
    if (confirm("Are you sure you want to delete this report?")) {
        router.delete(route("companies.departments.reports.destroy", { department: props.department.id, report: props.report.id }));
    }
};

const getStatusColor = (status) => {
    const colors = {
        draft: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        submitted: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        reviewed: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        approved: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        rejected: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    };
    return colors[status] || colors.draft;
};

const getGeneralStatusColor = (status) => {
    const colors = {
        complete: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        pending: 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
    };
    return colors[status] || colors.pending;
};
</script>

<template>
    <Head title="Report Details" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            :title="report.title"
            :showButton="false"
            :addSearchInput="false"
        />

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700 mt-6">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <span :class="['px-3 py-1 text-sm font-medium rounded-md', getStatusColor(report.status)]">
                                {{ report.status.charAt(0).toUpperCase() + report.status.slice(1) }}
                            </span>
                            <span :class="['px-3 py-1 text-sm font-medium rounded-md', getGeneralStatusColor(generalReportStatus)]">
                                {{ generalReportStatus ? generalReportStatus.charAt(0).toUpperCase() + generalReportStatus.slice(1) : 'Pending' }}
                            </span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 mb-0.5">Report Date</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ new Date(report.report_date).toLocaleDateString() }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 mb-0.5">Created By</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ report.creator ? `${report.creator.fname} ${report.creator.lname}` : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 mb-0.5">Department</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ report.department?.name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 mb-0.5">Company</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ report.department?.company?.name }}</p>
                            </div>
                        </div>
                        <p v-if="report.description" class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            {{ report.description }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <Link
                            :href="route('companies.departments.reports.edit', { department: department.id, report: report.id })"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                        >
                            Edit
                        </Link>
                        <button
                            @click="deleteReport"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Attached Forms Section -->
            <div class="p-6">
                <div v-if="report.forms && report.forms.length > 0">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Attached Forms</h3>
                        <div class="flex gap-2 text-sm">
                            <span class="text-gray-500 dark:text-gray-400">
                                {{ report.forms.filter(f => isFormSubmitted(f.id)).length }} of {{ report.forms.length }} completed
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="form in report.forms"
                            :key="form.id"
                            class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/30 hover:border-gray-300 dark:hover:border-gray-500 transition-colors"
                        >
                            <div class="flex items-start justify-between mb-3">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ form.name }}
                                </h4>
                                <span
                                    v-if="isFormSubmitted(form.id)"
                                    class="px-2 py-0.5 text-xs font-medium rounded bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
                                >
                                    Completed
                                </span>
                                <span
                                    v-else
                                    class="px-2 py-0.5 text-xs font-medium rounded bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200"
                                >
                                    Pending
                                </span>
                            </div>
                            <div class="flex gap-2">
                                <Link
                                    v-if="isFormSubmitted(form.id)"
                                    :href="route('companies.departments.reports.forms.show', { department: department.id, report: report.id, form: form.id })"
                                    class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                                >
                                    View Response
                                </Link>
                                <Link
                                    :href="route('companies.departments.reports.forms.create', { department: department.id, report: report.id, form: form.id })"
                                    :class="[
                                        'flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-md',
                                        isFormSubmitted(form.id)
                                            ? 'text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800 dark:hover:bg-blue-900/50'
                                            : 'text-white bg-blue-600 border border-transparent hover:bg-blue-700'
                                    ]"
                                >
                                    {{ isFormSubmitted(form.id) ? 'Update' : 'Fill Form' }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="py-12 text-center border border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                    <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No forms attached to this report</p>
                    <Link
                        :href="route('companies.departments.reports.edit', { department: department.id, report: report.id })"
                        class="mt-3 inline-block text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                        Attach forms from Edit
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

