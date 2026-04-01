<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/vue3";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    departments: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const employee = computed(() => page.props.authEmployee?.employee);

// Static placeholders to match the reference dashboard design
const companyIndustry = computed(() => employee.value?.company?.sector?.industry?.name || employee.value?.company?.industry?.name || "-");
const companyEntity = computed(() => employee.value?.company?.name || "-");

const filters = ref({
    department: "Flight Ops",
    reportType: "Safety Reports",
});

const showDepartmentPicker = ref(false);
const selectedDepartmentId = ref("");

const openNewReport = () => {
    selectedDepartmentId.value = "";
    showDepartmentPicker.value = true;
};

const goToCreateReport = () => {
    if (!selectedDepartmentId.value) return;
    showDepartmentPicker.value = false;
    router.visit(route("companies.departments.reports.create", { department: selectedDepartmentId.value }));
};

const statCards = [
    { label: "Open Safety Reports", value: 52, tone: "bg-rose-100", text: "text-rose-900" },
    { label: "Open Quality Findings", value: 12, tone: "bg-amber-100", text: "text-amber-900" },
    { label: "Closed Reports", value: 134, tone: "bg-emerald-100", text: "text-emerald-900" },
    { label: "Overdue CAPAs", value: 7, tone: "bg-sky-100", text: "text-sky-900" },
];

const reportStatus = [
    { label: "Draft", color: "bg-blue-600", value: 18 },
    { label: "Submitted", color: "bg-emerald-600", value: 12 },
    { label: "Under Review", color: "bg-amber-500", value: 9 },
    { label: "Closed", color: "bg-rose-500", value: 61 },
];

const recentReports = [
    { id: "ASR-1123", title: "Runway Incursion", dept: "Flight Ops", type: "Safety", status: "Under Review", date: "25-Jun-2025" },
    { id: "ASR-123", title: "Tumpkace", dept: "Flight Ops", type: "Safety", status: "Submitted", date: "24-Jun-2025" },
    { id: "QFR-203", title: "Inspection deviation", dept: "Maintenance", type: "Quality", status: "Draft", date: "23-Jun-2025" },
    { id: "ASR-980", title: "Ground handling delay", dept: "Ground Ops", type: "Safety", status: "Closed", date: "22-Jun-2025" },
];
</script>

