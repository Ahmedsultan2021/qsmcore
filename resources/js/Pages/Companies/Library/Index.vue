<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    documents: Object,
});

const page = usePage();
const authEmployeeId = page.props.authEmployee?.employee?.id;

const formatBytes = (bytes) => {
    if (!bytes) return "";
    const sizes = ["B", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return (bytes / Math.pow(1024, i)).toFixed(1) + " " + sizes[i];
};

const fileIcon = (type) => {
    if (type === "pdf")        return { icon: "fa-file-pdf",        color: "text-red-600 dark:text-red-400",    bg: "bg-red-50 dark:bg-red-900/20",    label: "PDF" };
    if (type === "word")       return { icon: "fa-file-word",       color: "text-blue-600 dark:text-blue-400",  bg: "bg-blue-50 dark:bg-blue-900/20",  label: "Word" };
    if (type === "excel")      return { icon: "fa-file-excel",      color: "text-green-600 dark:text-green-400", bg: "bg-green-50 dark:bg-green-900/20", label: "Excel" };
    if (type === "powerpoint") return { icon: "fa-file-powerpoint", color: "text-orange-600 dark:text-orange-400", bg: "bg-orange-50 dark:bg-orange-900/20", label: "PPT" };
    if (type === "image")      return { icon: "fa-file-image",      color: "text-purple-600 dark:text-purple-400", bg: "bg-purple-50 dark:bg-purple-900/20", label: "Image" };
    return { icon: "fa-file", color: "text-gray-500 dark:text-gray-400", bg: "bg-gray-50 dark:bg-gray-700", label: "File" };
};

const confirmDelete = (doc) => {
    if (confirm(`Delete "${doc.title}"? This cannot be undone.`)) {
        router.delete(`/companies/library/${doc.id}`);
    }
};

const formatDate = (dateStr) => {
    if (!dateStr) return "";
    return new Date(dateStr).toLocaleDateString("en-GB", { day: "2-digit", month: "short", year: "numeric" });
};
</script>

<template>
    <Head title="Library" />

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                    <i class="fa-solid fa-book-open text-indigo-600"></i>
                    Company Library
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Share documents, guides, and resources with your team.
                </p>
            </div>
            <Link
                :href="'/companies/library/create'"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition shadow-sm"
            >
                <i class="fa-solid fa-plus"></i>
                Upload Document
            </Link>
        </div>

        <div v-if="!documents.data || documents.data.length === 0" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-10 text-center">
            <i class="fa-solid fa-folder-open text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">No documents yet</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Be the first to share a document with your team.</p>
            <Link
                :href="'/companies/library/create'"
                class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition"
            >
                <i class="fa-solid fa-upload"></i> Upload Document
            </Link>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <div
                v-for="doc in documents.data"
                :key="doc.id"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 shadow-sm hover:shadow-md transition flex flex-col"
            >
                <div class="flex items-start gap-3 mb-3">
                    <div :class="['w-12 h-12 rounded-lg flex items-center justify-center shrink-0', fileIcon(doc.file_type).bg]">
                        <i :class="['fa-solid', fileIcon(doc.file_type).icon, fileIcon(doc.file_type).color, 'fa-xl']"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate" :title="doc.title">
                            {{ doc.title }}
                        </h3>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-2 flex-wrap">
                            <span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 font-medium">{{ fileIcon(doc.file_type).label }}</span>
                            <span v-if="doc.file_size">{{ formatBytes(doc.file_size) }}</span>
                        </div>
                    </div>
                </div>

                <p v-if="doc.description" class="text-sm text-gray-600 dark:text-gray-300 mb-3 line-clamp-2">
                    {{ doc.description }}
                </p>
                <div v-else class="flex-1"></div>

                <div class="text-xs text-gray-400 dark:text-gray-500 mb-3 flex items-center gap-1.5">
                    <i class="fa-solid fa-user"></i>
                    <span>{{ doc.uploader ? doc.uploader.fname + ' ' + doc.uploader.lname : 'Unknown' }}</span>
                    <span class="mx-1">&middot;</span>
                    <span>{{ formatDate(doc.created_at) }}</span>
                </div>

                <div class="flex items-center gap-2 mt-auto">
                    <a
                        :href="doc.file_url"
                        target="_blank"
                        rel="noopener"
                        class="flex-1 inline-flex justify-center items-center gap-2 px-3 py-2 rounded-md bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition"
                    >
                        <i class="fa-solid fa-eye"></i> View
                    </a>
                    <a
                        :href="doc.file_url"
                        :download="doc.original_name"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                        :title="'Download ' + doc.original_name"
                    >
                        <i class="fa-solid fa-download"></i>
                    </a>
                    <template v-if="doc.uploaded_by === authEmployeeId">
                        <Link
                            :href="'/companies/library/' + doc.id + '/edit'"
                            class="inline-flex items-center px-2.5 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                            title="Edit"
                        >
                            <i class="fa-solid fa-pen-to-square"></i>
                        </Link>
                        <button
                            @click="confirmDelete(doc)"
                            class="inline-flex items-center px-2.5 py-2 rounded-md border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 text-sm hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                            title="Delete"
                        >
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="documents.links && documents.last_page > 1" class="mt-8 flex justify-center">
            <nav class="inline-flex gap-1">
                <template v-for="link in documents.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="px-3 py-2 text-sm rounded-md border transition"
                        :class="link.active
                            ? 'bg-indigo-600 text-white border-indigo-600'
                            : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'"
                        v-html="link.label"
                    />
                    <span
                        v-else
                        class="px-3 py-2 text-sm rounded-md border border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-600"
                        v-html="link.label"
                    />
                </template>
            </nav>
        </div>
    </div>
</template>
