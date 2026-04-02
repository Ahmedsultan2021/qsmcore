<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    sectors: Array,
    sector_id: Number,
});

const form = useForm({
    sector_id: props.sector_id || "",
    name: "",
    email: "",
    phone: "",
    address: "",
    description: "",
    logo: null,
});

const submit = () => {
    form.post(route("companies.store"), {
        forceFormData: true,
    });
};

const clearLogo = () => {
    form.logo = null;
};
</script>

<template>
    <Head title="Create Company" />

    <div class="p-6">
        <div class="mb-6">
            <Link
                :href="route('companies.index', sector_id ? { sector_id } : {})"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
            >
                ← Back to Companies
            </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Create New Company</h1>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label for="sector_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sector <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="sector_id"
                        v-model="form.sector_id"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">Select Sector</option>
                        <option v-for="sector in sectors" :key="sector.id" :value="sector.id">
                            {{ sector.industry.name }} - {{ sector.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.sector_id" class="mt-1 text-sm text-red-600">{{ form.errors.sector_id }}</div>
                </div>

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

                <div class="mb-4">
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

                <div class="mb-4">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Address
                    </label>
                    <textarea
                        id="address"
                        v-model="form.address"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    ></textarea>
                    <div v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</div>
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
                    <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Logo
                    </label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">PNG, JPG, WebP, or GIF. Max 2 MB.</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <input
                            id="logo"
                            type="file"
                            accept="image/*"
                            class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-600 dark:file:text-gray-200"
                            @input="form.logo = $event.target.files[0]"
                        />
                        <button
                            v-if="form.logo"
                            type="button"
                            class="text-sm text-red-600 hover:text-red-800 dark:text-red-400"
                            @click="clearLogo"
                        >
                            Clear file
                        </button>
                    </div>
                    <div v-if="form.errors.logo" class="mt-1 text-sm text-red-600">{{ form.errors.logo }}</div>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <Link
                        :href="route('companies.index', sector_id ? { sector_id } : {})"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        {{ form.processing ? "Creating..." : "Create Company" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