<template>
    <Head title="Companies Dashboard" />

    <div class="px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-start justify-between gap-4 mb-5">
            <div class="min-w-0">
                <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">Dashboard</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 truncate">
                    {{ employee?.company?.sector?.name }} • {{ employee?.company?.name }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left filters (matches reference UI) -->
            <aside class="lg:col-span-3">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-4 space-y-4 shadow-sm">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2">Industry</label>
                        <div class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-2.5 text-gray-900 dark:text-white font-semibold">
                            {{ companyIndustry }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2">Entity</label>
                        <div class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-2.5 text-gray-900 dark:text-white font-semibold">
                            {{ companyEntity }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2">Department</label>
                        <select v-model="filters.department" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-brand-sky">
                            <option>Flight Ops</option>
                            <option>Ground Ops</option>
                            <option>Maintenance</option>
                            <option>OCC</option>
                            <option>Safety</option>
                            <option>Quality</option>
                            <option>Training</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2">Report Type</label>
                        <div class="grid grid-cols-1 gap-2">
                            <button
                                type="button"
                                class="w-full text-left px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                                :class="filters.reportType === 'Safety Reports' ? 'ring-2 ring-brand-sky' : ''"
                                @click="filters.reportType = 'Safety Reports'"
                            >
                                Safety Reports
                            </button>
                            <button
                                type="button"
                                class="w-full text-left px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                                :class="filters.reportType === 'Quality Reports' ? 'ring-2 ring-brand-sky' : ''"
                                @click="filters.reportType = 'Quality Reports'"
                            >
                                Quality Reports
                            </button>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main content -->
            <section class="lg:col-span-9 space-y-6">
                <!-- KPI Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    <div
                        v-for="c in statCards"
                        :key="c.label"
                        class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden"
                    >
                        <div class="p-4" :class="c.tone">
                            <div class="text-sm font-semibold" :class="c.text">{{ c.label }}</div>
                            <div class="mt-1 text-3xl font-extrabold text-gray-900">{{ c.value }}</div>
                        </div>
                    </div>
                </div>

                <!-- Mid row -->
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">
                    <!-- Report status donut (static) -->
                    <div class="xl:col-span-4 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-extrabold text-gray-900 dark:text-white">Report Status</h3>
                        </div>
                        <div class="mt-4 flex items-center gap-5">
                            <div class="relative w-24 h-24 rounded-full border-8 border-gray-100 dark:border-gray-700">
                                <div class="absolute inset-0 rounded-full border-8 border-blue-600 border-r-amber-500 border-b-rose-500 border-l-emerald-600"></div>
                                <div class="absolute inset-0 m-auto w-12 h-12 rounded-full bg-white dark:bg-gray-800"></div>
                            </div>
                            <div class="space-y-2">
                                <div v-for="s in reportStatus" :key="s.label" class="flex items-center gap-2 text-sm">
                                    <span class="w-2.5 h-2.5 rounded-full" :class="s.color"></span>
                                    <span class="text-gray-700 dark:text-gray-200">{{ s.label }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Report volume -->
                    <div class="xl:col-span-5 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm p-4">
                        <h3 class="text-sm font-extrabold text-gray-900 dark:text-white">Report Volume by Depart</h3>
                        <div class="mt-4 space-y-3">
                            <div>
                                <div class="flex justify-between text-xs text-gray-600 dark:text-gray-300 mb-1">
                                    <span>Safety</span><span>54%</span>
                                </div>
                                <div class="h-2.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                    <div class="h-2.5 bg-blue-600 rounded-full" style="width: 54%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs text-gray-600 dark:text-gray-300 mb-1">
                                    <span>Quality</span><span>29%</span>
                                </div>
                                <div class="h-2.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                    <div class="h-2.5 bg-emerald-600 rounded-full" style="width: 29%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs text-gray-600 dark:text-gray-300 mb-1">
                                    <span>Audit</span><span>17%</span>
                                </div>
                                <div class="h-2.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                    <div class="h-2.5 bg-amber-500 rounded-full" style="width: 17%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-400">Static placeholders for now</div>
                    </div>

                    <!-- Actions -->
                    <div class="xl:col-span-3 space-y-3">
                        <button
                            type="button"
                            class="w-full inline-flex items-center justify-between px-4 py-3 rounded-2xl bg-emerald-600 text-white font-extrabold shadow-sm hover:bg-emerald-700 transition"
                            @click="openNewReport"
                        >
                            <span>New Report</span>
                            <span class="text-white/90">›</span>
                        </button>
                        <Link
                            :href="route('companies.risks.index')"
                            class="w-full inline-flex items-center justify-between px-4 py-3 rounded-2xl bg-slate-100 text-gray-900 font-extrabold border border-gray-200 hover:bg-slate-50 transition"
                        >
                            <span>View Risk Register</span>
                            <span class="text-gray-500">›</span>
                        </Link>

                        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm p-4">
                            <div class="text-sm font-extrabold text-gray-900 dark:text-white">Ongoing Investigations</div>
                            <div class="mt-3 space-y-2">
                                <div class="h-2.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                    <div class="h-2.5 bg-blue-600 rounded-full" style="width: 72%"></div>
                                </div>
                                <div class="h-2.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                    <div class="h-2.5 bg-emerald-600 rounded-full" style="width: 49%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent reports + alerts -->
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">
                    <div class="xl:col-span-9 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <div class="text-sm font-extrabold text-gray-900 dark:text-white">Recent Reports</div>
                            <Link :href="route('companies.reports.index')" class="text-sm font-semibold text-brand-blue hover:text-brand-navy">
                                View all
                            </Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-900/40 text-gray-600 dark:text-gray-300">
                                    <tr>
                                        <th class="text-left font-semibold px-4 py-3">Report ID</th>
                                        <th class="text-left font-semibold px-4 py-3">Title</th>
                                        <th class="text-left font-semibold px-4 py-3">Dept.</th>
                                        <th class="text-left font-semibold px-4 py-3">Type</th>
                                        <th class="text-left font-semibold px-4 py-3">Status</th>
                                        <th class="text-left font-semibold px-4 py-3">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="r in recentReports" :key="r.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
                                        <td class="px-4 py-3 font-semibold text-brand-blue">{{ r.id }}</td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-white">{{ r.title }}</td>
                                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ r.dept }}</td>
                                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ r.type }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">
                                                {{ r.status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ r.date }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="xl:col-span-3 space-y-4">
                        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm p-4">
                            <div class="text-sm font-extrabold text-gray-900 dark:text-white">Recent Safety Alerts</div>
                            <ul class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                                <li class="flex gap-2">
                                    <span class="mt-2 h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                    <span>Alert text here</span>
                                </li>
                                <li class="flex gap-2">
                                    <span class="mt-2 h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                    <span>Alert text here</span>
                                </li>
                            </ul>
                        </div>

                        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm p-4">
                            <div class="text-sm font-extrabold text-gray-900 dark:text-white">Export Reports</div>
                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <button type="button" class="px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition" disabled>
                                    Excel
                                </button>
                                <button type="button" class="px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition" disabled>
                                    PDF
                                </button>
                            </div>
                            <div class="mt-2 text-xs text-gray-400">Static placeholders for now</div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Department picker modal -->
    <div v-if="showDepartmentPicker" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <button
            type="button"
            class="absolute inset-0 bg-black/40"
            @click="showDepartmentPicker = false"
            aria-label="Close"
        ></button>

        <div class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-base font-extrabold text-gray-900 dark:text-white">Choose department</div>
                    <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">Select a department to create the report in.</div>
                </div>
                <button
                    type="button"
                    class="rounded-xl px-2 py-1 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="showDepartmentPicker = false"
                >
                    ✕
                </button>
            </div>

            <div class="mt-4">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2">Department</label>
                <select
                    v-model="selectedDepartmentId"
                    class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-brand-sky"
                >
                    <option value="" disabled>Select department…</option>
                    <option v-for="d in props.departments" :key="d.id" :value="d.id">
                        {{ d.name }}
                    </option>
                </select>
            </div>

            <div class="mt-5 flex items-center justify-end gap-2">
                <button
                    type="button"
                    class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                    @click="showDepartmentPicker = false"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-extrabold hover:bg-emerald-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="!selectedDepartmentId"
                    @click="goToCreateReport"
                >
                    Continue
                </button>
            </div>
        </div>
    </div>
</template>

