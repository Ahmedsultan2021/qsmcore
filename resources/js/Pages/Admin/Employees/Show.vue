<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    employee: Object,
});

const deleteEmployee = () => {
    if (confirm("Are you sure you want to delete this employee?")) {
        router.delete(route("employees.destroy", props.employee.id));
    }
};
</script>

<template>
    <Head title="Employee Details" />

    <div class="p-6">
        <div class="mb-6">
            <Link
                :href="route('employees.index', { company_id: employee.company_id })"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
            >
                ← Back to Employees
            </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ employee.fname }} {{ employee.lname }}
                    </h1>
                    <div class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                        <p>Email: {{ employee.email }}</p>
                        <p v-if="employee.phone">Phone: {{ employee.phone }}</p>
                        <p v-if="employee.position">Position: {{ employee.position }}</p>
                        <p>
                            Company:
                            <Link
                                :href="route('companies.show', employee.company.id)"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                {{ employee.company.name }}
                            </Link>
                        </p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <Link
                        :href="route('employees.edit', employee.id)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    >
                        Edit
                    </Link>
                    <button
                        @click="deleteEmployee"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

