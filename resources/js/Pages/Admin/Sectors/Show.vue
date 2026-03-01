<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    sector: Object,
});

const deleteSector = () => {
    if (confirm("Are you sure you want to delete this sector?")) {
        router.delete(route("sectors.destroy", props.sector.id));
    }
};
</script>

<template>
    <Head title="Sector Details" />

    <div class="p-6">
        <div class="mb-6">
            <Link
                :href="route('sectors.index', { industry_id: sector.industry_id })"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
            >
                ← Back to Sectors
            </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ sector.name }}</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Industry:
                        <Link
                            :href="route('industries.show', sector.industry.id)"
                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                        >
                            {{ sector.industry.name }}
                        </Link>
                    </p>
                    <p v-if="sector.description" class="mt-2 text-gray-600 dark:text-gray-400">{{ sector.description }}</p>
                </div>
                <div class="flex space-x-3">
                    <Link
                        :href="route('sectors.edit', sector.id)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    >
                        Edit
                    </Link>
                    <button
                        @click="deleteSector"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Companies</h2>
                <div class="mb-4">
                    <Link
                        :href="route('companies.create', { sector_id: sector.id })"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg"
                    >
                        Add New Company
                    </Link>
                </div>
                <div v-if="sector.companies && sector.companies.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        v-for="company in sector.companies"
                        :key="company.id"
                        class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow"
                    >
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            <Link
                                :href="route('companies.show', company.id)"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                {{ company.name }}
                            </Link>
                        </h3>
                        <p v-if="company.email" class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                            Email: {{ company.email }}
                        </p>
                        <p v-if="company.phone" class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                            Phone: {{ company.phone }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">
                            Employees: {{ company.employees ? company.employees.length : 0 }}
                        </p>
                    </div>
                </div>
                <p v-else class="text-gray-500 dark:text-gray-400">No companies found.</p>
            </div>
        </div>
    </div>
</template>

