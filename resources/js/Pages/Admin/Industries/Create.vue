<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    formTemplates: Object,
});

const form = useForm({
    name: "",
    description: "",
    form_template_ids: [],
});

const toggleTemplate = (id) => {
    const ids = [...(form.form_template_ids || [])];
    const idx = ids.indexOf(id);
    if (idx >= 0) {
        ids.splice(idx, 1);
    } else {
        ids.push(id);
    }
    form.form_template_ids = ids;
};

const isSelected = (id) => (form.form_template_ids || []).includes(id);

const categories = computed(() => Object.keys(props.formTemplates || {}));

const submit = () => {
    form.post(route("industries.store"));
};
</script>

<template>
    <Head title="Create Industry" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="[
                { name: 'Dashboard', linkName: 'dashboard' },
                { name: 'Industries', linkName: 'industries.index' },
                { name: 'Add New', linkName: 'industries.create' },
            ]"
            title="Create New Industry"
            :showButton="false"
            :addSearchInput="false"
        />

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mt-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Create New Industry</h1>

            <form @submit.prevent="submit">
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

                <!-- Attached Report Forms (Form Templates) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Attached Report Forms
                    </label>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Select which form templates (report forms) are applicable to this industry.
                    </p>
                    <div class="space-y-4 max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                        <div
                            v-for="category in categories"
                            :key="category"
                            class="space-y-2"
                        >
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                {{ category }}
                            </h4>
                            <div class="space-y-2 pl-2">
                                <label
                                    v-for="template in formTemplates[category]"
                                    :key="template.id"
                                    class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded px-2 py-1.5"
                                >
                                    <input
                                        type="checkbox"
                                        :checked="isSelected(template.id)"
                                        @change="toggleTemplate(template.id)"
                                        class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500"
                                    />
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ template.name }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <Link
                        :href="route('industries.index')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        {{ form.processing ? "Creating..." : "Create Industry" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

