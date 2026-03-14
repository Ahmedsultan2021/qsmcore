<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    departments: Object,
});

const viewMode = ref('grid'); // 'grid' or 'list'

const deleteDepartment = (id) => {
    if (confirm("Are you sure you want to delete this department?")) {
        router.delete(route("companies.departments.destroy", id));
    }
};

const navs = computed(() => [
    { name: "Dashboard", linkName: "companies.dashboard" },
    { name: "Departments", linkName: "companies.departments.index" },
]);
</script>

<template>
    <Head title="Departments" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            title="Departments"
            navLinkName="companies.departments.create"
            NavLinkText="Add New Department"
            :showButton="true"
            :addSearchInput="false"
        />

        <!-- View Mode Toggle -->
        <div class="flex justify-end mt-6">
            <div class="inline-flex rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-1">
                <button
                    @click="viewMode = 'grid'"
                    :class="[
                        'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                        viewMode === 'grid'
                            ? 'bg-blue-600 text-white'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                    ]"
                >
                    <i class="fa-solid fa-grip mr-2"></i>Grid
                </button>
                <button
                    @click="viewMode = 'list'"
                    :class="[
                        'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                        viewMode === 'list'
                            ? 'bg-blue-600 text-white'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                    ]"
                >
                    <i class="fa-solid fa-list mr-2"></i>List
                </button>
            </div>
        </div>

        <!-- Grid View -->
        <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
            <div
                v-for="department in departments.data"
                :key="department.id"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-300 group"
            >
                <!-- Card Header -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ department.name }}
                            </h3>
                            <p v-if="department.description" class="text-sm text-gray-600 dark:text-gray-400 mt-2 line-clamp-2">
                                {{ department.description }}
                            </p>
                            <p v-else class="text-sm text-gray-400 dark:text-gray-500 mt-2 italic">
                                No description available
                            </p>
                        </div>
                        <div class="ml-4 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                            <i class="fa-solid fa-building text-blue-600 dark:text-blue-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6 space-y-4">
                    <!-- Manager Info -->
                    <div class="flex items-center text-sm">
                        <div class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-user-tie text-gray-600 dark:text-gray-400 text-xs"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Manager</p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ department.manager_name || 'Not assigned' }}
                            </p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div v-if="department.email" class="flex items-center text-sm">
                        <div class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-envelope text-gray-600 dark:text-gray-400 text-xs"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                            <a :href="`mailto:${department.email}`" class="font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                {{ department.email }}
                            </a>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div v-if="department.phone" class="flex items-center text-sm">
                        <div class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-phone text-gray-600 dark:text-gray-400 text-xs"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Phone</p>
                            <a :href="`tel:${department.phone}`" class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                {{ department.phone }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <Link
                        :href="route('companies.departments.show', department.id)"
                        class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
                    >
                        <i class="fa-solid fa-eye mr-2"></i>View Details
                    </Link>
                    <div class="flex items-center space-x-2">
                        <Link
                            :href="route('companies.departments.edit', department.id)"
                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                            title="Edit"
                        >
                            <i class="fa-solid fa-pencil"></i>
                        </Link>
                        <button
                            @click="deleteDepartment(department.id)"
                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                            title="Delete"
                        >
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- List View (Original Table) -->
        <div v-if="viewMode === 'list'" class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mt-6">
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Manager
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Phone
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="department in departments.data" :key="department.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-building text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ department.name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            <div class="flex items-center">
                                <i class="fa-solid fa-user-tie mr-2 text-gray-400"></i>
                                {{ department.manager_name || "-" }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a v-if="department.email" :href="`mailto:${department.email}`" class="text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                                <i class="fa-solid fa-envelope mr-2 text-gray-400"></i>
                                {{ department.email }}
                            </a>
                            <span v-else class="text-gray-400 dark:text-gray-500">-</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            <a v-if="department.phone" :href="`tel:${department.phone}`" class="hover:text-blue-600 dark:hover:text-blue-400 flex items-center">
                                <i class="fa-solid fa-phone mr-2 text-gray-400"></i>
                                {{ department.phone }}
                            </a>
                            <span v-else class="text-gray-400 dark:text-gray-500">-</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                            <div class="max-w-xs truncate">
                                {{ department.description || "-" }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <Link
                                    :href="route('companies.departments.show', department.id)"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
                                >
                                    <i class="fa-solid fa-eye mr-1.5"></i>View
                                </Link>
                                <Link
                                    :href="route('companies.departments.edit', department.id)"
                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors"
                                >
                                    <i class="fa-solid fa-pencil mr-1.5"></i>Edit
                                </Link>
                                <button
                                    @click="deleteDepartment(department.id)"
                                    class="inline-flex items-center px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
                                >
                                    <i class="fa-solid fa-trash mr-1.5"></i>Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="departments.links && departments.links.length > 3" class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    <Link
                        v-if="departments.prev_page_url"
                        :href="departments.prev_page_url"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                        Previous
                    </Link>
                    <Link
                        v-if="departments.next_page_url"
                        :href="departments.next_page_url"
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                        Next
                    </Link>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Showing
                            <span class="font-medium">{{ departments.from }}</span>
                            to
                            <span class="font-medium">{{ departments.to }}</span>
                            of
                            <span class="font-medium">{{ departments.total }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <Link
                                v-for="link in departments.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                    link.active
                                        ? 'z-10 bg-blue-600 border-blue-600 text-white'
                                        : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700',
                                    link.url ? '' : 'cursor-not-allowed opacity-50'
                                ]"
                            >
                                <span v-if="link.label.includes('Previous')"><i class="fa-solid fa-chevron-left"></i></span>
                                <span v-else-if="link.label.includes('Next')"><i class="fa-solid fa-chevron-right"></i></span>
                                <span v-else>{{ link.label }}</span>
                            </Link>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="!departments.data || departments.data.length === 0" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center mt-6">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full mb-4">
                <i class="fa-solid fa-building text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No departments yet</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Get started by creating your first department.</p>
            <Link
                :href="route('companies.departments.create')"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
                <i class="fa-solid fa-plus mr-2"></i>Add New Department
            </Link>
        </div>
    </div>
</template>

