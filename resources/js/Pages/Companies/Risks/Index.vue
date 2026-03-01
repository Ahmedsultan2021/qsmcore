<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    risks: Object,
    departments: Array,
    filters: Object,
    categories: Object,
    statuses: Object,
});

const viewMode = ref('list');
const search = ref(props.filters?.search || '');
const selectedDepartment = ref(props.filters?.department_id || '');
const selectedStatus = ref(props.filters?.status || '');
const selectedCategory = ref(props.filters?.category || '');
const selectedRiskLevel = ref(props.filters?.risk_level || '');

const navs = computed(() => [
    { name: "Dashboard", linkName: "companies.dashboard" },
    { name: "Risk Register", linkName: "companies.risks.index" },
]);

const getRiskLevelColor = (level) => {
    const colors = {
        critical: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
        high: 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
        medium: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
        low: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
    };
    return colors[level] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const getRiskLevelIcon = (level) => {
    const icons = {
        critical: 'fa-circle-exclamation',
        high: 'fa-triangle-exclamation',
        medium: 'fa-circle-info',
        low: 'fa-circle-check',
    };
    return icons[level] || 'fa-circle';
};

const getStatusColor = (status) => {
    const colors = {
        identified: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
        assessed: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
        in_progress: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
        mitigated: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
        monitoring: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
        closed: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const applyFilters = () => {
    const params = {};
    if (search.value) params.search = search.value;
    if (selectedDepartment.value) params.department_id = selectedDepartment.value;
    if (selectedStatus.value) params.status = selectedStatus.value;
    if (selectedCategory.value) params.category = selectedCategory.value;
    if (selectedRiskLevel.value) params.risk_level = selectedRiskLevel.value;
    
    router.get(route('companies.risks.index'), params, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    search.value = '';
    selectedDepartment.value = '';
    selectedStatus.value = '';
    selectedCategory.value = '';
    selectedRiskLevel.value = '';
    router.get(route('companies.risks.index'));
};

const deleteRisk = (id) => {
    if (confirm("Are you sure you want to delete this risk?")) {
        router.delete(route("companies.risks.destroy", id));
    }
};

// Risk Matrix Data
const riskMatrix = computed(() => {
    const matrix = [];
    for (let impact = 5; impact >= 1; impact--) {
        const row = [];
        for (let likelihood = 1; likelihood <= 5; likelihood++) {
            const score = likelihood * impact;
            let level = 'low';
            if (score >= 15) level = 'critical';
            else if (score >= 10) level = 'high';
            else if (score >= 5) level = 'medium';
            
            const risksInCell = props.risks.data.filter(r => 
                r.likelihood === likelihood && r.impact === impact
            );
            
            row.push({
                likelihood,
                impact,
                score,
                level,
                count: risksInCell.length,
                risks: risksInCell,
            });
        }
        matrix.push(row);
    }
    return matrix;
});

watch([search], () => {
    // Debounce search
    const timeoutId = setTimeout(() => {
        applyFilters();
    }, 500);
    
    return () => clearTimeout(timeoutId);
});
</script>

<template>
    <Head title="Risk Register" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            title="Risk Register"
            navLinkName="companies.risks.create"
            NavLinkText="Add New Risk"
            :showButton="true"
            :addSearchInput="false"
        />

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Search
                    </label>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search risks..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                    />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Department
                    </label>
                    <select
                        v-model="selectedDepartment"
                        @change="applyFilters"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                    >
                        <option value="">All Departments</option>
                        <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                            {{ dept.name }}
                        </option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status
                    </label>
                    <select
                        v-model="selectedStatus"
                        @change="applyFilters"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                    >
                        <option value="">All Statuses</option>
                        <option v-for="(label, value) in statuses" :key="value" :value="value">
                            {{ label }}
                        </option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Category
                    </label>
                    <select
                        v-model="selectedCategory"
                        @change="applyFilters"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                    >
                        <option value="">All Categories</option>
                        <option v-for="(label, value) in categories" :key="value" :value="value">
                            {{ label }}
                        </option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Risk Level
                    </label>
                    <select
                        v-model="selectedRiskLevel"
                        @change="applyFilters"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                    >
                        <option value="">All Levels</option>
                        <option value="critical">Critical</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-4 flex justify-end">
                <button
                    @click="clearFilters"
                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                >
                    <i class="fa-solid fa-xmark mr-2"></i>Clear Filters
                </button>
            </div>
        </div>

        <!-- View Mode Toggle -->
        <div class="flex justify-end mt-6">
            <div class="inline-flex rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-1">
                <button
                    @click="viewMode = 'list'"
                    :class="[
                        'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                        viewMode === 'list'
                            ? 'bg-blue-600 text-white'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                    ]"
                >
                    <i class="fa-solid fa-list mr-2"></i>List
                </button>
                <button
                    @click="viewMode = 'matrix'"
                    :class="[
                        'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                        viewMode === 'matrix'
                            ? 'bg-blue-600 text-white'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                    ]"
                >
                    <i class="fa-solid fa-table-cells mr-2"></i>Matrix
                </button>
            </div>
        </div>

        <!-- List View -->
        <div v-if="viewMode === 'list'" class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mt-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Risk
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Department
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Category
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Score
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Level
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="risk in risks.data" :key="risk.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mt-1">
                                        <i :class="`fa-solid ${getRiskLevelIcon(risk.risk_level)} text-${risk.risk_level === 'critical' ? 'red' : risk.risk_level === 'high' ? 'orange' : risk.risk_level === 'medium' ? 'yellow' : 'green'}-500`"></i>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ risk.title }}
                                        </div>
                                        <div v-if="risk.risk_code" class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ risk.risk_code }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                            {{ risk.description }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                {{ risk.department?.name || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    {{ categories[risk.category] || risk.category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ risk.likelihood * risk.impact }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ risk.likelihood }}×{{ risk.impact }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span :class="`inline-flex px-3 py-1 text-xs font-semibold rounded-full ${getRiskLevelColor(risk.risk_level)}`">
                                    {{ risk.risk_level.toUpperCase() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="`inline-flex px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(risk.status)}`">
                                    {{ statuses[risk.status] || risk.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <Link
                                        :href="route('companies.risks.show', risk.id)"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
                                    >
                                        <i class="fa-solid fa-eye mr-1.5"></i>View
                                    </Link>
                                    <Link
                                        :href="route('companies.risks.edit', risk.id)"
                                        class="inline-flex items-center px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors"
                                    >
                                        <i class="fa-solid fa-pencil mr-1.5"></i>Edit
                                    </Link>
                                    <button
                                        @click="deleteRisk(risk.id)"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
                                    >
                                        <i class="fa-solid fa-trash mr-1.5"></i>Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-if="!risks.data || risks.data.length === 0" class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full mb-4">
                    <i class="fa-solid fa-triangle-exclamation text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No risks found</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Get started by creating your first risk entry.</p>
                <Link
                    :href="route('companies.risks.create')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    <i class="fa-solid fa-plus mr-2"></i>Add New Risk
                </Link>
            </div>
        </div>

        <!-- Matrix View -->
        <div v-if="viewMode === 'matrix'" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Risk Matrix</h3>
            
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full">
                    <!-- Y-axis label -->
                    <div class="flex items-center mb-2">
                        <div class="w-24 text-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 transform -rotate-90 inline-block">
                                Impact →
                            </span>
                        </div>
                        <div class="flex-1"></div>
                    </div>
                    
                    <div class="flex">
                        <!-- Y-axis values -->
                        <div class="w-24 flex flex-col-reverse justify-between pr-2">
                            <div v-for="i in 5" :key="i" class="h-24 flex items-center justify-end">
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ i }}</span>
                            </div>
                        </div>
                        
                        <!-- Matrix cells -->
                        <div class="flex-1">
                            <div v-for="(row, rowIndex) in riskMatrix" :key="rowIndex" class="flex">
                                <div
                                    v-for="(cell, cellIndex) in row"
                                    :key="cellIndex"
                                    :class="[
                                        'w-24 h-24 border border-gray-300 dark:border-gray-600 flex flex-col items-center justify-center p-2 cursor-pointer hover:opacity-80 transition-opacity',
                                        cell.level === 'critical' ? 'bg-red-200 dark:bg-red-900/40' : 
                                        cell.level === 'high' ? 'bg-orange-200 dark:bg-orange-900/40' : 
                                        cell.level === 'medium' ? 'bg-yellow-200 dark:bg-yellow-900/40' : 
                                        'bg-green-200 dark:bg-green-900/40'
                                    ]"
                                    :title="`Likelihood: ${cell.likelihood}, Impact: ${cell.impact}, Score: ${cell.score}`"
                                >
                                    <div class="text-2xl font-bold text-gray-800 dark:text-white">
                                        {{ cell.count }}
                                    </div>
                                    <div class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                        {{ cell.count === 1 ? 'risk' : 'risks' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- X-axis values -->
                    <div class="flex mt-2">
                        <div class="w-24"></div>
                        <div class="flex-1 flex justify-around">
                            <div v-for="i in 5" :key="i" class="w-24 text-center">
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ i }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- X-axis label -->
                    <div class="flex mt-2">
                        <div class="w-24"></div>
                        <div class="flex-1 text-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">← Likelihood</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Legend -->
            <div class="mt-6 flex items-center justify-center space-x-6">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-200 dark:bg-green-900/40 border border-gray-300 dark:border-gray-600 mr-2"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Low (1-4)</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-yellow-200 dark:bg-yellow-900/40 border border-gray-300 dark:border-gray-600 mr-2"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Medium (5-9)</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-orange-200 dark:bg-orange-900/40 border border-gray-300 dark:border-gray-600 mr-2"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">High (10-14)</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-red-200 dark:bg-red-900/40 border border-gray-300 dark:border-gray-600 mr-2"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Critical (15-25)</span>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="viewMode === 'list' && risks.links && risks.links.length > 3" class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    <Link
                        v-if="risks.prev_page_url"
                        :href="risks.prev_page_url"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                        Previous
                    </Link>
                    <Link
                        v-if="risks.next_page_url"
                        :href="risks.next_page_url"
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                        Next
                    </Link>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Showing
                            <span class="font-medium">{{ risks.from }}</span>
                            to
                            <span class="font-medium">{{ risks.to }}</span>
                            of
                            <span class="font-medium">{{ risks.total }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <Link
                                v-for="link in risks.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                    link.active
                                        ? 'z-10 bg-blue-600 border-blue-600 text-white'
                                        : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700',
                                    link.url ? '' : 'cursor-not-allowed opacity-50'
                                ]"
                            >
                                <span v-if="link.label.includes('Previous')"><i class="fa-solid fa-chevron-left"></i></span>
                                <span v-else-if="link.label.includes('Next')"><i class="fa-solid fa-chevron-right"></i></span>
                                <span v-else>{{ link.label }}</span>
                            </Link>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
