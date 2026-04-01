<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineOptions({ layout: CompanyLayout });
</script>

<template>
    <Head title="CAPA Management" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
            <h1 class="text-2xl font-extrabold text-gray-900">CAPA Management</h1>
            <p class="mt-2 text-gray-600">
                This section is currently a <span class="font-semibold">static placeholder</span> to match the new Company Portal design.
                We’ll implement real CAPA workflows (create, assign, due dates, overdue tracking, statuses) next.
            </p>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <div class="text-sm font-semibold text-gray-700">Overdue CAPAs</div>
                    <div class="mt-2 text-3xl font-extrabold text-gray-900">7</div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <div class="text-sm font-semibold text-gray-700">Open CAPAs</div>
                    <div class="mt-2 text-3xl font-extrabold text-gray-900">19</div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <div class="text-sm font-semibold text-gray-700">Closed (30d)</div>
                    <div class="mt-2 text-3xl font-extrabold text-gray-900">34</div>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <button
                    type="button"
                    class="px-4 py-2.5 rounded-xl bg-gray-900 text-white font-semibold shadow-sm hover:bg-gray-800 transition"
                    disabled
                >
                    New CAPA (coming soon)
                </button>
                <Link
                    :href="route('companies.dashboard')"
                    class="px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-900 font-semibold shadow-sm hover:bg-gray-50 transition"
                >
                    Back to Dashboard
                </Link>
            </div>
        </div>
    </div>
</template>

