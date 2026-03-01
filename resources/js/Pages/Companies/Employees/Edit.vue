<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    employee: Object,
    roles: Array,
});

const form = useForm({
    fname: props.employee.fname || "",
    lname: props.employee.lname || "",
    email: props.employee.email || "",
    password: "",
    password_confirmation: "",
    phone: props.employee.phone || "",
    position: props.employee.position || "",
    roles: props.employee.roles ? props.employee.roles.map(r => r.id) : [],
});

const submit = () => {
    form.put(route("companies.employees.update", props.employee.id));
};
</script>

<template>
    <Head title="Edit Employee" />

    <div class="p-6">
        <div class="mb-6">
            <Link
                :href="route('companies.employees.index')"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
            >
                ← Back to Employees
            </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Employee</h1>

            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="fname" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="fname"
                            v-model="form.fname"
                            type="text"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                        <div v-if="form.errors.fname" class="mt-1 text-sm text-red-600">{{ form.errors.fname }}</div>
                    </div>

                    <div>
                        <label for="lname" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="lname"
                            v-model="form.lname"
                            type="text"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                        <div v-if="form.errors.lname" class="mt-1 text-sm text-red-600">{{ form.errors.lname }}</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                    <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Password (leave blank to keep current)
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                        <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Confirm Password
                        </label>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Phone
                        </label>
                        <input
                            id="phone"
                            v-model="form.phone"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                        <div v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</div>
                    </div>

                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Position
                        </label>
                        <input
                            id="position"
                            v-model="form.position"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                        <div v-if="form.errors.position" class="mt-1 text-sm text-red-600">{{ form.errors.position }}</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="roles" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Roles
                    </label>
                    <select
                        id="roles"
                        v-model="form.roles"
                        multiple
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option v-for="role in roles" :key="role.id" :value="role.id">
                            {{ role.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.roles" class="mt-1 text-sm text-red-600">{{ form.errors.roles }}</div>
                    <p class="mt-1 text-xs text-gray-500">Hold Ctrl (or Cmd on Mac) to select multiple roles</p>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <Link
                        :href="route('companies.employees.index')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        {{ form.processing ? "Updating..." : "Update Employee" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

