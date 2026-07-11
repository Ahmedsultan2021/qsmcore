<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    settings: Object,
});

const form = useForm({
    contact_email: props.settings.contact_email || "",
    contact_phone: props.settings.contact_phone || "",
    contact_address: props.settings.contact_address || "",
});

const submit = () => {
    form.put(route("site-settings.update"));
};
</script>

<template>
    <Head title="Site Settings" />

    <div class="p-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 max-w-2xl">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Site Settings</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                Contact details shown on the public website (welcome page, blog, and other guest pages).
            </p>

            <div
                v-if="$page.props.flash?.success"
                class="mb-6 rounded-md bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-300"
            >
                {{ $page.props.flash.success }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Contact Email
                    </label>
                    <input
                        id="contact_email"
                        v-model="form.contact_email"
                        type="email"
                        placeholder="support@qsm.com"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                    <div v-if="form.errors.contact_email" class="mt-1 text-sm text-red-600">{{ form.errors.contact_email }}</div>
                </div>

                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Contact Phone
                    </label>
                    <input
                        id="contact_phone"
                        v-model="form.contact_phone"
                        type="text"
                        placeholder="+1 (555) 123-4567"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                    <div v-if="form.errors.contact_phone" class="mt-1 text-sm text-red-600">{{ form.errors.contact_phone }}</div>
                </div>

                <div>
                    <label for="contact_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Contact Address
                    </label>
                    <textarea
                        id="contact_address"
                        v-model="form.contact_address"
                        rows="3"
                        placeholder="123 Business St, Suite 100"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none"
                    />
                    <div v-if="form.errors.contact_address" class="mt-1 text-sm text-red-600">{{ form.errors.contact_address }}</div>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-md hover:bg-blue-700 disabled:opacity-50"
                >
                    Save Changes
                </button>
            </form>
        </div>
    </div>
</template>
