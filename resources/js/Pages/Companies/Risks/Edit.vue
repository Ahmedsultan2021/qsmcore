<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    risk: Object,
    departments: Array,
    employees: Array,
    categories: Object,
    treatmentStrategies: Object,
    statuses: Object,
    likelihoodLevels: Object,
    impactLevels: Object,
});

const form = useForm({
    department_id: props.risk.department_id || "",
    risk_owner_id: props.risk.risk_owner_id || null,
    risk_code: props.risk.risk_code || "",
    title: props.risk.title || "",
    description: props.risk.description || "",
    category: props.risk.category || "",
    likelihood: props.risk.likelihood || 3,
    impact: props.risk.impact || 3,
    current_controls: props.risk.current_controls || "",
    treatment_strategy: props.risk.treatment_strategy || "",
    action_plan: props.risk.action_plan || "",
    target_date: props.risk.target_date || "",
    status: props.risk.status || "identified",
    date_identified: props.risk.date_identified || "",
    last_review_date: props.risk.last_review_date || new Date().toISOString().split('T')[0],
    residual_likelihood: props.risk.residual_likelihood || null,
    residual_impact: props.risk.residual_impact || null,
});

const submit = () => {
    form.put(route("companies.risks.update", props.risk.id));
};

const navs = computed(() => [
    { name: "Dashboard", linkName: "companies.dashboard" },
    { name: "Risk Register", linkName: "companies.risks.index" },
    { name: props.risk.title, linkName: "companies.risks.show", param: { risk: props.risk.id } },
    { name: "Edit", linkName: "companies.risks.edit", param: { risk: props.risk.id } },
]);

const riskScore = computed(() => form.likelihood * form.impact);

const riskLevel = computed(() => {
    const score = riskScore.value;
    if (score >= 15) return { level: 'Critical', color: 'text-red-600 dark:text-red-400' };
    if (score >= 10) return { level: 'High', color: 'text-orange-600 dark:text-orange-400' };
    if (score >= 5) return { level: 'Medium', color: 'text-yellow-600 dark:text-yellow-400' };
    return { level: 'Low', color: 'text-green-600 dark:text-green-400' };
});

const residualRiskScore = computed(() => {
    if (!form.residual_likelihood || !form.residual_impact) return null;
    return form.residual_likelihood * form.residual_impact;
});

const residualRiskLevel = computed(() => {
    const score = residualRiskScore.value;
    if (!score) return null;
    if (score >= 15) return { level: 'Critical', color: 'text-red-600 dark:text-red-400' };
    if (score >= 10) return { level: 'High', color: 'text-orange-600 dark:text-orange-400' };
    if (score >= 5) return { level: 'Medium', color: 'text-yellow-600 dark:text-yellow-400' };
    return { level: 'Low', color: 'text-green-600 dark:text-green-400' };
});
</script>

