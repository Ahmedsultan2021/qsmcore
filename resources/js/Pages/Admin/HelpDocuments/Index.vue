<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, router } from "@inertiajs/vue3";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    documents: Object,
});

const deleteDocument = (id) => {
    if (confirm("Are you sure you want to delete this help document?")) {
        router.delete(route("help-documents.destroy", id));
    }
};

const formatBytes = (bytes) => {
    if (!bytes) return "-";
    const sizes = ["B", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return (bytes / Math.pow(1024, i)).toFixed(1) + " " + sizes[i];
};

const fileIcon = (type) => {
    if (type === "pdf")   return { icon: "fa-file-pdf",      color: "text-red-600 dark:text-red-400" };
    if (type === "word")  return { icon: "fa-file-word",     color: "text-blue-600 dark:text-blue-400" };
    if (type === "excel") return { icon: "fa-file-excel",    color: "text-green-600 dark:text-green-400" };
    return { icon: "fa-file", color: "text-gray-500 dark:text-gray-400" };
};

const navs = [
    { name: "Dashboard", linkName: "dashboard" },
    { name: "Help Documents", linkName: "help-documents.index" },
];
</script>

<template>
    <Head title="Help Documents" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            title="Help Documents"
            navLinkName="help-documents.create"
            NavLinkText="Add New Document"
            :showButton="true"
            :addSearchInput="false"
        />

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mt-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">File</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Size</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-if="documents.data.length === 0">
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                No help documents yet. Click "Add New Document" to upload one.
                            </td>
                        </tr>
                        <tr v-for="doc in documents.data" :key="doc.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm">
                                <div class="font-medium text-gray-900 dark:text-white">{{ doc.title }}</div>
                                <div v-if="doc.description" class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                    {{ doc.description }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a :href="doc.file_url" target="_blank" class="inline-flex items-center gap-2 text-gray-700 dark:text-gray-200 hover:underline">
                                    <i :class="['fa-solid', fileIcon(doc.file_type).icon, fileIcon(doc.file_type).color, 'fa-lg']"></i>
                                    <span class="truncate max-w-[180px]">{{ doc.original_name }}</span>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                {{ formatBytes(doc.file_size) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                {{ doc.sort_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span
                                    :class="[
                                        'px-2 py-1 rounded-full text-xs font-semibold',
                                        doc.is_active
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                            : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                    ]"
                                >
                                    {{ doc.is_active ? "Active" : "Hidden" }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link
                                    :href="route('help-documents.edit', doc.id)"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3"
                                >
                                    Edit
                                </Link>
                                <button
                                    @click="deleteDocument(doc.id)"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="documents.links && documents.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <nav class="flex items-center justify-end gap-1">
                    <Link
                        v-for="link in documents.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        v-html="link.label"
                        :class="[
                            'px-3 py-1 border text-sm rounded',
                            link.active
                                ? 'bg-blue-600 text-white border-blue-600'
                                : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600',
                            link.url ? '' : 'cursor-not-allowed opacity-50'
                        ]"
                    />
                </nav>
            </div>
        </div>
    </div>
</template>
