<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    reports: Array,
});

const page = usePage();
const queryParams = new URLSearchParams(window.location.search);
const prefillDate = queryParams.get("date");

const form = useForm({
    name: "",
    description: "",
    image: null,
    attached_file: null,
    audit_date: prefillDate || new Date().toISOString().split("T")[0],
    reports: [],
});

const submit = () => {
    form.post(route("companies.audits.store"), {
        forceFormData: true,
    });
};

const removeImage = () => {
    form.image = null;
};

const removeFile = () => {
    form.attached_file = null;
};
</script>

<template>
    <Head title="Create Audit" />

    <div class="p-6">
        <div class="mb-6">
            <Link
                :href="route('companies.audits.index')"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
            >
                ← Back to Audit Tracker
            </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Create New Audit</h1>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Audit Name <span class="text-red-500">*</span>
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

                <div class="mb-4">
                    <label for="audit_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Audit Date <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="audit_date"
                        v-model="form.audit_date"
                        type="date"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                    <div v-if="form.errors.audit_date" class="mt-1 text-sm text-red-600">{{ form.errors.audit_date }}</div>
                </div>

                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Image
                    </label>
                    <input
                        id="image"
                        @input="form.image = $event.target.files[0]"
                        type="file"
                        accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                    <div v-if="form.image" class="mt-2 flex items-center gap-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ form.image.name }}</span>
                        <button
                            type="button"
                            @click="removeImage"
                            class="text-red-600 hover:text-red-800 text-sm"
                        >
                            Remove
                        </button>
                    </div>
                    <div v-if="form.errors.image" class="mt-1 text-sm text-red-600">{{ form.errors.image }}</div>
                </div>

                <div class="mb-4">
                    <label for="attached_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Attached File
                    </label>
                    <input
                        id="attached_file"
                        @input="form.attached_file = $event.target.files[0]"
                        type="file"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                    <div v-if="form.attached_file" class="mt-2 flex items-center gap-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ form.attached_file.name }}</span>
                        <button
                            type="button"
                            @click="removeFile"
                            class="text-red-600 hover:text-red-800 text-sm"
                        >
                            Remove
                        </button>
                    </div>
                    <div v-if="form.errors.attached_file" class="mt-1 text-sm text-red-600">{{ form.errors.attached_file }}</div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Attach Reports (Optional - Internal Audit)
                    </label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                        Leave empty for external audit. Select reports for internal audit.
                    </p>
                    <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-md p-3 dark:bg-gray-700 dark:border-gray-600">
                        <div v-for="report in reports" :key="report.id" class="mb-2">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    :value="report.id"
                                    v-model="form.reports"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-600 dark:border-gray-500"
                                />
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    {{ report.title }} 
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        ({{ report.department?.name }})
                                    </span>
                                </span>
                            </label>
                        </div>
                        <p v-if="reports.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
                            No reports available
                        </p>
                    </div>
                    <div v-if="form.errors.reports" class="mt-1 text-sm text-red-600">{{ form.errors.reports }}</div>
                </div>

                <div class="flex justify-end gap-3">
                    <Link
                        :href="route('companies.audits.index')"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                    >
                        {{ form.processing ? "Creating..." : "Create Audit" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
