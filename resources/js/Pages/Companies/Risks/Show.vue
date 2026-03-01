<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    risk: Object,
    categories: Object,
    treatmentStrategies: Object,
    statuses: Object,
    likelihoodLevels: Object,
    impactLevels: Object,
});

const getRiskLevelColor = (score) => {
    if (score >= 15) return { bg: 'bg-red-100 dark:bg-red-900/30', text: 'text-red-800 dark:text-red-300', level: 'Critical' };
    if (score >= 10) return { bg: 'bg-orange-100 dark:bg-orange-900/30', text: 'text-orange-800 dark:text-orange-300', level: 'High' };
    if (score >= 5) return { bg: 'bg-yellow-100 dark:bg-yellow-900/30', text: 'text-yellow-800 dark:text-yellow-300', level: 'Medium' };
    return { bg: 'bg-green-100 dark:bg-green-900/30', text: 'text-green-800 dark:text-green-300', level: 'Low' };
};

const riskScore = computed(() => props.risk.likelihood * props.risk.impact);
const riskLevel = computed(() => getRiskLevelColor(riskScore.value));

const residualRiskScore = computed(() => {
    if (!props.risk.residual_likelihood || !props.risk.residual_impact) return null;
    return props.risk.residual_likelihood * props.risk.residual_impact;
});

const residualRiskLevel = computed(() => {
    if (!residualRiskScore.value) return null;
    return getRiskLevelColor(residualRiskScore.value);
});

const deleteRisk = () => {
    if (confirm("Are you sure you want to delete this risk?")) {
        router.delete(route("companies.risks.destroy", props.risk.id));
    }
};

const isOverdue = computed(() => {
    if (!props.risk.target_date) return false;
    if (['closed', 'mitigated'].includes(props.risk.status)) return false;
    return new Date(props.risk.target_date) < new Date();
});
</script>

