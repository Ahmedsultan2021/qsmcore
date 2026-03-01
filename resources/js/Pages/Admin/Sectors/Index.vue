<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    sectors: Object,
    industries: Array,
    filters: Object,
});

const deleteSector = (id) => {
    if (confirm("Are you sure you want to delete this sector?")) {
        router.delete(route("sectors.destroy", id));
    }
};

const navs = computed(() => [
    { name: "Dashboard", linkName: "dashboard" },
    { name: "Sectors", linkName: "sectors.index" },
]);

const filterForm = useForm({
    industry_id: props.filters?.industry_id || '',
    search: props.filters?.search || '',
});

const applyFilters = () => {
    filterForm.get(route('sectors.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    filterForm.industry_id = '';
    filterForm.search = '';
    applyFilters();
};
</script>

<template>
    <Head title="Sectors" />

    <div class="p-6 pt-20">
        <BaseDashboardHeader
            :navs="navs"
            title="Sectors"
            navLinkName="sectors.create"
            NavLinkText="Add New Sector"
            :showButton="true"
            :addSearchInput="false"
        />

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mt-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Industry
                    </label>
                    <select
                        v-model="filterForm.industry_id"
                        @change="applyFilters()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">All Industries</option>
                        <option v-for="industry in industries" :key="industry.id" :value="industry.id">
                            {{ industry.name }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Search
                    </label>
                    <input
                        v-model="filterForm.search"
                        @keyup.enter="applyFilters()"
                        type="text"
                        placeholder="Name, description..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                </div>
                <div class="flex items-end">
                    <button
                        @click="applyFilters()"
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Filter
                    </button>
                    <button
                        @click="clearFilters()"
                        class="ml-2 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500"
                    >
                        Clear
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mt-6">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Industry
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Companies
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="sector in sectors.data" :key="sector.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <Link
                                :href="route('industries.show', sector.industry.id)"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                {{ sector.industry.name }}
                            </Link>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ sector.name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                            {{ sector.description || "-" }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ sector.companies ? sector.companies.length : 0 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <Link
                                :href="route('sectors.show', sector.id)"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3"
                            >
                                View
                            </Link>
                            <Link
                                :href="route('sectors.edit', sector.id)"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3"
                            >
                                Edit
                            </Link>
                            <button
                                @click="deleteSector(sector.id)"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="sectors.links && sectors.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link
                            v-if="sectors.prev_page_url"
                            :href="sectors.prev_page_url"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Previous
                        </Link>
                        <Link
                            v-if="sectors.next_page_url"
                            :href="sectors.next_page_url"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Next
                        </Link>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing
                                <span class="font-medium">{{ sectors.from }}</span>
                                to
                                <span class="font-medium">{{ sectors.to }}</span>
                                of
                                <span class="font-medium">{{ sectors.total }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <Link
                                    v-for="link in sectors.links"
                                    :key="link.label"
                                    :href="link.url || '#'"
                                    v-html="link.label"
                                    :class="[
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                        link.active
                                            ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                        link.url ? '' : 'cursor-not-allowed opacity-50'
                                    ]"
                                ></Link>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

