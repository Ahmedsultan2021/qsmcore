<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    reports: Object,
    departments: Array,
    filters: Object,
});

const deleteReport = (report) => {
    if (confirm("Are you sure you want to delete this report?")) {
        router.delete(route("companies.departments.reports.destroy", [report.department.id, report.id]));
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

const navs = computed(() => [
    { name: "Dashboard", linkName: "companies.dashboard" },
    { name: "Reports", linkName: "companies.reports.index" },
]);

const filterForm = useForm({
    department_id: props.filters?.department_id || '',
});

const applyFilters = () => {
    filterForm.get(route('companies.reports.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    filterForm.department_id = '';
    applyFilters();
};
</script>

<template>
    <Head title="All Reports" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            title="All Reports"
            :showButton="false"
            :addSearchInput="false"
        />

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mt-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Department
                    </label>
                    <select
                        v-model="filterForm.department_id"
                        @change="applyFilters()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">All Departments</option>
                        <option v-for="department in departments" :key="department.id" :value="department.id">
                            {{ department.name }}
                        </option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button
                        @click="clearFilters()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500"
                    >
                        Clear Filter
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mt-6">
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Department
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Title
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Report Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Created By
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="report in reports.data" :key="report.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <Link
                                :href="route('companies.departments.show', report.department.id)"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                {{ report.department.name }}
                            </Link>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ report.title }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="['px-2 py-1 text-xs font-semibold rounded-full', getStatusColor(report.status)]">
                                {{ report.status.charAt(0).toUpperCase() + report.status.slice(1) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ new Date(report.report_date).toLocaleDateString() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ report.creator ? `${report.creator.fname} ${report.creator.lname}` : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                            <div class="max-w-xs truncate">
                                {{ report.description || "-" }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <Link
                                :href="route('companies.departments.reports.show', [report.department.id, report.id])"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3"
                            >
                                View
                            </Link>
                            <Link
                                :href="route('companies.departments.reports.edit', [report.department.id, report.id])"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3"
                            >
                                Edit
                            </Link>
                            <button
                                @click="deleteReport(report)"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>

            <div v-if="reports.links && reports.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link
                            v-if="reports.prev_page_url"
                            :href="reports.prev_page_url"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Previous
                        </Link>
                        <Link
                            v-if="reports.next_page_url"
                            :href="reports.next_page_url"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Next
                        </Link>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing
                                <span class="font-medium">{{ reports.from }}</span>
                                to
                                <span class="font-medium">{{ reports.to }}</span>
                                of
                                <span class="font-medium">{{ reports.total }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <template v-for="link in reports.links" :key="link.label">
                                    <Link
                                        v-if="link.url"
                                        :href="link.url"
                                        :class="[
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                            link.active
                                                ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                        ]"
                                    >
                                        <span v-html="link.label"></span>
                                    </Link>
                                    <span
                                        v-else
                                        :class="[
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium cursor-not-allowed opacity-50',
                                            'bg-white border-gray-300 text-gray-500'
                                        ]"
                                    >
                                        <span v-html="link.label"></span>
                                    </span>
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
