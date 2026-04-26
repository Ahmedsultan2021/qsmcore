<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

let searchTimer = null;

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    formTemplates: Object,
    filters: {
        type: Object,
        default: () => ({}),
    },
    industries: Array,
    sectors: Array,
    departments: Array,
});

const navs = [
    { name: "Dashboard", linkName: "dashboard" },
    { name: "Form templates", linkName: "form-templates.index" },
];

const industryId   = ref(props.filters.industry_id   != null ? String(props.filters.industry_id)   : "");
const sectorId     = ref(props.filters.sector_id     != null ? String(props.filters.sector_id)     : "");
const departmentId = ref(props.filters.department_id != null ? String(props.filters.department_id) : "");
const search       = ref(props.filters.search ?? "");

watch(
    () => props.filters,
    (f) => {
        industryId.value   = f.industry_id   != null ? String(f.industry_id)   : "";
        sectorId.value     = f.sector_id     != null ? String(f.sector_id)     : "";
        departmentId.value = f.department_id != null ? String(f.department_id) : "";
        search.value       = f.search ?? "";
    },
    { deep: true }
);

watch(search, () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(applyFilters, 350);
});

const filteredSectors = computed(() => {
    if (!industryId.value) {
        return props.sectors || [];
    }
    const ind = Number(industryId.value);
    return (props.sectors || []).filter((s) => Number(s.industry_id) === ind);
});

const filteredDepartments = computed(() => {
    const depts = props.departments || [];
    if (sectorId.value) {
        const sec = Number(sectorId.value);
        return depts.filter((d) => d.sector_id != null && Number(d.sector_id) === sec);
    }
    if (industryId.value) {
        const ind = Number(industryId.value);
        return depts.filter((d) => d.industry_id != null && Number(d.industry_id) === ind);
    }
    return depts;
});

function buildQuery() {
    const q = {};
    if (search.value)       q.search       = search.value;
    if (industryId.value)   q.industry_id  = industryId.value;
    if (sectorId.value)     q.sector_id    = sectorId.value;
    if (departmentId.value) q.department_id = departmentId.value;
    return q;
}

function applyFilters() {
    router.get(route("form-templates.index"), buildQuery(), {
        preserveState: true,
        replace: true,
        preserveScroll: true,
    });
}

function onIndustryChange() {
    sectorId.value = "";
    departmentId.value = "";
    applyFilters();
}

function onSectorChange() {
    departmentId.value = "";
    applyFilters();
}

function onDepartmentChange() {
    applyFilters();
}

function clearFilters() {
    search.value       = "";
    industryId.value   = "";
    sectorId.value     = "";
    departmentId.value = "";
    router.get(route("form-templates.index"), {}, { replace: true, preserveScroll: true });
}
</script>

<template>
    <Head title="Form templates" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            title="Form templates"
            :showButton="false"
            :addSearchInput="false"
        />

        <p class="mt-4 text-sm text-gray-600 dark:text-gray-400 max-w-3xl">
            Master list of form blueprints. Use the filters to see templates linked to an industry, sector, or company
            department (pivot tables). Manage industry attachments under
            <Link :href="route('industries.index')" class="text-blue-600 hover:underline dark:text-blue-400">Industries</Link>
            and sector attachments under
            <Link :href="route('sectors.index')" class="text-blue-600 hover:underline dark:text-blue-400">Sectors</Link>.
        </p>

        <div
            class="mt-6 flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-600 dark:bg-gray-800 sm:flex-row sm:flex-wrap sm:items-end"
        >
            <!-- Search -->
            <div class="w-full sm:max-w-sm">
                <label for="filter-search" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">
                    Search
                </label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
                    </svg>
                    <input
                        id="filter-search"
                        v-model="search"
                        type="text"
                        placeholder="Search by name, library, or description…"
                        class="w-full rounded-md border border-gray-300 bg-white py-2 pl-9 pr-3 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    />
                </div>
            </div>

            <div class="min-w-[10rem] flex-1 sm:max-w-xs">
                <label for="filter-industry" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">
                    Industry
                </label>
                <select
                    id="filter-industry"
                    v-model="industryId"
                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    @change="onIndustryChange"
                >
                    <option value="">All industries</option>
                    <option v-for="ind in industries" :key="ind.id" :value="String(ind.id)">{{ ind.name }}</option>
                </select>
            </div>
            <div class="min-w-[10rem] flex-1 sm:max-w-xs">
                <label for="filter-sector" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">
                    Sector
                </label>
                <select
                    id="filter-sector"
                    v-model="sectorId"
                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    @change="onSectorChange"
                >
                    <option value="">All sectors</option>
                    <option v-for="sec in filteredSectors" :key="sec.id" :value="String(sec.id)">{{ sec.name }}</option>
                </select>
            </div>
            <div class="min-w-[12rem] flex-1 sm:max-w-md">
                <label for="filter-department" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">
                    Department
                </label>
                <select
                    id="filter-department"
                    v-model="departmentId"
                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    @change="onDepartmentChange"
                >
                    <option value="">All departments</option>
                    <option v-for="dep in filteredDepartments" :key="dep.id" :value="String(dep.id)">{{ dep.label }}</option>
                </select>
            </div>
            <button
                type="button"
                class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                @click="clearFilters"
            >
                Clear filters
            </button>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mt-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                            >
                                Name
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                            >
                                Library
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                            >
                                KPI themes
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                            >
                                Description
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                            >
                                Fields
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                            >
                                Industries
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                            >
                                Sectors
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-if="!formTemplates.data?.length">
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                No templates match these filters.
                            </td>
                        </tr>
                        <tr
                            v-for="row in formTemplates.data"
                            :key="row.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/80"
                        >
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                {{ row.name }}
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-gray-600 dark:text-gray-300">
                                {{ row.library_key || "—" }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                <span v-if="!(row.themes || []).length">—</span>
                                <span v-else>{{ (row.themes || []).map((t) => t.name).join(", ") }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-md line-clamp-2">
                                {{ row.description || "—" }}
                            </td>
                            <td class="px-6 py-4 text-sm text-center text-gray-600 dark:text-gray-300">
                                {{ row.form_template_fields_count }}
                            </td>
                            <td class="px-6 py-4 text-sm text-center text-gray-600 dark:text-gray-300">
                                {{ row.industries_count }}
                            </td>
                            <td class="px-6 py-4 text-sm text-center text-gray-600 dark:text-gray-300">
                                {{ row.sectors_count }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="formTemplates.links && formTemplates.links.length > 3"
                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700"
            >
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link
                            v-if="formTemplates.prev_page_url"
                            :href="formTemplates.prev_page_url"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                        >
                            Previous
                        </Link>
                        <Link
                            v-if="formTemplates.next_page_url"
                            :href="formTemplates.next_page_url"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                        >
                            Next
                        </Link>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing
                                <span class="font-medium">{{ formTemplates.from }}</span>
                                to
                                <span class="font-medium">{{ formTemplates.to }}</span>
                                of
                                <span class="font-medium">{{ formTemplates.total }}</span>
                                templates
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <Link
                                    v-for="link in formTemplates.links"
                                    :key="link.label"
                                    :href="link.url || '#'"
                                    :class="[
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                        link.active
                                            ? 'z-10 bg-blue-50 border-blue-500 text-blue-600 dark:bg-blue-900/30 dark:border-blue-600 dark:text-blue-300'
                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400',
                                        link.url ? '' : 'cursor-not-allowed opacity-50',
                                    ]"
                                >
                                    <span v-html="link.label"></span>
                                </Link>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
