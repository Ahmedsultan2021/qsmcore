<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({ vacancies: Object });

const navs = computed(() => [
    { name: "Dashboard", linkName: "dashboard" },
    { name: "Vacancies", linkName: "vacancies.index" },
]);

const typeColors = {
    'full-time': 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
    'part-time': 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
    'contract':  'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
    'remote':    'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
};

const deleteVacancy = (id) => {
    if (confirm('Delete this vacancy? All its applications will also be deleted.')) {
        router.delete(route('vacancies.destroy', id));
    }
};

const toggleActive = (vacancy) => {
    router.post(route('vacancies.toggle-active', vacancy.id), {}, { preserveScroll: true });
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
</script>

<template>
    <Head title="Vacancies" />
    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            title="Vacancies"
            navLinkName="vacancies.create"
            NavLinkText="Add New Vacancy"
            :showButton="true"
            :addSearchInput="false"
        />

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mt-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Closes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Applications</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-if="vacancies.data.length === 0">
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">No vacancies yet.</td>
                        </tr>
                        <tr v-for="v in vacancies.data" :key="v.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ v.title }}</div>
                                <div v-if="v.department" class="text-xs text-gray-500">{{ v.department }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="typeColors[v.type]" class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize">{{ v.type }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ v.location || '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ formatDate(v.closing_date) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">{{ v.applications_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="v.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300'" class="px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    {{ v.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                <Link :href="route('vacancies.show', v.id)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">View</Link>
                                <Link :href="route('vacancies.edit', v.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Edit</Link>
                                <button @click="toggleActive(v)" :class="v.is_active ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900'">
                                    {{ v.is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                <button @click="deleteVacancy(v.id)" class="text-red-600 hover:text-red-900 dark:text-red-400">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="vacancies.links && vacancies.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <nav class="flex justify-end gap-1">
                    <a v-for="link in vacancies.links" :key="link.label" :href="link.url || '#'" v-html="link.label"
                        :class="['px-3 py-1 rounded text-sm border', link.active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50', !link.url ? 'opacity-40 cursor-not-allowed' : '']"
                    ></a>
                </nav>
            </div>
        </div>
    </div>
</template>
