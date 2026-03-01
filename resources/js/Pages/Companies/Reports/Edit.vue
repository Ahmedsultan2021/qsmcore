<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    department: Object,
    report: Object,
    forms: Array,
});

const form = useForm({
    title: props.report.title || "",
    description: props.report.description || "",
    status: props.report.status || "draft",
    report_date: props.report.report_date ? new Date(props.report.report_date).toISOString().split('T')[0] : new Date().toISOString().split('T')[0],
    forms: props.report.forms ? props.report.forms.map(f => f.id) : [],
});

const submit = () => {
    form.put(route("companies.departments.reports.update", [props.department.id, props.report.id]));
};
</script>

<template>
    <Head title="Edit Report" />

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
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Report</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Department: {{ department.name }}</p>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Report Title <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="title"
                        v-model="form.title"
                        type="text"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                    <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</div>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    ></textarea>
                    <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="status"
                            v-model="form.status"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="draft">Draft</option>
                            <option value="submitted">Submitted</option>
                            <option value="reviewed">Reviewed</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                        <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</div>
                    </div>

                    <div>
                        <label for="report_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Report Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="report_date"
                            v-model="form.report_date"
                            type="date"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                        <div v-if="form.errors.report_date" class="mt-1 text-sm text-red-600">{{ form.errors.report_date }}</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="forms" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Attach Forms
                    </label>
                    <select
                        id="forms"
                        v-model="form.forms"
                        multiple
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option v-for="formOption in forms" :key="formOption.id" :value="formOption.id">
                            {{ formOption.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.forms" class="mt-1 text-sm text-red-600">{{ form.errors.forms }}</div>
                    <p class="mt-1 text-xs text-gray-500">Hold Ctrl (or Cmd on Mac) to select multiple forms</p>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <Link
                        :href="route('companies.departments.reports.index', department.id)"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        {{ form.processing ? "Updating..." : "Update Report" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