<template>
    <Head title="Edit Risk" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            title="Edit Risk"
            :showButton="false"
            :addSearchInput="false"
        />

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mt-6">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Risk Identification Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                        <i class="fa-solid fa-circle-info mr-2 text-blue-600"></i>Risk Identification
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Department <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="department_id"
                                v-model="form.department_id"
                                required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                                <option value="">Select Department</option>
                                <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                                    {{ dept.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.department_id" class="mt-1 text-sm text-red-600">{{ form.errors.department_id }}</div>
                        </div>

                        <div>
                            <label for="risk_owner_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Risk Owner
                            </label>
                            <select
                                id="risk_owner_id"
                                v-model="form.risk_owner_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                                <option :value="null">Select Risk Owner</option>
                                <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                                    {{ emp.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.risk_owner_id" class="mt-1 text-sm text-red-600">{{ form.errors.risk_owner_id }}</div>
                        </div>

                        <div>
                            <label for="risk_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Risk Code
                            </label>
                            <input
                                id="risk_code"
                                v-model="form.risk_code"
                                type="text"
                                placeholder="e.g., RISK-001"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            />
                            <div v-if="form.errors.risk_code" class="mt-1 text-sm text-red-600">{{ form.errors.risk_code }}</div>
                        </div>

                        <div>
                            <label for="date_identified" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Date Identified
                            </label>
                            <input
                                id="date_identified"
                                v-model="form.date_identified"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            />
                            <div v-if="form.errors.date_identified" class="mt-1 text-sm text-red-600">{{ form.errors.date_identified }}</div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Risk Title <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            required
                            placeholder="Brief title describing the risk"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        />
                        <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</div>
                    </div>

                    <div class="mt-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Risk Description <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            required
                            rows="3"
                            placeholder="Detailed description of the risk and its potential consequences"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        ></textarea>
                        <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</div>
                    </div>

                    <div class="mt-4">
                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="category"
                            v-model="form.category"
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">Select Category</option>
                            <option v-for="(label, value) in categories" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                        <div v-if="form.errors.category" class="mt-1 text-sm text-red-600">{{ form.errors.category }}</div>
                    </div>
                </div>

                <!-- Risk Assessment Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                        <i class="fa-solid fa-chart-line mr-2 text-purple-600"></i>Risk Assessment
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="likelihood" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Likelihood <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="likelihood"
                                v-model.number="form.likelihood"
                                required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                                <option v-for="(label, value) in likelihoodLevels" :key="value" :value="parseInt(value)">
                                    {{ value }} - {{ label }}
                                </option>
                            </select>
                            <div v-if="form.errors.likelihood" class="mt-1 text-sm text-red-600">{{ form.errors.likelihood }}</div>
                        </div>

                        <div>
                            <label for="impact" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Impact <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="impact"
                                v-model.number="form.impact"
                                required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                                <option v-for="(label, value) in impactLevels" :key="value" :value="parseInt(value)">
                                    {{ value }} - {{ label }}
                                </option>
                            </select>
                            <div v-if="form.errors.impact" class="mt-1 text-sm text-red-600">{{ form.errors.impact }}</div>
                        </div>
                    </div>

                    <!-- Risk Score Display -->
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Inherent Risk Score</p>
                                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ riskScore }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ form.likelihood }} × {{ form.impact }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Risk Level</p>
                                <p :class="`text-2xl font-bold ${riskLevel.color}`">{{ riskLevel.level }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk Treatment Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                        <i class="fa-solid fa-shield-halved mr-2 text-green-600"></i>Risk Treatment
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="treatment_strategy" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Treatment Strategy
                            </label>
                            <select
                                id="treatment_strategy"
                                v-model="form.treatment_strategy"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                                <option value="">Select Strategy</option>
                                <option v-for="(label, value) in treatmentStrategies" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                            <div v-if="form.errors.treatment_strategy" class="mt-1 text-sm text-red-600">{{ form.errors.treatment_strategy }}</div>
                        </div>

                        <div>
                            <label for="target_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Target Completion Date
                            </label>
                            <input
                                id="target_date"
                                v-model="form.target_date"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            />
                            <div v-if="form.errors.target_date" class="mt-1 text-sm text-red-600">{{ form.errors.target_date }}</div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="current_controls" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Current Controls/Mitigation Measures
                        </label>
                        <textarea
                            id="current_controls"
                            v-model="form.current_controls"
                            rows="3"
                            placeholder="Existing controls and measures in place to manage this risk"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        ></textarea>
                        <div v-if="form.errors.current_controls" class="mt-1 text-sm text-red-600">{{ form.errors.current_controls }}</div>
                    </div>

                    <div class="mt-4">
                        <label for="action_plan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Action Plan
                        </label>
                        <textarea
                            id="action_plan"
                            v-model="form.action_plan"
                            rows="3"
                            placeholder="Steps to be taken to further mitigate or manage this risk"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        ></textarea>
                        <div v-if="form.errors.action_plan" class="mt-1 text-sm text-red-600">{{ form.errors.action_plan }}</div>
                    </div>

                    <div class="mt-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="status"
                            v-model="form.status"
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option v-for="(label, value) in statuses" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                        <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</div>
                    </div>
                </div>

                <!-- Residual Risk Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                        <i class="fa-solid fa-gauge mr-2 text-indigo-600"></i>Residual Risk (After Mitigation)
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="residual_likelihood" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Residual Likelihood
                            </label>
                            <select
                                id="residual_likelihood"
                                v-model.number="form.residual_likelihood"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                                <option :value="null">Not assessed yet</option>
                                <option v-for="(label, value) in likelihoodLevels" :key="value" :value="parseInt(value)">
                                    {{ value }} - {{ label }}
                                </option>
                            </select>
                            <div v-if="form.errors.residual_likelihood" class="mt-1 text-sm text-red-600">{{ form.errors.residual_likelihood }}</div>
                        </div>

                        <div>
                            <label for="residual_impact" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Residual Impact
                            </label>
                            <select
                                id="residual_impact"
                                v-model.number="form.residual_impact"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                                <option :value="null">Not assessed yet</option>
                                <option v-for="(label, value) in impactLevels" :key="value" :value="parseInt(value)">
                                    {{ value }} - {{ label }}
                                </option>
                            </select>
                            <div v-if="form.errors.residual_impact" class="mt-1 text-sm text-red-600">{{ form.errors.residual_impact }}</div>
                        </div>
                    </div>

                    <!-- Residual Risk Score Display -->
                    <div v-if="residualRiskScore" class="mt-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Residual Risk Score</p>
                                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ residualRiskScore }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ form.residual_likelihood }} × {{ form.residual_impact }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Risk Level</p>
                                <p :class="`text-2xl font-bold ${residualRiskLevel.color}`">{{ residualRiskLevel.level }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('companies.risks.index')"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 transition-colors"
                    >
                        {{ form.processing ? "Updating..." : "Update Risk" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
