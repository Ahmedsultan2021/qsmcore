<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/vue3";

defineOptions({ layout: AuthenticatedLayout });

const page = usePage();
const user = computed(() => page.props.auth?.user);

// Fake data for charts and statistics
const stats = ref({
    industries: {
        total: 12,
        active: 10,
        growth: 8.5,
    },
    sectors: {
        total: 45,
        active: 42,
        growth: 12.3,
    },
    companies: {
        total: 156,
        active: 148,
        growth: 15.7,
    },
    employees: {
        total: 1245,
        active: 1189,
        growth: 9.2,
    },
});

const chartData = ref({
    industriesGrowth: [12, 15, 18, 14, 16, 20, 12],
    companiesGrowth: [120, 135, 142, 138, 145, 156, 148],
    monthlyReports: [45, 52, 48, 61, 55, 67, 58],
    monthlyIncidents: [8, 12, 6, 15, 10, 9, 11],
});

// Fake data for reports and incidents charts (same as Companies Dashboard)
const fakeData = ref({
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
});

const recentActivities = ref([
    { type: 'company', action: 'New company registered', name: 'Tech Corp', time: '2 hours ago' },
    { type: 'employee', action: 'New employee added', name: 'John Doe', time: '5 hours ago' },
    { type: 'sector', action: 'Sector updated', name: 'Manufacturing', time: '1 day ago' },
    { type: 'industry', action: 'Industry created', name: 'Healthcare', time: '2 days ago' },
]);
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="p-6 space-y-6">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Welcome back, {{ user?.fname || 'Admin' }}!
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Here's what's happening with your system today.
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Industries Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="text-green-600 dark:text-green-400 text-sm font-semibold flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ stats.industries.growth }}%
                    </span>
                </div>
                <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Total Industries</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.industries.total }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ stats.industries.active }} active</p>
            </div>

            <!-- Sectors Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-100 dark:bg-green-900 rounded-lg p-3">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z" />
                        </svg>
                    </div>
                    <span class="text-green-600 dark:text-green-400 text-sm font-semibold flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ stats.sectors.growth }}%
                    </span>
                </div>
                <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Total Sectors</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.sectors.total }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ stats.sectors.active }} active</p>
            </div>

            <!-- Companies Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 dark:bg-purple-900 rounded-lg p-3">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="text-green-600 dark:text-green-400 text-sm font-semibold flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ stats.companies.growth }}%
                    </span>
                </div>
                <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Total Companies</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.companies.total }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ stats.companies.active }} active</p>
            </div>

            <!-- Employees Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-orange-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-orange-100 dark:bg-orange-900 rounded-lg p-3">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="text-green-600 dark:text-green-400 text-sm font-semibold flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ stats.employees.growth }}%
                    </span>
                </div>
                <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Total Employees</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.employees.total }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ stats.employees.active }} active</p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Growth Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Growth Overview</h3>
                <div class="h-64 flex items-end justify-between space-x-2">
                    <div v-for="(value, index) in chartData.industriesGrowth" :key="index" class="flex-1 flex flex-col items-center">
                        <div 
                            class="w-full bg-gradient-to-t from-blue-500 to-blue-400 rounded-t-lg hover:from-blue-600 hover:to-blue-500 transition-all cursor-pointer"
                            :style="`height: ${(value / Math.max(...chartData.industriesGrowth)) * 100}%`"
                            :title="`${value} industries`"
                        ></div>
                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][index] }}</span>
                    </div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-4 text-center">Industries growth over the week</p>
            </div>

            <!-- Companies Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Companies Registration</h3>
                <div class="h-64 flex items-end justify-between space-x-2">
                    <div v-for="(value, index) in chartData.companiesGrowth" :key="index" class="flex-1 flex flex-col items-center">
                        <div 
                            class="w-full bg-gradient-to-t from-purple-500 to-purple-400 rounded-t-lg hover:from-purple-600 hover:to-purple-500 transition-all cursor-pointer"
                            :style="`height: ${(value / Math.max(...chartData.companiesGrowth)) * 100}%`"
                            :title="`${value} companies`"
                        ></div>
                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][index] }}</span>
                    </div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-4 text-center">New companies registered this week</p>
            </div>

            <!-- Reports Overview Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
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

            <!-- Incidents Status Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
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

        <!-- Recent Activities -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activities</h3>
            <div class="space-y-4">
                <div v-for="(activity, index) in recentActivities" :key="index" class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div :class="{
                        'bg-blue-100 dark:bg-blue-900': activity.type === 'company',
                        'bg-green-100 dark:bg-green-900': activity.type === 'employee',
                        'bg-purple-100 dark:bg-purple-900': activity.type === 'sector',
                        'bg-orange-100 dark:bg-orange-900': activity.type === 'industry',
                    }" class="p-2 rounded-lg">
                        <svg v-if="activity.type === 'company'" class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <svg v-else-if="activity.type === 'employee'" class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <svg v-else-if="activity.type === 'sector'" class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z" />
                        </svg>
                        <svg v-else class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ activity.action }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ activity.name }}</p>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ activity.time }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
