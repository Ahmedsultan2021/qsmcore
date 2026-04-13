<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({ partners: Object });

const navs = computed(() => [
    { name: "Dashboard", linkName: "dashboard" },
    { name: "Partners",  linkName: "partners.index" },
]);

const toggleActive = (p) => router.post(route('partners.toggle-active', p.id), {}, { preserveScroll: true });

const deletePartner = (id) => {
    if (confirm('Delete this partner?')) {
        router.delete(route('partners.destroy', id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Partners" />
    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            title="Partners"
            navLinkName="partners.create"
            NavLinkText="Add Partner"
            :showButton="true"
            :addSearchInput="false"
        />

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mt-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Website</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-if="!partners.data.length">
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400">No partners yet.</td>
                        </tr>
                        <tr v-for="p in partners.data" :key="p.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <img v-if="p.logo_url" :src="p.logo_url" :alt="p.name" class="h-10 w-20 object-contain rounded border border-gray-200 bg-white p-1" />
                                <span v-else class="text-gray-400 text-xs">No logo</span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ p.name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <a v-if="p.website" :href="p.website" target="_blank" class="text-blue-600 hover:underline truncate max-w-[180px] block">{{ p.website }}</a>
                                <span v-else>—</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ p.order }}</td>
                            <td class="px-6 py-4">
                                <span :class="p.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    {{ p.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium space-x-3">
                                <Link :href="route('partners.edit', p.id)" class="text-indigo-600 hover:text-indigo-900">Edit</Link>
                                <button @click="toggleActive(p)" :class="p.is_active ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900'">
                                    {{ p.is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                <button @click="deletePartner(p.id)" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="partners.links && partners.links.length > 3" class="px-6 py-4 border-t border-gray-200">
                <nav class="flex justify-end gap-1">
                    <a v-for="link in partners.links" :key="link.label" :href="link.url || '#'" v-html="link.label"
                        :class="['px-3 py-1 rounded text-sm border', link.active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50', !link.url ? 'opacity-40 cursor-not-allowed' : '']"
                    ></a>
                </nav>
            </div>
        </div>
    </div>
</template>
