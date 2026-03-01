<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    audit: Object,
    completionStatus: String,
});

const deleteAudit = () => {
    if (confirm("Are you sure you want to delete this audit?")) {
        router.delete(route("companies.audits.destroy", props.audit.id));
    }
};

const getCompletionStatusColor = (status) => {
    const colors = {
        complete: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        pending: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
    };
    return colors[status] || colors.pending;
};

const isInternal = computed(() => {
    return props.audit.reports && props.audit.reports.length > 0;
});
</script>

<template>
    <Head title="Audit Details" />

    <div class="p-6">
        <div class="mb-6">
            <Link
                :href="route('companies.audits.index')"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
            >
                ← Back to Audit Tracker
            </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ audit.name }}
                    </h1>
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:</span>
                            <span :class="['px-3 py-1 text-sm font-semibold rounded-full', getCompletionStatusColor(completionStatus)]">
                                {{ completionStatus ? completionStatus.charAt(0).toUpperCase() + completionStatus.slice(1) : 'Pending' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Type:</span>
                            <span :class="[
                                'px-3 py-1 text-sm font-semibold rounded-full',
                                isInternal
                                    ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                    : 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
                            ]">
                                {{ isInternal ? 'Internal Audit' : 'External Audit' }}
                            </span>
                        </div>
                        <div v-if="audit.description" class="text-sm text-gray-600 dark:text-gray-400 mt-4">
                            <p class="font-medium text-gray-700 dark:text-gray-300 mb-1">Description:</p>
                            <p>{{ audit.description }}</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div class="text-sm">
                                <p class="font-medium text-gray-700 dark:text-gray-300">Audit Date:</p>
                                <p class="text-gray-600 dark:text-gray-400">
                                    {{ new Date(audit.audit_date).toLocaleDateString() }}
                                </p>
                            </div>
                            <div class="text-sm">
                                <p class="font-medium text-gray-700 dark:text-gray-300">Company:</p>
                                <p class="text-gray-600 dark:text-gray-400">{{ audit.company?.name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Link
                        :href="route('companies.audits.edit', audit.id)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium"
                    >
                        Edit
                    </Link>
                    <button
                        @click="deleteAudit"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium"
                    >
                        Delete
                    </button>
                </div>
            </div>

            <!-- Image -->
            <div v-if="audit.image" class="mb-6">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Image:</p>
                <img
                    :src="'/storage/' + audit.image"
                    :alt="audit.name"
                    class="max-w-md h-auto rounded-lg shadow-md"
                />
            </div>

            <!-- Attached File -->
            <div v-if="audit.attached_file" class="mb-6">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attached File:</p>
                <a
                    :href="'/storage/' + audit.attached_file"
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Download File
                </a>
            </div>

            <!-- Attached Reports (Internal Audit) -->
            <div v-if="isInternal" class="mt-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Attached Reports</h2>
                <div v-if="audit.reports && audit.reports.length > 0" class="space-y-4">
                    <div
                        v-for="report in audit.reports"
                        :key="report.id"
                        class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ report.title }}
                                </h3>
                                <div class="mt-2 space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Department:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ report.department?.name }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Status:</span>
                                        <span :class="[
                                            'px-2 py-1 text-xs font-semibold rounded-full',
                                            report.general_report_status === 'complete'
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                                        ]">
                                            {{ report.general_report_status === 'complete' ? 'Complete' : 'Pending' }}
                                        </span>
                                    </div>
                                    <div v-if="report.description" class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                        {{ report.description }}
                                    </div>
                                </div>
                            </div>
                            <Link
                                :href="route('companies.departments.reports.show', [report.department_id, report.id])"
                                class="ml-4 text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium"
                            >
                                View Report →
                            </Link>
                        </div>
                    </div>
                </div>
                <div v-else class="text-sm text-gray-500 dark:text-gray-400">
                    No reports attached to this audit.
                </div>
            </div>

            <!-- External Audit Notice -->
            <div v-else class="mt-8 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-purple-800 dark:text-purple-300">External Audit</p>
                        <p class="text-sm text-purple-700 dark:text-purple-400 mt-1">
                            This is an external audit with no attached reports. External audits are automatically marked as complete.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
