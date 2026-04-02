<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    company_id: Number,
    sectorTemplateGroups: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    name: "",
    description: "",
    manager_name: "",
    phone: "",
    email: "",
    template_ids: [],
});

const allSectorTemplateIds = computed(() =>
    props.sectorTemplateGroups.flatMap((g) => g.templates.map((t) => t.id))
);

const selectAllSectorTemplates = () => {
    form.template_ids = [...allSectorTemplateIds.value];
};

const clearTemplateSelection = () => {
    form.template_ids = [];
};

const submit = () => {
    form.post(route("companies.departments.store"));
};
</script>

<template>
    <Head title="Create Department" />

    <div class="p-6">
        <div class="mb-6">
            <Link
                :href="route('companies.departments.index')"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
            >
                ← Back to Departments
            </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Create New Department</h1>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Department Name <span class="text-red-500">*</span>
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

                <div class="mb-4 rounded-lg border border-gray-200 dark:border-gray-600 p-4 bg-gray-50/80 dark:bg-gray-900/40">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Form templates for this department
                        </label>
                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                class="text-xs px-2 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                @click="selectAllSectorTemplates"
                            >
                                Select all
                            </button>
                            <button
                                type="button"
                                class="text-xs px-2 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                @click="clearTemplateSelection"
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                        Choose which templates this department may use. If you leave none selected, every template for your
                        sector will be linked (same as a full library).
                    </p>
                    <div v-if="!sectorTemplateGroups.length" class="text-sm text-amber-700 dark:text-amber-300">
                        No templates are attached to your sector yet. You can create the department; link templates later or
                        ask an administrator to configure the sector library.
                    </div>
                    <div v-else class="space-y-4 max-h-72 overflow-y-auto pr-1">
                        <div v-for="group in sectorTemplateGroups" :key="group.group_key">
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">{{ group.group_label }}</p>
                            <ul class="space-y-2">
                                <li v-for="tpl in group.templates" :key="tpl.id" class="flex items-start gap-2">
                                    <input
                                        :id="`tpl-${tpl.id}`"
                                        v-model="form.template_ids"
                                        type="checkbox"
                                        class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        :value="tpl.id"
                                    />
                                    <label :for="`tpl-${tpl.id}`" class="text-sm text-gray-800 dark:text-gray-200 cursor-pointer">
                                        {{ tpl.name }}
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div v-if="form.errors.template_ids" class="mt-2 text-sm text-red-600">{{ form.errors.template_ids }}</div>
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="manager_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Manager Name
                        </label>
                        <input
                            id="manager_name"
                            v-model="form.manager_name"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                        <div v-if="form.errors.manager_name" class="mt-1 text-sm text-red-600">{{ form.errors.manager_name }}</div>
                    </div>

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
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email
                    </label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                    <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</div>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <Link
                        :href="route('companies.departments.index')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        {{ form.processing ? "Creating..." : "Create Department" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
