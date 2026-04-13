<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineOptions({ layout: AuthenticatedLayout });

const form = useForm({
    title: '',
    department: '',
    location: '',
    type: 'full-time',
    description: '',
    requirements: '',
    benefits: '',
    salary_range: '',
    closing_date: '',
    is_active: true,
});

const submit = () => form.post(route('vacancies.store'));
</script>

<template>
    <Head title="Create Vacancy" />
    <div class="p-6">
        <div class="mb-6">
            <Link :href="route('vacancies.index')" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">← Back to Vacancies</Link>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Create New Vacancy</h1>

            <form @submit.prevent="submit" class="space-y-5">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Job Title <span class="text-red-500">*</span></label>
                    <input v-model="form.title" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Department -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                        <input v-model="form.department" type="text" placeholder="e.g. IT, HR, Operations" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    </div>
                    <!-- Location -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                        <input v-model="form.location" type="text" placeholder="e.g. Dubai, UAE / Remote" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    </div>
                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Employment Type <span class="text-red-500">*</span></label>
                        <select v-model="form.type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="full-time">Full-Time</option>
                            <option value="part-time">Part-Time</option>
                            <option value="contract">Contract</option>
                            <option value="remote">Remote</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Salary -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Salary Range</label>
                        <input v-model="form.salary_range" type="text" placeholder="e.g. $60,000 – $80,000 / year" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    </div>
                    <!-- Closing Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Closing Date</label>
                        <input v-model="form.closing_date" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                        <div v-if="form.errors.closing_date" class="mt-1 text-sm text-red-600">{{ form.errors.closing_date }}</div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Job Description <span class="text-red-500">*</span></label>
                    <textarea v-model="form.description" rows="6" placeholder="Describe the role, responsibilities, and what the candidate will be doing..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                    <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</div>
                </div>

                <!-- Requirements -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Requirements & Qualifications</label>
                    <textarea v-model="form.requirements" rows="5" placeholder="List required skills, experience, certifications..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>

                <!-- Benefits -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Benefits & Perks</label>
                    <textarea v-model="form.benefits" rows="4" placeholder="Health insurance, flexible hours, remote work options..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>

                <!-- Active toggle -->
                <div class="flex items-center gap-3">
                    <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" />
                    <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Publish immediately (visible on careers page)</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <Link :href="route('vacancies.index')" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">Cancel</Link>
                    <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
                        {{ form.processing ? 'Creating...' : 'Create Vacancy' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
