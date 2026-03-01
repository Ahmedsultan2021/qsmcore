<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    audits: Array,
    reports: Array,
});

const calendarRef = ref(null);
const calendarOptions = ref({
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: "dayGridMonth",
    headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,dayGridWeek",
    },
    events: computed(() => {
        return props.audits.map((audit) => {
            const isComplete = audit.reports?.length === 0 || 
                audit.reports?.every((report) => report.general_report_status === "complete");
            
            return {
                id: audit.id,
                title: audit.name,
                start: audit.audit_date,
                backgroundColor: isComplete ? "#10b981" : "#f59e0b",
                borderColor: isComplete ? "#059669" : "#d97706",
                extendedProps: {
                    audit: audit,
                    completionStatus: isComplete ? "complete" : "pending",
                },
            };
        });
    }),
    eventClick: (info) => {
        router.visit(route("companies.audits.show", info.event.id));
    },
    dateClick: (info) => {
        // Optional: Navigate to create page with pre-filled date
        router.visit(route("companies.audits.create") + "?date=" + info.dateStr);
    },
    height: "auto",
    editable: false,
    selectable: false,
});

const deleteAudit = (id) => {
    if (confirm("Are you sure you want to delete this audit?")) {
        router.delete(route("companies.audits.destroy", id));
    }
};

const getCompletionStatusColor = (status) => {
    const colors = {
        complete: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        pending: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
    };
    return colors[status] || colors.pending;
};

const navs = computed(() => [
    { name: "Dashboard", linkName: "companies.dashboard" },
    { name: "Audit Tracker", linkName: "companies.audits.index" },
]);
</script>

<template>
    <Head title="Audit Tracker" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            title="Audit Tracker"
            navLinkName="companies.audits.create"
            NavLinkText="Add New Audit"
            :showButton="true"
            :addSearchInput="false"
        />

        <!-- Calendar View -->
        <div class="mt-6 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <FullCalendar ref="calendarRef" :options="calendarOptions" />
        </div>

        <!-- Legend -->
        <div class="mt-6 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Legend</h2>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-green-500 rounded"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Complete</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Pending</span>
                </div>
            </div>
        </div>

        <!-- Audits List -->
        <div class="mt-6 bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">All Audits</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Audit Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Reports
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="audit in audits" :key="audit.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ audit.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ new Date(audit.audit_date).toLocaleDateString() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            <span :class="[
                                'px-2 py-1 text-xs font-semibold rounded-full',
                                audit.reports?.length > 0 
                                    ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                    : 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
                            ]">
                                {{ audit.reports?.length > 0 ? 'Internal' : 'External' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="[
                                'px-2 py-1 text-xs font-semibold rounded-full',
                                getCompletionStatusColor(
                                    audit.reports?.length === 0 || 
                                    audit.reports?.every((r) => r.general_report_status === 'complete')
                                        ? 'complete'
                                        : 'pending'
                                )
                            ]">
                                {{
                                    audit.reports?.length === 0 || 
                                    audit.reports?.every((r) => r.general_report_status === 'complete')
                                        ? 'Complete'
                                        : 'Pending'
                                }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ audit.reports?.length || 0 }} report(s)
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <Link
                                :href="route('companies.audits.show', audit.id)"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3"
                            >
                                View
                            </Link>
                            <Link
                                :href="route('companies.audits.edit', audit.id)"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3"
                            >
                                Edit
                            </Link>
                            <button
                                @click="deleteAudit(audit.id)"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                    <tr v-if="audits.length === 0">
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No audits found. <Link :href="route('companies.audits.create')" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">Create one</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
