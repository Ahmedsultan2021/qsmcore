<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import { Head } from "@inertiajs/vue3";
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

defineOptions({ layout: CompanyLayout });

const page = usePage();
const employee = computed(() => page.props.authEmployee?.employee);

// Fake data for charts
const fakeData = {
    reports: {
        total: 156,
        thisMonth: 23,
        pending: 12,
        completed: 144,
    },
    incidents: {
        total: 45,
        thisMonth: 8,
        resolved: 38,
        pending: 7,
    },
    correctiveActions: {
        total: 89,
        thisMonth: 15,
        completed: 72,
        inProgress: 17,
    },
    employees: {
        total: employee.value?.company?.employees?.length || 0,
        active: employee.value?.company?.employees?.length || 0,
    },
};
</script>

<template>
    <Head title="Companies Dashboard" />

    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Welcome, {{ employee?.fname }}!
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                {{ employee?.company?.name }} - {{ employee?.company?.sector?.name }}
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Reports</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ fakeData.reports.total }}</p>
                        <p class="text-sm text-green-600 dark:text-green-400 mt-1">+{{ fakeData.reports.thisMonth }} this month</p>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Incidents</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ fakeData.incidents.total }}</p>
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ fakeData.incidents.pending }} pending</p>
                    </div>
                    <div class="bg-red-100 dark:bg-red-900 rounded-full p-3">
                        <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Corrective Actions</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ fakeData.correctiveActions.total }}</p>
                        <p class="text-sm text-yellow-600 dark:text-yellow-400 mt-1">{{ fakeData.correctiveActions.inProgress }} in progress</p>
                    </div>
                    <div class="bg-yellow-100 dark:bg-yellow-900 rounded-full p-3">
                        <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Employees</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ fakeData.employees.total }}</p>
                        <p class="text-sm text-green-600 dark:text-green-400 mt-1">{{ fakeData.employees.active }} active</p>
                    </div>
                    <div class="bg-green-100 dark:bg-green-900 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Reports Overview</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Completed</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ fakeData.reports.completed }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-blue-600 h-2.5 rounded-full" :style="`width: ${(fakeData.reports.completed / fakeData.reports.total) * 100}%`"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Pending</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ fakeData.reports.pending }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-yellow-600 h-2.5 rounded-full" :style="`width: ${(fakeData.reports.pending / fakeData.reports.total) * 100}%`"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Incidents Status</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Resolved</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ fakeData.incidents.resolved }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-green-600 h-2.5 rounded-full" :style="`width: ${(fakeData.incidents.resolved / fakeData.incidents.total) * 100}%`"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Pending</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ fakeData.incidents.pending }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-red-600 h-2.5 rounded-full" :style="`width: ${(fakeData.incidents.pending / fakeData.incidents.total) * 100}%`"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

