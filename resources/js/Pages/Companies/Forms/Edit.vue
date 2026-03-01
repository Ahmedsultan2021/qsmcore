<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    form: Object,
    departments: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    name: props.form.name || "",
    department_id: props.form.department_id || null,
});

const submit = () => {
    form.put(route("companies.forms.update", props.form.id));
};

const navs = computed(() => [
    { name: "Dashboard", linkName: "companies.dashboard" },
    { name: "Forms", linkName: "companies.forms.index" },
    { name: props.form.name, linkName: "companies.forms.show", param: { form: props.form.id } },
    { name: "Edit", linkName: "companies.forms.edit", param: { form: props.form.id } },
]);
</script>

<template>
    <Head title="Edit Form" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            title="Edit Form"
            :showButton="false"
            :addSearchInput="false"
        />

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mt-6">

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Form Name <span class="text-red-500">*</span>
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
                    <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Department (Optional)
                    </label>
                    <select
                        id="department_id"
                        v-model="form.department_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option :value="null">-- Select Department --</option>
                        <option v-for="department in departments" :key="department.id" :value="department.id">
                            {{ department.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.department_id" class="mt-1 text-sm text-red-600">{{ form.errors.department_id }}</div>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <Link
                        :href="route('companies.forms.index')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        {{ form.processing ? "Updating..." : "Update Form" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

