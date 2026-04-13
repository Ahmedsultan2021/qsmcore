<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({ vacancy: Object });

const statusColors = {
    pending:     'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
    reviewed:    'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
    shortlisted: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
    rejected:    'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
};

const typeColors = {
    'full-time': 'bg-blue-100 text-blue-700',
    'part-time': 'bg-purple-100 text-purple-700',
    'contract':  'bg-yellow-100 text-yellow-700',
    'remote':    'bg-green-100 text-green-700',
};

const updateStatus = (app, status) => {
    router.patch(route('applications.update-status', app.id), { status }, { preserveScroll: true });
};

const deleteApp = (id) => {
    if (confirm('Delete this application? The CV file will also be removed.')) {
        router.delete(route('applications.destroy', id), { preserveScroll: true });
    }
};

const formatDate = (d) => new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

const expandedApp = ref(null);
</script>

<template>
    <Head :title="vacancy.title" />
    <div class="p-6">
        <div class="mb-6">
            <Link :href="route('vacancies.index')" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">← Back to Vacancies</Link>
        </div>

        <!-- Vacancy details card -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ vacancy.title }}</h1>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span :class="typeColors[vacancy.type]" class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize">{{ vacancy.type }}</span>
                        <span v-if="vacancy.department" class="bg-gray-100 text-gray-700 px-2.5 py-0.5 rounded-full text-xs font-medium">{{ vacancy.department }}</span>
                        <span v-if="vacancy.location" class="bg-gray-100 text-gray-700 px-2.5 py-0.5 rounded-full text-xs font-medium">📍 {{ vacancy.location }}</span>
                        <span v-if="vacancy.salary_range" class="bg-gray-100 text-gray-700 px-2.5 py-0.5 rounded-full text-xs font-medium">💰 {{ vacancy.salary_range }}</span>
                        <span v-if="vacancy.closing_date" class="bg-gray-100 text-gray-700 px-2.5 py-0.5 rounded-full text-xs font-medium">Closes {{ formatDate(vacancy.closing_date) }}</span>
                        <span :class="vacancy.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-2.5 py-0.5 rounded-full text-xs font-medium">{{ vacancy.is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                </div>
                <Link :href="route('vacancies.edit', vacancy.id)" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">Edit Vacancy</Link>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div class="md:col-span-2 space-y-5">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Description</h3>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">{{ vacancy.description }}</p>
                    </div>
                    <div v-if="vacancy.requirements">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Requirements</h3>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">{{ vacancy.requirements }}</p>
                    </div>
                    <div v-if="vacancy.benefits">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Benefits</h3>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">{{ vacancy.benefits }}</p>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ vacancy.applications.length }}</div>
                    <div class="text-sm text-gray-500 mt-1">Total Applications</div>
                    <div class="mt-4 space-y-1 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Pending</span><span class="font-medium">{{ vacancy.applications.filter(a => a.status === 'pending').length }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Reviewed</span><span class="font-medium">{{ vacancy.applications.filter(a => a.status === 'reviewed').length }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Shortlisted</span><span class="font-medium text-green-600">{{ vacancy.applications.filter(a => a.status === 'shortlisted').length }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Rejected</span><span class="font-medium text-red-600">{{ vacancy.applications.filter(a => a.status === 'rejected').length }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Applications -->
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Applications</h2>

        <div v-if="vacancy.applications.length === 0" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center text-gray-400">
            <i class="fa-solid fa-file-circle-xmark text-4xl mb-3 block opacity-30"></i>No applications yet.
        </div>

        <div class="space-y-3">
            <div v-for="app in vacancy.applications" :key="app.id" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <!-- Row -->
                <div class="flex items-center gap-4 px-5 py-4 cursor-pointer" @click="expandedApp = expandedApp === app.id ? null : app.id">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                            <span class="font-semibold text-gray-900 dark:text-white">{{ app.name }}</span>
                            <a :href="`mailto:${app.email}`" class="text-sm text-blue-600 dark:text-blue-400 hover:underline" @click.stop>{{ app.email }}</a>
                            <span v-if="app.phone" class="text-sm text-gray-500">{{ app.phone }}</span>
                        </div>
                        <div class="text-xs text-gray-400 mt-0.5">Applied {{ formatDate(app.created_at) }}</div>
                    </div>
                    <span :class="statusColors[app.status]" class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize flex-shrink-0">{{ app.status }}</span>
                    <svg class="w-5 h-5 text-gray-400 transition-transform flex-shrink-0" :class="{ 'rotate-180': expandedApp === app.id }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                <!-- Expanded -->
                <div v-if="expandedApp === app.id" class="px-5 pb-5 border-t border-gray-100 dark:border-gray-700 pt-4 space-y-4">
                    <div v-if="app.cover_letter">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Cover Letter</h4>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed text-sm">{{ app.cover_letter }}</p>
                    </div>

                    <div class="flex flex-wrap gap-3 items-center">
                        <!-- Download CV -->
                        <a :href="app.cv_url" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                            <i class="fa-solid fa-file-arrow-down"></i> Download CV
                        </a>

                        <!-- Status selector -->
                        <select
                            :value="app.status"
                            @change="updateStatus(app, $event.target.value)"
                            class="px-3 py-2 border border-gray-300 rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500"
                        >
                            <option value="pending">Pending</option>
                            <option value="reviewed">Reviewed</option>
                            <option value="shortlisted">Shortlisted</option>
                            <option value="rejected">Rejected</option>
                        </select>

                        <a :href="`mailto:${app.email}?subject=Re: Your Application for ${vacancy.title}`"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300">
                            <i class="fa-solid fa-reply"></i> Reply
                        </a>

                        <button @click="deleteApp(app.id)" class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 dark:bg-red-900 dark:text-red-300">
                            <i class="fa-solid fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
