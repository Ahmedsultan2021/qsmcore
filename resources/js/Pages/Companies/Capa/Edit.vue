<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    capa: Object,
    departments: Array,
    employees: Array,
});

const navs = computed(() => [
    { name: "Dashboard", linkName: "companies.dashboard" },
    { name: "CAPA Management", linkName: "companies.capa.index" },
    { name: props.capa?.title || "CAPA", linkName: "companies.capa.show", param: props.capa?.id ? { capa: props.capa.id } : undefined },
    { name: "Edit", linkName: "companies.capa.edit", param: props.capa?.id ? { capa: props.capa.id } : undefined },
]);

const form = useForm({
    title: props.capa?.title || "",
    type: props.capa?.type || "corrective",
    status: props.capa?.status || "open",
    priority: props.capa?.priority || "medium",
    department_id: props.capa?.department_id || "",
    assigned_to: props.capa?.assigned_to || "",
    due_date: props.capa?.due_date || "",
    description: props.capa?.description || "",
    root_cause: props.capa?.root_cause || "",
    corrective_action: props.capa?.corrective_action || "",
    preventive_action: props.capa?.preventive_action || "",
});

const submit = () => {
    form.put(route("companies.capa.update", props.capa.id));
};
</script>

<template>
    <Head :title="`Edit CAPA`" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            title="Edit CAPA"
            :showButton="false"
            :addSearchInput="false"
        />

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mt-6">
            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input
                        v-model="form.title"
                        type="text"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                    <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                        <select
                            v-model="form.type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="corrective">Corrective</option>
                            <option value="preventive">Preventive</option>
                        </select>
                        <div v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select
                            v-model="form.status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="open">Open</option>
                            <option value="in_progress">In progress</option>
                            <option value="closed">Closed</option>
                        </select>
                        <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority</label>
                        <select
                            v-model="form.priority"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                        <div v-if="form.errors.priority" class="mt-1 text-sm text-red-600">{{ form.errors.priority }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Department</label>
                        <select
                            v-model="form.department_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="">None</option>
                            <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                        </select>
                        <div v-if="form.errors.department_id" class="mt-1 text-sm text-red-600">{{ form.errors.department_id }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assignee</label>
                        <select
                            v-model="form.assigned_to"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="">Unassigned</option>
                            <option v-for="e in employees" :key="e.id" :value="e.id">{{ e.fname }} {{ e.lname }}</option>
                        </select>
                        <div v-if="form.errors.assigned_to" class="mt-1 text-sm text-red-600">{{ form.errors.assigned_to }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Due date</label>
                        <input
                            v-model="form.due_date"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                        <div v-if="form.errors.due_date" class="mt-1 text-sm text-red-600">{{ form.errors.due_date }}</div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea
                        v-model="form.description"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    ></textarea>
                    <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Root cause</label>
                    <textarea
                        v-model="form.root_cause"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    ></textarea>
                    <div v-if="form.errors.root_cause" class="mt-1 text-sm text-red-600">{{ form.errors.root_cause }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Corrective action</label>
                    <textarea
                        v-model="form.corrective_action"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    ></textarea>
                    <div v-if="form.errors.corrective_action" class="mt-1 text-sm text-red-600">{{ form.errors.corrective_action }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preventive action</label>
                    <textarea
                        v-model="form.preventive_action"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    ></textarea>
                    <div v-if="form.errors.preventive_action" class="mt-1 text-sm text-red-600">{{ form.errors.preventive_action }}</div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <Link
                        :href="route('companies.capa.show', capa.id)"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        {{ form.processing ? "Saving..." : "Save changes" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

