<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    capas: Object,
    departments: Array,
    employees: Array,
    filters: Object,
});

const navs = computed(() => [
    { name: "Dashboard", linkName: "companies.dashboard" },
    { name: "CAPA Management", linkName: "companies.capa.index" },
]);

const filterForm = useForm({
    status: props.filters?.status || "",
    department_id: props.filters?.department_id || "",
    assigned_to: props.filters?.assigned_to || "",
    overdue: props.filters?.overdue ? true : false,
});

const applyFilters = () => {
    filterForm.get(route("companies.capa.index"), {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    filterForm.status = "";
    filterForm.department_id = "";
    filterForm.assigned_to = "";
    filterForm.overdue = false;
    applyFilters();
};

const deleteCapa = (capa) => {
    if (confirm("Are you sure you want to delete this CAPA?")) {
        router.delete(route("companies.capa.destroy", capa.id));
    }
};

const statusPill = (status) => {
    const colors = {
        open: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
        in_progress: "bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200",
        closed: "bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200",
    };
    return colors[status] || colors.open;
};

const priorityPill = (priority) => {
    const colors = {
        low: "bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-200",
        medium: "bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200",
        high: "bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200",
    };
    return colors[priority] || colors.medium;
};
</script>

<template>
    <Head title="CAPA Management" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            title="CAPA Management"
            navLinkName="companies.capa.create"
            NavLinkText="New CAPA"
            :showButton="true"
            :addSearchInput="false"
        />

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mt-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select
                        v-model="filterForm.status"
                        @change="applyFilters()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">All</option>
                        <option value="open">Open</option>
                        <option value="in_progress">In progress</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Department</label>
                    <select
                        v-model="filterForm.department_id"
                        @change="applyFilters()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">All Departments</option>
                        <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assignee</label>
                    <select
                        v-model="filterForm.assigned_to"
                        @change="applyFilters()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">All Employees</option>
                        <option v-for="e in employees" :key="e.id" :value="e.id">
                            {{ e.fname }} {{ e.lname }}
                        </option>
                    </select>
                </div>

                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        <input
                            type="checkbox"
                            v-model="filterForm.overdue"
                            @change="applyFilters()"
                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500"
                        />
                        Overdue only
                    </label>
                </div>

                <div class="flex items-end">
                    <button
                        type="button"
                        @click="clearFilters()"
                        class="w-full px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500"
                    >
                        Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- List -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mt-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Title
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Priority
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Department
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Assignee
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Due
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="c in capas.data" :key="c.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                <Link :href="route('companies.capa.show', c.id)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ c.title }}
                                </Link>
                                <div v-if="c.is_overdue" class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">
                                    Overdue
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ (c.type || '-').charAt(0).toUpperCase() + (c.type || '-').slice(1) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="['px-2 py-1 text-xs font-semibold rounded-full', statusPill(c.status)]">
                                    {{ c.status === 'in_progress' ? 'In progress' : (c.status || '').charAt(0).toUpperCase() + (c.status || '').slice(1) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="['px-2 py-1 text-xs font-semibold rounded-full', priorityPill(c.priority)]">
                                    {{ (c.priority || '').charAt(0).toUpperCase() + (c.priority || '').slice(1) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ c.department?.name || "-" }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ c.assignee ? `${c.assignee.fname} ${c.assignee.lname}` : "-" }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ c.due_date ? new Date(c.due_date).toLocaleDateString() : "-" }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link :href="route('companies.capa.show', c.id)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                    View
                                </Link>
                                <Link :href="route('companies.capa.edit', c.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                    Edit
                                </Link>
                                <button @click="deleteCapa(c)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="capas.links && capas.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link
                            v-if="capas.prev_page_url"
                            :href="capas.prev_page_url"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Previous
                        </Link>
                        <Link
                            v-if="capas.next_page_url"
                            :href="capas.next_page_url"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Next
                        </Link>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing
                                <span class="font-medium">{{ capas.from }}</span>
                                to
                                <span class="font-medium">{{ capas.to }}</span>
                                of
                                <span class="font-medium">{{ capas.total }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <template v-for="link in capas.links" :key="link.label">
                                    <Link
                                        v-if="link.url"
                                        :href="link.url"
                                        :class="[
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                            link.active ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                        ]"
                                    >
                                        <span v-html="link.label"></span>
                                    </Link>
                                    <span
                                        v-else
                                        class="relative inline-flex items-center px-4 py-2 border text-sm font-medium cursor-not-allowed opacity-50 bg-white border-gray-300 text-gray-500"
                                    >
                                        <span v-html="link.label"></span>
                                    </span>
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="!capas.data || capas.data.length === 0" class="p-10 text-center text-gray-600 dark:text-gray-300">
                No CAPAs found.
                <div class="mt-3">
                    <Link
                        :href="route('companies.capa.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Create your first CAPA
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

