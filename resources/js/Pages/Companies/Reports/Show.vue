<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    department: Object,
    report: Object,
    submittedForms: Array,
    generalReportStatus: String,
});

const isFormSubmitted = (formId) => {
    return props.submittedForms && props.submittedForms.includes(formId);
};

const deleteReport = () => {
    if (confirm("Are you sure you want to delete this report?")) {
        router.delete(route("companies.departments.reports.destroy", [props.department.id, props.report.id]));
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
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    };
    return colors[status] || colors.pending;
};
</script>

<template>
    <Head title="Report Details" />

    <div class="p-6">
        <div class="mb-6">
            <Link
                :href="route('companies.departments.reports.index', department.id)"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
            >
                ← Back to Reports
            </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ report.title }}
                    </h1>
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:</span>
                            <span :class="['px-3 py-1 text-sm font-semibold rounded-full', getStatusColor(report.status)]">
                                {{ report.status.charAt(0).toUpperCase() + report.status.slice(1) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">General Report Status:</span>
                            <span :class="['px-3 py-1 text-sm font-semibold rounded-full', getGeneralStatusColor(generalReportStatus)]">
                                {{ generalReportStatus ? generalReportStatus.charAt(0).toUpperCase() + generalReportStatus.slice(1) : 'Pending' }}
                            </span>
                        </div>
                        <div v-if="report.description" class="text-sm text-gray-600 dark:text-gray-400">
                            <p class="font-medium text-gray-700 dark:text-gray-300 mb-1">Description:</p>
                            <p>{{ report.description }}</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div class="text-sm">
                                <p class="font-medium text-gray-700 dark:text-gray-300">Report Date:</p>
                                <p class="text-gray-600 dark:text-gray-400">{{ new Date(report.report_date).toLocaleDateString() }}</p>
                            </div>
                            <div class="text-sm">
                                <p class="font-medium text-gray-700 dark:text-gray-300">Created By:</p>
                                <p class="text-gray-600 dark:text-gray-400">
                                    {{ report.creator ? `${report.creator.fname} ${report.creator.lname}` : '-' }}
                                </p>
                            </div>
                            <div class="text-sm">
                                <p class="font-medium text-gray-700 dark:text-gray-300">Department:</p>
                                <p class="text-gray-600 dark:text-gray-400">{{ report.department?.name }}</p>
                            </div>
                            <div class="text-sm">
                                <p class="font-medium text-gray-700 dark:text-gray-300">Company:</p>
                                <p class="text-gray-600 dark:text-gray-400">{{ report.department?.company?.name }}</p>
                            </div>
                        </div>
                        <div v-if="report.forms && report.forms.length > 0" class="mt-8">
                            <!-- Enhanced Header -->
                            <div class="mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Attached Forms</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                {{ report.forms.length }} {{ report.forms.length === 1 ? 'form' : 'forms' }} attached to this report
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-3 py-1.5 text-sm font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full shadow-sm">
                                            {{ report.forms.filter(f => isFormSubmitted(f.id)).length }} Completed
                                        </span>
                                        <span class="px-3 py-1.5 text-sm font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full shadow-sm">
                                            {{ report.forms.filter(f => !isFormSubmitted(f.id)).length }} Pending
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Form Cards -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div
                                    v-for="form in report.forms"
                                    :key="form.id"
                                    :class="[
                                        'group relative overflow-hidden rounded-2xl border-2 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-2xl',
                                        isFormSubmitted(form.id)
                                            ? 'bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 dark:from-green-900/30 dark:via-emerald-900/20 dark:to-teal-900/30 border-green-300 dark:border-green-700 shadow-lg shadow-green-200/50 dark:shadow-green-900/20'
                                            : 'bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-blue-900/30 dark:via-indigo-900/20 dark:to-purple-900/30 border-blue-300 dark:border-blue-700 shadow-lg shadow-blue-200/50 dark:shadow-blue-900/20'
                                    ]"
                                >
                                    <!-- Decorative Background Pattern -->
                                    <div :class="[
                                        'absolute top-0 right-0 w-32 h-32 opacity-10',
                                        isFormSubmitted(form.id) ? 'text-green-600' : 'text-blue-600'
                                    ]">
                                        <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>

                                    <!-- Status Badge -->
                                    <div class="absolute top-4 right-4 z-10">
                                        <span
                                            v-if="isFormSubmitted(form.id)"
                                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-green-500 text-white shadow-lg animate-pulse"
                                        >
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Completed
                                        </span>
                                        <span
                                            v-else
                                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-yellow-500 text-white shadow-lg"
                                        >
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                            Pending
                                        </span>
                                    </div>

                                    <!-- Card Content -->
                                    <div class="relative p-6">
                                        <!-- Form Icon -->
                                        <div class="mb-4">
                                            <div :class="[
                                                'inline-flex items-center justify-center w-16 h-16 rounded-2xl shadow-lg transform transition-transform group-hover:rotate-6',
                                                isFormSubmitted(form.id)
                                                    ? 'bg-gradient-to-br from-green-400 to-emerald-500'
                                                    : 'bg-gradient-to-br from-blue-400 to-indigo-500'
                                            ]">
                                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Form Name -->
                                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                            {{ form.name }}
                                        </h4>

                                        <!-- Form Info -->
                                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                            <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Form ID: {{ form.id }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="mt-6 flex flex-col gap-2">
                                            <Link
                                                v-if="isFormSubmitted(form.id)"
                                                :href="route('companies.departments.reports.forms.show', [department.id, report.id, form.id])"
                                                class="w-full inline-flex items-center justify-center px-4 py-3 text-sm font-semibold text-white bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View Response
                                            </Link>
                                            <Link
                                                :href="route('companies.departments.reports.forms.create', [department.id, report.id, form.id])"
                                                :class="[
                                                    'w-full inline-flex items-center justify-center px-4 py-3 text-sm font-semibold rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5',
                                                    isFormSubmitted(form.id)
                                                        ? 'text-blue-700 bg-gradient-to-r from-blue-100 to-indigo-100 hover:from-blue-200 hover:to-indigo-200 dark:text-blue-300 dark:from-blue-800 dark:to-indigo-800 dark:hover:from-blue-700 dark:hover:to-indigo-700 focus:ring-blue-500'
                                                        : 'text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 focus:ring-blue-500'
                                                ]"
                                            >
                                                <svg v-if="!isFormSubmitted(form.id)" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                {{ isFormSubmitted(form.id) ? 'Update Response' : 'Fill Form' }}
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="mt-6">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-8 text-center border-2 border-dashed border-gray-300 dark:border-gray-600">
                                <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No forms attached to this report</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <Link
                        :href="route('companies.departments.reports.edit', [department.id, report.id])"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    >
                        Edit
                    </Link>
                    <button
                        @click="deleteReport"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

