<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    department: Object,
    reportsCount: Number,
    reportsByStatus: Object,
    formsStats: Object,
    functionsStats: Object,
});

const deleteDepartment = () => {
    if (confirm("Are you sure you want to delete this department?")) {
        router.delete(route("companies.departments.destroy", props.department.id));
    }
};
</script>

<template>
    <Head title="Department Details" />

    <div class="p-6">
        <div class="mb-6">
            <Link
                :href="route('companies.departments.index')"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
            >
                ← Back to Departments
            </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ department.name }}
                    </h1>
                    <div class="mt-4 space-y-2">
                        <div v-if="department.description" class="text-sm text-gray-600 dark:text-gray-400">
                            <p class="font-medium text-gray-700 dark:text-gray-300 mb-1">Description:</p>
                            <p>{{ department.description }}</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div v-if="department.manager_name" class="text-sm">
                                <p class="font-medium text-gray-700 dark:text-gray-300">Manager:</p>
                                <p class="text-gray-600 dark:text-gray-400">{{ department.manager_name }}</p>
                            </div>
                            <div v-if="department.email" class="text-sm">
                                <p class="font-medium text-gray-700 dark:text-gray-300">Email:</p>
                                <p class="text-gray-600 dark:text-gray-400">{{ department.email }}</p>
                            </div>
                            <div v-if="department.phone" class="text-sm">
                                <p class="font-medium text-gray-700 dark:text-gray-300">Phone:</p>
                                <p class="text-gray-600 dark:text-gray-400">{{ department.phone }}</p>
                            </div>
                            <div class="text-sm">
                                <p class="font-medium text-gray-700 dark:text-gray-300">Company:</p>
                                <p class="text-gray-600 dark:text-gray-400">{{ department.company?.name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <Link
                        :href="route('companies.departments.reports.index', department.id)"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                    >
                        View Reports
                    </Link>
                    <Link
                        :href="route('companies.departments.edit', department.id)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    >
                        Edit
                    </Link>
                    <button
                        @click="deleteDepartment"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Reports Statistics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Reports Overview</h2>
                    <Link
                        :href="route('companies.departments.reports.index', department.id)"
                        class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400"
                    >
                        View All
                    </Link>
                </div>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Reports</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ reportsCount }}</span>
                        </div>
                    </div>
                    <div v-if="reportsByStatus">
                        <div v-for="(count, status) in reportsByStatus" :key="status" class="mb-2">
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ status }}</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ count }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                <div 
                                    class="bg-blue-600 h-2 rounded-full" 
                                    :style="`width: ${(count / reportsCount) * 100}%`"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Forms Statistics -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Forms Statistics</h2>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Forms</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ formsStats.total }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-blue-600 h-2.5 rounded-full" :style="`width: ${(formsStats.completed / formsStats.total) * 100}%`"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ formsStats.completed }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Completed</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ formsStats.in_progress }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">In Progress</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ formsStats.pending }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Pending</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Functions Statistics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Audits & Inspections -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Audits & Inspections</h2>
                    <Link
                        :href="route('companies.audits.index')"
                        class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400"
                    >
                        View All
                    </Link>
                </div>
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ functionsStats.audits.total }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Completed</span>
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ functionsStats.audits.completed }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div 
                                class="bg-green-600 h-2 rounded-full" 
                                :style="`width: ${functionsStats.audits.total > 0 ? (functionsStats.audits.completed / functionsStats.audits.total) * 100 : 0}%`"
                            ></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">In Progress</span>
                            <span class="text-sm font-medium text-yellow-600 dark:text-yellow-400">{{ functionsStats.audits.in_progress }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Pending</span>
                            <span class="text-sm font-medium text-red-600 dark:text-red-400">{{ functionsStats.audits.pending }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance & KPIs -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Performance & KPIs</h2>
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total KPIs</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ functionsStats.kpis.total }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">On Target</span>
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ functionsStats.kpis.on_target }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-green-600 h-2 rounded-full" :style="`width: ${(functionsStats.kpis.on_target / functionsStats.kpis.total) * 100}%`"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Above Target</span>
                            <span class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ functionsStats.kpis.above_target }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Below Target</span>
                            <span class="text-sm font-medium text-red-600 dark:text-red-400">{{ functionsStats.kpis.below_target }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Risk Register -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Risk Register</h2>
                    <Link
                        :href="route('companies.departments.risks.index', department.id)"
                        class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400"
                    >
                        View All
                    </Link>
                </div>
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Risks</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ functionsStats.risk_register.total }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Low</span>
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ functionsStats.risk_register.low }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-green-600 h-2 rounded-full" :style="`width: ${(functionsStats.risk_register.low / functionsStats.risk_register.total) * 100}%`"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Medium</span>
                            <span class="text-sm font-medium text-yellow-600 dark:text-yellow-400">{{ functionsStats.risk_register.medium }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-yellow-600 h-2 rounded-full" :style="`width: ${(functionsStats.risk_register.medium / functionsStats.risk_register.total) * 100}%`"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">High</span>
                            <span class="text-sm font-medium text-orange-600 dark:text-orange-400">{{ functionsStats.risk_register.high }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-orange-600 h-2 rounded-full" :style="`width: ${functionsStats.risk_register.total > 0 ? (functionsStats.risk_register.high / functionsStats.risk_register.total) * 100 : 0}%`"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Critical</span>
                            <span class="text-sm font-medium text-red-600 dark:text-red-400">{{ functionsStats.risk_register.critical }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-red-600 h-2 rounded-full" :style="`width: ${functionsStats.risk_register.total > 0 ? (functionsStats.risk_register.critical / functionsStats.risk_register.total) * 100 : 0}%`"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Action Button -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('companies.risks.create', { department_id: department.id })"
                        class="block w-full px-4 py-2 text-center text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        <i class="fa-solid fa-plus mr-2"></i>Add New Risk
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
