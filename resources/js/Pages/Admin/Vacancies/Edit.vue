<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({ vacancy: Object });

const form = useForm({
    title:        props.vacancy.title,
    department:   props.vacancy.department || '',
    location:     props.vacancy.location || '',
    type:         props.vacancy.type,
    description:  props.vacancy.description,
    requirements: props.vacancy.requirements || '',
    benefits:     props.vacancy.benefits || '',
    salary_range: props.vacancy.salary_range || '',
    closing_date: props.vacancy.closing_date || '',
    is_active:    props.vacancy.is_active,
});

const submit = () => form.put(route('vacancies.update', props.vacancy.id));
</script>

<template>
    <Head title="Edit Vacancy" />
    <div class="p-6">
        <div class="mb-6">
            <Link :href="route('vacancies.index')" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">← Back to Vacancies</Link>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Vacancy</h1>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Job Title <span class="text-red-500">*</span></label>
                    <input v-model="form.title" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                        <input v-model="form.department" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                        <input v-model="form.location" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    </div>
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Salary Range</label>
                        <input v-model="form.salary_range" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Closing Date</label>
                        <input v-model="form.closing_date" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                        <div v-if="form.errors.closing_date" class="mt-1 text-sm text-red-600">{{ form.errors.closing_date }}</div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Job Description <span class="text-red-500">*</span></label>
                    <textarea v-model="form.description" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                    <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Requirements & Qualifications</label>
                    <textarea v-model="form.requirements" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Benefits & Perks</label>
                    <textarea v-model="form.benefits" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>

                <div class="flex items-center gap-3">
                    <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" />
                    <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Active (visible on careers page)</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <Link :href="route('vacancies.index')" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">Cancel</Link>
                    <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