<template>
    <Head :title="risk.title" />

    <div class="p-6">
        <!-- Header with Breadcrumb -->
        <div class="mb-6">
            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-2">
                <Link :href="route('companies.dashboard')" class="hover:text-blue-600 dark:hover:text-blue-400">
                    Dashboard
                </Link>
                <i class="fa-solid fa-chevron-right mx-2 text-xs"></i>
                <Link :href="route('companies.risks.index')" class="hover:text-blue-600 dark:hover:text-blue-400">
                    Risk Register
                </Link>
                <i class="fa-solid fa-chevron-right mx-2 text-xs"></i>
                <span class="text-gray-900 dark:text-white">{{ risk.title }}</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ risk.title }}</h1>
                    <p v-if="risk.risk_code" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Risk Code: {{ risk.risk_code }}
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <Link
                        :href="route('companies.risks.edit', risk.id)"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                    >
                        <i class="fa-solid fa-pencil mr-2"></i>Edit
                    </Link>
                    <button
                        @click="deleteRisk"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        <i class="fa-solid fa-trash mr-2"></i>Delete
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Risk Overview Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fa-solid fa-circle-info mr-2 text-blue-600"></i>Risk Overview
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Description</label>
                            <p class="text-gray-900 dark:text-white">{{ risk.description }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Category</label>
                                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    {{ categories[risk.category] || risk.category }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Department</label>
                                <Link
                                    :href="route('companies.departments.show', risk.department.id)"
                                    class="text-blue-600 dark:text-blue-400 hover:underline"
                                >
                                    {{ risk.department.name }}
                                </Link>
                            </div>
                        </div>

                        <div v-if="risk.risk_owner" class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Risk Owner</label>
                                <p class="text-gray-900 dark:text-white">
                                    <i class="fa-solid fa-user mr-2 text-gray-400"></i>
                                    {{ risk.risk_owner?.fname }} {{ risk.risk_owner?.lname }}
                                </p>
                            </div>

                            <div v-if="risk.date_identified">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Date Identified</label>
                                <p class="text-gray-900 dark:text-white">
                                    {{ new Date(risk.date_identified).toLocaleDateString() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk Assessment Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fa-solid fa-chart-line mr-2 text-purple-600"></i>Risk Assessment
                    </h2>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Inherent Risk -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-3">Inherent Risk</h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Likelihood</label>
                                    <div class="flex items-center">
                                        <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                            <div :style="`width: ${risk.likelihood * 20}%`" class="bg-blue-600 h-2 rounded-full"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ risk.likelihood }}/5
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ likelihoodLevels[risk.likelihood] }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Impact</label>
                                    <div class="flex items-center">
                                        <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                            <div :style="`width: ${risk.impact * 20}%`" class="bg-purple-600 h-2 rounded-full"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ risk.impact }}/5
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ impactLevels[risk.impact] }}
                                    </p>
                                </div>

                                <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Risk Score</span>
                                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ riskScore }}</span>
                                    </div>
                                    <div class="mt-2">
                                        <span :class="`inline-flex px-3 py-1 text-sm font-semibold rounded-full ${riskLevel.bg} ${riskLevel.text}`">
                                            {{ riskLevel.level }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Residual Risk -->
                        <div v-if="residualRiskScore" class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-3">Residual Risk (After Mitigation)</h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Likelihood</label>
                                    <div class="flex items-center">
                                        <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                            <div :style="`width: ${risk.residual_likelihood * 20}%`" class="bg-blue-600 h-2 rounded-full"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ risk.residual_likelihood }}/5
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ likelihoodLevels[risk.residual_likelihood] }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Impact</label>
                                    <div class="flex items-center">
                                        <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                            <div :style="`width: ${risk.residual_impact * 20}%`" class="bg-purple-600 h-2 rounded-full"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ risk.residual_impact }}/5
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ impactLevels[risk.residual_impact] }}
                                    </p>
                                </div>

                                <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Risk Score</span>
                                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ residualRiskScore }}</span>
                                    </div>
                                    <div class="mt-2">
                                        <span :class="`inline-flex px-3 py-1 text-sm font-semibold rounded-full ${residualRiskLevel.bg} ${residualRiskLevel.text}`">
                                            {{ residualRiskLevel.level }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                            <div class="text-center">
                                <i class="fa-solid fa-gauge text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Residual risk not yet assessed</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk Treatment Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fa-solid fa-shield-halved mr-2 text-green-600"></i>Risk Treatment
                    </h2>
                    
                    <div class="space-y-4">
                        <div v-if="risk.treatment_strategy">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Treatment Strategy</label>
                            <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                {{ treatmentStrategies[risk.treatment_strategy] || risk.treatment_strategy }}
                            </span>
                        </div>

                        <div v-if="risk.current_controls">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Current Controls</label>
                            <p class="text-gray-900 dark:text-white whitespace-pre-line">{{ risk.current_controls }}</p>
                        </div>

                        <div v-if="risk.action_plan">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Action Plan</label>
                            <p class="text-gray-900 dark:text-white whitespace-pre-line">{{ risk.action_plan }}</p>
                        </div>

                        <div v-if="risk.target_date" class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Target Date</label>
                                <p :class="[
                                    'font-medium',
                                    isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white'
                                ]">
                                    <i :class="`fa-solid ${isOverdue ? 'fa-exclamation-triangle' : 'fa-calendar'} mr-2`"></i>
                                    {{ new Date(risk.target_date).toLocaleDateString() }}
                                    <span v-if="isOverdue" class="ml-2 text-sm">(Overdue)</span>
                                </p>
                            </div>

                            <div v-if="risk.last_review_date">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Last Reviewed</label>
                                <p class="text-gray-900 dark:text-white">
                                    {{ new Date(risk.last_review_date).toLocaleDateString() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Current Status</label>
                            <span :class="`inline-flex px-3 py-1.5 text-sm font-medium rounded-full ${
                                risk.status === 'closed' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' :
                                risk.status === 'mitigated' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
                                risk.status === 'in_progress' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' :
                                'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'
                            }`">
                                {{ statuses[risk.status] || risk.status }}
                            </span>
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Timestamps</label>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Created</span>
                                    <span class="text-gray-900 dark:text-white">{{ new Date(risk.created_at).toLocaleDateString() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Updated</span>
                                    <span class="text-gray-900 dark:text-white">{{ new Date(risk.updated_at).toLocaleDateString() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                    
                    <div class="space-y-2">
                        <Link
                            :href="route('companies.departments.show', risk.department.id)"
                            class="block w-full px-4 py-2 text-sm text-left text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                        >
                            <i class="fa-solid fa-building mr-2"></i>View Department
                        </Link>
                        <Link
                            :href="route('companies.risks.index', { department_id: risk.department_id })"
                            class="block w-full px-4 py-2 text-sm text-left text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                        >
                            <i class="fa-solid fa-list mr-2"></i>All Risks in Department
                        </Link>
                        <Link
                            :href="route('companies.risks.index', { category: risk.category })"
                            class="block w-full px-4 py-2 text-sm text-left text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                        >
                            <i class="fa-solid fa-tag mr-2"></i>Similar Risks
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
