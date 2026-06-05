<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    company: Object,
});

const deleteCompany = () => {
    if (confirm("Are you sure you want to delete this company?")) {
        router.delete(route("companies.destroy", props.company.id));
    }
};

const loginAsEmployee = (employee) => {
    const name = `${employee.fname} ${employee.lname}`;
    if (confirm(`Login as "${name}"?`)) {
        router.post(route("employees.impersonate", employee.id));
    }
};

const loginAsCompany = () => {
    if (confirm(`Login as first employee of "${props.company.name}"?`)) {
        router.post(route("companies.impersonate", props.company.id));
    }
};
</script>

<template>
    <Head title="Company Details" />

    <div class="p-6">
        <div class="mb-6">
            <Link
                :href="route('companies.index', { sector_id: company.sector_id })"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
            >
                ← Back to Companies
            </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-start mb-6 gap-4">
                <div class="flex items-start gap-4 min-w-0">
                    <img
                        v-if="company.logo_url"
                        :src="company.logo_url"
                        :alt="`${company.name} logo`"
                        class="h-16 w-16 sm:h-20 sm:w-20 shrink-0 object-contain rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 p-1"
                    />
                    <div class="min-w-0">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ company.name }}</h1>
                    <div class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                        <p v-if="company.email">Email: {{ company.email }}</p>
                        <p v-if="company.phone">Phone: {{ company.phone }}</p>
                        <p v-if="company.address">Address: {{ company.address }}</p>
                        <p>
                            Sector:
                            <Link
                                :href="route('sectors.show', company.sector.id)"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                {{ company.sector.name }}
                            </Link>
                        </p>
                    </div>
                    <p v-if="company.description" class="mt-2 text-gray-600 dark:text-gray-400">{{ company.description }}</p>
                    </div>
                </div>
                <div class="flex space-x-3 shrink-0">
                    <button
                        @click="loginAsCompany"
                        class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700"
                    >
                        <i class="fa-solid fa-arrow-right-to-bracket mr-1"></i>Login as Company
                    </button>
                    <Link
                        :href="route('companies.edit', company.id)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    >
                        Edit
                    </Link>
                    <button
                        @click="deleteCompany"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Employees</h2>
                <div class="mb-4">
                    <Link
                        :href="route('employees.create', { company_id: company.id })"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg"
                    >
                        Add New Employee
                    </Link>
                </div>
                <div v-if="company.employees && company.employees.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        v-for="employee in company.employees"
                        :key="employee.id"
                        class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow"
                    >
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            <Link
                                :href="route('employees.show', employee.id)"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                {{ employee.fname }} {{ employee.lname }}
                            </Link>
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Email: {{ employee.email }}</p>
                        <p v-if="employee.phone" class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                            Phone: {{ employee.phone }}
                        </p>
                        <p v-if="employee.position" class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            Position: {{ employee.position }}
                        </p>
                        <button
                            @click="loginAsEmployee(employee)"
                            class="text-sm text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300 font-medium"
                        >
                            <i class="fa-solid fa-arrow-right-to-bracket mr-1"></i>Login as
                        </button>
                    </div>
                </div>
                <p v-else class="text-gray-500 dark:text-gray-400">No employees found.</p>
            </div>
        </div>
    </div>
</template>

