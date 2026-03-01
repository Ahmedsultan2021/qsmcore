<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    sector: Object,
    industries: Array,
});

const form = useForm({
    industry_id: props.sector.industry_id || "",
    name: props.sector.name || "",
    description: props.sector.description || "",
});

const submit = () => {
    form.put(route("sectors.update", props.sector.id));
};
</script>

<template>
    <Head title="Edit Sector" />

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
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Sector</h1>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label for="industry_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Industry <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="industry_id"
                        v-model="form.industry_id"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">Select Industry</option>
                        <option v-for="industry in industries" :key="industry.id" :value="industry.id">
                            {{ industry.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.industry_id" class="mt-1 text-sm text-red-600">{{ form.errors.industry_id }}</div>
                </div>

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    ></textarea>
                    <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</div>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <Link
                        :href="route('sectors.index', { industry_id: sector.industry_id })"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        {{ form.processing ? "Updating..." : "Update Sector" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

