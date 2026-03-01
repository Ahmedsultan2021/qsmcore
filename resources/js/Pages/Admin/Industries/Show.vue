<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    industry: Object,
});

const deleteIndustry = () => {
    if (confirm("Are you sure you want to delete this industry?")) {
        router.delete(route("industries.destroy", props.industry.id));
    }
};
</script>

<template>
    <Head title="Industry Details" />

    <div class="p-6">
        <div class="mb-6">
            <Link
                :href="route('industries.index')"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
            >
                ← Back to Industries
            </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ industry.name }}</h1>
                    <p v-if="industry.description" class="mt-2 text-gray-600 dark:text-gray-400">{{ industry.description }}</p>
                </div>
                <div class="flex space-x-3">
                    <Link
                        :href="route('industries.edit', industry.id)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    >
                        Edit
                    </Link>
                    <button
                        @click="deleteIndustry"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Sectors</h2>
                <div class="mb-4">
                    <Link
                        :href="route('sectors.create', { industry_id: industry.id })"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg"
                    >
                        Add New Sector
                    </Link>
                </div>
                <div v-if="industry.sectors && industry.sectors.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        v-for="sector in industry.sectors"
                        :key="sector.id"
                        class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow"
                    >
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            <Link
                                :href="route('sectors.show', sector.id)"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                {{ sector.name }}
                            </Link>
                        </h3>
                        <p v-if="sector.description" class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                            {{ sector.description }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">
                            Companies: {{ sector.companies ? sector.companies.length : 0 }}
                        </p>
                    </div>
                </div>
                <p v-else class="text-gray-500 dark:text-gray-400">No sectors found.</p>
            </div>
        </div>
    </div>
</template>

