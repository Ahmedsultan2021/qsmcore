<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    forms: Object,
});

const deleteForm = (id) => {
    if (confirm("Are you sure you want to delete this form?")) {
        router.delete(route("companies.forms.destroy", id));
    }
};

const navs = computed(() => [
    { name: "Dashboard", linkName: "companies.dashboard" },
    { name: "Forms", linkName: "companies.forms.index" },
]);
</script>

<template>
    <Head title="Forms" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            title="Forms"
            navLinkName="companies.forms.create"
            NavLinkText="Add New Form"
            :showButton="true"
            :addSearchInput="false"
        />

        <div class="flex flex-wrap gap-2 mt-4 mb-2">
            <Link
                :href="route('companies.forms.templates.bank')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
            >
                Add from Template Bank
            </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mt-6">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Department
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Created At
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="form in forms.data" :key="form.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ form.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            <span v-if="form.department" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ form.department.name }}
                            </span>
                            <span v-else class="text-gray-400 dark:text-gray-500 italic">No department</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ new Date(form.created_at).toLocaleDateString() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <Link
                                :href="route('companies.forms.show', form.id)"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3"
                            >
                                View
                            </Link>
                            <Link
                                :href="route('companies.forms.edit', form.id)"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3"
                            >
                                Edit
                            </Link>
                            <button
                                @click="deleteForm(form.id)"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="forms.links && forms.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link
                            v-if="forms.prev_page_url"
                            :href="forms.prev_page_url"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Previous
                        </Link>
                        <Link
                            v-if="forms.next_page_url"
                            :href="forms.next_page_url"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Next
                        </Link>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing
                                <span class="font-medium">{{ forms.from }}</span>
                                to
                                <span class="font-medium">{{ forms.to }}</span>
                                of
                                <span class="font-medium">{{ forms.total }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <Link
                                    v-for="link in forms.links"
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

