<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import { Head } from "@inertiajs/vue3";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    documents: Array,
});

const formatBytes = (bytes) => {
    if (!bytes) return "";
    const sizes = ["B", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return (bytes / Math.pow(1024, i)).toFixed(1) + " " + sizes[i];
};

const fileIcon = (type) => {
    if (type === "pdf")   return { icon: "fa-file-pdf",   color: "text-red-600 dark:text-red-400",   bg: "bg-red-50 dark:bg-red-900/20",   label: "PDF" };
    if (type === "word")  return { icon: "fa-file-word",  color: "text-blue-600 dark:text-blue-400", bg: "bg-blue-50 dark:bg-blue-900/20", label: "Word" };
    if (type === "excel") return { icon: "fa-file-excel", color: "text-green-600 dark:text-green-400", bg: "bg-green-50 dark:bg-green-900/20", label: "Excel" };
    return { icon: "fa-file", color: "text-gray-500 dark:text-gray-400", bg: "bg-gray-50 dark:bg-gray-700", label: "File" };
};
</script>

<template>
    <Head title="Help" />

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <i class="fa-solid fa-circle-question text-blue-600"></i>
                Help &amp; Documentation
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Browse and download guides, manuals and reference documents prepared by your administrators.
            </p>
        </div>

        <div v-if="documents.length === 0" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-10 text-center">
            <i class="fa-solid fa-folder-open text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">No help documents yet</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Your administrator hasn't published any help documents.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <div
                v-for="doc in documents"
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
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-2">
                            <span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 font-medium">{{ fileIcon(doc.file_type).label }}</span>
                            <span v-if="doc.file_size">{{ formatBytes(doc.file_size) }}</span>
                        </div>
                    </div>
                </div>

                <p v-if="doc.description" class="text-sm text-gray-600 dark:text-gray-300 mb-4 flex-1">
                    {{ doc.description }}
                </p>
                <div v-else class="flex-1"></div>

                <div class="flex items-center gap-2 mt-2">
                    <a
                        :href="doc.file_url"
                        target="_blank"
                        rel="noopener"
                        class="flex-1 inline-flex justify-center items-center gap-2 px-3 py-2 rounded-md bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition"
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
                </div>
            </div>
        </div>
    </div>
</template>
