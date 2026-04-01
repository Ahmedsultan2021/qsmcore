<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    capa: Object,
});

const navs = computed(() => [
    { name: "Dashboard", linkName: "companies.dashboard" },
    { name: "CAPA Management", linkName: "companies.capa.index" },
    { name: props.capa?.title || "CAPA", linkName: "companies.capa.show", param: { capa: props.capa.id } },
]);

const deleteCapa = () => {
    if (confirm("Are you sure you want to delete this CAPA?")) {
        router.delete(route("companies.capa.destroy", props.capa.id));
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
    <Head :title="capa.title" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            :title="capa.title"
            :showButton="false"
            :addSearchInput="false"
        />

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mt-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span :class="['px-2 py-1 text-xs font-semibold rounded-full', statusPill(capa.status)]">
                        {{ capa.status === 'in_progress' ? 'In progress' : (capa.status || '').charAt(0).toUpperCase() + (capa.status || '').slice(1) }}
                    </span>
                    <span :class="['px-2 py-1 text-xs font-semibold rounded-full', priorityPill(capa.priority)]">
                        {{ (capa.priority || '').charAt(0).toUpperCase() + (capa.priority || '').slice(1) }}
                    </span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        {{ (capa.type || '-').charAt(0).toUpperCase() + (capa.type || '-').slice(1) }}
                    </span>
                    <span v-if="capa.is_overdue" class="px-2 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200">
                        Overdue
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <Link
                        :href="route('companies.capa.edit', capa.id)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    >
                        Edit
                    </Link>
                    <button
                        type="button"
                        @click="deleteCapa"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
            </div>

            <dl class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Department</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ capa.department?.name || "-" }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Assignee</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ capa.assignee ? `${capa.assignee.fname} ${capa.assignee.lname}` : "-" }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Due date</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ capa.due_date ? new Date(capa.due_date).toLocaleDateString() : "-" }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Closed at</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ capa.closed_at ? new Date(capa.closed_at).toLocaleString() : "-" }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ capa.description || "-" }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Root cause</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ capa.root_cause || "-" }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Corrective action</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ capa.corrective_action || "-" }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Preventive action</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ capa.preventive_action || "-" }}</dd>
                </div>
            </dl>

            <div class="mt-8">
                <Link :href="route('companies.capa.index')" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                    ← Back to CAPA list
                </Link>
            </div>
        </div>
    </div>
</template>

