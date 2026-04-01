<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import { Head } from "@inertiajs/vue3";

defineOptions({ layout: CompanyLayout });
</script>

<template>
    <Head title="Settings" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
            <h1 class="text-2xl font-extrabold text-gray-900">Settings</h1>
            <p class="mt-2 text-gray-600">
                This page is a <span class="font-semibold">static placeholder</span> to match the new Company Portal navigation.
            </p>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-xl border border-gray-200 p-4">
                    <div class="text-sm font-semibold text-gray-900">Profile</div>
                    <p class="mt-1 text-sm text-gray-600">Update your display name, email, and password.</p>
                </div>
                <div class="rounded-xl border border-gray-200 p-4">
                    <div class="text-sm font-semibold text-gray-900">Notifications</div>
                    <p class="mt-1 text-sm text-gray-600">Email alerts for new reports, audits, and due items.</p>
                </div>
                <div class="rounded-xl border border-gray-200 p-4">
                    <div class="text-sm font-semibold text-gray-900">Company</div>
                    <p class="mt-1 text-sm text-gray-600">Company details, departments, and permissions.</p>
                </div>
                <div class="rounded-xl border border-gray-200 p-4">
                    <div class="text-sm font-semibold text-gray-900">Integrations</div>
                    <p class="mt-1 text-sm text-gray-600">Exporting, API keys, and external systems.</p>
                </div>
            </div>
        </div>
    </div>
</template>

